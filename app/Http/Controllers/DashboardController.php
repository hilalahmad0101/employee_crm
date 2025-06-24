<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
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
        )->orderby('name', $sortBy)->paginate(10)->withQueryString();

        return Inertia::render('Dashboard', [
            'companies' => $companies,
            'totalCompanies'=>Company::count(),
            'filters' => [
                'sortBy' => $sortBy,
                'search' => $search,
            ],
        ]);
    }
}
