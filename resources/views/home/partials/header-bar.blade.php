<nav class="navbar navigation">
    <a class="navbar-brand logo" href="{{ route("Home") }}">
        <h1>PayWithIOTA</h1>
    </a>

    <button class="navbar-toggler hidden-lg-up float-lg-right" type="button" data-toggle="collapse" data-target="#navbarResponsive">
        <i class="tf-ion-android-menu"></i>
    </button>
    <div class="collapse navbar-toggleable-md" id="navbarResponsive">
        <ul class="nav navbar-nav menu float-lg-right" id="top-nav">
            <li class="">
                <a href="{{ route("About") }}">About</a>
            </li>


        @if (Auth::check())
            <!-- Right Side Of Navbar -->
                <li><a href="{{ route("Users.Account") }}" class="{{ request()->routeIs("Users.Account") ? 'active' : '' }}">My Account</a></li>
                <li><a href="{{ route("Payments") }}" class="{{ request()->routeIs("Payments") ? 'active' : '' }}">Payments</a></li>
                <li><a href="{{ route("Addresses") }}" class="{{ request()->routeIs("Addresses") ? 'active' : '' }}">Addresses</a></li>
                <li><a href="{{ route("Payments.Deposit.ShowForm") }}" class="{{ request()->routeIs("Payments.Deposit.ShowForm") ? 'active' : '' }}">Deposit</a>
                </li>
                <li><a href="{{ route("Payments.Transfer.ShowForm") }}"
                       class="{{ request()->routeIs("Payments.Transfer.ShowForm") ? 'active' : '' }}">Transfer</a>
                </li>
            @else
                <li class="">
                    <a href="login">Login</a>
                </li>
                <li class="">
                    <a href="register">Register</a>
                </li>
            @endif


        </ul>
    </div>
</nav>