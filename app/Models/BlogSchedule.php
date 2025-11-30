<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogSchedule extends Model
{
    protected $fillable = [
        'is_enabled',
        'time',
        'frequency',
        'count',
        'topics',
        'category_id',
        'tags',
        'download_image',
        'auto_publish',
        'last_run_at',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'download_image' => 'boolean',
        'auto_publish' => 'boolean',
        'last_run_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
