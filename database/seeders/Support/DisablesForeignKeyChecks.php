<?php

namespace Database\Seeders\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait DisablesForeignKeyChecks
{
    private static int $foreignKeyChecksDisabledDepth = 0;

    /**
     * Run a callback with foreign key checks disabled (MySQL/MariaDB).
     */
    protected function withoutForeignKeyChecks(callable $callback): mixed
    {
        $this->disableForeignKeyChecksIfNeeded();

        try {
            return $callback();
        } finally {
            $this->enableForeignKeyChecksIfNeeded();
        }
    }

    /**
     * Empty tables in order (child tables first). Requires FK checks disabled when truncating referenced tables.
     *
     * @param  list<string>  $tables
     */
    protected function truncateTables(array $tables): void
    {
        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }
    }

    private function disableForeignKeyChecksIfNeeded(): void
    {
        if (self::$foreignKeyChecksDisabledDepth === 0) {
            if (DB::getDriverName() === 'mysql') {
                DB::statement('SET FOREIGN_KEY_CHECKS=0');
            }

            Schema::disableForeignKeyConstraints();
        }

        self::$foreignKeyChecksDisabledDepth++;
    }

    private function enableForeignKeyChecksIfNeeded(): void
    {
        self::$foreignKeyChecksDisabledDepth = max(0, self::$foreignKeyChecksDisabledDepth - 1);

        if (self::$foreignKeyChecksDisabledDepth === 0) {
            Schema::enableForeignKeyConstraints();

            if (DB::getDriverName() === 'mysql') {
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            }
        }
    }
}
