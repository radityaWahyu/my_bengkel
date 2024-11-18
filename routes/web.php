<?php

use App\Models\StockCorrection;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RackController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\JurnalController;
use App\Http\Controllers\RepairController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\StockCorrectionController;

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
Route::get('/transaksi/service/detail/{service}', [ServiceController::class, 'customerServiceDetail'])->name('backoffice.service.customer-service-detail');

// ---------------------route for backoffice-------------------------------------

Route::prefix('backoffice/auth')->controller(AuthController::class)->group(
    function () {
        Route::post('/', 'store')->name('backoffice.auth.store');
    }
);


Route::middleware('auth.backoffice')->group(
    function () {
        Route::post('/logout', [AuthController::class, 'destroy'])->name('backoffice.auth.logout');

        Route::prefix('backoffice')->group(function () {
            Route::prefix('auth')->controller(AuthController::class)->group(function () {
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
                Route::get('/list', 'employeeList')->name('backoffice.employee.list');
                Route::get('/{employee}', 'edit')->name('backoffice.employee.edit');
                Route::post('/', 'store')->name('backoffice.employee.store');
                Route::post('/delete_all', 'deleteAll')->name('backoffice.employee.delete-all');
                Route::delete('/{employee}', 'destroy')->name('backoffice.employee.delete');
                Route::put('/{employee}', 'update')->name('backoffice.employee.update');
            });

            Route::prefix('pemasok')->controller(SupplierController::class)->group(function () {
                Route::get('/', 'index')->name('backoffice.supplier.index');
                Route::get('/list', 'getSupplierLists')->name('backoffice.supplier.list');
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
                    Route::get('/detail/{service}', 'show')->name('backoffice.service.detail');
                    Route::get('/create-invoice/{service}', 'createInvoice')->name('backoffice.service.create-invoice');
                    Route::get('/receipt/{service}', 'printReceipt')->name('backoffice.service.receipt');
                    Route::get('/invoice/{service}', 'printInvoice')->name('backoffice.service.invoice');
                    Route::get('/{service}', 'edit')->name('backoffice.service.edit');
                    Route::post('/', 'store')->name('backoffice.service.store');
                    Route::post('/add-product/{service}', 'addProduct')->name('backoffice.service.add-product');
                    Route::post('/add-repair/{service}', 'addRepair')->name('backoffice.service.add-repair');
                    Route::post('/add-employee/{service_repair}', 'addEmployee')->name('backoffice.service.add-employee');
                    Route::post('/start_repair/{service_repair}', 'startRepair')->name('backoffice.service.start-repair');
                    Route::post('/finish_repair/{service_repair}', 'finishRepair')->name('backoffice.service.finish-repair');
                    Route::post('/update-qty/{service_product}', 'updateQtyProduct')->name('backoffice.service.update-qty-product');
                    Route::delete('delete-repair/{service_repair}', 'deleteServiceRepair')->name('backoffice.service.delete-repair');
                    Route::delete('delete-product/{service_product}', 'deleteServiceProduct')->name('backoffice.service.delete-product');
                    Route::delete('/{service}', 'destroy')->name('backoffice.service.delete');
                    Route::put('/{service}', 'update')->name('backoffice.service.update');
                    Route::put('/approved/{service}', 'approvedService')->name('backoffice.service.approved');
                });

                Route::prefix('penjualan')->controller(SaleController::class)->group(function () {
                    Route::get('/', 'index')->name('backoffice.sale.index');
                    Route::get('/create', 'create')->name('backoffice.sale.create');
                    Route::get('/create/{sale}', 'createInvoice')->name('backoffice.sale.create-invoice');
                    Route::get('/invoice/{sale}', 'printInvoice')->name('backoffice.sale.invoice');
                    Route::post('/{sale}', 'store')->name('backoffice.sale.store');
                    Route::post('/add-product/{sale}', 'addProduct')->name('backoffice.sale.add-product');
                    Route::post('/add-product/{sale}', 'addProduct')->name('backoffice.sale.add-product');
                    Route::post('/update-qty/{sale_product}', 'updateQtyProduct')->name('backoffice.sale.update-qty-product');
                    Route::delete('delete-product/{sale_product}', 'deleteServiceProduct')->name('backoffice.sale.delete-product');
                    Route::delete('/cancel/{sale}', 'cancelSale')->name('backoffice.sale.cancel');
                    Route::delete('/{sale}', 'destroy')->name('backoffice.sale.delete');
                });

                Route::prefix('pembelian')->controller(PurchaseController::class)->group(function () {
                    Route::get('/', 'index')->name('backoffice.purchase.index');
                    Route::get('/create', 'create')->name('backoffice.purchase.create');
                    Route::get('/create/{purchase}', 'createInvoice')->name('backoffice.purchase.create-invoice');
                    Route::get('/detail/{purchase}', 'show')->name('backoffice.purchase.detail');
                    Route::post('/{purchase}', 'store')->name('backoffice.purchase.store');
                    Route::post('/add-product/{purchase}', 'addProduct')->name('backoffice.purchase.add-product');
                    Route::post('/update-qty/{purchase_product}', 'updateQtyProduct')->name('backoffice.purchase.update-qty-product');
                    Route::post('/update-price/{purchase_product}', 'updatePriceProduct')->name('backoffice.purchase.update-price-product');
                    Route::delete('delete-product/{purchase_product}', 'deletePurchaseProduct')->name('backoffice.purchase.delete-product');
                    Route::delete('/{purchase}', 'destroy')->name('backoffice.purchase.delete');
                });
            });

            Route::prefix('history')->controller(ReportController::class)->group(function () {
                Route::get('/service',  'serviceHistory')->name('backoffice.history.service');
                Route::get('/service/{service}', 'serviceDetailHistory')->name('backoffice.history.service-detail');
            });



            Route::prefix('laporan')->controller(ReportController::class)->group(function () {
                Route::get('/lservice', 'serviceReport')->name('backoffice.report.service');
                Route::get('/lservice/cetak', 'printServiceReport')->name('backoffice.report.service-print');
                Route::get('/lservice/export', 'exportServiceReport')->name('backoffice.report.service-export');
                Route::get('/lpenjualan', 'saleReport')->name('backoffice.report.sale');
                Route::get('/lpenjualan/cetak', 'printSaleReport')->name('backoffice.report.sale-print');
                Route::get('/lpenjualan/export', 'exportSaleReport')->name('backoffice.report.sale-export');
                Route::get('/lpembelian', 'purchaseReport')->name('backoffice.report.purchase');
                Route::get('/lpembelian/cetak', 'printPurchaseReport')->name('backoffice.report.purchase-print');
                Route::get('/lpembelian/export', 'exportPurchaseReport')->name('backoffice.report.purchase-export');
            });

            Route::prefix('stok-opname')->controller(StockCorrectionController::class)->group(function () {
                Route::get('/', 'index')->name('backoffice.stock-correction.index');
                Route::get('/create', 'create')->name('backoffice.stock-correction.create');
                Route::post('/', 'store')->name('backoffice.stock-correction.store');
                Route::delete('/{stock_correction}', 'destroy')->name('backoffice.stock-correction.delete');
            });

            Route::prefix('jurnal')->controller(JurnalController::class)->group(function () {
                Route::get('/', 'index')->name('backoffice.jurnal.index');
                Route::get('/create', 'create')->name('backoffice.jurnal.create');
                Route::get('/{jurnal}', 'edit')->name('backoffice.jurnal.edit');
                Route::post('/', 'store')->name('backoffice.jurnal.store');
                Route::delete('/{jurnal}', 'destroy')->name('backoffice.jurnal.delete');
                Route::put('/{jurnal}', 'update')->name('backoffice.jurnal.update');
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
                    Route::get('/list', 'listPayment')->name('backoffice.payment.list');
                    Route::get('/{payment}', 'edit')->name('backoffice.payment.edit');
                    Route::post('/', 'store')->name('backoffice.payment.store');
                    Route::post('/delete_all', 'deleteAll')->name('backoffice.payment.delete-all');
                    Route::delete('/{payment}', 'destroy')->name('backoffice.payment.delete');
                    Route::put('/{payment}', 'update')->name('backoffice.payment.update');
                });

                Route::prefix('satuan')->controller(UnitController::class)->group(function () {
                    Route::get('/', 'index')->name('backoffice.unit.index');
                    Route::get('/{unit}', 'edit')->name('backoffice.unit.edit');
                    Route::post('/', 'store')->name('backoffice.unit.store');
                    Route::post('/delete_all', 'deleteAll')->name('backoffice.unit.delete-all');
                    Route::delete('/{unit}', 'destroy')->name('backoffice.unit.delete');
                    Route::put('/{unit}', 'update')->name('backoffice.unit.update');
                });
            });
        });
    }
);
