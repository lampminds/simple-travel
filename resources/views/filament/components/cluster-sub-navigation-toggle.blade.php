@php
    $storageKey = 'st_filament_cluster_subnav_hidden';
    $hideLabel = __('filament.panel.cluster_subnav_hide');
    $showLabel = __('filament.panel.cluster_subnav_show');
@endphp
<span
    id="st-cluster-subnav-toggle"
    class="fi-page-cluster-subnav-toggle hidden"
    data-storage-key="{{ $storageKey }}"
    data-label-hide="{{ $hideLabel }}"
    data-label-show="{{ $showLabel }}"
></span>
