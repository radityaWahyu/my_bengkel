<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RackController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\RepairController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SupplierController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [AuthController::class, 'index'])->name('backoffice.auth.login');

// ---------------------route for backoffice-------------------------------------
Route::prefix('backoffice')->group(function () {
    Route::prefix('auth')->controller(AuthController::class)->group(function () {
        Route::post('/', 'store')->name('backoffice.auth.store');
        Route::get('/logout', 'destroy')->name('backoffice.auth.destroy');
    });

    Route::prefix('kategori')->controller(CategoryController::class)->group(function () {
        Route::get('/', 'index')->name('backoffice.category.index');
        Route::get('/{category}', 'edit')->name('backoffice.category.edit');
        Route::post('/', 'store')->name('backoffice.category.store');
        Route::post('/delete_all', 'deleteAll')->name('backoffice.category.delete-all');
        Route::delete('/{category}', 'destroy')->name('backoffice.category.delete');
        Route::put('/{category}', 'update')->name('backoffice.category.update');
    });

    Route::prefix('rak')->controller(RackController::class)->group(function () {
        Route::get('/', 'index')->name('backoffice.rack.index');
        Route::get('/{rack}', 'edit')->name('backoffice.rack.edit');
        Route::post('/', 'store')->name('backoffice.rack.store');
        Route::post('/delete_all', 'deleteAll')->name('backoffice.rack.delete-all');
        Route::delete('/{rack}', 'destroy')->name('backoffice.rack.delete');
        Route::put('/{rack}', 'update')->name('backoffice.rack.update');
    });

    Route::prefix('merk')->controller(BrandController::class)->group(function () {
        Route::get('/', 'index')->name('backoffice.brand.index');
        Route::get('/{brand}', 'edit')->name('backoffice.brand.edit');
        Route::post('/', 'store')->name('backoffice.brand.store');
        Route::post('/delete_all', 'deleteAll')->name('backoffice.brand.delete-all');
        Route::delete('/{brand}', 'destroy')->name('backoffice.brand.delete');
        Route::put('/{brand}', 'update')->name('backoffice.brand.update');
    });

    Route::prefix('perbaikan')->controller(RepairController::class)->group(function () {
        Route::get('/', 'index')->name('backoffice.repair.index');
        Route::get('/list', 'getRepairLists')->name('backoffice.repair.list');
        Route::get('/{repair}', 'edit')->name('backoffice.repair.edit');
        Route::post('/', 'store')->name('backoffice.repair.store');
        Route::post('/delete_all', 'deleteAll')->name('backoffice.repair.delete-all');
        Route::delete('/{repair}', 'destroy')->name('backoffice.repair.delete');
        Route::put('/{repair}', 'update')->name('backoffice.repair.update');
    });

    Route::prefix('barang')->controller(ProductController::class)->group(function () {
        Route::get('/', 'index')->name('backoffice.product.index');
        Route::get('/list', 'getProductLists')->name('backoffice.product.list');
        Route::get('/create', 'create')->name('backoffice.product.create');
        Route::get('/{product}', 'edit')->name('backoffice.product.edit');
        Route::post('/', 'store')->name('backoffice.product.store');
        Route::post('/delete_all', 'deleteAll')->name('backoffice.product.delete-all');
        Route::delete('/{product}', 'destroy')->name('backoffice.product.delete');
        Route::put('/{product}', 'update')->name('backoffice.product.update');
    });

    Route::prefix('pegawai')->controller(EmployeeController::class)->group(function () {
        Route::get('/', 'index')->name('backoffice.employee.index');
        Route::get('/create', 'create')->name('backoffice.employee.create');
        Route::get('/{employee}', 'edit')->name('backoffice.employee.edit');
        Route::post('/', 'store')->name('backoffice.employee.store');
        Route::post('/delete_all', 'deleteAll')->name('backoffice.employee.delete-all');
        Route::delete('/{employee}', 'destroy')->name('backoffice.employee.delete');
        Route::put('/{employee}', 'update')->name('backoffice.employee.update');
    });

    Route::prefix('pemasok')->controller(SupplierController::class)->group(function () {
        Route::get('/', 'index')->name('backoffice.supplier.index');
        Route::get('/create', 'create')->name('backoffice.supplier.create');
        Route::get('/{supplier}', 'edit')->name('backoffice.supplier.edit');
        Route::post('/', 'store')->name('backoffice.supplier.store');
        Route::post('/delete_all', 'deleteAll')->name('backoffice.supplier.delete-all');
        Route::delete('/{supplier}', 'destroy')->name('backoffice.supplier.delete');
        Route::put('/{supplier}', 'update')->name('backoffice.supplier.update');
    });

    Route::prefix('pelanggan')->controller(CustomerController::class)->group(function () {
        Route::get('/', 'index')->name('backoffice.customer.index');
        Route::get('/create', 'create')->name('backoffice.customer.create');
        Route::get('/detail/{customer}', 'show')->name('backoffice.customer.show');
        Route::get('/{customer}', 'edit')->name('backoffice.customer.edit');
        Route::post('/', 'store')->name('backoffice.customer.store');
        Route::post('/delete_all', 'deleteAll')->name('backoffice.customer.delete-all');
        Route::delete('/{customer}', 'destroy')->name('backoffice.customer.delete');
        Route::put('/{customer}', 'update')->name('backoffice.customer.update');

        Route::prefix('kendaraan')->controller(VehicleController::class)->group(function () {
            Route::get('/', 'index')->name('backoffice.vehicle.index');
            Route::get('/list', 'getVehicleLists')->name('backoffice.vehicle.list');
            Route::get('/create', 'create')->name('backoffice.vehicle.create');
            Route::get('/{vehicle}', 'edit')->name('backoffice.vehicle.edit');
            Route::post('/', 'store')->name('backoffice.vehicle.store');
            Route::post('/delete_all', 'deleteAll')->name('backoffice.vehicle.delete-all');
            Route::delete('/{vehicle}', 'destroy')->name('backoffice.vehicle.delete');
            Route::put('/{vehicle}', 'update')->name('backoffice.vehicle.update');
        });
    });

    Route::prefix('transaksi')->group(function () {
        Route::prefix('service')->controller(ServiceController::class)->group(function () {
            Route::get('/', 'index')->name('backoffice.service.index');
            Route::get('/create', 'create')->name('backoffice.service.create');
            Route::get('/create-invoice/{service}', 'createInvoice')->name('backoffice.service.create-invoice');
            Route::get('/{service}', 'edit')->name('backoffice.service.edit');
            Route::post('/', 'store')->name('backoffice.service.store');
            Route::post('/add-product/{service}', 'addProduct')->name('backoffice.service.add-product');
            Route::post('/add-repair/{service}', 'addRepair')->name('backoffice.service.add-repair');
            Route::delete('/{service}', 'destroy')->name('backoffice.service.delete');
            Route::put('/{service}', 'update')->name('backoffice.service.update');
        });
    });

    Route::prefix('pengaturan')->group(function () {

        Route::prefix('user')->controller(UserController::class)->group(function () {
            Route::get('/', 'index')->name('backoffice.user.index');
            Route::get('/create', 'create')->name('backoffice.user.create');
            Route::get('/{user}', 'edit')->name('backoffice.user.edit');
            Route::post('/', 'store')->name('backoffice.user.store');
            Route::post('/delete_all', 'deleteAll')->name('backoffice.user.delete-all');
            Route::delete('/{user}', 'destroy')->name('backoffice.user.delete');
            Route::put('/{user}', 'update')->name('backoffice.user.update');
            Route::put('/{user}/enabled', 'enabled')->name('backoffice.user.enabled');
        });

        Route::prefix('sistem')->controller(SettingController::class)->group(function () {
            Route::get('/', 'index')->name('backoffice.setting.index');
            Route::post('/', 'store')->name('backoffice.setting.store');
        });

        Route::prefix('pembayaran')->controller(PaymentController::class)->group(function () {
            Route::get('/', 'index')->name('backoffice.payment.index');
            Route::get('/{payment}', 'edit')->name('backoffice.payment.edit');
            Route::post('/', 'store')->name('backoffice.payment.store');
            Route::post('/delete_all', 'deleteAll')->name('backoffice.payment.delete-all');
            Route::delete('/{payment}', 'destroy')->name('backoffice.payment.delete');
            Route::put('/{payment}', 'update')->name('backoffice.payment.update');
        });
    });
});
