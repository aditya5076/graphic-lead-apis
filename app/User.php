<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Traits\HasJWT;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable, HasJWT;

    protected $primaryKey = 'userid';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'firstname', 'email', 'password', 'lastname', 'company', 'active', 'lastlogin', 'codecount'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
}
