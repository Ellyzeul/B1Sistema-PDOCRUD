<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\NavbarItemController;
use App\Http\Controllers\OrderController;
use App\Models\Order;
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
        Route::get('/pdocrud-table', 'getPDOCrudTable');
        Route::get('/address', 'getAddress');
        Route::get('/shipment-label-data', 'getShipmentLabelData');
        Route::get('/invoice-link', 'getInvoiceLink');
        Route::get('/get-total-orders-in-phase', 'getTotalOrdersInPhase');
        Route::get('/ask-rating/whatsapp', 'getAskRatingWhatsapp');
        Route::get('/ask-rating/spreadsheet-data', 'getDataForAskRatingSpreadSheet');
        Route::post('/import-from-date', 'importOrdersFromDate');
        Route::post('/ask-rating/mail/send', 'sendAskRatingEmail');
        Route::patch('/accept-fnac', 'acceptFNACOrder');
        Route::patch('/bling/order', 'putBlingOrder');
        Route::patch('/address-verified', 'updateAddressVerified');
        Route::patch('/read-for-ship', 'updateReadForShip');
        Route::patch('/traking-id', 'updateTrackingCode');
        Route::patch('/traking-service', 'updateDeliveryMethod');
        Route::patch('/invoice-number', 'updateInvoiceNumber');
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
        Route::post('/order-nuvemshop-insert', 'orderNuvemshopInsert');
        Route::post('/order-estante-insert', 'orderEstanteInsert');
        Route::post('/order-alibris-insert', 'orderAlibrisInsert');
        Route::post('/order-fnac-insert', 'orderFNACInsert');
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
        Route::get('/read-purchases', 'readPurchases');
        Route::post('/read-for-excel', 'readForExcel');
        Route::post('/update', 'update');
        Route::post('/update-purchase', 'updatePurchase');
        Route::post('/update-all', 'updateAll');
        Route::post('/update-field', 'updateField');
        Route::get('/consult-price-and-shipping', 'consultPriceAndShipping');
        Route::get('/consult-zipcode', 'consultZipCode');
    });
