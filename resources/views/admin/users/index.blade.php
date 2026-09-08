<x-admin-layout>
    <!-- Page Header & Actions -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-base-content tracking-tight">User Management</h1>
            <p class="text-sm text-base-content/70 mt-0.5">Manage system users, assign Spatie roles, and configure administrative access.</p>
        </div>
        <x-authorized permission="users.create">
            <div class="flex gap-2">
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm sm:btn-md gap-2 shadow-xs">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                    <span>Add New User</span>
                </a>
            </div>
        </x-authorized>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
        <div class="alert alert-success shadow-xs mb-6" role="alert">
            <i data-lucide="check-circle" class="w-5 h-5 shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error shadow-xs mb-6" role="alert">
            <i data-lucide="alert-circle" class="w-5 h-5 shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Metric Summary Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="card bg-base-100 border border-base-200/60 shadow-xs">
            <div class="card-body p-4">
                <span class="text-xs font-medium text-base-content/60 uppercase tracking-wider">Total Registered Users</span>
                <h3 class="text-2xl font-bold text-base-content mt-1">{{ $totalUsers }}</h3>
            </div>
        </div>
        <div class="card bg-base-100 border border-base-200/60 shadow-xs">
            <div class="card-body p-4">
                <span class="text-xs font-medium text-base-content/60 uppercase tracking-wider">Administrators</span>
                <h3 class="text-2xl font-bold text-base-content mt-1">{{ $totalAdmins }}</h3>
            </div>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="card bg-base-100 border border-base-200/60 shadow-xs mb-6">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                <div class="sm:col-span-6">
                    <div class="join w-full">
                        <span class="join-item btn btn-sm sm:btn-md btn-disabled bg-base-200 border-base-300 px-3">
                            <i data-lucide="search" class="w-4 h-4 text-base-content/60"></i>
                        </span>
                        <input type="search" name="search" class="input input-bordered input-sm sm:input-md join-item w-full bg-base-100 text-base-content" placeholder="Search by name or email..." aria-label="Search users by name or email" value="{{ $search }}">
                    </div>
                </div>
                <div class="sm:col-span-4">
                    <select name="role" class="select select-bordered select-sm sm:select-md w-full bg-base-100 text-base-content" aria-label="Filter by role">
                        <option value="">All Roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ $roleFilter === $role->name ? 'selected' : '' }}>
                                Role: {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2 flex items-center gap-2">
                    <button type="submit" class="btn btn-primary btn-sm sm:btn-md gap-2 w-full">
                        <i data-lucide="filter" class="w-4 h-4"></i> Filter
                    </button>
                    @if($search || $roleFilter)
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline btn-sm sm:btn-md btn-square" title="Reset Filters" aria-label="Reset filters">
                            <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table Card -->
    <div class="card bg-base-100 border border-base-200/60 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-base-200/60 flex items-center justify-between">
            <h2 class="font-semibold text-base-content">Users List ({{ $users->total() }})</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
                <thead>
                    <tr class="bg-base-200/50 text-base-content/70">
                        <th>User</th>
                        <th>Email</th>
                        <th>Spatie Roles</th>
                        <th>Joined Date</th>
                        <x-authorized :permission="['users.edit', 'users.delete']">
                            <th class="text-right">Actions</th>
                        </x-authorized>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr class="hover:bg-base-200/40 transition-colors">
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="avatar placeholder">
                                        <div class="bg-primary/10 text-primary font-bold rounded-full w-9 h-9 text-xs">
                                            <span>{{ $user->initials }}</span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-base-content">{{ $user->name }}</div>
                                        @if(Auth::id() === $user->id)
                                            <span class="badge badge-xs badge-info badge-soft font-mono">You</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-base-content/80 text-sm">{{ $user->email }}</td>
                            <td>
                                <div class="flex flex-wrap gap-1">
                                    @forelse($user->roles as $role)
                                        @php
                                            $badgeClass = match($role->name) {
                                                'super-admin' => 'badge-error',
                                                'admin' => 'badge-warning',
                                                default => 'badge-primary'
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }} badge-soft font-medium">
                                            {{ ucfirst($role->name) }}
                                        </span>
                                    @empty
                                        <span class="badge badge-ghost badge-soft text-base-content/50">No Role</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="text-base-content/70 text-sm">{{ $user->created_at->format('M d, Y') }}</td>
                            <x-authorized :permission="['users.edit', 'users.delete']">
                                <td class="text-right">
                                    <div class="inline-flex items-center gap-1">
                                        <x-authorized permission="users.edit">
                                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-ghost btn-square text-base-content/70 hover:text-primary" title="Edit User" aria-label="Edit user {{ $user->name }}">
                                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                                            </a>
                                        </x-authorized>
                                        @if(Auth::id() !== $user->id)
                                            <x-authorized permission="users.delete">
                                                <button type="button" class="btn btn-sm btn-ghost btn-square text-error hover:bg-error/10" title="Delete User"
                                                        aria-label="Delete user {{ $user->name }}"
                                                        onclick="openDeleteUserModal('{{ $user->name }}', '{{ route('admin.users.destroy', $user) }}')">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </x-authorized>
                                        @endif
                                    </div>
                                </td>
                            </x-authorized>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-10 text-base-content/60">
                                <i data-lucide="users" class="mb-2 mx-auto text-base-content/30 w-9 h-9"></i>
                                <span>No users found matching your criteria.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="border-t border-base-200/60">
                {{ $users->links('vendor.pagination.daisyui') }}
            </div>
        @endif
    </div>

    <!-- Delete Confirmation Modal -->
    <x-authorized permission="users.delete">
        <dialog id="deleteUserModal" class="modal">
            <div class="modal-box bg-base-100 max-w-md border border-base-200">
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>
                <h3 class="font-bold text-lg text-error flex items-center gap-2 mb-3">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                    Confirm Deletion
                </h3>
                <p class="text-sm text-base-content mb-2">Are you sure you want to permanently delete user <strong id="deleteUserName" class="text-base-content font-bold"></strong>?</p>
                <p class="text-xs text-base-content/60 mb-6">This action cannot be reversed and will revoke all permissions.</p>
                <div class="modal-action">
                    <button type="button" class="btn btn-ghost" onclick="document.getElementById('deleteUserModal').close()">Cancel</button>
                    <form id="deleteUserForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-error">Delete User</button>
                    </form>
                </div>
            </div>
            <form method="dialog" class="modal-backdrop">
                <button>close</button>
            </form>
        </dialog>

        <script>
            function openDeleteUserModal(name, actionUrl) {
                document.getElementById('deleteUserName').textContent = name;
                document.getElementById('deleteUserForm').setAttribute('action', actionUrl);
                document.getElementById('deleteUserModal').showModal();
            }
        </script>
    </x-authorized>
</x-admin-layout>
