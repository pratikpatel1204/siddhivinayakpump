<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Reward;
use App\Models\RewardManagement;
use App\Models\GiftHistory;
use App\Models\RedeemHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Shuchkin\SimpleXLSX as ShuchkinSimpleXLSX;

class CustomerController extends Controller
{
    public function show_customer_list(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        if ($startDate && $endDate) {
            $customers = Customer::whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ])->get();
        } else {
            // Default to current month
            $startDate = Carbon::now()->startOfMonth()->toDateString();
            $endDate = Carbon::now()->endOfMonth()->toDateString();

            $customers = Customer::whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ])->get();
        }
        return view('customer-master.list', compact('customers', 'startDate', 'endDate'));
    }
    public function create_customer()
    {
        return view('customer-master.create');
    }
    public function store_customer(Request $request)
    {
        $request->validate([
            'customerFile' => 'required|mimes:csv,txt',
        ]);
    
        set_time_limit(300);
        ini_set('memory_limit', '512M');
    
        $file = $request->file('customerFile');
        $handle = fopen($file->getPathname(), 'r');
        $header = fgetcsv($handle);
    
        $whatsappIds = []; // Collect all reward IDs
    
        while (($row = fgetcsv($handle, 0, ",")) !== FALSE) {
            if (count($row) < 1) {
                continue;
            }
    
            $input = $row[11];
            preg_match('/^[A-Za-z]+/', $input, $matches);
            $alphabetPart = $matches[0] ?? '';
    
            $type = ($alphabetPart == 'C') ? 'Commercial' : (($alphabetPart == 'T') ? 'Tractor' : 'Regular');
    
            $existingCustomer = Customer::where('trx_id', $row[1])->first();
            if ($existingCustomer) {
                continue;
            }
    
            $cs = Customer::create([
                'trx_id' => $row[1] ?? null,
                'type' => $type,
                'date_time' => $row[2] ?? null,
                'pump' => $row[3] ?? null,
                'rdb_nozzle' => $row[4] ?? null,
                'product' => $row[5] ?? null,
                'unit_price' => $row[6] ?? null,
                'payment' => $row[7] ?? null,
                'volume' => $row[8] ?? null,
                'amount' => $row[9] ?? null,
                'print_id' => $row[10] ?? null,
                'vehicle_no' => $row[11] ?? null,
                'mobile_no' => $row[12] ?? null,
                'reward_processed' => 0,
                'reward_expired_processed' => 0
            ]);
    
            $rew = Reward::first();
            if ($type == 'Regular' && $row[9] >= $rew->regular_reward_price) {
                $reward_points = ($row[9] / $rew->regular_reward_price) * $rew->regular_reward_points;  
            }else if($type == 'Commercial' && $row[9] >= $rew->commercial_reward_price){
                $reward_points = ($row[9] / $rew->commercial_reward_price) * $rew->commercial_reward_points;  
            }else if($type == 'Tractor' && $row[9] >= $rew->tractor_reward_price){
                $reward_points = ($row[9] / $rew->tractor_reward_price) * $rew->tractor_reward_points;  
            }else{
                $reward_points = 0;
            }           
            $rewardmanag = RewardManagement::where('vehicle_no', $row[11])
                ->where('mobile_no', $row[12])
                ->where('type', $type)
                ->first();            
            if ($rewardmanag) {
                $rewardmanag->type = $type;
                $rewardmanag->total_amount += $row[9];
                $rewardmanag->earned_reward_points += $reward_points;
                $rewardmanag->pending_reward_points = $rewardmanag->earned_reward_points - $rewardmanag->used_reward_points;
                $rewardmanag->total_earned_points += $reward_points;
                $rewardmanag->save();
                $whatsappIds[] = $rewardmanag->id;
            } else {
                $newreward = RewardManagement::create([
                    'type' => $type,
                    'vehicle_no' => $row[11] ?? 'Not Entered',
                    'mobile_no' => $row[12] ?? 'Not Entered',
                    'total_amount' => $row[9],
                    'earned_reward_points' => $reward_points,
                    'used_reward_points' => 0,
                    'pending_reward_points' => $reward_points,
                    'total_earned_points' => $reward_points,
                ]);
                $whatsappIds[] = $newreward->id;
            }
        }
    
        fclose($handle);
    
        // Send WhatsApp messages for all processed IDs after the loop
        foreach ($whatsappIds as $id) {
            $this->autosendWhatsApp($id);
        }
    
        return response()->json([
            'success' => true,
            'message' => 'CSV Imported Successfully! WhatsApp messages sent!',
        ]);
    }

    public function add_customer()
    {
        return view('customer-master.add');
    }
    public function save_customer(Request $request)
    {
        $request->validate([
            'trx_id' => 'required|string|max:255|unique:customers,trx_id',
            'type' => 'required|in:Regular,Commercial,Tractor',
            'date_time' => 'required|date',
            'pump' => 'required|string|max:255',
            'rdb_nozzle' => 'required|string|max:255',
            'product' => 'required|string|max:255',
            'unit_price' => 'required|numeric|min:0',
            'payment' => 'required|string|max:255',
            'volume' => 'required|numeric|min:0',
            'amount' => 'required|numeric|min:0',
            'print_id' => 'required|string|max:255',
            'vehicle_no' => 'required|string|max:255',
            'mobile_no' => 'required|string|regex:/^[0-9]{10}$/',
        ]);

        // Save Customer Data
        Customer::create([
            'trx_id' => $request->trx_id,
            'type' => $request->type,
            'date_time' => $request->date_time,
            'pump' => $request->pump,
            'rdb_nozzle' => $request->rdb_nozzle,
            'product' => $request->product,
            'unit_price' => $request->unit_price,
            'payment' => $request->payment,
            'volume' => $request->volume,
            'amount' => $request->amount,
            'print_id' => $request->print_id,
            'vehicle_no' => $request->vehicle_no,
            'mobile_no' => $request->mobile_no,
            'reward_processed' => 0,
            'reward_expired_processed' => 0
        ]);

        // Get Reward Management Data
        $rewardmanag = RewardManagement::where('vehicle_no', $request->vehicle_no)
            ->where('mobile_no', $request->mobile_no)
            ->first();
        $rew = Reward::first();
        if ($request->type == 'Regular' && $request->amount >= $rew->regular_reward_price) {
            $reward_points = ($request->amount / $rew->regular_reward_price) * $rew->regular_reward_points;  
        }else if($request->type == 'Commercial' && $request->amount >= $rew->commercial_reward_price){
            $reward_points = ($request->amount / $rew->commercial_reward_price) * $rew->commercial_reward_points;  
        }else if($request->type == 'Tractor' && $request->amount >= $rew->tractor_reward_price){
            $reward_points = ($request->amount / $rew->tractor_reward_price) * $rew->tractor_reward_points;  
        }else{
            $reward_points = 0;
        }  
    
        // Update or Create Reward Management
        if ($rewardmanag) {
            $rewardmanag->type = $request->type ?? $rewardmanag->type;
            $rewardmanag->total_amount += $request->amount ?? 0;
            $rewardmanag->earned_reward_points += $reward_points ?? 0;
            $rewardmanag->pending_reward_points = $rewardmanag->earned_reward_points - $rewardmanag->used_reward_points;
            $rewardmanag->save();
        } else {
            $newreward = new RewardManagement();
            $newreward->type = $request->type;
            $newreward->vehicle_no = $request->vehicle_no ?? 'Not Entered';
            $newreward->mobile_no = $request->mobile_no ?? 'Not Entered';
            $newreward->total_amount = $request->amount ?? 0;
            $newreward->earned_reward_points = $reward_points ?? 0;
            $newreward->used_reward_points = 0;
            $newreward->pending_reward_points = $reward_points ?? 0;
            $newreward->save();
        }
        return redirect()->route('add.customer')->with('success', 'Data saved successfully!');
    }
    public function edit_customer($encryptedId)
    {
        try {
            $decrypted = Crypt::decryptString($encryptedId);
            $id = unserialize($decrypted); // Convert back to an integer
            $customer = Customer::findOrFail($id);
            $rewardmanag = RewardManagement::where('mobile_no', $customer->mobile_no)
                ->where('vehicle_no', $customer->vehicle_no)
                ->where('type', $customer->type)
                ->first();
            $redeemhistory = RedeemHistory::where('mobile_no', $customer->mobile_no)
                ->where('vehicle_no', $customer->vehicle_no)
                ->where('type', $customer->type)
                ->first();
            return view('customer-master.edit', compact('customer', 'rewardmanag', 'redeemhistory'));
        } catch (\Exception $e) {
            return abort(404);
        }
    }
    public function update_customer(Request $request)
    {
        $request->validate([
            // 'trx_id' => 'required|string|max:255',
            'type' => 'required|in:Regular,Commercial,Tractor',
            'date_time' => 'required|date',
            'pump' => 'required|integer',
            'rdb_nozzle' => 'required|integer',
            'product' => 'required|string|in:MS,HSD', // Assuming allowed values
            'unit_price' => 'required|numeric|min:0',
            'payment' => 'required|string|in:Cash,Card,UPI,Other', // Assuming payment methods
            'volume' => 'required|numeric|min:0',
            'amount' => 'required|numeric|min:0',
            'print_id' => 'required|integer',
            'vehicle_no' => 'required|string|max:255',
            'mobile_no' => 'required|digits:10',
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'village_city' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'state' => 'required|string|max:255',
        ]);
        $customer = Customer::where(function ($query) use ($request) {
            $query->where(function ($q) use ($request) {
                if (!empty($request->old_mobile_no)) {
                    $q->where('mobile_no', $request->old_mobile_no);
                } else {
                    $q->whereNull('mobile_no')->orWhere('mobile_no', '');
                }
            });
            $query->where(function ($q) use ($request) {
                if (!empty($request->old_vehicle_no)) {
                    $q->where('vehicle_no', $request->old_vehicle_no);
                } else {
                    $q->whereNull('vehicle_no')->orWhere('vehicle_no', '');
                }
            });
            $query->where(function ($q) use ($request) {
                if (!empty($request->old_type)) {
                    $q->where('type', $request->old_type);
                } else {
                    $q->whereNull('type')->orWhere('type', '');
                }
            });
        })->get();        
        foreach ($customer as $cust) {
            $cust->update([
                // 'trx_id' => $request->trx_id,
                'type' => $request->type,
                'date_time' => $request->date_time,
                'pump' => $request->pump,
                'rdb_nozzle' => $request->rdb_nozzle,
                'product' => $request->product,
                'unit_price' => $request->unit_price,
                'payment' => $request->payment,
                'volume' => $request->volume,
                'amount' => $request->amount,
                'print_id' => $request->print_id,
                'vehicle_no' => $request->vehicle_no,
                'mobile_no' => $request->mobile_no,
                'reward_processed' => 0,
                'reward_expired_processed' => 0
            ]);
        }     
        $rewardmanag = RewardManagement::where(function ($query) use ($request) {
            $query->where(function ($q) use ($request) {
                if (!empty($request->old_mobile_no)) {
                    $q->where('mobile_no', $request->old_mobile_no);
                } else {
                    $q->whereNull('mobile_no')->orWhere('mobile_no', '');
                }
            });
            $query->where(function ($q) use ($request) {
                if (!empty($request->old_vehicle_no)) {
                    $q->where('vehicle_no', $request->old_vehicle_no);
                } else {
                    $q->whereNull('vehicle_no')->orWhere('vehicle_no', '');
                }
            });
            $query->where(function ($q) use ($request) {
                if (!empty($request->old_type)) {
                    $q->where('type', $request->old_type);
                } else {
                    $q->whereNull('type')->orWhere('type', '');
                }
            });
        })->get();               
        foreach ($rewardmanag as $rewardman) {
            $rewardman->update([
                'mobile_no' => $request->mobile_no,
                'vehicle_no' => $request->vehicle_no,
                'type' => $request->type,
            ]);
        }
        $redeemhistory = RedeemHistory::where(function ($query) use ($request) {
            $query->where(function ($q) use ($request) {
                if (!empty($request->old_mobile_no)) {
                    $q->where('mobile_no', $request->old_mobile_no);
                } else {
                    $q->whereNull('mobile_no')->orWhere('mobile_no', '');
                }
            });
            $query->where(function ($q) use ($request) {
                if (!empty($request->old_vehicle_no)) {
                    $q->where('vehicle_no', $request->old_vehicle_no);
                } else {
                    $q->whereNull('vehicle_no')->orWhere('vehicle_no', '');
                }
            });
            $query->where(function ($q) use ($request) {
                if (!empty($request->old_type)) {
                    $q->where('type', $request->old_type);
                } else {
                    $q->whereNull('type')->orWhere('type', '');
                }
            });
        })->get();            
        foreach ($redeemhistory as $redeemhis) {
            $redeemhis->update([
                "name" => $request->name,
                "address" => $request->address,
                "village_city" => $request->village_city,
                "district" => $request->district,
                "state" => $request->state,
                'mobile_no' => $request->mobile_no,
                'vehicle_no' => $request->vehicle_no,
                'type' => $request->type,
            ]);
        }
        $gifthistory = GiftHistory::where(function ($query) use ($request) {
            $query->where(function ($q) use ($request) {
                if (!empty($request->old_mobile_no)) {
                    $q->where('mobile_no', $request->old_mobile_no);
                } else {
                    $q->whereNull('mobile_no')->orWhere('mobile_no', '');
                }
            });
            $query->where(function ($q) use ($request) {
                if (!empty($request->old_vehicle_no)) {
                    $q->where('vehicle_no', $request->old_vehicle_no);
                } else {
                    $q->whereNull('vehicle_no')->orWhere('vehicle_no', '');
                }
            });
            $query->where(function ($q) use ($request) {
                if (!empty($request->old_type)) {
                    $q->where('type', $request->old_type);
                } else {
                    $q->whereNull('type')->orWhere('type', '');
                }
            });
        })->get();            
        foreach ($gifthistory as $gifthist) {
            $gifthist->update([
                "name" => $request->name,
                "address" => $request->address,
                "village_city" => $request->village_city,
                "district" => $request->district,
                "state" => $request->state,
                'mobile_no' => $request->mobile_no,
                'vehicle_no' => $request->vehicle_no,
                'type' => $request->type,
            ]);
        }
        return redirect()->route('customer.list')->with('success', 'Data updated successfully!');
    }
    public function destroy_customer(Request $request)
    {
        $customer = Customer::find($request->id);
        if ($customer) {
            $customer->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Customer deleted successfully.',
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Customer not found.',
            ], 404);
        }
    }
    public function updateStatus(Request $request)
    {
        $customer = Customer::find($request->id);
        if ($customer) {
            $customer->status = $request->status;
            $customer->save();

            return response()->json(['success' => true, 'status' => $customer->status]);
        }
        return response()->json(['success' => false]);
    }
    public function autosendWhatsApp($ids){
        if (empty($ids)) {
            return response()->json(['message' => 'No IDs provided'], 400);
        }
        $apiUrl = "https://api.nextel.io/API_V2/Whatsapp/send_template/djgwTjVkdytiSnRQZzM2T2orejdQdz09";
        $rewards = RewardManagement::findOrFail($ids);
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
                "petrol/diesel", $ids, $customer->date_time,$customer->amount,$reward_points, $gift->earned_points, $rewards->pending_reward_points, $gift->final_points
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
        return response()->json(['status' => 'success','message' => 'WhatsApp messages sent successfully!', 'ids' => $ids]);
    }
}