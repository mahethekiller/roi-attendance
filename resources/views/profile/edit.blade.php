<x-admin-layout>
    <!-- Page Header -->
    <div class="mb-4">
        <h2 class="fw-bold text-body-emphasis mb-1">Account Profile</h2>
        <p class="text-body-secondary mb-0">Manage your administrator profile details, security password, and account settings.</p>
    </div>

    <!-- Feedback Alerts -->
    @if (session('status') === 'profile-updated')
        <div class="alert alert-success border-0 shadow-sm mb-4 d-flex align-items-center gap-2" role="alert">
            <i data-lucide="check-circle" style="width: 20px; height: 20px;"></i>
            <div>Your profile information has been saved successfully.</div>
        </div>
    @elseif (session('status') === 'password-updated')
        <div class="alert alert-success border-0 shadow-sm mb-4 d-flex align-items-center gap-2" role="alert">
            <i data-lucide="check-circle" style="width: 20px; height: 20px;"></i>
            <div>Your password has been updated successfully.</div>
        </div>
    @elseif (session('status') === 'verification-link-sent')
        <div class="alert alert-info border-0 shadow-sm mb-4 d-flex align-items-center gap-2" role="alert">
            <i data-lucide="mail" style="width: 20px; height: 20px;"></i>
            <div>A new verification link has been sent to your email address.</div>
        </div>
    @endif

    <div class="row g-4">
        <!-- 1. Profile Information Card -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm bg-body text-body h-100">
                <div class="card-header bg-body border-bottom p-3 d-flex align-items-center gap-2">
                    <i data-lucide="user" class="text-primary" style="width: 20px; height: 20px;"></i>
                    <h6 class="fw-bold mb-0 text-body-emphasis">Profile Information</h6>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold text-body-emphasis">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control bg-body-tertiary text-body @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" aria-label="Full Name">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold text-body-emphasis">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" class="form-control bg-body-tertiary text-body @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required autocomplete="username" aria-label="Email Address">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                <div class="mt-2 small text-warning d-flex align-items-center gap-1">
                                    <i data-lucide="alert-circle" style="width: 14px; height: 14px;"></i>
                                    <span>Your email address is unverified.</span>
                                </div>
                            @endif
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                                <i data-lucide="check" style="width: 16px; height: 16px;"></i>
                                <span>Save Changes</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- 2. Update Password Card -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm bg-body text-body h-100">
                <div class="card-header bg-body border-bottom p-3 d-flex align-items-center gap-2">
                    <i data-lucide="lock" class="text-primary" style="width: 20px; height: 20px;"></i>
                    <h6 class="fw-bold mb-0 text-body-emphasis">Update Security Password</h6>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="current_password" class="form-label fw-semibold text-body-emphasis">Current Password <span class="text-danger">*</span></label>
                            <input type="password" name="current_password" id="current_password" class="form-control bg-body-tertiary text-body @if($errors->updatePassword->has('current_password')) is-invalid @endif" autocomplete="current-password" aria-label="Current Password">
                            @if($errors->updatePassword->has('current_password'))
                                <div class="invalid-feedback">{{ $errors->updatePassword->first('current_password') }}</div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold text-body-emphasis">New Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" id="password" class="form-control bg-body-tertiary text-body @if($errors->updatePassword->has('password')) is-invalid @endif" autocomplete="new-password" aria-label="New Password">
                            @if($errors->updatePassword->has('password'))
                                <div class="invalid-feedback">{{ $errors->updatePassword->first('password') }}</div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label fw-semibold text-body-emphasis">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control bg-body-tertiary text-body @if($errors->updatePassword->has('password_confirmation')) is-invalid @endif" autocomplete="new-password" aria-label="Confirm Password">
                            @if($errors->updatePassword->has('password_confirmation'))
                                <div class="invalid-feedback">{{ $errors->updatePassword->first('password_confirmation') }}</div>
                            @endif
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                                <i data-lucide="key" style="width: 16px; height: 16px;"></i>
                                <span>Update Password</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- 3. Delete Account Card -->
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-body text-body border-start border-danger border-3">
                <div class="card-body p-4 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                    <div>
                        <h6 class="fw-bold text-danger mb-1">Delete Account</h6>
                        <p class="text-body-secondary small mb-0">Permanently delete your user account and revoke all permissions.</p>
                    </div>
                    <button type="button" class="btn btn-outline-danger d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#confirmUserDeletionModal">
                        <i data-lucide="trash-2" style="width: 16px; height: 16px;"></i>
                        <span>Delete Account</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm User Deletion Modal -->
    <div class="modal fade @if($errors->userDeletion->isNotEmpty()) show d-block @endif" id="confirmUserDeletionModal" tabindex="-1" aria-labelledby="confirmUserDeletionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-body border-0 shadow">
                <form method="POST" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('DELETE')

                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-bold text-danger" id="confirmUserDeletionModalLabel">
                            <i data-lucide="alert-triangle" class="me-2" style="width: 20px; height: 20px;"></i>
                            Confirm Account Deletion
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-body">
                        <p class="mb-3 text-body-secondary small">
                            Are you sure you want to delete your account? Please enter your current password to confirm this permanent action.
                        </p>
                        <div class="mb-3">
                            <label for="delete_password" class="form-label fw-semibold text-body-emphasis">Current Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" id="delete_password" class="form-control bg-body-tertiary text-body @if($errors->userDeletion->has('password')) is-invalid @endif" placeholder="Enter password to confirm" required aria-label="Current password for account deletion">
                            @if($errors->userDeletion->has('password'))
                                <div class="invalid-feedback">{{ $errors->userDeletion->first('password') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger d-flex align-items-center gap-1">
                            <i data-lucide="trash-2" style="width: 16px; height: 16px;"></i>
                            <span>Confirm Deletion</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
