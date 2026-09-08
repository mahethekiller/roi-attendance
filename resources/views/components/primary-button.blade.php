<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-primary gap-2 shadow-xs']) }}>
    {{ $slot }}
</button>
