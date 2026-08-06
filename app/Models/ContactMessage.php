<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = ['name', 'email', 'subject', 'message', 'is_resolved'];

    protected function casts(): array
    {
        return ['is_resolved' => 'boolean'];
    }
}
