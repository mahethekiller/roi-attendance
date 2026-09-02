<x-admin-layout>
    <!-- Page Header & Action Buttons -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold text-body-emphasis mb-1">Employee Directory</h2>
            <p class="text-body-secondary mb-0">Manage employee records, RFID card mappings, and bulk import data.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.employees.sample-csv') }}" class="btn btn-outline-secondary shadow-sm d-flex align-items-center gap-2">
                <i data-lucide="file-spreadsheet" style="width: 18px; height: 18px;"></i>
                <span>Sample CSV</span>
            </a>
            <button type="button" class="btn btn-outline-primary shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#importCsvModal">
                <i data-lucide="upload" style="width: 18px; height: 18px;"></i>
                <span>Import CSV</span>
            </button>
            <a href="{{ route('admin.employees.create') }}" class="btn btn-primary px-3 shadow-sm d-flex align-items-center gap-2">
                <i data-lucide="user-plus" style="width: 18px; height: 18px;"></i>
                <span>Add Employee</span>
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

    <!-- Metrics Row -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm bg-body text-body">
                <div class="card-body p-3">
                    <span class="text-body-secondary small fw-medium">Total Employees</span>
                    <h3 class="mb-0 fw-bold text-body-emphasis mt-1">{{ $totalEmployees }}</h3>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm bg-body text-body">
                <div class="card-body p-3">
                    <span class="text-body-secondary small fw-medium">Assigned Smart Cards</span>
                    <h3 class="mb-0 fw-bold text-body-emphasis mt-1">{{ $assignedCards }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Card -->
    <div class="card border-0 shadow-sm bg-body text-body mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.employees.index') }}" class="row g-2 align-items-center">
                <div class="col-12 col-md-9">
                    <div class="input-group">
                        <span class="input-group-text bg-body-tertiary border-end-0 text-body-secondary">
                            <i data-lucide="search" style="width: 18px; height: 18px;"></i>
                        </span>
                        <input type="search" name="search" class="form-control bg-body-tertiary border-start-0 text-body" placeholder="Search by Employee ID, Card No, Name, or Email..." value="{{ $search }}">
                    </div>
                </div>
                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-1">
                        <i data-lucide="filter" style="width: 16px; height: 16px;"></i> Search
                    </button>
                    @if($search)
                        <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center">
                            <i data-lucide="rotate-ccw" style="width: 16px; height: 16px;"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Employees Data Table -->
    <div class="card border-0 shadow-sm bg-body text-body">
        <div class="card-header bg-body border-bottom p-3 d-flex align-items-center justify-content-between">
            <h6 class="fw-bold mb-0 text-body-emphasis">Employees List ({{ $employees->total() }})</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-body-secondary">
                    <tr>
                        <th>Employee</th>
                        <th>Employee ID</th>
                        <th>Card No</th>
                        <th>Email</th>
                        <th>Company</th>
                        <th>Created At</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; font-size: 0.85rem;">
                                        {{ $employee->initials }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-body-emphasis">{{ $employee->full_name }}</div>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.7rem;">Active</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-body-tertiary text-body border fw-mono px-2 py-1">
                                    {{ $employee->employee_id }}
                                </span>
                            </td>
                            <td>
                                @if($employee->card_no)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        <i data-lucide="credit-card" class="me-1" style="width: 13px; height: 13px;"></i>
                                        {{ $employee->card_no }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Unassigned</span>
                                @endif
                            </td>
                            <td class="text-body-secondary">{{ $employee->email }}</td>
                            <td>
                                @if($employee->company)
                                    <span class="badge bg-info-subtle text-info border border-info-subtle">
                                        <i data-lucide="building-2" class="me-1" style="width: 12px; height: 12px;"></i>
                                        {{ $employee->company }}
                                    </span>
                                @else
                                    <span class="text-body-secondary small">-</span>
                                @endif
                            </td>
                            <td class="text-body-secondary">{{ $employee->created_at->format('M d, Y') }}</td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('admin.employees.edit', $employee) }}" class="btn btn-sm btn-outline-secondary" title="Edit Employee">
                                        <i data-lucide="edit-3" style="width: 15px; height: 15px;"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Delete Employee"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteEmployeeModal"
                                            data-emp-name="{{ $employee->full_name }}"
                                            data-emp-action="{{ route('admin.employees.destroy', $employee) }}">
                                        <i data-lucide="trash-2" style="width: 15px; height: 15px;"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-body-secondary">
                                <i data-lucide="users" class="mb-2 d-block mx-auto text-body-tertiary" style="width: 36px; height: 36px;"></i>
                                <span>No employees found in the directory.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($employees->hasPages())
            <div class="card-footer bg-body border-top p-3">
                {{ $employees->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

    <!-- Import CSV Modal -->
    <div class="modal fade" id="importCsvModal" tabindex="-1" aria-labelledby="importCsvModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-body border-0 shadow">
                <form method="POST" action="{{ route('admin.employees.import') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-bold text-body-emphasis" id="importCsvModalLabel">
                            <i data-lucide="upload" class="text-primary me-2" style="width: 20px; height: 20px;"></i>
                            Bulk Import Employees
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-body">
                        <p class="text-body-secondary small mb-3">
                            Upload a standard CSV file with columns: <code>employee_id, card_no, first_name, last_name, email</code>.
                        </p>
                        <div class="mb-3">
                            <label for="csv_file" class="form-label fw-semibold text-body-emphasis">Select CSV File <span class="text-danger">*</span></label>
                            <input type="file" name="csv_file" id="csv_file" class="form-control bg-body-tertiary text-body" accept=".csv,text/csv" required>
                        </div>
                        <div class="p-3 bg-body-tertiary rounded-3 border small text-body-secondary">
                            <i data-lucide="info" class="me-1 text-primary" style="width: 14px; height: 14px;"></i>
                            Need a template? <a href="{{ route('admin.employees.sample-csv') }}" class="text-primary fw-medium text-decoration-none">Download Dummy Sample CSV</a>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                            <i data-lucide="upload-cloud" style="width: 16px; height: 16px;"></i>
                            <span>Start Import</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteEmployeeModal" tabindex="-1" aria-labelledby="deleteEmployeeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-body border-0 shadow">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold text-body-emphasis" id="deleteEmployeeModalLabel">
                        <i data-lucide="alert-triangle" class="text-danger me-2" style="width: 22px; height: 22px;"></i>
                        Delete Employee
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-body">
                    <p class="mb-2">Are you sure you want to delete employee <strong id="deleteEmpName" class="text-body-emphasis"></strong>?</p>
                    <p class="text-body-secondary small mb-0">This will remove the employee record and their associated user account.</p>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteEmployeeForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete Employee</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const deleteModal = document.getElementById('deleteEmployeeModal');
            if (deleteModal) {
                deleteModal.addEventListener('show.bs.modal', (event) => {
                    const button = event.relatedTarget;
                    const empName = button.getAttribute('data-emp-name');
                    const empAction = button.getAttribute('data-emp-action');

                    document.getElementById('deleteEmpName').textContent = empName;
                    document.getElementById('deleteEmployeeForm').setAttribute('action', empAction);
                });
            }
        });
    </script>
</x-admin-layout>
