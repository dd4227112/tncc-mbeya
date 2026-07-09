<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;
use Illuminate\Validation\Rule;

class CropsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $units = Unit::all();
        return view('pages.crops.index', compact('units'));
    }
    public function getCrops()
    {

        try {
            $crops = Crop::select(['id', 'name', 'price', 'description', 'unit_id'])->get();

            $data = $crops->map(function ($crop, $index) {
                return [
                    'id' => $index + 1,
                    'name' => $crop->name,
                    'description' => $crop->description,
                    'unit' => $crop->unit->name ?? 'N/A',
                    'price' => $crop->price,
                    'actions' => '
                    <button class="btn btn-sm btn-primary edit-crop" href="#" data-id="' . $crop->id . '">Edit</button> 
                    <button class="btn btn-sm btn-danger delete-crop" href="#" data-id="' . $crop->id . '">Delete</button>
                    ',
                ];
            })->values();

            return response()->json([
                'draw' => 1,
                'recordsTotal' => $crops->count(),
                'recordsFiltered' => $crops->count(),
                'data' => $data,
            ]);
        } catch (Throwable $e) {
            Log::error('Error fetching crops: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'message' => 'Failed to get crops. Please try again later or contact support.',
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
            'name' => ['required', 'string', 'max:255', Rule::unique('crops')],
            'description' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:1'],
            'unit_id' => ['required', 'exists:units,id'],
        ]);

        try {
            $crop = Crop::create($validated);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Unable to create crop. Please try again.',
            ], 500);
        }

        return response()->json([
            'message' => 'Crop created successfully.',
            'data' => [
                'id' => $crop->id,
                'name' => $crop->name,
                'description' => $crop->description,
                'price' => $crop->price,
                'unit_id' => $crop->unit_id,
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
    public function edit(Crop $crop)
    {
        return response()->json([
            'data' => [
                'id' => $crop->id,
                'name' => $crop->name,
                'description' => $crop->description,
                'price' => $crop->price,
                'unit_id' => $crop->unit_id,
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Crop $crop)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('crops')->ignore($crop->id)],
            'description' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:1'],
            'unit_id' => ['required', 'exists:units,id'],
        ]);

        try {
            $crop->update($validated);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Unable to update crop. Please try again.',
            ], 500);
        }

        return response()->json([
            'message' => 'Crop updated successfully.',
            'data' => [
                'id' => $crop->id,
                'name' => $crop->name,
                'description' => $crop->description,
                'price' => $crop->price,
                'unit_id' => $crop->unit_id,
            ],
        ]);
    }

    /**     

     * Remove the specified resource from storage.
     */
    public function destroy(Crop $crop)
    {
        try {
            $crop->delete();
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Unable to delete crop. Please try again.',
            ], 500);
        }

        return response()->json([
            'message' => 'Crop deleted successfully.',
        ]);
    }
    // search crop by name or description
    public function searchCrop(string $term)
    {
        try {
            $crops = Crop::where('name', 'ilike', '%' . $term . '%')
                ->orWhere('description', 'ilike', '%' . $term . '%')
                ->get();
            if ($crops->isEmpty()) {
                return response()->json([
                    'message' => 'No crops found.',
                    'data' => [],
                ]);
            }
            $data = $crops->map(function ($crop) {
                return [
                    'id' => $crop->id,
                    'name' => $crop->name,
                    'description' => $crop->description,
                    'unit' => $crop->unit->name ?? 'N/A',
                    'price' => $crop->price,
                ];
            })->values();

            return response()->json([
                'message' => 'Crop fetched successfully.',
                'data' => $data,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Unable to search crops. Please try again.',
            ], 500);
        }
    }
}
