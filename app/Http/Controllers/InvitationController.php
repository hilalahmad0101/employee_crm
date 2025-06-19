<?php

namespace App\Http\Controllers;

use App\Models\EmployeeInvitation;
use App\Models\Company;
use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
// use App\Mail\InviteEmployeeMail; // Uncomment when you create the mailable

class InvitationController extends Controller
{
    /**
     * Send an employee invitation
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employee_invitations,email',
            'company_id' => 'required|exists:companies,id',
        ]);

        $token = Str::random(40);

        $invitation = EmployeeInvitation::create([
            ...$validated,
            'token' => $token,
            'status' => 'pending', // ensure this column exists in DB
        ]);

        // TODO: implement and enable this later
        // Mail::to($invitation->email)->send(new InviteEmployeeMail($invitation));

        // Log history
        History::create([
            'message' => 'Admin "' . auth()->user()->name . '" invited employee "' . $invitation->name . '" to join the company "' . $invitation->company->name . '"',
        ]);

        return response()->json(['message' => 'Invitation sent successfully', 'token' => $token]);
    }

    /**
     * Cancel an invitation if not accepted
     */
    public function cancel($id)
    {
        $invitation = EmployeeInvitation::findOrFail($id);

        if ($invitation->status === 'accepted') {
            return response()->json(['message' => 'Cannot cancel an accepted invitation'], 403);
        }

        $invitation->delete();

        // Log history
        History::create([
            'message' => 'Admin "' . auth()->user()->name . '" canceled invitation for "' . $invitation->name . '"',
        ]);

        return response()->json(['message' => 'Invitation cancelled']);
    }
}
