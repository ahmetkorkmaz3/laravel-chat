<?php

namespace App\Http\Controllers;

use App\Events\MessageEvent;
use App\Http\Requests\Message\StoreMessageRequest;
use App\Http\Resources\Notification\ConversationResource;
use App\Http\Resources\Message\MessageResource;
use App\Models\Conversation;
use App\Models\Message;
use App\Notifications\MessageNotification;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Notification;

class MessageController extends Controller
{
    /**
     * @param Conversation $conversation
     * @return AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(Conversation $conversation): AnonymousResourceCollection
    {
        $this->authorize('view', [Message::class, $conversation]);

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
     * @throws AuthorizationException
     */
    public function store(StoreMessageRequest $request, Conversation $conversation): MessageResource
    {
        $this->authorize('create', [Message::class, $conversation]);

        $message = $conversation->messages()->create(array_merge(
            $request->validated(),
            ['user_id' => auth()->user()->id]
        ));

        $receiverUser = $conversation->receiverUser();

        if ($conversation->is_group) {
            $receiverUser->each(function ($user) use ($message) {
                event(new MessageEvent(MessageResource::make($message), $user->id));
            });
        } else {
            event(new MessageEvent(MessageResource::make($message), $receiverUser->id));
        }

        $message->load('user');

        Notification::send(
            $receiverUser,
            new MessageNotification(
                ConversationResource::make($conversation),
                MessageResource::make($message))
        );

        return MessageResource::make($message);
    }
}
