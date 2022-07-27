<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SupplierURLController;
use App\Http\Controllers\NavbarItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PhaseController;
use App\Http\Controllers\PhotoController;
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

Route::post('/photo/create', function (Request $request) {
    Log::info("Rota - /api/photo/create");
    $photoFile = $request->file("photo");
    $photoName = $photoFile->getClientOriginalName();
    Log::info("Imagem para salvar recebida");

    return PhotoController::create(
        $photoFile,
        $photoName
    );
});

Route::get('/photo/read', function (Request $request) {
    Log::info("Rota - /api/photo/read");
    $photoNamePattern = $request->input("name_pattern");

    return PhotoController::read(
        $photoNamePattern
    );
});

Route::get('/navbar-items/read', function (Request $request) {
    Log::info("Rota - /api/navbar-items/read");
    $email = $request->input("email");

    return NavbarItemController::read($email);
});
