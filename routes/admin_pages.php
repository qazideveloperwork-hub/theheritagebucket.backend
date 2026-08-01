<?php

use App\Http\Controllers\ArtisanStoryController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CategorySliderController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\FavouriteProductController;
use App\Http\Controllers\HomeSliderController;
use App\Http\Controllers\InfoController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\NewArrivalController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImportExportController;
use App\Http\Controllers\ShippingPolicyController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ShippingCostController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;

// Registered under the auth-protected group in web.php.

Route::get('/products', [ProductController::class, 'index'])->name('products.list');
Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
Route::post('/products', [ProductController::class, 'store'])->name('products.store');
Route::get('/products/import', [ProductImportExportController::class, 'importForm'])->name('products.import');
Route::post('/products/import', [ProductImportExportController::class, 'import'])->name('products.import.store');
Route::get('/products/import/template', [ProductImportExportController::class, 'template'])->name('products.import.template');
Route::get('/products/export', [ProductImportExportController::class, 'export'])->name('products.export');
Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

Route::get('/categories', [CategoryController::class, 'index'])->name('categories.list');
Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

Route::get('/orders', [OrderController::class, 'index'])->name('orders.list');
Route::get('/orders/delayed', [OrderController::class, 'delayed'])->name('orders.delayed');
Route::get('/orders/completed', [OrderController::class, 'completed'])->name('orders.completed');
Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');

Route::get('/settings/shipping', [ShippingCostController::class, 'index'])->name('settings.shipping');
Route::put('/settings/shipping/{product}', [ShippingCostController::class, 'update'])->name('settings.shipping.update');
Route::view('/settings/tax', 'settings.tax')->name('settings.tax');
Route::get('/settings/options', [SettingController::class, 'edit'])->name('settings.options');
Route::put('/settings/options', [SettingController::class, 'update'])->name('settings.options.update');

Route::get('/sliders/home-product', [HomeSliderController::class, 'edit'])->name('sliders.home');
Route::put('/sliders/home-product', [HomeSliderController::class, 'update'])->name('sliders.home.update');
Route::get('/sliders/category', [CategorySliderController::class, 'index'])->name('sliders.category');
Route::post('/sliders/category', [CategorySliderController::class, 'store'])->name('sliders.category.store');
Route::delete('/sliders/category/{slider}', [CategorySliderController::class, 'destroy'])->name('sliders.category.destroy');
Route::view('/sliders/banner', 'slider.banner')->name('sliders.banner');

Route::get('/sliders/new-arrival', [NewArrivalController::class, 'index'])->name('sliders.new-arrival');
Route::post('/sliders/new-arrival', [NewArrivalController::class, 'store'])->name('sliders.new-arrival.store');
Route::delete('/sliders/new-arrival/{slider}', [NewArrivalController::class, 'destroy'])->name('sliders.new-arrival.destroy');

Route::get('/sliders/artisan-story', [ArtisanStoryController::class, 'edit'])->name('sliders.artisan-story');
Route::put('/sliders/artisan-story', [ArtisanStoryController::class, 'update'])->name('sliders.artisan-story.update');

Route::get('/sliders/favourite-product', [FavouriteProductController::class, 'index'])->name('sliders.favourite-product');
Route::post('/sliders/favourite-product', [FavouriteProductController::class, 'store'])->name('sliders.favourite-product.store');
Route::delete('/sliders/favourite-product/{slider}', [FavouriteProductController::class, 'destroy'])->name('sliders.favourite-product.destroy');

Route::get('/purchase/stock', [InventoryController::class, 'index'])->name('purchase.stock');
Route::put('/purchase/stock/{inventory}', [InventoryController::class, 'update'])->name('inventory.update');

Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.list');
Route::get('/suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');

Route::get('/colors', [ColorController::class, 'index'])->name('colors.list');
Route::get('/colors/create', [ColorController::class, 'create'])->name('colors.create');
Route::post('/colors', [ColorController::class, 'store'])->name('colors.store');
Route::get('/colors/{color}/edit', [ColorController::class, 'edit'])->name('colors.edit');
Route::put('/colors/{color}', [ColorController::class, 'update'])->name('colors.update');
Route::delete('/colors/{color}', [ColorController::class, 'destroy'])->name('colors.destroy');

Route::get('/sizes', [SizeController::class, 'index'])->name('sizes.list');
Route::get('/sizes/create', [SizeController::class, 'create'])->name('sizes.create');
Route::post('/sizes', [SizeController::class, 'store'])->name('sizes.store');
Route::get('/sizes/{size}/edit', [SizeController::class, 'edit'])->name('sizes.edit');
Route::put('/sizes/{size}', [SizeController::class, 'update'])->name('sizes.update');
Route::delete('/sizes/{size}', [SizeController::class, 'destroy'])->name('sizes.destroy');

Route::get('/info', [InfoController::class, 'index'])->name('infos.list');
Route::get('/info/create', [InfoController::class, 'create'])->name('infos.create');
Route::post('/info', [InfoController::class, 'store'])->name('infos.store');
Route::get('/info/{info}/edit', [InfoController::class, 'edit'])->name('infos.edit');
Route::put('/info/{info}', [InfoController::class, 'update'])->name('infos.update');
Route::delete('/info/{info}', [InfoController::class, 'destroy'])->name('infos.destroy');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.list');
Route::get('/blog/create', [BlogController::class, 'create'])->name('blog.create');
Route::post('/blog', [BlogController::class, 'store'])->name('blog.store');
Route::get('/blog/{blogPost}/edit', [BlogController::class, 'edit'])->name('blog.edit');
Route::put('/blog/{blogPost}', [BlogController::class, 'update'])->name('blog.update');
Route::delete('/blog/{blogPost}', [BlogController::class, 'destroy'])->name('blog.destroy');

Route::get('/pages/shipping-policy', [ShippingPolicyController::class, 'edit'])->name('pages.shipping-policy');
Route::put('/pages/shipping-policy', [ShippingPolicyController::class, 'update'])->name('pages.shipping-policy.update');

Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
Route::post('/users', [UserController::class, 'store'])->name('users.store');
Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
Route::patch('/users/{user}/status', [UserController::class, 'updateStatus'])->name('users.status');
Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
Route::get('/employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
Route::patch('/employees/{employee}/status', [EmployeeController::class, 'updateStatus'])->name('employees.status');
Route::post('/employees/{employee}/reset-password', [EmployeeController::class, 'resetPassword'])->name('employees.reset-password');
Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
Route::get('/media', [MediaController::class, 'index'])->name('media.index');
Route::post('/media', [MediaController::class, 'store'])->name('media.store');
Route::put('/media/{media}', [MediaController::class, 'update'])->name('media.update');
Route::delete('/media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');
