        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('admin.dashboard') }}">
                <div class="sidebar-brand-icon">
                <img src="{{ asset('img/sk-logo-2.png') }}" alt="Logo" class="rounded" style="width: 40px;">
                </div>
                
                <div class="sidebar-brand-text mx-3">ADMIN</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>
            

            <!-- Divider -->
            <hr class="sidebar-divider">


            <!-- Heading -->
            <div class="sidebar-heading">
                Staff Operations
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-chart-area"></i>
                    <span>Monitor Operations</span>
                </a>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="{{ route('admin.game_sessions.index') }}">Sessions</a>
                        <a class="collapse-item" href="{{ route('admin.payments.index') }}">Payments</a>
                        <a class="collapse-item" href="{{ route('admin.queues.index') }}">Queues</a>
                        <a class="collapse-item" href="{{ route('admin.bookings.index') }}">Bookings</a>
                    </div>
                </div>
            </li>



            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

               <!-- Heading -->
            <div class="sidebar-heading">
                Admin Operations
            </div>

            <!--<li class="nav-item">
                <a class="nav-link" href="{{route('admin.bookings.index')}}">
                    <i class="fas fa-fw fa-chart-area"></i>
                    <span>Manage Bookings</span></a>
            </li>-->

            <li class="nav-item">
                <a class="nav-link" href="{{route('admin.courts.index')}}">
                    <i class="fas fa-baseball fa-chart-area"></i>
                    <span>Manage Courts</span></a>
            </li>

            <!-- Nav Item - Tables -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.users.index') }}">
                    <i class="fas fa-users"></i>
                    <span>Manage Users</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.daily_operations.index') }}">
                    <i class="fas fa-flag"></i>
                    <span>Daily Operations</span></a>
            </li>

    
        
            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>