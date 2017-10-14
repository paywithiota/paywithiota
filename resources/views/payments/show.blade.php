@extends('spark::layouts.app')

@section('content')
    <div class="container">

        <div class="row">
            <div class="col-lg-offset-2 col-lg-8 col-lg-offset-2" style="border:solid 1px; margin-bottom: 10px;">

                <div class="row">
                    <div class="col-md-12 text-center">
                        <h2>Payment Details</h2>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-offset-2 col-md-4 col-xs-6" style="margin: 10px 0;">
                        <em>Date: {{ $payment->created_at->format('d M Y') }}</em>
                    </div>
                </div>

                <div class="row">
                    <div id="qrcode" class="text-center"></div>
                </div>

                <div class="row text-center" style="margin:10px 5px;">
                    <pre>{{$payment->address->address}}</pre>
                </div>

                <div class="row">
                    <div class="form-group text-center">
                        <div class="col-sm-4"><label for="amount">Invoice #:</label></div>
                        <div class="col-sm-8" style="color: #00008b; font-weight: 800;">{{ $payment->invoice_id }}</div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group text-center">
                        <div class="col-sm-4"><label for="amount">Amount:</label></div>
                        <div class="col-sm-8" style="color: #00008b; font-weight: 800;">{{ (new \App\Util\Iota())->unit($payment->price_iota) }}</div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group text-center">
                        <div class="col-sm-4"><label for="created">Created:</label></div>
                        <div class="col-sm-8" style="color: #00008b; font-weight: 800;">{{ $payment->created_at->diffForHumans() }}</div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group text-center">
                        <div class="col-sm-4"><label for="payment_status">Payment Status:</label></div>
                        <div class="col-sm-8" style="color: #00008b; font-weight: 800;">{{ isset($payment) && $payment->status ? 'Successful' : 'Pending' }}</div>
                    </div>
                </div>

                <div class="row" style="margin: 30px 0;">
                    <div class="form-group text-center col-md-12">
                        <div class="col-sm-5">
                            <a href="https://iotasear.ch/hash/{{ $payment->address->address }}" class="btn btn-success btn-lg btn-block" target="_blank">View on Tangle</a>
                        </div>

                        <div class="col-sm-2">

                        </div>
                        <div class="col-sm-5">
                            <a href="{{ route("Payments") }}" class="btn btn-success btn-lg btn-block">Back</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('before-body-end')
    <script src="{{ asset('/js/jquery.qrcode.min.js') }}"></script>
    <script>

        var amount = '{{ $payment->price_iota }}';
        var address = "{{ $payment->address->address }}";

        $(document).ready(function () {

            setTimeout(function () {
                $('#qrcode').qrcode({
                    width: 200,
                    height: 200,
                    text: JSON.stringify({"address": address, "amount": amount, "tag": ""})
                });

            }, 2000);
        });

    </script>
@endsection