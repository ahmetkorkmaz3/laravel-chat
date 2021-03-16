<?php

namespace App\Http\Controllers;

use App\Http\Requests\Message\StoreMessageRequest;
use App\Http\Resources\Message\MessageResource;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MessageController extends Controller
{
    /**
     * @param Conversation $conversation
     * @return AnonymousResourceCollection
     */
    public function index(Conversation $conversation): AnonymousResourceCollection
    {
        $messages = Message::where('conversation_id', $conversation->id)
            ->with(['conversation', 'user'])
            ->orderBy('created_at', 'DESC')
            ->get();

        return MessageResource::collection($messages);
    }

    /**
     * @param StoreMessageRequest $request
     * @param Conversation $conversation
     * @return MessageResource
     */
    public function store(StoreMessageRequest $request, Conversation $conversation): MessageResource
    {
        $message = $conversation->messages()->create($request->validated());

        return MessageResource::make($message);
    }
}
