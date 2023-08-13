<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JwtToken extends Model
{
    use HasFactory;

    protected $fillable = [
      'unique_id',
      'user_id',
      'token_title',
      'expires_at',
      'last_used_at',
      'refreshed_at'
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
