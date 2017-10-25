@extends('spark::layouts.app')

@section('content')

    <div class="container">

        <div class="row">
            <div class="col-lg-offset-2 col-lg-8 col-lg-offset-2" style="border:solid 1px; margin-bottom: 10px;">

                <div class="row">
                    <div class="col-md-12 text-center">
                        <h2>Address</h2>
                    </div>
                </div>

                <div class="row">
                    <div class="text-center qr-code-holder"
                         data-content="{{ json_encode(["address" => $address->address, 'tag' => '']) }}"
                         data-width="200"
                         data-height="200">

                    </div>
                </div>

                <div class="row text-center" style="margin:10px 5px;">
                    <pre>{{$address->address}}</pre>
                </div>

                <div class="row">
                    <div class="form-group text-center">
                        <div class="col-sm-4"><label for="amount">Balance:</label></div>
                        <div class="col-sm-8"
                             style="color: #00008b; font-weight: 800;">{{ (new \App\Util\Iota())->unit($address->getBalance()) }}OTA
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group text-center">
                        <div class="col-sm-4"><label for="created">Created:</label></div>
                        <div class="col-sm-8"
                             style="color: #00008b; font-weight: 800;">{{ $address->created_at->diffForHumans() }}</div>
                    </div>
                </div>

                <div class="row" style="margin: 30px 0;">
                    <div class="form-group text-center col-md-12">
                        <div class="col-sm-5">
                            <a href="https://thetangle.org/address/{{ $address->address }}"
                               class="btn btn-success btn-lg btn-block" target="_blank">View on Tangle</a>
                        </div>

                        <div class="col-sm-2">

                        </div>
                        <div class="col-sm-5">
                            <a href="{{ route("Addresses") }}" class="btn btn-success btn-lg btn-block">Back</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection


@section('before-body-end')

@endsection