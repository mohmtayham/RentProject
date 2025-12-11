<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Http\Resources\UserResourcev;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\Landlord;
use App\Models\Tenant;

class UserController extends Controller
{
   public function register(Request $request)
    {
        // Validate input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:4',
            'phone_number' => 'required|string',
            'user_type' => 'required|in:tenant,landlord,admin',
        ]);

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number,
            'user_type' => $request->user_type,
        ]);

        // Create related profile record for the chosen user_type
        switch ($request->user_type) {
            case 'admin':
                Admin::create(['user_id' => $user->id]);
                break;
            case 'landlord':
                Landlord::create(['user_id' => $user->id]);
                break;
            case 'tenant':
                Tenant::create(['user_id' => $user->id]);
                break;
        }

        // create token immediately
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user->load($user->user_type),
            'token' => $token
        ], 201);
    }

    public function login(Request $request)
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

    $user = User::where('email', $request->email)->firstOrFail();

    // revoke previous tokens and issue a new one
    $user->tokens()->delete();
    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'Login successful',
        'user' => $user->load($user->user_type),
        'token' => $token
    ], 200);
}

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        
        return response()->json([
            'message' => 'Logout successful'
        ]);
    }
    public function GetUser()
{
    $user_id = Auth::user()->id;
    $userData = User::findOrFail($user_id);
    return new UserResourcev($userData);
}
public function show(Request $request)
{
    return new UserResourcev($request->user()->load($request->user()->user_type));
}

public function index()
{
    $users = User::all();
    return UserResourcev::collection($users);

}

public function destroy($id)
{
    $user = User::findOrFail($id);
    $user->delete();

    return response()->json([
        'message' => 'User deleted successfully'
    ]);}

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|required|min:4',
            'phone_number' => 'sometimes|required|string',
            'user_type' => 'sometimes|required|in:tenant,landlord,admin',
        ]);

        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }
        if ($request->has('phone_number')) {
            $user->phone_number = $request->phone_number;
        }
        if ($request->has('user_type')) {
            $user->user_type = $request->user_type;
        }

        $user->save();

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user
        ]);
    }  


}