<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Http\Resources\PropertyResource;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


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

/**
 * Add property to authenticated user's favorites
 */

//   public function addToFavorites($taskId)
//     {
//         try {
//             Task::findOrFail($taskId);
//             Auth::user()->favoriteTasks()->syncWithoutDetaching($taskId);
//             return response()->json(['message' => 'Task added to favorites'], 200);
//         } catch (ModelNotFoundException $e) {
//             return response()->json(['error' => 'Task not found'], 404);
//         } catch (Exception $e) {
//             return response()->json(['error' => 'Something went wrong'], 500);
//         }
//     }
//  public function user()
//     {
//         return $this->belongsTo(User::class);
//     }
//     public function categories()
//     {
//         return $this->belongsToMany(Category::class,'category_task');
//     }
//     public function favoriteByUser()
//     {
//         return $this->belongsToMany(User::class,'favorites');
//     }
//     public function profile()
//     {
//         return $this->hasOne(Profile::class);
//     }
//     public function tasks()
//     {
//         return $this->hasMany(Task::class);
//     }
//     public function favoriteTasks()
//     {
//         return $this->belongsToMany(Task::class,'favorites');
//     }
// }


public function addToFavorites(Request $request, $propertyId)
{
    //  Property::findOrFail($propertyId);
    //        Auth::user()->favoriteProperties()->syncWithoutDetaching($propertyId);
    //       return response()->json(['message' => 'Task added to favorites'], 200);
               try {
        // Log the start of the request
        Log::info('addToFavorites called', [
            'user_id' => $request->user()?->id,
            'property_id' => $propertyId,
            'ip' => $request->ip()
        ]);

   
        // Check if user is authenticated
                $tenant = $request->user()->tenant;

        if (!$tenant) {
            Log::warning('Unauthenticated user tried to add to favorites', ['property_id' => $propertyId]);
            return response()->json([
                'message' => 'Unauthenticated.'
            ], 401);
        }

        // Validate propertyId is numeric
        if (!is_numeric($propertyId)) {
            Log::warning('Invalid property ID format', ['property_id' => $propertyId]);
            return response()->json([
                'message' => 'Invalid property ID.'
            ], 400);
        }

        // Find the property
        $property = Property::find($propertyId);
        if (!$property) {
            Log::info('Property not found', ['property_id' => $propertyId]);
            return response()->json([
                'message' => 'Property not found.'
            ], 404);
        }

        Log::info('Property found', ['property_id' => $property->id, 'title' => $property->title ?? 'No title']);

        // Check if already in favorites
       $alreadyFavorite = $tenant->favoriteProperties()
 ->where('property_id', $property->id)
    ->exists();

        if ($alreadyFavorite) {
            Log::info('Property already in favorites', [
                'tenant_id' => $tenant->id,
                'property_id' => $property->id
            ]);

            return response()->json([
                'message' => 'Property is already in your favorites',
                'is_favorite' => true
            ], 200);
        }

        // Attach to favorites
        $tenant->favoriteProperties()->attach($property->id);

        Log::info('Property successfully added to favorites', [
            'user_id' => $tenant->id,
            'property_id' => $property->id
        ]);

        return response()->json([
            'message' => 'Property added to your favorites',
            'user_id' => $tenant->id,
            'property_id' => $property->id,
            'is_favorite' => true
        ], 200);

    } catch (Exception $e) {
        // Log the full error with trace
        Log::error('Error in addToFavorites', [
            'user_id' => $request->user()?->id ?? 'guest',
            'property_id' => $propertyId ?? null,
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);

        // Return a clean error response (don't expose details in production)
        return response()->json([
            'message' => 'An error occurred while adding to favorites.',
            'hint' => app()->environment('local') ? $e->getMessage() : 'Check server logs.'
        ], 500);
    }
}

/**
 * Remove property from authenticated user's favorites
 */
public function removeFromFavorites(Request $request, $id)
{
    // Find the property
    $property = Property::findOrFail($id);
    
    // Get the authenticated user
    $user = $request->user();
    
    // Check if property is in favorites
    if (!$user->favoriteProperties()->where('property_id', $property->id)->exists()) {
        return response()->json([
            'message' => 'Property is not in your favorites',
            'is_favorite' => false
        ], 404);
    }
    
    // Remove from favorites
    $user->favoriteProperties()->detach($property->id);
    
    return response()->json([
        'message' => 'Property removed from your favorites',
        'user_id' => $user->id,
        'property_id' => $property->id,
        'is_favorite' => false
    ], 200);
}

/**
 * List authenticated user's favorite properties
 */
public function listFavoriteProperties(Request $request)
{
    $user = $request->user();
    
    // Get favorite properties with relationships
    $favoriteProperties = $user->favoriteProperties()
        ->with(['images', 'landlord'])
        ->paginate(15);
    
    return PropertyResource::collection($favoriteProperties);
}

/**
 * Check if property is in authenticated user's favorites
 */
public function checkFavorite(Request $request, $id)
{
    $property = Property::findOrFail($id);
    $user = $request->user();
    
    $isFavorite = $user->favoriteProperties()
        ->where('property_id', $property->id)
        ->exists();
    
    return response()->json([
        'is_favorite' => $isFavorite,
        'property_id' => $property->id,
        'user_id' => $user->id
    ]);
}

}
