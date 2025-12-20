<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\User;
use App\Models\Landlord;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use function Symfony\Component\String\u;

class AdminController extends Controller
{

        public function approveUser($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'approve';
        $user->save();   
        return response()->json([
            'message' => 'User approved successfully'
        ], 200);
    }

    public function rejectUser($id, Request $request)
    {
        $user = User::findOrFail($id);
        $user->status = 'reject';
        $user->save();   
        return response()->json([
            'message' => 'User reject successfully'
        ], 200);
    }

    public function getPendingUsers()
    {
        $pendingUsers = User::where('status', 'pending')->get();
        return response()->json($pendingUsers, 200);
    }
}