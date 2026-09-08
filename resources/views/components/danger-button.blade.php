<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-error gap-2 shadow-xs']) }}>
    {{ $slot }}
</button>
