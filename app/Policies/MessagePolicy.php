<?php

namespace App\Policies;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MessagePolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @param Conversation $conversation
     * @return bool
     */
    public function view(User $user, Conversation $conversation): bool
    {
        return $this->queryUserInConversation($user, $conversation);
    }

    /**
     * @param User $user
     * @param Conversation $conversation
     * @return bool
     */
    public function create(User $user, Conversation $conversation): bool
    {
        return $this->queryUserInConversation($user, $conversation);
    }

    public function queryUserInConversation(User $user, Conversation $conversation): bool
    {
        if ($conversation->relationLoaded('users')) {
            return $this->isEagerLoadingUsers($user, $conversation);
        }
        return $this->isEloquentUsers($user, $conversation);
    }

    private function isEagerLoadingUsers(User $user, Conversation $conversation): bool
    {
        return $conversation->users
                ->where('user_id', $user->id)
                ->count() > 0;
    }

    private function isEloquentUsers(User $user, Conversation $conversation): bool
    {
        return $conversation->users()
            ->where('user_id', $user->id)
            ->exists();
    }
}
