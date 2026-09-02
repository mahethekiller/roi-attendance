<x-admin-layout>
    <!-- Page Header & Actions -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold text-body-emphasis mb-1">User Management</h2>
            <p class="text-body-secondary mb-0">Manage system users, assign Spatie roles, and configure administrative access.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary px-3 shadow-sm d-flex align-items-center gap-2">
                <i data-lucide="user-plus" style="width: 18px; height: 18px;"></i>
                <span>Add New User</span>
            </a>
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4 d-flex align-items-center gap-2" role="alert">
            <i data-lucide="check-circle" style="width: 20px; height: 20px;"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm mb-4 d-flex align-items-center gap-2" role="alert">
            <i data-lucide="alert-circle" style="width: 20px; height: 20px;"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <!-- Metric Summary Grid -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm bg-body text-body">
                <div class="card-body p-3">
                    <span class="text-body-secondary small fw-medium">Total Registered Users</span>
                    <h3 class="mb-0 fw-bold text-body-emphasis mt-1">{{ $totalUsers }}</h3>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm bg-body text-body">
                <div class="card-body p-3">
                    <span class="text-body-secondary small fw-medium">Administrators</span>
                    <h3 class="mb-0 fw-bold text-body-emphasis mt-1">{{ $totalAdmins }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="card border-0 shadow-sm bg-body text-body mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 align-items-center">
                <div class="col-12 col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-body-tertiary border-end-0 text-body-secondary">
                            <i data-lucide="search" style="width: 18px; height: 18px;"></i>
                        </span>
                        <input type="search" name="search" class="form-control bg-body-tertiary border-start-0 text-body" placeholder="Search by name or email..." value="{{ $search }}">
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <select name="role" class="form-select bg-body-tertiary text-body">
                        <option value="">All Roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ $roleFilter === $role->name ? 'selected' : '' }}>
                                Role: {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-1">
                        <i data-lucide="filter" style="width: 16px; height: 16px;"></i> Filter
                    </button>
                    @if($search || $roleFilter)
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center">
                            <i data-lucide="rotate-ccw" style="width: 16px; height: 16px;"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table Card -->
    <div class="card border-0 shadow-sm bg-body text-body">
        <div class="card-header bg-body border-bottom p-3 d-flex align-items-center justify-content-between">
            <h6 class="fw-bold mb-0 text-body-emphasis">Users List ({{ $users->total() }})</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-body-secondary">
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Spatie Roles</th>
                        <th>Joined Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; font-size: 0.85rem;">
                                        {{ $user->initials }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-body-emphasis">{{ $user->name }}</div>
                                        @if(Auth::id() === $user->id)
                                            <span class="badge bg-info-subtle text-info border border-info-subtle" style="font-size: 0.65rem;">You</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-body-secondary">{{ $user->email }}</td>
                            <td>
                                @forelse($user->roles as $role)
                                    @php
                                        $badgeColor = match($role->name) {
                                            'super-admin' => 'danger',
                                            'admin' => 'warning',
                                            default => 'primary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $badgeColor }}-subtle text-{{ $badgeColor }} border border-{{ $badgeColor }}-subtle me-1">
                                        {{ ucfirst($role->name) }}
                                    </span>
                                @empty
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">No Role</span>
                                @endforelse
                            </td>
                            <td class="text-body-secondary">{{ $user->created_at->format('M d, Y') }}</td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-secondary" title="Edit User">
                                        <i data-lucide="edit-3" style="width: 15px; height: 15px;"></i>
                                    </a>
                                    @if(Auth::id() !== $user->id)
                                        <button type="button" class="btn btn-sm btn-outline-danger" title="Delete User"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteUserModal"
                                                data-user-id="{{ $user->id }}"
                                                data-user-name="{{ $user->name }}"
                                                data-user-action="{{ route('admin.users.destroy', $user) }}">
                                            <i data-lucide="trash-2" style="width: 15px; height: 15px;"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-body-secondary">
                                <i data-lucide="users" class="mb-2 d-block mx-auto text-body-tertiary" style="width: 36px; height: 36px;"></i>
                                <span>No users found matching your criteria.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="card-footer bg-body border-top p-3">
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-body border-0 shadow">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold text-body-emphasis" id="deleteUserModalLabel">
                        <i data-lucide="alert-triangle" class="text-danger me-2" style="width: 22px; height: 22px;"></i>
                        Confirm Deletion
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-body">
                    <p class="mb-2">Are you sure you want to permanently delete user <strong id="deleteUserName" class="text-body-emphasis"></strong>?</p>
                    <p class="text-body-secondary small mb-0">This action cannot be reversed and will revoke all permissions.</p>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteUserForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete User</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const deleteModal = document.getElementById('deleteUserModal');
            if (deleteModal) {
                deleteModal.addEventListener('show.bs.modal', (event) => {
                    const button = event.relatedTarget;
                    const userName = button.getAttribute('data-user-name');
                    const userAction = button.getAttribute('data-user-action');

                    document.getElementById('deleteUserName').textContent = userName;
                    document.getElementById('deleteUserForm').setAttribute('action', userAction);
                });
            }
        });
    </script>
</x-admin-layout>
