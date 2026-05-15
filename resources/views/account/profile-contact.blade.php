@extends('layouts.base', ['title' => __('profile.contact_title')])

@section('content')

    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false,'topbarColor' => 'navbar-light', 'classList' => 'mx-auto' ])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title">
                        <h3 class="my-0">{{ __('profile.contact_heading') }}</h3>
                        <p class="mt-1 fw-medium">{{ __('profile.contact_subtitle') }}</p>
                    </div>
                </div>
            </div>

            @if (session('status'))
                <div class="alert alert-success mb-3" role="alert">{{ session('status') }}</div>
            @endif

            <div class="row mt-2">
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-3">{{ __('profile.contact_heading') }}</h5>

                            @if (! $profilePerson)
                                <div class="alert alert-warning mb-0" role="alert">{{ __('profile.contact_person_missing') }}</div>
                            @else
                                <form method="post" action="{{ route('account.contact.update') }}">
                                    @csrf
                                    @method('PUT')
                                    <x-form-validation-summary bag="contact" />
                                    @error('methods', 'contact')
                                        <div class="alert alert-danger" role="alert">{{ $message }}</div>
                                    @enderror
                                    <div class="alert alert-info" role="alert">
                                        {{ __('profile.contact_registration_email_locked_help') }}
                                    </div>

                                    @php
                                        $oldMethods = old('methods');
                                        $rows = [];
                                        if (is_array($oldMethods)) {
                                            $rows = array_values($oldMethods);
                                        } else {
                                            foreach ($contactMethods as $method) {
                                                $rows[] = [
                                                    'id' => $method->id,
                                                    'contact_type_id' => $method->contact_type_id,
                                                    'value' => $method->value,
                                                    'delete' => false,
                                                ];
                                            }
                                        }
                                        $nextIndex = count($rows);
                                    @endphp

                                    <div id="contact-method-rows">
                                        @forelse($rows as $idx => $row)
                                            @php
                                                $existingId = isset($row['id']) ? (int) $row['id'] : null;
                                                $isProtected = $existingId !== null && in_array($existingId, $protectedMethodIds ?? [], true);
                                            @endphp
                                            <div class="row g-2 align-items-end border rounded p-2 mb-2 contact-method-row">
                                                @if($existingId)
                                                    <input type="hidden" name="methods[{{ $idx }}][id]" value="{{ $existingId }}">
                                                @endif
                                                <div class="col-md-4">
                                                    <label class="form-label">{{ __('profile.contact_type') }}</label>
                                                    <select name="methods[{{ $idx }}][contact_type_id]" class="form-select @error("methods.$idx.contact_type_id", 'contact') is-invalid @enderror" @disabled($isProtected)>
                                                        <option value="">{{ __('profile.contact_select_type') }}</option>
                                                        @foreach($contactTypes as $type)
                                                            <option value="{{ $type->id }}" @selected((int) ($row['contact_type_id'] ?? 0) === (int) $type->id)>
                                                                {{ $type->code }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error("methods.$idx.contact_type_id", 'contact')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">{{ __('profile.contact_value') }}</label>
                                                    <input type="text"
                                                           name="methods[{{ $idx }}][value]"
                                                           class="form-control @error("methods.$idx.value", 'contact') is-invalid @enderror"
                                                           value="{{ (string) ($row['value'] ?? '') }}"
                                                           placeholder="{{ __('profile.contact_value_placeholder') }}"
                                                           @disabled($isProtected)>
                                                    @error("methods.$idx.value", 'contact')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-2">
                                                    @if($existingId)
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="checkbox"
                                                                   id="delete-method-{{ $existingId }}"
                                                                   name="methods[{{ $idx }}][delete]" value="1"
                                                                   @checked((bool) ($row['delete'] ?? false))
                                                                   @disabled($isProtected)>
                                                            <label class="form-check-label" for="delete-method-{{ $existingId }}">
                                                                {{ __('profile.contact_delete') }}
                                                            </label>
                                                        </div>
                                                    @else
                                                        <button type="button" class="btn btn-outline-danger btn-sm js-remove-row">{{ __('profile.contact_remove_row') }}</button>
                                                    @endif
                                                </div>
                                                @if($isProtected)
                                                    <div class="col-12">
                                                        <small class="text-muted d-block mt-1">{{ __('profile.contact_registration_email_locked_row') }}</small>
                                                    </div>
                                                @endif
                                            </div>
                                        @empty
                                            <p class="text-muted">{{ __('profile.contact_empty') }}</p>
                                        @endforelse
                                    </div>

                                    <button type="button" class="btn btn-outline-secondary me-2" id="btn-add-contact-row">{{ __('profile.contact_add_row') }}</button>
                                    <button type="submit" class="btn btn-primary">{{ __('profile.contact_save') }}</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-site-footer-simple />

@endsection

@section('script-bottom')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var container = document.getElementById('contact-method-rows');
            var addBtn = document.getElementById('btn-add-contact-row');
            if (!container || !addBtn) {
                return;
            }

            var nextIndex = {{ $nextIndex ?? 0 }};
            var optionsHtml = @json(collect($contactTypes)->map(fn ($type) => ['id' => (int) $type->id, 'code' => (string) $type->code])->values());
            function buildOptions() {
                var html = '<option value="">{{ __('profile.contact_select_type') }}</option>';
                optionsHtml.forEach(function (opt) {
                    html += '<option value="' + opt.id + '">' + opt.code + '</option>';
                });
                return html;
            }

            addBtn.addEventListener('click', function () {
                var idx = nextIndex++;
                var row = document.createElement('div');
                row.className = 'row g-2 align-items-end border rounded p-2 mb-2 contact-method-row';
                row.innerHTML =
                    '<div class="col-md-4">' +
                        '<label class="form-label">{{ __('profile.contact_type') }}</label>' +
                        '<select name="methods[' + idx + '][contact_type_id]" class="form-select">' +
                            buildOptions() +
                        '</select>' +
                    '</div>' +
                    '<div class="col-md-6">' +
                        '<label class="form-label">{{ __('profile.contact_value') }}</label>' +
                        '<input type="text" name="methods[' + idx + '][value]" class="form-control" placeholder="{{ __('profile.contact_value_placeholder') }}">' +
                    '</div>' +
                    '<div class="col-md-2">' +
                        '<button type="button" class="btn btn-outline-danger btn-sm js-remove-row">{{ __('profile.contact_remove_row') }}</button>' +
                    '</div>';
                container.appendChild(row);
            });

            container.addEventListener('click', function (event) {
                var target = event.target;
                if (target && target.classList.contains('js-remove-row')) {
                    var row = target.closest('.contact-method-row');
                    if (row) {
                        row.remove();
                    }
                }
            });
        });
    </script>
@endsection
