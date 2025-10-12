<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

        <!-- Left Icons -->
 <ul class="navbar-nav align-items-center">
    <!-- Alerts -->
    <li class="nav-item mx-1">
        <a class="nav-link" href="{{ route('admin.booking_requests.index') }}">
            <i class="fas fa-bell fa-fw"></i>
            <span class="badge badge-danger badge-counter" id="notificationCount">0</span>
        </a>
    </li>
</ul>


<script>
    function fetchNotifications() {
        fetch("{{ route('admin.notifications.count') }}")
            .then(response => response.json())
            .then(data => {
                let badge = document.getElementById('notificationCount');
                badge.innerText = data.count > 0 ? data.count : '';
            });
    }

    // Fetch every 10 seconds
    setInterval(fetchNotifications, 10000);

    // Initial load
    fetchNotifications();
</script>


   

    <!-- Spacer pushes right content -->
    <div class="ml-auto d-flex align-items-center">

        <!-- User Name -->
        <span class="mr-3 d-none d-lg-inline text-gray-600 small">
            {{ Auth::user()->name }}
        </span>

 

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-primary btn-icon-split btn-sm">
                <span class="icon text-white-50">
                    <i class="fas fa-sign-out-alt"></i>
                </span>
                <span class="text">Log Out</span>
            </button>
        </form>

    </div>

</nav>
