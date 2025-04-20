<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ToppingController;
use App\Http\Controllers\Admin\ComboController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\ProductExtraController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Home routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Menu routes
Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
Route::get('/menu/category/{category}', [MenuController::class, 'category'])->name('menu.category');
Route::get('/menu/product/{category}/{product}', [MenuController::class, 'product'])->name('menu.product');
Route::get('/menu/todays-specials', [MenuController::class, 'todaysSpecials'])->name('menu.todays-specials');

// Cart routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::get('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::post('/cart/apply-discount', [CartController::class, 'applyDiscount'])->name('cart.apply-discount');
Route::get('/cart/remove-discount', [CartController::class, 'removeDiscount'])->name('cart.remove-discount');
Route::post('/cart/add-upsell', [CartController::class, 'addUpsell'])->name('cart.add-upsell');

// Checkout routes
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
Route::get('/checkout/confirmation/{order}', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');
Route::post('/checkout/pickup-times', [CheckoutController::class, 'getPickupTimes'])->name('checkout.pickup-times');

// Admin routes - protected by basic auth middleware
Route::prefix('admin')->middleware(['auth.basic'])->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Products management
    Route::resource('products', ProductController::class);
    
    // Categories management
    Route::resource('categories', CategoryController::class);
    
    // Toppings management
    Route::resource('toppings', ToppingController::class);
    
    // Combos management
    Route::resource('combos', ComboController::class);
    
    // Orders management
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
    
    // Reports
    Route::get('reports/orders', [OrderController::class, 'report'])->name('reports.orders');
    Route::get('reports/orders/export', [OrderController::class, 'export'])->name('reports.orders.export');
    
    // Enhanced Reports with ReportController
    Route::get('reports/dashboard', [App\Http\Controllers\Admin\ReportController::class, 'dashboard'])->name('reports.dashboard');
    Route::get('reports/orders', [App\Http\Controllers\Admin\ReportController::class, 'orders'])->name('reports.orders');
    Route::get('reports/export-orders', [App\Http\Controllers\Admin\ReportController::class, 'exportOrders'])->name('reports.export-orders');
    Route::get('reports/products', [App\Http\Controllers\Admin\ReportController::class, 'products'])->name('reports.products');
    Route::get('reports/export-products', [App\Http\Controllers\Admin\ReportController::class, 'exportProducts'])->name('reports.export-products');
    Route::get('reports/discounts', [App\Http\Controllers\Admin\ReportController::class, 'discounts'])->name('reports.discounts');
    Route::get('reports/export-discounts', [App\Http\Controllers\Admin\ReportController::class, 'exportDiscounts'])->name('reports.export-discounts');
    
    // Settings
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    
    // Discounts
    Route::resource('discounts', DiscountController::class);
    Route::get('discounts/generate-code', [DiscountController::class, 'generateCode'])->name('discounts.generate-code');
    
    // Product Extras
    Route::resource('extras', ProductExtraController::class);
    Route::post('extras/bulk-toggle-active', [ProductExtraController::class, 'bulkToggleActive'])->name('extras.bulk-toggle-active');
    Route::get('extras/by-category', [ProductExtraController::class, 'getByCategory'])->name('extras.by-category');

    // Order routes
    Route::get('orders/{order}/debug', [OrderController::class, 'debug'])->name('orders.debug');
});

// Debug route to display combo products
Route::get('/debug/combos', function () {
    $products = \App\Models\Product::where('category_id', 3)
        ->orWhere('name', 'like', '%Combo%')
        ->get();
    
    echo "<h2>Combo Products</h2>";
    echo "<pre>";
    foreach ($products as $product) {
        echo "ID: {$product->id} - Name: {$product->name} - Slug: {$product->slug}<br>";
    }
    echo "</pre>";
    
    return "Done";
});

// Debug route to check a specific product
Route::get('/debug/product/{id}', function ($id) {
    $product = \App\Models\Product::find($id);
    
    if (!$product) {
        return "Product not found";
    }
    
    echo "<h2>Product Details</h2>";
    echo "<pre>";
    echo "ID: {$product->id}<br>";
    echo "Name: {$product->name}<br>";
    echo "Slug: {$product->slug}<br>";
    echo "Category ID: {$product->category_id}<br>";
    
    // Check specific conditions
    echo "<br>Condition Checks:<br>";
    echo "Contains '2 Medium Pizzas Combo': " . (str_contains($product->name, '2 Medium Pizzas Combo') ? 'Yes' : 'No') . "<br>";
    echo "Contains '2 Large Pizzas Combo': " . (str_contains($product->name, '2 Large Pizzas Combo') ? 'Yes' : 'No') . "<br>";
    echo "Contains '2 XLarge Pizzas Combo': " . (str_contains($product->name, '2 XLarge Pizzas Combo') ? 'Yes' : 'No') . "<br>";
    echo "Contains 'Pizzas Combo': " . (str_contains($product->name, 'Pizzas Combo') ? 'Yes' : 'No') . "<br>";
    echo "</pre>";
    
    return "Done";
});
