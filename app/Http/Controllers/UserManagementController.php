<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Closure;
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
        if (hasPermission('users.view')) {
            $this->data['role'] = $this->role;
            $allRoles = Role::latest()->get();
            $this->data['roles'] = $allRoles->where('name', '!=', 'Member');
            $this->data['all_roles'] = $allRoles;
            return view('pages.users.index', $this->data);
        } else {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to access this page.');
        }
    }

    public function getUsers()
    {
        try {
            $users = User::with('roles')->select(['id', 'first_name', 'last_name', 'phone', 'email', 'address'])->latest()->get();
            $data = $users->map(function ($user, $index) {
                $editUser = hasPermission('users.view') ? ('<li><a class="dropdown-item text-primary edit-user" type="button" data-id="' . $user->id . '"><i class="bx bx-show me-2"></i>Edit</a></li>') : '';
                $deleteUser = hasPermission('users.delete') ?  ('<li><a class="dropdown-item text-danger delete-user" type="button" data-id="' . $user->id . '"><i class="bx bx-trash me-2"></i>Delete</a></li>') : '';
                $changeRole = hasPermission('settings.update') ? ('<li><a class="dropdown-item text-secondary update-role" type="button" data-id="' . $user->id . '"><i class="bx bx-cog me-2"></i>Change Role</a></li>') : '';

                return [
                    'id' => $index + 1,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'address' => $user->address,
                    'roles' => $user->roles->pluck('name')->implode(', '),
                    'actions' =>
                    '<div class="dropdown"><button class="btn btn-link font-size-16 shadow-none py-0 text-muted dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bx bx-dots-horizontal-rounded"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">'
                        . $editUser
                        . $deleteUser
                        . $changeRole .
                        '</ul>
                    </div>',
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

        if (hasPermission('users.create')) {
            $validated = $request->validate([
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'phone' => [
                    'required',
                    'string',
                    'size:13',
                    'unique:users,phone',
                   function (string $attribute, mixed $value, Closure $fail) {
                    if (! isValidPhone($value)) {
                        $fail('The :attribute must be a valid Tanzanian phone number.');
                    }
                },
                ],
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
        } else {
            return response()->json([
                'message' => 'You do not have permission to create users.',
            ], 403);
        }
    }

    public function show(string $id)
    {
        //
    }

    public function edit(int $id)
    {
        if (hasPermission('users.update')) {
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
        } else {
            return response()->json([
                'message' => 'You do not have permission to edit users.',
            ], 403);
        }
    }

    public function update(Request $request, int $id)
    {
        if (hasPermission('users.update')) {
            $validated = $request->validate([
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'phone' => [
                    'required',
                    'string',
                    'size:13',
                    Rule::unique('users', 'phone')->ignore($id),
                   function (string $attribute, mixed $value, Closure $fail) {
                    if (! isValidPhone($value)) {
                        $fail('The :attribute must be a valid Tanzanian phone number.');
                    }
                },
                ],
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
        } else {
            return response()->json([
                'message' => 'You do not have permission to edit users.',
            ], 403);
        }
    }

    public function destroy(int $id)
    {
        if (hasPermission('users.delete')) {
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
        } else {
            return response()->json([
                'message' => 'You do not have permission to delete users.',
            ], 403);
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
