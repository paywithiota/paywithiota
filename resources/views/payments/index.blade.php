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
                                <th>From</th>
                                <th>To</th>
                                <th>Address</th>
                                <th>Amount (IOTA)</th>
                                @if(request()->get('balance'))
                                    <th>Balance</th>
                                @endif
                                <th>Status</th>
                                <th>Created</th>
                                <th>Type</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($payments as $payment)
                                @if($payment->address)
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
                                        <td>{{ $payment->sender && $payment->sender->id != auth()->user()->id ? $payment->sender->email: "You"  }}</td>
                                        <td>{{ $payment->user_id == auth()->user()->id ? $payment->address->user_id ? "You" : "Address" : $payment->receiver->email }}</td>

                                        <td><a href="https://thetangle.org/address/{{$payment->address->address}}"
                                               target="_blank">{{ substr($payment->address->address, 0, 7) . '.....' .  substr($payment->address->address, -7, strlen($payment->address->address)) }}</a>
                                        </td>
                                        <td>{{ (new \App\Util\Iota())->unit($payment->price_iota) }}OTA</td>

                                        @if(request()->get('balance'))
                                            <td>{{ (new \App\Util\Iota())->unit($addressBalance) }}</td>
                                        @endif
                                        <td>{{ $payment->status ? 'Done' : 'Pending' }}</td>
                                        <td title="{{$payment->created_at->format('Y-m-d H:i:s')}} UTC">{{ $payment->created_at->diffForHumans()  }}</td>
                                        <td>
                                            <span style="color:{{ $payment->user_id != auth()->user()->id ? "red" : 'green' }}"> {{ $payment->user_id == auth()->user()->id ? "IN" : "OUT"  }}</span>
                                        </td>
                                    </tr>
                                @endif
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
