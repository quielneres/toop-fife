<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
            'cpf'      => 'required|unique:users',
            'email'    => 'required|unique:users',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);

        }
        $user           = new User();
        $user->name     = $request->get('name');
        $user->email    = $request->get('email');
        $user->cpf      = $request->get('cpf');
        $user->password = Hash::make($request->get('password'));
        $user->save();

        return response()->json(['status' => 'success', 'message' => 'Cadastrado com sucesso']);

    }
}
