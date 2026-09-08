<x-admin-layout>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-base-content tracking-tight">Create New User</h1>
            <p class="text-sm text-base-content/70 mt-0.5">Add a new user and assign roles and permissions.</p>
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
                    <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5">
                        @csrf

                        <!-- Full Name -->
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend text-xs font-bold uppercase tracking-wider text-base-content/70">Full Name <span class="text-error">*</span></legend>
                            <label class="input input-bordered flex items-center gap-2 w-full @error('name') input-error @enderror">
                                <i data-lucide="user" class="w-4 h-4 text-base-content/50 shrink-0"></i>
                                <input type="text" name="name" id="name" class="grow bg-transparent" placeholder="e.g. John Doe" value="{{ old('name') }}" required>
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
                                <input type="email" name="email" id="email" class="grow bg-transparent" placeholder="e.g. john@example.com" value="{{ old('email') }}" required>
                            </label>
                            @error('email')
                                <p class="fieldset-label text-error">{{ $message }}</p>
                            @enderror
                        </fieldset>

                        <!-- Spatie Roles Assignment -->
                        <div class="space-y-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-base-content/70 block">Assign Roles <span class="text-error">*</span></span>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                @foreach($roles as $role)
                                    <label class="flex items-center gap-3 p-3.5 rounded-lg border border-base-200 bg-base-200/40 hover:bg-base-200/70 cursor-pointer transition-colors">
                                        <input type="checkbox" name="roles[]" value="{{ $role->name }}" class="checkbox checkbox-primary checkbox-sm" id="role_{{ $role->id }}" {{ in_array($role->name, old('roles', ['user'])) ? 'checked' : '' }}>
                                        <span class="text-sm font-semibold text-base-content">{{ ucfirst($role->name) }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('roles')
                                <p class="text-xs text-error mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="divider my-2"></div>

                        <!-- Password -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend text-xs font-bold uppercase tracking-wider text-base-content/70">Password <span class="text-error">*</span></legend>
                                <label class="input input-bordered flex items-center gap-2 w-full @error('password') input-error @enderror">
                                    <i data-lucide="lock" class="w-4 h-4 text-base-content/50 shrink-0"></i>
                                    <input type="password" name="password" id="password" class="grow bg-transparent" placeholder="Min. 8 characters" required>
                                </label>
                                @error('password')
                                    <p class="fieldset-label text-error">{{ $message }}</p>
                                @enderror
                            </fieldset>

                            <fieldset class="fieldset">
                                <legend class="fieldset-legend text-xs font-bold uppercase tracking-wider text-base-content/70">Confirm Password <span class="text-error">*</span></legend>
                                <label class="input input-bordered flex items-center gap-2 w-full">
                                    <i data-lucide="check" class="w-4 h-4 text-base-content/50 shrink-0"></i>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="grow bg-transparent" placeholder="Re-type password" required>
                                </label>
                            </fieldset>
                        </div>

                        <!-- Form Action Buttons -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-base-200/60">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Cancel</a>
                            <button type="submit" class="btn btn-primary px-6 gap-2">
                                <i data-lucide="save" class="w-4 h-4"></i>
                                <span>Save User</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
