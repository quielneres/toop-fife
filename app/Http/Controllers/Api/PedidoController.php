<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Pedido;
use App\Recarga;
use Illuminate\Http\Request;
use Moip\Auth\BasicAuth;
use Moip\Moip;

class PedidoController extends Controller
{
    public $moip;

    public function __construct()
    {
        $token      = '60FAPH4GGLJGUM4MFW8CUSEFNNAJT5SC';
        $key        = 'UWNSJ0UYVTWH41GSKCTC4UWDVDLVEH8XSGBCMROS';
        $this->moip = new Moip(new BasicAuth($token, $key), Moip::ENDPOINT_SANDBOX);
    }


    public function newResquest(Request $request)
    {
        $id_comprador     = $request->get('id_comprador');
        $valor            = $request->get('valor');
        $preco            = $request->get('preco');
        $quantidade       = $request->get('quantidade');
        $descricao_pedido = $request->get('descricao');
        $detalhes         = $request->get('detalhes');

        try {
            $order = $this->moip->orders()->setOwnId(uniqid())
                ->addItem($descricao_pedido, $quantidade, $detalhes, intval($preco))
                ->setShippingAmount(intval($valor))->setAddition(0)->setDiscount(0)
                ->setCustomerId($id_comprador)
                ->create();

            $pedido               = new Pedido();
            $pedido->id_usuario   = 1;
            $pedido->id_comprador = $id_comprador;
            $pedido->id_pedido    = $order->getId();
            $pedido->json         = json_encode($order);
            $pedido->save();

            if ($request->get('payment_method') == 1) {
                $payment = $this->boletoGeneration($order, $pedido);

                $recarga = new Recarga();
                $recarga->id_usuario = 1;
                $recarga->id_pedido = $order->getId();
                $recarga->nu_celular = $request->get('cell_number');
                $recarga->operador = $request->get('operadora_name');
                $recarga->valor = $request->get('valor');
                $recarga->link_boleto = $payment->getHrefBoleto();
                $recarga->save();

                return response()->json(['status' => true, 'link_boleto' => $payment->getHrefBoleto()]);
            }

            return response()->json(['status' => true]);


        } catch (\Exception $e) {
            return response()->json(['status' => false]);
        }
    }

    public function boletoGeneration($order, $pedido)
    {
        $logo_uri          = "http://www.lojaexemplo.com.br/logo.jpg";
        $expiration_date   = (new \DateTime('+5 day'))->format('Y-m-d');
        $instruction_lines = [
            "Atenção,",                                         //First
            "fique atento à data de vencimento do boleto.",     //Second
            "Pague em qualquer casa lotérica."                  //Third
        ];

        $payment = $order->payments()
            ->setBoleto($expiration_date, $logo_uri, $instruction_lines)
            ->setStatementDescriptor("Pag Toop")
            ->execute();

        return $payment;
    }

    public function meusPedidos()
    {
        $order_id = 'ORD-7RD3AVDW66I6';

        try {
            $order = $this->moip->orders()->get($order_id);
            return response()->json($order);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Errro interno']);
        }
    }

    public function pedidosLocal()
    {
        $pedidos = Pedido::query()->orderBy('id', 'desc')->limit(3)->get();
        $data    = [];
        foreach ($pedidos as $pedido) {
            $data[] = [
                'id'             => $pedido->id,
                'id_comprador'   => json_decode($pedido->json)->customer->id,
                'nome_comprador' => json_decode($pedido->json)->customer->fullname,
                'email'          => json_decode($pedido->json)->customer->email,
                'id_pedido'      => json_decode($pedido->json)->id,
                'cadastro'       => date('d/m/y H:i', strtotime(json_decode($pedido->json)->createdAt)),
                'total'          => number_format(json_decode($pedido->json)->amount->total, 2, ',', '.')
            ];
        }
        return response()->json(['pedido' => $data]);
    }

    public function pedidoDetalhe($order_id)
    {
        try {
            $order = $this->moip->orders()->get($order_id);
            return response()->json($order);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Errro interno']);
        }
    }
}
