<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\ProdukTerjualController;
use App\Http\Controllers\ProductSaleController;
use Illuminate\Support\Facades\Route;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
});

// Protected Routes (Require Authentication)
Route::middleware('auth')->group(function () {
    // Logout Route
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    
    // Common Routes (accessible by all authenticated users)
    Route::get('/stock', [ProductController::class, 'index'])->name('stock');
    Route::get('/stock/search', [ProductController::class, 'search'])->name('products.search');

    // Dashboard Route with Role-based Redirect
    Route::get('/dashboard', function () {
        $user = auth()->user();
        $route = $user->role . '.dashboard';
        return redirect()->route($route);
    })->name('dashboard');

    // Owner Routes
    Route::middleware('role:owner')->group(function () {
        // Dashboard
        Route::get('/owner/dashboard', function () {
            return view('dashboard', ['type' => 'owner']);
        })->name('owner.dashboard');

        Route::get('/products/print-receipt/{id}', [ProductController::class, 'printReceipt'])
        ->name('products.print-receipt');
        
        // Product Management
        Route::resource('products', ProductController::class)->except(['show']);

        // Transaction Management
        Route::prefix('transactions')->name('transactions.')->group(function () {
            Route::get('/', [InventoryController::class, 'index'])->name('index');
            Route::get('/search', [InventoryController::class, 'search'])->name('search');
        });

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ProductController::class, 'reports'])->name('index');
            Route::get('/export', [ProductController::class, 'exportPDF'])->name('export');
        });
    });

    // Warehouse Routes
    Route::middleware('role:warehouse|owner')->group(function () {
        // Dashboard
        Route::get('/warehouse/dashboard', function () {
            return view('dashboard', ['type' => 'warehouse']);
        })->name('warehouse.dashboard');
        
        // Warehouse Management
        Route::prefix('warehouse')->name('warehouse.')->group(function () {
            Route::get('/input-barang', [WarehouseController::class, 'inputBarang'])->name('input-barang');
            Route::post('/simpan-barang', [WarehouseController::class, 'simpanBarang'])->name('simpan-barang');
            Route::get('/edit-barang/{id}', [WarehouseController::class, 'editBarang'])->name('edit-barang');
            Route::put('/update-barang/{id}', [WarehouseController::class, 'updateBarang'])->name('update-barang');
            Route::delete('/hapus-barang/{id}', [WarehouseController::class, 'hapusBarang'])->name('hapus-barang');
            Route::get('/riwayat-barang/{id}', [WarehouseController::class, 'riwayatBarang'])->name('riwayat-barang');
        });
        
        // Inventory Management
        Route::prefix('inventory')->name('inventory.')->group(function () {
            // Product In
            Route::get('/product-in', [InventoryController::class, 'productIn'])->name('product-in');
            Route::post('/product-in', [InventoryController::class, 'storeProductIn']);
            Route::get('/product-in/history', [InventoryController::class, 'productInHistory'])->name('product-in.history');
            
            // Stock Adjustments
            Route::post('/adjust-stock/{id}', [WarehouseController::class, 'adjustStock'])->name('adjust-stock');
        });
    });

    // Sales Routes
    Route::middleware('role:sales|owner')->group(function () {
        // Dashboard
        Route::get('/sales/dashboard', function () {
            return view('dashboard', ['type' => 'sales']);
        })->name('sales.dashboard');
        
        // Sales Management
        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/sold', [ProductSaleController::class, 'index'])->name('sold');
            Route::get('/create-sale', [ProductSaleController::class, 'create'])->name('create-sale');
            Route::post('/store-sale', [ProductSaleController::class, 'store'])->name('store-sale');
            Route::get('/search-sold', [ProductSaleController::class, 'index'])->name('searchSold');
            Route::get('/export-pdf', [ProductController::class, 'exportPDF'])->name('exportPdf');
        });
        
        // Inventory Out Management
        Route::prefix('inventory')->name('inventory.')->group(function () {
            Route::get('/product-out', [InventoryController::class, 'productOut'])->name('product-out');
            Route::post('/product-out', [InventoryController::class, 'storeProductOut']);
            Route::get('/product-out/history', [InventoryController::class, 'productOutHistory'])->name('product-out.history');
        });
    });

    // Profile Management Routes
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // API Routes for AJAX Requests
    Route::prefix('api')->group(function () {
        Route::get('/check-low-stock', [ProductController::class, 'checkLowStock']);
        Route::get('/transactions/chart-data', [InventoryController::class, 'getChartData']);
    });
});



// Fallback Route
Route::fallback(function () {
    return redirect()->route('dashboard');
});