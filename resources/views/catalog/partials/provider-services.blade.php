{{--
    Provider catalog: same listing and "new service" selector as the former provider dashboard block.
    Expects: $services (Collection), $serviceTypes (Collection)
--}}
<div class="row mt-4">
    <div class="col-lg-12">
        <h4 class="h5 mb-3">{{ __('wizard.provider_services_title') }}</h4>

        @if ($services->isEmpty())
            <div class="alert alert-light border mb-3" role="status">
                {{ __('wizard.provider_services_empty') }}
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle bg-white rounded border">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="text-center" style="width: 76px;">{{ __('wizard.provider_services_col_thumb') }}</th>
                            <th scope="col">{{ __('wizard.provider_services_col_name') }}</th>
                            <th scope="col">{{ __('wizard.provider_services_col_type') }}</th>
                            <th scope="col">{{ __('wizard.provider_services_col_status') }}</th>
                            <th scope="col" class="text-end">{{ __('wizard.provider_services_col_actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($services as $svc)
                            <tr>
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
                                    @php $st = $svc->status ?? ''; @endphp
                                    {{ $st !== '' ? __('filament.resources.service_status.'.$st) : '—' }}
                                </td>
                                <td class="text-end text-nowrap">
                                    @if ($svc->serviceType)
                                        <a
                                            href="{{ route('services.wizard.step1.edit', ['serviceType' => $svc->serviceType->code, 'service' => $svc->id]) }}"
                                            class="btn btn-sm btn-outline-primary me-1"
                                        >
                                            {{ __('wizard.provider_services_action_step1') }}
                                        </a>
                                        <a
                                            href="{{ route('services.wizard.step2', ['serviceType' => $svc->serviceType->code, 'service' => $svc->id]) }}"
                                            class="btn btn-sm btn-outline-secondary"
                                        >
                                            {{ __('wizard.provider_services_action_step2') }}
                                        </a>
                                        <a
                                            href="{{ route('services.wizard.step3', ['serviceType' => $svc->serviceType->code, 'service' => $svc->id]) }}"
                                            class="btn btn-sm btn-outline-secondary me-1"
                                        >
                                            {{ __('wizard.provider_services_action_step3') }}
                                        </a>
                                        <a
                                            href="{{ route('services.wizard.step4', ['serviceType' => $svc->serviceType->code, 'service' => $svc->id]) }}"
                                            class="btn btn-sm btn-outline-secondary"
                                        >
                                            {{ __('wizard.provider_services_action_step4') }}
                                        </a>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-12 col-xl-8">
        @if ($serviceTypes->isEmpty())
            <div class="alert alert-warning mb-0" role="alert">
                {{ __('wizard.provider_no_service_types') }}
            </div>
        @else
            <div class="mb-0">
                <select
                    id="provider-new-service-type"
                    class="form-select form-select-lg"
                    aria-label="{{ __('wizard.provider_new_service_placeholder') }}"
                    onchange="if (this.value) { window.location.href = this.value; }"
                >
                    <option value="">{{ __('wizard.provider_new_service_placeholder') }}</option>
                    @foreach ($serviceTypes as $serviceType)
                        <option value="{{ route('services.wizard.step1', ['serviceType' => $serviceType->code]) }}">
                            {{ $serviceType->name !== '' ? $serviceType->name : strtoupper($serviceType->code) }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif
    </div>
</div>
