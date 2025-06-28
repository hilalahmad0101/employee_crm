<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Invitation;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class EmployeeController extends Controller
{
    public function index($token = null)
    {
        if (!$token) {
            return to_route('home')->with('toast', 'Invalid Token');
        }
        $invitation = Invitation::where('uuid', $token)->first();
        if ($invitation && $invitation->status != 'pending') {
            return to_route('home')->with('toast', 'Your already ' . $invitation->status . ' status');
        }
        return Inertia::render("employee/InvitationAccept", [
            'token' => $token
        ]);
    }

    public function accept($token = null)
    {
        if (!$token) {
            return to_route('home')->with('toast', 'Invalid Token');
        }

        $invitation = Invitation::where('uuid', $token)->first();
        if (!$invitation) {
            return to_route('home')->with('', '');
        }

        if ($invitation->status != 'pending') {
            return to_route('home')->with('toast', 'Your already ' . $invitation->status . ' status');
        }

        $invitation->status = 'accepted';
        $invitation->save();

        return redirect("/password/{$token}")->with('toast', 'Invitation accept please Update your profile to access the dashboard');
    }


    public function reject($token = null)
    {
        if (!$token) {
            return to_route('home')->with('toast', 'Invalid Token');
        }

        $invitation = Invitation::where('uuid', $token)->first();
        if (!$invitation) {
            return to_route('home')->with('', '');
        }

        if ($invitation->status != 'pending') {
            return to_route('home')->with('toast', 'Your already ' . $invitation->status . ' status');
        }

        $invitation->status = 'rejected';
        $invitation->save();

        return to_route('home')->with('toast', 'Thanks for your feedback');
    }

    public function passwordReset(Request $request, $token)
    {

        if (!$token) {
            return to_route('home')->with('toast', 'Invalid token');
        }

        $user = User::where('uuid', $token)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect("/profile/update/{$token}")->with('toast', 'Please update your profile to access the dashboard');
    }

    public function updateProfile(Request $request, $token)
    {
        if (!$token) {
            return to_route('home')->with('toast', 'Invalid token');
        }

        // Validate the incoming request data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone_number' => 'required|string|regex:/^[\+]?[0-9\s\-\(\)]{10,20}$/|unique:profiles,phone_number',
            'dob' => 'required|date|before:today',
        ], [
            'name.required' => 'Name is required.',
            'address.required' => 'Address is required.',
            'phone_number.required' => 'Phone number is required.',
            'phone_number.regex' => 'Please enter a valid phone number.',
            'phone_number.unique' => 'This phone number is already taken.',
            'dob.required' => 'Date of birth is required.',
            'dob.before' => 'Date of birth must be before today.',
        ]);

        try {
            // Find the user
            $user = User::where('uuid', $token)->first();

            if (!$user) {
                return back()->with('toast', 'User not found');
            }

            // Check if phone number is unique (excluding current user if updating)
            $existingProfile = Profile::where('phone_number', $validated['phone_number'])
                ->where('user_id', '!=', $user->id)
                ->first();

            if ($existingProfile) {
                return back()->withErrors(['phone_number' => 'This phone number is already taken.']);
            }

            // Update or create profile
            $profile = Profile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => $validated['name'],
                    'address' => $validated['address'],
                    'phone_number' => $validated['phone_number'],
                    'dob' => $validated['dob'],
                ]
            );

            $user->update([
                'name' => $request->name,
            ]);

            Auth::login($user);

            return to_route('dashboard')->with('toast', 'Profile updated successfully!');
        } catch (\Exception $e) {
            return back()->with('toast', 'Something went wrong. Please try again.');
        }
    }

    public function empList(Request $request)
    {
        $search = $request->input('search');
        $sortBy = $request->input('sortBy', 'asc');

        $employees = User::with('profile')
            ->where('role', 'employee')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', '%' . $search . '%')
                        ->orWhere('email', 'LIKE', '%' . $search . '%');
                });
            })
            ->orderBy('name', $sortBy)
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('employee/List', [
            'employees' => $employees,
            'filters' => [
                'sortBy' => $sortBy,
                'search' => $search,
            ],
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return back()->with('toast', 'Record Delete successfully');
    }
}
