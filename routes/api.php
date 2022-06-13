<?php

use App\Http\Controllers\CarroController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\LocacaoController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\ModeloController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
//Route::resource('cliente', ClienteController::class);
Route::prefix('v1')->middleware('jwt.auth')->group(function () {
    Route::post('me', [App\Http\Controllers\AuthController::class, 'me']);
    Route::post('logout', [App\Http\Controllers\AuthController::class, 'logout']);

    Route::apiResource('cliente', ClienteController::class);
    Route::apiResource('carro', CarroController::class);
    Route::apiResource('locacao', LocacaoController::class);
    Route::apiResource('marca', MarcaController::class);
    Route::apiResource('modelo', ModeloController::class);
});

Route::post('refresh', [App\Http\Controllers\AuthController::class, 'refresh']);
Route::post('login', [App\Http\Controllers\AuthController::class, 'login']);



