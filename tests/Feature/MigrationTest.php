<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\LegacySchema;
use Tests\TestCase;

class MigrationTest extends TestCase
{
    use LegacySchema;

    public function test_migrations_run_on_top_of_the_legacy_schema(): void
    {
        $this->createLegacySchema();
        DB::table('administrator')->insert(['name' => 'A', 'login' => 'a', 'password' => sha1(md5('x')), 'token' => null]);

        Artisan::call('migrate', ['--force' => true]);

        $this->assertTrue(Schema::hasTable('cache'));
        $this->assertTrue(Schema::hasTable('migrations'));
        $this->assertSame(1, DB::table('administrator')->count());
        DB::table('administrator')->where('login', 'a')->update(['password' => str_repeat('x', 60)]);
        $this->assertSame(60, strlen(DB::table('administrator')->where('login', 'a')->value('password')));
    }

    public function test_migrations_run_on_an_empty_database(): void
    {
        Artisan::call('migrate', ['--force' => true]);

        $this->assertTrue(Schema::hasTable('cache'));
        $this->assertFalse(Schema::hasTable('administrator'));
    }
}
