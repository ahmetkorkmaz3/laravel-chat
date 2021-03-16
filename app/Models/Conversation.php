<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

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

    public function receiverUser(): Collection
    {
        return $this->users()->whereNotIn('user_id', [auth()->user()->id])->get();
    }
}
