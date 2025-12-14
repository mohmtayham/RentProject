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
    public function registerAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:4',
            'phone_number' => 'required|string',
        ]);

      
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number,
            'user_type' => 'admin',
        ]);

        
        Admin::create(['user_id' => $user->id]);

        return response()->json([
            'message' => 'Admin registered successfully',
            'user' => $user->load('admin'),
        ], 201);
    }

    public function loginAdmin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

      
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid email or password'
            ], 401);
        }

        $user = User::where('email', $request->email)->first();

       
        if ($user->user_type !== 'admin') {
            Auth::logout(); 
            return response()->json([
                'message' => 'Access denied. Only admins can log in.'
            ], 403);
        }

       
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'admin' => $user->load('admin'),
            'token' => $token
        ], 200);
    }

    public function logoutAdmin(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Logged out successfully'
        ], 200);
    }

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
}