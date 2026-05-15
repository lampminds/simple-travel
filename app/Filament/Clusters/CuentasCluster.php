<?php

namespace App\Filament\Clusters;

use BackedEnum;
use Filament\Clusters\Cluster;

/**
 * Account-scoped application of catalog data (e.g. per-service condition rows).
 */
class CuentasCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    /** Below Dashboard, above Catalog. */
    protected static ?int $navigationSort = 5;

    public static function getNavigationLabel(): string
    {
        return __('filament.clusters.accounts');
    }

    public static function getClusterBreadcrumb(): ?string
    {
        return __('filament.clusters.accounts');
    }
}
