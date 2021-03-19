<?php

namespace App\Http\Controllers;

use App\Events\ConversationEvent;
use App\Http\Requests\Conversation\StoreConversationRequest;
use App\Http\Requests\Conversation\UpdateConversationRequest;
use App\Http\Resources\Conversation\ConversationResource;
use App\Http\Resources\User\UserResource;
use App\Models\Conversation;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PHPUnit\Util\Exception;

class ConversationController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Conversation::class);

        $conversations = Conversation::with('latestMessage')
            ->withReceiverUsers()
            ->orderBy('created_at', 'DESC')
            ->get();

        return ConversationResource::collection($conversations);
    }

    /**
     * @param StoreConversationRequest $request
     * @return ConversationResource
     * @throws AuthorizationException
     */
    public function store(StoreConversationRequest $request): ConversationResource
    {
        $this->authorize('create', Conversation::class);

        DB::beginTransaction();
        try {
            $authUser = auth()->user();
            $conversation = Conversation::create($request->validated());

            $users = $request->to_user_id;
            array_push($users, $authUser->id);
            $conversation->users()->attach($users);

            foreach ($request->to_user_id as $userId) {
                event(new ConversationEvent(
                    ConversationResource::make($conversation),
                    UserResource::make($authUser),
                    $userId
                ));
            }
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
     * @throws AuthorizationException
     */
    public function show(Conversation $conversation): ConversationResource
    {
        $this->authorize('view', $conversation);

        $conversation->load(['users', 'messages']);

        return ConversationResource::make($conversation);
    }

    /**
     * @param UpdateConversationRequest $request
     * @param Conversation $conversation
     * @return ConversationResource
     * @throws AuthorizationException
     */
    public function update(UpdateConversationRequest $request, Conversation $conversation): ConversationResource
    {
        $this->authorize('update', $conversation);

        DB::beginTransaction();
        try {
            $conversation->update($request->validated());
            if ($request->has('to_user_id')) {
                $conversation->users()->syncWithoutDetaching($request->to_user_id);
            }
        } catch (Exception $exception) {
            DB::rollBack();
            abort(500, 'Somethings wrong');
        }
        DB::commit();

        return ConversationResource::make($conversation);
    }

    /**
     * @param Conversation $conversation
     * @return Response
     * @throws AuthorizationException
     */
    public function destroy(Conversation $conversation): Response
    {
        $this->authorize('delete', $conversation);

        try {
            $conversation->delete();
        } catch (\Exception $exception) {
            abort(500, $exception);
        }

        return response()->noContent();
    }
}
