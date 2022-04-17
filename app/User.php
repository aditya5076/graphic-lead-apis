<?php

namespace App;

<<<<<<< HEAD
// use App\Traits\HasJWT;
=======
>>>>>>> 370aab338a16c0e3ae917c10e17f67b3c7e31b14
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
<<<<<<< HEAD
use Laravel\Sanctum\HasApiTokens;
=======
use App\Traits\HasJWT;
>>>>>>> 370aab338a16c0e3ae917c10e17f67b3c7e31b14

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

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
