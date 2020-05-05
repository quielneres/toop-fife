<?php

namespace App\Http\Controllers\Api;

use App\Client;
use App\Http\Controllers\Controller;
use App\Services\ClientService;
use App\Services\UserService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();

        return response()->json(['usuarios' => $users]);
    }

    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'email'    => 'required|unique:users',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);

        }
        $user           = new User();
        $user->name     = $request->get('name');
        $user->email    = $request->get('email');
        $user->password = Hash::make($request->get('password'));
        $user->save();

        return response()->json(['status' => 'success', 'message' => 'Cadastrado com sucesso']);
    }

    public function update(Request $request, $id, ClientService $clientService, UserService $userService)
    {

        $validator = Validator::make($request->all(), [
            'name'          => 'required',
            'cpf'           => 'required',
            'birth'         => 'required',
            'phone'         => 'required',
            'street'        => 'required',
            'number_adress' => 'required',
            'district'      => 'required',
            'city'          => 'required',
            'state'         => 'required',
            'complement'    => 'required',
            'zipe_code'     => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);

        }


        $birth = $this->toDate($request->get('birth'));

        $user                = User::where('id', $id)->firstOrFail();

        $user->name          = $request->get('name');
        $user->cpf           = $request->request->getDigits('cpf');
        $user->cnpj          = $request->request->getDigits('cnpj');
        $user->phone_number  = $request->request->getDigits('phone');
        $user->zipe_code     = $request->request->getDigits('zipe_code');
        $user->street        = $request->get('street');
        $user->number_adress = $request->get('number_adress');
        $user->district      = $request->get('district');
        $user->city          = $request->get('city');
        $user->state         = $request->get('state');
        $user->complement    = $request->get('complement');
        $user->country       = $request->get('country');
        $user->birth         = $birth;
        $user->save();

        $client = Client::where('id_usuario', $user->id)->first();

        if (!$client) {
            $customer                = $clientService->registerClient($user);

            $client               = new Client();
            $client->id_usuario   = $id;
            $client->id_comprador = $customer->getId();
            $client->json         = json_encode($customer);
            $client->save();
        }

        $user_obj = $userService->getUser($user->id);

        return response()->json(['status' => true, 'user' => $user_obj['user'], 'card' => $user_obj['cc']]);
    }

    public function toDate($data)
    {
        if (count(explode("/", $data)) > 1) {
            return implode("-", array_reverse(explode("/", $data)));
        } elseif (count(explode("-", $data)) > 1) {
            return implode("/", array_reverse(explode("-", $data)));
        }
    }


}

