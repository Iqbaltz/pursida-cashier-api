<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PaymentMethodController;
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

Route::group(['middleware' => 'api', 'prefix' => 'category'], function () {
    Route::get('/', [CategoryController::class, '__invoke'])->name('all');
    Route::get('/{slug}', [CategoryController::class, 'detail'])->name('detail');
    Route::post('/', [CategoryController::class, 'insert'])->name('insert');
    Route::post('/{id}', [CategoryController::class, 'update'])->name('update');
    Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('destroy');
});

Route::group(['middleware' => 'api', 'prefix' => 'payment-method'], function () {
    Route::get('/', [PaymentMethodController::class, '__invoke'])->name('all');
    Route::get('/{slug}', [PaymentMethodController::class, 'detail'])->name('detail');
    Route::post('/', [PaymentMethodController::class, 'insert'])->name('insert');
    Route::post('/{id}', [PaymentMethodController::class, 'update'])->name('update');
    Route::delete('/{id}', [PaymentMethodController::class, 'destroy'])->name('destroy');
});

Route::group(['middleware' => 'api', 'prefix' => 'barang'], function () {
    Route::get('/', [BarangController::class, '__invoke'])->name('all');
    Route::get('/{slug}', [BarangController::class, 'detail'])->name('detail');
    Route::post('/', [BarangController::class, 'insert'])->name('insert');
    Route::post('/{id}', [BarangController::class, 'update'])->name('update');
    Route::delete('/{id}', [BarangController::class, 'destroy'])->name('destroy');
});

Route::group(['middleware' => 'api', 'prefix' => 'supplier'], function () {
    Route::get('/', [SupplierController::class, '__invoke'])->name('all');
    Route::get('/{id}', [SupplierController::class, 'detail'])->name('detail');
    Route::post('/', [SupplierController::class, 'insert'])->name('insert');
    Route::post('/{id}', [SupplierController::class, 'update'])->name('update');
    Route::delete('/{id}', [SupplierController::class, 'destroy'])->name('destroy');
});

Route::group(['middleware' => 'api', 'prefix' => 'customer'], function () {
    Route::get('/', [CustomerController::class, '__invoke'])->name('all');
    Route::get('/{id}', [CustomerController::class, 'detail'])->name('detail');
    Route::post('/', [CustomerController::class, 'insert'])->name('insert');
    Route::post('/{id}', [CustomerController::class, 'update'])->name('update');
    Route::delete('/{id}', [CustomerController::class, 'destroy'])->name('destroy');
});
