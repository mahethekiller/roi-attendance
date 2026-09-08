<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceOverride;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AttendanceOverrideController extends Controller
{
    public function index(Request $request): View
    {
        $query = AttendanceOverride::with('employee')->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('employee_name', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%")
                  ->orWhere('card_no', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $status = $request->status === 'active';
            $query->where('is_active', $status);
        }

        $overrides = $query->paginate(15)->withQueryString();
        $totalRules = AttendanceOverride::count();
        $activeRules = AttendanceOverride::where('is_active', true)->count();

        return view('admin.attendance-overrides.index', compact('overrides', 'totalRules', 'activeRules'));
    }

    public function create(): View
    {
        $employees = Employee::orderBy('first_name')->get(['id', 'employee_id', 'card_no', 'first_name', 'last_name', 'company']);

        return view('admin.attendance-overrides.create', compact('employees'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id'            => ['nullable', 'string', 'max:100'],
            'card_no'                => ['nullable', 'string', 'max:100'],
            'employee_name'          => ['nullable', 'string', 'max:255'],
            'check_in_window_start'  => ['required', 'string'],
            'check_in_window_end'    => ['required', 'string'],
            'adjusted_in_min_minute' => ['required', 'integer', 'min:0', 'max:59'],
            'adjusted_in_max_minute' => ['required', 'integer', 'min:0', 'max:59', 'gte:adjusted_in_min_minute'],
            'min_duration_hours'     => ['required', 'numeric', 'min:1', 'max:24'],
            'is_active'              => ['sometimes', 'boolean'],
            'notes'                  => ['nullable', 'string', 'max:1000'],
        ]);

        if (empty($validated['employee_id']) && empty($validated['card_no'])) {
            return back()->withInput()->withErrors(['employee_id' => 'Please provide at least a target Employee ID or Card Number.']);
        }

        // Normalize time formats to H:i:s
        $validated['check_in_window_start'] = strlen($validated['check_in_window_start']) === 5 ? $validated['check_in_window_start'] . ':00' : $validated['check_in_window_start'];
        $validated['check_in_window_end'] = strlen($validated['check_in_window_end']) === 5 ? $validated['check_in_window_end'] . ':00' : $validated['check_in_window_end'];
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['created_by'] = Auth::id();

        // Auto-fill employee_name if empty and employee exists
        if (empty($validated['employee_name'])) {
            $matchedEmp = null;
            if (!empty($validated['card_no'])) {
                $matchedEmp = Employee::where('card_no', $validated['card_no'])->first();
            }
            if (!$matchedEmp && !empty($validated['employee_id'])) {
                $matchedEmp = Employee::where('employee_id', $validated['employee_id'])->first();
            }
            if ($matchedEmp) {
                $validated['employee_name'] = $matchedEmp->full_name;
            }
        }

        AttendanceOverride::create($validated);

        return redirect()->route('admin.attendance-overrides.index')
            ->with('success', 'Attendance override rule successfully created.');
    }

    public function edit(AttendanceOverride $attendanceOverride): View
    {
        $employees = Employee::orderBy('first_name')->get(['id', 'employee_id', 'card_no', 'first_name', 'last_name', 'company']);

        return view('admin.attendance-overrides.edit', compact('attendanceOverride', 'employees'));
    }

    public function update(Request $request, AttendanceOverride $attendanceOverride): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id'            => ['nullable', 'string', 'max:100'],
            'card_no'                => ['nullable', 'string', 'max:100'],
            'employee_name'          => ['nullable', 'string', 'max:255'],
            'check_in_window_start'  => ['required', 'string'],
            'check_in_window_end'    => ['required', 'string'],
            'adjusted_in_min_minute' => ['required', 'integer', 'min:0', 'max:59'],
            'adjusted_in_max_minute' => ['required', 'integer', 'min:0', 'max:59', 'gte:adjusted_in_min_minute'],
            'min_duration_hours'     => ['required', 'numeric', 'min:1', 'max:24'],
            'is_active'              => ['sometimes', 'boolean'],
            'notes'                  => ['nullable', 'string', 'max:1000'],
        ]);

        if (empty($validated['employee_id']) && empty($validated['card_no'])) {
            return back()->withInput()->withErrors(['employee_id' => 'Please provide at least a target Employee ID or Card Number.']);
        }

        $validated['check_in_window_start'] = strlen($validated['check_in_window_start']) === 5 ? $validated['check_in_window_start'] . ':00' : $validated['check_in_window_start'];
        $validated['check_in_window_end'] = strlen($validated['check_in_window_end']) === 5 ? $validated['check_in_window_end'] . ':00' : $validated['check_in_window_end'];
        $validated['is_active'] = $request->boolean('is_active', false);

        $attendanceOverride->update($validated);

        return redirect()->route('admin.attendance-overrides.index')
            ->with('success', 'Attendance override rule updated successfully.');
    }

    public function toggle(AttendanceOverride $attendanceOverride): RedirectResponse
    {
        $attendanceOverride->update([
            'is_active' => !$attendanceOverride->is_active,
        ]);

        $statusText = $attendanceOverride->is_active ? 'activated' : 'deactivated';

        return redirect()->route('admin.attendance-overrides.index')
            ->with('success', "Override rule for '{$attendanceOverride->employee_name}' has been {$statusText}.");
    }

    public function destroy(AttendanceOverride $attendanceOverride): RedirectResponse
    {
        $name = $attendanceOverride->employee_name ?: ($attendanceOverride->employee_id ?: $attendanceOverride->card_no);
        $attendanceOverride->delete();

        return redirect()->route('admin.attendance-overrides.index')
            ->with('success', "Attendance override rule for '{$name}' deleted successfully.");
    }
}
