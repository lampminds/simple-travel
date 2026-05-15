<?php

namespace App\Filament\Clusters;

use BackedEnum;
use Filament\Clusters\Cluster;

class GastronomyCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cake';

    protected static ?int $navigationSort = 20;

    public static function getNavigationLabel(): string
    {
        return __('filament.clusters.gastronomy');
    }

    public static function getClusterBreadcrumb(): ?string
    {
        return __('filament.clusters.gastronomy');
    }
}

