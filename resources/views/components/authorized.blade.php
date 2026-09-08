@props([
    'permission' => null,
    'role' => null,
    'requireAll' => false,
    'mode' => 'hide',
    'disabledTooltip' => 'You do not have permission to perform this action.',
])

@php
    $user = Auth::user();
    $isAuthorized = false;

    if ($user) {
        if ($user->hasRole('super-admin')) {
            $isAuthorized = true;
        } else {
            $permAuthorized = null;
            $roleAuthorized = null;

            if ($permission !== null) {
                $permissions = is_array($permission) ? $permission : [$permission];
                if ($requireAll) {
                    $permAuthorized = $user->hasAllPermissions($permissions);
                } else {
                    $permAuthorized = $user->hasAnyPermission($permissions);
                }
            }

            if ($role !== null) {
                $roles = is_array($role) ? $role : [$role];
                if ($requireAll) {
                    $roleAuthorized = $user->hasAllRoles($roles);
                } else {
                    $roleAuthorized = $user->hasAnyRole($roles);
                }
            }

            if ($permission !== null && $role !== null) {
                $isAuthorized = $requireAll
                    ? ($permAuthorized && $roleAuthorized)
                    : ($permAuthorized || $roleAuthorized);
            } elseif ($permission !== null) {
                $isAuthorized = $permAuthorized;
            } elseif ($role !== null) {
                $isAuthorized = $roleAuthorized;
            } else {
                $isAuthorized = true;
            }
        }
    }
@endphp

@if($isAuthorized)
    {{ $slot }}
@elseif($mode === 'disable')
    <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $disabledTooltip }}">
        <span style="pointer-events: none; opacity: 0.55;">
            {{ $slot }}
        </span>
    </span>
@endif
