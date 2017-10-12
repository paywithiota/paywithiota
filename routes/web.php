<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Route::group(['middleware' => 'web'], function ($router){

    /**
     * Non-logged in routes
     */
    Route::get("/pay", ['uses' => "PaymentsController@pay"])->name("Payments.Pay");
    Route::post("/pay", ['uses' => "PaymentsController@pay"])->name("Payments.Pay.Post");


    /**
     * Temp routes
     */
    Route::get("/product", ['uses' => "PaymentsController@product"])->name("Product");
    Route::post("/buy", ['uses' => "PaymentsController@buy"])->name("Buy.Post");


    /**
     * Login required
     */
    Route::group(['middleware' => 'auth'], function ($router){

        Route::get('/', 'PaymentsController@index')->name("Payments");
        Route::get('/home', 'PaymentsController@index')->name("Home");
        Route::get('/payments/sync', function (){
            if (auth()->user()) {
                \Artisan::call("iota:payments:check", ['user' => auth()->user()->id]);

                return redirect(route("Payments"));
            }
        })->name("Payments.Sync");

        Route::get('/addresses', 'AddressesController@index')->name("Addresses");
        Route::get('/addresses/create', 'AddressesController@create')->name("Addresses.Create");
    });
});