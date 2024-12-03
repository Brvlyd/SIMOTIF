<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\ProdukTerjualController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    
    // Stock route - accessible by all authenticated users
    Route::get('/stock', [ProductController::class, 'index'])->name('stock');

    // Dashboard route
    Route::get('/dashboard', function () {
        $user = auth()->user();
        $route = $user->role . '.dashboard';
        return redirect()->route($route);
    })->name('dashboard');

    // Owner Routes
    Route::middleware('role:owner')->group(function () {
        Route::get('/owner/dashboard', function () {
            return view('dashboard', ['type' => 'owner']);
        })->name('owner.dashboard');
        
        // Product Management
        Route::resource('products', ProductController::class)->except(['show']);

        // View All Transactions
        Route::get('/transactions', [InventoryController::class, 'index'])->name('transactions.index');
        Route::get('/transactions/search', [InventoryController::class, 'search'])->name('transactions.search');
        
        // Reports
        Route::get('/reports', [ProductController::class, 'reports'])->name('reports');
        Route::get('/reports/export', [ProductController::class, 'export'])->name('reports.export');
    });

    // Warehouse Routes
    Route::middleware('role:warehouse')->group(function () {
        Route::get('/warehouse/dashboard', function () {
            return view('dashboard', ['type' => 'warehouse']);
        })->name('warehouse.dashboard');
        
        // Input Barang Routes
        Route::get('/warehouse/input-barang', [WarehouseController::class, 'inputBarang'])->name('warehouse.input-barang');
        Route::post('/warehouse/simpan-barang', [WarehouseController::class, 'simpanBarang'])->name('warehouse.simpan-barang');
        
        // Inventory Management - Product In
        Route::get('/product-in', [InventoryController::class, 'productIn'])->name('inventory.product-in');
        Route::post('/product-in', [InventoryController::class, 'storeProductIn']);
        Route::get('/product-in/history', [InventoryController::class, 'productInHistory'])
            ->name('inventory.product-in.history');
    });

    // Sales Routes
    Route::middleware('role:sales')->group(function () {
        Route::get('/sales/dashboard', function () {
            return view('dashboard', ['type' => 'sales']);
        })->name('sales.dashboard');
        
        // Updated Produk Terjual Routes
        Route::get('/products/sold', [ProductController::class, 'sold'])->name('products.sold');
        Route::get('/products/create-sale', [ProductController::class, 'createSale'])->name('products.create-sale');
        Route::post('/products/store-sale', [ProductController::class, 'storeSale'])->name('products.store-sale');
        Route::get('/products/search-sold', [ProductController::class, 'searchSoldProducts'])->name('products.searchSold');
        
        // Inventory Management - Product Out
        Route::get('/product-out', [InventoryController::class, 'productOut'])->name('inventory.product-out');
        Route::post('/product-out', [InventoryController::class, 'storeProductOut']);
        Route::get('/product-out/history', [InventoryController::class, 'productOutHistory'])
            ->name('inventory.product-out.history');
    });

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Stock Management
    Route::prefix('stock')->group(function () {
        Route::get('/alerts', [ProductController::class, 'alerts'])->name('stock.alerts');
        Route::get('/history/{product}', [ProductController::class, 'history'])->name('stock.history');
        Route::get('/search', [ProductController::class, 'search'])->name('stock.search');
    });

    // API Routes for AJAX requests
    Route::prefix('api')->group(function () {
        Route::get('/check-low-stock', [ProductController::class, 'checkLowStock']);
        Route::get('/product/search', [ProductController::class, 'search'])->name('products.search');
        Route::get('/transactions/chart-data', [InventoryController::class, 'getChartData']);
    });
});

// Fallback route
Route::fallback(function () {
    return redirect()->route('dashboard');
});