<?php
/**
 * Created by PhpStorm.
 * User: ezequiel
 * Date: 21/03/20
 * Time: 20:12
 */

namespace App\Http\Controllers\Api;


use App\Client;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Moip\Auth\BasicAuth;
use Moip\Moip;

class WireCardController extends Controller
{
    public $moip;

    public function __construct()
    {
        $token      = '60FAPH4GGLJGUM4MFW8CUSEFNNAJT5SC';
        $key        = 'UWNSJ0UYVTWH41GSKCTC4UWDVDLVEH8XSGBCMROS';
        $this->moip = new Moip(new BasicAuth($token, $key), Moip::ENDPOINT_SANDBOX);
    }

    public function newComprador()
    {
        try {
            $customer = $this->moip->customers()->setOwnId(uniqid())
                ->setFullname('Fulano de Tal')
                ->setEmail('fulano@email.com')
                ->setBirthDate('1988-12-30')
                ->setTaxDocument('22222222222')
                ->setPhone(11, 66778899)
                ->addAddress('BILLING',
                    'Rua de teste', 123,
                    'Bairro', 'Sao Paulo', 'SP',
                    '01234567', 8)
                ->addAddress('SHIPPING',
                    'Rua de teste do SHIPPING', 123,
                    'Bairro do SHIPPING', 'Sao Paulo', 'SP',
                    '01234567', 8)
                ->create();

            $comprador = new Client();
            $comprador->id_usuario = 1;
            $comprador->json = json_encode($customer);
            $comprador->save();

            return response()->json($customer);
        } catch (\Exception $exception) {
            return response()->json($exception->getMessage());
        }
    }

    public function getComprador()
    {
        try {
            $customer_id = 'CUS-9KVUEVSDXIXW';
            $customer    = $this->moip->customers()->get($customer_id);
            return response()->json($customer);
        } catch (\Exception $e) {
            return response()->json(['mensage' => 'Comprador nao encotrado']);
        }
    }

    public function addCreditCard()
    {

        try {
            $customer = $this->moip->customers()->creditCard()
                ->setExpirationMonth('05')
                ->setExpirationYear(2022)
                ->setNumber('4012001037141112')
                ->setCVC('123')
                ->setFullName('Jose Portador da Silva')
                ->setBirthDate('1988-12-30')
                ->setTaxDocument('CPF', '33333333333')
                ->setPhone('55', '11', '66778899')
                ->create('CUS-9KVUEVSDXIXW');

            return response()->json($customer);
        } catch (\Exception $exception) {
            return response()->json($exception->getMessage());
        }
    }

    public function deleteCreditCarde(){
        $this->moip->customers()->creditCard()->delete('CRC-PLY237U3OP0V');

        return response()->json(['mensage' => 'Deletado com secesso']);
    }

    public function newOrder()
    {
        $order = $this->moip->orders()->setOwnId(uniqid())
            ->addItem("Descrição do pedido",1, "Camiseta estampada branca", 9500)
            ->setShippingAmount(1500)->setAddition(0)->setDiscount(0)
            ->setCustomerId("CUS-9KVUEVSDXIXW")
            ->create();

        return response()->json($order);
    }

    public function getOrder()
    {
        try {
            $order_id = 'ORD-7RD3AVDW66I6';
            $order = $this->moip->orders()->get($order_id);
            return response()->json($order);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Errro interno']);
        }
    }

    public function allOrdes()
    {
        try {
            $orders = $this->moip->orders()->getList();
            return response()->json($orders);

        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }

    }
}
