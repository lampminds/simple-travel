<div>
    <table class="table table-hover align-middle bg-white rounded border">
        <thead class="table-light">
            <tr>
                <th scope="col" class="text-center" style="width: 76px;">{{ __('wizard.provider_services_col_thumb') }}</th>
                <th scope="col">{{ __('wizard.provider_services_col_name') }}</th>
                <th scope="col">{{ __('wizard.provider_services_col_type') }}</th>
                <th scope="col">{{ __('wizard.provider_services_col_status') }}</th>
                <th scope="col" class="text-center">{{ __('wizard.provider_services_col_variants') }}</th>
                <th scope="col" class="text-end">{{ __('wizard.provider_services_col_actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($services as $svc)
                <tr wire:key="provider-service-row-{{ $svc->id }}">
                    <td class="text-center align-middle">
                        @if ($url = $svc->mainImageUrl(\App\Models\Service::MEDIA_CONVERSION_THUMBNAIL))
                            <img src="{{ $url }}" alt="" class="rounded border" style="width: 56px; height: 56px; object-fit: cover;">
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td>{{ $svc->name !== '' ? $svc->name : ('#'.$svc->id) }}</td>
                    <td>{{ $svc->serviceType?->name ?: strtoupper($svc->serviceType?->code ?? '') }}</td>
                    <td>
                        @include('catalog.partials.catalog-status-badge', [
                            'presentation' => \App\Support\ServiceCatalogStatus::forService($svc->status ?? null),
                        ])
                    </td>
                    <td class="text-center">
                        @if ($this->skipsVariantsForType($svc->serviceType?->code))
                            <span class="text-muted small">—</span>
                        @else
                            {{ (int) ($svc->service_variants_count ?? 0) }}
                        @endif
                    </td>
                    <td class="text-end text-nowrap">
                        @if ($svc->serviceType)
                            <button
                                type="button"
                                class="btn btn-sm btn-outline-primary me-1"
                                title="{{ __('wizard.service_copy_button') }}"
                                aria-label="{{ __('wizard.service_copy_button') }}"
                                wire:click="requestCopy({{ $svc->id }})"
                            >
                                <i class="icon icon-xs" data-feather="copy" aria-hidden="true"></i>
                            </button>
                            <div class="dropdown d-inline-block">
                                <button
                                    class="btn btn-sm btn-outline-secondary"
                                    type="button"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false"
                                    aria-label="{{ __('wizard.provider_services_actions_menu') }}"
                                >
                                    <i class="icon icon-xs text-primary" data-feather="more-horizontal" aria-hidden="true"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a
                                            href="{{ route('services.wizard.step1.edit', ['serviceType' => $svc->serviceType->code, 'service' => $svc]) }}"
                                            class="dropdown-item"
                                        >
                                            <i class="icon-xxs icon me-2 text-primary" data-feather="edit-3" aria-hidden="true"></i>
                                            {{ __('wizard.provider_services_action_step1') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a
                                            href="{{ route('services.wizard.step2', ['serviceType' => $svc->serviceType->code, 'service' => $svc]) }}"
                                            class="dropdown-item"
                                        >
                                            <i class="icon-xxs icon me-2 text-success" data-feather="toggle-right" aria-hidden="true"></i>
                                            {{ __('wizard.provider_services_action_step2') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a
                                            href="{{ route('services.wizard.step3', ['serviceType' => $svc->serviceType->code, 'service' => $svc]) }}"
                                            class="dropdown-item"
                                        >
                                            <i class="icon-xxs icon me-2 text-info" data-feather="sliders" aria-hidden="true"></i>
                                            {{ __('wizard.provider_services_action_step3') }}
                                        </a>
                                    </li>
                                    @unless ($this->skipsVariantsForType($svc->serviceType->code))
                                        <li>
                                            <a
                                                href="{{ route('services.wizard.step4', ['serviceType' => $svc->serviceType->code, 'service' => $svc]) }}"
                                                class="dropdown-item"
                                            >
                                                <i class="icon-xxs icon me-2 text-warning" data-feather="layers" aria-hidden="true"></i>
                                                {{ __('wizard.provider_services_action_step4') }}
                                            </a>
                                        </li>
                                    @endunless
                                    <li>
                                        <a
                                            href="{{ route('services.wizard.step5', ['serviceType' => $svc->serviceType->code, 'service' => $svc]) }}"
                                            class="dropdown-item"
                                        >
                                            <i class="icon-xxs icon me-2 text-danger" data-feather="image" aria-hidden="true"></i>
                                            {{ __('wizard.provider_services_action_step5') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a
                                            href="{{ route('services.wizard.step6', ['serviceType' => $svc->serviceType->code, 'service' => $svc]) }}"
                                            class="dropdown-item"
                                        >
                                            <i class="icon-xxs icon me-2 text-secondary" data-feather="file-text" aria-hidden="true"></i>
                                            {{ __('wizard.provider_services_action_step6') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a
                                            href="{{ route('services.wizard.step7', ['serviceType' => $svc->serviceType->code, 'service' => $svc]) }}"
                                            class="dropdown-item"
                                        >
                                            <i class="icon-xxs icon me-2 text-primary" data-feather="package" aria-hidden="true"></i>
                                            {{ __('wizard.provider_services_action_step7') }}
                                        </a>
                                    </li>
                                    @if ($this->hasAdvancedStepForType($svc->serviceType->code))
                                        <li>
                                            <a
                                                href="{{ route('services.wizard.step8', ['serviceType' => $svc->serviceType->code, 'service' => $svc]) }}"
                                                class="dropdown-item"
                                            >
                                                <i class="icon-xxs icon me-2 text-primary" data-feather="settings" aria-hidden="true"></i>
                                                {{ ($svc->serviceType->code ?? '') === 'transfer' ? __('wizard.provider_services_action_step8_transfer') : __('wizard.provider_services_action_step8') }}
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
