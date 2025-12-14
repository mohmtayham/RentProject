<?php

namespace App\Http\Controllers;

use App\Models\Landlord;
use App\Http\Requests\StoreLandlordRequest;
use App\Http\Requests\UpdateLandlordRequest;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LandlordController extends Controller
{
 public function approvecontract(Request $request, $id)
    {
        

       $application=Application::findOrFail($id); 
        if ($application->landlord_id !== Auth::id()) { 
            return response()->json(['error' => 'Unauthorized'], 403);
             if (!in_array($application->status, ['pending', 'under_review'])) {
            return response()->json(['error' => 'Cannot modify a finalized application'], 400);
        }
        }  
       $application->status='approved';
       $application->save();

        return response()->json(['message' => 'the contract has been approved successfully']);
    }
   // $table->enum('status', ['pending', 'approved', 'rejected', 'under_review'])->default('pending');

public function rejectcontract(Request $request, $id)
    {
       $application=Application::findOrFail($id);   
         if ($application->landlord_id !== Auth::id()) { 
            return response()->json(['error' => 'Unauthorized'], 403);
        }  
         if (!in_array($application->status, ['pending', 'under_review'])) {
            return response()->json(['error' => 'Cannot modify a finalized application'], 400);
        }
 $application->contract_status = 'rejected';
       $application->save();

       
        

        return response()->json(['message' => 'the contract has been rejected successfully']);
    }
public function underreviewcontract(Request $request, $id)
    {
       $application=Application::findOrFail($id);   
         if ($application->landlord_id !== Auth::id()) { 
            return response()->json(['error' => 'Unauthorized'], 403);
        }  
         if (!in_array($application->status, ['pending', 'under_review'])) {
            return response()->json(['error' => 'Cannot modify a finalized application'], 400);
        }
 $application->contract_status = 'under_review';
       $application->save();

       
        

        return response()->json(['message' => 'the contract is under review now']);
    }

}
