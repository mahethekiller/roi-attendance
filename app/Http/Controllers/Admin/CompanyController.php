<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyController extends Controller
{
    /**
     * Display a listing of the companies.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $query = Company::withCount('employees')->latest();

        if (!empty($search)) {
            $query->search($search);
        }

        if (!empty($status) && in_array($status, ['active', 'inactive'])) {
            $query->where('status', $status);
        }

        $companies = $query->paginate(10)->withQueryString();

        $totalCompanies = Company::count();
        $activeCompanies = Company::where('status', 'active')->count();
        $totalEmployeesAssigned = Employee::whereNotNull('company_id')->count();

        return view('admin.companies.index', compact(
            'companies',
            'search',
            'status',
            'totalCompanies',
            'activeCompanies',
            'totalEmployeesAssigned'
        ));
    }

    /**
     * Show the form for creating a new company.
     */
    public function create(): View
    {
        return view('admin.companies.create');
    }

    /**
     * Store a newly created company in storage.
     */
    public function store(StoreCompanyRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $company = Company::create($validated);

        return redirect()->route('admin.companies.index')
            ->with('success', "Company '{$company->name}' created successfully.");
    }

    /**
     * Show the form for editing the specified company.
     */
    public function edit(Company $company): View
    {
        return view('admin.companies.edit', compact('company'));
    }

    /**
     * Update the specified company in storage.
     */
    public function update(UpdateCompanyRequest $request, Company $company): RedirectResponse
    {
        $validated = $request->validated();
        $oldName = $company->name;

        $company->update($validated);

        // Keep employees.company name string in sync if the name changed
        if ($oldName !== $company->name) {
            Employee::where('company_id', $company->id)->update([
                'company' => $company->name,
            ]);
        }

        return redirect()->route('admin.companies.index')
            ->with('success', "Company '{$company->name}' updated successfully.");
    }

    /**
     * Remove the specified company from storage.
     */
    public function destroy(Company $company): RedirectResponse
    {
        $assignedCount = $company->employees()->count();

        if ($assignedCount > 0) {
            return redirect()->route('admin.companies.index')
                ->with('error', "Cannot delete company '{$company->name}' because {$assignedCount} employee(s) are currently assigned to it. Please reassign or update them first.");
        }

        $companyName = $company->name;
        $company->delete();

        return redirect()->route('admin.companies.index')
            ->with('success', "Company '{$companyName}' deleted successfully.");
    }
}
