<?php

namespace App\Http\Controllers;

use App\Events\ConversationEvent;
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

        $conversation->messages()->update(['is_read' => true]);

        return MessageResource::collection($messages);
    }

    /**
     * @param StoreMessageRequest $request
     * @param Conversation $conversation
     * @return MessageResource
     */
    public function store(StoreMessageRequest $request, Conversation $conversation): MessageResource
    {
        $message = $conversation->messages()->create(array_merge(
            $request->validated(),
            ['user_id' => auth()->user()->id]
        ));

        if ($conversation->is_group) {
            $conversation->receiverUser()->each(function ($user) use ($message) {
                event(new ConversationEvent($message, $user->id));
            });
        } else {
            event(new ConversationEvent($message, $conversation->receiverUser()->id));
        }

        return MessageResource::make($message);
    }
}
