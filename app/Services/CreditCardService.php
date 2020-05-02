<?php

namespace App\Services;

use App\User;
use Moip\Auth\BasicAuth;
use Moip\Moip;

class CreditCardService
{

    public $moip;

    public function __construct()
    {
        $token      = '60FAPH4GGLJGUM4MFW8CUSEFNNAJT5SC';
        $key        = 'UWNSJ0UYVTWH41GSKCTC4UWDVDLVEH8XSGBCMROS';
        $this->moip = new Moip(new BasicAuth($token, $key), Moip::ENDPOINT_SANDBOX);
    }

    public function saveCard($data, User $user)    {

        try {
            $expiretion = explode('/', $data->get('expiration'));

            $number       = $data->request->getDigits('number_card');
            $cvc          = $data->get('cvc');
            $nameTitular  = $data->get('name_holder');
            $cpf          = $user->cpf;

            $ddd    = substr($user->phone_number, 0, 2);
            $phone_number = substr($user->phone_number, 2);
            $birthDate = date('Y-m-d', strtotime($user->birth));

            $customer_card = $this->moip->customers()->creditCard()
                ->setExpirationMonth($expiretion[0])
                ->setExpirationYear($expiretion[1])
                ->setNumber($number)
                ->setCVC($cvc)
                ->setFullName($nameTitular)
                ->setBirthDate($birthDate)
                ->setTaxDocument('CPF', $cpf)
                ->setPhone('55', intval($ddd), intval($phone_number))
                ->create($user->countClient->id_comprador);

            return $customer_card;
        } catch (\Exception $exception) {
            return response(false);
        }
    }

}
