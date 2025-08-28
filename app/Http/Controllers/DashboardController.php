<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\GiftHistory;
use App\Models\RewardManagement;
use App\Models\User;
use App\Models\Reward;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']); // Ensure the user is authenticated
    }

    // Admin Dashboard
    public function showdashboard()
    {
        if (auth()->user()->hasRole('admin')) {
            $totalUsedPoints = RewardManagement::sum('used_reward_points');
            $totalCustomers = Customer::count();
            $totalGiftPoints = GiftHistory::sum('used_reward_points');
            $totalUsers = User::whereDoesntHave('roles', function ($query) {
                $query->where('name', 'Admin');
            })->count();
            return view('admin-dashboard', compact('totalUsedPoints','totalCustomers','totalGiftPoints','totalUsers'));
        }else{
            return view('employee-dashboard');
        }
    }
    public function sendWhatsApp(Request $request){
        $ids = $request->input('ids');
        if (empty($ids)) {
            return response()->json(['message' => 'No IDs provided'], 400);
        }
        
        $apiUrl = "https://api.nextel.io/API_V2/Whatsapp/send_template/djgwTjVkdytiSnRQZzM2T2orejdQdz09";
        foreach ($ids as $id) {
            $rewards = RewardManagement::findOrFail($id);
            $customer = Customer::where(function ($query) use ($rewards) {
                if (!empty($rewards->vehicle_no)) {
                    $query->where('vehicle_no', $rewards->vehicle_no);
                }
                if (!empty($rewards->mobile_no)) {
                    $query->where('mobile_no', $rewards->mobile_no); // Changed to where()
                }
                if (!empty($rewards->type)) {
                    $query->where('type', $rewards->type); // Changed to where()
                }
            })->latest('updated_at')->first();
            $rew = Reward::first();
            if ($customer->type == 'Regular' && $customer->amount >= $rew->regular_reward_price) {
                $reward_points = ($customer->amount / $rew->regular_reward_price) * $rew->regular_reward_points;  
            }else if($customer->type == 'Commercial' && $customer->amount >= $rew->commercial_reward_price){
                $reward_points = ($customer->amount / $rew->commercial_reward_price) * $rew->commercial_reward_points;  
            }else if($customer->type == 'Tractor' && $customer->amount >= $rew->tractor_reward_price){
                $reward_points = ($customer->amount / $rew->tractor_reward_price) * $rew->tractor_reward_points;  
            }else{
                $reward_points = 0;
            }   
            $gifts = RewardManagement::select('mobile_no', 'vehicle_no', 'type')
                ->selectRaw('SUM(total_amount) as total_amount')
                ->when(!empty($rewards->vehicle_no), function ($query) use ($rewards) {
                    return $query->where('vehicle_no', $rewards->vehicle_no);
                })
                ->when(!empty($rewards->mobile_no), function ($query) use ($rewards) {
                    return $query->where('mobile_no', $rewards->mobile_no);
                })
                ->when(!empty($rewards->type), function ($query) use ($rewards) {
                    return $query->where('type', $rewards->type);
                })
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
                $usedPoints = GiftHistory::where(function ($query) use ($gift) {
                    // Handle mobile_no
                    $query->where(function ($q) use ($gift) {
                        if (isset($gift->mobile_no) && trim($gift->mobile_no) !== '') {
                            $q->where('mobile_no', $gift->mobile_no);
                        } else {
                            $q->whereNull('mobile_no')->orWhere('mobile_no', '');
                        }
                    });
                    $query->where(function ($q) use ($gift) {
                        if (!empty($gift->vehicle_no)) {
                            $q->where('vehicle_no', $gift->vehicle_no);
                        } else {
                            $q->whereNull('vehicle_no')->orWhere('vehicle_no', '');
                        }
                    });
                    $query->where(function ($q) use ($gift) {
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
                    "petrol/diesel", $id, $customer->date_time,$customer->amount,$reward_points, $gift->earned_points, $rewards->pending_reward_points, $gift->final_points
                ],
                "sender_phone" => $senderPhone
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
        }
        return response()->json(['status' => 'success','message' => 'WhatsApp messages sent successfully!', 'ids' => $ids]);
    }
}
