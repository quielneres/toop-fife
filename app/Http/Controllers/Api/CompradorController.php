<?php

namespace App\Http\Controllers\Api;

use App\Client;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Moip\Auth\BasicAuth;
use Moip\Moip;

class CompradorController extends Controller
{
    public $moip;

    public function __construct()
    {
        $token      = '60FAPH4GGLJGUM4MFW8CUSEFNNAJT5SC';
        $key        = 'UWNSJ0UYVTWH41GSKCTC4UWDVDLVEH8XSGBCMROS';
        $this->moip = new Moip(new BasicAuth($token, $key), Moip::ENDPOINT_SANDBOX);
    }

    public function novoComprador(Request $request)
    {
        $params = $request->get('itens');

        $nome       = $params[0]['nome'];
        $email      = $params[0]['email'];
        $nascimento = $params[0]['nascimento'];
        $cpf        = $params[0]['cpf'];
        $telefone   = $params[0]['telefone'];
        $endereco   = $params[0]['endereco'];

        try {
            $customer = $this->moip->customers()->setOwnId(uniqid())
                ->setFullname($nome)
                ->setEmail($email)
                ->setBirthDate($nascimento)
                ->setTaxDocument($cpf)
                ->setPhone(11, 66778899)
                ->addAddress('BILLING',
                    $endereco['street'], $endereco['number'],
                    $endereco['district'], $endereco['city'], $endereco['state'],
                    $endereco['zipe'], $endereco['complement'])
                ->addAddress('SHIPPING',
                    'Rua de teste do SHIPPING', 123,
                    'Bairro do SHIPPING', 'Sao Paulo', 'SP',
                    '01234567', 8)
                ->create();

            $comprador               = new Client();
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
