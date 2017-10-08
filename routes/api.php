<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register the API routes for your application as
| the routes are automatically authenticated using the API guard and
| loaded automatically by this application's RouteServiceProvider.
|
*/
// lk13hGruRQ76vMAQ0wufNrg63X4xuzrWuCBqywRl5jIgLVyPvYDNNUYGfbR7
Route::group(['middleware' => 'auth:api', 'namespace' => 'Api', 'as' => 'Api.'], function (){
    Route::get('/payments', ['uses' => 'PaymentsController@index'])->name("Payments");
    Route::post('/payments', ['uses' => 'PaymentsController@store'])->name("Payments.Create");
});


