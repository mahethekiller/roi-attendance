<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EmployeeController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $employees = Employee::with('user')
            ->search($search)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $totalEmployees = Employee::count();
        $assignedCards = Employee::whereNotNull('card_no')->where('card_no', '!=', '')->count();

        return view('admin.employees.index', compact('employees', 'search', 'totalEmployees', 'assignedCards'));
    }

    public function create(): View
    {
        return view('admin.employees.create');
    }

    public function store(StoreEmployeeRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {
            $fullName = trim("{$validated['first_name']} {$validated['last_name']}");
            $password = !empty($validated['password']) ? $validated['password'] : 'password123';

            $user = User::create([
                'name' => $fullName,
                'email' => $validated['email'],
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]);

            $user->assignRole('user');

            Employee::create([
                'user_id' => $user->id,
                'employee_id' => $validated['employee_id'],
                'card_no' => $validated['card_no'] ?? null,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'company' => $validated['company'] ?? null,
            ]);
        });

        return redirect()->route('admin.employees.index')
            ->with('success', "Employee '{$validated['first_name']} {$validated['last_name']}' created successfully.");
    }

    public function edit(Employee $employee): View
    {
        return view('admin.employees.edit', compact('employee'));
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($employee, $validated) {
            $fullName = trim("{$validated['first_name']} {$validated['last_name']}");

            $employee->update([
                'employee_id' => $validated['employee_id'],
                'card_no' => $validated['card_no'] ?? null,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'company' => $validated['company'] ?? null,
            ]);

            if ($employee->user) {
                $employee->user->update([
                    'name' => $fullName,
                    'email' => $validated['email'],
                ]);
            }
        });

        return redirect()->route('admin.employees.index')
            ->with('success', "Employee '{$employee->full_name}' updated successfully.");
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $employeeName = $employee->full_name;

        DB::transaction(function () use ($employee) {
            $user = $employee->user;
            $employee->delete();
            if ($user) {
                $user->delete();
            }
        });

        return redirect()->route('admin.employees.index')
            ->with('success', "Employee '{$employeeName}' and associated user account deleted.");
    }

    public function downloadSampleCsv(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="employees_sample.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            // CSV Header
            fputcsv($file, ['employee_id', 'card_no', 'first_name', 'last_name', 'email', 'company_name']);

            // Dummy CSV rows
            $dummyData = [
                ['EMP-1001', 'CRD-7701', 'Alexander', 'Pierce', 'alex.pierce@example.com', 'Acme Corp'],
                ['EMP-1002', 'CRD-7702', 'Eleanor', 'Vance', 'eleanor.vance@example.com', 'Acme Corp'],
                ['EMP-1003', 'CRD-7703', 'Marcus', 'Aurelius', 'marcus.aurelius@example.com', 'Tech Innovations'],
                ['EMP-1004', 'CRD-7704', 'Sophia', 'Chen', 'sophia.chen@example.com', 'Tech Innovations'],
                ['EMP-1005', 'CRD-7705', 'David', 'Kim', 'david.kim@example.com', 'Global Logistics'],
            ];

            foreach ($dummyData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importCsv(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        if (!$handle) {
            return redirect()->route('admin.employees.index')
                ->with('error', 'Unable to read the uploaded CSV file.');
        }

        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return redirect()->route('admin.employees.index')
                ->with('error', 'The CSV file is empty.');
        }

        // Normalize headers
        $headerMap = [];
        foreach ($header as $idx => $h) {
            $cleaned = strtolower(trim(str_replace([' ', '_', '-'], '', $h)));
            $headerMap[$cleaned] = $idx;
        }

        $importedCount = 0;
        $skippedCount = 0;
        $rowNumber = 1;

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                $rowNumber++;
                if (empty(array_filter($row))) {
                    continue; // Skip empty rows
                }

                // Match by named header or fallback positions
                $getVal = function ($keys, $fallbackIdx) use ($row, $headerMap) {
                    foreach ((array)$keys as $k) {
                        if (isset($headerMap[$k]) && isset($row[$headerMap[$k]])) {
                            return trim($row[$headerMap[$k]]);
                        }
                    }
                    return isset($row[$fallbackIdx]) ? trim($row[$fallbackIdx]) : '';
                };

                $empId     = $getVal(['employeeid', 'empid', 'id'], 0);
                $cardNo    = $getVal(['cardno', 'card', 'cardnumber', 'badgenumber'], 1);
                $firstName = $getVal(['firstname', 'first', 'name'], 2);
                $lastName  = $getVal(['lastname', 'last'], 3);
                $email     = strtolower($getVal(['email', 'emailaddress', 'useremail'], 4));
                $company   = $getVal(['companyname', 'company', 'compname'], 5);

                // If empId is numeric user_id and employee_id was separate
                if (isset($headerMap['userid']) && isset($headerMap['employeeid'])) {
                    $empId = trim($row[$headerMap['employeeid']]);
                }

                if (empty($empId) && empty($firstName)) {
                    $skippedCount++;
                    continue;
                }

                if (empty($empId)) {
                    $empId = 'EMP-' . str_pad($rowNumber, 4, '0', STR_PAD_LEFT);
                }

                if (empty($firstName)) {
                    $firstName = 'Employee';
                }

                if (empty($email)) {
                    $sanitizedEmpId = preg_replace('/[^A-Za-z0-9]/', '', $empId);
                    $email = strtolower($sanitizedEmpId . '@company.local');
                }

                // Check for duplicate employee_id or email
                if (Employee::where('employee_id', $empId)->exists() || Employee::where('email', $email)->exists()) {
                    $skippedCount++;
                    continue;
                }

                $fullName = trim("{$firstName} {$lastName}");

                // Find or create User
                $user = User::where('email', $email)->first();
                if (!$user) {
                    $user = User::create([
                        'name' => $fullName,
                        'email' => $email,
                        'password' => Hash::make('password123'),
                        'email_verified_at' => now(),
                    ]);
                    $user->assignRole('user');
                }

                Employee::create([
                    'user_id' => $user->id,
                    'employee_id' => $empId,
                    'card_no' => !empty($cardNo) ? $cardNo : null,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'company' => !empty($company) ? $company : null,
                ]);

                $importedCount++;
            }

            DB::commit();
            fclose($handle);

            return redirect()->route('admin.employees.index')
                ->with('success', "CSV Import Complete: {$importedCount} employees imported successfully. ({$skippedCount} skipped/duplicate rows)");
        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);
            return redirect()->route('admin.employees.index')
                ->with('error', "Import failed on row {$rowNumber}: " . $e->getMessage());
        }
    }
}
