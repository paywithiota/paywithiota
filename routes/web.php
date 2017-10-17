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

    Route::get("/about", ['uses' => "HomeController@about"])->name("About");
    Route::get("/terms", ['uses' => "HomeController@terms"])->name("Terms");

    Route::get('/', 'HomeController@index')->name("Home");

    /**
     * Temp routes
     */
    Route::get("/product", ['uses' => "PaymentsController@product"])->name("Product");
    Route::post("/buy", ['uses' => "PaymentsController@buy"])->name("Buy.Post");

    /**
     * Login required
     */
    Route::group(['middleware' => 'auth'], function ($router){


        Route::get('/payments/sync', function (){
            if (auth()->user()) {
                \Artisan::call("iota:payments:check", ['user' => auth()->user()->id]);

                return redirect(route("Payments"));
            }
        })->name("Payments.Sync");

        Route::get('/payments', 'PaymentsController@index')->name("Payments");
        Route::get('/home', 'PaymentsController@index');
        Route::get('/payments/deposit', 'PaymentsController@showDepositForm')->name("Payments.Deposit.ShowForm");
        Route::get('/payments/transfer', 'PaymentsController@showTransferForm')->name("Payments.Transfer.ShowForm");
        Route::post('/payments/transfer', 'PaymentsController@transfer')->name("Payments.Transfer");
        Route::post('/payments/update', 'PaymentsController@updateMetadata')->name("Payments.Update.Metadata");
        Route::get('/payments/{payment}', 'PaymentsController@show')->name("Payments.Show");
        Route::post('/payments/deposit', 'PaymentsController@deposit')->name("Payments.Deposit");
        Route::get('/addresses', 'AddressesController@index')->name("Addresses");
        Route::get('/addresses/create', 'AddressesController@create')->name("Addresses.Create");
        Route::get('/addresses/{address}', 'AddressesController@show')->name("Addresses.Show");
        Route::get('/users/search', 'UsersController@searchUserEmail')->name("Users.SearchByEmail");
        Route::get('/account', 'UsersController@getAccountData')->name("Users.Account");
    });
});