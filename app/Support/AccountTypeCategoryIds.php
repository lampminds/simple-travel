<?php

namespace App\Support;

final class AccountTypeCategoryIds
{
    /** Provider lane ID in cat_account_types (routes, menus, dashboards). */
    public const PROVIDER = 3;

    /** Operator lane ID in cat_account_types (operator dashboard, catalog, relationships, menus). */
    public const OPERATOR = 4;

    /** Agency lane ID in cat_account_types. */
    public const AGENCY = 6;

    /**
     * @return list<int>
     */
    public static function allowed(): array
    {
        return [
            self::PROVIDER,
            self::OPERATOR,
            self::AGENCY,
        ];
    }

    /**
     * @return list<int>
     */
    public static function operatorLaneTypeIds(): array
    {
        return [self::OPERATOR];
    }

    public static function isOperatorLaneTypeId(int $id): bool
    {
        return in_array($id, self::operatorLaneTypeIds(), true);
    }
}
