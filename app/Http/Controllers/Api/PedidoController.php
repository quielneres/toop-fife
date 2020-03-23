<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Pedido;
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


    public function novoPedido(Request $request)
    {
        $params = $request->get('itens');

        $id_comprador     = $params[0]['id_comprador'];
        $valor            = $params[0]['valor'];
        $preco            = $params[0]['preco'];
        $quantidade       = $params[0]['quantidade'];
        $descricao_pedido = $params[0]['descricao'];
        $detalhes         = $params[0]['detalhes'];

        try{
            $order = $this->moip->orders()->setOwnId(uniqid())
                ->addItem($descricao_pedido, $quantidade, $detalhes, $preco)
                ->setShippingAmount($valor)->setAddition(0)->setDiscount(0)
                ->setCustomerId($id_comprador)
                ->create();

            $pedido               = new Pedido();
            $pedido->id_usuario   = 1;
            $pedido->id_comprador = $id_comprador;
            $pedido->id_pedido    = $order->getId();
            $pedido->json         = json_encode($order);
            $pedido->save();

            return response()->json(['pedido' => $order]);
        }catch (\Exception $e){
            return response()->json($e->getMessage());
        }

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
        $pedidos = Pedido::all();

        $data=[];
        foreach ($pedidos as $pedido){
            $data[] = json_decode($pedido->json);
        }
        return response()->json(['pedidos' => $data]);
    }
}
