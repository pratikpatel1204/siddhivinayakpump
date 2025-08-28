<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class ServiceController extends Controller
{
    public function show_service_master()
    {
        $service = Service::all();
        return view('service-master.list', compact('service'));
    }
    public function create_service_master()
    {
        return view('service-master.create');
    }
    public function store_service_master(Request $request)
    {
        $request->validate([
            'price' => 'required|numeric',
            'service_name' => 'required|string|max:255',
        ]);

        try {
            $service = Service::create([
                'service_name' => $request->service_name,
                'price' => $request->price,
            ]);
            return response()->json([
                'message' => 'Service created successfully!',
                'service' => $service
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create service: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit_service_master($encryptedId)
    {
        try {
            $id = Crypt::decryptString($encryptedId);
            $service = Service::findOrFail($id);
            return view('service-master.edit', compact('service'));
        } catch (\Exception $e) {
            return abort(404);
        }
    }
    public function update_service_master(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:services,id', 
            'price' => 'required|numeric',
            'service_name' => 'required|string|max:255',
        ]);
        try {
            $service = Service::find($request->id);

            if (!$service) {
                return response()->json([
                    'message' => 'Service not found.'
                ], 404);
            }
            $service->update([
                'price' => $request->price,
                'service_name' => $request->service_name,
            ]);
            return response()->json([
                'message' => 'Service updated successfully!',
                'service' => $service
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update service: ' . $e->getMessage()
            ], 500);
        }
    }
    public function destroy_service_master(Request $request)
    {
        $service = Service::find($request->id);
        if ($service) {
            $service->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Service deleted successfully.',
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Service not found.',
            ], 404);
        }
    }
}
