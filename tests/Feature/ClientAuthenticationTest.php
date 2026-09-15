<?php

namespace Tests\Feature;

use App\Models\Helpers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\LegacySchema;
use Tests\TestCase;

class ClientAuthenticationTest extends TestCase
{
    use LegacySchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createLegacySchema();
    }

    private function createClient(string $register, string $password, bool $legacyHash = false, ?string $recovery = null): int
    {
        return DB::table('client')->insertGetId([
            'name' => 'Client '.$register,
            'email' => $register.'@example.com',
            'register' => $register,
            'password' => $legacyHash ? sha1(md5($password)) : Hash::make($password),
            'passwordrecovery' => $recovery,
            'sex' => 'F',
            'datebirth' => '1990-01-01',
        ]);
    }

    private function createDevice(int $idclient, ?string $token, string $uuid = 'uuid-1'): int
    {
        return DB::table('device')->insertGetId([
            'idclient' => $idclient,
            'registratorid' => null,
            'platform' => 'Android',
            'uuid' => $uuid,
            'token' => $token,
        ]);
    }

    public function test_logged_out_device_cannot_authenticate_without_a_token(): void
    {
        $client = $this->createClient('100', 'secret');
        $device = $this->createDevice($client, null);

        $this->getJson('/api/app/client', ['GSX-DEVICE' => $device])->assertStatus(401);
        $this->getJson('/api/app/client', ['GSX-DEVICE' => $device, 'GSX-TOKEN' => 'nope'])->assertStatus(401);
    }

    public function test_client_only_sees_its_own_profile(): void
    {
        $mine = $this->createClient('100', 'secret');
        $other = $this->createClient('200', 'secret');
        $device = $this->createDevice($mine, 'device-token');

        // The client id used to be read from a "user_id" header.
        $response = $this->getJson('/api/app/client', ['GSX-DEVICE' => $device, 'GSX-TOKEN' => 'device-token', 'user_id' => $other]);

        $response->assertStatus(200)->assertJsonPath('id', $mine)->assertJsonPath('register', '100');
        $this->assertArrayNotHasKey('password', $response->json());
    }

    public function test_login_returns_a_random_device_token_and_upgrades_legacy_hashes(): void
    {
        $client = $this->createClient('100', 'legacy', true);

        $response = $this->postJson('/api/app/login', ['register' => '100', 'password' => 'legacy', 'uuid' => 'new-uuid', 'platform' => 'Android']);

        $response->assertStatus(200)->assertJsonStructure(['client' => ['id'], 'device' => ['id', 'token']]);
        $token = $response->json('device.token');
        $this->assertMatchesRegularExpression('/^[a-f0-9]{32}$/', $token);
        $this->assertSame($token, DB::table('device')->where('uuid', 'new-uuid')->value('token'));

        $stored = DB::table('client')->where('idclient', $client)->value('password');
        $this->assertFalse(Helpers::isLegacyHash($stored));
        $this->assertTrue(Hash::check('legacy', $stored));

        $this->postJson('/api/app/login', ['register' => '100', 'password' => 'wrong', 'uuid' => 'new-uuid'])->assertStatus(203);
    }

    public function test_temporary_recovery_password_becomes_the_new_password(): void
    {
        $client = $this->createClient('100', 'old-pass', false, Hash::make('temp-pass'));

        $this->postJson('/api/app/login', ['register' => '100', 'password' => 'temp-pass', 'uuid' => 'u'])->assertStatus(200);

        $row = DB::table('client')->where('idclient', $client)->first();
        $this->assertNull($row->passwordrecovery);
        $this->assertTrue(Hash::check('temp-pass', $row->password));
        $this->postJson('/api/app/login', ['register' => '100', 'password' => 'old-pass', 'uuid' => 'u'])->assertStatus(203);
    }

    public function test_password_change_requires_the_current_password(): void
    {
        $client = $this->createClient('100', 'secret');
        $device = $this->createDevice($client, 'device-token');
        $headers = ['GSX-DEVICE' => $device, 'GSX-TOKEN' => 'device-token'];

        $this->postJson('/api/app/client', ['password' => 'wrong', 'newpassword' => 'hacked'], $headers)->assertStatus(403);
        $this->assertTrue(Hash::check('secret', DB::table('client')->where('idclient', $client)->value('password')));

        $this->postJson('/api/app/client', ['password' => 'secret', 'newpassword' => 'changed'], $headers)->assertStatus(200);
        $this->assertTrue(Hash::check('changed', DB::table('client')->where('idclient', $client)->value('password')));

        $this->postJson('/api/app/client', ['name' => 'Renamed'], $headers)->assertStatus(200)->assertJsonPath('name', 'Renamed');
    }

    public function test_answers_are_always_stored_for_the_authenticated_client(): void
    {
        $mine = $this->createClient('100', 'secret');
        $other = $this->createClient('200', 'secret');
        $device = $this->createDevice($mine, 'device-token');
        $headers = ['GSX-DEVICE' => $device, 'GSX-TOKEN' => 'device-token'];
        DB::table('answer')->insert(['idanswer' => 50, 'date' => '2026-01-01', 'time' => '10:00:00', 'answer' => '{}', 'idclient' => $other, 'idscheduler' => 1, 'capture_technique_id' => 1]);

        $payload = ['date' => '2026-01-01', 'time' => '10:00:00', 'answer' => '{"max":1}', 'client_id' => $other, 'scheduler_id' => 2, 'technique_id' => 1, 'latitude' => '1', 'longitude' => '2'];
        $this->postJson('/api/app/answer', $payload, $headers)->assertStatus(200)->assertJsonPath('client.id', $mine);
        $this->assertSame(1, DB::table('answer')->where('idclient', $mine)->count());

        // Editing another client's answer is refused.
        $this->postJson('/api/app/answer', ['id' => 50, 'answer' => '{"tampered":1}'], $headers)->assertStatus(404);
        $this->assertSame('{}', DB::table('answer')->where('idanswer', 50)->value('answer'));

        $this->getJson('/api/app/answer', $headers)->assertStatus(200)->assertJsonCount(1, 'answers');
    }

    public function test_team_membership_is_bound_to_the_authenticated_client(): void
    {
        $mine = $this->createClient('100', 'secret');
        $other = $this->createClient('200', 'secret');
        $device = $this->createDevice($mine, 'device-token');
        $headers = ['GSX-DEVICE' => $device, 'GSX-TOKEN' => 'device-token'];

        $this->postJson('/api/app/team-client', ['team' => 7, 'client' => $other], $headers)->assertStatus(200);

        $this->assertSame(1, DB::table('team_client')->where('idteam', 7)->where('idclient', $mine)->count());
        $this->assertSame(0, DB::table('team_client')->where('idclient', $other)->count());
    }

    public function test_logout_clears_the_device_token(): void
    {
        $client = $this->createClient('100', 'secret');
        $device = $this->createDevice($client, 'device-token');
        $headers = ['GSX-DEVICE' => $device, 'GSX-TOKEN' => 'device-token'];

        $this->getJson('/api/app/logout', $headers)->assertStatus(200);

        $this->assertNull(DB::table('device')->where('iddevice', $device)->value('token'));
        $this->getJson('/api/app/client', $headers)->assertStatus(401);
    }

    public function test_pending_scheduler_for_client_is_scoped_to_its_teams(): void
    {
        $client = $this->createClient('100', 'secret');
        $device = $this->createDevice($client, 'device-token');
        $headers = ['GSX-DEVICE' => $device, 'GSX-TOKEN' => 'device-token'];
        DB::table('team_client')->insert(['idteam' => 1, 'idclient' => $client]);
        DB::table('scheduler')->insert(['idscheduler' => 1, 'date' => now()->toDateString(), 'time' => '00:00:00', 'idquestion' => 1, 'idteam' => 1, 'capture_technique_id' => 1]);
        DB::table('scheduler')->insert(['idscheduler' => 2, 'date' => now()->toDateString(), 'time' => '00:00:00', 'idquestion' => 1, 'idteam' => 99, 'capture_technique_id' => 1]);

        $this->getJson('/api/app/scheduler', $headers)->assertStatus(200)->assertJsonPath('id', 1);

        DB::table('answer')->insert(['date' => '2026-01-01', 'time' => '10:00:00', 'answer' => '{}', 'idclient' => $client, 'idscheduler' => 1, 'capture_technique_id' => 1]);
        $this->getJson('/api/app/scheduler', $headers)->assertStatus(200)->assertSee('');
    }

    public function test_technique_list_accepts_device_credentials(): void
    {
        $client = $this->createClient('100', 'secret');
        $device = $this->createDevice($client, 'device-token');
        DB::table('capture_technique')->insert(['name' => 'Eye tracking', 'description' => 'x']);

        $this->getJson('/api/manager/technique', ['GSX-DEVICE' => $device, 'GSX-TOKEN' => 'device-token'])
            ->assertStatus(200)
            ->assertJsonCount(1, 'techniques');
    }
}
