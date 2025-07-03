<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortBy = $request->input('sortBy',  'asc');

        $admins = User::query()
            ->where('role', 'admin')
            ->orWhere('role', 'company')
            ->where('id', '!=', auth()->id())
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', '%' . $search . '%')
                        ->orWhere('email', 'LIKE', '%' . $search . '%');
                });
            })
            ->orderBy('name', $sortBy)
            ->paginate(10)
            ->withQueryString();
        return Inertia::render('admin/List', [
            'admins' => $admins,
            'filters' => [
                'sortBy' => $sortBy,
                'search' => $search,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('admin/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required',
            'role' => 'required|in:admin,company'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
        ]);
        return redirect()->route('admins.list')->with('success', 'Admin create successfully');
    }

    public function show(User $user)
    {
        return Inertia::render('admin/Update', [
            'user' => $user
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,company'
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);
        return redirect()->route('admins.list')->with('success', 'Admin update successfully');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admins.list')->with('success', 'Admin Delete successfully');
    }
}
