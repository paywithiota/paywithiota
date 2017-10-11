<!-- Right Side Of Navbar -->
<li><a href="{{ route("Payments") }}" class="{{ request()->routeIs("Payments") ? 'active' : '' }}">Payments</a> </li>
<li><a href="{{ route("Addresses") }}" class="{{ request()->routeIs("Addresses") ? 'active' : '' }}">Addresses</a> </li>