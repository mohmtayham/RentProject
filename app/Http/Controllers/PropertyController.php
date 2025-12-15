<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Http\Resources\PropertyResource;
use Illuminate\Support\Facades\Storage;

class PropertyController extends Controller
{
public function index()
{
    $properties = Property::all(); // Use proper variable name
    return PropertyResource::collection($properties); 
}
public function filterBycity(Request $request)
{
    $validated = $request->validate([
        'city' => 'required|string|max:100',
    ]);

    $properties = Property::where('city', $validated['city'])->get();

    return PropertyResource::collection($properties);
}







public function filterBymonthly_rent(Request $request)
{
    
    $validated = $request->validate([
        'min_rent' => 'required|numeric|min:0',      
        'max_rent' => 'required|numeric|gte:min_rent', 
    ]);
    
   
    $properties = Property::whereBetween('monthly_rent', [
        $validated['min_rent'],  
        $validated['max_rent']   
    ])->get();
    
    return PropertyResource::collection($properties);
}
public function make_ave_rationg($propertyId, Request $request)
{
    $property = Property::findOrFail($propertyId);
    $newRating = $request->input('rating');

    // Simple average calculation (you might want to store number of ratings for a better average)
    $property->avg_rating = ($property->avg_rating + $newRating) / 2;
    $property->save();

    return new PropertyResource($property);
}
public function showAvillableProperties()
{
    $properties = Property::where('is_available', true)->get();
    return PropertyResource::collection($properties);
}

public function showPropertyDetails($id)
{
    $property = Property::findOrFail($id);
    return new PropertyResource($property);
}





/**
 * Store a newly created property in storage.
 */
/**
 * Store a newly created property in storage.
 */
public function store(Request $request)
{
    // Validate incoming data
    $validated = $request->validate([
        'landlord_id' => 'required|exists:landlords,id',
        'address' => 'required|string|max:255',
        'city' => 'required|string|max:100',
        'state' => 'nullable|string|max:100',
        'square_feet' => 'nullable|integer|min:0',
        'monthly_rent' => 'required|numeric|min:0',
        'description' => 'nullable|string',
        'is_available' => 'nullable|boolean',
        'note' => 'nullable|string|max:500',
        'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Changed to image validation
    ]);

    // Prepare property data
    $propertyData = [
        'landlord_id' => $request->landlord_id,
        'address' => $request->address,
        'city' => $request->city,
        'state' => $request->state,
        'square_feet' => $request->square_feet,
        'monthly_rent' => $request->monthly_rent,
        'description' => $request->description,
        'is_available' => $request->is_available ?? true,
        'note' => $request->note,
    ];

    // Handle photo upload
    if ($request->hasFile('photo')) {
        $photoPath = $request->file('photo')->store('properties/photos', 'public');
        $propertyData['photo'] = $photoPath;
    }

    // Create the property
    $property = Property::create($propertyData);

    // Return response with the created property
    return response()->json([
        'message' => 'Property created successfully',
        'data' => new PropertyResource($property)
    ], 201);
}
/**
 * Update the specified property in storage.
 */
public function update(Request $request, $id)
{
    // Find the property or fail
    $property = Property::findOrFail($id);

    // Validate incoming data
    $validated = $request->validate([
        'landlord_id' => 'sometimes|exists:landlords,id',
        'address' => 'sometimes|string|max:255',
        'city' => 'sometimes|string|max:100',
        'state' => 'nullable|string|max:100',
        'square_feet' => 'nullable|integer|min:0',
        'monthly_rent' => 'sometimes|numeric|min:0',
        'description' => 'nullable|string',
        'is_available' => 'nullable|boolean',
        'note' => 'nullable|string|max:500',
        'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    // Prepare update data
    $updateData = [];

    // Add fields that are present in the request
    $fields = [
        'landlord_id', 'address', 'city', 'state', 'square_feet',
        'monthly_rent', 'description', 'is_available', 'note'
    ];

    foreach ($fields as $field) {
        if ($request->has($field)) {
            $updateData[$field] = $request->$field;
        }
    }

    // Handle photo upload
    if ($request->hasFile('photo')) {
        // Delete old photo if exists
        if ($property->photo && Storage::disk('public')->exists($property->photo)) {
            Storage::disk('public')->delete($property->photo);
        }
        
        // Upload new photo
        $photoPath = $request->file('photo')->store('properties/photos', 'public');
        $updateData['photo'] = $photoPath;
    }

    // Update the property
    $property->update($updateData);

    // Return response
    return response()->json([
        'message' => 'Property updated successfully',
        'data' => new PropertyResource($property->fresh())
    ]);
}
/**
 * Remove the specified property from storage.
 */
public function destroy($id)
{
    // Find the property or fail
    $property = Property::findOrFail($id);

    // Delete the property's photo if exists
    if ($property->photo && Storage::disk('public')->exists($property->photo)) {
        Storage::disk('public')->delete($property->photo);
    }

    // Delete the property
    $property->delete();

    // Return success response
    return response()->json([
        'message' => 'Property deleted successfully'
    ], 200);
}

public function addToFavorites(Request $request, $id)
{
    $property = Property::findOrFail($id);
    $user = $request->user();

    $user->favoriteProperties()->attach($property->id);

    return response()->json([
        'message' => 'Property added to favorites'
    ], 200);
}

public function removeFromFavorites(Request $request, $id)
{
    $property = Property::findOrFail($id);
    $user = $request->user();

    $user->favoriteProperties()->detach($property->id);

    return response()->json([
        'message' => 'Property removed from favorites'
    ], 200);
}
public function listFavoriteProperties(Request $request)
{
    $user = $request->user();
    $favoriteProperties = $user->favoriteProperties;

    return PropertyResource::collection($favoriteProperties);


}

}
