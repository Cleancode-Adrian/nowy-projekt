<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'title',
        'slug',
        'route_name', // dla linków do istniejących route (np. blog.index, faq)
        'external_url', // dla linków zewnętrznych
        'content',
        'meta_title',
        'meta_description',
        'is_active',
        'is_system', // dla stron systemowych jak polityka prywatności, regulamin
        'order',
        'show_in_menu',
        'menu_position', // 'footer', 'header', 'both'
        'menu_order',
        'icon', // ikona FontAwesome dla menu
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_system' => 'boolean',
        'show_in_menu' => 'boolean',
        'order' => 'integer',
        'menu_order' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($page) {
            if (empty($page->slug)) {
                $page->slug = Str::slug($page->title);
            }
        });

        static::updating(function ($page) {
            if ($page->isDirty('title') && empty($page->slug)) {
                $page->slug = Str::slug($page->title);
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('title');
    }

    public function scopeInMenu($query, $position = null)
    {
        $query->where('show_in_menu', true)
              ->where('is_active', true)
              ->whereNull('parent_id'); // tylko główne elementy menu (bez podmenu)
        
        if ($position) {
            $query->where(function($q) use ($position) {
                $q->where('menu_position', $position)
                  ->orWhere('menu_position', 'both');
            });
        }
        
        return $query->orderBy('menu_order')->orderBy('title');
    }

    public function parent()
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Page::class, 'parent_id')->where('is_active', true)->orderBy('menu_order')->orderBy('title');
    }

    public function getUrlAttribute()
    {
        if ($this->external_url) {
            return $this->external_url;
        }
        
        if ($this->route_name && \Route::has($this->route_name)) {
            return route($this->route_name);
        }
        
        if ($this->slug === 'polityka-prywatnosci') {
            return route('privacy-policy');
        }
        
        if ($this->slug === 'regulamin') {
            return route('terms-of-service');
        }
        
        return route('page.show', $this->slug);
    }
}

