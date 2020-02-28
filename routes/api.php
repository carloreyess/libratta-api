<?php

use Illuminate\Http\Request;
use App\User;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('new-user', function() {
    $data= new User();
    $data->name = 'Librarian';
    $data->email = 'librarian@kaklaze.com';
    $data->password = bcrypt('123123');
    $data->save();
    return $data;
});

Route::prefix('books')->group(function() {
    Route::post('index','BookController@index');
    Route::post('store','BookController@store');
    Route::post('update','BookController@update');
});

Route::prefix('students')->group(function() {
    Route::post('index','StudentController@index');
    Route::post('store','StudentController@store');
    Route::post('update','StudentController@update');
});

Route::prefix('barrow')->group(function() {
    Route::post('index','BarrowedBookController@index');
    Route::get('history/{id}','BarrowedBookController@history');
    Route::post('store','BarrowedBookController@store');
    Route::post('select-students','BarrowedBookController@select_students');
    Route::post('select-books','BarrowedBookController@select_books');
    Route::get('barrowed-books/{id}','BarrowedBookController@barrowed');
    Route::post('return-books','BarrowedBookController@return_books');
    Route::post('barrow-extend','BarrowedBookController@extend_barrow');
});

Route::prefix('dashboard')->group(function() {
    Route::post('count-books','DashboardController@count_books');
    Route::post('count-barrowed','DashboardController@count_barrowed');
    Route::post('count-available','DashboardController@count_available');
    Route::post('count-overdue','DashboardController@count_overdue');
    Route::post('category-chart','DashboardController@category_chart');
});

Route::prefix('search')->group(function() {
    Route::get('search-books/{name}','SearchController@search');
});

Route::prefix('accounts')->group(function() {
    Route::get('index','UserController@index');
    Route::post('store','UserController@store');
    Route::post('update','UserController@update');
});

Route::prefix('category')->group(function() {
    Route::post('index','CategoryController@index');
    Route::post('store','CategoryController@store');
    Route::post('update','CategoryController@update');
    Route::post('select-categories','CategoryController@select_categories');
});

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        // Below mention routes are public, user can access those without any restriction.
        // Create New User
        Route::post('register', 'AuthController@register');
        // Login User
        Route::post('login', 'AuthController@login');

        // Refresh the JWT Token
        Route::get('refresh', 'AuthController@refresh');

        // Below mention routes are available only for the authenticated users.
        Route::middleware('auth:api')->group(function () {
            // Get user info
            Route::get('user', 'AuthController@user');
            // Logout user from application
            Route::post('logout', 'AuthController@logout');
        });
    });
});
