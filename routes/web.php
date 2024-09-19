<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RackController;
use App\Http\Controllers\CategoryController;



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
});
