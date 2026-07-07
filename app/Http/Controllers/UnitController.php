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
        return view('pages.units.index');
    }

    /**
     * Get units data for DataTables.
     */
    public function getUnits()
    {
        try {
            $units = Unit::select(['id', 'name', 'abbreviation'])->get();

            $data = $units->map(function ($unit, $index) {
                // return [
                //     'id' => $index + 1,
                //     'name' => $unit->name,
                //     'abbreviation' => $unit->abbreviation,
                //     'actions' => '<div class="dropdown">
                //         <button class="btn btn-link font-size-16 shadow-none py-0 text-muted dropdown-toggle" type="button"
                //           data-bs-toggle="dropdown" aria-expanded="false">
                //           <i class="bx bx-dots-horizontal-rounded"></i>
                //         </button>
                //         <ul class="dropdown-menu dropdown-menu-end">
                //           <li><a class="dropdown-item edit-unit" href="#" data-id="' . $unit->id . '">Edit</a></li>
                //           <li><a class="dropdown-item delete-unit" href="#" data-id="' . $unit->id . '">Delete</a></li>
                //         </ul>
                //       </div>'
                // ];
                return [
                    'id' => $index + 1,
                    'name' => $unit->name,
                    'abbreviation' => $unit->abbreviation,
                    'actions' => '
                    <button class="btn btn-sm btn-primary edit-unit" href="#" data-id="' . $unit->id . '">Edit</button> 
                    <button class="btn btn-sm btn-danger delete-unit" href="#" data-id="' . $unit->id . '">Delete</button>
                    ',
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
        return response()->json([
            'data' => [
                'id' => $unit->id,
                'name' => $unit->name,
                'abbreviation' => $unit->abbreviation,
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit $unit)
    {
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
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit)
    {
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
    }
}
