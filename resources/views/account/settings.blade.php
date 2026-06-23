@extends('layouts.base', ['title' => 'Configuración'])

@section('content')

    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false,'topbarColor' => 'navbar-light', 'classList' => 'mx-auto' ])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title">
                        <h3 class="my-0">Configuración</h3>
                        <p class="mt-1 fw-medium">Parámetros de tu cuenta</p>
                    </div>
                </div>
            </div>

            @if (session('status'))
                <div class="alert alert-success mb-3" role="alert">{{ session('status') }}</div>
            @endif

            @php
                $categories = $definitionsByCategory->keys()->values();
                $errorKeys = $errors->keys();
                $firstCategoryWithErrors = null;
                foreach ($categories as $categoryName) {
                    $defs = $definitionsByCategory->get($categoryName, collect());
                    $hasError = $defs->contains(function ($definition) use ($errorKeys): bool {
                        return collect($errorKeys)->contains('values.'.$definition->id);
                    });
                    if ($hasError) {
                        $firstCategoryWithErrors = $categoryName;
                        break;
                    }
                }
                $activeCategory = $firstCategoryWithErrors ?: ($categories->first() ?? null);
            @endphp

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    Revisa los parámetros. Hay errores en una o más secciones.
                </div>
            @endif

            <div class="row mt-2">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="{{ route('account.settings.update') }}" novalidate>
                                @csrf
                                @method('PUT')

                                <ul class="nav nav-tabs mb-3" id="tenant-settings-tabs" role="tablist">
                                    @foreach($categories as $categoryName)
                                        @php
                                            $defs = $definitionsByCategory->get($categoryName, collect());
                                            $categorySlug = \Illuminate\Support\Str::slug((string) $categoryName, '-');
                                            $hasCategoryErrors = $defs->contains(function ($definition) use ($errorKeys): bool {
                                                return collect($errorKeys)->contains('values.'.$definition->id);
                                            });
                                        @endphp
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link @if($activeCategory === $categoryName) active @endif @if($hasCategoryErrors) text-danger @endif"
                                                    id="tab-{{ $categorySlug }}"
                                                    data-bs-toggle="tab"
                                                    data-bs-target="#pane-{{ $categorySlug }}"
                                                    type="button"
                                                    role="tab"
                                                    aria-controls="pane-{{ $categorySlug }}"
                                                    aria-selected="{{ $activeCategory === $categoryName ? 'true' : 'false' }}">
                                                {{ $categoryName }}
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>

                                <div class="tab-content">
                                    @foreach($categories as $categoryName)
                                        @php
                                            $defs = $definitionsByCategory->get($categoryName, collect());
                                            $categorySlug = \Illuminate\Support\Str::slug((string) $categoryName, '-');
                                            $defsBySubcategory = $defs->groupBy(fn ($definition) => $definition->subcategory ?: 'General');
                                        @endphp
                                        <div class="tab-pane fade @if($activeCategory === $categoryName) show active @endif"
                                             id="pane-{{ $categorySlug }}"
                                             role="tabpanel"
                                             aria-labelledby="tab-{{ $categorySlug }}"
                                             tabindex="0">
                                            @foreach($defsBySubcategory as $subcategory => $subDefs)
                                                <div class="border rounded p-3 mb-3">
                                                    <h6 class="mb-3">{{ $subcategory }}</h6>
                                                    <div class="row">
                                                        @foreach($subDefs as $definition)
                                                            @php
                                                                $stored = $valuesByDefinitionId->get($definition->id)?->value;
                                                                $default = $definition->has_default ? $definition->default_value : null;
                                                                $currentValue = old('values.'.$definition->id, $stored ?? $default);
                                                                $usesOptions = \App\Models\ParameterDefinition::uiComponentRequiresOptions($definition->ui_component) && $definition->parameterOptions->count() >= 2;
                                                                $inputType = 'text';
                                                                if ($definition->type === 'integer' || $definition->type === 'decimal') {
                                                                    $inputType = 'number';
                                                                } elseif ($definition->type === 'date') {
                                                                    $inputType = 'date';
                                                                } elseif ($definition->type === 'time') {
                                                                    $inputType = 'time';
                                                                } elseif ($definition->type === 'datetime') {
                                                                    $inputType = 'datetime-local';
                                                                }
                                                            @endphp
                                                            <div class="col-lg-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label">{{ $definition->name !== '' ? $definition->name : $definition->code }}</label>

                                                                    @if($usesOptions)
                                                                        <select name="values[{{ $definition->id }}]" class="form-select @error('values.'.$definition->id) is-invalid @enderror">
                                                                            <option value="">—</option>
                                                                            @foreach($definition->parameterOptions as $option)
                                                                                <option value="{{ $option->value }}" @selected((string) $currentValue === (string) $option->value)>
                                                                                    {{ $option->labelForDisplay() }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    @elseif(in_array($definition->ui_component, ['textarea', 'editor'], true))
                                                                        <textarea name="values[{{ $definition->id }}]" rows="3" class="form-control @error('values.'.$definition->id) is-invalid @enderror">{{ (string) $currentValue }}</textarea>
                                                                    @elseif(in_array($definition->ui_component, ['checkbox', 'switch'], true) || $definition->type === 'boolean')
                                                                        <input type="hidden" name="values[{{ $definition->id }}]" value="0">
                                                                        <div class="form-check form-switch">
                                                                            <input class="form-check-input @error('values.'.$definition->id) is-invalid @enderror"
                                                                                   type="checkbox"
                                                                                   id="param-{{ $definition->id }}"
                                                                                   name="values[{{ $definition->id }}]"
                                                                                   value="1"
                                                                                   @checked((string) $currentValue === '1' || $currentValue === true || $currentValue === 1)>
                                                                            <label class="form-check-label" for="param-{{ $definition->id }}">Activado</label>
                                                                        </div>
                                                                    @else
                                                                        <input type="{{ $inputType }}"
                                                                               name="values[{{ $definition->id }}]"
                                                                               class="form-control @error('values.'.$definition->id) is-invalid @enderror"
                                                                               value="{{ (string) $currentValue }}">
                                                                    @endif

                                                                    @if($definition->help)
                                                                        <small class="text-muted d-block mt-1">{{ $definition->help }}</small>
                                                                    @endif

                                                                    @error('values.'.$definition->id)
                                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>

                                <div class="d-flex gap-2">
                                    <a class="btn btn-light" href="{{ route('account.dashboard') }}">Volver</a>
                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-site-footer-simple />

@endsection
