<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta Information -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', config('app.name'))</title>

    <!-- Fonts -->
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600' rel='stylesheet' type='text/css'>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css' rel='stylesheet' type='text/css'>

    <!-- CSS -->
    <link href="/css/sweetalert.css" rel="stylesheet">
    <link href="/css/jquery.ui.autocomplete.css" rel="stylesheet">
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">


    <!-- Global Spark Object -->
    <script>
        window.Spark = <?php echo json_encode(array_merge(
            Spark::scriptVariables(), []
        )); ?>;

        var routes = {};
        var currentPageName = '{{ str_replace('.', '', request()->route()->getName()) }}';
        var iotaNodeUrl = "{{ (new \App\Util\Iota())->getWorkingNode(false) }}";
    </script>

    <!-- Scripts -->
    @yield('scripts', '')

</head>
<body class="with-navbar">
<div id="spark-app" v-cloak>
    <!-- Navigation -->
@if (Auth::check())
    @include('spark::nav.user')
@else
    @include('spark::nav.guest')
@endif

@include('flash::message')

<!-- Main Content -->
@yield('content')

<!-- Application Level Modals -->
    @if (Auth::check())
        @include('spark::modals.notifications')
        @include('spark::modals.support')
        @include('spark::modals.session-expired')
    @endif
</div>

@if(config('services.iota.donation_address'))

    <br/>
    <br/>
    <br/>
    <div class="container">
        <div class="input-group">
            <span class="input-group-addon">Donations&nbsp;<i class="fa fa-heart"></i></span>
            <input type="text" class="form-control"
                   value="{{ config('services.iota.donation_address') }}" readonly="">
        </div>
    </div>
@endif
<!-- JavaScript -->
<script src="{{ mix('js/app.js') }}"></script>

@yield('before-body-end')
</body>
</html>
