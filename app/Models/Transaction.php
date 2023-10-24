<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_card_id',
        'receiver_card_id',
        'amount',
        'status',
    ];

    protected $casts = [
        'amount' => 'integer',
        'status' => TransactionStatus::class,
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(Card::class, 'sender_card_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(Card::class, 'receiver_card_id');
    }

    public function fees(): HasMany
    {
        return $this->hasMany(Fee::class);
    }
}
