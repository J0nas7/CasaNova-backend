<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FavoriteController extends Controller
{
    /**
     * Display a listing of favorites.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $favorites = Favorite::with(['user', 'property'])->get();
        return response()->json($favorites);
    }

    /**
     * Store a newly created favorite in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'User_ID' => 'required|integer|exists:CN_Users,User_ID',
            'Property_ID' => 'required|integer|exists:CN_Properties,Property_ID',
        ]);

        // Check if favorite already exists
        $existingFavorite = Favorite::where('User_ID', $validated['User_ID'])
            ->where('Property_ID', $validated['Property_ID'])
            ->first();

        if ($existingFavorite) {
            return response()->json(['message' => 'Property is already favorited'], 409);
        }

        $favorite = Favorite::create($validated);
        return response()->json($favorite, 201);
    }

    /**
     * Display the specified favorite.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $favorite = Favorite::with(['user', 'property'])->find($id);

        if (!$favorite) {
            return response()->json(['message' => 'Favorite not found'], 404);
        }

        return response()->json($favorite);
    }

    /**
     * Remove the specified favorite from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $favorite = Favorite::find($id);

        if (!$favorite) {
            return response()->json(['message' => 'Favorite not found'], 404);
        }

        $favorite->delete();
        return response()->json(['message' => 'Favorite removed successfully.']);
    }

    /**
     * Remove a favorite by User_ID and Property_ID.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function removeByUserAndProperty(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'User_ID' => 'required|integer|exists:CN_Users,User_ID',
            'Property_ID' => 'required|integer|exists:CN_Properties,Property_ID',
        ]);

        $favorite = Favorite::where('User_ID', $validated['User_ID'])
            ->where('Property_ID', $validated['Property_ID'])
            ->first();

        if (!$favorite) {
            return response()->json(['message' => 'Favorite not found'], 404);
        }
        
        $favorite->delete();
        return response()->json(['message' => 'Favorite removed successfully.']);
    }
}
?>