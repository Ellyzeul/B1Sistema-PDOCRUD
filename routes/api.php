<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SupplierURLController;
use App\Http\Controllers\NavbarItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PhaseController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\UserController;

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
        $request->input('supplier_url') ?? ""
    );
});

Route::controller(OrderController::class)
    ->prefix('orders')
    ->group(function () {
        Route::get('/read', 'read');
        Route::post('/address-verified/update', 'updateAddressVerified');
    }
);

Route::get('/phases/read', function (Request $request) {
    return PhaseController::read(
        $request->input('email')
    );
});

Route::post('/user/create', function (Request $request) {
    return UserController::create(
        $request->input('name'),
        $request->input('email'),
        $request->input('password'),
        $request->input('id_section')
    );
});

Route::post('/user/login', function (Request $request) {
    return UserController::login(
        $request->input('email'),
        $request->input('password')
    );
});

Route::controller(PhotoController::class)
    ->prefix('photo')
    ->group(function() {
        Route::post('/create', 'create');
        Route::get('/read', 'read');
        Route::get('/verify-list', 'verifyFromList');
    }
);

Route::get('/navbar-items/read', function (Request $request) {
    Log::info("Rota - /api/navbar-items/read");
    $email = $request->input("email");

    return NavbarItemController::read($email);
});

Route::get('/quotes/read', function (Request $request) {
    return QuoteController::read();
});
