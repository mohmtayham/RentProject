<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResourcev;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use App\Models\Landlord;
use App\Models\Tenant;
use Illuminate\Support\Facades\Storage;


class UserController extends Controller
{
   public function register(Request $request)
{
    // Validate input (no 'status' needed)
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:4',
       'phone_number' => [
        'required',
        'string',
        'size:10',                   
        'regex:/^09[0-9]{8}$/',      
        
    ],
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

  //  $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'Registration successful. Awaiting admin approval.',
        'user' => $user->load($user->user_type),
     
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

    $validated = $request->validate([
        'name' => 'sometimes|required|string|max:255',
        'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
        'password' => 'sometimes|required|min:4',
        'phone_number' => 'sometimes|required|string',
        'user_type' => 'sometimes|required|in:tenant,landlord,admin',
    ]);

    // Handle password separately (hash it)
    if ($request->filled('password')) {
        $validated['password'] = Hash::make($request->password);
    }

    // Update user with validated data (including hashed password)
    $user->update($validated);

    return response()->json([
        'message' => 'User updated successfully',
        'user' => $user
    ]);
}
    //  public function update(UpdateTaskRequest $request, $id)
    // {
    //     try {
    //         $user_id = Auth::user()->id;
    //         $task = Task::findOrFail($id);
    //         if ($task->user_id != $user_id)
    //             return response()->json(['message' => 'Unauthorized'], 403);

    //         $task->update($request->validated());
    //         return response()->json($task, 200);
    //     } catch (ModelNotFoundException $e) {
    //         return response()->json(['error' => 'Task not found'], 404);
    //     } catch (Exception $e) {
    //         return response()->json(['error' => 'Something went wrong'], 500);
    //     }
    // }


public function store(Request $request)
{
   
    // $userId = Auth::id(); 


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



public function editPhotodbugallproblems(Request $request, $id)
{
    // Correct debug logging with proper PHP syntax:
    Log::info('=== EDIT PHOTO DEBUG ===');
    Log::info('User ID: ' . $id);
    Log::info('Request Method: ' . $request->method());
    Log::info('Content-Type: ' . $request->header('Content-Type'));
    Log::info('All Input: ' . json_encode($request->all()));
    Log::info('Files: ' . json_encode($request->allFiles()));
    Log::info('Has file photo? ' . ($request->hasFile('photo') ? 'YES' : 'NO'));
    
    if ($request->hasFile('photo')) {
        Log::info('File details:');
        $file = $request->file('photo');
        Log::info('  Name: ' . $file->getClientOriginalName());
        Log::info('  Size: ' . $file->getSize());
        Log::info('  MIME: ' . $file->getMimeType());

        return response()->json([
            'debug' => [
                'user_id' => $id,
                'method' => $request->method(),
                'has_file_photo' => $request->hasFile('photo'),
                'all_files' => array_keys($request->allFiles()),
                'all_input' => array_keys($request->all()),
                'content_type' => $request->header('Content-Type'),
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'file_mime' => $file->getMimeType()
            ]
        ]);
    }
    
    // If no file is detected, return error
    return response()->json([
        'error' => 'No file detected in request',
        'debug' => [
            'user_id' => $id,
            'has_file_photo' => false,
            'request_keys' => array_keys($request->all())
        ]
    ], 400);
}
public function editPhoto(Request $request, $id)
{


   
    try {
        
        // Find the user
        $user = User::findOrFail($id);
        
        // Validate the request
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        // Store the new photo
        if ($request->hasFile('photo')) {
            // Optional: Delete old photo if exists
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }
            
            $photoPath = $request->file('photo')->store('photos', 'public');
            $user->photo = $photoPath;
            $user->save();
        }
        
        return response()->json([
            'message' => 'Profile photo updated successfully',
            'user' => $user,
            'photo_url' => asset('storage/' . $user->photo) 
        ], 200);
        
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'message' => 'User not found'
        ], 404);
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);
        
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'An error occurred',
            'error' => $e->getMessage()
        ], 500);
    }
}


public function editIdPhoto(Request $request, $id)
{


   
    try {
        
        // Find the user
        $user = User::findOrFail($id);
        
        // Validate the request
        $request->validate([
            'id_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        // Store the new photo
        if ($request->hasFile('id_photo')) {
            // Optional: Delete old photo if exists
            if ($user->id_photo && Storage::disk('public')->exists($user->id_photo)) {
                Storage::disk('public')->delete($user->id_photo);
            }
            
            $id_photoPath = $request->file('id_photo')->store('id_photos', 'public');
            $user->id_photo = $id_photoPath;
            $user->save();
        }
        
        return response()->json([
            'message' => 'Profile photo updated successfully',
            'user' => $user,
            'id_photo_url' => asset('storage/' . $user->id_photo) 
        ], 200);
        
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'message' => 'User not found'
        ], 404);
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);
        
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'An error occurred',
            'error' => $e->getMessage()
        ], 500);
    }
}

}