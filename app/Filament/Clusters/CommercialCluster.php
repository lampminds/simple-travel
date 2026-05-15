<?php

namespace App\Filament\Clusters;

use BackedEnum;
use Filament\Clusters\Cluster;

class CommercialCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?int $navigationSort = 50;

    public static function getNavigationLabel(): string
    {
        return __('filament.clusters.commercial');
    }

    public static function getClusterBreadcrumb(): ?string
    {
        return __('filament.clusters.commercial');
    }
}

