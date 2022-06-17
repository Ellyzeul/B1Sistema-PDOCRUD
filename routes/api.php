<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SupplierURLController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PhaseController;

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

Route::get('/supplier_url/read', function (Request $request) {
    return SupplierURLController::read(
        intval($request->input('id'))
    );
});

Route::post('/supplier_url/update', function (Request $request) {
    return SupplierURLController::update(
        intval($request->input('id')),
        $request->input('supplier_url')
    );
});

Route::get('/orders/read', function (Request $request) {
    return OrderController::read(
        $request->input('phase') ? strval($request->input('phase')) : null
    );
});

Route::get('/phases/read', function () {
    return PhaseController::read();
});
