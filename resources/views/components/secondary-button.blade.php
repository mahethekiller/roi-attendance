<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn btn-outline gap-2 shadow-xs']) }}>
    {{ $slot }}
</button>
