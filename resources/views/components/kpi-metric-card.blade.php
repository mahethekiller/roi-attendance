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

<div {{ $attributes->merge(['class' => 'card border-0 shadow-sm dashboard-card bg-body text-body h-100']) }}>
    <div class="card-body p-3">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="text-body-secondary small fw-semibold text-uppercase tracking-wider" style="font-size: 0.75rem; letter-spacing: 0.04em;">
                {{ $title }}
            </span>
            <div class="rounded-circle p-2 bg-{{ $color }}-subtle text-{{ $color }} d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                <i data-lucide="{{ $icon }}" style="width: 18px; height: 18px;"></i>
            </div>
        </div>
        <div class="d-flex align-items-baseline justify-content-between">
            <h3 class="mb-0 fw-bold text-body-emphasis font-monospace" style="font-size: 1.75rem;">
                {{ $value }}
            </h3>
            @if($trend)
                <span class="badge bg-{{ $trendType }}-subtle text-{{ $trendType }} border border-{{ $trendType }}-subtle d-inline-flex align-items-center gap-1 fw-semibold" style="font-size: 0.75rem;">
                    @if($trendIcon)
                        <i data-lucide="{{ $trendIcon }}" style="width: 13px; height: 13px;"></i>
                    @endif
                    {{ $trend }}
                </span>
            @endif
        </div>
        @if($subtitle)
            <div class="mt-2 text-body-secondary small" style="font-size: 0.78rem;">
                {{ $subtitle }}
            </div>
        @endif
    </div>
</div>
