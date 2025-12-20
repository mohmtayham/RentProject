<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
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
    $userId = Auth::id();
    
    if (!$userId) {
        return response()->json(['error' => 'Unauthenticated'], 401);
    }

    $request->validate([
        'recipient_id' => 'required|integer|exists:users,id|different:sender_id',
        'body' => 'required|string|max:1000',
    ]);

    $message = Message::create([
        'sender_id' => $userId,
        'recipient_id' => $request->recipient_id,
        'body' => $request->body,
    ]);

   
    broadcast(new MessageSent($message))->toOthers();

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