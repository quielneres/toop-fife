<?php

namespace App\Services;


use App\User;
use Moip\Auth\BasicAuth;
use Moip\Moip;

class ClientService
{

    public $moip;

    public function __construct()
    {
        $token      = '60FAPH4GGLJGUM4MFW8CUSEFNNAJT5SC';
        $key        = 'UWNSJ0UYVTWH41GSKCTC4UWDVDLVEH8XSGBCMROS';
        $this->moip = new Moip(new BasicAuth($token, $key), Moip::ENDPOINT_SANDBOX);
    }

    public function registerClient(User $user)
    {


        $name          = $user->name;
        $email         = $user->email;
        $birth         = $user->birth;
        $cpf           = $user->cpf;
        $street        = $user->street;
        $number_adress = $user->number_adress;
        $district      = $user->district;
        $city          = $user->city;
        $state         = $user->state;
        $zipe_code     = $user->zipe_code;
        $complement    = $user->complement;
        $ddd    = substr($user->phone_number, 0, 2);
        $phone_number = substr($user->phone_number, 2);


        try {
            $customer = $this->moip->customers()->setOwnId(uniqid())
                ->setFullname($name)
                ->setEmail($email)
                ->setBirthDate($birth)
                ->setTaxDocument($cpf)
                ->setPhone(intval($ddd), intval($phone_number))
                ->addAddress('BILLING',
                    $street, $number_adress,
                    $district, $city, $state,
                    $zipe_code, $complement)
                ->addAddress('SHIPPING',
                    'Rua de teste do SHIPPING', 123,
                    'Bairro do SHIPPING', 'Brasilia', 'DF',
                    '70170400', 8)
                ->create();

            return $customer;
        } catch (\Exception $exception) {
            return response()->json($exception->getMessage());
        }
    }

}
