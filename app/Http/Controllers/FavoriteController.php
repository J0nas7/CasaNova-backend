<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends BaseController
{
    /**
     * The model class associated with this controller.
     *
     * @var string
     */
    protected string $modelClass = Favorite::class;

    /**
     * The relationships to eager load when fetching favorites.
     *
     * @var array
     */
    protected array $with = ['user', 'property'];

    /**
     * Define the validation rules for favorites.
     *
     * @return array The validation rules.
     */
    protected function rules(): array
    {
        return [
            'User_ID' => 'required|integer|exists:CN_Users,User_ID',
            'Property_ID' => 'required|integer|exists:CN_Properties,Property_ID'
        ];
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