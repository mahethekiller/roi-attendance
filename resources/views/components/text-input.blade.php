@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'input input-bordered w-full bg-base-100 text-base-content']) }}>
