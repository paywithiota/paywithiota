@extends('spark::layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-4 item-photo">
                <img style="max-width:100%;"
                     src="https://forum.iota.org/uploads/default/original/1X/970dd4e95825aa52d1e914cc40f9a4c43084c621.png"/>
            </div>
            <div class="col-xs-5">
                <h3>Product IOTA</h3>

                <div class="section" style="padding-bottom:20px;">
                    <form method="post" action="{{ route("Buy.Post") }}">

                        @if(isset($payment))
                            <input type="hidden" name="payment_id" value="{{ $payment->data->payment_id }}">
                            <input type="hidden" name="return_url" value="http://paywithiota.com">
                            <button class="btn btn-success"><span style="margin-right:20px" class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span>
                                Submit
                            </button>
                        @else
                            <div class="form-group">
                                <label>Api Token:</label>
                                <input placeholder="API Token" required type="text" name="api_token"
                                       value="8eqGV8GAeuhMv9PUWUQNMzDNpE6Mon8LaB1A7Gq2TKguR7lwLkfR8w6VgT77"></div>

                            <div class="form-group">
                                <label>Invoice Id:</label>
                                <input placeholder="Invoice Id" type="text" name="invoice_id" value="{{ uniqid() }}">
                            </div>
                            <div class="form-group">
                                <label>No. of IOTA:</label>
                                <input placeholder="No. of IOTA" required type="number" name="price_iota" value="1">
                            </div>
                            <div class="form-group">
                                <label>IPN URL (Optional):</label>
                                <input placeholder="IPN URL (Optional)" type="url" name="ipn" value="">
                            </div>
                            <div class="form-group">
                                <label>Return URL (Optional):</label>
                                <input type="text" placeholder="Return URL (Optional)" name="url" value="http://paywithiota.com">
                            </div>
                            <button class="btn btn-success"><span style="margin-right:20px" class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span>
                                Submit
                            </button>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

