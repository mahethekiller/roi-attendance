<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\OtherCompanyAttendance;
use App\Services\BiometricSyncService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OtherCompanyAttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $date = $request->input('date', date('Y-m-d'));
        $company = $request->input('company');
        $status = $request->input('status');

        $query = OtherCompanyAttendance::with('employee')
            ->forDate($date)
            ->latest('check_in_datetime');

        if (!empty($company)) {
            $query->forCompany($company);
        }

        if (!empty($status)) {
            $query->status($status);
        }

        if (!empty($search)) {
            $query->search($search);
        }

        $attendances = $query->paginate(15)->withQueryString();

        // Metrics for the selected date
        $baseDateQuery = OtherCompanyAttendance::whereDate('punch_date', $date);
        $totalPunchesToday = (clone $baseDateQuery)->count();
        $totalCompaniesToday = (clone $baseDateQuery)->whereNotNull('company_name')->distinct('company_name')->count('company_name');
        $totalStaffToday = (clone $baseDateQuery)->whereNotNull('card_no')->distinct('card_no')->count('card_no');

        // All distinct companies for the filter dropdown
        $attendanceCompanies = OtherCompanyAttendance::whereNotNull('company_name')
            ->distinct()
            ->pluck('company_name')
            ->toArray();

        $employeeCompanies = Employee::whereNotNull('company')
            ->where('company', '!=', '')
            ->where('company', 'not like', '%ROI Mantra%')
            ->distinct()
            ->pluck('company')
            ->toArray();

        $allCompanies = array_values(array_unique(array_filter(array_merge($attendanceCompanies, $employeeCompanies))));
        sort($allCompanies);

        return view('admin.other-attendances.index', compact(
            'attendances',
            'search',
            'date',
            'company',
            'status',
            'allCompanies',
            'totalPunchesToday',
            'totalCompaniesToday',
            'totalStaffToday'
        ));
    }

    public function sync(BiometricSyncService $service): RedirectResponse
    {
        $result = $service->sync(null, null, 'other_companies_ui');

        if ($result['success']) {
            return redirect()->route('admin.other-attendances.index')
                ->with('success', $result['message']);
        }

        return redirect()->route('admin.other-attendances.index')
            ->with('error', $result['message']);
    }
}
