<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Requests\UpdateMessageRequest;

class MessageController extends Controller
{
    public function index()
    {
        return Message::paginate(20);
    }

    public function show(Message $message)
    {
        return $message;
    }

    public function store(StoreMessageRequest $request)
    {
        $message = Message::create($request->validated());
        return response()->json($message, 201);
    }

    public function update(UpdateMessageRequest $request, Message $message)
    {
        $message->fill($request->validated());
        $message->save();
        return $message;
    }

    public function destroy(Message $message)
    {
        $message->delete();
        return response()->noContent();
    }
}
