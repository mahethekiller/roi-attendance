<x-admin-layout>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-base-content tracking-tight">Edit Employee: {{ $employee->full_name }}</h1>
            <p class="text-sm text-base-content/70 mt-0.5">Update employee profile, smart card, and contact information.</p>
        </div>
        <div>
            <a href="{{ route('admin.employees.index') }}" class="btn btn-outline btn-sm gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Back to Employees</span>
            </a>
        </div>
    </div>

    <div class="flex justify-center">
        <div class="w-full max-w-3xl">
            <div class="card bg-base-100 border border-base-200/60 shadow-xs">
                <div class="card-body p-6 sm:p-8">
                    <form method="POST" action="{{ route('admin.employees.update', $employee) }}" class="space-y-5">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- First Name -->
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend text-xs font-bold uppercase tracking-wider text-base-content/70">First Name <span class="text-error">*</span></legend>
                                <label class="input input-bordered flex items-center gap-2 w-full @error('first_name') input-error @enderror">
                                    <i data-lucide="user" class="w-4 h-4 text-base-content/50 shrink-0"></i>
                                    <input type="text" name="first_name" id="first_name" class="grow bg-transparent" value="{{ old('first_name', $employee->first_name) }}" required>
                                </label>
                                @error('first_name')
                                    <p class="fieldset-label text-error">{{ $message }}</p>
                                @enderror
                            </fieldset>

                            <!-- Last Name -->
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend text-xs font-bold uppercase tracking-wider text-base-content/70">Last Name <span class="text-error">*</span></legend>
                                <label class="input input-bordered flex items-center gap-2 w-full @error('last_name') input-error @enderror">
                                    <i data-lucide="user" class="w-4 h-4 text-base-content/50 shrink-0"></i>
                                    <input type="text" name="last_name" id="last_name" class="grow bg-transparent" value="{{ old('last_name', $employee->last_name) }}" required>
                                </label>
                                @error('last_name')
                                    <p class="fieldset-label text-error">{{ $message }}</p>
                                @enderror
                            </fieldset>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Employee ID -->
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend text-xs font-bold uppercase tracking-wider text-base-content/70">Employee ID <span class="text-error">*</span></legend>
                                <label class="input input-bordered flex items-center gap-2 w-full @error('employee_id') input-error @enderror">
                                    <i data-lucide="badge-check" class="w-4 h-4 text-base-content/50 shrink-0"></i>
                                    <input type="text" name="employee_id" id="employee_id" class="grow bg-transparent font-mono" value="{{ old('employee_id', $employee->employee_id) }}" required>
                                </label>
                                @error('employee_id')
                                    <p class="fieldset-label text-error">{{ $message }}</p>
                                @enderror
                            </fieldset>

                            <!-- Card No -->
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend text-xs font-bold uppercase tracking-wider text-base-content/70">Card No (RFID / Smart Card)</legend>
                                <label class="input input-bordered flex items-center gap-2 w-full @error('card_no') input-error @enderror">
                                    <i data-lucide="credit-card" class="w-4 h-4 text-base-content/50 shrink-0"></i>
                                    <input type="text" name="card_no" id="card_no" class="grow bg-transparent font-mono" value="{{ old('card_no', $employee->card_no) }}">
                                </label>
                                @error('card_no')
                                    <p class="fieldset-label text-error">{{ $message }}</p>
                                @enderror
                            </fieldset>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Email Address -->
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend text-xs font-bold uppercase tracking-wider text-base-content/70">Work Email Address <span class="text-error">*</span></legend>
                                <label class="input input-bordered flex items-center gap-2 w-full @error('email') input-error @enderror">
                                    <i data-lucide="mail" class="w-4 h-4 text-base-content/50 shrink-0"></i>
                                    <input type="email" name="email" id="email" class="grow bg-transparent" value="{{ old('email', $employee->email) }}" required>
                                </label>
                                @error('email')
                                    <p class="fieldset-label text-error">{{ $message }}</p>
                                @enderror
                            </fieldset>

                            <!-- Company -->
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend text-xs font-bold uppercase tracking-wider text-base-content/70">Company Name</legend>
                                <label class="input input-bordered flex items-center gap-2 w-full @error('company') input-error @enderror">
                                    <i data-lucide="building-2" class="w-4 h-4 text-base-content/50 shrink-0"></i>
                                    <input type="text" name="company" id="company" class="grow bg-transparent" value="{{ old('company', $employee->company) }}" placeholder="e.g. ROI Technologies">
                                </label>
                                @error('company')
                                    <p class="fieldset-label text-error">{{ $message }}</p>
                                @enderror
                            </fieldset>
                        </div>

                        <!-- Form Action Buttons -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-base-200/60">
                            <a href="{{ route('admin.employees.index') }}" class="btn btn-ghost">Cancel</a>
                            <button type="submit" class="btn btn-primary px-6 gap-2">
                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                <span>Update Employee</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
