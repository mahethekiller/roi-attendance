@props([
    'title' => '',
    'value' => '0',
    'subtitle' => null,
    'icon' => 'activity',
    'color' => 'primary',
    'trend' => null,
    'trendType' => 'success',
    'trendIcon' => null,
])

@php
    $numericVal = preg_replace('/[^0-9.]/', '', $value);
    $hasPercent = str_contains($value, '%');
@endphp

<div {{ $attributes->merge(['class' => 'card bg-base-100 shadow-sm border border-base-200 stat-card h-full']) }}>
    <div class="card-body p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-semibold uppercase tracking-wider text-base-content/70">
                {{ $title }}
            </span>
            <div class="rounded-full p-2 bg-{{ $color }}/10 text-{{ $color }} flex items-center justify-center kpi-icon-pill" style="width: 38px; height: 38px; transition: transform var(--duration-normal) var(--ease-out-expo);">
                <i data-lucide="{{ $icon }}" style="width: 18px; height: 18px;"></i>
            </div>
        </div>
        <div class="flex items-baseline justify-between">
            <h3 class="text-2xl font-bold text-base-content font-mono counter-value" 
                data-counter-target="{{ $numericVal }}" 
                data-counter-suffix="{{ $hasPercent ? '%' : '' }}">
                {{ $value }}
            </h3>
            @if($trend)
                <span class="badge badge-sm badge-{{ $trendType }} badge-soft font-semibold flex items-center gap-1 font-mono">
                    @if($trendIcon)
                        <i data-lucide="{{ $trendIcon }}" style="width: 13px; height: 13px;"></i>
                    @endif
                    {{ $trend }}
                </span>
            @endif
        </div>
        @if($subtitle)
            <div class="mt-2 text-xs text-base-content/60">
                {{ $subtitle }}
            </div>
        @endif
    </div>
</div>
