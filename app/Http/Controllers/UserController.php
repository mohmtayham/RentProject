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
use App\Http\Controllers;

class UserController extends Controller
{
   public function register(Request $request)
{
    // Validate input (no 'status' needed)
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:4',
        'phone_number' => 'required|string',
        'user_type' => 'required|in:tenant,landlord,admin',
    ]);

   
   

   
    $existing = User::where('email', $request->email)
        ->whereIn('status', ['pending', 'reject'])
        ->first();

    if ($existing) {
        if ($existing->status === 'pending') {
            return response()->json([
                'message' => 'You already have a pending registration. Please wait for admin approval.'
            ], 403);
        } else {
            return response()->json([
                'message' => 'Your previous registration was rejected. Please contact support.'
            ], 403);
        }
    }

   
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'phone_number' => $request->phone_number,
        'user_type' => $request->user_type,
        'status' => 'pending', // ← This is the key line!
    ]);

    // Create related profile
    switch ($request->user_type) {
        case 'landlord':
            Landlord::create(['user_id' => $user->id]);
            break;
        case 'tenant':
            Tenant::create(['user_id' => $user->id]);
            break;
        // admin is blocked above, so no need to handle
    }

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'Registration successful. Awaiting admin approval.',
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

    // Check if user is approved
    if ($user->status !== 'approve') {
        // Log them out automatically
        Auth::logout();
        
        return response()->json([
            'message' => 'Account pending admin approval',
            'status' => $user->status
        ], 403);
    }

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

   $user->update($request->only(['name', 'email', 'phone_number', 'user_type']));

        $user->save();

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user
        ]);
    }  
public function addimage(Request $request, $id)
{
    $user = User::findOrFail($id);

    $request->validate([
        'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    if ($request->hasFile('profile_image')) {
        $image = $request->file('profile_image');
        $imageName = time() . '_' . $image->getClientOriginalName();
        $image->move(public_path('images/profiles'), $imageName);
        $user->profile_image = 'images/profiles/' . $imageName;
        $user->save();

        return response()->json([
            'message' => 'Profile image uploaded successfully',
            'profile_image' => $user->profile_image
        ]);
    }

    return response()->json([
        'message' => 'No image file found'
    ], 400);    
}

public function store(Request $request)
{
   
    $userId = Auth::id(); 


    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:4',
        'phone_number' => 'required|string',
        'user_type' => 'required|in:tenant,landlord,admin',
        'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'id_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    
    $userData = [
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'phone_number' => $request->phone_number,
        'user_type' => $request->user_type,
    ];

    
    if ($request->hasFile('photo')) {
        $photoPath = $request->file('photo')->store('photos', 'public');
        $userData['photo'] = $photoPath;
    }

    if ($request->hasFile('id_photo')) {
        $idPhotoPath = $request->file('id_photo')->store('id_photos', 'public');
        $userData['id_photo'] = $idPhotoPath;
    }

    $user = User::create($userData);

    return response()->json([
        'message' => 'User created successfully',
        'user' => $user
    ], 201);
}
public function editIdPhoto(Request $request, $id)
{
    $user = User::findOrFail($id);

    $request->validate([
        'id_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    if ($request->hasFile('id_photo')) {
        $idPhoto = $request->file('id_photo');
        $idPhotoName = time() . '_' . $idPhoto->getClientOriginalName();
        $idPhoto->move(public_path('images/id_photos'), $idPhotoName);
        $user->id_photo = 'images/id_photos/' . $idPhotoName;
        $user->save();

        return response()->json([
            'message' => 'ID photo updated successfully',
            'id_photo' => $user->id_photo
        ]);
    }

    return response()->json([
        'message' => 'No ID photo file found'
    ], 400);

}
public function editPhoto(Request $request, $id)
{
    $user = User::findOrFail($id);

    $request->validate([
        'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    if ($request->hasFile('photo')) {
        $photo = $request->file('photo');
        $photoName = time() . '_' . $photo->getClientOriginalName();
        $photo->move(public_path('images/photos'), $photoName);
        $user->photo = 'images/photos/' . $photoName;
        $user->save();

        return response()->json([
            'message' => 'Photo updated successfully',
            'photo' => $user->photo
        ]);
    }

    return response()->json([
        'message' => 'No photo file found'
    ], 400);    }




}