<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\HargaController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderStatusController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminMenuController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\GoogleController;

Route::get('/', [HomeController::class, 'index']);

Route::get(
    '/admin/dashboard',
    [AdminController::class, 'dashboard']
)->middleware('admin');

Route::get('/menu', [MenuController::class, 'index']);

Route::get('/harga', [HargaController::class, 'index']);

Route::get('/about', [AboutController::class, 'index']);

Route::get('/cart', [CartController::class, 'index']);

Route::get('/order-status', [OrderStatusController::class, 'index']);

Route::post('/cart/add/{id}', [CartController::class, 'add']);

Route::get('/cart/remove/{id}', [CartController::class, 'remove']);

Route::get('/admin/menu', [AdminMenuController::class, 'index']);

Route::get('/admin/menu/create', [AdminMenuController::class, 'create']);

Route::post('/admin/menu/store', [AdminMenuController::class, 'store']);

Route::get('/admin/menu/edit/{id}', [AdminMenuController::class, 'edit']);

Route::post('/admin/menu/update/{id}', [AdminMenuController::class, 'update']);

Route::get('/admin/menu/delete/{id}', [AdminMenuController::class, 'destroy']);

Route::get('/checkout', [CheckoutController::class, 'index']);

Route::post('/checkout/store', [CheckoutController::class, 'store']);

Route::get('/admin/orders', [AdminOrderController::class, 'index']);

Route::get('/admin/orders/show/{id}', [AdminOrderController::class, 'show']);

Route::get('/admin/orders/process/{id}', [AdminOrderController::class, 'process']);

Route::get('/admin/orders/finish/{id}', [AdminOrderController::class, 'finish']);

Route::get('/admin/login', [AuthController::class, 'login']);

Route::post('/admin/login', [AuthController::class, 'authenticate']);

Route::get('/admin/logout', [AuthController::class, 'logout']);

Route::get('/admin/reports', [AdminReportController::class, 'index']);

Route::get('/admin/orders/verify/{id}', [AdminOrderController::class, 'verify']);

Route::get('/auth/google', [GoogleController::class, 'redirect'])
    ->name('google.login');

Route::get('/auth/google/callback', [GoogleController::class, 'callback']);

Route::post('/logout', [GoogleController::class, 'logout'])
    ->name('logout');

Route::get('/login-required', function () {
    return view('auth.login-required');
})->name('login.required');

Route::get('/admin/auth/google', [GoogleController::class, 'adminRedirect'])
    ->name('admin.google');

Route::get('/admin/auth/google/callback', [GoogleController::class, 'adminCallback']);

Route::post('/admin/logout', function () {

    session()->forget([
        'admin_id',
        'admin_nama',
        'admin_email',
        'admin_avatar',
    ]);

    return redirect('/admin/login');

})->name('admin.logout');

use App\Services\DokuService;

Route::get('/test-doku', function (DokuService $doku) {

    return response()->json(

        $doku->createCheckout(
            50000,
            'INV-'.time()
        )
    );
});

use App\Http\Controllers\PaymentController;

Route::post('/payment/callback', [PaymentController::class, 'callback']);

Route::get('/payment/success', function () {
    return view('payment.success');
});
