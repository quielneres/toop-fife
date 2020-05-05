<?php

namespace App\Services;


use App\CreditCard;
use App\User;

class UserService
{

    public function getUser($id)
    {
        $user_obj = User::query()
            ->select(
                'users.*',
                'c.id_comprador'
            )
            ->leftJoin('clients as c', 'c.id_usuario', '=', 'users.id')
            ->where('users.id', $id)
            ->firstOrfail();

        $cc = CreditCard::where('id_user', $user_obj->id)->first();

        return [
            'user'=> $user_obj,
                'cc' =>  $cc ? json_decode($cc->json)->creditCard : []
        ];
    }

}
