<?php

namespace App\Http\Controllers\Api;

use App\Comprador;
use App\CreditCard;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
        $id_client = null;

        $validator = Validator::make($request->all(), [
            'cpf_client' => 'required',
        ]);

        if ($validator->fails()) {

            return response()->json($validator->errors(), 400);
        }

        $client = Comprador::where('json', 'LIKE', "%{$request->get('cpf_client')}%")->first();

        if (!$client) {

            $customer = $this->registerClient($request);
            if ($customer['status']) {
                $id_client = $customer['id_client'];
            } else {
                return response()->json($customer);
            }
        } else {
            $id_client = $client->id_comprador;
        }

        try {

            $data_nasci = date('Y-m-d', strtotime(str_replace('/', '-', $request->get('birth_client'))));
            $expiretion = explode('/', $request->get('expiration'));

            $number       = $request->get('number_card');
            $cvc          = $request->get('cvc');
            $nameTitular  = $request->get('name_holder');
            $birthDate    = $data_nasci;
            $cpf          = $request->get('cpf_client');
            $ddd          = intval($request->get('ddd'));
            $phone_number = intval($request->get('phone_number'));

            $customer_card = $this->moip->customers()->creditCard()
                ->setExpirationMonth($expiretion[0])
                ->setExpirationYear($expiretion[1])
                ->setNumber($number)
                ->setCVC($cvc)
                ->setFullName($nameTitular)
                ->setBirthDate($birthDate)
                ->setTaxDocument('CPF', $cpf)
                ->setPhone('55', $ddd, $phone_number)
                ->create($id_client);

            $credit_cards             = new CreditCard();
            $credit_cards->id_user    = 1;
            $credit_cards->id_cliente = $id_client;
            $credit_cards->id_card    = $customer_card->getId();
            $credit_cards->json       = json_encode($customer_card);
            $credit_cards->save();

            return response()->json(['status' => true, 'message' => 'Cartao cadastrado com sucesso']);
        } catch (\Exception $exception) {
            return response()->json(['status' => false, 'message' => 'Erro ao cadastrar cartao']);
        }
    }


    public function registerClient($data)
    {

        $data_nasci = date('Y-m-d', strtotime(str_replace('/', '-', $data->get('birth_client'))));

        $nome         = $data->get('name_client');
        $email        = $data->get('email_client');
        $nascimento   = $data_nasci;
        $cpf          = $data->get('cpf_client');
        $street       = $data->get('street');
        $number       = $data->get('adress_nuumber');
        $district     = $data->get('district');
        $city         = $data->get('city');
        $state        = $data->get('state_adress');
        $complement   = $data->get('complement');
        $zipe         = $data->get('zip_code');
        $ddd          = intval($data->get('ddd'));
        $phone_number = intval($data->get('phone_number'));

        try {
            $customer = $this->moip->customers()->setOwnId(uniqid())
                ->setFullname($nome)
                ->setEmail($email)
                ->setBirthDate($nascimento)
                ->setTaxDocument($cpf)
                ->setPhone($ddd, $phone_number)
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

            return ['status' => true, 'id_client' => $customer->getId()];
        } catch (\Exception $exception) {
            return ['status' => false, 'message' => 'Error ao salvar cliente'];
        }
    }

    public function listCreditCards($id_user)
    {
        $cards = CreditCard::where('id_user', $id_user)->get()->toArray();
        $list_cards=[];

        foreach ($cards as $card){
            $list_cards[] = [
                'id_card' => json_decode($card['json'])->creditCard->id,
                'flag' => json_decode($card['json'])->creditCard->brand,
                'dig_start' => json_decode($card['json'])->creditCard->first6,
                'dig_end' => json_decode($card['json'])->creditCard->last4,
                'status' => $card['status']
            ];
        }


        return response()->json(['data' => $list_cards]);
    }
}
