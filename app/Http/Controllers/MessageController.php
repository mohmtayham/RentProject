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
            'receiver_id' => 'required|integer|exists:users,id',
            'content' => 'required|string',
        ]);
        
        $message = Message::create([
            'sender_id' => $request->sender_id,
            'receiver_id' => $request->receiver_id,
            'content' => $request->body,
        ]);
        
        return new MessageResource($message);
    }

    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $messages = Message::all();
        return MessageResource::collection($messages);
    }
    public function showUserMessages($userId): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $messages = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->get();
        
        return MessageResource::collection($messages);
    }
}