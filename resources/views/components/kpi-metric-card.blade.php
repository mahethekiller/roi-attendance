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

<div {{ $attributes->merge(['class' => 'card bg-base-100 shadow-xs border border-base-200 stat-card h-full transition-all duration-200 hover:border-base-content/20 hover:shadow-md']) }}>
    <div class="card-body p-4 sm:p-5 flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between gap-2 mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider text-base-content/70 truncate">
                    {{ $title }}
                </span>
                <div class="rounded-xl p-2 bg-{{ $color }}/10 text-{{ $color }} shrink-0 flex items-center justify-center kpi-icon-pill" style="width: 36px; height: 36px;">
                    <i data-lucide="{{ $icon }}" style="width: 18px; height: 18px;"></i>
                </div>
            </div>
            <div class="flex items-baseline justify-between gap-2">
                <h3 class="text-2xl sm:text-3xl font-bold text-base-content font-mono tabular-nums tracking-tight counter-value" 
                    data-counter-target="{{ $numericVal }}" 
                    data-counter-suffix="{{ $hasPercent ? '%' : '' }}">
                    {{ $value }}
                </h3>
                @if($trend)
                    <span class="badge badge-sm badge-{{ $trendType }} badge-soft font-semibold flex items-center gap-1 font-mono shrink-0">
                        @if($trendIcon)
                            <i data-lucide="{{ $trendIcon }}" style="width: 12px; height: 12px;"></i>
                        @endif
                        {{ $trend }}
                    </span>
                @endif
            </div>
        </div>
        @if($subtitle)
            <div class="mt-3 pt-2.5 border-t border-base-200/80 text-xs text-base-content/60 leading-relaxed">
                {{ $subtitle }}
            </div>
        @endif
    </div>
</div>
