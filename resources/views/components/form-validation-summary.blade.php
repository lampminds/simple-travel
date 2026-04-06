@props(['bag' => null])
{{--
    Generic validation banner: use once per form, above fields.
    Optional bag="profile" when using validateWithBag() so multiple forms on one page stay isolated.
--}}
@if ($bag ? $errors->getBag($bag)->any() : $errors->any())
    <div {{ $attributes->merge(['class' => 'alert alert-danger mb-3', 'role' => 'alert']) }}>
        {{ __('auth.form_errors_summary') }}
    </div>
@endif
