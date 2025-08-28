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

    public function expired_reward()
    {
        ini_set('max_execution_time', 0);

        $rew = Reward::first();
        $today = Carbon::today(); // Carbon instance
        $expiryThreshold = $today->copy()->subDays($rew->expiry_days); // for filtering customers

        Customer::select('id', 'date_time', 'vehicle_no', 'mobile_no', 'amount', 'type')
            ->chunk(500, function ($customers) use ($rew, $today, $expiryThreshold) {

                foreach ($customers as $customer) {

                    if (!empty($customer->date_time) && $this->isValidDate($customer->date_time, 'd-m-Y H:i:s')) {

                        // Customer purchase date as Carbon
                        $customerDate = Carbon::createFromFormat('d-m-Y H:i:s', $customer->date_time)->startOfDay();

                        // Only process rewards older than expiry_days
                        if ($customerDate->lessThanOrEqualTo($expiryThreshold)) {

                            // Calculate the actual reward expired date
                            $rewardExpiredDate = $customerDate->copy()->addDays($rew->expiry_days)->format('Y-m-d');

                            // Check if already logged
                            $alreadyExpired = DailyRewardExpire::where('customer_id', $customer->id)
                                ->where('reward_expired_date', $rewardExpiredDate)
                                ->exists();

                            if ($alreadyExpired) {
                                continue; // skip duplicates
                            }

                            // Fetch reward management
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

                            if (!$rewardmanag) continue;

                            // Calculate reward points
                            if ($customer->type == 'Regular' && $customer->amount >= $rew->regular_reward_price) {
                                $reward_points = ($customer->amount / $rew->regular_reward_price) * $rew->regular_reward_points;
                            } else if ($customer->type == 'Commercial' && $customer->amount >= $rew->commercial_reward_price) {
                                $reward_points = ($customer->amount / $rew->commercial_reward_price) * $rew->commercial_reward_points;
                            } else if ($customer->type == 'Tractor' && $customer->amount >= $rew->tractor_reward_price) {
                                $reward_points = ($customer->amount / $rew->tractor_reward_price) * $rew->tractor_reward_points;
                            } else {
                                $reward_points = 0;
                            }

                            if ($reward_points > 0) {
                                // Deduct points
                                $rewardmanag->pending_reward_points = max(0, $rewardmanag->pending_reward_points - $reward_points);
                                $rewardmanag->earned_reward_points  = max(0, $rewardmanag->earned_reward_points - $reward_points);
                                $rewardmanag->save();

                                // Log expired reward
                                DailyRewardExpire::create([
                                    'customer_id'         => $customer->id,
                                    'reward_created_date' => $customerDate->format('Y-m-d'),
                                    'reward_expired_date' => $rewardExpiredDate,
                                    'expired_on'          => $today->format('Y-m-d'),
                                    'expired_points'      => $reward_points,
                                ]);
                            }
                        }
                    }
                }
            });
        return redirect()->back()->with('success', 'Expired rewards updated successfully!');
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