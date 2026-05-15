<?php

namespace App\Filament\Clusters;

use BackedEnum;
use Filament\Clusters\Cluster;

class CrmCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?int $navigationSort = 30;

    public static function getNavigationLabel(): string
    {
        return __('filament.clusters.crm');
    }

    public static function getClusterBreadcrumb(): ?string
    {
        return __('filament.clusters.crm');
    }
}

