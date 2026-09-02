<x-admin-layout>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-body-emphasis mb-1">Create New User</h2>
            <p class="text-body-secondary mb-0">Add a new user and assign roles and permissions.</p>
        </div>
        <div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-1 shadow-sm">
                <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
                <span>Back to Users</span>
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm bg-body text-body">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.users.store') }}">
                        @csrf

                        <!-- Full Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold text-body-emphasis">Full Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary text-body-secondary border-end-0">
                                    <i data-lucide="user" style="width: 18px; height: 18px;"></i>
                                </span>
                                <input type="text" name="name" id="name" class="form-control bg-body-tertiary border-start-0 text-body @error('name') is-invalid @enderror" placeholder="e.g. John Doe" value="{{ old('name') }}" required>
                            </div>
                            @error('name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email Address -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold text-body-emphasis">Email Address <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary text-body-secondary border-end-0">
                                    <i data-lucide="mail" style="width: 18px; height: 18px;"></i>
                                </span>
                                <input type="email" name="email" id="email" class="form-control bg-body-tertiary border-start-0 text-body @error('email') is-invalid @enderror" placeholder="e.g. john@example.com" value="{{ old('email') }}" required>
                            </div>
                            @error('email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Spatie Roles Assignment -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-body-emphasis">Assign Roles <span class="text-danger">*</span></label>
                            <div class="row g-2">
                                @foreach($roles as $role)
                                    <div class="col-sm-4">
                                        <div class="p-3 rounded-3 border bg-body-tertiary d-flex align-items-center gap-2">
                                            <input class="form-check-input mt-0" type="checkbox" name="roles[]" value="{{ $role->name }}" id="role_{{ $role->id }}" {{ in_array($role->name, old('roles', ['user'])) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-medium text-body-emphasis mb-0" for="role_{{ $role->id }}">
                                                {{ ucfirst($role->name) }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('roles')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4 border-secondary-subtle">

                        <!-- Password -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="password" class="form-label fw-semibold text-body-emphasis">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-body-tertiary text-body-secondary border-end-0">
                                        <i data-lucide="lock" style="width: 18px; height: 18px;"></i>
                                    </span>
                                    <input type="password" name="password" id="password" class="form-control bg-body-tertiary border-start-0 text-body @error('password') is-invalid @enderror" placeholder="Min. 8 characters" required>
                                </div>
                                @error('password')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label fw-semibold text-body-emphasis">Confirm Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-body-tertiary text-body-secondary border-end-0">
                                        <i data-lucide="check" style="width: 18px; height: 18px;"></i>
                                    </span>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control bg-body-tertiary border-start-0 text-body" placeholder="Re-type password" required>
                                </div>
                            </div>
                        </div>

                        <!-- Form Action Buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4 d-flex align-items-center gap-2">
                                <i data-lucide="save" style="width: 18px; height: 18px;"></i>
                                <span>Save User</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
