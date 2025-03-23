<?php

namespace App\Http\Controllers;

use App\Models\PropertyImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PropertyImageController extends BaseController
{
    /**
     * The model class associated with this controller.
     *
     * @var string
     */
    protected string $modelClass = PropertyImage::class;

    /**
     * The relationships to eager load when fetching property images.
     *
     * @var array
     */
    protected array $with = ['property'];

    /**
     * Define the validation rules for property images.
     *
     * @return array The validation rules.
     */
    protected function rules(): array
    {
        return [
            'Property_ID' => 'required|integer|exists:CN_Properties,Property_ID',
            'Image_URL' => 'required|string|max:255',
        ];
    }

    /**
     * Retrieve all images associated with a specific property, including property details.
     *
     * @param int $propertyId The ID of the property.
     * @return JsonResponse A list of images for the property with eager loading.
     */
    public function getImagesByProperty(int $propertyId): JsonResponse
    {
        // Fetch images related to the given property ID with eager loading
        $images = PropertyImage::with($this->with)
            ->where('Property_ID', $propertyId)
            ->get();

        // If no images are found, return a 404 response
        if ($images->isEmpty()) {
            return response()->json(['message' => 'No images found for this property'], 404);
        }

        // Return the list of images with the related property data
        return response()->json($images);
    }
}
?>