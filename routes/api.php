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
    return response()->json(['api'=> 'v-0.0.1']);
});

Route::post('auth/login', 'Api\\AuthController@login');

Route::group(['middleware' => ['apiJwt']], function (){
});
    Route::get('users','Api\\UserController@index');
    Route::post('novo-comprador','Api\\WireCardController@newComprador');
    Route::post('consultar-comprador','Api\\WireCardController@getComprador');
    Route::post('add-credit-card','Api\\WireCardController@addCreditCard');
    Route::post('novo-pedido','Api\\WireCardController@newOrder');
    Route::post('ver-pedido','Api\\WireCardController@getOrder');
    Route::post('ver-pedido-all','Api\\WireCardController@allOrdes');
