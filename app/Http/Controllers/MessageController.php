<?php

namespace App\Http\Controllers;

use App\Http\Resources\MessageResource; // 
use App\Models\Message; //
use Illuminate\Http\Request; // 

class MessageController extends Controller
{
  public function store(Request $request): MessageResource
{
  $request->validate([
    'sender_id' => 'required|integer|exists:users,id',
    'recipient_id' => 'required|integer|exists:users,id',
    'body' => 'required|string|max:1000',
], [
    'sender_id.required' => 'Please provide a valid sender ID',
    'recipient_id.required' => 'Please provide a valid recipient ID',
    'body.required' => 'Message body is required'
]);
    
    $message = Message::create([
        'sender_id' => $request->sender_id,
        'recipient_id' => $request->recipient_id, // Changed from receiver_id
        'body' => $request->body,
    ]);
    
    return new MessageResource($message);
}

    public function index()
    {
        $messages = Message::all();
        return MessageResource::collection($messages);
    }
    public function showUserMessages($userId)
    {
        $messages = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
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