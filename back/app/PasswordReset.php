<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    protected $filliable = [
        'email', 'token',
    ];
}
