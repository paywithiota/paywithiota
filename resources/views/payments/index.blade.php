@extends('spark::layouts.app')

@section('content')

    @php $balance = 0 @endphp
    <div class="container">
        <!-- Application Dashboard -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Payments <a href="{{ route('Payments.Sync') }}" class="pull-right"><i class="fa fa-refresh"
                                                                                                                     aria-hidden="true"
                                                                                                                     title="Recheck pending payments"></i>
                        </a></div>

                    <div class="panel-body">
                        <table class="table table-responsive">
                            <thead>
                            <tr>
                                <th>Payment Id</th>
                                <th>Invoice Id</th>
                                <th>Address</th>
                                <th>Amount (USD)</th>
                                <th>Amount (IOTA)</th>
                                @if(request()->get('balance'))
                                    <th>Balance</th>
                                @endif
                                <th>Status</th>
                                <th>Created</th>
                                <th>Updated</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($payments as $payment)

                                @if(request()->get('balance'))
                                    @php
                                        $addressBalance =  $payment->address->getBalance();
                                        $balance += $addressBalance;
                                    @endphp
                                @endif

                                <tr class="{{ $payment->status ? 'status-done' : 'status-pending' }}">
                                    <th scope="row"><a
                                                href="{{ route("Payments.Show", ["payment" => base64_encode($payment->id)]) }}">{{ base64_encode($payment->id) }}</a>
                                    </th>
                                    <td>{{ $payment->invoice_id }}</td>
                                    <td><a href="https://iotasear.ch/hash/{{$payment->address->address}}"
                                           target="_blank">{{ substr($payment->address->address, 0, 7) . '.....' .  substr($payment->address->address, -7, strlen($payment->address->address)) }}</a>
                                    </td>
                                    <td>{{ $payment->price_usd ? '$' . $payment->price_usd : "-" }}</td>
                                    <td>{{ (new \App\Util\Iota())->unit($payment->price_iota) }}OTA</td>

                                    @if(request()->get('balance'))
                                        <td>{{ (new \App\Util\Iota())->unit($addressBalance) }}</td>
                                    @endif
                                    <td>{{ $payment->status ? 'Done' : 'Pending' }}</td>
                                    <td title="{{$payment->created_at->format('Y-m-d H:i:s')}} UTC">{{ $payment->created_at->diffForHumans()  }}</td>
                                    <td title="{{$payment->updated_at->format('Y-m-d H:i:s')}} UTC">{{ $payment->updated_at->diffForHumans()  }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            @if(request()->get('balance'))
                                <tfoot>
                                <tr>
                                    <th scope="row">Total Balance</th>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>{{ (new \App\Util\Iota())->unit($balance) }}</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
