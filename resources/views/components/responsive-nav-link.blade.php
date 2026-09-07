@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 rounded-2 text-start text-base font-semibold text-primary bg-primary bg-opacity-10 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 rounded-2 text-start text-base font-medium text-body-secondary hover:text-body hover:bg-body-tertiary transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
