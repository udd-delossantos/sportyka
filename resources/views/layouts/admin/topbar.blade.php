<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

   

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
