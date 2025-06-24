<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortBy = $request->input('sortBy',  'asc');

        $companies = Company::query()->when(
            $search,
            fn($query) =>
            $query->where('name', 'LIKE', '%' . $search . '%')
                ->orWhere('email', 'LIKE', '%' . $search . '%')
                ->orWhere('address', 'LIKE', '%' . $search . '%')
        )->orderby('name', $sortBy)->paginate(10)->withQueryString();;
        return Inertia::render('companies/List', [
            'companies' => $companies,
            'filters' => [
                'sortBy' => $sortBy,
                'search' => $search,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('companies/Create');
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies,email',
            'address' => 'nullable|string|max:255',
        ]);

        $company = Company::create([
            'name' => $validated['company_name'],
            'email' => $validated['email'],
            'address' => $validated['address'],
        ]);
        return to_route('companies.list')->with('toast', 'Record add successfully');
        // return response()->json(['message' => 'Company created', 'company' => $company]);
    }

    public function edit($id)
    {
        return Inertia::render('companies/Update', [
            'company' => Company::findOrFail($id)
        ]);
    }

    public function update(Request $request, $id)
    {
        $company = Company::findOrFail($id);
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies,email,' . $company->id,
            'address' => 'nullable|string|max:255',
        ]);

        $company->update([
            'name' => $validated['company_name'],
            'email' => $validated['email'],
            'address' => $validated['address'],
        ]);
        return to_route('companies.list')->with('toast', 'Record Update successfully');
    }

    public function destroy(Company $company)
    {
        $company->delete();
        return to_route('companies.list')->with('toast', 'Record Delete successfully');
    }
}
