<?php

namespace App\Filament\Clusters;

use BackedEnum;
use Filament\Clusters\Cluster;

class CatalogCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 10;

    public static function getNavigationLabel(): string
    {
        return __('filament.clusters.catalog');
    }

    public static function getClusterBreadcrumb(): ?string
    {
        return __('filament.clusters.catalog');
    }
}

