<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
    <div class="container">

        <!-- Brand -->
        <a class="navbar-brand font-weight-bold d-flex align-items-center" href="{{ route('customer.dashboard') }}">
            <img src="{{ asset('img/sk-logo-2.png') }}" alt="Sporty Ka Logo" width="35" height="35" class="mr-2 rounded-circle border border-light">
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
                    <a class="nav-link" href="{{ route('customer.dashboard') }}"><i class="fas fa-home"></i> Dashboard</a>
                </li>
                <li class="nav-item px-2">
                    <a class="nav-link" href="{{ route('customer.booking_requests.create') }}"><i class="fas fa-calendar-plus"></i> Book Session </a>
                </li>
                <li class="nav-item px-2">
                    <a class="nav-link" href="{{ route('customer.booking_requests.index') }}"><i class="fas fa-bookmark"></i> My Bookings </a>
                </li>
            </ul>

            <!-- Right Side: User Button Dropdown -->
            <ul class="navbar-nav ms-auto ml-auto align-items-lg-center text-center text-lg-right">
                <li class="nav-item dropdown">
                    <button class="btn btn-light btn-sm rounded-pill px-3 dropdown-toggle w-100 w-lg-auto"
                            id="userDropdown" data-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle mr-1"></i> {{ Auth::user()->name }}
                    </button>
                    <div class="dropdown-menu dropdown-menu-right shadow mt-2 mt-lg-0" aria-labelledby="userDropdown">
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-user mr-2"></i> Profile
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="fas fa-sign-out-alt mr-2"></i> Log Out
                            </button>
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>
