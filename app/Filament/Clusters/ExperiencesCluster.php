<?php

namespace App\Filament\Clusters;

use BackedEnum;
use Filament\Clusters\Cluster;

class ExperiencesCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    protected static ?int $navigationSort = 25;

    public static function getNavigationLabel(): string
    {
        return __('filament.clusters.experiences');
    }

    public static function getClusterBreadcrumb(): ?string
    {
        return __('filament.clusters.experiences');
    }
}

