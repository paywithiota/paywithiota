@extends('spark::layouts.app')

@section('content')
    <div class="container">


        <div class="row">
            <div class="col-lg-offset-2 col-lg-8 col-lg-offset-2" style="border:solid 1px;">

                <div class="row">
                    <div class="col-md-12 text-center">
                        <h2>Payment Details</h2>
                    </div>
                </div>

                <div class="row" style="margin: 10px 0;">
                    <div class="col-md-offset-1 col-md-5 col-xs-6">
                        <em>Date: {{ \Carbon\Carbon::now()->format('d M Y') }}</em>
                    </div>
                    <div class="col-md-4 col-md-offset-2 col-xs-6">
                        <em>Invoice #: {{ $payment->invoice_id }}</em>
                    </div>
                </div>


                <div class="row">
                    <div class="text-center qr-code-holder"
                         data-content="{{ json_encode(["address" => $payment->address->address, 'tag' => '', 'amount' => $payment->price_iota]) }}"
                         data-width="200"
                         data-height="200"></div>
                </div>

                <div class="row text-center" style="margin:10px 5px;">
                    <pre>{{$payment->address->address}}</pre>
                </div>

                <div class="row">
                    <h4 class="text-center" style="color: #00008b; font-weight: 800;">{{ (new \App\Util\Iota())->unit($payment->price_iota) }}OTA</h4>
                </div>

                <div class="row">
                    <div class="form-group col-md-offset-4 col-md-4 col-md-offset-4">
                        <div class="radio">
                            <label>
                                <input type="radio" class="pay_with_input"
                                       data-login="{{ isset($user) && $user ? '1' : '0' }}" name="pay_with"
                                       value="account">PayWithIOTA.com
                                account {{ isset($user) && $user ? '' : '(Login Required)' }}</label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" class="pay_with_input" name="pay_with"
                                       value="direct" checked>Send To
                                Address/QR code</label>
                        </div>
                        <div class="radio">
                            <label><input type="radio" class="pay_with_input" name="pay_with"
                                          value="seed">Seed (Not Recommended)</label>
                        </div>
                    </div>
                </div>

                <div class="row" style="margin-bottom:30px;">
                    <div class="form-group pay_with_seed text-center" style="display:none;">
                        <div class="col-sm-4"><label for="customSeed">Input your Seed:</label></div>
                        <div class="col-sm-8"><input type="password" placeholder="SEED" value="" class="form-control" id="customSeed"></div>
                    </div>
                </div>


                <div class="row" style="margin-bottom: 30px;">
                    <div class="col-md-5 col-md-offset-4">

                        <button style="display:none;" id="payNow" type="button"
                                data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Processing"
                                class="btn btn-success btn-lg btn-block"
                                data-ready-text='Pay Now <span class="glyphicon glyphicon-chevron-right"></span>'>
                            Start Transfer
                        </button>

                        <button id="payNowWithAddress" type="button"
                                class="btn btn-success btn-lg btn-block">
                            Sent To Address
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('scripts')
    <script>
        var paymentId = '{{ base64_encode($payment->id) }}',
            amount = '{{ $payment->price_iota }}',
            amountWithUnit = '{{ (new \App\Util\Iota())->unit($payment->price_iota )}}OTA',
            address = "{{ $payment->address->address }}",
            returnUrl = "{{ isset($returnUrl) ? $returnUrl : '' }}",
            seed = "{{ isset($user) && $user ? $user->iota_seed : '' }}",
            iotaAddressEndIndex = "{{ isset($totalAddresses) && $totalAddresses > 60 ? $totalAddresses + 10 : 60 }}";
        routes['Payments.Update.Metadata'] = "{{route("Payments.Update.Metadata")}}";
    </script>
@endsection