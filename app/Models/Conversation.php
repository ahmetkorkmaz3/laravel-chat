<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Conversation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'is_group',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($conversation) {
            $conversation->messages()->delete();
            $conversation->users()->detach();
        });
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * @return Collection|mixed|null
     */
    public function receiverUser()
    {
        if ($this->is_group) {
            return $this->users()->whereNotIn('user_id', [auth()->user()->id])->get();
        }
        return $this->users()->where('user_id', '!=', auth()->user()->id)->first();
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(Message::class)->latest();
    }

    public function scopeWithReceiverUsers($query)
    {
        return $query->with('users', function ($query) {
            $query->whereNotIn('user_id', [auth()->user()->id]);
        });
    }
}
