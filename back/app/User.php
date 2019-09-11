<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @SWG\Definition(
 *      definition="User",
 *      required={"name", "email", "password"},
 *      @SWG\Property(
 *          property="name",
 *          type="string",
 *          description="Nombre de usuario",
 *          example="John Conor"
 *      ),
 *      @SWG\Property(
 *          property="email",
 *          type="string",
 *          description="Dirección de correo electrónico",
 *          example="john.conor@terminator.com"
 *      ),
 *      @SWG\Property(
 *          property="password",
 *          type="string",
 *          description="Una contraseña muy segura",
 *          example="123456"
 *      )
 * )
 */
class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
