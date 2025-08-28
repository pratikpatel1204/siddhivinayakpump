<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionsController extends Controller
{
    public function showemployeePermissions()
    {
        $roles = Role::where('name', 'employee')->get();
        $permissions = Permission::all();
        return view('permissions.employee', compact('roles', 'permissions'));       
    }
    public function updateemployeePermissions(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'array',
        ]);

        $role = Role::find($request->role_id);
        $role->syncPermissions($request->permissions);

        return redirect()->route('employee.permissions')->with('success', 'Permissions updated successfully.');
    }
}
