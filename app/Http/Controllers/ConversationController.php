<?php

namespace App\Http\Controllers;

use App\Http\Requests\Conversation\StoreConversationRequest;
use App\Http\Requests\Conversation\UpdateConversationRequest;
use App\Http\Resources\Conversation\ConversationResource;
use App\Models\Conversation;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use PHPUnit\Util\Exception;

class ConversationController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $conversations = Conversation::whereHas('users', function ($query) {
            $query->where('user_id', auth()->user()->id);
        })->with(['messages' => function ($query) {
            $query->orderBy('created_at', 'DESC')->first();
        }])->orderBy('created_at', 'DESC')->get();

        return ConversationResource::collection($conversations);
    }

    /**
     * @param StoreConversationRequest $request
     * @return ConversationResource
     */
    public function store(StoreConversationRequest $request): ConversationResource
    {
        DB::beginTransaction();
        try {
            $conversation = Conversation::create($request->validated());
            $users = $request->to_user_id;
            array_push($users, auth()->user()->id);
            $conversation->users()->attach($users);
        } catch (Exception $exception) {
            DB::rollBack();
            abort(500, $exception);
        }
        DB::commit();

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
