<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Recarga extends Authenticatable
{
    use Notifiable;

    protected $table = 'recarga';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'id_usuario',
        'id_pedido',
        'nu_celular',
        'operador',
        'valor',
        'created_at',
        'updated_at',
    ];


}
