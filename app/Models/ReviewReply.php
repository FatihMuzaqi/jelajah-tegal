<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReviewReply extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $fillable = ['review_id', 'mitra_id', 'replied_by', 'body', 'status'];

    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(Mitra::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'replied_by');
    }
}
