<?php

namespace App\Filament\Clusters;

use BackedEnum;
use Filament\Clusters\Cluster;

class TransportCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-truck';

    protected static ?int $navigationSort = 21;

    public static function getNavigationLabel(): string
    {
        return __('filament.clusters.transport');
    }

    public static function getClusterBreadcrumb(): ?string
    {
        return __('filament.clusters.transport');
    }
}
