<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
    protected array $with = [
        'user',
        'images'
    ];

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
            // 'Property_State' => 'required|string|max:255',
            'Property_Zip_Code' => 'required|integer',
            'Property_Price_Per_Month' => 'required|numeric|min:0',
            'Property_Num_Bedrooms' => 'required|integer|min:1',
            'Property_Num_Bathrooms' => 'required|integer|min:1',
            'Property_Square_Feet' => 'required|integer|min:0',
            'Property_Amenities' => 'nullable|array', // Should be an array if provided
            'Property_Property_Type' => 'required|integer|max:50', // Validate property type
            // 'Property_Available_From' => 'nullable|date', // Optional date
            // 'Property_Available_To' => 'nullable|string|max:50', // 3-6 months, 6-12 months, 1-2 years, 2+ years etc.
            'Property_Is_Active' => 'required|boolean', // Ensure it’s a boolean value (true/false)
        ];
    }

    // OVERRIDE of the BaseController methods
    public function index(): JsonResponse
    {
        $authUser = Auth::guard('api')->user();

        // Modify the eager-loaded relationships dynamically
        $with = [
            'user',
            'images',
            'favorites',
            'messages' => function ($query) use ($authUser) {
                if ($authUser) {
                    // Only load messages for the authenticated user
                    $query->where('Receiver_ID', $authUser->User_ID)
                        ->orWhere('Sender_ID', $authUser->User_ID);
                }
            }
        ];

        // Retrieve all properties with the modified eager loading where Property_Is_Active is true
        $properties = $this->modelClass::with($with)
            ->where('Property_Is_Active', true)
            ->get();

        // Return the properties as a JSON response
        return response()->json($properties);
    }
    
    public function show(int $id): JsonResponse
    {
        $with = [
            'user',
            'images',
            'favorites',
            'messages' => function ($query) {
                $authUser = Auth::guard('api')->user();
                if ($authUser) {
                    // Only load messages for the authenticated user
                    $query->where('Receiver_ID', $authUser->User_ID)
                        ->orWhere('Sender_ID', $authUser->User_ID);
                }
            }
        ];

        $property = $this->modelClass::with($with)->find($id);

        if (!$property) {
            return response()->json(['message' => 'Property not found'], 404);
        }

        return response()->json($property);
    }

    // Custom methods for PropertyController
    /**
     * Update the availability details of a property.
     *
     * This method validates the incoming request data, retrieves the property
     * by its ID, and updates its availability information. The availability
     * includes the date range when the property is available and its active status.
     *
     * @param \Illuminate\Http\Request $request The incoming HTTP request containing the property availability data.
     * @param int $propertyId The ID of the property to be updated.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the success of the update operation.
     *
     * @throws \Illuminate\Validation\ValidationException If the request data fails validation.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the property with the given ID is not found.
     */
    public function updatePropertyAvailability(Request $request, int $propertyId): JsonResponse
    {
        // Validate the request data
        $validated = $request->validate([
            'Property_Available_From' => 'nullable|date',
            'Property_Available_To' => 'nullable|string|max:50', // 3-6 months, 6-12 months, 1-2 years, 2+ years etc.
            'Property_Is_Active' => 'required|boolean',
        ]);

        // Find the property by ID
        $property = Property::findOrFail($propertyId);

        // Update the property availability
        $property->update($validated);

        return response()->json([
            'message' => 'Property updated successfully',
            'property' => $property->load('images')
        ], 200);
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

    /**
     * Create a new property along with its associated images.
     *
     * This method validates the incoming request data, creates a new property record
     * in the database, and optionally associates image URLs with the property. The
     * operation is performed within a database transaction to ensure data consistency.
     *
     * @param \Illuminate\Http\Request $request The incoming HTTP request containing property data.
     * 
     * @return \Illuminate\Http\JsonResponse A JSON response containing the created property
     *                                       and its associated images, or an error message
     *                                       if the operation fails.
     *
     * @throws \Illuminate\Validation\ValidationException If the request validation fails.
     * @throws \Exception If an error occurs during the database transaction.
     */
    public function createPropertyWithImages(Request $request): JsonResponse
    {
        $validated = $this->prepareValidatedPropertyData($request);

        try {
            DB::beginTransaction(); // Start a transaction

            // Create the property
            $property = Property::create($validated);

            // Handle image uploads if files are provided
            if ($request->hasFile('images')) {
                $imageData = [];
                foreach ($request->file('images') as $image) {
                    // Generate a unique file name
                    $fileName = time() . '-' . $image->getClientOriginalName();

                    // Store the image in 'public/listings' directory and get the path
                    $filePath = $image->storeAs('listings', $fileName, 'public');

                    // Prepare data for batch insert
                    $imageData[] = [
                        'Property_ID' => $property->Property_ID,
                        'Image_Name' => $fileName,
                        'Image_Path' => $filePath,
                        'Image_Order' => count($imageData) + 1,
                        'Image_Type' => $image->getClientOriginalExtension(), // Store the file extension (JPG, PNG)
                        'Image_CreatedAt' => now(),
                        'Image_UpdatedAt' => now()
                    ];
                }

                // Insert images into database
                PropertyImage::insert($imageData);
            }

            DB::commit(); // Commit transaction

            return response()->json([
                'message' => 'Property created successfully',
                'property' => $property->load('images')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction if an error occurs
            return response()->json(['error' => 'Failed to create property', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Update an existing property along with its associated images.
     *
     * This method validates the incoming request data, updates an existing property record
     * in the database, and optionally updates or associates new image URLs with the property.
     * The operation is performed within a database transaction to ensure data consistency.
     *
     * @param \Illuminate\Http\Request $request The incoming HTTP request containing property data.
     * @param int $propertyId The ID of the property to update.
     * 
     * @return \Illuminate\Http\JsonResponse A JSON response containing the updated property
     *                                       and its associated images, or an error message
     *                                       if the operation fails.
     *
     * @throws \Illuminate\Validation\ValidationException If the request validation fails.
     * @throws \Exception If an error occurs during the database transaction.
     */
    public function updatePropertyWithImages(Request $request, int $propertyId): JsonResponse
    {
        $validated = $this->prepareValidatedPropertyData($request);

        try {
            DB::beginTransaction(); // Start a transaction

            // Find the property by ID
            $property = Property::findOrFail($propertyId);

            // Update the property
            $property->update($validated);

            // Handle image uploads if files are provided
            $newOrder = [];
            if ($request->has('images')) {
                // Extract paths from the provided images array
                $providedPaths = collect($request->input('images'))
                    ->filter(fn($image) => is_string($image)) // Only keep string paths
                    ->toArray();

                // Delete existing images for the property that are not in the provided paths
                PropertyImage::where('Property_ID', $propertyId)
                    ->whereNotIn('Image_Path', $providedPaths)
                    ->delete();

                $imageData = [];

                if ($request->file('images')) {
                    foreach ($request->file('images') as $image) {
                        if ($image instanceof \Illuminate\Http\UploadedFile) {
                            // Generate a unique file name
                            $fileName = time() . '-' . $image->getClientOriginalName();

                            // Store the image in 'public/listings' directory and get the path
                            $filePath = $image->storeAs('listings', $fileName, 'public');

                            // Prepare data for batch insert
                            $imageData[] = [
                                'Property_ID' => $property->Property_ID,
                                'Image_Name' => $fileName,
                                'Image_Path' => $filePath,
                                'Image_Order' => count($imageData) + 1,
                                'Image_Type' => $image->getClientOriginalExtension(), // Store the file extension (JPG, PNG)
                                'Image_CreatedAt' => now(),
                                'Image_UpdatedAt' => now()
                            ];
                        }
                    }
                }

                // Insert new images into database
                PropertyImage::insert($imageData);

                // Incrementally update Image_Order for all images
                if ($request->input('images')) {
                    foreach ($request->input('images') as $index => $imagePath) {
                        if (is_string($imagePath)) {
                            PropertyImage::where('Property_ID', $propertyId)
                                ->where('Image_Path', $imagePath)
                                ->update(['Image_Order' => $index + 1]);
                            $newOrder[] = $imagePath;
                        }
                    }
                }
            }

            DB::commit(); // Commit transaction

            return response()->json([
                'message' => 'Property updated successfully',
                'property' => $property->load('images'),
                'has' => $request->has('images'),
                'input' => $request->input('images'),
                'file' => $request->file('images'),
                'newOrder' => $newOrder,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction if an error occurs
            return response()->json([
                'error' => 'Failed to update property',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Prepares and validates the request data before creating or updating a property.
     */
    private function prepareValidatedPropertyData(Request $request): array
    {
        // Convert string booleans to actual booleans
        if ($request->has('Property_Is_Active')) {
            $request->merge([
                'Property_Is_Active' => filter_var($request->input('Property_Is_Active'), FILTER_VALIDATE_BOOLEAN)
            ]);
        }

        // Handle Property_Amenities from formData JSON string or comma-separated values
        if ($request->has('Property_Amenities')) {
            $amenities = $request->input('Property_Amenities');

            // Handle the case where "null" is sent as a string
            if ($amenities === "null") {
                $amenities = null;
            }
            // Convert JSON string to array if necessary
            elseif (is_string($amenities)) {
                $decoded = json_decode($amenities, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $amenities = $decoded;
                } else {
                    // Convert comma-separated values to an array
                    $amenities = explode(',', $amenities);
                }
            }

            // Ensure it's an array or null
            $request->merge([
                'Property_Amenities' => is_array($amenities) ? $amenities : ($amenities === null ? null : []),
            ]);
        }

        // Validate request data
        return $request->validate(array_merge($this->rules(), [
            'images' => 'nullable',
            // 'images.*' => 'file|max:20480|mimes:jpg,jpeg,png',
            'images.*' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!is_string($value) && !$value instanceof \Illuminate\Http\UploadedFile) {
                        $fail('Each image must be either a valid file or a string URL.');
                    }
                },
                function ($attribute, $value, $fail) {
                    if (is_string($value)) {
                        return; // Skip further validation if it's a string
                    }
                    if ($value->getSize() > 20480 * 1024) { // 20MB in bytes
                        $fail('The file size must not exceed 20MB.');
                    }
                    $allowedMimes = ['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG'];
                    if (!in_array($value->getClientOriginalExtension(), $allowedMimes)) {
                        $fail('The file must be a type of: jpg, jpeg, png.');
                    }
                },
            ],
        ]));
    }
}
