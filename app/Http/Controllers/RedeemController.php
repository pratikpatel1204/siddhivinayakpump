<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\RedeemHistory;
use App\Models\RewardManagement;
use App\Models\Reward;
use App\Models\GiftHistory;
use App\Models\Service;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class RedeemController extends Controller
{
    public function showredeem()
    {
        $services = Service::all();
        return view('redeem.create', compact('services'));
    }
    public function redeem_check(Request $request)
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
        $rewardmanag = RewardManagement::where(function ($query) use ($request) {
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
        $redeemhistory = RedeemHistory::where(function ($query) use ($request) {
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
        if ($rewardmanag) {
            return response()->json([
                'status' => 'found',
                'Reward' => $rewardmanag,
                'redeemhistory' => $redeemhistory ?? null
            ], 200);
        } else {
            return response()->json([
                'status' => 'not found',
                'message' => 'No reward data found for the provided details.'
            ], 404);
        }
    }
    public function redeem_updateold(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:reward_management,id',
            'available_reward_points' => 'required|numeric|min:1',
            'used_reward_points' => 'required|numeric|min:1|lte:available_reward_points',
        ]);
        $rewards = RewardManagement::findOrFail($request->id);
        $existreward = $rewards->pending_reward_points - $request->used_reward_points;
        $rewards->used_reward_points += $request->used_reward_points;
        $rewards->pending_reward_points = $existreward;
        $rewards->updated_at = now();
        $rewards->save();
        return response()->json([
            'status' => 'success',
            'message' => 'Reward points updated successfully.',
        ]);
    }
    public function redeem_update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:reward_management,id',
            'available_reward_points' => 'required|numeric|min:1',
            'used_reward_points' => 'required|numeric|min:1|lte:available_reward_points',
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'village_city' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'service' => 'required',
        ]);

        try {
            $rewards = RewardManagement::findOrFail($request->id);
            $newPendingPoints = $rewards->pending_reward_points - $request->used_reward_points;
            if ($newPendingPoints < 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Not enough reward points available.',
                ], 400);
            }
            $rewards->used_reward_points += $request->used_reward_points;
            $rewards->pending_reward_points = $newPendingPoints;
            $rewards->updated_at = now();
            $rewards->save();
            $user = Auth::user();  
            $redeemHistory = RedeemHistory::create([
                'reward_management_id' => $request->id,
                'name' => $request->name,
                'address' => $request->address,
                'village_city' => $request->village_city,
                'district' => $request->district,
                'state' => $request->state,
                'service' => $request->service,
                'type' => $request->type,
                'mobile_no' => $request->mobile_no,
                'vehicle_no' => $request->vehicle_no,
                'used_reward_points' => $request->used_reward_points,
                'employee' => $user->id,
            ]);
            return $this->autosendWhatsApp(
                [
                    'use_point' => $request->used_reward_points,
                    'total_point' => $rewards->pending_reward_points,
                    'name' => $request->name,
                    'number' => $request->mobile_no,
                    'address' => $request->address,
                    'village_city' => $request->village_city,
                    'district' => $request->district,
                    'state' => $request->state,
                    'service' => $request->service,
                ]
            );
            return response()->json([
                'status' => 'success',
                'message' => 'Reward points updated successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong! Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function redeem_reward_history(){
        $redeemHistory = RedeemHistory::with('emp')->orderBy('id', 'desc')->get();
        return view('redeem.reward-history', compact('redeemHistory'));
    }
    public function autosendWhatsApp(array $data)
    { 
        $usedPoints = $data['use_point'];
        $totalPoints = $data['total_point'];
        $name = $data['name'];
        $address = $data['address'];
        $villageCity = $data['village_city'];
        $district = $data['district'];
        $state = $data['state'];
        $service = $data['service'];
        $number = $data['number'];
        $total = $usedPoints + $totalPoints;
        $apiUrl = "https://api.nextel.io/API_V2/Whatsapp/send_template/djgwTjVkdytiSnRQZzM2T2orejdQdz09";
        $payload = [
            "type" => "buttonTemplate",
            "templateId" => "washing_service_receipt",
            "templateLanguage" => "en",
            "templateArgs" => [
                $name, 'Petrol/Diesel', $total , $usedPoints , $totalPoints ,$address
            ],
            "sender_phone" => $number
        ];
        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post($apiUrl, $payload);
        if ($response->failed()) {
            return response()->json([
                'message' => 'Failed to send WhatsApp message',
                'error' => $response->body()
            ], 500);
        }
        return response()->json(['status' => 'success','message' => 'WhatsApp messages sent successfully!']);
    }
}
