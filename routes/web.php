<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RackController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EmployeeController;

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

// ---------------------route for backoffice-------------------------------------
Route::prefix('backoffice')->group(function () {
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

    Route::prefix('barang')->controller(ProductController::class)->group(function () {
        Route::get('/', 'index')->name('backoffice.product.index');
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
});
