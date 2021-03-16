<?php

namespace App\Http\Controllers;

use App\Http\Requests\Conversation\StoreConversationRequest;
use App\Http\Requests\Conversation\UpdateConversationRequest;
use App\Http\Resources\Conversation\ConversationResource;
use App\Models\Conversation;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ConversationController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return ConversationResource::collection(auth()->user()->conversations);
    }

    /**
     * @param StoreConversationRequest $request
     * @return ConversationResource
     */
    public function store(StoreConversationRequest $request): ConversationResource
    {
        $conversation = Conversation::create($request->validated());
        $conversation->users()->sync([auth()->user()->id, $request->to_user_id]);

        return ConversationResource::make($conversation);
    }

    /**
     * @param Conversation $conversation
     * @return ConversationResource
     */
    public function show(Conversation $conversation): ConversationResource
    {
        $conversation = Conversation::where('id', $conversation->id)->with(['messages', 'users'])->get();

        return ConversationResource::make($conversation);
    }

    /**
     * @param UpdateConversationRequest $request
     * @param Conversation $conversation
     * @return ConversationResource
     */
    public function update(UpdateConversationRequest $request, Conversation $conversation): ConversationResource
    {
        $conversation->update($request->validated());

        return ConversationResource::make($conversation);
    }

    /**
     * @param Conversation $conversation
     * @return Response
     */
    public function destroy(Conversation $conversation): Response
    {
        try {
            $conversation->delete();
        } catch (\Exception $exception) {
            abort(500, $exception);
        }

        return response()->noContent();
    }
}
