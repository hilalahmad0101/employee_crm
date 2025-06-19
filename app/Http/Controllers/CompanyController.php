<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::withCount('employees')->orderBy('name')->get();
        return response()->json($companies);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies,email',
            'address' => 'nullable|string|max:255',
        ]);

        $company = Company::create($validated);

        return response()->json(['message' => 'Company created', 'company' => $company]);
    }

    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies,email,' . $company->id,
            'address' => 'nullable|string|max:255',
        ]);

        $company->update($validated);

        return response()->json(['message' => 'Company updated', 'company' => $company]);
    }

    public function destroy(Company $company)
    {
        if ($company->employees()->exists()) {
            return response()->json(['message' => 'Cannot delete company with employees.'], 403);
        }

        $company->delete();
        return response()->json(['message' => 'Company deleted']);
    }
}
