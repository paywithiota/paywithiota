@extends('spark::layouts.app')

@section('content')

    @php $balance = 0 @endphp
    <home :user="user" inline-template>
        <div class="container">
            <!-- Application Dashboard -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">Addresses</div>

                        <div class="panel-body">
                            <table class="table table-responsive">
                                <thead>
                                <tr>
                                    <th>Address</th>
                                    <th>Balance</th>
                                    <th>Created</th>
                                    <th>Updated</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($addresses as $address)

                                    @php
                                        $addressBalance =  $address->getBalance();
                                        $balance += $addressBalance;
                                    @endphp

                                    <tr>
                                        <td>
                                            <span>{{ $address->address }}</span>
                                            <a href="https://iotasear.ch/hash/{{$address->address}}"
                                               target="_blank"><i class="fa fa-external-link" aria-hidden="true"></i>
                                            </a>
                                        </td>

                                        <td>{{ $addressBalance / 1000000 >= 1 ? doubleval($addressBalance / 1000000 ). ' MIOTA' : $addressBalance . ' IOTA' }}</td>

                                        <td title="{{$address->created_at->format('Y-m-d H:i:s')}} UTC">{{ $address->created_at->diffForHumans()  }}</td>
                                        <td title="{{$address->updated_at->format('Y-m-d H:i:s')}} UTC">{{ $address->updated_at->diffForHumans()  }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                                @if(request()->get('balance'))
                                    <tfoot>
                                    <tr>
                                        <th scope="row">Total Balance</th>

                                        <td>{{ $balance / 1000000 >= 1 ? doubleval($balance / 1000000) . ' MIOTA' : $balance . ' IOTA' }}</td>
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
    </home>
@endsection
