<x-admin-layout>
    <!-- Header Title & Action Buttons -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2.5 mb-1 flex-wrap">
                <h2 class="text-2xl sm:text-3xl font-bold text-base-content tracking-tight">Attendance Overrides</h2>
                <span class="badge badge-primary badge-soft font-mono text-xs px-2.5 py-1">
                    {{ $activeRules }} / {{ $totalRules }} Active
                </span>
                <span class="badge badge-neutral badge-soft text-xs hidden sm:inline-flex px-2.5 py-1">
                    Super Admin Controlled
                </span>
            </div>
            <p class="text-sm text-base-content/70 max-w-2xl leading-relaxed">
                Configure automated punch-in time adjustments and minimum shift duration overrides for specific employees or RFID cards during biometric synchronization.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('admin.attendance-overrides.create') }}" class="btn btn-primary btn-sm shadow-xs flex items-center gap-2 transition-all duration-200 hover:shadow-sm">
                <i data-lucide="plus" style="width: 15px; height: 15px;"></i>
                <span>Add Override Rule</span>
            </a>
            <x-authorized permission="attendances.sync">
                <form action="{{ route('admin.attendances.sync') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="btn btn-outline btn-sm shadow-xs flex items-center gap-2">
                        <i data-lucide="refresh-cw" style="width: 14px; height: 14px;"></i>
                        <span>Sync Now</span>
                    </button>
                </form>
            </x-authorized>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-xs mb-6 flex items-center gap-3 border border-success/20" role="alert">
            <i data-lucide="check-circle-2" class="text-success shrink-0" style="width: 18px; height: 18px;"></i>
            <span class="flex-1 text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error shadow-xs mb-6 flex items-center gap-3 border border-error/20" role="alert">
            <i data-lucide="alert-triangle" class="text-error shrink-0" style="width: 18px; height: 18px;"></i>
            <span class="flex-1 text-sm font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Search & Filter Bar -->
    <div class="card bg-base-100 border border-base-200 shadow-xs mb-6">
        <div class="card-body p-4">
            <form action="{{ route('admin.attendance-overrides.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3">
                <div class="relative flex-1 w-full">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-base-content/40" style="width: 16px; height: 16px;"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, employee ID, or card number..." class="input input-sm input-bordered w-full pl-9" />
                </div>
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <select name="status" class="select select-sm select-bordered w-full sm:w-auto">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Only</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive Only</option>
                    </select>
                    <button type="submit" class="btn btn-sm btn-neutral gap-1.5 shrink-0">
                        <i data-lucide="filter" style="width: 14px; height: 14px;"></i>
                        <span>Filter</span>
                    </button>
                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('admin.attendance-overrides.index') }}" class="btn btn-sm btn-ghost shrink-0" title="Clear filters">
                            <i data-lucide="x" style="width: 14px; height: 14px;"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Overrides Data Table -->
    <div class="card bg-base-100 border border-base-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table table-zebra table-hover w-full text-sm">
                <thead class="bg-base-200/50 text-base-content/70 text-xs uppercase font-semibold tracking-wider">
                    <tr>
                        <th class="pl-5 py-3.5">Target Employee</th>
                        <th class="py-3.5">Card Number</th>
                        <th class="py-3.5">Trigger Window</th>
                        <th class="py-3.5">Adjusted Punch-In</th>
                        <th class="py-3.5">Min Duration</th>
                        <th class="py-3.5">Status</th>
                        <th class="py-3.5">Notes</th>
                        <th class="text-right pr-5 py-3.5">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-base-200/60">
                    @forelse($overrides as $rule)
                        @php
                            $displayName = $rule->employee_name ?: ($rule->employee?->full_name ?? 'Unspecified');
                            $initials = strtoupper(substr($displayName, 0, 2));
                        @endphp
                        <tr class="hover:bg-base-200/40 transition-colors">
                            <td class="pl-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="avatar placeholder shrink-0">
                                        <div class="bg-primary/10 text-primary border border-primary/20 rounded-xl w-8 h-8 text-xs font-bold font-mono flex items-center justify-center">
                                            {{ $initials }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-base-content leading-tight">{{ $displayName }}</div>
                                        @if($rule->employee_id)
                                            <div class="text-[11px] text-base-content/60 font-mono mt-0.5">
                                                ID: {{ $rule->employee_id }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-3">
                                @if($rule->card_no)
                                    <span class="badge badge-sm badge-neutral badge-soft font-mono px-2 py-0.5">
                                        {{ $rule->card_no }}
                                    </span>
                                @else
                                    <span class="text-base-content/40 text-xs">—</span>
                                @endif
                            </td>
                            <td class="py-3">
                                <div class="font-mono text-xs text-base-content tabular-nums font-medium">
                                    {{ substr($rule->check_in_window_start, 0, 5) }} – {{ substr($rule->check_in_window_end, 0, 5) }}
                                </div>
                                <div class="text-[11px] text-base-content/50">Punch-in window</div>
                            </td>
                            <td class="py-3">
                                <span class="badge badge-sm badge-info badge-soft font-mono text-xs px-2 py-0.5">
                                    09:{{ sprintf('%02d', $rule->adjusted_in_min_minute) }} – 09:{{ sprintf('%02d', $rule->adjusted_in_max_minute) }} AM
                                </span>
                            </td>
                            <td class="py-3">
                                <span class="font-mono text-xs font-semibold text-base-content tabular-nums">
                                    {{ number_format($rule->min_duration_hours, 1) }} hrs
                                </span>
                            </td>
                            <td class="py-3">
                                <form action="{{ route('admin.attendance-overrides.toggle', $rule) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="badge badge-sm cursor-pointer transition-transform hover:scale-105 {{ $rule->is_active ? 'badge-success badge-soft' : 'badge-neutral badge-soft' }}" title="Click to toggle active status">
                                        <span class="inline-block w-1.5 h-1.5 rounded-full mr-1 {{ $rule->is_active ? 'bg-success' : 'bg-base-content/40' }}"></span>
                                        {{ $rule->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
                            </td>
                            <td class="py-3 max-w-xs truncate text-xs text-base-content/70" title="{{ $rule->notes }}">
                                {{ $rule->notes ?: '—' }}
                            </td>
                            <td class="text-right pr-5 py-3">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.attendance-overrides.edit', $rule) }}" class="btn btn-xs btn-outline" title="Edit rule">
                                        <i data-lucide="edit-3" style="width: 12px; height: 12px;"></i>
                                        <span>Edit</span>
                                    </a>
                                    <form action="{{ route('admin.attendance-overrides.destroy', $rule) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this attendance override rule?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-outline btn-error" title="Delete rule">
                                            <i data-lucide="trash-2" style="width: 12px; height: 12px;"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-base-content/60">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="rounded-2xl bg-base-200 p-4 mb-3 text-base-content/50">
                                        <i data-lucide="sliders" style="width: 32px; height: 32px;"></i>
                                    </div>
                                    <h4 class="font-bold text-base-content text-base mb-1">No attendance override rules found</h4>
                                    <p class="text-xs text-base-content/60 mb-4 max-w-sm leading-relaxed">
                                        Configure an automated override rule to adjust biometric punches for specific staff members.
                                    </p>
                                    <a href="{{ route('admin.attendance-overrides.create') }}" class="btn btn-primary btn-sm inline-flex items-center gap-2 shadow-xs">
                                        <i data-lucide="plus" style="width: 14px; height: 14px;"></i>
                                        <span>Create First Override Rule</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($overrides->hasPages())
            <div class="p-4 border-t border-base-200">
                {{ $overrides->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>
