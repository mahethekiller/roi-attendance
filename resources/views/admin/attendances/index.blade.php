<x-admin-layout>
    <!-- Page Header & Action Buttons -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-base-content tracking-tight">Attendance Logs</h1>
            <p class="text-sm text-base-content/70 mt-0.5">Biometric punch entries, check-in/out timestamps, and employee status tracking.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <x-authorized permission="sync-logs.view">
                <a href="{{ route('admin.sync-logs.index') }}" class="btn btn-outline btn-sm sm:btn-md gap-2 shadow-xs">
                    <i data-lucide="history" class="w-4 h-4"></i>
                    <span>View Sync History</span>
                </a>
            </x-authorized>
            <x-authorized permission="attendances.sync">
                <form method="POST" action="{{ route('admin.attendances.sync') }}" class="inline">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm sm:btn-md gap-2 shadow-xs">
                        <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                        <span>Sync Biometric Data</span>
                    </button>
                </form>
            </x-authorized>
            <x-authorized permission="attendances.export">
                <button type="button" class="btn btn-outline btn-sm sm:btn-md gap-2 shadow-xs" onclick="window.print()">
                    <i data-lucide="printer" class="w-4 h-4"></i>
                    <span>Print Daily Sheet</span>
                </button>
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
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="card bg-base-100 border border-base-200/60 shadow-xs">
            <div class="card-body p-4">
                <span class="text-xs font-medium text-base-content/60 uppercase tracking-wider">Present on {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</span>
                <h3 class="text-2xl font-bold text-success mt-1">{{ $totalPresentToday }}</h3>
            </div>
        </div>
        <div class="card bg-base-100 border border-base-200/60 shadow-xs">
            <div class="card-body p-4">
                <span class="text-xs font-medium text-base-content/60 uppercase tracking-wider">Absent Staff</span>
                <h3 class="text-2xl font-bold text-error mt-1">{{ $totalAbsentToday }}</h3>
            </div>
        </div>
        <div class="card bg-base-100 border border-base-200/60 shadow-xs">
            <div class="card-body p-4">
                <span class="text-xs font-medium text-base-content/60 uppercase tracking-wider">Total Registered Employees</span>
                <h3 class="text-2xl font-bold text-base-content mt-1">{{ $totalEmployees }}</h3>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card bg-base-100 border border-base-200/60 shadow-xs mb-6">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('admin.attendances.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                <!-- Date Picker -->
                <div class="sm:col-span-3">
                    <input type="date" name="date" class="input input-bordered input-sm sm:input-md w-full bg-base-100 text-base-content" aria-label="Select attendance date" value="{{ $date }}">
                </div>

                <!-- Search Input -->
                <div class="sm:col-span-4">
                    <div class="join w-full">
                        <span class="join-item btn btn-sm sm:btn-md btn-disabled bg-base-200 border-base-300 px-3">
                            <i data-lucide="search" class="w-4 h-4 text-base-content/60"></i>
                        </span>
                        <input type="search" name="search" class="input input-bordered input-sm sm:input-md join-item w-full bg-base-100 text-base-content" placeholder="Search Card, Badge No, or Name..." aria-label="Search by Card, Badge No, or Name" value="{{ $search }}">
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="sm:col-span-3">
                    <select name="status" class="select select-bordered select-sm sm:select-md w-full bg-base-100 text-base-content" aria-label="Filter by attendance status">
                        <option value="">All Statuses</option>
                        <option value="present" {{ $status === 'present' ? 'selected' : '' }}>Present</option>
                        <option value="absent" {{ $status === 'absent' ? 'selected' : '' }}>Absent</option>
                    </select>
                </div>

                <!-- Submit Button -->
                <div class="sm:col-span-2 flex items-center gap-2">
                    <button type="submit" class="btn btn-primary btn-sm sm:btn-md gap-2 w-full">
                        <i data-lucide="filter" class="w-4 h-4"></i> Filter
                    </button>
                    @if($search || $status || $date !== date('Y-m-d'))
                        <a href="{{ route('admin.attendances.index') }}" class="btn btn-outline btn-sm sm:btn-md btn-square" title="Reset Filters" aria-label="Reset attendance filters">
                            <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Attendance Table Card -->
    <div class="card bg-base-100 border border-base-200/60 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-base-200/60 flex items-center justify-between">
            <h2 class="font-semibold text-base-content">
                Daily Records for {{ \Carbon\Carbon::parse($date)->format('M d, Y') }} ({{ $attendances->total() }})
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
                <thead>
                    <tr class="bg-base-200/50 text-base-content/70">
                        <th>Employee</th>
                        <th>Card No</th>
                        <th>Badge No</th>
                        <th>Punch Date</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Total Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $attendance)
                        <tr class="hover:bg-base-200/40 transition-colors">
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="avatar placeholder">
                                        <div class="bg-primary/10 text-primary font-bold rounded-full w-8 h-8 text-xs">
                                            <span>{{ $attendance->employee->initials ?? 'NA' }}</span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-base-content">
                                            {{ $attendance->employee->full_name ?? 'Unregistered Card' }}
                                        </div>
                                        <div class="text-xs text-base-content/60 font-mono">
                                            {{ $attendance->employee->employee_id ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-neutral badge-soft font-mono px-2 py-1">
                                    {{ $attendance->card_no ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="text-sm text-base-content/70 font-mono">{{ $attendance->badgenumber ?? '-' }}</td>
                            <td class="text-sm text-base-content/70">{{ $attendance->punch_date ? $attendance->punch_date->format('M d, Y') : '-' }}</td>
                            <td>
                                @if($attendance->check_in_time)
                                    <span class="text-base-content font-medium text-sm font-mono">{{ $attendance->check_in_time }}</span>
                                @elseif($attendance->check_in_datetime)
                                    <span class="text-base-content font-medium text-sm font-mono">{{ $attendance->check_in_datetime->format('h:i A') }}</span>
                                @else
                                    <span class="text-base-content/40 text-sm">--:--</span>
                                @endif
                            </td>
                            <td>
                                @if($attendance->check_out_time)
                                    <span class="text-base-content font-medium text-sm font-mono">{{ $attendance->check_out_time }}</span>
                                @elseif($attendance->check_out_datetime)
                                    <span class="text-base-content font-medium text-sm font-mono">{{ $attendance->check_out_datetime->format('h:i A') }}</span>
                                @else
                                    <span class="text-base-content/40 text-sm">--:--</span>
                                @endif
                            </td>
                            <td>
                                @if($attendance->total_time)
                                    <span class="badge badge-primary badge-soft font-mono font-semibold text-xs px-2.5 py-1 inline-flex items-center gap-1">
                                        <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                        <span>{{ $attendance->total_time }}</span>
                                    </span>
                                @else
                                    <span class="text-base-content/40 text-xs font-mono">--:--</span>
                                @endif
                            </td>
                            <td>
                                @if(strtolower((string) $attendance->show_status) === 'absent')
                                    <span class="badge badge-error badge-soft font-medium">
                                        Absent
                                    </span>
                                @else
                                    <span class="badge badge-success badge-soft font-medium">
                                        Present
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-10 text-base-content/60">
                                <i data-lucide="calendar-x" class="mb-2 mx-auto text-base-content/30 w-9 h-9"></i>
                                <span>No attendance records logged for {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attendances->hasPages())
            <div class="border-t border-base-200/60">
                {{ $attendances->links('vendor.pagination.daisyui') }}
            </div>
        @endif
    </div>
</x-admin-layout>
