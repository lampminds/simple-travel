<?php

namespace App\Support;

final class AccountTypeCategoryIds
{
    public const PROVIDER = 3;

    /** Business type `operator` in cat_account_categories (operator dashboard, catalog, relationships, menus). */
    public const OPERATOR = 4;

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
