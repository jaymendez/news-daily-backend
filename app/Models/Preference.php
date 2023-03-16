<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Preference extends Model
{
    use HasFactory;

    protected $fillable = [
        'sources',
        'categories',
        'authors'
    ];

    protected $attributes = [
        'sources' => '',
        'categories' => '',
        'authors' => ''
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
