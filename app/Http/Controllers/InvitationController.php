<?php

namespace App\Http\Controllers;

use App\Mail\SendCompanyInvitation;
use App\Models\Company;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class InvitationController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortBy = $request->input('sortBy',  'asc');

        $invitations = Invitation::query()->when(
            $search,
            fn($query) =>
            $query->where('email', 'LIKE', '%' . $search . '%')
                ->orWhere('role', 'LIKE', '%' . $search . '%')
        )->with('company')->orderby('email', direction: $sortBy)->paginate(10)->withQueryString();
        return Inertia::render('invitations/List', [
            'invitations' => $invitations,
            'filters' => [
                'sortBy' => $sortBy,
                'search' => $search,
            ],
        ]);
    }

    public function create()
    {
        $companies = Company::latest()->get();
        return Inertia::render('invitations/Create', [
            'companies' => $companies
        ]);
    }


    public function sendInvitation(Request $request)
    {
        $request->validate([
            'email' => 'required|unique:invitations,email',
            'company_id' => 'required|exists:companies,id',
            'role' => 'required'
        ]);

        $company = Company::findOrFail($request->company_id);

        $invitation = Invitation::create([
            'email' => $request->email,
            'company_id' => $request->company_id,
            'role' => $request->role,
            'uuid' => Str::uuid()
        ]);
        $details = [
            'subject' => 'Company Email Invitations',
            'url' => url('/') . '/company/invitation/' . $invitation->uuid,
            'user_email' => $request->email,
            'email' => $company->email,
            'name' => $company->name,
            'address' => $company->address
        ];

        \Mail::to($request->email)->send(new SendCompanyInvitation($details));

        return to_route('invitations.list')->with('toast', 'Invitations send successfully');
    }
}
