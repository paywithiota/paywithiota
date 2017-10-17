@extends('spark::layouts.app')

@section('content')

    <div class="container">

        <div class="row">
            <div class="col-lg-offset-2 col-lg-8 col-lg-offset-2" style="border:solid 1px;">

                <div class="row">
                    <div class="col-md-12 text-center">
                        @if($totalAddresses > 0)
                            <p>It can take upto {{ ceil(($totalAddresses * 5) / 60) }} minute(s) to fetch your balance Total of {{ $totalAddresses }} addresses
                                are being checked for balance. </p>
                        @endif
                        <h2>Current Balance: <span id="accountBalance">Loading....</span></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('before-body-end')
    <script>
        var iotaAddressEndIndex = "{{ isset($totalAddresses) ? $totalAddresses + 35 : 49 }}";

        $( document ).ready( function()
        {
            setTimeout( function()
            {
                var startIndex = 0;
                var endIndex = typeof iotaAddressEndIndex === "undefined" ? 49 : iotaAddressEndIndex;

                iota.api.getInputs( "{{$user->iota_seed}}", {start: startIndex, end: endIndex}, function( error, data )
                {
                    if( error )
                    {
                        alert( error );
                        return false;
                    }

                    $( '#accountBalance' ).text( data.totalBalance + " IOTA" );
                } )
            }, 3000 );

        } );
    </script>
@endsection