<nav class="navbar navigation">
    <a class="navbar-brand logo" href="{{ route("Home") }}">
        <h1>PayWithIOTA</h1>
    </a>

    <button class="navbar-toggler hidden-lg-up float-lg-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" >
        <i class="tf-ion-android-menu"></i>
    </button>
    <div class="collapse navbar-toggleable-md" id="navbarResponsive">
        <ul class="nav navbar-nav menu float-lg-right" id="top-nav">
            <li class="">
                <a href="{{ route("About") }}">ABOUT</a>
            </li>
            <li class="">
                <a href="login">LOGIN</a>
            </li>
            <li class="">
                <a href="register">REGISTER</a>
            </li>
        </ul>
    </div>
</nav>