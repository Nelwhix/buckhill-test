<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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

    public static function createToken(string $userId, bool $isAccessToken ): self {
        return self::create([
            'unique_id' => (string) Str::uuid(),
            'user_id' => $userId,
            'token_title' => $isAccessToken ? 'access token' : 'refresh token',
            'expires_at' =>  $isAccessToken ? now()->addMinutes(15) : now()->addDay(),
        ]);
    }
}
