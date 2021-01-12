<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BuscaCepLogadouroController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('buscaEndereco/cep/{cep}', [BuscaCepLogadouroController::class, 'getCep']);
Route::get('buscaEndereco/logadouro/{logadouro}', [BuscaCepLogadouroController::class, 'getLogadouro']);

Route::fallback(function(){
    return response()->json(['message' => ''], 404);
})->name('api.fallback.404');
