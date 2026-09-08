@props([
    'title' => 'Access Restricted',
    'message' => 'You do not have the required permissions to view this section or component.',
    'permission' => null,
    'icon' => 'shield-alert',
])

<div {{ $attributes->merge(['class' => 'card bg-base-100 border border-base-200 shadow-sm p-6 text-center']) }}>
    <div class="card-body py-4 items-center">
        <div class="rounded-full bg-warning/10 text-warning flex items-center justify-center p-3 mb-3" style="width: 56px; height: 56px;">
            <i data-lucide="{{ $icon }}" style="width: 28px; height: 28px;"></i>
        </div>
        <h3 class="font-bold text-lg text-base-content mb-1">{{ $title }}</h3>
        <p class="text-sm text-base-content/70 mb-4 max-w-md mx-auto">
            {{ $message }}
        </p>
        @if($permission)
            <div>
                <span class="badge badge-warning badge-soft font-mono px-3 py-1">
                    Required Clearance: {{ $permission }}
                </span>
            </div>
        @endif
    </div>
</div>
