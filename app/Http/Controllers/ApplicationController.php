<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApplicationResource;
use App\Models\Application;
use App\Http\Requests\StoreApplicationRequest;
use App\Http\Requests\UpdateApplicationRequest;
use App\Models\Landlord;
use App\Models\Property;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class ApplicationController extends Controller
{
    public function index()
    {
        $applications = Application::all();
        return ApplicationResource::collection($applications);
    }




    // Only allow tenant to cancel or re-edit (if still pending)
    



 
public function makeOrderToLandlord(Request $request)
{
    $user = $request->user();

    if (!$user || $user->user_type !== 'tenant') {
        return response()->json(['message' => 'Only tenants can submit rental applications.'], 403);
    }

    $validated = $request->validate([
        'property_id'  => 'required|exists:properties,id',
        'start_date'   => 'required|date|after_or_equal:today',
        'end_date'     => [
            'required',
            'date',
            'after:start_date',
            'before:' . now()->addYears(2)->format('Y-m-d'),
        ],
        'monthly_rent' => 'required|numeric|min:10|max:60000',
        'notes'        => 'nullable|string|max:1000',
    ]);

    $tenant = $user->tenant;
    if (!$tenant) {
        return response()->json(['message' => 'Tenant profile not found.'], 404);
    }

    $property = Property::with(['landlord.user'])->findOrFail($validated['property_id']);

    if (!$property->is_available || !$property->landlord) {
        return response()->json(['message' => 'Property not available or no landlord assigned.'], 400);
    }

    
    $existing = Application::where('tenant_id', $tenant->id)
        ->where('property_id', $property->id)
        ->whereIn('status', ['pending', 'under_review', 'approved'])
        ->exists();

    if ($existing) {
        return response()->json(['message' => 'You already have an active application for this property.'], 400);
    }
      $conflict = Application::where('property_id', $validated['property_id'])
    ->where('start_date', '<', $validated['end_date'])  
    ->where('end_date', '>', $validated['start_date'])   
    ->exists();

if ($conflict) {
    return response()->json(['message' => 'Reservation conflict detected.'], 409);
}


   
    $startDate = Carbon::parse($validated['start_date']);
    $endDate   = Carbon::parse($validated['end_date']);
    $durationMonths = $startDate->diffInMonths($endDate);

   
    $application = Application::create([
        'tenant_id'     => $tenant->id,
        'property_id'   => $property->id,
        'landlord_id'   => $property->landlord_id,
        'start_date'    => $startDate,
        'end_date'      => $endDate,
        'monthly_rent'  => $validated['monthly_rent'],
        'status'        => 'pending',
        'submitted_at'  => now(),
        'notes'         => $validated['notes'] ?? null,
    ]);


    return response()->json([
        'message' => 'make succsessfuly',
        'application' => [
            'id'           => $application->id,
            'status'       => $application->status,
            'submitted_at' => $application->submitted_at->format('Y-m-d H:i:s'),
            'notes'        => $application->notes,

            'property' => [
                'id'           => $property->id,
                'address'      => $property->address,
                'city'         => $property->city,
                'monthly_rent' => (float) $property->monthly_rent,
                'is_available' => $property->is_available,
            ],

            'landlord' => [
                'id'    => $property->landlord->id,
                'name'  => $property->landlord->user->name ?? 'غير متوفر',
                'email' => $property->landlord->user->email ?? 'غير متوفر',
                'phone' => $property->landlord->user->phone_number ?? null,
            ],

            'tenant' => [
                'id'    => $tenant->id,
                'name'  => $user->name,
                'email' => $user->email,
                'phone' => $user->phone_number ?? null,
            ],

            'proposed_rental_details' => [
                'start_date'      => $startDate->format('Y-m-d'),
                'end_date'        => $endDate->format('Y-m-d'),
                'duration_months' => $durationMonths,
                'monthly_rent'    => (float) $validated['monthly_rent'],
            ],
        ]
    ], 201);
}
public function approveOrRejectOrMakeUnderreview(Request $request, $id)
{
    Log::info('approveOrRejectOrMakeUnderreview endpoint called', [
        'application_id' => $id,
        'user_id'        => Auth::id(),
        'ip'             => $request->ip(),
        'request_data'   => $request->all()
    ]);

    try {
      
        $user = Auth::user();
        if (!$user) {
            Log::warning('Unauthenticated user tried to change application status', ['application_id' => $id]);
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

      
        if ($user->user_type !== 'landlord') {
            Log::warning('Non-landlord user tried to change application status', [
                'user_id'    => $user->id,
                'user_type'  => $user->user_type,
                'application_id' => $id
            ]);
            return response()->json([
                'message' => 'Only landlords can approve or reject applications.'
            ], 403);
        }

       
        if (!$user->landlord) {
            Log::warning('Landlord user has no landlord profile', ['user_id' => $user->id]);
            return response()->json([
                'message' => 'Your landlord profile is incomplete.'
            ], 403);
        }

        Log::info('Landlord authenticated', [
            'user_id'     => $user->id,
            'landlord_id' => $user->landlord->id
        ]);

   
   
$application = Application::find($id);

if (!$application) {
    Log::warning('Application not found', ['application_id' => $id]);
    return response()->json(['message' => 'Application not found.'], 404);
}

$application->load('property.landlord');

if (!$application->property) {
    Log::error('Application has no associated property', ['application_id' => $id]);
    return response()->json(['message' => 'Invalid application: missing property.'], 500);
}

if (!$application->property->landlord) {
    Log::error('Property has no landlord', ['property_id' => $application->property_id]);
    return response()->json(['message' => 'Invalid property: no landlord assigned.'], 500);
}

if ($application->property->landlord_id !== $user->landlord->id) {
    Log::warning('Unauthorized access attempt', [
        'landlord_id' => $user->landlord->id,
        'property_landlord_id' => $application->property->landlord_id,
        'application_id' => $id
    ]);
    return response()->json(['message' => 'You are not authorized to manage this application.'], 403);
}

        // 6. Validation للـ status
        $validated = $request->validate([
            'status' => 'required|string|in:approved,rejected,under_review',
        ]);

        Log::info('Validation passed', ['new_status' => $validated['status']]);

       
        $oldStatus = $application->status;
        $application->update([
            'status' => $validated['status']
        ]);

        Log::info('Application status updated successfully', [
            'application_id' => $application->id,
            'old_status'     => $oldStatus,
            'new_status'     => $application->status,
            'updated_by'     => $user->id
        ]);

       
        return response()->json([
            'message' => 'Application status updated successfully.',
            'application' => [
                'id'         => $application->id,
                'status'     => $application->status,
                'property_id'=> $application->property_id,
                'tenant_id'  => $application->tenant_id,
                'updated_at' => $application->updated_at,
            ]
        ], 200);

    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::warning('Validation failed in approveOrRejectOrMakeUnderreview', [
            'application_id' => $id,
            'errors'         => $e->errors()
        ]);
        return response()->json([
            'message' => 'Validation failed',
            'errors'  => $e->errors()
        ], 422);

    } catch (\Exception $e) {
        Log::error('Unexpected error in approveOrRejectOrMakeUnderreview', [
            'application_id' => $id,
            'user_id'        => Auth::id(),
            'message'        => $e->getMessage(),
            'file'           => $e->getFile(),
            'line'           => $e->getLine(),
            'trace'          => $e->getTraceAsString()
        ]);

        return response()->json([
            'message' => 'Failed to update application status.',
            'hint'    => app()->environment('local') ? $e->getMessage() : 'Check server logs for details.'
        ], 500);
    }
}
}