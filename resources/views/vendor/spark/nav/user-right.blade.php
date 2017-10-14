<!-- Right Side Of Navbar -->
<li><a href="{{ route("Payments") }}" class="{{ request()->routeIs("Payments") ? 'active' : '' }}">Payments</a></li>
<li><a href="{{ route("Addresses") }}" class="{{ request()->routeIs("Addresses") ? 'active' : '' }}">Addresses</a></li>
<li><a href="{{ route("Payments.Deposit.ShowForm") }}" class="{{ request()->routeIs("Payments.Deposit.ShowForm") ? 'active' : '' }}">Deposit</a></li>
<li><a href="{{ route("Payments.Transfer.ShowForm") }}" class="{{ request()->routeIs("Payments.Transfer.ShowForm") ? 'active' : '' }}">Transfer</a></li>