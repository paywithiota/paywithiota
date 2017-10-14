let mix = require( 'laravel-mix' );
var path = require( 'path' );

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.less( 'resources/assets/less/app.less', 'public/css' )
    .copy( 'node_modules/sweetalert/dist/sweetalert.min.js', '../resources/assets/js/plugin/sweetalert/sweetalert.min.js' )
    .js( 'resources/assets/js/app.js', 'public/js' )
    .copy( 'node_modules/sweetalert/dist/sweetalert.css', 'public/css/sweetalert.css' )
    .combine( [
        'public/js/app.js',
        'resources/assets/js/plugin/jquery-ui/jquery-ui.min.js',
        'resources/assets/js/plugin/iota/iota.js',
        'resources/assets/js/plugin/sweetalert/sweetalert.min.js',
        'resources/assets/js/plugin/qrcode/jquery.qrcode.min.js',
        'resources/assets/js/common.js',
        'resources/assets/js/pages/payments/pay.js',
        'resources/assets/js/pages/payments/transfer.js'
    ], 'public/js/app.js' )
    .webpackConfig( {
        externals: [{xmlhttprequest: '{XMLHttpRequest:XMLHttpRequest}'}],

        resolve: {
            modules: [
                path.resolve( __dirname, 'vendor/laravel/spark/resources/assets/js' ),
                'node_modules'
            ],
            alias: {
                'vue$': 'vue/dist/vue.js'
            }
        }
    } );
