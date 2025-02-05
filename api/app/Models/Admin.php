<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable 
{
    use HasApiTokens, HasApiTokens;
    //
    protected $fillable = [
        'name',
        'staffNo',
        'idNo',
        'email',
        'password'
    ];
}
