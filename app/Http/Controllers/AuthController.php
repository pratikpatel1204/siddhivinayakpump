<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reward;
use App\Models\Customer;
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
            $this->expired_reward();
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

    public function expired_reward()
    {
        $rew = Reward::first(); // Get first reward
        $today = Carbon::today()->format('d-m-Y'); // Get today's date in DD-MM-YYYY format

        $customers = Customer::select('date_time', 'vehicle_no', 'mobile_no', 'amount', 'type')->get();
        foreach ($customers as $customer) {
            if (!empty($customer->date_time) && $this->isValidDate($customer->date_time, 'd-m-Y H:i:s')) {            
                $date = Carbon::parse($customer->date_time);
                $expiryDate = $date->copy()->addDays($rew->expiry_days);
                if ($expiryDate->isSameDay($today)) {
                    $rewardmanag = RewardManagement::where(function ($query) use ($customer) {
                        if (!empty($customer->vehicle_no)) {
                            $query->where('vehicle_no', $customer->vehicle_no);
                        }
                        if (!empty($customer->mobile_no)) {
                            $query->where('mobile_no', $customer->mobile_no);
                        }
                        if (!empty($customer->type)) {
                            $query->where('type', $customer->type);
                        }
                    })->first();
                    if ($customer->type == 'Regular' && $customer->amount >= $rew->regular_reward_price) {
                        $reward_points = ($customer->amount / $rew->regular_reward_price) * $rew->regular_reward_points;
                    } else if ($customer->type == 'Commercial' && $customer->amount >= $rew->commercial_reward_price) {
                        $reward_points = ($customer->amount / $rew->commercial_reward_price) * $rew->commercial_reward_points;
                    } else if ($customer->type == 'Tractor' && $customer->amount >= $rew->tractor_reward_price) {
                        $reward_points = ($customer->amount / $rew->tractor_reward_price) * $rew->tractor_reward_points;
                    } else {
                        $reward_points = 0;
                    }
                    $rewardmanag->pending_reward_points -= $reward_points;
                    $rewardmanag->earned_reward_points -= $reward_points;
                    $rewardmanag->pending_reward_points = max(0, $rewardmanag->pending_reward_points);
                    $rewardmanag->earned_reward_points = max(0, $rewardmanag->earned_reward_points);
                    $rewardmanag->save();        
                }
            }
        }
    }
    private function isValidDate($date, $format = 'd-m-Y H:i:s')
    {
        try {
            $d = Carbon::createFromFormat($format, $date);
            return $d && $d->format($format) === $date;
        } catch (\Exception $e) {
            return false;
        }
    }
}