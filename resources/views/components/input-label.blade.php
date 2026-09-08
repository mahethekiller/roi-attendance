@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-semibold text-xs uppercase tracking-wider text-base-content/70 mb-1']) }}>
    {{ $value ?? $slot }}
</label>
