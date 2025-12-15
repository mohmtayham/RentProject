<?php

namespace App\Http\Controllers;

use App\Http\Resources\MessageResource;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken; // Add this import

class MessageController extends Controller
{
 

    public function store(Request $request)
    {
        // Get authenticated user ID (requires proper Sanctum setup)
        $userId = Auth::id();
        
        if (!$userId) {
            return response()->json([
                'error' => 'Unauthenticated',
                'message' => 'User not authenticated.'
            ], 401);
        }
        
        // Validate request
        $request->validate([
            'recipient_id' => 'required|integer|exists:users,id',
            'body' => 'required|string|max:1000',
        ]);
        
        // Create message
        $message = Message::create([
            'sender_id' => $userId,
            'recipient_id' => $request->recipient_id,
            'body' => $request->body,
        ]);
        
        return new MessageResource($message);
    }

 
    // Other methods...

 
//  public function store(Request $request): MessageResource
// {
//     $userId = Auth::id();
    
//     $request->validate([
//         'recipient_id' => 'required|integer|exists:users,id',
//         'body' => 'required|string|max:1000',
//     ], [
//         'recipient_id.required' => 'Please provide a valid recipient ID',
//         'body.required' => 'Message body is required'
//     ]);
    
//     $message = Message::create([
//         'sender_id' => $userId, 
//         'recipient_id' => $request->recipient_id,
//         'body' => $request->body,
//     ]);
    
//     return new MessageResource($message);
// }
    public function index()
    {
        $messages = Message::all();
        return MessageResource::collection($messages);
    }
    public function showUserMessages($userId)
    {
        $messages = Message::where('sender_id', $userId)
            ->orWhere('recipient_id', $userId)
            ->get();
      
        return MessageResource::collection($messages);
    }
    public function destory($id)
    {
        $message = Message::findOrFail($id);    
        $message->delete();
        return response()->json(['message' => 'Message deleted successfully.']);
    }
}