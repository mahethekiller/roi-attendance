@props([
    'route' => null,
    'url' => '#',
    'activePattern' => null,
    'icon' => 'circle',
    'label' => '',
    'permission' => null,
    'role' => null,
    'badge' => null,
    'badgeColor' => 'primary',
])

@php
    $href = $route ? route($route) : $url;
    $isActive = $activePattern ? request()->routeIs($activePattern) : ($route ? request()->routeIs($route) : false);
@endphp

<x-authorized :permission="$permission" :role="$role">
    <li>
        <a href="{{ $href }}" class="{{ $isActive ? 'active bg-primary text-primary-content font-semibold' : 'text-base-content/80 hover:bg-base-200' }} flex items-center justify-between rounded-lg py-2.5 px-3 transition-colors">
            <div class="flex items-center gap-2.5">
                <i data-lucide="{{ $icon }}" style="width: 18px; height: 18px;"></i>
                <span>{{ $label }}</span>
            </div>
            @if($badge)
                <span class="badge badge-sm badge-{{ $badgeColor }} badge-soft font-mono">
                    {{ $badge }}
                </span>
            @endif
        </a>
    </li>
</x-authorized>
