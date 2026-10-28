<x-admin-layout>
    <!-- Page Header & Actions -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-base-content tracking-tight">Company Management</h1>
            <p class="text-sm text-base-content/70 mt-0.5">Configure organization entities and assign employees for biometric synchronization.</p>
        </div>
        <x-authorized permission="companies.create">
            <div class="flex gap-2">
                <a href="{{ route('admin.companies.create') }}" class="btn btn-primary btn-sm sm:btn-md gap-2 shadow-xs">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span>Add New Company</span>
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
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="card bg-base-100 border border-base-200/60 shadow-xs">
            <div class="card-body p-4 flex flex-row items-center justify-between">
                <div>
                    <span class="text-xs font-medium text-base-content/60 uppercase tracking-wider">Total Companies</span>
                    <h3 class="text-2xl font-bold text-base-content mt-1">{{ $totalCompanies }}</h3>
                </div>
                <div class="p-3 bg-primary/10 text-primary rounded-xl">
                    <i data-lucide="building" class="w-6 h-6"></i>
                </div>
            </div>
        </div>
        <div class="card bg-base-100 border border-base-200/60 shadow-xs">
            <div class="card-body p-4 flex flex-row items-center justify-between">
                <div>
                    <span class="text-xs font-medium text-base-content/60 uppercase tracking-wider">Active Entities</span>
                    <h3 class="text-2xl font-bold text-success mt-1">{{ $activeCompanies }}</h3>
                </div>
                <div class="p-3 bg-success/10 text-success rounded-xl">
                    <i data-lucide="check-circle-2" class="w-6 h-6"></i>
                </div>
            </div>
        </div>
        <div class="card bg-base-100 border border-base-200/60 shadow-xs">
            <div class="card-body p-4 flex flex-row items-center justify-between">
                <div>
                    <span class="text-xs font-medium text-base-content/60 uppercase tracking-wider">Assigned Employees</span>
                    <h3 class="text-2xl font-bold text-info mt-1">{{ $totalEmployeesAssigned }}</h3>
                </div>
                <div class="p-3 bg-info/10 text-info rounded-xl">
                    <i data-lucide="users" class="w-6 h-6"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="card bg-base-100 border border-base-200/60 shadow-xs mb-6">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('admin.companies.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                <div class="sm:col-span-6">
                    <div class="join w-full">
                        <span class="join-item btn btn-sm sm:btn-md btn-disabled bg-base-200 border-base-300 px-3">
                            <i data-lucide="search" class="w-4 h-4 text-base-content/60"></i>
                        </span>
                        <input type="search" name="search" class="input input-bordered input-sm sm:input-md join-item w-full bg-base-100 text-base-content" placeholder="Search by name, code or description..." aria-label="Search companies" value="{{ $search }}">
                    </div>
                </div>
                <div class="sm:col-span-4">
                    <select name="status" class="select select-bordered select-sm sm:select-md w-full bg-base-100 text-base-content" aria-label="Filter by status">
                        <option value="">All Statuses</option>
                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Status: Active</option>
                        <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Status: Inactive</option>
                    </select>
                </div>
                <div class="sm:col-span-2 flex items-center gap-2">
                    <button type="submit" class="btn btn-primary btn-sm sm:btn-md gap-2 w-full">
                        <i data-lucide="filter" class="w-4 h-4"></i> Filter
                    </button>
                    @if($search || $status)
                        <a href="{{ route('admin.companies.index') }}" class="btn btn-ghost btn-sm sm:btn-md" title="Reset Filters">
                            <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Companies Data Table -->
    <div class="card bg-base-100 border border-base-200/60 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-base-200/60 flex items-center justify-between">
            <h2 class="font-semibold text-base-content">Companies List ({{ $companies->total() }})</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
                <thead>
                    <tr class="bg-base-200/50 text-base-content/70">
                        <th class="w-16">ID</th>
                        <th>Company Name</th>
                        <th>Code</th>
                        <th>Status</th>
                        <th>Employees</th>
                        <th>Description</th>
                        <x-authorized :permission="['companies.edit', 'companies.delete']">
                            <th class="text-right">Actions</th>
                        </x-authorized>
                    </tr>
                </thead>
                <tbody>
                    @forelse($companies as $company)
                        <tr class="hover:bg-base-200/40 transition-colors">
                            <td class="font-mono text-xs text-base-content/60">#{{ $company->id }}</td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-xs uppercase">
                                        {{ substr($company->name, 0, 2) }}
                                    </div>
                                    <div class="font-semibold text-base-content">{{ $company->name }}</div>
                                </div>
                            </td>
                            <td>
                                @if($company->code)
                                    <span class="badge badge-neutral badge-soft font-mono px-2 py-1">
                                        {{ $company->code }}
                                    </span>
                                @else
                                    <span class="text-base-content/40 text-xs">—</span>
                                @endif
                            </td>
                            <td>
                                @if($company->status === 'active')
                                    <span class="badge badge-success badge-soft gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-success"></span>
                                        Active
                                    </span>
                                @else
                                    <span class="badge badge-neutral badge-soft gap-1 text-base-content/60">
                                        <span class="w-1.5 h-1.5 rounded-full bg-base-content/40"></span>
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.employees.index', ['search' => $company->name]) }}" class="badge badge-info badge-soft hover:badge-outline gap-1 font-mono transition-all" title="View employees of this company">
                                    <i data-lucide="users" class="w-3 h-3"></i>
                                    {{ $company->employees_count }}
                                </a>
                            </td>
                            <td class="text-sm text-base-content/70 max-w-xs truncate" title="{{ $company->description }}">
                                {{ $company->description ?: '—' }}
                            </td>
                            <x-authorized :permission="['companies.edit', 'companies.delete']">
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        @can('companies.edit')
                                            <a href="{{ route('admin.companies.edit', $company) }}" class="btn btn-ghost btn-xs text-primary gap-1" title="Edit Company">
                                                <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                                <span class="hidden sm:inline">Edit</span>
                                            </a>
                                        @endcan

                                        @can('companies.delete')
                                            <button type="button" 
                                                    onclick="openDeleteModal('{{ $company->id }}', '{{ addslashes($company->name) }}', {{ $company->employees_count }})" 
                                                    class="btn btn-ghost btn-xs text-error gap-1" 
                                                    title="Delete Company">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                                <span class="hidden sm:inline">Delete</span>
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </x-authorized>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-base-content/50">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i data-lucide="building" class="w-8 h-8 opacity-40"></i>
                                    <span>No companies found matching your criteria.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($companies->hasPages())
            <div class="p-4 border-t border-base-200/60">
                {{ $companies->links() }}
            </div>
        @endif
    </div>

    <!-- Delete Confirmation Modal -->
    <dialog id="delete_modal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-base-100">
            <h3 class="font-bold text-lg text-base-content flex items-center gap-2">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-warning"></i>
                Confirm Company Deletion
            </h3>
            <p class="py-4 text-sm text-base-content/80">
                Are you sure you want to permanently delete company <strong id="delete_company_name" class="text-base-content"></strong>?
            </p>
            <div id="delete_warning_box" class="alert alert-warning shadow-xs mb-4 hidden">
                <i data-lucide="alert-circle" class="w-5 h-5 shrink-0"></i>
                <span id="delete_warning_text" class="text-xs"></span>
            </div>
            <div class="modal-action">
                <form method="dialog">
                    <button class="btn btn-ghost btn-sm sm:btn-md">Cancel</button>
                </form>
                <form id="delete_form" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" id="confirm_delete_btn" class="btn btn-error btn-sm sm:btn-md gap-2">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                        <span>Delete Company</span>
                    </button>
                </form>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    <script>
        function openDeleteModal(companyId, companyName, employeeCount) {
            const modal = document.getElementById('delete_modal');
            const form = document.getElementById('delete_form');
            const nameEl = document.getElementById('delete_company_name');
            const warningBox = document.getElementById('delete_warning_box');
            const warningText = document.getElementById('delete_warning_text');
            const deleteBtn = document.getElementById('confirm_delete_btn');

            nameEl.textContent = `"${companyName}"`;
            form.action = `/admin/companies/${companyId}`;

            if (employeeCount > 0) {
                warningBox.classList.remove('hidden');
                warningText.textContent = `Warning: ${employeeCount} employee(s) are currently assigned to this company. You must reassign or remove them before deleting.`;
                deleteBtn.disabled = true;
                deleteBtn.classList.add('btn-disabled');
            } else {
                warningBox.classList.add('hidden');
                warningText.textContent = '';
                deleteBtn.disabled = false;
                deleteBtn.classList.remove('btn-disabled');
            }

            modal.showModal();
        }
    </script>
</x-admin-layout>
