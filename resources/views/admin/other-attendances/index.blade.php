<x-admin-layout>
    <!-- Page Header & Action Buttons -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-base-content tracking-tight">Other Companies Attendance</h1>
            <p class="text-sm text-base-content/70 mt-0.5">Biometric punch entries, shift durations, and workforce telemetry for partner and external entities.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <x-authorized permission="sync-logs.view">
                <a href="{{ route('admin.sync-logs.index') }}" class="btn btn-outline btn-sm sm:btn-md gap-2 shadow-xs">
                    <i data-lucide="history" class="w-4 h-4"></i>
                    <span>View Sync History</span>
                </a>
            </x-authorized>
            <x-authorized permission="other_attendances.sync">
                <form method="POST" action="{{ route('admin.other-attendances.sync') }}" class="inline">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm sm:btn-md gap-2 shadow-xs">
                        <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                        <span>Sync Biometric Data</span>
                    </button>
                </form>
            </x-authorized>
            <button type="button" class="btn btn-outline btn-sm sm:btn-md gap-2 shadow-xs" onclick="window.print()">
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span>Print Daily Sheet</span>
            </button>
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
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-medium text-base-content/60 uppercase tracking-wider">Present on {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</span>
                        <h3 class="text-2xl font-bold text-success mt-1">{{ $totalPunchesToday }}</h3>
                    </div>
                    <div class="rounded-xl bg-success/10 text-success p-2.5">
                        <i data-lucide="user-check" class="w-6 h-6"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="card bg-base-100 border border-base-200/60 shadow-xs">
            <div class="card-body p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-medium text-base-content/60 uppercase tracking-wider">Companies Represented</span>
                        <h3 class="text-2xl font-bold text-primary mt-1">{{ $totalCompaniesToday }}</h3>
                    </div>
                    <div class="rounded-xl bg-primary/10 text-primary p-2.5">
                        <i data-lucide="building-2" class="w-6 h-6"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="card bg-base-100 border border-base-200/60 shadow-xs">
            <div class="card-body p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-medium text-base-content/60 uppercase tracking-wider">Active Staff Members</span>
                        <h3 class="text-2xl font-bold text-base-content mt-1">{{ $totalStaffToday }}</h3>
                    </div>
                    <div class="rounded-xl bg-base-200 text-base-content/70 p-2.5">
                        <i data-lucide="users" class="w-6 h-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card bg-base-100 border border-base-200/60 shadow-xs mb-6">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('admin.other-attendances.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                <!-- Date Picker -->
                <div class="sm:col-span-3">
                    <label class="text-[11px] font-semibold text-base-content/60 uppercase tracking-wider block mb-1">Date</label>
                    <input type="date" name="date" class="input input-bordered input-sm sm:input-md w-full bg-base-100 text-base-content" aria-label="Select attendance date" value="{{ $date }}">
                </div>

                <!-- Company Dropdown Filter -->
                <div class="sm:col-span-3">
                    <label class="text-[11px] font-semibold text-base-content/60 uppercase tracking-wider block mb-1">Company</label>
                    <select name="company" class="select select-bordered select-sm sm:select-md w-full bg-base-100 text-base-content" aria-label="Filter by company">
                        <option value="">All Other Companies</option>
                        @foreach($allCompanies as $comp)
                            <option value="{{ $comp }}" {{ $company === $comp ? 'selected' : '' }}>{{ $comp }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Search Input -->
                <div class="sm:col-span-3">
                    <label class="text-[11px] font-semibold text-base-content/60 uppercase tracking-wider block mb-1">Search</label>
                    <div class="join w-full">
                        <span class="join-item btn btn-sm sm:btn-md btn-disabled bg-base-200 border-base-300 px-3">
                            <i data-lucide="search" class="w-4 h-4 text-base-content/60"></i>
                        </span>
                        <input type="search" name="search" class="input input-bordered input-sm sm:input-md join-item w-full bg-base-100 text-base-content" placeholder="Card, Name, Badge..." aria-label="Search" value="{{ $search }}">
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="sm:col-span-2">
                    <label class="text-[11px] font-semibold text-base-content/60 uppercase tracking-wider block mb-1">Status</label>
                    <select name="status" class="select select-bordered select-sm sm:select-md w-full bg-base-100 text-base-content" aria-label="Filter by status">
                        <option value="">All Statuses</option>
                        <option value="present" {{ $status === 'present' ? 'selected' : '' }}>Present</option>
                        <option value="absent" {{ $status === 'absent' ? 'selected' : '' }}>Absent</option>
                    </select>
                </div>

                <!-- Filter Actions -->
                <div class="sm:col-span-1 flex items-end gap-1.5 pt-4 sm:pt-0">
                    <button type="submit" class="btn btn-primary btn-sm sm:btn-md flex-1" title="Apply Filters">
                        <i data-lucide="filter" class="w-4 h-4"></i>
                    </button>
                    @if($search || $status || $company || $date !== date('Y-m-d'))
                        <a href="{{ route('admin.other-attendances.index') }}" class="btn btn-outline btn-sm sm:btn-md btn-square" title="Reset Filters" aria-label="Reset attendance filters">
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
            <div class="flex items-center gap-2">
                <div class="p-1.5 rounded-lg bg-primary/10 text-primary">
                    <i data-lucide="table-2" class="w-4 h-4"></i>
                </div>
                <h2 class="font-semibold text-base-content">
                    Records for {{ \Carbon\Carbon::parse($date)->format('M d, Y') }} ({{ $attendances->total() }})
                </h2>
            </div>
            @if(!empty($company))
                <span class="badge badge-outline badge-primary font-medium text-xs px-2.5 py-1">
                    Company: {{ $company }}
                </span>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
                <thead>
                    <tr class="bg-base-200/50 text-base-content/70">
                        <th>Employee</th>
                        <th>Company</th>
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
                    @forelse($attendances as $item)
                        <tr class="hover:bg-base-200/40 transition-colors">
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="avatar placeholder">
                                        <div class="bg-neutral/10 text-neutral-content font-bold rounded-full w-8 h-8 text-xs border border-base-300">
                                            @php
                                                $name = $item->employee_name ?: ($item->employee->full_name ?? 'Card ' . $item->card_no);
                                                $words = explode(' ', trim($name));
                                                $initials = strtoupper(substr($words[0] ?? '', 0, 1) . substr($words[1] ?? '', 0, 1)) ?: 'OC';
                                            @endphp
                                            <span class="text-base-content/80 font-mono text-[11px]">{{ $initials }}</span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-base-content text-sm">
                                            {{ $item->employee_name ?: ($item->employee->full_name ?? 'Unregistered External Card') }}
                                        </div>
                                        <div class="text-xs text-base-content/60 font-mono">
                                            {{ $item->employee->employee_id ?? ($item->badgenumber ? 'Badge: ' . $item->badgenumber : 'ID: N/A') }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-neutral badge-soft text-xs font-medium px-2.5 py-1">
                                    <i data-lucide="building" class="w-3 h-3 mr-1 opacity-70"></i>
                                    {{ $item->company_name ?? 'External Company' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-ghost font-mono px-2 py-1 text-xs">
                                    {{ $item->card_no ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="text-sm text-base-content/70 font-mono">{{ $item->badgenumber ?? '-' }}</td>
                            <td class="text-sm text-base-content/70">{{ $item->punch_date ? $item->punch_date->format('M d, Y') : '-' }}</td>
                            <td>
                                @if($item->check_in_time)
                                    <span class="text-base-content font-medium text-sm font-mono">{{ $item->check_in_time }}</span>
                                @elseif($item->check_in_datetime)
                                    <span class="text-base-content font-medium text-sm font-mono">{{ $item->check_in_datetime->format('h:i A') }}</span>
                                @else
                                    <span class="text-base-content/40 text-sm">--:--</span>
                                @endif
                            </td>
                            <td>
                                @if($item->check_out_time)
                                    <span class="text-base-content font-medium text-sm font-mono">{{ $item->check_out_time }}</span>
                                @elseif($item->check_out_datetime)
                                    <span class="text-base-content font-medium text-sm font-mono">{{ $item->check_out_datetime->format('h:i A') }}</span>
                                @else
                                    <span class="text-base-content/40 text-sm font-mono">--:--</span>
                                @endif
                            </td>
                            <td>
                                @if($item->total_time)
                                    <span class="badge badge-primary badge-soft font-mono font-semibold text-xs px-2.5 py-1 inline-flex items-center gap-1">
                                        <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                        <span>{{ $item->total_time }}</span>
                                    </span>
                                @else
                                    <span class="text-base-content/40 text-xs font-mono">--:--</span>
                                @endif
                            </td>
                            <td>
                                @if(strtolower((string) $item->show_status) === 'absent')
                                    <span class="badge badge-error badge-soft font-medium text-xs">
                                        Absent
                                    </span>
                                @else
                                    <span class="badge badge-success badge-soft font-medium text-xs">
                                        Present
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-12 text-base-content/60">
                                <div class="max-w-sm mx-auto flex flex-col items-center">
                                    <div class="p-3 rounded-full bg-base-200 text-base-content/40 mb-3">
                                        <i data-lucide="building" class="w-8 h-8"></i>
                                    </div>
                                    <div class="font-semibold text-base-content text-sm mb-1">No other company records found</div>
                                    <p class="text-xs text-base-content/60">No punches were recorded for partner/other companies on {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attendances->hasPages())
            <div class="border-t border-base-200/60 p-2">
                {{ $attendances->links('vendor.pagination.daisyui') }}
            </div>
        @endif
    </div>
</x-admin-layout>
