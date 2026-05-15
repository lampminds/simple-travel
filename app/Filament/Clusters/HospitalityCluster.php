<?php

namespace App\Filament\Clusters;

use BackedEnum;
use Filament\Clusters\Cluster;

class HospitalityCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home-modern';

    protected static ?int $navigationSort = 22;

    public static function getNavigationLabel(): string
    {
        return __('filament.clusters.hospitality');
    }

    public static function getClusterBreadcrumb(): ?string
    {
        return __('filament.clusters.hospitality');
    }
}

