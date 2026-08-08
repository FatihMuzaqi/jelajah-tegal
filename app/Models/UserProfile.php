<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserProfile extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $fillable = ['user_id', 'avatar_media_id', 'birth_date', 'gender', 'notification_preferences'];

    protected function casts(): array
    {
        return ['birth_date' => 'date', 'notification_preferences' => 'array'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function avatar(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'avatar_media_id');
    }
}
