if( currentPageName === 'PaymentsTransferShowForm' )
{
    $( "#transferUserEmailAutocomplete" ).autocomplete( {
        source: function( request, callback )
        {
            $.ajax( {
                url: routes['Users.SearchByEmail'],
                dataType: "json",
                data: {
                    term: request.term
                },
                success: function( response )
                {
                    callback( response.data );
                }
            } );
        },
        minLength: 3
    } );
}