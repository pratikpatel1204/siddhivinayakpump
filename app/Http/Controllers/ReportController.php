<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\GiftHistory;
use App\Models\RewardManagement;
use App\Models\DailyRewardExpire;
use App\Models\RedeemHistory;
use App\Models\Reward;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;


class ReportController extends Controller
{
    public function customer_report()
    {
        return view('report.customer-report');
    }
    public function search_customer_report(Request $request)
    {
        $request->validate([
            'number' => 'required|numeric',
            'rewarddate' => 'required|string',
        ]);
        $number = $request->number;
        $dateRange = $request->rewarddate;
        $dateParts = explode(' - ', $dateRange);
        if (count($dateParts) == 2) {
            $startDate = Carbon::parse($dateParts[0])->startOfDay();
            $endDate = Carbon::parse($dateParts[1])->endOfDay();
        } else {
            return response()->json(['error' => 'Invalid date range format.'], 400);
        }
        $customer = Customer::where('mobile_no', $number)
            ->whereRaw("STR_TO_DATE(date_time, '%d-%m-%Y %H:%i:%s') BETWEEN ? AND ?", [$startDate, $endDate])
            ->get();
        $rewardmanag = RewardManagement::where('mobile_no', $number)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $rewardhistory = RedeemHistory::where('mobile_no', $number)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $gifthistory = GiftHistory::where('mobile_no', $number)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        // Filter `gifts` by mobile_no and created_at
        $gifts = RewardManagement::where('mobile_no', $number)
            ->whereBetween('created_at', [$startDate, $endDate])
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

            // Filter used points by mobile_no, type, and date
            $usedPoints = GiftHistory::whereBetween('created_at', [$startDate, $endDate])
                ->where(function ($query) use ($gift) {
                    $query->where(function ($q) use ($gift) {
                        if (!empty($gift->mobile_no)) {
                            $q->where('mobile_no', $gift->mobile_no);
                        } else {
                            $q->whereNull('mobile_no')->orWhere('mobile_no', '');
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

            $gift->use_points = $usedPoints;
            $gift->pennding_points = max(0, $gift->earned_points - $usedPoints);
        }
        if ($rewardmanag->isEmpty() && $rewardhistory->isEmpty() && $gifthistory->isEmpty() && $customer->isEmpty()) {
            return response()->json([
                'message' => 'No data found for the specified mobile number and date range.',
            ], 404);
        }
        return response()->json([
            'message' => 'Data fetched successfully',
            'customer' => $customer,
            'reward_management' => $rewardmanag,
            'gifts' => $gifts,
            'redeem_history' => $rewardhistory,
            'gift_history' => $gifthistory,
        ]);
    }
    public function reward_report_list()
    {
        $rewardmanag = RewardManagement::all();
        foreach ($rewardmanag as $reward) {
            $rewardhistory = RedeemHistory::with('emp')->where(function ($query) use ($reward) {
                $query->where(function ($q) use ($reward) {
                    if (!empty($reward->mobile_no)) {
                        $q->where('mobile_no', $reward->mobile_no);
                    } else {
                        $q->whereNull('mobile_no')->orWhere('mobile_no', '');
                    }
                });
                $query->where(function ($q) use ($reward) {
                    if (!empty($reward->vehicle_no)) {
                        $q->where('vehicle_no', $reward->vehicle_no);
                    } else {
                        $q->whereNull('vehicle_no')->orWhere('vehicle_no', '');
                    }
                });
                $query->where(function ($q) use ($reward) {
                    if (!empty($reward->type)) {
                        $q->where('type', $reward->type);
                    } else {
                        $q->whereNull('type')->orWhere('type', '');
                    }
                });
            })->first();
            $reward->name = $rewardhistory->name ?? 'Not Entered';
            $reward->village_city = $rewardhistory->village_city ?? 'Not Entered';
            $reward->district = $rewardhistory->district ?? 'Not Entered';
        }
        return view('report.reward-report', compact('rewardmanag'));
    }
    public function filterRewardReport(Request $request)
    {
        $startDate = Carbon::parse($request->start_date)->startOfDay(); // 00:00:00
        $endDate = Carbon::parse($request->end_date)->endOfDay(); // 23:59:59

        $filteredData = RewardManagement::whereBetween('updated_at', [$startDate, $endDate])->get();


        foreach ($filteredData as $reward) {
            $rewardhistory = RedeemHistory::with('emp')
                ->where(function ($query) use ($reward) {
                    $query->where(function ($q) use ($reward) {
                        if (!empty($reward->mobile_no)) {
                            $q->where('mobile_no', $reward->mobile_no);
                        } else {
                            $q->whereNull('mobile_no')->orWhere('mobile_no', '');
                        }
                    });
                    $query->where(function ($q) use ($reward) {
                        if (!empty($reward->vehicle_no)) {
                            $q->where('vehicle_no', $reward->vehicle_no);
                        } else {
                            $q->whereNull('vehicle_no')->orWhere('vehicle_no', '');
                        }
                    });
                    $query->where(function ($q) use ($reward) {
                        if (!empty($reward->type)) {
                            $q->where('type', $reward->type);
                        } else {
                            $q->whereNull('type')->orWhere('type', '');
                        }
                    });
                })
                ->first();

            // Assign retrieved values or default to 'Not Entered'
            $reward->name = $rewardhistory->name ?? 'Not Entered';
            $reward->village_city = $rewardhistory->village_city ?? 'Not Entered';
            $reward->district = $rewardhistory->district ?? 'Not Entered';
        }

        return response()->json(['data' => $filteredData]);
    }
    public function gift_report_list()
    {
        // Get customers with grouped amounts
        $customers = Customer::select('mobile_no', 'vehicle_no', 'type', 'date_time')
            ->selectRaw('SUM(amount) as total_amount')
            ->groupBy('mobile_no', 'vehicle_no', 'type', 'date_time')
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
            $giftHistory = GiftHistory::where(function ($query) use ($customer) {
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
            })->first();
            $usedPoints = $giftHistory ? $giftHistory->used_reward_points : 0;
            $customer->used_points = $usedPoints;
            $customer->pending_points = max(0, $customer->earned_points - $usedPoints);
            $customer->name = $giftHistory->name ?? 'Not Entered';
            $customer->village_city = $giftHistory->village_city ?? 'Not Entered';
            $customer->district = $giftHistory->district ?? 'Not Entered';
        }
        return view('report.gift-report', compact('customers'));
    }
    public function all_report_list()
    {
        $employee = User::where('role', 'employee')->get();
        $redeemHistory = RedeemHistory::with('emp')->get();
        $giftHistory = GiftHistory::with('emp')->get();
        return view('report.all-report', compact('employee', 'giftHistory', 'redeemHistory'));
    }
    public function all_report_reward_filter(Request $request)
    {
        // Ensure start and end dates are parsed correctly
        $startDate = $request->startDate ? Carbon::parse($request->startDate)->startOfDay() : null;
        $endDate = $request->endDate ? Carbon::parse($request->endDate)->endOfDay() : null;
        $employeeId = $request->employee;

        // Start query with eager loading
        $query = RedeemHistory::with('emp');

        // Apply date range filter if both dates exist
        if ($startDate && $endDate) {
            $query->whereBetween('updated_at', [$startDate, $endDate]);
        }

        // Apply employee filter if ID is provided and not 0
        if (!empty($employeeId) && $employeeId != 0) {
            $query->where('employee', $employeeId); // Correct field name
        }

        // Fetch filtered data
        $filteredData = $query->get();
        return response()->json(['data' => $filteredData]);
    }
    public function all_report_gift_filter(Request $request)
    {
        $startDate = $request->startDate ? Carbon::parse($request->startDate)->startOfDay() : null;
        $endDate = $request->endDate ? Carbon::parse($request->endDate)->endOfDay() : null;
        $employeeId = $request->employee;

        // Start query with eager loading
        $query = GiftHistory::with('emp');

        // Apply date range filter if both dates exist
        if ($startDate && $endDate) {
            $query->whereBetween('updated_at', [$startDate, $endDate]);
        }

        // Apply employee filter if ID is provided and not 0
        if (!empty($employeeId) && $employeeId != 0) {
            $query->where('employee', $employeeId); // Correct field name
        }

        // Fetch filtered data
        $filteredData = $query->get();
        return response()->json(['data' => $filteredData]);
    }
    public function show_expired_reward_point_list()
    {
        $expiredRewards = DailyRewardExpire::with('customer')->get();
        return view('report.expired-reward-point-list', compact('expiredRewards'));
    }
}