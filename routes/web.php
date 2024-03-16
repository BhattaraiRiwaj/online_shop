<?php

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\HomeController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\AdminLoginController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\SubCategoryController;
use App\Http\Controllers\admin\TempImagesController;

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

Route::get('/', function () {
    return view('welcome');
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

        //sub category Routes
        Route::name('sub_categories.')->group(function () {
            Route::get('sub_category/index', [SubCategoryController::class, 'index'])->name('index');
            Route::get('sub_category/create', [SubCategoryController::class, 'create'])->name('create');
            Route::post('sub_category/store', [SubCategoryController::class, 'store'])->name('store');
            Route::get('sub_category/{id}/edit', [SubCategoryController::class, 'edit'])->name('edit');
            Route::patch('sub_category/{id}', [SubCategoryController::class, 'update'])->name('update');
            Route::delete('sub_category/{id}', [SubCategoryController::class, 'destroy'])->name('delete');
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
            Route::get('status/{id}', [BrandController::class, 'brandStatus'])->name('status');

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
});
