@extends('spark::layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-6 col-sm-offset-3">
                <h1 class="text-center">Transfer IOTA to PayWithIOTA.com user or an Address</h1>

                <div class="panel panel-default">
                    <div class="panel-heading">Enter email, amount and select IOTA unit. As soon as you click "Create Transfer Request" button, you will be
                        redirected to transfer/payment page to complete payment. You have options to pay with your PayWithIOTA.com account, Send directly to the
                        address, or using your SEED.
                    </div>
                    <div class="panel-body">
                        <div class="tab-content">
                            <form method="post" action="{{ route("Payments.Transfer") }}">
                                <div class='form-row'>
                                    <div class='form-group required'>
                                        <label class='control-label' for="transferType">Send To: </label>
                                        <select id="transferType" name="type" class='form-control'>
                                            <option value="user" selected>PayWithIOTA.com User</option>
                                            <option value="address">IOTA Address</option>
                                        </select>
                                    </div>
                                </div>
                                <div class='form-row user-email-holder'>
                                    <div class='form-group required'>
                                        <label class='control-label' for="transferUserEmailAutocomplete">PayWithIOTA.com Email: </label>
                                        <input id="transferUserEmailAutocomplete" name="email" placeholder="john@snow.com" class='form-control'
                                               type='email'>
                                    </div>
                                </div>
                                <div class='form-row iota-address-input-holder' style="display: none;">
                                    <div class='form-group required'>
                                        <label class='control-label' for="transferUserEmailAutocomplete">IOTA Address: </label>
                                        <input id="iotaAddress" name="address" placeholder="" class='form-control'
                                               type='text'>
                                    </div>
                                </div>
                                <div class='form-row'>
                                    <div class='form-group required'>
                                        <label class='control-label' for="amount">Amount: <span
                                                    id="usdAmount"></span></label>
                                        <input id="amount" name="amount" placeholder="1000" class='form-control' required type='number'>
                                    </div>
                                </div>
                                <div class='form-row'>
                                    <div class='form-group card required'>
                                        <label for="unit" class='control-label'>Unit: </label>
                                        <select id="unit" name="unit">
                                            @foreach(config("services.iota.units") as $unit => $value)
                                                <option value="{{ $unit }}" data-multiply="{{ $value }}">{{ $unit }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class='form-row'>
                                    <div class='form-group'>
                                        <label class='control-label'></label>
                                        <button class='form-control btn btn-success' type='submit'> Create Transfer Request→</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        routes['Users.SearchByEmail'] = "{{ route("Users.SearchByEmail") }}";
    </script>
@endsection

@section('before-body-end')
    <script src="{{ asset("/js/jquery-ui.min.js") }}" type="text/javascript"></script>
    <script>
        $( document ).on( "change", "#transferType", function()
        {
            var $transferType = $( this ).val();

            if( $transferType === "address" )
            {
                $( '.user-email-holder' ).hide();
                $( '.iota-address-input-holder' ).show();
                $( '#transferUserEmailAutocomplete' ).val( '' );
            }
            else
            {
                $( '.user-email-holder' ).show();
                $( '.iota-address-input-holder' ).hide();
                $( '#iotaAddress' ).val( '' );
            }
        } );

        $( document ).ready( function()
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
        } );

    </script>
@endsection