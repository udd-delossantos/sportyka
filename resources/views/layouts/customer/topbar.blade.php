<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
    <div class="container">

        <!-- Brand -->
        <a class="navbar-brand font-weight-bold" href="#">
            Sporty Ka?
        </a>

        <!-- Collapse Button -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Content -->
        <div class="collapse navbar-collapse" id="navbarNav">
            
            <!-- Left Side: Nav Items -->
            <ul class="navbar-nav mr-auto">
                <li class="nav-item px-2">
                    <a class="nav-link" href="#"><i class="fas fa-home"></i> Dashboard</a>
                </li>
                <li class="nav-item px-2">
                    <a class="nav-link" href="{{ route('customer.booking_requests.create') }}"><i class="fas fa-calendar-plus"></i> Book Session </a>
                </li>
                <li class="nav-item px-2">
                    <a class="nav-link" href="{{ route('customer.booking_requests.index') }}"><i class="fas fa-bookmark"></i> My Bookings </a>
                </li>

            </ul>

            <!-- Right Side: User + Logout -->
            <ul class="navbar-nav ml-auto align-items-center">
                <li class="nav-item d-flex align-items-center px-2">
                    <span class="mr-3 text-white small">
                        {{ Auth::user()->name }}
                    </span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-light  btn-sm rounded-pill px-3">
                            <i class="fas fa-sign-out-alt mr-1"></i> Log Out
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>
