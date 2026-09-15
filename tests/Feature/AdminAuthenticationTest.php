<?php

namespace Tests\Feature;

use App\Models\Helpers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\LegacySchema;
use Tests\TestCase;

class AdminAuthenticationTest extends TestCase
{
    use LegacySchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createLegacySchema();
    }

    private function createAdmin(string $password, ?string $token = null, bool $legacyHash = false): int
    {
        return DB::table('administrator')->insertGetId([
            'name' => 'Admin',
            'login' => 'admin@uxsense.com.br',
            'password' => $legacyHash ? sha1(md5($password)) : Hash::make($password),
            'token' => $token,
        ]);
    }

    public function test_protected_routes_require_credentials(): void
    {
        $this->getJson('/api/manager/unit')->assertStatus(401);
        $this->getJson('/api/analyze')->assertStatus(401);
        $this->getJson('/api/manager/technique')->assertStatus(401);
        $this->getJson('/api/push/check')->assertStatus(401);
    }

    public function test_missing_token_header_does_not_match_administrators_without_token(): void
    {
        $id = $this->createAdmin('secret', null);

        // Regression: "where token = NULL" used to match logged out admins.
        $this->getJson('/api/manager/unit', ['GSX-CODE' => $id])->assertStatus(401);
        $this->getJson('/api/manager/unit', ['GSX-CODE' => $id, 'GSX-TOKEN' => ''])->assertStatus(401);
    }

    public function test_wrong_token_is_rejected_and_right_token_is_accepted(): void
    {
        $id = $this->createAdmin('secret', 'valid-token');

        $this->getJson('/api/manager/unit', ['GSX-CODE' => $id, 'GSX-TOKEN' => 'other'])->assertStatus(401);
        $this->getJson('/api/manager/unit', ['GSX-CODE' => $id, 'GSX-TOKEN' => 'valid-token'])
            ->assertStatus(200)
            ->assertJson(['units' => []]);
    }

    public function test_login_with_bcrypt_password_returns_a_random_token(): void
    {
        $id = $this->createAdmin('secret');

        $response = $this->postJson('/api/manager/login', ['login' => 'admin@uxsense.com.br', 'password' => 'secret']);

        $response->assertStatus(200)->assertJsonStructure(['id', 'name', 'login', 'token']);
        $token = $response->json('token');
        $this->assertMatchesRegularExpression('/^[a-f0-9]{32}$/', $token);
        $this->assertSame($token, DB::table('administrator')->where('idadministrator', $id)->value('token'));
        $this->assertArrayNotHasKey('password', $response->json());
    }

    public function test_login_with_legacy_hash_succeeds_and_upgrades_the_hash(): void
    {
        $id = $this->createAdmin('legacy-pass', null, true);

        $this->postJson('/api/manager/login', ['login' => 'admin@uxsense.com.br', 'password' => 'legacy-pass'])
            ->assertStatus(200);

        $stored = DB::table('administrator')->where('idadministrator', $id)->value('password');
        $this->assertFalse(Helpers::isLegacyHash($stored));
        $this->assertTrue(Hash::check('legacy-pass', $stored));

        // The upgraded hash keeps working.
        $this->postJson('/api/manager/login', ['login' => 'admin@uxsense.com.br', 'password' => 'legacy-pass'])
            ->assertStatus(200);
    }

    public function test_login_with_wrong_password_fails(): void
    {
        $this->createAdmin('secret');

        $this->postJson('/api/manager/login', ['login' => 'admin@uxsense.com.br', 'password' => 'wrong'])->assertStatus(203);
        $this->postJson('/api/manager/login', ['login' => 'admin@uxsense.com.br'])->assertStatus(400);
        $this->postJson('/api/manager/login', ['login' => ['x'], 'password' => ['y']])->assertStatus(400);
    }

    public function test_logout_invalidates_the_token(): void
    {
        $id = $this->createAdmin('secret', 'valid-token');

        $this->postJson('/api/manager/logout', [], ['GSX-CODE' => $id, 'GSX-TOKEN' => 'valid-token'])->assertStatus(200);

        $this->assertNull(DB::table('administrator')->where('idadministrator', $id)->value('token'));
        $this->getJson('/api/manager/unit', ['GSX-CODE' => $id, 'GSX-TOKEN' => 'valid-token'])->assertStatus(401);
    }

    public function test_first_access_sets_a_bcrypt_password_and_consumes_the_token(): void
    {
        $id = $this->createAdmin('old', null, true);
        DB::table('manager_password')->insert(['token' => 'invite-token', 'idadministrator' => $id]);

        $this->postJson('/api/manager/login/first-access', ['token' => 'invite-token', 'password' => 'new-pass'])->assertStatus(200);

        $stored = DB::table('administrator')->where('idadministrator', $id)->value('password');
        $this->assertTrue(Hash::check('new-pass', $stored));
        $this->assertSame(0, DB::table('manager_password')->count());
        $this->postJson('/api/manager/login/first-access', ['token' => 'invite-token', 'password' => 'again'])->assertStatus(400);
    }

    public function test_expired_first_access_tokens_are_rejected(): void
    {
        $id = $this->createAdmin('old');
        DB::table('manager_password')->insert([
            'token' => 'stale-token',
            'idadministrator' => $id,
            'date' => now()->subDays(10)->toDateTimeString(),
        ]);

        $this->postJson('/api/manager/login/first-access', ['token' => 'stale-token', 'password' => 'new-pass'])->assertStatus(400);
        $this->assertTrue(Hash::check('old', DB::table('administrator')->where('idadministrator', $id)->value('password')));
    }

    public function test_push_check_requires_the_cron_secret(): void
    {
        $this->getJson('/api/push/check', ['GSX-CRON-TOKEN' => 'wrong'])->assertStatus(401);
        $this->getJson('/api/push/check', ['GSX-CRON-TOKEN' => 'test-cron-token'])
            ->assertStatus(200)
            ->assertJson(['schedulers' => [], 'send_data' => []]);
    }

    public function test_scheduler_listing_rejects_sql_in_sort_direction(): void
    {
        $id = $this->createAdmin('secret', 'valid-token');
        DB::table('scheduler')->insert(['date' => '2026-01-01', 'time' => '10:00:00', 'idquestion' => 1, 'idteam' => 1, 'capture_technique_id' => 1, 'sent' => 0]);
        DB::table('scheduler')->insert(['date' => '2026-01-02', 'time' => '10:00:00', 'idquestion' => 2, 'idteam' => 1, 'capture_technique_id' => 1, 'sent' => 0]);
        $headers = ['GSX-CODE' => $id, 'GSX-TOKEN' => 'valid-token'];

        $this->getJson('/api/manager/scheduler?date=desc', $headers)->assertStatus(200)->assertJsonPath('schedulers.0.date', '2026-01-02');
        $this->getJson('/api/manager/scheduler?date=asc;DROP TABLE scheduler', $headers)->assertStatus(200)->assertJsonPath('schedulers.0.date', '2026-01-01');
        $this->getJson("/api/manager/scheduler?question_id=1 OR 1=1", $headers)->assertStatus(200)->assertJsonCount(0, 'schedulers');
        $this->getJson('/api/manager/scheduler?question_id=2', $headers)->assertStatus(200)->assertJsonCount(1, 'schedulers');
        $this->getJson("/api/manager/scheduler/1 OR 1=1", $headers)->assertStatus(400);
        $this->getJson('/api/manager/scheduler/1', $headers)->assertStatus(200)->assertJsonPath('id', 1);
    }

    public function test_server_errors_do_not_leak_internals_when_debug_is_off(): void
    {
        $id = $this->createAdmin('secret', 'valid-token');
        DB::statement('DROP TABLE unit');

        $response = $this->getJson('/api/manager/unit', ['GSX-CODE' => $id, 'GSX-TOKEN' => 'valid-token']);

        $response->assertStatus(500)->assertExactJson(['message' => 'Server Error']);
    }

    public function test_unknown_api_routes_return_json_404(): void
    {
        $this->getJson('/api/does-not-exist')->assertStatus(404)->assertJsonStructure(['message']);
    }
}
