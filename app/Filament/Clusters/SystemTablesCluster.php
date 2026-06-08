<?php

namespace App\Filament\Clusters;

use BackedEnum;
use Filament\Clusters\Cluster;

class SystemTablesCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-table-cells';

    protected static ?int $navigationSort = 88;

    public static function getNavigationLabel(): string
    {
        return __('filament.clusters.system_tables');
    }

    public static function getClusterBreadcrumb(): ?string
    {
        return __('filament.clusters.system_tables');
    }
}
