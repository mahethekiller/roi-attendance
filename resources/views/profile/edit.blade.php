<x-admin-layout>
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-base-content tracking-tight">Account Profile</h1>
        <p class="text-sm text-base-content/70 mt-0.5">Manage your administrator profile details, security password, and account settings.</p>
    </div>

    <!-- Feedback Alerts -->
    @if (session('status') === 'profile-updated')
        <div class="alert alert-success shadow-xs mb-6" role="alert">
            <i data-lucide="check-circle" class="w-5 h-5 shrink-0"></i>
            <span>Your profile information has been saved successfully.</span>
        </div>
    @elseif (session('status') === 'password-updated')
        <div class="alert alert-success shadow-xs mb-6" role="alert">
            <i data-lucide="check-circle" class="w-5 h-5 shrink-0"></i>
            <span>Your password has been updated successfully.</span>
        </div>
    @elseif (session('status') === 'verification-link-sent')
        <div class="alert alert-info shadow-xs mb-6" role="alert">
            <i data-lucide="mail" class="w-5 h-5 shrink-0"></i>
            <span>A new verification link has been sent to your email address.</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- 1. Profile Information Card -->
        <div class="card bg-base-100 border border-base-200/60 shadow-xs">
            <div class="p-4 border-b border-base-200/60 flex items-center gap-2">
                <i data-lucide="user" class="w-5 h-5 text-primary"></i>
                <h2 class="font-bold text-base-content">Profile Information</h2>
            </div>
            <div class="card-body p-6">
                <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <fieldset class="fieldset">
                        <legend class="fieldset-legend text-xs font-bold uppercase tracking-wider text-base-content/70">Full Name <span class="text-error">*</span></legend>
                        <input type="text" name="name" id="name" class="input input-bordered w-full text-sm @error('name') input-error @enderror" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" aria-label="Full Name">
                        @error('name')
                            <p class="fieldset-label text-xs text-error">{{ $message }}</p>
                        @enderror
                    </fieldset>

                    <fieldset class="fieldset">
                        <legend class="fieldset-legend text-xs font-bold uppercase tracking-wider text-base-content/70">Email Address <span class="text-error">*</span></legend>
                        <input type="email" name="email" id="email" class="input input-bordered w-full text-sm @error('email') input-error @enderror" value="{{ old('email', $user->email) }}" required autocomplete="username" aria-label="Email Address">
                        @error('email')
                            <p class="fieldset-label text-xs text-error">{{ $message }}</p>
                        @enderror

                        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                            <div class="mt-2 text-xs text-warning flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                <span>Your email address is unverified.</span>
                            </div>
                        @endif
                    </fieldset>

                    <div class="flex justify-end pt-4 border-t border-base-200/60">
                        <button type="submit" class="btn btn-primary gap-2">
                            <i data-lucide="check" class="w-4 h-4"></i>
                            <span>Save Changes</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 2. Update Password Card -->
        <div class="card bg-base-100 border border-base-200/60 shadow-xs">
            <div class="p-4 border-b border-base-200/60 flex items-center gap-2">
                <i data-lucide="lock" class="w-5 h-5 text-primary"></i>
                <h2 class="font-bold text-base-content">Update Security Password</h2>
            </div>
            <div class="card-body p-6">
                <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <fieldset class="fieldset">
                        <legend class="fieldset-legend text-xs font-bold uppercase tracking-wider text-base-content/70">Current Password <span class="text-error">*</span></legend>
                        <input type="password" name="current_password" id="current_password" class="input input-bordered w-full text-sm @if($errors->updatePassword->has('current_password')) input-error @endif" autocomplete="current-password" aria-label="Current Password">
                        @if($errors->updatePassword->has('current_password'))
                            <p class="fieldset-label text-xs text-error">{{ $errors->updatePassword->first('current_password') }}</p>
                        @endif
                    </fieldset>

                    <fieldset class="fieldset">
                        <legend class="fieldset-legend text-xs font-bold uppercase tracking-wider text-base-content/70">New Password <span class="text-error">*</span></legend>
                        <input type="password" name="password" id="password" class="input input-bordered w-full text-sm @if($errors->updatePassword->has('password')) input-error @endif" autocomplete="new-password" aria-label="New Password">
                        @if($errors->updatePassword->has('password'))
                            <p class="fieldset-label text-xs text-error">{{ $errors->updatePassword->first('password') }}</p>
                        @endif
                    </fieldset>

                    <fieldset class="fieldset">
                        <legend class="fieldset-legend text-xs font-bold uppercase tracking-wider text-base-content/70">Confirm Password <span class="text-error">*</span></legend>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="input input-bordered w-full text-sm @if($errors->updatePassword->has('password_confirmation')) input-error @endif" autocomplete="new-password" aria-label="Confirm Password">
                        @if($errors->updatePassword->has('password_confirmation'))
                            <p class="fieldset-label text-xs text-error">{{ $errors->updatePassword->first('password_confirmation') }}</p>
                        @endif
                    </fieldset>

                    <div class="flex justify-end pt-4 border-t border-base-200/60">
                        <button type="submit" class="btn btn-primary gap-2">
                            <i data-lucide="key" class="w-4 h-4"></i>
                            <span>Update Password</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 3. Delete Account Card -->
        <div class="col-span-1 lg:col-span-2">
            <div class="card bg-base-100 border border-error/30 shadow-xs">
                <div class="card-body p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h3 class="font-bold text-error text-base mb-0.5">Delete Account</h3>
                        <p class="text-xs text-base-content/70">Permanently delete your user account and revoke all permissions.</p>
                    </div>
                    <button type="button" class="btn btn-error btn-outline gap-2 shrink-0" onclick="document.getElementById('confirmUserDeletionModal').showModal()">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                        <span>Delete Account</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm User Deletion Modal -->
    <dialog id="confirmUserDeletionModal" class="modal" @if($errors->userDeletion->isNotEmpty()) open @endif>
        <div class="modal-box bg-base-100 max-w-md border border-base-200">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            </form>
            <form method="POST" action="{{ route('profile.destroy') }}">
                @csrf
                @method('DELETE')

                <h3 class="font-bold text-lg text-error flex items-center gap-2 mb-3">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                    Confirm Account Deletion
                </h3>
                <p class="text-xs text-base-content/70 mb-4">
                    Are you sure you want to delete your account? Please enter your current password to confirm this permanent action.
                </p>

                <fieldset class="fieldset mb-6">
                    <legend class="fieldset-legend text-xs font-bold uppercase tracking-wider text-base-content/70">Current Password <span class="text-error">*</span></legend>
                    <input type="password" name="password" id="delete_password" class="input input-bordered w-full text-sm @if($errors->userDeletion->has('password')) input-error @endif" placeholder="Enter password to confirm" required aria-label="Current password for account deletion">
                    @if($errors->userDeletion->has('password'))
                        <p class="fieldset-label text-xs text-error">{{ $errors->userDeletion->first('password') }}</p>
                    @endif
                </fieldset>

                <div class="modal-action">
                    <button type="button" class="btn btn-ghost" onclick="document.getElementById('confirmUserDeletionModal').close()">Cancel</button>
                    <button type="submit" class="btn btn-error gap-2">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                        <span>Confirm Deletion</span>
                    </button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>
</x-admin-layout>
