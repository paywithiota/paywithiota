@extends('spark::layouts.app')

@section('content')

    <div class="container">

        <div class="row">

        </div>
    </div>
@endsection


@section('before-body-end')
    <script>
        $( document ).ready( function()
        {
            setTimeout( function()
            {
                var startIndex = 0;
                var endIndex = typeof iotaAddressEndIndex === "undefined" ? 49 : iotaAddressEndIndex;

                iota.api.getAccountData( "{{$user->iota_seed}}", {start: startIndex, end: endIndex}, function( error, data )
                {
                    console.log( error )
                    console.log( data )
                } )
            }, 3000 );

        } );
    </script>
@endsection