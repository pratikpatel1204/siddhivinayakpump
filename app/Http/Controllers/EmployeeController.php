<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Silber\Bouncer\BouncerFacade as Bouncer;

class EmployeeController extends Controller
{
    public function employee_list()
    {
        $this->authorize('view', User::class);
        $users = User::where('role', '!=', 'admin')->get();
        return view('employee.list', compact('users'));
    }
    public function add_employee()
    {
        return view('employee.add');
    }
    public function store_employee(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'mobile' => 'required|digits:10|unique:users,mobile',
            'profileImage' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        $imagePath = null;
        if ($request->hasFile('profileImage')) {
            $image = $request->file('profileImage');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('profile_images'), $imageName);
            $imagePath = 'profile_images/' . $imageName;
        }
        $employee = User::create([
            'role' => 'employee',
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'showpassword' => $request->input('password'),
            'mobile' => $request->input('mobile'),
            'profile_image' => $imagePath,
        ]);
        $employee->assignRole('employee');
        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully!',
            'user' => $employee
        ], 201);
    }
    public function edit_employee($encryptedId)
    {
        try {
            $id = Crypt::decryptString($encryptedId);
            $employee = User::findOrFail($id);

            return view('employee.edit', compact('employee'));
        } catch (\Exception $e) {
            return abort(404);
        }
    }
    public function update_employee(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'nullable|string|min:6|confirmed',
            'profileImage' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'mobile' => 'required|digits:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }
        $employee = User::find($request->id);

        if (!$employee) {
            return response()->json([
                'status' => 'error',
                'message' => 'Employee not found.',
            ], 404);
        }
        $employee->name = $request->input('name', $employee->name);
        $employee->email = $request->input('email', $employee->email);
        if ($request->filled('password')) {
            $employee->password = Hash::make($request->input('password'));
            $employee->showpassword = $request->input('password');
        }
        if ($request->hasFile('profileImage')) {
            if ($employee->profile_image && file_exists(public_path($employee->profile_image))) {
                unlink(public_path($employee->profile_image));
            }
            $image = $request->file('profileImage');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('profile_images'), $imageName);
            $employee->profile_image = 'profile_images/' . $imageName;
        }    
        $employee->mobile = $request->input('mobile');
        $employee->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Employee updated successfully!',
        ], 200);
    }

    public function destroy_employee(Request $request)
    {
        $employee = User::find($request->id);
        if ($employee) {
            if ($employee->profile_image && file_exists(public_path($employee->profile_image))) {
                unlink(public_path($employee->profile_image));
            }
            $employee->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Employee deleted successfully.',
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Employee not found.',
            ], 404);
        }
    }
}
