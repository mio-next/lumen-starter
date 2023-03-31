<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTEntity
{
    use Authenticatable, Authorizable, HasFactory;

    /**
     * @var string[]
     */
    protected $hidden = ['password'];

    /**
     * @var array
     */
    protected $guarded = [];
}
