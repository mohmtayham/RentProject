<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApplicationResource;
use App\Models\Application;
use App\Http\Requests\StoreApplicationRequest;
use App\Http\Requests\UpdateApplicationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // ✅ هذا هو الحل!

class ApplicationController extends Controller
{
    public function index()
    {
        $applications = Application::all();
        return ApplicationResource::collection($applications);
    }

    // Only allow tenant to cancel or re-edit (if still pending)
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:cancelled,pending' // Only allow these
        ]);

        $application = Application::findOrFail($id);

        // Ensure the authenticated user is the tenant who owns this application
        if ($application->tenant_id !== Auth::id()) { 
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Only allow changing status if current status is pending or under_review
        if (!in_array($application->status, ['pending', 'under_review'])) {
            return response()->json(['error' => 'Cannot modify a finalized application'], 400);
        }

        $application->status = $request->status;
        $application->save();

        return new ApplicationResource($application);
    }
}