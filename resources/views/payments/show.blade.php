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
                    <div class="text-center qr-code-holder"
                         data-content="{{ json_encode(["address" => $payment->address->address, 'tag' => '', 'amount' => $payment->price_iota]) }}"
                         data-width="200"
                         data-height="200">

                    </div>
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
                        <div class="col-sm-8" style="color: #00008b; font-weight: 800;">{{ (new \App\Util\Iota())->unit($payment->price_iota) }}OTA</div>
                    </div>
                </div>

                @foreach($payment->metadata as $key => $metadata)

                    @if(is_array($metadata))
                        @foreach($metadata as $subkey => $submetadata)
                            @if(is_array($submetadata))
                                @php($submetadata = json_encode($submetadata))
                            @endif
                            <div class="row">
                                <div class="form-group text-center">
                                    <div class="col-sm-4"><label>{{ $subkey }}:</label></div>
                                    <div class="col-sm-8"
                                         style="word-wrap: break-word">{{ $submetadata }}</div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="row">
                            <div class="form-group text-center">
                                <div class="col-sm-4"><label>{{ $key }}:</label></div>
                                <div class="col-sm-8"
                                     style="color: #00008b; font-weight: 800;">{{ $metadata }}</div>
                            </div>
                        </div>
                    @endif
                @endforeach
                <div class="row">
                    <div class="form-group text-center">
                        <div class="col-sm-4"><label for="created">Created:</label></div>
                        <div class="col-sm-8" style="color: #00008b; font-weight: 800;">{{ $payment->created_at->diffForHumans() }}</div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group text-center">
                        <div class="col-sm-4"><label for="payment_status">Payment Status:</label></div>
                        <div class="col-sm-8"
                             style="color: #00008b; font-weight: 800;">{{  $payment->status ? 'Completed' : 'Waiting for payment' }}
                            @if($payment->status == 0)
                                <a href="{{route("Payments.Pay", [

                    'payment_id' => base64_encode($payment->id),
                ])}}">Visit Pay Now page</a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row" style="margin: 30px 0;">
                    <div class="form-group text-center col-md-12">
                        <div class="col-sm-5">
                            <a href="https://iotasear.ch/hash/{{ $payment->address->address }}" class="btn btn-success btn-lg btn-block" target="_blank">Check
                                on
                                Tangle</a>
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

        var amount = '{{ (new \App\Util\Iota())->unit($payment->price_iota) }}OTA';
        var address = "{{ $payment->address->address }}";

        $( document ).ready( function()
        {

        } );

    </script>
@endsection