<x-admin-layout>
    <!-- Page Header & Action Buttons -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-base-content tracking-tight">Employee Directory</h1>
            <p class="text-sm text-base-content/70 mt-0.5">Manage employee records, RFID card mappings, and bulk import data.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <x-authorized permission="employees.import">
                <a href="{{ route('admin.employees.sample-csv') }}" class="btn btn-outline btn-sm sm:btn-md gap-2 shadow-xs">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                    <span>Sample CSV</span>
                </a>
                <button type="button" class="btn btn-outline btn-primary btn-sm sm:btn-md gap-2 shadow-xs" onclick="document.getElementById('importCsvModal').showModal()">
                    <i data-lucide="upload" class="w-4 h-4"></i>
                    <span>Import CSV</span>
                </button>
            </x-authorized>
            <x-authorized permission="employees.create">
                <a href="{{ route('admin.employees.create') }}" class="btn btn-primary btn-sm sm:btn-md gap-2 shadow-xs">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                    <span>Add Employee</span>
                </a>
            </x-authorized>
        </div>
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

    <!-- Metrics Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="card bg-base-100 border border-base-200/60 shadow-xs">
            <div class="card-body p-4">
                <span class="text-xs font-medium text-base-content/60 uppercase tracking-wider">Total Employees</span>
                <h3 class="text-2xl font-bold text-base-content mt-1">{{ $totalEmployees }}</h3>
            </div>
        </div>
        <div class="card bg-base-100 border border-base-200/60 shadow-xs">
            <div class="card-body p-4">
                <span class="text-xs font-medium text-base-content/60 uppercase tracking-wider">Assigned Smart Cards</span>
                <h3 class="text-2xl font-bold text-base-content mt-1">{{ $assignedCards }}</h3>
            </div>
        </div>
    </div>

    <!-- Search Card -->
    <div class="card bg-base-100 border border-base-200/60 shadow-xs mb-6">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('admin.employees.index') }}" class="flex flex-col sm:flex-row gap-3 items-center">
                <div class="join w-full flex-1">
                    <span class="join-item btn btn-sm sm:btn-md btn-disabled bg-base-200 border-base-300 px-3">
                        <i data-lucide="search" class="w-4 h-4 text-base-content/60"></i>
                    </span>
                    <input type="search" name="search" class="input input-bordered input-sm sm:input-md join-item w-full bg-base-100 text-base-content" placeholder="Search by Employee ID, Card No, Name, or Email..." aria-label="Search employees by ID, Card No, Name, or Email" value="{{ $search }}">
                </div>
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button type="submit" class="btn btn-primary btn-sm sm:btn-md gap-2 w-full sm:w-auto">
                        <i data-lucide="filter" class="w-4 h-4"></i> Search
                    </button>
                    @if($search)
                        <a href="{{ route('admin.employees.index') }}" class="btn btn-outline btn-sm sm:btn-md btn-square" title="Reset Search" aria-label="Reset search filter">
                            <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Employees Data Table -->
    <div class="card bg-base-100 border border-base-200/60 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-base-200/60 flex items-center justify-between">
            <h2 class="font-semibold text-base-content">Employees List ({{ $employees->total() }})</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
                <thead>
                    <tr class="bg-base-200/50 text-base-content/70">
                        <th>Employee</th>
                        <th>Employee ID</th>
                        <th>Card No</th>
                        <th>Email</th>
                        <th>Company</th>
                        <th>Created At</th>
                        <x-authorized :permission="['employees.edit', 'employees.delete']">
                            <th class="text-right">Actions</th>
                        </x-authorized>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                        <tr class="hover:bg-base-200/40 transition-colors">
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="avatar placeholder">
                                        <div class="bg-primary/10 text-primary font-bold rounded-full w-9 h-9 text-xs">
                                            <span>{{ $employee->initials }}</span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-base-content">{{ $employee->full_name }}</div>
                                        <span class="badge badge-xs badge-success badge-soft font-mono">Active</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-neutral badge-soft font-mono px-2 py-1">
                                    {{ $employee->employee_id }}
                                </span>
                            </td>
                            <td>
                                @if($employee->card_no)
                                    <span class="badge badge-success badge-soft gap-1">
                                        <i data-lucide="credit-card" class="w-3 h-3"></i>
                                        {{ $employee->card_no }}
                                    </span>
                                @else
                                    <span class="badge badge-ghost badge-soft text-base-content/50">Unassigned</span>
                                @endif
                            </td>
                            <td class="text-base-content/80 text-sm">{{ $employee->email }}</td>
                            <td>
                                @if($employee->company)
                                    <span class="badge badge-info badge-soft gap-1">
                                        <i data-lucide="building-2" class="w-3 h-3"></i>
                                        {{ $employee->company }}
                                    </span>
                                @else
                                    <span class="text-base-content/40 text-sm">-</span>
                                @endif
                            </td>
                            <td class="text-base-content/70 text-sm">{{ $employee->created_at->format('M d, Y') }}</td>
                            <x-authorized :permission="['employees.edit', 'employees.delete']">
                                <td class="text-right">
                                    <div class="inline-flex items-center gap-1">
                                        <x-authorized permission="employees.edit">
                                            <a href="{{ route('admin.employees.edit', $employee) }}" class="btn btn-sm btn-ghost btn-square text-base-content/70 hover:text-primary" title="Edit Employee" aria-label="Edit employee {{ $employee->full_name }}">
                                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                                            </a>
                                        </x-authorized>
                                        <x-authorized permission="employees.delete">
                                            <button type="button" class="btn btn-sm btn-ghost btn-square text-error hover:bg-error/10" title="Delete Employee"
                                                    aria-label="Delete employee {{ $employee->full_name }}"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteEmployeeModal"
                                                    onclick="openDeleteModal('{{ $employee->full_name }}', '{{ route('admin.employees.destroy', $employee) }}')">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </x-authorized>
                                    </div>
                                </td>
                            </x-authorized>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-10 text-base-content/60">
                                <i data-lucide="users" class="mb-2 mx-auto text-base-content/30 w-9 h-9"></i>
                                <span>No employees found in the directory.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($employees->hasPages())
            <div class="border-t border-base-200/60">
                {{ $employees->links('vendor.pagination.daisyui') }}
            </div>
        @endif
    </div>

    {{-- Import CSV Modal --}}
    <x-authorized permission="employees.import">
        <dialog id="importCsvModal" class="modal">
            <div class="modal-box bg-base-100 max-w-md border border-base-200">
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>
                <form method="POST" action="{{ route('admin.employees.import') }}" enctype="multipart/form-data">
                    @csrf
                    <h3 class="font-bold text-lg text-base-content flex items-center gap-2 mb-3">
                        <i data-lucide="upload" class="w-5 h-5 text-primary"></i>
                        Bulk Import Employees
                    </h3>
                    <p class="text-xs text-base-content/70 mb-4">
                        Upload a standard CSV file with columns: <code class="badge badge-neutral badge-xs">employee_id, card_no, first_name, last_name, email</code>.
                    </p>
                    <div class="form-control mb-4">
                        <label for="csv_file" class="label text-sm font-semibold text-base-content">Select CSV File <span class="text-error">*</span></label>
                        <input type="file" name="csv_file" id="csv_file" class="file-input file-input-bordered file-input-primary w-full text-sm" accept=".csv,text/csv" required>
                    </div>
                    <div class="p-3 bg-base-200/60 rounded-lg text-xs text-base-content/70 mb-6 flex items-start gap-2">
                        <i data-lucide="info" class="w-4 h-4 text-primary shrink-0 mt-0.5"></i>
                        <span>Need a template? <a href="{{ route('admin.employees.sample-csv') }}" class="link link-primary font-medium">Download Dummy Sample CSV</a></span>
                    </div>
                    <div class="modal-action">
                        <button type="button" class="btn btn-ghost" onclick="document.getElementById('importCsvModal').close()">Cancel</button>
                        <button type="submit" class="btn btn-primary gap-2">
                            <i data-lucide="upload-cloud" class="w-4 h-4"></i>
                            <span>Start Import</span>
                        </button>
                    </div>
                </form>
            </div>
            <form method="dialog" class="modal-backdrop">
                <button>close</button>
            </form>
        </dialog>
    </x-authorized>

    {{-- Delete Confirmation Modal --}}
    <x-authorized permission="employees.delete">
        <dialog id="deleteEmployeeModal" class="modal">
            <div class="modal-box bg-base-100 max-w-md border border-base-200">
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>
                <h3 class="font-bold text-lg text-error flex items-center gap-2 mb-3">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                    Delete Employee
                </h3>
                <p class="text-sm text-base-content mb-2">Are you sure you want to delete employee <strong id="deleteEmpName" class="text-base-content font-bold"></strong>?</p>
                <p class="text-xs text-base-content/60 mb-6">This will remove the employee record and their associated user account.</p>
                <div class="modal-action">
                    <button type="button" class="btn btn-ghost" onclick="document.getElementById('deleteEmployeeModal').close()">Cancel</button>
                    <form id="deleteEmployeeForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-error">Delete Employee</button>
                    </form>
                </div>
            </div>
            <form method="dialog" class="modal-backdrop">
                <button>close</button>
            </form>
        </dialog>

        <script>
            function openDeleteModal(name, actionUrl) {
                document.getElementById('deleteEmpName').textContent = name;
                document.getElementById('deleteEmployeeForm').setAttribute('action', actionUrl);
                document.getElementById('deleteEmployeeModal').showModal();
            }
        </script>
    </x-authorized>
</x-admin-layout>
