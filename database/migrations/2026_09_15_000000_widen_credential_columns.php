<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Passwords are now stored as bcrypt hashes (60 characters) instead of
 * unsalted sha1(md5()) digests (40 characters), and API tokens are random
 * strings up to 40 characters. Make sure the legacy columns can hold them.
 *
 * Every change is guarded so the migration is a no-op on databases that do
 * not contain the legacy tables (e.g. the test database).
 */
return new class extends Migration
{
    /**
     * Columns to widen, grouped by table.
     *
     * @var array<string, array<int, string>>
     */
    private array $columns = [
        'administrator' => ['password', 'token'],
        'client' => ['password', 'passwordrecovery'],
        'device' => ['token'],
        'manager_password' => ['token'],
    ];

    public function up(): void
    {
        foreach ($this->columns as $table => $columns) {
            if (! Schema::hasTable($table)) {
                continue;
            }
            $existing = array_values(array_filter($columns, fn ($column) => Schema::hasColumn($table, $column)));
            if ($existing === []) {
                continue;
            }
            Schema::table($table, function (Blueprint $blueprint) use ($existing) {
                foreach ($existing as $column) {
                    $blueprint->string($column, 255)->nullable()->change();
                }
            });
        }
    }

    public function down(): void
    {
        // The wider columns are backwards compatible; nothing to revert.
    }
};
