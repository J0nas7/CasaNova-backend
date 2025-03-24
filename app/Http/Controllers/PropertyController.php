<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PropertyController extends BaseController
{
    /**
     * The model class associated with this controller.
     *
     * @var string
     */
    protected string $modelClass = Property::class;

    /**
     * The relationships to eager load when fetching properties.
     *
     * @var array
     */
    protected array $with = ['user', 'images', 'favorites'];

    /**
     * Define the validation rules for properties.
     *
     * @return array The validation rules.
     */
    protected function rules(): array
    {
        return [
            'User_ID' => 'required|integer|exists:CN_Users,User_ID', // Ensure the user exists
            'Property_Title' => 'required|string|max:255',
            'Property_Description' => 'nullable|string',
            'Property_Address' => 'required|string|max:500',
            'Property_Latitude' => 'required|integer',
            'Property_Longitude' => 'required|integer',
            'Property_City' => 'required|string|max:255',
            'Property_State' => 'required|string|max:255',
            'Property_Zip_Code' => 'required|string|max:20',
            'Property_Price_Per_Month' => 'required|numeric|min:0',
            'Property_Num_Bedrooms' => 'required|integer|min:1',
            'Property_Num_Bathrooms' => 'required|integer|min:1',
            'Property_Square_Feet' => 'required|integer|min:0',
            'Property_Amenities' => 'nullable|array', // Should be an array if provided
            'Property_Property_Type' => 'required|string|max:50', // Validate property type
            'Property_Available_From' => 'nullable|date', // Optional date
            'Property_Available_To' => 'nullable|date', // Optional date
            'Property_Is_Active' => 'required|boolean', // Ensure it’s a boolean value (true/false)
        ];
    }

    /**
     * Retrieve all properties belonging to a specific user.
     *
     * @param int $userId The ID of the user whose properties should be retrieved.
     * @return JsonResponse A list of properties owned by the user.
     */
    public function getPropertiesByUser(int $userId): JsonResponse
    {
        // Fetch properties belonging to the given user ID and eager load relationships
        $properties = Property::with($this->with)->where('User_ID', $userId)->get();

        // If no properties found, return a 404 response
        if ($properties->isEmpty()) {
            return response()->json(['message' => 'No properties found for this user'], 404);
        }

        // Return the list of properties as a JSON response
        return response()->json($properties);
    }

    /**
     * Retrieve all properties within a specific price range.
     *
     * @param float $minPrice The minimum price.
     * @param float $maxPrice The maximum price.
     * @return JsonResponse A list of properties within the price range.
     */
    public function getPropertiesByPriceRange(float $minPrice, float $maxPrice): JsonResponse
    {
        // Fetch properties that fall within the specified price range
        $properties = Property::with($this->with)
            ->whereBetween('Property_Price', [$minPrice, $maxPrice])
            ->get();

        // If no properties found, return a 404 response
        if ($properties->isEmpty()) {
            return response()->json(['message' => 'No properties found in this price range'], 404);
        }

        // Return the list of properties as a JSON response
        return response()->json($properties);
    }
}
