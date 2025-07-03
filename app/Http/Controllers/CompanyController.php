<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortBy = $request->input('sortBy', 'asc');
        $user = Auth::user();

        // For employees: restrict to their invited company
        if ($user->role == 'employee') {
            $invitation = Invitation::where('uuid', $user->uuid)->first();

            if ($invitation) {
                $companies = Company::query()
                    ->where('id', $invitation->company_id)
                    ->when($search, function ($query) use ($search) {
                        $query->where(function ($q) use ($search) {
                            $q->where('name', 'LIKE', '%' . $search . '%')
                                ->orWhere('email', 'LIKE', '%' . $search . '%')
                                ->orWhere('address', 'LIKE', '%' . $search . '%');
                        });
                    })
                    ->orderBy('name', $sortBy)
                    ->paginate(10)
                    ->withQueryString();

                return Inertia::render('companies/List', [
                    'companies' => $companies,
                    'filters' => [
                        'sortBy' => $sortBy,
                        'search' => $search,
                    ],
                ]);
            }
        }

        // For all other roles: search all companies
        $companies = Company::query()
            ->where('admin_id', $user->id)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', '%' . $search . '%')
                        ->orWhere('email', 'LIKE', '%' . $search . '%')
                        ->orWhere('address', 'LIKE', '%' . $search . '%');
                });
            })
            ->orderBy('name', $sortBy)
            ->paginate(10)
            ->withQueryString();

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
            'admin_id' => auth()->id()
        ]);
        return to_route('companies.list')->with('toast', 'Record add successfully');
        // return response()->json(['message' => 'Company created', 'company' => $company]);
    }

    public function edit($id)
    {
        $company = Company::findOrFail($id);
        if ($company->admin_id != auth()->id()) {
            return back()->with('toast', 'Cannot get other company');
        }
        return Inertia::render('companies/Update', [
            'company' => $company
        ]);
    }

    public function update(Request $request, $id)
    {
        $company = Company::findOrFail($id);
        if ($company->admin_id != auth()->id()) {
            return back()->with('toast', 'Cannot update other company');
        }
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
        if ($company->admin_id != auth()->id()) {
            return back()->with('toast', 'Cannot delete other record');
        }
        $company->delete();
        return back()->with('toast', 'Record Delete successfully');
    }
}
