<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // extends Authenticatable to allow auth features
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'city',
        'address',
        'password',
        'api_token',
    ];

    protected $hidden = [
        'password',
        'api_token',
    ];

     /**
     * The attributes that should be cast.
     */
    
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
