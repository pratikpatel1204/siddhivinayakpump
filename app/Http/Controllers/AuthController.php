<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reward;
use App\Models\Customer;
use App\Models\DailyRewardExpire;
use App\Models\RewardManagement;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return response()->json(['redirect' => route('show.dashboard')], 200);
        }

        return response()->json([
            'errors' => ['email' => ['Invalid credentials.']]
        ], 422);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
    public function showprofile()
    {
        $user = auth()->user();
        return view('profile', compact('user'));
    }
    public function updatePassword(Request $request)
    {
        // Validate the request
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|confirmed|min:6',
        ]);

        // Get the authenticated user
        $user = auth()->user();

        // Check if the current password matches
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['status' => 'error', 'message' => 'Current password is incorrect.']);
        }

        // Update the password
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json(['status' => 'success']);
    }
}