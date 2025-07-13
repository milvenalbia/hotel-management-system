<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {

        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);
        
        $credentials = [
            'username' => $request->username,
            'password' => $request->password,
        ];
        
        // Case-sensitive comparison for the username
        if (Auth::attempt($credentials)) {
            // Retrieve the authenticated user
            $authenticatedUser = Auth::user();
        
            // Perform a case-sensitive check for the username
            if ($authenticatedUser && $authenticatedUser->username === $request->username) {
                return redirect('/dashboard')->with('success', 'You are now logged in!');
            } else {
                Auth::logout();

                session()->invalidate();
                session()->regenerateToken();
                
                return redirect('/login')->with('error', 'Invalid Credentials!');
            }
        } else {
            return back()->with('error', 'Invalid Credentials!');
        }


    }
}
