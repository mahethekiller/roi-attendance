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
    $classes = $isActive
        ? 'active bg-primary text-white'
        : 'text-body-secondary';
@endphp

<x-authorized :permission="$permission" :role="$role">
    <li class="nav-item">
        <a href="{{ $href }}" class="nav-link {{ $classes }} rounded-3 px-3 py-2 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i data-lucide="{{ $icon }}" style="width: 18px; height: 18px;"></i>
                <span>{{ $label }}</span>
            </div>
            @if($badge)
                <span class="badge bg-{{ $badgeColor }}-subtle text-{{ $badgeColor }} border border-{{ $badgeColor }}-subtle" style="font-size: 0.7rem;">
                    {{ $badge }}
                </span>
            @endif
        </a>
    </li>
</x-authorized>
