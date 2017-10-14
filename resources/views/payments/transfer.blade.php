@extends('spark::layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-6 col-sm-offset-3">
                <h1 class="text-center">Send To PayWithIOTA.com User</h1>

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
                                        <label class='control-label' for="email">PayWithIOTA.com Email: </label>
                                        <input id="email" name="email" placeholder="john@snow.com" class='form-control' required type='email'>
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

@section('before-body-end')
    <script>
        $( document ).ready( function()
        {
            $( "#email" ).autocomplete( {
                source: function( request, response )
                {
                    $.ajax( {
                        url: "{{ route('Search.User') }}",
                        dataType: "json",
                        data: {
                            term: request.term
                        },
                        success: function( data )
                        {
                            response( data );

                        }
                    } );
                },
                minLength: 3
            } );
        } );
    </script>
@endsection