@props(['name', 'bag' => null])

@if ($bag)
    @error($name, $bag)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
@else
    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
@endif
