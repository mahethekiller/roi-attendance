@props([
    'title' => 'Access Restricted',
    'message' => 'You do not have the required permissions to view this section or component.',
    'permission' => null,
    'icon' => 'shield-alert',
])

<div {{ $attributes->merge(['class' => 'card border-0 shadow-sm bg-body text-body p-4 text-center']) }}>
    <div class="card-body py-4">
        <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-inline-flex align-items-center justify-content-center p-3 mb-3" style="width: 56px; height: 56px;">
            <i data-lucide="{{ $icon }}" style="width: 28px; height: 28px;"></i>
        </div>
        <h5 class="fw-bold text-body-emphasis mb-2">{{ $title }}</h5>
        <p class="text-body-secondary mb-3 mx-auto" style="max-width: 460px;">
            {{ $message }}
        </p>
        @if($permission)
            <div class="d-inline-block">
                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle font-monospace px-2 py-1">
                    Required Clearance: {{ $permission }}
                </span>
            </div>
        @endif
    </div>
</div>
