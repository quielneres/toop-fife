<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Pedido extends Authenticatable
{
    use Notifiable;

    protected $table = 'pedidos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'id_usuario',
        'id_comprador',
        'id_pedido',
        'json',
        'created_at',
        'updated_at',
    ];


}
