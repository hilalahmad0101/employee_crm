<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\EmployeeInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RegisterEmployeeController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $invitation = EmployeeInvitation::where('token', $request->token)
            ->where('status', 'pending')
            ->first();

        if (!$invitation) {
            return response()->json(['message' => 'Invalid or expired token.'], 404);
        }

        $user = User::create([
            'name' => $invitation->name,
            'email' => $invitation->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('employee');

        $invitation->update(['status' => 'accepted']);

        return response()->json(['message' => 'Registration complete.']);
    }
}
