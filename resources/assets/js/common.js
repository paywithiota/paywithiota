/**
 * QR Code generator
 */
$( '.qr-code-holder' ).each( function()
{
    console.log( $( this ) )
    var width = parseInt( $( this ).data( 'width' ), 10 );
    var height = parseInt( $( this ).data( 'height' ), 10 );
    var content = $( this ).data( 'content' );

    if( typeof content === "object" )
    {
        content = JSON.stringify( content )
    }

    $( this ).qrcode( {
        width: width > 0 ? width : 200,
        height: height > 0 ? height : 200,
        text: content
    } );
} );

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

