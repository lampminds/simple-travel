<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PlanItemsTableSeeder extends Seeder
{
    /**
     * Seed so that parents are inserted before children (FK parent_id).
     *
     * @return void
     */
    public function run()
    {
        \DB::table('plan_features')->delete();

        $rows = $this->getRows();
        $ordered = $this->orderParentsBeforeChildren($rows);
        \DB::table('plan_features')->insert($ordered);
    }

    /**
     * Order rows so every parent_id exists before the row is inserted.
     *
     * @param array<int, array<string, mixed>> $rows
     * @return array<int, array<string, mixed>>
     */
    private function orderParentsBeforeChildren(array $rows): array
    {
        $byId = [];
        foreach ($rows as $row) {
            $byId[(int) $row['id']] = $row;
        }
        $ordered = [];
        $added = [];
        $pending = $rows;
        while (! empty($pending)) {
            $before = count($pending);
            foreach ($pending as $i => $row) {
                $parentId = $row['parent_id'];
                if ($parentId === null || isset($added[$parentId])) {
                    $ordered[] = $row;
                    $added[(int) $row['id']] = true;
                    unset($pending[$i]);
                }
            }
            if (count($pending) === $before) {
                break;
            }
        }
        if (! empty($pending)) {
            throw new \RuntimeException('Plan items seeder: circular or missing parent_id references.');
        }
        return $ordered;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function getRows(): array
    {
        return array (
            0 =>
            array (
                'id' => 1,
                'plan_id' => 1,
                'sort_order' => 10,
                'active' => 1,
                'parent_id' => NULL,
            ),
            1 =>
            array (
                'id' => 2,
                'plan_id' => 1,
                'sort_order' => 20,
                'active' => 1,
                'parent_id' => 1,
            ),
            2 =>
            array (
                'id' => 3,
                'plan_id' => 1,
                'sort_order' => 30,
                'active' => 1,
                'parent_id' => 1,
            ),
            3 =>
            array (
                'id' => 4,
                'plan_id' => 1,
                'sort_order' => 40,
                'active' => 1,
                'parent_id' => 1,
            ),
            4 =>
            array (
                'id' => 5,
                'plan_id' => 1,
                'sort_order' => 50,
                'active' => 1,
                'parent_id' => 1,
            ),
            5 =>
            array (
                'id' => 6,
                'plan_id' => 1,
                'sort_order' => 9999,
                'active' => 1,
                'parent_id' => NULL,
            ),
            6 =>
            array (
                'id' => 7,
                'plan_id' => 1,
                'sort_order' => 9999,
                'active' => 1,
                'parent_id' => 6,
            ),
            7 =>
            array (
                'id' => 8,
                'plan_id' => 1,
                'sort_order' => 9999,
                'active' => 1,
                'parent_id' => 6,
            ),
            8 =>
            array (
                'id' => 9,
                'plan_id' => 1,
                'sort_order' => 9999,
                'active' => 1,
                'parent_id' => 6,
            ),
            9 =>
            array (
                'id' => 10,
                'plan_id' => 1,
                'sort_order' => 9999,
                'active' => 1,
                'parent_id' => 6,
            ),
            10 =>
            array (
                'id' => 11,
                'plan_id' => 1,
                'sort_order' => 9999,
                'active' => 1,
                'parent_id' => NULL,
            ),
            11 =>
            array (
                'id' => 12,
                'plan_id' => 1,
                'sort_order' => 9999,
                'active' => 1,
                'parent_id' => 11,
            ),
            12 =>
            array (
                'id' => 13,
                'plan_id' => 1,
                'sort_order' => 9999,
                'active' => 1,
                'parent_id' => 11,
            ),
            13 =>
            array (
                'id' => 14,
                'plan_id' => 1,
                'sort_order' => 9999,
                'active' => 1,
                'parent_id' => 11,
            ),
            14 =>
            array (
                'id' => 15,
                'plan_id' => 1,
                'sort_order' => 9999,
                'active' => 1,
                'parent_id' => NULL,
            ),
            15 =>
            array (
                'id' => 16,
                'plan_id' => 2,
                'sort_order' => 9999,
                'active' => 1,
                'parent_id' => NULL,
            ),
            16 =>
            array (
                'id' => 17,
                'plan_id' => 2,
                'sort_order' => 9999,
                'active' => 1,
                'parent_id' => 16,
            ),
            17 =>
            array (
                'id' => 18,
                'plan_id' => 2,
                'sort_order' => 9999,
                'active' => 1,
                'parent_id' => 16,
            ),
            18 =>
            array (
                'id' => 19,
                'plan_id' => 2,
                'sort_order' => 9999,
                'active' => 1,
                'parent_id' => 24,
            ),
            19 =>
            array (
                'id' => 20,
                'plan_id' => 2,
                'sort_order' => 9999,
                'active' => 1,
                'parent_id' => 24,
            ),
            20 =>
            array (
                'id' => 21,
                'plan_id' => 2,
                'sort_order' => 9999,
                'active' => 1,
                'parent_id' => 24,
            ),
            21 =>
            array (
                'id' => 23,
                'plan_id' => 3,
                'sort_order' => 9999,
                'active' => 1,
                'parent_id' => NULL,
            ),
            22 =>
            array (
                'id' => 24,
                'plan_id' => 2,
                'sort_order' => 9999,
                'active' => 1,
                'parent_id' => NULL,
            ),
            23 =>
            array (
                'id' => 25,
                'plan_id' => 3,
                'sort_order' => 9999,
                'active' => 1,
                'parent_id' => NULL,
            ),
            24 =>
            array (
                'id' => 26,
                'plan_id' => 3,
                'sort_order' => 9999,
                'active' => 1,
                'parent_id' => NULL,
            ),
            25 =>
            array (
                'id' => 27,
                'plan_id' => 3,
                'sort_order' => 9999,
                'active' => 1,
                'parent_id' => NULL,
            ),
            26 =>
            array (
                'id' => 28,
                'plan_id' => 4,
                'sort_order' => 9999,
                'active' => 1,
                'parent_id' => NULL,
            ),
            27 =>
            array (
                'id' => 29,
                'plan_id' => 4,
                'sort_order' => 9999,
                'active' => 1,
                'parent_id' => NULL,
            ),
            28 =>
            array (
                'id' => 30,
                'plan_id' => 4,
                'sort_order' => 9999,
                'active' => 1,
                'parent_id' => NULL,
            ),
            29 =>
            array (
                'id' => 31,
                'plan_id' => 4,
                'sort_order' => 9999,
                'active' => 1,
                'parent_id' => NULL,
            ),
            30 =>
            array (
                'id' => 32,
                'plan_id' => 1,
                'sort_order' => 9999,
                'active' => 1,
                'parent_id' => NULL,
            ),
            31 =>
            array (
                'id' => 33,
                'plan_id' => 1,
                'sort_order' => 9999,
                'active' => 1,
                'parent_id' => 32,
            ),
            32 =>
            array (
                'id' => 34,
                'plan_id' => 1,
                'sort_order' => 9999,
                'active' => 1,
                'parent_id' => 32,
            ),
            33 =>
            array (
                'id' => 35,
                'plan_id' => 1,
                'sort_order' => 9999,
                'active' => 1,
                'parent_id' => 32,
            ),
            34 =>
            array (
                'id' => 36,
                'plan_id' => 1,
                'sort_order' => 9999,
                'active' => 1,
                'parent_id' => 15,
            ),
            35 =>
            array (
                'id' => 37,
                'plan_id' => 1,
                'sort_order' => 9999,
                'active' => 1,
                'parent_id' => 15,
            ),
            36 =>
            array (
                'id' => 38,
                'plan_id' => 5,
                'sort_order' => 9999,
                'active' => 1,
                'parent_id' => NULL,
            ),
            37 =>
            array (
                'id' => 39,
                'plan_id' => 5,
                'sort_order' => 9999,
                'active' => 1,
                'parent_id' => NULL,
            ),
            38 =>
            array (
                'id' => 40,
                'plan_id' => 5,
                'sort_order' => 9999,
                'active' => 1,
                'parent_id' => NULL,
            ),
            39 =>
            array (
                'id' => 41,
                'plan_id' => 5,
                'sort_order' => 9999,
                'active' => 1,
                'parent_id' => NULL,
            ),
            40 =>
            array (
                'id' => 42,
                'plan_id' => 5,
                'sort_order' => 9999,
                'active' => 1,
                'parent_id' => NULL,
            ),
        );
    }
}
