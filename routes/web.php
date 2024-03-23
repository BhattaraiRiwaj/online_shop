<?php

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\HomeController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\AdminLoginController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\ProductImageController;
use App\Http\Controllers\admin\ShippingController;
use App\Http\Controllers\admin\SubCategoryController;
use App\Http\Controllers\admin\TempImagesController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\ProductSubCategoryController;
use App\Http\Controllers\ShopController;

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

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/',[FrontController::class,'index'])->name('front.home');
Route::get('/shop/{categorySlug?}/{subCategorySlug?}',[ShopController::class,'index'])->name('front.shop');
Route::get('/product/{slug}',[ShopController::class,'product'])->name('front.product');
Route::get('/cart',[CartController::class,'cart'])->name('front.cart');
Route::post('/add-To-Cart',[CartController::class,'addToCart'])->name('front.addToCart');
Route::post('/update-Cart',[CartController::class,'updateCart'])->name('front.updateCart');
Route::post('/delete-item',[CartController::class,'deleteItem'])->name('front.deleteItem.cart');
Route::get('/checkout',[CartController::class,'checkout'])->name('front.checkout');
Route::post('/process-checkout',[CartController::class,'processCheckout'])->name('front.process.checkout');
Route::get('/thanks/{order_id}',[CartController::class,'thanks'])->name('front.thankYou');
Route::post('/get-order-summery',[CartController::class,'getOrderSummary'])->name('front.getOrderSummary');


//Auth Route

Route::group(['prefix' => 'account'], function () {
    Route::group(['middleware' => 'guest'], function () {
        Route::get('/login',[AuthController::class,'login'])->name('account.login');
        Route::post('/login',[AuthController::class,'authenticate'])->name('account.authenticate');

        Route::get('/register',[AuthController::class,'register'])->name('account.register');
        Route::post('/process-register',[AuthController::class,'processRegister'])->name('account.processRegister');
    });

    Route::group(['middleware' => 'auth'], function () {
        Route::get('/profile',[AuthController::class,'profile'])->name('account.profile');
        Route::get('/logout',[AuthController::class,'logout'])->name('account.logout');
     });
 });

Route::group(['prefix' => 'admin'], function () {

    Route::group(['middleware' => 'admin.guest'], function () {
        Route::get('/login', [AdminLoginController::class, 'index'])->name('admin.login');
        Route::post('/auhenticate', [AdminLoginController::class, 'auhenticate'])->name('admin.auhenticate');
    });



    Route::group(['middleware' => 'admin.auth'], function () {
        Route::get('/dashboard', [HomeController::class, 'index'])->name('admin.dashboard');
        Route::get('/logout', [HomeController::class, 'logout'])->name('admin.logout');


        //category Routes
        Route::prefix('category/')->name('categories.')->group(function () {
            Route::get('index', [CategoryController::class, 'index'])->name('index');
            Route::get('create', [CategoryController::class, 'create'])->name('create');
            Route::post('store', [CategoryController::class, 'store'])->name('store');
            Route::get('{id}/edit', [CategoryController::class, 'edit'])->name('edit');
            Route::patch('{id}', [CategoryController::class, 'update'])->name('update');
            Route::delete('{id}', [CategoryController::class, 'destroy'])->name('delete');

            //status
            Route::get('status/{id}', [CategoryController::class, 'userStatus'])->name('status');
            Route::get('show_home/{id}', [CategoryController::class, 'show_home'])->name('show_home');

            // temp-images.create
            Route::post('temp-image/upload', [TempImagesController::class, 'create'])->name('temp-images.create');
        });

        //sub category Routes
        Route::name('sub_categories.')->group(function () {
            Route::get('sub_category/index', [SubCategoryController::class, 'index'])->name('index');
            Route::get('sub_category/create', [SubCategoryController::class, 'create'])->name('create');
            Route::post('sub_category/store', [SubCategoryController::class, 'store'])->name('store');
            Route::get('sub_category/{id}/edit', [SubCategoryController::class, 'edit'])->name('edit');
            Route::patch('sub_category/{id}', [SubCategoryController::class, 'update'])->name('update');
            Route::delete('sub_category/{id}', [SubCategoryController::class, 'destroy'])->name('delete');


            Route::get('show_home/{id}', [SubCategoryController::class, 'show_home'])->name('show_home');
        });
        //status
            Route::get('subCategory/status/{id}',[SubCategoryController::class,'status'])->name('subCategory.status');


        //Brands Routes
        Route::name('brands.')->group(function () {
            Route::get('brands/index', [BrandController::class, 'index'])->name('index');
            Route::get('brands/create', [BrandController::class, 'create'])->name('create');
            Route::post('brands/store', [BrandController::class, 'store'])->name('store');
            Route::get('brands/{id}/edit', [BrandController::class, 'edit'])->name('edit');
            Route::patch('brands/{id}', [BrandController::class, 'update'])->name('update');
            Route::delete('brands/{id}', [BrandController::class, 'destroy'])->name('delete');
            //status
            Route::get('brand/status/{id}', [BrandController::class, 'status'])->name('status');
        });


        //product Routes
        Route::name('products.')->group(function(){
            Route::get('product/index', [ProductController::class, 'index'])->name('index');
            Route::get('product/create', [ProductController::class, 'create'])->name('create');
            Route::post('product/store', [ProductController::class, 'store'])->name('store');
            Route::get('product/{id}/edit', [ProductController::class, 'edit'])->name('edit');
            Route::patch('product/{id}', [ProductController::class, 'update'])->name('update');
            Route::delete('product/{id}', [ProductController::class, 'destroy'])->name('delete');

            //getProducts
            Route::get('get/products', [ProductController::class, 'getProducts'])->name('getProducts');

            //product Image Update
            Route::delete('product/image/delete', [ProductImageController::class, 'destroy'])->name('image');
            //product Image Update
            Route::post('product/image/update', [ProductImageController::class, 'update'])->name('imageUpdate');
            //status
            Route::get('status/{id}', [ProductController::class, 'productStatus'])->name('status');
        });

        //shipping Route
        Route::get('/shipping/create',[ShippingController::class,'create'])->name('shipping.create');
        Route::post('/shipping/store',[ShippingController::class,'store'])->name('shipping.store');
        Route::get('/shipping/{id}',[ShippingController::class,'edit'])->name('shipping.edit');
        Route::patch('/shipping/update/{id}',[ShippingController::class,'update'])->name('shipping.update');
        Route::delete('/shipping/delete/{id}',[ShippingController::class,'destroy'])->name('shipping.delete');

        //product Sub Categbories
        Route::get('/product-subCategories', [ProductSubCategoryController::class, 'index'])->name('product-subCategories.index');

        // temp-images.create
        Route::post('temp-image/upload', [TempImagesController::class, 'create'])->name('temp-images.create');

        //getslug
        Route::get('getSlug', function (Request $request) {
            $slug = '';
            if (!empty($request->title)) {
                $slug = Str::slug($request->title);
                return response()->json([
                    'status' => true,
                    'slug' => $slug,
                ]);
            }
        })->name('slug');
    });
});
