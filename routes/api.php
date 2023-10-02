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
use App\Http\Controllers\BlacklistController;
use App\Http\Controllers\InventoryController;

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
        // Route::get('/ask-rating/whatsapp', 'getAskRatingWhatsapp');
        Route::get('/ask-rating/spreadsheet-data', 'getDataForAskRatingSpreadSheet');
        Route::get('/get-order-control', 'readOrderControlByOrderNumber');
        Route::get('/get-order-addresses', 'readOrderAddressesByOrderNumber');
        Route::get('/order-messages', 'getOrderMessages');
        Route::post('/order-message', 'postOrderMessage');
        Route::post('/import-from-date', 'importOrdersFromDate');
        Route::post('/send-bling-order', 'sendOrderToBling');
        Route::post('/ask-rating', 'sendAskRating');
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
        Route::get('/read', 'read');
        Route::get('/verify-list', 'verifyFromList');
        Route::post('/create', 'create');
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
        Route::get('/read', 'readOrders');
        Route::get('/read-purchases', 'readPurchases');
        Route::get('/consult-zipcode', 'consultPostalCode');
        Route::get('/consult-price-and-shipping', 'consultPriceAndShipping');
        Route::post('/read-for-excel', 'readForExcel');
        Route::post('/update', 'update');
        Route::post('/update-purchase', 'updatePurchase');
        Route::post('/update-all', 'updateAll');
        Route::post('/update-field', 'updateField');
        Route::post('/update-phase', 'updateOrderPhase');
    });

Route::controller(BlacklistController::class)
    ->prefix('blacklist')
    ->group(function () {
        Route::get('/read-from-interval', 'readBlacklistFromInterval');
        Route::get('/search', 'searchBlacklist');
        Route::post('/insert-or-update', 'insertOrUpdateBlacklist');
        Route::post('/verify-list', 'verifyListBlacklist');
        Route::delete('/delete', 'deleteFromBlacklist');
    });   

Route::controller(InventoryController::class)
    ->prefix('inventory')
    ->group(function () {
        Route::get('/search', 'searchInventory');
        Route::post('/insert-or-update', 'insertOrUpdateInventory');
        Route::post('/verify-list', 'verifyListInventory');
        Route::patch('/avaliable-quantity', 'updateAvaliableQuantityInventory');
        Route::delete('/delete', 'deleteFromInventory');
    });       