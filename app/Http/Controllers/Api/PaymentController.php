<?php

namespace App\Http\Controllers\Api;

use App\Client;
use App\CreditCard;
use App\Http\Controllers\Controller;
use App\Services\CreditCardService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Moip\Auth\BasicAuth;
use Moip\Moip;

class PaymentController extends Controller
{

    public function register(Request $request, CreditCardService $creditCardService, $id)
    {

        $user = User::where('id', $id)->firstOrFail();

        if ($user->countClient) {

            try {

                $customer_card = $creditCardService->saveCard($request, $user);

                $credit_cards             = new CreditCard();
                $credit_cards->id_user    = $id;
                $credit_cards->id_cliente = $user->countClient->id_comprador;
                $credit_cards->id_card    = $customer_card->getId();
                $credit_cards->json       = json_encode($customer_card);
                $credit_cards->save();

                return response()->json(['status' => true]);
            } catch (\Exception $exception) {
                return response()->json(['status' => false, $exception->getErros()]);
            }
        }
    }

    public function listCreditCards($id_user)
    {
        $cards      = CreditCard::where('id_user', $id_user)->get()->toArray();
        $list_cards = [];

        foreach ($cards as $card) {
            $list_cards[] = [
                'id'        => $card['id'],
                'id_card'   => json_decode($card['json'])->creditCard->id,
                'flag'      => json_decode($card['json'])->creditCard->brand,
                'dig_start' => json_decode($card['json'])->creditCard->first6,
                'dig_end'   => json_decode($card['json'])->creditCard->last4,
                'status'    => $card['status']
            ];
        }


        return response()->json(['data' => $list_cards]);
    }

    public function cardDefault(Request $request, $id)
    {
        $card = CreditCard::where('id_user', $id)
            ->where('status', 1)
            ->first();

        if ($card) {
            $card->status = 0;
            $card->save();


        }

        $card         = CreditCard::where('id', $request->get('id'))->first();
        $card->status = $request->get('status') == 0 ? 1 : 0;
        $card->save();

        return response()->json(['status' => true]);

    }

    public function cardDelete($id_card)
    {
        $card = CreditCard::where('id', $id_card)->firstOrFail();
        $card->delete();

        return response()->json(['status' => true]);

    }
}
