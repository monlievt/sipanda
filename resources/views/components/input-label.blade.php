@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-semibold text-xs sm:text-sm text-ink mb-1']) }}>
    {{ $value ?? $slot }}
</label>
