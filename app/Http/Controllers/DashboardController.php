<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortBy = $request->input('sortBy',  'asc');

        $user = Auth::user();
        // dd($user-);
        if ($user->role == 'employee') {
            $invitation = Invitation::where('uuid', $user->uuid)->first();
            if ($invitation) {
                $companies = Company::query()->where('id', $invitation->company_id)->when(
                    $search,
                    fn($query) =>
                    $query->where('id', $invitation->company_id)->where('name', 'LIKE', '%' . $search . '%')

                        ->orWhere('email', 'LIKE', '%' . $search . '%')
                        ->orWhere('address', 'LIKE', '%' . $search . '%')
                )->orderby('name', $sortBy)->paginate(10)->withQueryString();


                return Inertia::render('Dashboard', [
                    'companies' => $companies,
                    'totalCompanies' => Company::count(),
                    'filters' => [
                        'sortBy' => $sortBy,
                        'search' => $search,
                    ],
                ]);
            }
        }

        $companies = Company::query()->when(
            $search,
            fn($query) =>
            $query->where('name', 'LIKE', '%' . $search . '%')
                ->orWhere('email', 'LIKE', '%' . $search . '%')
                ->orWhere('address', 'LIKE', '%' . $search . '%')
        )->orderby('name', $sortBy)->paginate(10)->withQueryString();

        return Inertia::render('Dashboard', [
            'companies' => $companies,
            'totalCompanies' => Company::count(),
            'totalUsers' => User::where('role','employee')->count(),
            'totalInvitation' => Invitation::where('status','pending')->count(),
            'filters' => [
                'sortBy' => $sortBy,
                'search' => $search,
            ],
        ]);
    }
}
