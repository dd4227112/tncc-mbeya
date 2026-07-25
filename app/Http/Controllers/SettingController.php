<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;
use Illuminate\Validation\Rule;

class SettingController extends Controller
{
    public function roles()
    {
        if (hasPermission('settings.view')) {
            $this->data['roles'] = Role::with('permissions')->latest()->get();
            $this->data['permissions'] = Permission::latest()->get();

            return view('pages.settings.roles', $this->data);
        } else {
            return response()->json([
                'message' => 'You do not have permission to view setting.',
                'data' => [],
            ], 403);
        }
    }
    public function togglePermission(Request $request)
    {
        $request->validate([
            'role_id'       => 'required|exists:roles,id',
            'permission_id' => 'required|exists:permissions,id',
        ]);

        $role = Role::findOrFail($request->role_id);

        $exists = $role->permissions()->where('permission_id', $request->permission_id)->exists();

        if ($exists) {
            $role->permissions()->detach($request->permission_id);
            $status = false;
        } else {
            $role->permissions()->attach($request->permission_id);
            $status = true;
        }

        return response()->json([
            'success' => true,
            'status'  => $status, // true = now has permission, false = removed
        ]);
    }
    public function create(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')],
            'description' => ['nullable', 'string', 'max:50'],
        ]);
        try {
            Role::create($validated);
            return response()->json([
                'message' => 'Role created successfully.',
            ], 201);
        } catch (Throwable $e) {
            Log::error('Error creating role: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'message' => 'Unable to create role. Please try again.',
            ], 500);
        }
    }
}
