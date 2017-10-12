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
                                    <th>Created</th>
                                    <th>Updated</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($addresses as $address)
                                    <tr>
                                        <td>
                                            <a href="{{ route("Addresses.Show", ['address' => $address->id]) }}">{{ $address->address }}</a>
                                        </td>


                                        <td title="{{$address->created_at->format('Y-m-d H:i:s')}} UTC">{{ $address->created_at->diffForHumans()  }}</td>
                                        <td title="{{$address->updated_at->format('Y-m-d H:i:s')}} UTC">{{ $address->updated_at->diffForHumans()  }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </home>
@endsection
