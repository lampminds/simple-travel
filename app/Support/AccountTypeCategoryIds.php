<?php

namespace App\Support;

final class AccountTypeCategoryIds
{
    public const PROVIDER = 3;

    /** Tour operator (same UI lane as wholesaler / operator dashboard). */
    public const TOUR_OPERATOR = 4;

    public const WHOLESALER = 5;

    public const AGENCY = 6;

    /**
     * @return list<int>
     */
    public static function allowed(): array
    {
        return [
            self::PROVIDER,
            self::TOUR_OPERATOR,
            self::WHOLESALER,
            self::AGENCY,
        ];
    }

    /**
     * Operator / mayorista lane: wholesaler preferred over tour_operator when both exist.
     *
     * @return list<int>
     */
    public static function operatorLaneTypeIds(): array
    {
        return [self::WHOLESALER, self::TOUR_OPERATOR];
    }

    public static function isOperatorLaneTypeId(int $id): bool
    {
        return in_array($id, self::operatorLaneTypeIds(), true);
    }
}
