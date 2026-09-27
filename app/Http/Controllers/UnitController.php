<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Throwable;
use Illuminate\Validation\Rule;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (hasPermission('units.view')) {
            return view('pages.units.index');
        } else {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to access this page.');
        }
    }

    /**
     * Get units data for DataTables.
     */
    public function getUnits()
    {
        try {
            $units = Unit::select(['id', 'name', 'abbreviation'])->get();

            $data = $units->map(function ($unit, $index) {
                return [
                    'id' => $index + 1,
                    'name' => $unit->name,
                    'abbreviation' => $unit->abbreviation,
                    'actions' => '<div class="d-flex flex-wrap gap-1">'
                        . (hasPermission('units.update') ? ('<button class="btn btn-sm btn-soft-primary edit-unit" type="button" data-id="' . $unit->id . '"><i class="bx bx-edit me-1"></i>Edit</button>') : '')
                        . (hasPermission('units.delete') ? ('<button class="btn btn-sm btn-soft-danger delete-unit" type="button" data-id="' . $unit->id . '"><i class="bx bx-trash me-1"></i>Delete</button>') : '')
                        . '</div>',
                ];
            })->values();

            return response()->json([
                'draw' => 1,
                'recordsTotal' => $units->count(),
                'recordsFiltered' => $units->count(),
                'data' => $data,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Failed to get units. Please try again later or contact support.',
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
        if (hasPermission('units.create')) {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255', Rule::unique('units')],
                'abbreviation' => ['required', 'string', 'max:50'],
            ]);
            try {
                $unit = Unit::create($validated);
            } catch (Throwable $e) {
                return response()->json([
                    'message' => 'Unable to create unit. Please try again.',
                ], 500);
            }

            return response()->json([
                'message' => 'Unit created successfully.',
                'data' => [
                    'id' => $unit->id,
                    'name' => $unit->name,
                    'abbreviation' => $unit->abbreviation,
                ],
            ], 201);
        } else {
            return response()->json([
                'message' => 'You do not have permission to create units.',
                'data' => [],
            ], 403);
        }
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
    public function edit(Unit $unit)
    {

        if (hasPermission('units.update')) {
            return response()->json([
                'data' => [
                    'id' => $unit->id,
                    'name' => $unit->name,
                    'abbreviation' => $unit->abbreviation,
                ],
            ]);
        } else {
            return response()->json([
                'message' => 'You do not have permission to view units.',
                'data' => [],
            ], 403);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit $unit)
    {
        if (hasPermission('units.update')) {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255', Rule::unique('units')->ignore($unit->id)],
                'abbreviation' => ['required', 'string', 'max:50'],
            ]);
            try {
                $unit->update($validated);
            } catch (Throwable $e) {
                return response()->json([
                    'message' => 'Unable to update unit. Please try again.',
                ], 500);
            }

            return response()->json([
                'message' => 'Unit updated successfully.',
                'data' => [
                    'id' => $unit->id,
                    'name' => $unit->name,
                    'abbreviation' => $unit->abbreviation,
                ],
            ]);
        } else {
            return response()->json([
                'message' => 'You do not have permission to update units.',
                'data' => [],
            ], 403);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit)
    {
        if (hasPermission('units.delete')) {
            try {
                $unit->delete();
            } catch (Throwable $e) {
                return response()->json([
                    'message' => 'Unable to delete unit. Please try again.',
                ], 500);
            }
            return response()->json([
                'message' => 'Unit deleted successfully.',
            ]);
        } else {
            return response()->json([
                'message' => 'You do not have permission to delete units.',
                'data' => [],
            ], 403);
        }
    }
}
