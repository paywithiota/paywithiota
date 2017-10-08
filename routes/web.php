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


Route::get('/', 'PaymentsController@index')->name("Payments");
Route::get('/addresses', 'PaymentsController@addresses')->name("Payments.Addresses");

Route::get('/payments/sync', function (){
    if (auth()->user()) {
        \Artisan::call("iota:payments:check", ['user' => auth()->user()->id]);

        return redirect(route("Payments"));
    }
})->name("Payments.Sync");

Route::get('send_test_email', function (){
   event( new \App\Events\PaymentCreated(\App\Payment::first()));
});