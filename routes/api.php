<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('api')->get('/', function (Request $request) {
    return response()->json(['api' => 'v-1.0.0']);
});

Route::post('auth/login', 'Api\\AuthController@login');

Route::group(['middleware' => ['apiJwt']], function () {
    Route::get('users', 'Api\\UserController@index');
});
Route::post('novo-comprador', 'Api\\CompradorController@novoComprador');
Route::post('consultar-comprador', 'Api\\WireCardController@getComprador');
//    Route::post('add-credit-card','Api\\WireCardController@addCreditCard');
Route::post('ver-pedido', 'Api\\WireCardController@getOrder');
Route::get('ver-pedido-all', 'Api\\PedidoController@pedidosLocal');
Route::get('pedido-detalhe/{id_pedido}', 'Api\\PedidoController@pedidoDetalhe');

//credit card
Route::post('credit-card-creat/{id_user}', 'Api\\PaymentController@register');
Route::post('credit-card-default/{id_user}', 'Api\\PaymentController@cardDefault');
Route::get('credit-card-delete/{id_user}', 'Api\\PaymentController@cardDelete');
Route::get('list-credit-card/{id_user}', 'Api\\PaymentController@listCreditCards');

Route::post('new-request', 'Api\\PedidoController@newResquest');
Route::post('boleto-generation', 'Api\\BoletoController@boletoGenerate');


Route::post('register-user', 'Api\\UserController@register');
Route::post('update-user/{id_user}', 'Api\\UserController@update');


