<x-admin-layout>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-base-content tracking-tight">Edit User: {{ $user->name }}</h1>
            <p class="text-sm text-base-content/70 mt-0.5">Update personal information, change password, and modify role permissions.</p>
        </div>
        <div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline btn-sm gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Back to Users</span>
            </a>
        </div>
    </div>

    <div class="flex justify-center">
        <div class="w-full max-w-3xl">
            <div class="card bg-base-100 border border-base-200/60 shadow-xs">
                <div class="card-body p-6 sm:p-8">
                    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-5">
                        @csrf
                        @method('PUT')

                        <!-- Full Name -->
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend text-xs font-bold uppercase tracking-wider text-base-content/70">Full Name <span class="text-error">*</span></legend>
                            <label class="input input-bordered flex items-center gap-2 w-full @error('name') input-error @enderror">
                                <i data-lucide="user" class="w-4 h-4 text-base-content/50 shrink-0"></i>
                                <input type="text" name="name" id="name" class="grow bg-transparent" value="{{ old('name', $user->name) }}" required>
                            </label>
                            @error('name')
                                <p class="fieldset-label text-error">{{ $message }}</p>
                            @enderror
                        </fieldset>

                        <!-- Email Address -->
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend text-xs font-bold uppercase tracking-wider text-base-content/70">Email Address <span class="text-error">*</span></legend>
                            <label class="input input-bordered flex items-center gap-2 w-full @error('email') input-error @enderror">
                                <i data-lucide="mail" class="w-4 h-4 text-base-content/50 shrink-0"></i>
                                <input type="email" name="email" id="email" class="grow bg-transparent" value="{{ old('email', $user->email) }}" required>
                            </label>
                            @error('email')
                                <p class="fieldset-label text-error">{{ $message }}</p>
                            @enderror
                        </fieldset>

                        <!-- Spatie Roles Assignment -->
                        <div class="space-y-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-base-content/70 block">Assigned Roles <span class="text-error">*</span></span>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                @foreach($roles as $role)
                                    <label class="flex items-center gap-3 p-3.5 rounded-lg border border-base-200 bg-base-200/40 hover:bg-base-200/70 cursor-pointer transition-colors">
                                        <input type="checkbox" name="roles[]" value="{{ $role->name }}" class="checkbox checkbox-primary checkbox-sm" id="role_{{ $role->id }}" {{ in_array($role->name, old('roles', $userRoleNames)) ? 'checked' : '' }}>
                                        <span class="text-sm font-semibold text-base-content">{{ ucfirst($role->name) }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('roles')
                                <p class="text-xs text-error mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Change Password Box -->
                        <div class="p-4 rounded-xl bg-base-200/50 border border-base-200/80 space-y-4">
                            <div>
                                <div class="flex items-center gap-2">
                                    <i data-lucide="shield-alert" class="w-4 h-4 text-warning"></i>
                                    <span class="font-semibold text-sm text-base-content">Change Password (Optional)</span>
                                </div>
                                <p class="text-xs text-base-content/60 mt-0.5">Leave blank if you do not want to alter the current user password.</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <fieldset class="fieldset">
                                    <legend class="fieldset-legend text-xs font-semibold text-base-content/80">New Password</legend>
                                    <label class="input input-bordered flex items-center gap-2 w-full bg-base-100 @error('password') input-error @enderror">
                                        <i data-lucide="lock" class="w-4 h-4 text-base-content/50 shrink-0"></i>
                                        <input type="password" name="password" id="password" class="grow bg-transparent" placeholder="New password">
                                    </label>
                                    @error('password')
                                        <p class="fieldset-label text-error">{{ $message }}</p>
                                    @enderror
                                </fieldset>

                                <fieldset class="fieldset">
                                    <legend class="fieldset-legend text-xs font-semibold text-base-content/80">Confirm New Password</legend>
                                    <label class="input input-bordered flex items-center gap-2 w-full bg-base-100">
                                        <i data-lucide="check" class="w-4 h-4 text-base-content/50 shrink-0"></i>
                                        <input type="password" name="password_confirmation" id="password_confirmation" class="grow bg-transparent" placeholder="Confirm new password">
                                    </label>
                                </fieldset>
                            </div>
                        </div>

                        <!-- Form Action Buttons -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-base-200/60">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Cancel</a>
                            <button type="submit" class="btn btn-primary px-6 gap-2">
                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                <span>Update User</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
