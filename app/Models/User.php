<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'newsletter_opt_in',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'newsletter_opt_in' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'administrateur';
    }

    public function favorites()
    {
        return $this->belongsToMany(Document::class, 'favorites')->withTimestamps();
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'submitted_by');
    }
}
