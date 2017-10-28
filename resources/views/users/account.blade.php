@extends('spark::layouts.app')

@section('content')

    <div class="container">

        <div class="row">
            <div class="col-lg-offset-2 col-lg-8 col-lg-offset-2" style="border:solid 1px;">

                <div class="row">
                    <div class="col-md-12 text-center">
                        @if($user->last_key_index > 0)
                            <p>It can take upto {{ ceil(($user->last_key_index * 5) / 60) }} minute(s) to fetch your balance Total
                                of {{ $user->last_key_index }} addresses
                                are being checked for balance. </p>
                        @endif
                        <h2>Current Balance: <span id="accountBalance">Loading....</span></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset("/js/iota.min.js") }}" type="text/javascript"></script>

@endsection

@section('before-body-end')

    <script>
        var iotaAddressEndIndex = "{{ isset($user) ? intval($user->last_key_index) : 49 }}";

        $( document ).ready( function()
        {
            setTimeout( function()
            {
                var startIndex = 0;
                var endIndex = typeof iotaAddressEndIndex === "undefined" ? 49 : parseInt( iotaAddressEndIndex, 10 );

                if( confirm( "Checking your account balance can take couple of minutes depending on your key index and your browser tab will become unresponsive, Do you want to continue?" ) )
                {
                    iota.api.getInputs( "{{$user->iota_seed}}", {start: startIndex, end: endIndex + 1}, function( error, data )
                    {
                        if( error )
                        {
                            alert( error );
                            return false;
                        }

                        $( '#accountBalance' ).text( data.totalBalance + " IOTA" );
                    } )
                }
                else
                {
                    window.location.href = "{{ route("Payments") }}";
                }

            }, 3000 );

        } );
    </script>
@endsection