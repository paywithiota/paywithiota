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


Route::get("/deploy", function (Request $request){
    $response = [
        'output' => []
    ];

    $dir = $request->get('dir') ? $request->get('dir') : base_path();

    if ($dir) {

        $branchTo = $request->get('branchTo') ? $request->get('branchTo') : 'production';
        $branchFrom = $request->get('branchFrom') ? $request->get('branchFrom') : 'production';

        chdir($dir);

        # The commands
        $commands = array(
            'echo %userprofile%', # ssh keys at this location
            'git fetch --all',
            'git reset --hard',
            'git clean -f -d',
            'git checkout ' . $branchTo,
            'git pull origin ' . $branchFrom,
        );

        if ($request->get('composer') == 1) {
            $commands[] = 'composer update';
        }

        if ($request->get('migrate') == 1) {
            $commands[] = 'php artisan migrate --force';
        }

        if ($request->get('seed') == 1) {
            $commands[] = 'php artisan db:seed --force';
        }

        # Run the commands for output

        foreach ($commands AS $command) {
            # Run it
            $tmp = (shell_exec($command . ' 2>&1'));
            # Output
            $response['output'][$command] = trim($tmp);
        }
    }

    return response()->json($response);
});