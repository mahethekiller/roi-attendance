<x-admin-layout>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-body-emphasis mb-1">Edit Employee: {{ $employee->full_name }}</h2>
            <p class="text-body-secondary mb-0">Update employee profile, smart card, and contact information.</p>
        </div>
        <div>
            <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-1 shadow-sm">
                <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
                <span>Back to Employees</span>
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm bg-body text-body">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.employees.update', $employee) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3 mb-3">
                            <!-- First Name -->
                            <div class="col-md-6">
                                <label for="first_name" class="form-label fw-semibold text-body-emphasis">First Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-body-tertiary text-body-secondary border-end-0">
                                        <i data-lucide="user" style="width: 18px; height: 18px;"></i>
                                    </span>
                                    <input type="text" name="first_name" id="first_name" class="form-control bg-body-tertiary border-start-0 text-body @error('first_name') is-invalid @enderror" value="{{ old('first_name', $employee->first_name) }}" required>
                                </div>
                                @error('first_name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Last Name -->
                            <div class="col-md-6">
                                <label for="last_name" class="form-label fw-semibold text-body-emphasis">Last Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-body-tertiary text-body-secondary border-end-0">
                                        <i data-lucide="user" style="width: 18px; height: 18px;"></i>
                                    </span>
                                    <input type="text" name="last_name" id="last_name" class="form-control bg-body-tertiary border-start-0 text-body @error('last_name') is-invalid @enderror" value="{{ old('last_name', $employee->last_name) }}" required>
                                </div>
                                @error('last_name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <!-- Employee ID -->
                            <div class="col-md-6">
                                <label for="employee_id" class="form-label fw-semibold text-body-emphasis">Employee ID <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-body-tertiary text-body-secondary border-end-0">
                                        <i data-lucide="badge-check" style="width: 18px; height: 18px;"></i>
                                    </span>
                                    <input type="text" name="employee_id" id="employee_id" class="form-control bg-body-tertiary border-start-0 text-body @error('employee_id') is-invalid @enderror" value="{{ old('employee_id', $employee->employee_id) }}" required>
                                </div>
                                @error('employee_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Card No -->
                            <div class="col-md-6">
                                <label for="card_no" class="form-label fw-semibold text-body-emphasis">Card No (RFID / Smart Card)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-body-tertiary text-body-secondary border-end-0">
                                        <i data-lucide="credit-card" style="width: 18px; height: 18px;"></i>
                                    </span>
                                    <input type="text" name="card_no" id="card_no" class="form-control bg-body-tertiary border-start-0 text-body @error('card_no') is-invalid @enderror" value="{{ old('card_no', $employee->card_no) }}">
                                </div>
                                @error('card_no')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <!-- Email Address -->
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold text-body-emphasis">Work Email Address <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-body-tertiary text-body-secondary border-end-0">
                                        <i data-lucide="mail" style="width: 18px; height: 18px;"></i>
                                    </span>
                                    <input type="email" name="email" id="email" class="form-control bg-body-tertiary border-start-0 text-body @error('email') is-invalid @enderror" value="{{ old('email', $employee->email) }}" required>
                                </div>
                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Company -->
                            <div class="col-md-6">
                                <label for="company" class="form-label fw-semibold text-body-emphasis">Company Name</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-body-tertiary text-body-secondary border-end-0">
                                        <i data-lucide="building-2" style="width: 18px; height: 18px;"></i>
                                    </span>
                                    <input type="text" name="company" id="company" class="form-control bg-body-tertiary border-start-0 text-body @error('company') is-invalid @enderror" value="{{ old('company', $employee->company) }}" placeholder="e.g. ROI Technologies">
                                </div>
                                @error('company')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Form Action Buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4 d-flex align-items-center gap-2">
                                <i data-lucide="check-circle" style="width: 18px; height: 18px;"></i>
                                <span>Update Employee</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
