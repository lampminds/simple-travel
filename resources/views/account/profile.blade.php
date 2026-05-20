@extends('layouts.base', ['title' => __('profile.profile_title')])

@section('content')

    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false,'topbarColor' => 'navbar-light', 'classList' => 'mx-auto' ])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title">
                        <h3 class="my-0">{{ __('profile.profile_heading') }}</h3>
                        <p class="mt-1 fw-medium">{{ __('profile.profile_subtitle') }}</p>
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
                            <h5 class="card-title mb-3">{{ __('profile.avatar_heading') }}</h5>
                            <p class="text-muted small mb-3">{{ __('profile.avatar_help') }}</p>

                            <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
                                <img src="{{ $profilePerson?->avatarDisplayUrl() ?? $user->avatarDisplayUrl() }}" width="128" height="128"
                                     class="rounded-circle border shadow-sm bg-light" alt="{{ $profilePerson?->name ?? $user->name }}"
                                     style="object-fit: cover;"/>
                                <div class="flex-grow-1">
                                    <form method="post" action="{{ route('account.profile.avatar') }}" enctype="multipart/form-data" class="mb-2">
                                        @csrf
                                        <x-form-validation-summary bag="avatar" />
                                        <label for="avatar" class="form-label">{{ __('profile.avatar_file_label') }}</label>
                                        <input type="file" name="avatar" id="avatar"
                                               class="form-control @error('avatar', 'avatar') is-invalid @enderror"
                                               accept="image/jpeg,image/png,image/gif,image/webp"/>
                                        <x-form-field-error name="avatar" bag="avatar" />
                                        <button type="submit" class="btn btn-primary btn-sm mt-2">{{ __('profile.avatar_upload') }}</button>
                                    </form>
                                    @if ($profilePerson?->hasUploadedAvatar())
                                        <form method="post" action="{{ route('account.profile.avatar.destroy') }}" class="d-inline"
                                              onsubmit="return confirm(@json(__('profile.avatar_remove_confirm')));">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm">{{ __('profile.avatar_remove') }}</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-3">{{ __('profile.profile_heading') }}</h5>
                            <form method="post" action="{{ route('account.profile.update') }}">
                                @csrf
                                @method('PUT')
                                <x-form-validation-summary bag="profile" />

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">{{ __('profile.name') }}</label>
                                            <input type="text" class="form-control @error('name', 'profile') is-invalid @enderror" id="name"
                                                   name="name" value="{{ old('name', $profilePerson?->name ?? $user->name) }}" required autocomplete="name"/>
                                            <x-form-field-error name="name" bag="profile" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="contact_department_id" class="form-label">{{ __('profile.department') }}</label>
                                            <select id="contact_department_id" name="contact_department_id" class="form-select @error('contact_department_id', 'profile') is-invalid @enderror" required>
                                                <option value="">{{ __('profile.select_department') }}</option>
                                                @foreach($departments as $department)
                                                    <option value="{{ $department->id }}" @selected((int) old('contact_department_id', $accountPerson?->contact_department_id) === (int) $department->id)>
                                                        {{ $department->code }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <x-form-field-error name="contact_department_id" bag="profile" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 offset-md-6">
                                        <div class="mb-3">
                                            <label for="contact_position_id" class="form-label">{{ __('profile.position') }}</label>
                                            <select id="contact_position_id" name="contact_position_id" class="form-select @error('contact_position_id', 'profile') is-invalid @enderror" required>
                                                <option value="">{{ __('profile.select_position') }}</option>
                                                @foreach($positions as $position)
                                                    <option value="{{ $position->id }}" @selected((int) old('contact_position_id', $accountPerson?->contact_position_id) === (int) $position->id)>
                                                        {{ $position->code }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <x-form-field-error name="contact_position_id" bag="profile" />
                                        </div>
                                    </div>
                                </div>

                                <div class="border rounded p-3 mb-3">
                                    <h6 class="fw-semibold mb-2">{{ __('profile.visibility_heading') }}</h6>
                                    <p class="text-muted small mb-3">{{ __('profile.visibility_help') }}</p>
                                    <div class="form-check mb-2">
                                        <input type="hidden" name="is_public_contact" value="0">
                                        <input class="form-check-input" type="checkbox" name="is_public_contact" id="is_public_contact" value="1"
                                               @checked((bool) old('is_public_contact', $accountPerson?->is_public_contact))>
                                        <label class="form-check-label" for="is_public_contact">{{ __('profile.is_public_contact') }}</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="hidden" name="is_preferred_contact_mode" value="0">
                                        <input class="form-check-input" type="checkbox" name="is_preferred_contact_mode" id="is_preferred_contact_mode" value="1"
                                               @checked((bool) old('is_preferred_contact_mode', $accountPerson?->is_preferred_contact_mode))>
                                        <label class="form-check-label" for="is_preferred_contact_mode">{{ __('profile.is_preferred_contact_mode') }}</label>
                                        <div class="form-text">{{ __('profile.is_preferred_contact_mode_help') }}</div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">{{ __('profile.save') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-site-footer-simple />

@endsection
