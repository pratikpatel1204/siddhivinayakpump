<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\DailyRewardExpire;
use App\Models\Reward;
use App\Models\RewardManagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

class RewardController extends Controller
{
    public function show_reward_master()
    {
        $reward = Reward::all();
        return view('reward-master.list', compact('reward'));
    }
    public function edit_reward_master($encryptedId)
    {
        try {
            $id = Crypt::decryptString($encryptedId);
            $reward = Reward::findOrFail($id);
            return view('reward-master.edit', compact('reward'));
        } catch (\Exception $e) {
            return abort(404);
        }
    }
    public function update_reward_master(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:rewards,id',
            'regular_reward_price' => 'required|numeric|min:0',
            'regular_reward_points' => 'required|integer|min:0',
            'commercial_reward_price' => 'required|numeric|min:0',
            'commercial_reward_points' => 'required|integer|min:0',
            'tractor_reward_price' => 'required|numeric|min:0',
            'tractor_reward_points' => 'required|integer|min:0',
            'regular_price' => 'required|numeric|min:0',
            'regular_gift_point' => 'required|numeric|min:0',
            'commercial_price' => 'required|numeric|min:0',
            'commercial_gift_point' => 'required|numeric|min:0',
            'tractor_price' => 'required|numeric|min:0',
            'tractor_gift_point' => 'required|numeric|min:0',
            'expiry_days' => 'required|numeric|min:0',
        ]);

        try {
            $reward = Reward::findOrFail($request->id);

            $reward->update([
                'regular_reward_price' => $request->regular_reward_price ?? 0,
                'regular_reward_points' => $request->regular_reward_points ?? 0,
                'commercial_reward_price' => $request->commercial_reward_price ?? 0,
                'commercial_reward_points' => $request->commercial_reward_points ?? 0,
                'tractor_reward_price' => $request->tractor_reward_price ?? 0,
                'tractor_reward_points' => $request->tractor_reward_points ?? 0,
                'regular_price' => $request->regular_price ?? 0,
                'regular_gift_point' => $request->regular_gift_point ?? 0,
                'commercial_price' => $request->commercial_price ?? 0,
                'commercial_gift_point' => $request->commercial_gift_point ?? 0,
                'tractor_price' => $request->tractor_price ?? 0,
                'tractor_gift_point' => $request->tractor_gift_point ?? 0,
                'expiry_days' => $request->expiry_days ?? 0,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Reward updated successfully!',
                'reward' => $reward
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Reward not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update reward: ' . $e->getMessage()
            ], 500);
        }
    }
    public function show_reward_management()
    {
        $rewardmanag = RewardManagement::all();
        return view('reward-management.list', compact('rewardmanag'));
    }
    public function updateTotalEarnedPoints()
    {
        ini_set('max_execution_time', 0); // avoid timeout for large data

        $rew = Reward::first();
        $typeList = ['Regular', 'Commercial', 'Tractor'];

        Customer::chunk(500, function ($customers) use ($rew, $typeList) {

            foreach ($customers as $cs) {

                // Skip if already processed (optional: add this column in Customer table)
                if (isset($cs->reward_processed) && $cs->reward_processed) {
                    continue;
                }

                // Validate amount
                if (!is_numeric($cs->amount) || $cs->amount <= 0) {
                    continue;
                }

                // Calculate reward points
                $reward_points = 0;
                if ($cs->type == 'Regular' && $cs->amount >= $rew->regular_reward_price) {
                    $reward_points = ($cs->amount / $rew->regular_reward_price) * $rew->regular_reward_points;
                } elseif ($cs->type == 'Commercial' && $cs->amount >= $rew->commercial_reward_price) {
                    $reward_points = ($cs->amount / $rew->commercial_reward_price) * $rew->commercial_reward_points;
                } elseif ($cs->type == 'Tractor' && $cs->amount >= $rew->tractor_reward_price) {
                    $reward_points = ($cs->amount / $rew->tractor_reward_price) * $rew->tractor_reward_points;
                }

                // Update RewardManagement if exists
                if ($reward_points > 0) {
                    $rewardmanag = RewardManagement::where('vehicle_no', $cs->vehicle_no)
                        ->where('mobile_no', $cs->mobile_no)
                        ->where('type', $cs->type)
                        ->first();

                    if ($rewardmanag) {
                        $rewardmanag->total_earned_points += $reward_points;
                        $rewardmanag->save();

                        // Mark customer as processed to avoid double counting next time
                        $cs->reward_processed = 1;
                        $cs->save();
                    }
                }
            }
        });

        return redirect()->back()->with('success', 'Total earned points updated successfully!');
    }
    public function updateTotalExpirePointsRewar()
    {
        ini_set('max_execution_time', 0);

        $rew = Reward::first();
        $today = Carbon::today(); // Carbon instance
        $expiryThreshold = $today->copy()->subDays($rew->expiry_days); // for filtering customers

        Customer::select('id', 'date_time', 'vehicle_no', 'mobile_no', 'amount', 'type')
            ->chunk(500, function ($customers) use ($rew, $today, $expiryThreshold) {

                foreach ($customers as $customer) {
                    if (isset($customer->reward_expired_processed) && $customer->reward_expired_processed) {
                        continue;
                    }
                    if (!empty($customer->date_time) && $this->isValidDate($customer->date_time, 'd-m-Y H:i:s')) {

                        // Customer purchase date as Carbon
                        $customerDate = Carbon::createFromFormat('d-m-Y H:i:s', $customer->date_time)->startOfDay();

                        // Only process rewards older than expiry_days
                        if ($customerDate->lessThanOrEqualTo($expiryThreshold)) {

                            // Calculate the actual reward expired date
                            $rewardExpiredDate = $customerDate->copy()->addDays($rew->expiry_days)->format('Y-m-d');

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
                                $rewardmanag->total_expired_points += $reward_points;
                                $rewardmanag->save();
                            }
                            $customer->reward_expired_processed = 1;
                            $customer->save();
                        }
                    }
                }
            });
        return redirect()->back()->with('success', 'Expired rewards updated successfully!');
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
                                $rewardmanag->earned_reward_points = $rewardmanag->earned_reward_points - $reward_points;
                                if ($rewardmanag->earned_reward_points < 0) {
                                    $rewardmanag->earned_reward_points = 0;
                                }
                                $rewardmanag->pending_reward_points = $rewardmanag->earned_reward_points - $rewardmanag->used_reward_points;
                                if ($rewardmanag->pending_reward_points < 0) {
                                    $rewardmanag->pending_reward_points = 0;
                                }
                                $rewardmanag->total_expired_points += $reward_points;
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