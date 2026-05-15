<?php

namespace App\Filament\Clusters;

use BackedEnum;
use Filament\Clusters\Cluster;

class AdministrationCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?int $navigationSort = 90;

    public static function getNavigationLabel(): string
    {
        return __('filament.clusters.administration');
    }

    public static function getClusterBreadcrumb(): ?string
    {
        return __('filament.clusters.administration');
    }
}

