<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Moip\Auth\BasicAuth;
use Moip\Moip;

class BoletoController extends Controller
{
    public $moip;

    public function __construct()
    {
        $token      = '60FAPH4GGLJGUM4MFW8CUSEFNNAJT5SC';
        $key        = 'UWNSJ0UYVTWH41GSKCTC4UWDVDLVEH8XSGBCMROS';
        $this->moip = new Moip(new BasicAuth($token, $key), Moip::ENDPOINT_SANDBOX);
    }


    public function boletoGenerate()
    {
        try {
            $order_id = 'ORD-IPFH3LVBJSTV';
            $order    = $this->moip->orders()->get($order_id);



            $logo_uri = "http://www.lojaexemplo.com.br/logo.jpg";
            $expiration_date = "2020-06-20";
            $instruction_lines = [
                "Atenção,",                                         //First
                "fique atento à data de vencimento do boleto.",     //Second
                "Pague em qualquer casa lotérica."                  //Third
            ];

            $payment = $order->payments()
                ->setBoleto($expiration_date, $logo_uri, $instruction_lines)
                ->setStatementDescriptor("Pag Toop")
                ->execute();

            return response()->json($payment);

        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }



    }
}
