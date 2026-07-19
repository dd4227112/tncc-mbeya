<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Throwable;

class UserManagementController extends Controller
{
    public string $role;

    public function __construct(Request $request)
    {
        $segment = $request->segment(1);

        $this->role = match ($segment) {
            'members' => 'members',
            'staffs' => 'staffs',
            default => abort(400, 'Invalid role'),
        };
        User::setManagementRole($this->role);
    }

    public function index(Request $request)
    {
        $this->data['role'] = $this->role;
        $this->data['roles'] = Role::where('name', '!=', 'Member')->get();

        return view('pages.users.index', $this->data);
    }

    public function getUsers()
    {
        try {
            $users = User::with('roles')->select(['id', 'first_name', 'last_name', 'phone', 'email', 'address'])->latest()->get();
            $data = $users->map(function ($user, $index) {
                return [
                    'id' => $index + 1,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'address' => $user->address,
                    'roles' => $user->roles->pluck('name')->implode(', '),
                    'actions' => '
                        <button class="btn btn-sm btn-primary edit-user" href="#" data-id="' . $user->id . '">Edit</button>
                        <button class="btn btn-sm btn-danger delete-user" href="#" data-id="' . $user->id . '">Delete</button>',
                ];
            })->values();

            return response()->json([
                'draw' => 1,
                'recordsTotal' => $users->count(),
                'recordsFiltered' => $users->count(),
                'data' => $data,
            ]);
        } catch (Throwable $e) {
            Log::error('Error fetching users: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'message' => 'Failed to get users. Please try again later or contact support.',
                'data' => [],
            ], 500);
        }
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'address' => ['nullable', 'string', 'max:255'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
        ]);

        try {
            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'phone' => $validated['phone'],
                'email' => $validated['email'] ?? null,
                'password' => $validated['phone'], // Set phone number as password
                'address' => $validated['address'] ?? null,
            ]);

            $user->roles()->sync([$validated['role_id']]);

            return response()->json([
                'message' => 'User created successfully.',
                'data' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'address' => $user->address,
                    'role' => $user->roles->pluck('name')->first() ?? null,
                ],
            ], 201);
        } catch (Throwable $e) {
            Log::error('Error creating user: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'message' => 'Unable to create user. Please try again.',
            ], 500);
        }
    }

    public function show(string $id)
    {
        //
    }

    public function edit(int $id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json([
                    'message' => 'User not found.',
                ], 404);
            }
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Unable to find user. Please try again.',
            ], 500);
        }

        return response()->json([
            'data' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'phone' => $user->phone,
                'email' => $user->email,
                'address' => $user->address,
                'role_id' => $user->roles->pluck('id')->first() ?? null,
                'role' => $user->roles->pluck('name')->first() ?? null,
            ],
        ]);
    }

    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', Rule::unique('users', 'phone')->ignore($id)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($id)],
            'address' => ['nullable', 'string', 'max:255'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
        ]);

        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json([
                    'message' => 'User not found.',
                ], 404);
            }
            $data = [
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'phone' => $validated['phone'],
                'email' => $validated['email'] ?? null,
                'address' => $validated['address'] ?? null,
            ];
            $user->update($data);
            $user->roles()->sync([$validated['role_id']]);

            return response()->json([
                'message' => 'User updated successfully.',
                'data' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'address' => $user->address,
                    'role_id' => $validated['role_id'],
                ],
            ]);
        } catch (Throwable $e) {
            Log::error('Error updating user: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'message' => 'Unable to update user. Please try again.',
            ], 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json([
                    'message' => 'User not found.',
                ], 404);
            }
            $user->delete();

            return response()->json([
                'message' => 'User deleted successfully.',
            ]);
        } catch (Throwable $e) {
            Log::error('Error deleting user: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'message' => 'Unable to delete user. Please try again.',
            ], 500);
        }
    }
    // search member by name or phone number
    public function searchMember(string $query)
    {
        try {
            $users = User::where('first_name', 'ilike', "%$query%")
                ->orWhere('last_name', 'ilike', "%$query%")
                ->orWhere('phone', 'ilike', "%$query%")
                ->orWhere('email', 'ilike', "%$query%")
                ->get();
            if ($users->isEmpty()) {
                return response()->json([
                    'message' => 'No members found matching the query.',
                    'data' => [],
                ]);
            }
            $data = $users->map(function ($user, $index) {
                return [
                    'id' => $user->id,
                    'name' => $user->first_name . ' ' . $user->last_name,
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'address' => $user->address,
                    'roles' => $user->roles->pluck('name')->implode(', '),
                ];
            })->values();
            return response()->json([
                'message' => 'Members found successfully.',
                'data' => $data,
            ]);
        } catch (Throwable $e) {
            Log::error('Error searching members: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'message' => 'Unable to search members. Please try again.',
                'data' => [],
            ], 500);
        }
    }
}
