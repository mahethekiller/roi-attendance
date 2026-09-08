<x-admin-layout>
    <div class="max-w-4xl mx-auto">
        <!-- Header & Breadcrumb -->
        <div class="mb-6">
            <div class="flex items-center gap-2 text-xs text-base-content/60 mb-2 font-medium">
                <a href="{{ route('admin.attendance-overrides.index') }}" class="hover:text-primary transition-colors flex items-center gap-1">
                    <i data-lucide="arrow-left" style="width: 14px; height: 14px;"></i>
                    <span>Back to Overrides</span>
                </a>
                <span>/</span>
                <span class="text-base-content">Add Override Rule</span>
            </div>
            <h2 class="text-2xl sm:text-3xl font-bold text-base-content tracking-tight">Create Attendance Override Rule</h2>
            <p class="text-sm text-base-content/70 mt-1">
                Configure automated punch-in adjustments and minimum shift duration for a specific employee or RFID badge.
            </p>
        </div>

        @if($errors->any())
            <div class="alert alert-error shadow-xs mb-6 border border-error/20" role="alert">
                <i data-lucide="alert-triangle" class="text-error shrink-0" style="width: 18px; height: 18px;"></i>
                <div class="flex-1 text-sm font-medium">
                    <div class="font-bold mb-1">Please correct the following errors:</div>
                    <ul class="list-disc list-inside text-xs">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="card bg-base-100 border border-base-200 shadow-xs">
            <div class="card-body p-5 sm:p-6">
                <form action="{{ route('admin.attendance-overrides.store') }}" method="POST" id="overrideForm">
                    @csrf

                    <!-- Section 1: Target Employee / Card Selection -->
                    <div class="mb-6 pb-6 border-b border-base-200">
                        <h3 class="font-bold text-base text-base-content mb-1">1. Target Identification</h3>
                        <p class="text-xs text-base-content/60 mb-4">Select an existing registered employee or manually specify the employee ID or RFID card number.</p>

                        <!-- Quick Employee Picker -->
                        <div class="form-control mb-4">
                            <label class="label pb-1.5" for="employeePicker">
                                <span class="label-text font-semibold text-xs text-base-content/80">Choose Registered Employee (Optional Quick Fill)</span>
                            </label>
                            <select id="employeePicker" class="select select-bordered select-sm w-full">
                                <option value="">-- Choose employee to auto-fill --</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" 
                                            data-empid="{{ $emp->employee_id }}" 
                                            data-cardno="{{ $emp->card_no }}" 
                                            data-name="{{ $emp->full_name }}">
                                        {{ $emp->full_name }} (ID: {{ $emp->employee_id ?: '—' }}, Card: {{ $emp->card_no ?: '—' }}) {{ $emp->company ? "• {$emp->company}" : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="form-control">
                                <label class="label pb-1.5" for="employee_id">
                                    <span class="label-text font-semibold text-xs text-base-content/80">Employee ID</span>
                                    <span class="label-text-alt text-base-content/50 text-[11px]">e.g. I2K2-0340</span>
                                </label>
                                <input type="text" name="employee_id" id="employee_id" value="{{ old('employee_id') }}" class="input input-sm input-bordered font-mono w-full" placeholder="I2K2-0340" />
                            </div>

                            <div class="form-control">
                                <label class="label pb-1.5" for="card_no">
                                    <span class="label-text font-semibold text-xs text-base-content/80">RFID Card Number</span>
                                    <span class="label-text-alt text-base-content/50 text-[11px]">e.g. 1234</span>
                                </label>
                                <input type="text" name="card_no" id="card_no" value="{{ old('card_no') }}" class="input input-sm input-bordered font-mono w-full" placeholder="1234" />
                            </div>

                            <div class="form-control">
                                <label class="label pb-1.5" for="employee_name">
                                    <span class="label-text font-semibold text-xs text-base-content/80">Reference / Display Name</span>
                                    <span class="label-text-alt text-base-content/50 text-[11px]">For Admin logs</span>
                                </label>
                                <input type="text" name="employee_name" id="employee_name" value="{{ old('employee_name') }}" class="input input-sm input-bordered w-full" placeholder="Employee Full Name" />
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Check-in Time Adjustments -->
                    <div class="mb-6 pb-6 border-b border-base-200">
                        <h3 class="font-bold text-base text-base-content mb-1">2. Check-In Adjustment Trigger</h3>
                        <p class="text-xs text-base-content/60 mb-4">When the biometric punch-in falls within this time window, it will automatically be adjusted to a randomized 09:XX time.</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div class="form-control">
                                <label class="label pb-1.5" for="check_in_window_start">
                                    <span class="label-text font-semibold text-xs text-base-content/80">Trigger Window Start Time</span>
                                </label>
                                <input type="time" step="1" name="check_in_window_start" id="check_in_window_start" value="{{ old('check_in_window_start', '10:00:00') }}" required class="input input-sm input-bordered font-mono w-full" />
                                <span class="text-[11px] text-base-content/50 mt-1">Punches after this time qualify for adjustment (Default: 10:00:00 AM)</span>
                            </div>

                            <div class="form-control">
                                <label class="label pb-1.5" for="check_in_window_end">
                                    <span class="label-text font-semibold text-xs text-base-content/80">Trigger Window End Time</span>
                                </label>
                                <input type="time" step="1" name="check_in_window_end" id="check_in_window_end" value="{{ old('check_in_window_end', '10:20:00') }}" required class="input input-sm input-bordered font-mono w-full" />
                                <span class="text-[11px] text-base-content/50 mt-1">Punches before this time qualify for adjustment (Default: 10:20:00 AM)</span>
                            </div>
                        </div>

                        <div class="p-3.5 rounded-xl bg-base-200/40 border border-base-200">
                            <div class="font-semibold text-xs text-base-content mb-2 flex items-center gap-1.5">
                                <i data-lucide="clock" style="width: 14px; height: 14px;" class="text-primary"></i>
                                <span>Adjusted Check-In Minute Range (09:XX AM)</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="form-control">
                                    <label class="label pb-1" for="adjusted_in_min_minute">
                                        <span class="label-text text-xs text-base-content/70">Random Minute Minimum</span>
                                    </label>
                                    <input type="number" min="0" max="59" name="adjusted_in_min_minute" id="adjusted_in_min_minute" value="{{ old('adjusted_in_min_minute', 20) }}" required class="input input-sm input-bordered font-mono w-full" />
                                    <span class="text-[11px] text-base-content/50 mt-0.5">e.g. 20 for 09:20 AM</span>
                                </div>
                                <div class="form-control">
                                    <label class="label pb-1" for="adjusted_in_max_minute">
                                        <span class="label-text text-xs text-base-content/70">Random Minute Maximum</span>
                                    </label>
                                    <input type="number" min="0" max="59" name="adjusted_in_max_minute" id="adjusted_in_max_minute" value="{{ old('adjusted_in_max_minute', 35) }}" required class="input input-sm input-bordered font-mono w-full" />
                                    <span class="text-[11px] text-base-content/50 mt-0.5">e.g. 35 for 09:35 AM</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Shift Duration & Check-out -->
                    <div class="mb-6 pb-6 border-b border-base-200">
                        <h3 class="font-bold text-base text-base-content mb-1">3. Check-Out Duration Guarantee</h3>
                        <p class="text-xs text-base-content/60 mb-4">Ensure the employee's total logged shift meets the required hours minimum.</p>

                        <div class="form-control max-w-sm">
                            <label class="label pb-1.5" for="min_duration_hours">
                                <span class="label-text font-semibold text-xs text-base-content/80">Minimum Shift Duration (Hours)</span>
                            </label>
                            <div class="join w-full">
                                <input type="number" step="0.25" min="1" max="24" name="min_duration_hours" id="min_duration_hours" value="{{ old('min_duration_hours', 9.00) }}" required class="input input-sm input-bordered join-item font-mono w-full" />
                                <span class="btn btn-sm btn-neutral join-item pointer-events-none">Hours</span>
                            </div>
                            <span class="text-[11px] text-base-content/50 mt-1">If total work time is under this amount, check-out is set to Check-In + this duration (Default: 9.00 hours).</span>
                        </div>
                    </div>

                    <!-- Section 4: Status & Notes -->
                    <div class="mb-6">
                        <h3 class="font-bold text-base text-base-content mb-1">4. Activation & Internal Notes</h3>
                        
                        <div class="form-control mb-4">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="checkbox checkbox-primary checkbox-sm" />
                                <span class="label-text font-semibold text-sm text-base-content">Enable this override rule immediately</span>
                            </label>
                        </div>

                        <div class="form-control">
                            <label class="label pb-1.5" for="notes">
                                <span class="label-text font-semibold text-xs text-base-content/80">Internal Administration Notes</span>
                            </label>
                            <textarea name="notes" id="notes" rows="2" class="textarea textarea-bordered text-sm w-full" placeholder="Reason or memo for this override rule...">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-base-200">
                        <a href="{{ route('admin.attendance-overrides.index') }}" class="btn btn-sm btn-ghost">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-sm btn-primary flex items-center gap-2 shadow-xs">
                            <i data-lucide="check" style="width: 15px; height: 15px;"></i>
                            <span>Save Override Rule</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const picker = document.getElementById('employeePicker');
            const empIdInput = document.getElementById('employee_id');
            const cardNoInput = document.getElementById('card_no');
            const empNameInput = document.getElementById('employee_name');

            if (picker) {
                picker.addEventListener('change', function () {
                    const selected = picker.options[picker.selectedIndex];
                    if (selected && selected.value) {
                        if (empIdInput) empIdInput.value = selected.getAttribute('data-empid') || '';
                        if (cardNoInput) cardNoInput.value = selected.getAttribute('data-cardno') || '';
                        if (empNameInput) empNameInput.value = selected.getAttribute('data-name') || '';
                    }
                });
            }
        });
    </script>
    @endpush
</x-admin-layout>
