<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'content',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'published_at'  => 'datetime',
    ];

    /**
     * Get the post's writer.
     */
    public function writer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'writer_id');
    }

    /**
     * Get the post's comments.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
