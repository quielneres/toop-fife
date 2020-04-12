<?php

namespace App\Http\Controllers\Api;

use App\Comprador;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Moip\Auth\BasicAuth;
use Moip\Moip;

class PaymentController extends Controller
{
    public $moip;

    public function __construct()
    {
        $token      = '60FAPH4GGLJGUM4MFW8CUSEFNNAJT5SC';
        $key        = 'UWNSJ0UYVTWH41GSKCTC4UWDVDLVEH8XSGBCMROS';
        $this->moip = new Moip(new BasicAuth($token, $key), Moip::ENDPOINT_SANDBOX);
    }

    public function register(Request $request)
    {
        $data      = $request->all();
        $id_client = null;

        $client = Comprador::where('json', 'LIKE', "%{$data['items']['cpf']}%")->first();

        if (!$client) {
            $customer  = $this->registerClient($data);
            $id_client = $customer->getId();
        } else {
            $id_client = $client->id_comprador;
        }

        try {

            $params = $data['items'];
            $number      = $params['numberCard'];
            $cvc         = $params['cvc'];
            $nameTitular = $params['nameTitular'];
            $birthDate   = $params['birth'];
            $cpf         = $params['cpf'];

            $customer = $this->moip->customers()->creditCard()
                ->setExpirationMonth('05')
                ->setExpirationYear(2022)
                ->setNumber($number)
                ->setCVC($cvc)
                ->setFullName($nameTitular)
                ->setBirthDate($birthDate)
                ->setTaxDocument('CPF', $cpf)
                ->setPhone('55', '11', '66778899')
                ->create($id_client);

            return response()->json($customer);
        } catch (\Exception $exception) {
            return response()->json($exception->getMessage());
        }

    }


    public function registerClient($data)
    {
        $params = $data['items'];

        $nome       = $params['name'];
        $email      = $params['email'];
        $nascimento = $params['birth'];
        $cpf        = $params['cpf'];
        $street     = $params['street'];
        $number     = $params['adressNumber'];
        $district   = $params['district'];
        $city       = $params['city'];
        $state      = $params['stateAdress'];
        $complement = $params['complement'];
        $zipe       = $params['cep'];

        try {
            $customer = $this->moip->customers()->setOwnId(uniqid())
                ->setFullname($nome)
                ->setEmail($email)
                ->setBirthDate($nascimento)
                ->setTaxDocument($cpf)
                ->setPhone(11, 66778899)
                ->addAddress('BILLING',
                    $street, $number,
                    $district, $city, $state,
                    $zipe, $complement)
                ->addAddress('SHIPPING',
                    'Rua de teste do SHIPPING', 123,
                    'Bairro do SHIPPING', 'Sao Paulo', 'SP',
                    '01234567', 8)
                ->create();

            $comprador               = new Comprador();
            $comprador->id_usuario   = 1;
            $comprador->id_comprador = $customer->getId();
            $comprador->json         = json_encode($customer);
            $comprador->save();

            return response()->json($customer);
        } catch (\Exception $exception) {
            return response()->json($exception->getMessage());
        }
    }
}
