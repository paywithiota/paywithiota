var IOTA = require( '../../../node_modules/iota.lib.js/lib/iota' );

// Create IOTA instance with host and port as provider
var iota = new IOTA( {
    'host': '{{ (new \App\Util\Iota())->getWorkingNode() }}',
} );

// Generate new address by options
iota.api.getNewAddress( "{{$user->iota_seed}}", {
    'index': {{$newKeyIndex}},
    'checksum': false,
    'total': 1,
    'security': 2,
    'returnAll': false
}, function( error, senderAddress )
{
    if( error )
    {
        // callback( error );
        return false;
    }

    console.log( senderAddress[0] )

} );
