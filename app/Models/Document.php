<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Document extends Model
{
    protected $fillable = [
        'title', 'slug', 'description', 'type', 'category_id', 'author',
        'file_path', 'external_url', 'keywords', 'status',
        'views_count', 'downloads_count', 'submitted_by', 'reviewed_by', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'keywords' => 'array',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Document $document) {
            if (empty($document->slug)) {
                $base = Str::slug($document->title);
                $slug = $base;
                $i = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $base.'-'.(++$i);
                }
                $document->slug = $slug;
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'publie');
    }
}
