        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.htmnl">
                <div class="sidebar-brand-icon">
                <img src="{{ asset('img/sk-logo-2.png') }}" alt="Logo" class="rounded" style="width: 40px;">
                </div>
                
                <div class="sidebar-brand-text mx-3">STAFF</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('staff.dashboard') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>
            

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Daily Operations
            </div>

            <!-- Nav Item - Charts 
            <li class="nav-item">
                <a class="nav-link" href="{{ route('staff.walk_ins.index') }}">
                    <i class="fas fa-fw fa-chart-area"></i>
                    <span>Manage Session</span></a>
            </li>-->

            <li class="nav-item">
                <a class="nav-link" href="{{ route('staff.game_sessions.index') }}">
                    <i class="fas fa-hourglass-end"></i>
                    <span>Sessions</span></a>
            </li>

            <!-- Nav Item - Tables -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('staff.payments.index') }}">
                    <i class="fas fa-money-bill"></i>
                    <span>Payments</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('staff.queues.index') }}">
                    <i class="fas fa-list-ol"></i>
                    <span>Queues</span></a>
            </li>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-bookmark"></i>
                    <span>Bookings</span>
                </a>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="{{ route('staff.bookings.index') }}">Schedule</a>
                        <a class="collapse-item" href="{{ route('staff.booking_requests.index') }}">Requests</a>
                    </div>
                </div>
            </li>

        
            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>