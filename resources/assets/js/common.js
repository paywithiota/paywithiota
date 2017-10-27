/**
 * QR Code generator
 */
$( '.qr-code-holder' ).each( function()
{
    createQR( $( this ) );
} );

function createQR( $object )
{
    var width = parseInt( $object.data( 'width' ), 10 );
    var height = parseInt( $object.data( 'height' ), 10 );
    var content = $object.data( 'content' );

    if( typeof content === "object" )
    {
        content = JSON.stringify( content )
    }

    try
    {
        $object.qrcode( {
            width: width > 0 ? width : 200,
            height: height > 0 ? height : 200,
            text: content
        } );
    }
    catch( error )
    {
        setTimeout( function()
        {
            createQR( $object )
        }, 3000 );
    }
}

/**
 * Url query param
 * @param uri
 * @param key
 * @param value
 * @returns {*}
 */
function queryParam( uri, key, value )
{
    var re = new RegExp( "([?&])" + key + "=.*?(&|$)", "i" );
    var separator = uri.indexOf( '?' ) !== - 1 ? "&" : "?";
    if( uri.match( re ) )
    {
        return uri.replace( re, '$1' + key + "=" + value + '$2' );
    }
    else
    {
        return uri + separator + key + "=" + value;
    }
}

/**
 * IOTA Lib
 */
var iotaLib = window.IOTA;
var iota = null;

if( typeof iotaLib !== "undefined" )
{
    const currentProviderProxy = new Proxy( {
        currentProvider: null
    }, {
        set: function( obj, prop, value )
        {
            obj[prop] = value;
            iota = new iotaLib( {'provider': iotaNodeUrl} );
            return true
        }
    } );

    currentProviderProxy.currentProvider = iotaNodeUrl;
}
