<?php

use App\Http\Controllers\OrderDetailController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\NeighborhoodController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\ShippingMethodController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\PostalCodeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SalespersonOrderController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\StreetController;
use App\Http\Controllers\WarrantyController;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('cities')->group(function(){
    Route::get('/', [CityController::class, 'index']);
    Route::post('/', [CityController::class, 'store']);
    Route::get('{id}', [CityController::class, 'show']);
    Route::put('{id}', [CityController::class, 'update']);
    Route::delete('{id}', [CityController::class, 'destroy']);
});

Route::prefix('ShippingMethods')->group(function(){
    Route::get('/', [ShippingMethodController::class, 'index']);
    Route::post('/', [ShippingMethodController::class, 'store']);
    Route::get('{id}', [ShippingMethodController::class, 'show']);
    Route::put('{id}', [ShippingMethodController::class, 'update']);
    Route::delete('{id}', [ShippingMethodController::class, 'destroy']);
});

Route::prefix('sections')->group(function(){
    Route::get('/', [SectionController::class, 'index']);
    Route::post('/', [SectionController::class, 'store']);
    Route::get('{id}', [SectionController::class, 'show']);
    Route::put('{id}', [SectionController::class, 'update']);
    Route::delete('{id}', [SectionController::class, 'destroy']);
});

Route::prefix('positions')->group(function(){
    Route::get('/', [PositionController::class, 'index']);
    Route::post('/', [PositionController::class, 'store']);
    Route::get('{id}', [PositionController::class, 'show']);
    Route::put('{id}', [PositionController::class, 'update']);
    Route::delete('{id}', [PositionController::class, 'destroy']);
});

Route::prefix('postalcodes')->group(function(){
    Route::get('/', [PostalCodeController::class, 'index']);
    Route::post('/', [PostalCodeController::class, 'store']);
    Route::get('{id}', [PostalCodeController::class, 'show']);
    Route::put('{id}', [PostalCodeController::class, 'update']);
    Route::delete('{id}', [PostalCodeController::class, 'destroy']);
});

Route::prefix('countries')->group(function(){
    Route::get('/', [CountryController::class, 'index']);
    Route::post('/', [CountryController::class, 'store']);
    Route::get('{id}', [CountryController::class, 'show']);
    Route::put('{id}', [CountryController::class, 'update']);
    Route::delete('{id}', [CountryController::class, 'destroy']);
});

Route::prefix('users')->group(function(){
    Route::get('/', [UserController::class, 'index']);
    Route::post('/', [UserController::class, 'store']);
    Route::get('{id}', [UserController::class, 'show']);
    Route::put('{id}', [UserController::class, 'update']);
    Route::delete('{id}', [UserController::class, 'destroy']);
});

Route::prefix('paymentMethods')->group(function(){
    Route::get('/', [PaymentMethodController::class, 'index']);
    Route::post('/', [PaymentMethodController::class, 'store']);
    Route::get('{id}', [PaymentMethodController::class, 'show']);
    Route::put('{id}', [PaymentMethodController::class, 'update']);
    Route::delete('{id}', [PaymentMethodController::class, 'destroy']);
});

Route::prefix('products')->group(function(){
    Route::get('/', [ProductController::class, 'index']);
    Route::post('/', [ProductController::class, 'store']);
    Route::get('{id}', [ProductController::class, 'show']);
    Route::put('{id}', [ProductController::class, 'update']);
    Route::delete('{id}', [ProductController::class, 'destroy']);
});

Route::prefix('states')->group(function(){
    Route::get('/', [StateController::class, 'index']);
    Route::post('/', [StateController::class, 'store']);
    Route::get('{id}', [StateController::class, 'show']);
    Route::put('{id}', [StateController::class, 'update']);
    Route::delete('{id}', [StateController::class, 'destroy']);
});
Route::prefix('neighborhoods')->group(function(){
    Route::get('/', [NeighborhoodController::class, 'index']);
    Route::post('/', [NeighborhoodController::class, 'store']);
    Route::get('{id}', [NeighborhoodController::class, 'show']);
    Route::put('{id}', [NeighborhoodController::class, 'update']);
    Route::delete('{id}', [NeighborhoodController::class, 'destroy']);
});

Route::prefix('streets')->group(function(){
    Route::get('/', [StreetController::class, 'index']);
    Route::post('/', [StreetController::class, 'store']);
    Route::get('{id}', [StreetController::class, 'show']);
    Route::put('{id}', [StreetController::class, 'update']);
    Route::delete('{id}', [StreetController::class, 'destroy']);
});

Route::prefix('employees')->group(function(){
    Route::get('/', [EmployeeController::class, 'index']);
    Route::post('/', [EmployeeController::class, 'store']);
    Route::get('{id}', [EmployeeController::class, 'show']);
    Route::put('{id}', [EmployeeController::class, 'update']);
    Route::delete('{id}', [EmployeeController::class, 'destroy']);
});

Route::prefix('salespersonOrders')->group(function(){
    Route::get('/', [SalespersonOrderController::class, 'index']);
    Route::post('/', [SalespersonOrderController::class, 'store']);
    Route::get('{id}', [SalespersonOrderController::class, 'show']);
    Route::put('{id}', [SalespersonOrderController::class, 'update']);
    Route::delete('{id}', [SalespersonOrderController::class, 'destroy']);
});

Route::prefix('orders')->group(function(){
    Route::get('/', [OrderController::class, 'index']);
    Route::post('/', [OrderController::class, 'store']);
    Route::get('{id}', [OrderController::class, 'show']);
    Route::put('{id}', [OrderController::class, 'update']);
    Route::delete('{id}', [OrderController::class, 'destroy']);
});

Route::prefix('invoices')->group(function(){
    Route::get('/', [InvoiceController::class, 'index']);
    Route::post('/', [InvoiceController::class, 'store']);
    Route::get('{id}', [InvoiceController::class, 'show']);
    Route::put('{id}', [InvoiceController::class, 'update']);
    Route::delete('{id}', [InvoiceController::class, 'destroy']);
});

Route::prefix('orderDetails')->group(function(){
    Route::get('/', [OrderDetailController::class, 'index']);
    Route::post('/', [OrderDetailController::class, 'store']);
    Route::get('{id}', [OrderDetailController::class, 'show']);
    Route::put('{id}', [OrderDetailController::class, 'update']);
    Route::delete('{id}', [OrderDetailController::class, 'destroy']);
});

Route::prefix('warranties')->group(function(){
    Route::get('/', [WarrantyController::class, 'index']);
    Route::post('/', [WarrantyController::class, 'store']);
    Route::get('{id}', [WarrantyController::class, 'show']);
    Route::put('{id}', [WarrantyController::class, 'update']);
    Route::delete('{id}', [WarrantyController::class, 'destroy']);
});
