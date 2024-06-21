<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BarangTransactionController;
use App\Http\Controllers\CashierTransactionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\StoreInformationController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [UserController::class, 'register'])->name('register');

Route::group(['middleware' => 'api', 'prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('refresh', [AuthController::class, 'refresh'])->name('refresh');
    Route::get('me', [AuthController::class, 'me'])->name('me');
});

Route::group(['middleware' => ['api', 'auth:api', 'check.token.expiry'], 'prefix' => 'users'], function () {
    Route::get('/', [UserController::class, '__invoke'])->name('all');
    Route::get('/{id}', [UserController::class, 'detail'])->name('detail');
    // Route::post('/', [CategoryController::class, 'insert'])->name('insert');
    // Route::post('/{id}', [CategoryController::class, 'update'])->name('update');
    // Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('destroy');
});

Route::group(['middleware' => ['api', 'auth:api', 'check.token.expiry'], 'prefix' => 'category'], function () {
    Route::get('/', [CategoryController::class, '__invoke'])->name('all');
    Route::get('/export-excel', [CategoryController::class, 'export_excel'])->name('export_excel');
    Route::get('/{id}', [CategoryController::class, 'detail'])->name('detail');
    Route::post('/', [CategoryController::class, 'insert'])->name('insert');
    Route::post('/{id}', [CategoryController::class, 'update'])->name('update');
    Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('destroy');
});

Route::group(['middleware' => ['api', 'auth:api', 'check.token.expiry'], 'prefix' => 'payment-method'], function () {
    Route::get('/', [PaymentMethodController::class, '__invoke'])->name('all');
    Route::get('/export-excel', [PaymentMethodController::class, 'export_excel'])->name('export_excel');
    Route::get('/{id}', [PaymentMethodController::class, 'detail'])->name('detail');
    Route::post('/', [PaymentMethodController::class, 'insert'])->name('insert');
    Route::post('/{id}', [PaymentMethodController::class, 'update'])->name('update');
    Route::delete('/{id}', [PaymentMethodController::class, 'destroy'])->name('destroy');
});

Route::group(['middleware' => ['api', 'auth:api', 'check.token.expiry'], 'prefix' => 'barang'], function () {
    Route::get('/', [BarangController::class, '__invoke'])->name('all');
    Route::get('/export-excel', [BarangController::class, 'export_excel'])->name('export_excel');
    Route::get('/{id}', [BarangController::class, 'detail'])->name('detail');
    Route::post('/', [BarangController::class, 'insert'])->name('insert');
    Route::post('/{id}', [BarangController::class, 'update'])->name('update');
    Route::delete('/{id}', [BarangController::class, 'destroy'])->name('destroy');
});

Route::group(['middleware' => ['api', 'auth:api', 'check.token.expiry'], 'prefix' => 'supplier'], function () {
    Route::get('/', [SupplierController::class, '__invoke'])->name('all');
    Route::get('/export-excel', [SupplierController::class, 'export_excel'])->name('export_excel');
    Route::get('/{id}', [SupplierController::class, 'detail'])->name('detail');
    Route::post('/', [SupplierController::class, 'insert'])->name('insert');
    Route::post('/{id}', [SupplierController::class, 'update'])->name('update');
    Route::delete('/{id}', [SupplierController::class, 'destroy'])->name('destroy');
});

Route::group(['middleware' => ['api', 'auth:api', 'check.token.expiry'], 'prefix' => 'customer'], function () {
    Route::get('/', [CustomerController::class, '__invoke'])->name('all');
    Route::get('/export-excel', [CustomerController::class, 'export_excel'])->name('export_excel');
    Route::get('/{id}', [CustomerController::class, 'detail'])->name('detail');
    Route::post('/', [CustomerController::class, 'insert'])->name('insert');
    Route::post('/{id}', [CustomerController::class, 'update'])->name('update');
    Route::delete('/{id}', [CustomerController::class, 'destroy'])->name('destroy');
});

Route::group(['middleware' => ['api', 'auth:api', 'check.token.expiry'], 'prefix' => 'barang-transaction'], function () {
    Route::get('/', [BarangTransactionController::class, '__invoke'])->name('all');
    Route::get('/export-excel', [BarangTransactionController::class, 'export_excel'])->name('export_excel');
    Route::get('/{id}', [BarangTransactionController::class, 'detail'])->name('detail');
    Route::post('/', [BarangTransactionController::class, 'insert'])->name('insert');
    Route::post('/{id}', [BarangTransactionController::class, 'update'])->name('update');
    Route::delete('/{id}', [BarangTransactionController::class, 'destroy'])->name('destroy');
});

Route::group(['middleware' => ['api', 'auth:api', 'check.token.expiry'], 'prefix' => 'cashier-transaction'], function () {
    Route::get('/', [CashierTransactionController::class, '__invoke'])->name('all');
    Route::get('/print-receipt/{id}', [CashierTransactionController::class, 'print_receipt'])->name('print_receipt');
    Route::get('/export-excel', [CashierTransactionController::class, 'export_excel'])->name('export_excel');
    Route::get('/{id}', [CashierTransactionController::class, 'detail'])->name('detail');
    Route::post('/', [CashierTransactionController::class, 'insert'])->name('insert');
    Route::post('/{id}', [CashierTransactionController::class, 'update'])->name('update');
    Route::delete('/{id}', [CashierTransactionController::class, 'destroy'])->name('destroy');
});

Route::group(['middleware' => ['api', 'auth:api', 'check.token.expiry'], 'prefix' => 'store-information'], function () {
    Route::get('/', [StoreInformationController::class, '__invoke'])->name('detail');
    Route::post('/', [StoreInformationController::class, 'update'])->name('update');
});
