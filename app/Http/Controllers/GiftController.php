<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\GiftHistory;
use App\Models\Reward;
use App\Models\RewardManagement;
use App\Models\Service;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class GiftController extends Controller
{
    public function gift_list()
    {
        $customers = Customer::select('mobile_no', 'vehicle_no', 'type')
            ->selectRaw('SUM(amount) as total_amount')
            ->groupBy('mobile_no', 'vehicle_no', 'type')
            ->get();

        $reward = Reward::first();
        foreach ($customers as $customer) {
            $totalAmount = $customer->total_amount;
            $customerType = $customer->type;
            $customer->earned_points = 0;
            if ($reward) {
                if ($customerType === 'Regular' && $totalAmount >= $reward->regular_price) {
                    $customer->earned_points = floor($totalAmount / $reward->regular_price) * $reward->regular_gift_point;
                } elseif ($customerType === 'Commercial' && $totalAmount >= $reward->commercial_price) {
                    $customer->earned_points = floor($totalAmount / $reward->commercial_price) * $reward->commercial_gift_point;
                } elseif ($customerType === 'Tractor' && $totalAmount >= $reward->tractor_price) {
                    $customer->earned_points = floor($totalAmount / $reward->tractor_price) * $reward->tractor_gift_point;
                }
            }
            $usedPoints = GiftHistory::where(function ($query) use ($customer) {
                // Handle mobile_no
                $query->where(function ($q) use ($customer) {
                    if (isset($customer->mobile_no) && trim($customer->mobile_no) !== '') {
                        $q->where('mobile_no', $customer->mobile_no);
                    } else {
                        $q->whereNull('mobile_no')->orWhere('mobile_no', '');
                    }
                });

                // Handle vehicle_no
                $query->where(function ($q) use ($customer) {
                    if (!empty($customer->vehicle_no)) {
                        $q->where('vehicle_no', $customer->vehicle_no);
                    } else {
                        $q->whereNull('vehicle_no')->orWhere('vehicle_no', '');
                    }
                });

                // Handle type
                $query->where(function ($q) use ($customer) {
                    if (!empty($customer->type)) {
                        $q->where('type', $customer->type);
                    } else {
                        $q->whereNull('type')->orWhere('type', '');
                    }
                });
            })->sum('used_reward_points');

            $customer->used_points = ($usedPoints);
            $customer->pending_points = max(0, $customer->earned_points - $usedPoints);
        }
        return view('gift.gift-list', compact('customers'));
    }
    public function showgift()
    {
        $services = Service::all();
        return view('gift.create', compact('services'));
    }
    public function redeem_gift_check(Request $request)
    {
        if (!$request->vehicle_no && !$request->mobile_no) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please enter either Vehicle No or Mobile No.'
            ], 400);
        }
        $customer = Customer::where(function ($query) use ($request) {
            if (!empty($request->vehicle_no)) {
                $query->where('vehicle_no', $request->vehicle_no);
            }
            if (!empty($request->mobile_no)) {
                $query->orWhere('mobile_no', $request->mobile_no);
            }
            if (!empty($request->type)) {
                $query->orWhere('type', $request->type);
            }
        })->where('status', 1)->first();


        if (!$customer) {
            return response()->json([
                'status' => 'error',
                'message' => 'No active customer found with the provided details.'
            ], 404);
        }
        $gifts = RewardManagement::select('mobile_no', 'vehicle_no', 'type')
            ->selectRaw('SUM(total_amount) as total_amount')
            ->when(!empty($request->vehicle_no), function ($query) use ($request) {
                return $query->where('vehicle_no', $request->vehicle_no);
            })
            ->when(!empty($request->mobile_no), function ($query) use ($request) {
                return $query->where('mobile_no', $request->mobile_no);
            })
            ->when(!empty($request->type), function ($query) use ($request) {
                return $query->where('type', $request->type);
            })
            ->groupBy('mobile_no', 'vehicle_no', 'type')
            ->get();

        $reward = Reward::first();

        foreach ($gifts as $gift) {
            $totalAmount = $gift->total_amount;
            $customerType = $gift->type;
            $gift->earned_points = 0; // Default to 0

            // Calculate earned points based on type
            if ($reward) {
                if ($customerType === 'Regular' && $totalAmount >= $reward->regular_price) {
                    $gift->earned_points = floor($totalAmount / $reward->regular_price) * $reward->regular_gift_point;
                } elseif ($customerType === 'Commercial' && $totalAmount >= $reward->commercial_price) {
                    $gift->earned_points = floor($totalAmount / $reward->commercial_price) * $reward->commercial_gift_point;
                } elseif ($customerType === 'Tractor' && $totalAmount >= $reward->tractor_price) {
                    $gift->earned_points = floor($totalAmount / $reward->tractor_price) * $reward->tractor_gift_point;
                }
            }

            // Get total used reward points from GiftHistory
            $usedPoints = GiftHistory::where(function ($query) use ($gift) {
                // Handle mobile_no
                $query->where(function ($q) use ($gift) {
                    if (isset($gift->mobile_no) && trim($gift->mobile_no) !== '') {
                        $q->where('mobile_no', $gift->mobile_no);
                    } else {
                        $q->whereNull('mobile_no')->orWhere('mobile_no', '');
                    }
                });
            
                // Handle vehicle_no
                $query->where(function ($q) use ($gift) {
                    if (!empty($gift->vehicle_no)) {
                        $q->where('vehicle_no', $gift->vehicle_no);
                    } else {
                        $q->whereNull('vehicle_no')->orWhere('vehicle_no', '');
                    }
                });
            
                // Handle type
                $query->where(function ($q) use ($gift) {
                    if (!empty($gift->type)) {
                        $q->where('type', $gift->type);
                    } else {
                        $q->whereNull('type')->orWhere('type', '');
                    }
                });
            })->sum('used_reward_points');
            

            // Final available points after deduction
            $gift->final_points = max(0, $gift->earned_points - $usedPoints);
        }
        $gifthistory = GiftHistory::where(function ($query) use ($request) {
            if (!empty($request->vehicle_no)) {
                $query->where('vehicle_no', $request->vehicle_no);
            }
            if (!empty($request->mobile_no)) {
                $query->where('mobile_no', $request->mobile_no); // Changed to where()
            }
            if (!empty($request->type)) {
                $query->where('type', $request->type); // Changed to where()
            }
        })->first();  
        if ($gifts->isNotEmpty()) {
            $gift = $gifts->first(); // Get the first matching record

            return response()->json([
                'status' => 'found',
                'reward' => [
                    'mobile_no' => $gift->mobile_no,
                    'vehicle_no' => $gift->vehicle_no,
                    'type' => $gift->type,
                    'total_amount' => $gift->total_amount,
                    'earned_points' => $gift->earned_points,
                    'used_points' => $usedPoints,
                    'final_points' => $gift->final_points,
                ],
                'gifthistory' => $gifthistory ?? null,
            ], 200);
        } else {
            return response()->json([
                'status' => 'not found',
                'message' => 'No reward data found for the provided details.'
            ], 404);
        }
    }
    public function redeem_gift_update(Request $request)
    {
        $request->validate([
            'available_reward_points' => 'required|numeric|min:1',
            'used_reward_points' => 'required|numeric|min:1|lte:available_reward_points',
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'village_city' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'state' => 'required|string|max:255',
        ]);

        try {
            $user = Auth::user();  
            $redeemHistory = GiftHistory::create([
                'name' => $request->name,
                'address' => $request->address,
                'village_city' => $request->village_city,
                'district' => $request->district,
                'state' => $request->state,
                'type' => $request->type,
                'mobile_no' => $request->mobile_no,
                'vehicle_no' => $request->vehicle_no,
                'used_reward_points' => $request->used_reward_points,
                'employee' => $user->id,
            ]);
            // $mobile = $request->mobile_no;
            // return $this->autosendWhatsApp($mobile);
            return response()->json([
                'status' => 'success',
                'message' => 'Gift points updated successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong! Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function redeem_gift_history()
    {
        $giftredeemHistory = GiftHistory::with('emp')->orderBy('id', 'desc')->get();
        return view('gift.gift-history', compact('giftredeemHistory'));
    }

    public function autosendWhatsApp($mobile) {
        if (empty($mobile)) {
            return response()->json(['message' => 'No Mobile provided'], 400);
        }
    
        $apiUrl = "https://api.nextel.io/API_V2/Whatsapp/send_template/djgwTjVkdytiSnRQZzM2T2orejdQdz09";
        
        // Fetch RewardManagement details
        $rewards = RewardManagement::where('mobile_no', $mobile)->first();
        if (!$rewards) {
            return response()->json(['message' => 'No rewards found for this mobile number'], 404);
        }
    
        // Fetch Customer details
        $customer = Customer::where(function ($query) use ($rewards) {
            if (!empty($rewards->vehicle_no)) {
                $query->where('vehicle_no', $rewards->vehicle_no);
            }
            if (!empty($rewards->mobile_no)) {
                $query->where('mobile_no', $rewards->mobile_no);
            }
            if (!empty($rewards->type)) {
                $query->where('type', $rewards->type);
            }
        })->latest('updated_at')->first();
    
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }
    
        // Get reward details
        $rew = Reward::first();
        $amount = $customer->amount ?? 0;
        $reward_price = $rew->price ?? 0;
        
        $reward_points = ($amount > $reward_price) ? 
            ($amount / $reward_price) * ($rew->reward_points ?? 0) : 
            0;
    
        // Calculate gift points
        $gifts = RewardManagement::select('mobile_no', 'vehicle_no', 'type')
            ->selectRaw('SUM(total_amount) as total_amount')
            ->when(!empty($rewards->vehicle_no), fn($query) => $query->where('vehicle_no', $rewards->vehicle_no))
            ->when(!empty($rewards->mobile_no), fn($query) => $query->where('mobile_no', $rewards->mobile_no))
            ->when(!empty($rewards->type), fn($query) => $query->where('type', $rewards->type))
            ->groupBy('mobile_no', 'vehicle_no', 'type')
            ->get();
    
        $reward = Reward::first();
        foreach ($gifts as $gift) {
            $totalAmount = $gift->total_amount;
            $customerType = $gift->type;
            $gift->earned_points = 0;
    
            if ($reward) {
                if ($customerType === 'Regular' && $totalAmount >= $reward->regular_price) {
                    $gift->earned_points = floor($totalAmount / $reward->regular_price) * $reward->regular_gift_point;
                } elseif ($customerType === 'Commercial' && $totalAmount >= $reward->commercial_price) {
                    $gift->earned_points = floor($totalAmount / $reward->commercial_price) * $reward->commercial_gift_point;
                } elseif ($customerType === 'Tractor' && $totalAmount >= $reward->tractor_price) {
                    $gift->earned_points = floor($totalAmount / $reward->tractor_price) * $reward->tractor_gift_point;
                }
            }
    
            // Calculate used reward points
            $usedPoints = GiftHistory::where(function ($query) use ($gift) {
                $query->where(function ($q) use ($gift) {
                    if (!empty($gift->mobile_no)) {
                        $q->where('mobile_no', $gift->mobile_no);
                    } else {
                        $q->whereNull('mobile_no')->orWhere('mobile_no', '');
                    }
                })
                ->where(function ($q) use ($gift) {
                    if (!empty($gift->vehicle_no)) {
                        $q->where('vehicle_no', $gift->vehicle_no);
                    } else {
                        $q->whereNull('vehicle_no')->orWhere('vehicle_no', '');
                    }
                })
                ->where(function ($q) use ($gift) {
                    if (!empty($gift->type)) {
                        $q->where('type', $gift->type);
                    } else {
                        $q->whereNull('type')->orWhere('type', '');
                    }
                });
            })->sum('used_reward_points');
    
            $gift->final_points = max(0, $gift->earned_points - $usedPoints);
        }
    
        $senderPhone = $rewards->mobile_no;
        $payload = [
            "type" => "template",
            "templateId" => "notification_new_msg_2",
            "templateLanguage" => "hi",
            "templateArgs" => [
                "petrol/diesel", $rewards->id, $customer->date_time, $amount, $reward_points, 
                $gift->earned_points ?? 0, $rewards->pending_reward_points, 
                $gift->final_points ?? 0
            ],
            "sender_phone" => $senderPhone
        ];
    
        // Retry mechanism with logging
        $maxRetries = 3;
        $retryDelay = 5; // seconds
    
        for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
            try {
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json'
                ])->timeout(60)->post($apiUrl, $payload);
    
                if ($response->successful()) {
                    return response()->json(['status' => 'success', 'message' => 'WhatsApp message sent successfully!', 'mobile' => $mobile]);
                }
    
                Log::error("WhatsApp API failed (Attempt $attempt): " . $response->body());
    
            } catch (\Exception $e) {
                Log::error("WhatsApp API error (Attempt $attempt): " . $e->getMessage());
                sleep($retryDelay);
            }
        }
        return response()->json(['message' => 'WhatsApp message failed after multiple attempts'], 500);
    }
}
