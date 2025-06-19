<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmployeeInvitation;

class AcceptInvitationController extends Controller
{
    public function show(Request $request)
    {
        $invitation = EmployeeInvitation::where('token', $request->token)->first();

        if (!$invitation || $invitation->status === 'accepted') {
            return response()->json(['message' => 'Invalid or expired invitation.'], 404);
        }

        return response()->json([
            'name' => $invitation->name,
            'email' => $invitation->email,
            'company_id' => $invitation->company_id,
        ]);
    }
}
