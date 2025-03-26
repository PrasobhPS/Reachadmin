<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ReachAdmin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the list of members.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function profile()
    {
        $user = auth()->user();
    }
    public function changePassword()
    {
        $user = auth()->user();

        return view('auth.passwords.change');
    }

    public function updatePassword(Request $request)
    {

        $user = auth()->user();
        $requestData = $request->all();
        // Validation rules
        $request->validate([
            'current_password' => [
                'required',
                function ($attribute, $value, $fail) use ($user) {
                    if (!Hash::check($value, $user->password)) {
                        $fail('The provided password does not match your current password.');
                    }
                }
            ],
            'new_password' => [
                'required',
                'string',

            ],
        ], [
            // Custom error messages
            'current_password.required' => 'The current password field is required.',
            'new_password.confirmed' => 'The new password confirmation does not match.',
        ]);

        // Update the password
        ReachAdmin::find($user['id'])->update([
            'password' => Hash::make($requestData['new_password']),
        ]);

        // Optionally, redirect with a success message
        return redirect()->back()->with('status', 'Password updated successfully.');
    }
}
