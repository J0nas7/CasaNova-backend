<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PropertyController extends Controller
{
    /**
     * Display a listing of properties.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $properties = Property::with(['user', 'images', 'favorites'])->get(); 
        return response()->json($properties);
    }

    /**
     * Store a newly created property in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'User_ID' => 'required|integer',
            'Property_Title' => 'required|string|max:255',
            'Property_Description' => 'nullable|string',
            'Property_Price' => 'required|numeric',
            'Property_Location' => 'required|string|max:255',
        ]);

        $property = Property::create($validated);
        return response()->json($property, 201);
    }

    /**
     * Display the specified property.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $property = Property::with(['user', 'images', 'favorites'])->find($id);

        if (!$property) {
            return response()->json(['message' => 'Property not found'], 404);
        }

        return response()->json($property);
    }

    /**
     * Update the specified property in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'User_ID' => 'required|integer',
            'Property_Title' => 'required|string|max:255',
            'Property_Description' => 'nullable|string',
            'Property_Price' => 'required|numeric',
            'Property_Location' => 'required|string|max:255',
        ]);

        $property = Property::find($id);

        if (!$property) {
            return response()->json(['message' => 'Property not found'], 404);
        }

        $property->update($validated);
        return response()->json($property);
    }

    /**
     * Remove the specified property from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $property = Property::find($id);

        if (!$property) {
            return response()->json(['message' => 'Property not found'], 404);
        }

        $property->delete();
        return response()->json(['message' => 'Property deleted successfully.']);
    }
}
?>