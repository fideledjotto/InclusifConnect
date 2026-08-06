<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'icon', 'description'];

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function publishedDocuments()
    {
        return $this->hasMany(Document::class)->where('status', 'publie');
    }
}
