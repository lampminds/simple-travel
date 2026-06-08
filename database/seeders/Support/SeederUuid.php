<?php

namespace Database\Seeders\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Assigns UUIDs to raw DB seeder rows for tables with a required uuid column.
 */
final class SeederUuid
{
    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return array<int, array<string, mixed>>
     */
    public static function assign(array $rows): array
    {
        foreach ($rows as &$row) {
            if (! array_key_exists('uuid', $row) || $row['uuid'] === null || $row['uuid'] === '') {
                $row['uuid'] = (string) Str::uuid();
            }
        }
        unset($row);

        return $rows;
    }

    /**
     * Adds a UUID only when updateOrInsert would insert a new row.
     *
     * @param  array<string, mixed>  $where
     * @param  array<string, mixed>  $values
     * @return array<string, mixed>
     */
    public static function forUpdateOrInsert(string $table, array $where, array $values): array
    {
        if (DB::table($table)->where($where)->exists()) {
            return $values;
        }

        if (! array_key_exists('uuid', $values) || $values['uuid'] === null || $values['uuid'] === '') {
            $values['uuid'] = (string) Str::uuid();
        }

        return $values;
    }
}
