<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\NavbarItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PhaseController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\SupplierURLController;
use App\Http\Controllers\TrackingController;
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
        Route::get('/get-total-orders-in-phase', 'getTotalOrdersInPhase');
        Route::post('/address-verified/update', 'updateAddressVerified');
        Route::post('/ask-rating/send', 'sendAskRatingEmail');
        Route::get('/address/get', 'getAddress');
    });

Route::get('/phases/read', function (Request $request) {
    return PhaseController::read(
        $request->input('email')
    );
});

Route::controller(UserController::class)
    ->prefix('user')
    ->group(function() {
        Route::post('/create', 'create');
        Route::post('/login', 'login');
    });

Route::controller(PhotoController::class)
    ->prefix('photo')
    ->group(function() {
        Route::post('/create', 'create');
        Route::get('/read', 'read');
        Route::get('/verify-list', 'verifyFromList');
        Route::delete('/exclude', 'exclude');
    });

Route::controller(CompanyController::class)
    ->prefix('company')
    ->group(function() {
        Route::get('/read-thumbnails', 'readThumbnails');
        Route::get('/read-info', 'readInfo');
    });

Route::controller(FileUploadController::class)
    ->prefix('file-upload')
    ->group(function() {
        Route::post('/order-update', 'orderUpdate');
        Route::post('/order-amazon-insert', 'orderAmazonInsert');
        Route::post('/order-address-insert', 'orderAddressInsert');
    });

Route::get('/navbar-items/read', function (Request $request) {
    Log::info("Rota - /api/navbar-items/read");
    $email = $request->input("email");

    return NavbarItemController::read($email);
});

Route::get('/quotes/read', function (Request $request) {
    return QuoteController::read();
});

Route::controller(TrackingController::class)
    ->prefix('tracking')
    ->group(function() {
        Route::get('/read', 'read');
        Route::post('/read-for-excel', 'readForExcel');
        Route::post('/update', 'update');
        Route::post('/update-field', 'updateField');
    });