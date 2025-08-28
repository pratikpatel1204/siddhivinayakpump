<?php

namespace App\Http\Controllers;

use App\Models\Reward;
use App\Models\RewardManagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

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
}
