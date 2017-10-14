@extends('spark::layouts.app')

@section('content')
    <div class="container">
        <div class="row">

            <div class="col-sm-6 col-sm-offset-3">
                <h1 class="text-center">Deposit Fund</h1>

                <div class="panel panel-default">
                    <div class="panel-heading">Enter amount, select IOTA unit and click on "Create Request" button.
                    </div>
                    <div class="panel-body">
                        <div class="tab-content">
                            <form method="post" action="{{ route("Payments.Deposit") }}">
                                <div class='form-row'>
                                    <div class='form-group required'>
                                        <label class='control-label' for="amount">Amount: <span id="usdAmount"></span></label>
                                        <input id="amount" name="amount" class='form-control' required type='number'>
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

                                        <button class='form-control btn btn-primary' type='submit'> Create Request →</button>
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

@endsection