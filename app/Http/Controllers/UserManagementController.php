<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;
use Illuminate\Validation\Rule;

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
    /**
     * Display a listing of the resource.
     */


    public function index(Request $request)
    {
        $this->data['role'] = $this->role;
        return view('pages.users.index', $this->data);
    }   
     public function getUsers()
    {

        try {
            $users = User::select(['id', 'first_name', 'last_name', 'phone', 'email', 'address'])->get();
     

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
                    <button class="btn btn-sm btn-danger delete-user" href="#" data-id="' . $user->id . '">Delete</button>
                    ',
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('users')],
            'description' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:1'],
            'unit_id' => ['required', 'exists:units,id'],
        ]);

        try {
            $user = User::create($validated);
        } catch (Throwable $e) {
            Log::error('Error creating user: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'message' => 'Unable to create user. Please try again.',
            ], 500);
        }

        return response()->json([
            'message' => 'User created successfully.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'description' => $user->description,
                'price' => $user->price,
                'unit_id' => $user->unit_id,
            ],
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return response()->json([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'description' => $user->description,
                'price' => $user->price,
                'unit_id' => $user->unit_id,
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'description' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:1'],
            'unit_id' => ['required', 'exists:units,id'],
        ]);

        try {
            $user->update($validated);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Unable to update user. Please try again.',
            ], 500);
        }

        return response()->json([
            'message' => 'User updated successfully.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'description' => $user->description,
                'price' => $user->price,
                'unit_id' => $user->unit_id,
            ],
        ]);
    }

    /**     

     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            $user->delete();
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Unable to delete user. Please try again.',
            ], 500);
        }

        return response()->json([
            'message' => "User deleted successfully.",
        ]);
    }
}
