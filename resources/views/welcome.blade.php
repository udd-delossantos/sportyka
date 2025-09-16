{{-- resources/views/welcome.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>PROVING GROUNDS SPORTS CENTER</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="{{ asset('landing/assets/favicon.ico') }}" />
    <!-- Bootstrap icons-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <!-- Google fonts-->
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic" rel="stylesheet" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="{{ asset('landing/css/styles.css') }}" rel="stylesheet" />
    <style>
        /* Remove default overlay in masthead */
    header.masthead::before {
        content: none !important;
        background: none !important;
    }

    </style>
</head>
<body>
    <!-- Navigation-->
    <nav class="navbar navbar-light bg-light static-top">
        <div class="container">
            <a class="navbar-brand" href="{{ url('') }}">Book Now!</a>
            <div>
              @auth
                @php
                    $role = Auth::user()->role;
                    $dashboardRoute = match ($role) {
                        'admin' => route('admin.dashboard'),
                        'staff' => route('staff.dashboard'),
                        'customer' => route('customer.dashboard'),
                        default => url('/'),
                    };
                @endphp

                <a href="{{ $dashboardRoute }}" class="btn btn-primary me-2">
                    Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary me-2">
                    Log in
                </a>

                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-primary">
                        Register
                    </a>
                @endif
            @endauth


            </div>
        </div>
    </nav>

    <!-- Masthead-->
    <header class="masthead text-white d-flex align-items-center" 
            style="background: url('{{ asset('landing/assets/img/pg.jpg') }}') no-repeat center center; 
                background-size: cover; 
                height:80vh;">

        <div class="container position-relative">
            <div class="row justify-content-center">
                <div class="col-xl-8">
                    <div class="text-center">
    

                    
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Icons Grid-->
    <section class="features-icons bg-light text-center">
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                    <div class="features-icons-item mx-auto mb-5 mb-lg-0 mb-lg-3">
                        <div class="features-icons-icon d-flex"><i class="bi-calendar-check m-auto text-primary"></i></div>
                        <h3>Hassle-Free Court Booking</h3>
                        <p class="lead mb-0">Reserve your preferred court anytime, anywhere through our user-friendly online booking system.</p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="features-icons-item mx-auto mb-5 mb-lg-0 mb-lg-3">
                        <div class="features-icons-icon d-flex"><i class="bi-building m-auto text-primary"></i></div>
                        <h3>Multi-Sport Modern Facility</h3>
                        <p class="lead mb-0">Enjoy playing in a professionally managed facility with badminton, tennis, pickleball, and basketball courts.</p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="features-icons-item mx-auto mb-0 mb-lg-3">
                        <div class="features-icons-icon d-flex"><i class="bi-clock m-auto text-primary"></i></div>
                        <h3>Real-Time Court Availability</h3>
                        <p class="lead mb-0">Check availability in real-time and get updates for open slots and events.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Image Showcases-->
    <section class="showcase">
        <div class="container-fluid p-0">
           
            <div class="row g-0">
                <div class="col-lg-6 order-lg-2 showcase-img" style="background-image: url('{{ asset('landing/assets/img/badminton.jpg') }}')"></div>
                <div class="col-lg-6 order-lg-1 my-auto showcase-text">
                    <h2 class="text-primary">Badminton Court</h2>
                    <p class="lead mb-0">Smash, drop, and rally on one of our six professional-grade badminton courts.</p>
                </div>
            </div>
            <div class="row g-0">
                <div class="col-lg-6 showcase-img" style="background-image: url('{{ asset('landing/assets/img/tennis.jpg') }}')"></div>
                <div class="col-lg-6 my-auto showcase-text">
                    <h2 class="text-primary">Tennis Court</h2>
                    <p class="lead mb-0">Play like a pro on our regulation-size tennis court, perfect for singles or doubles matches.</p>
                </div>
            </div>
        </div>
         <div class="row g-0">
                <div class="col-lg-6 order-lg-2 showcase-img" style="background-image: url('{{ asset('landing/assets/img/pickle.jpg') }}')"></div>
                <div class="col-lg-6 order-lg-1 my-auto showcase-text">
                    <h2 class="text-primary">Pickleball Court</h2>
                    <p class="lead mb-0">Enjoy exciting rallies on our dedicated pickleball court, perfect for beginners and seasoned players alike.</p>
                </div>
            </div>
            <div class="row g-0">
                <div class="col-lg-6 showcase-img" style="background-image: url('{{ asset('landing/assets/img/bball.jpg') }}')"></div>
                <div class="col-lg-6 my-auto showcase-text">
                    <h2 class="text-primary">Basketball Court</h2>
                    <p class="lead mb-0">Dribble, shoot, and score on our indoor basketball court designed for both casual games and competitive play.</p>
                </div>
            </div>
    </section>

    <!-- Footer-->
    <footer class="footer bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 text-center text-lg-start my-auto">
  
                    <p class="text-muted small mb-4 mb-lg-0">&copy; PROVING GROUNDS SPORTS CENTER.</p>
                </div>
                <div class="col-lg-6 text-center text-lg-end my-auto">
                    <ul class="list-inline mb-0">
                        <li class="list-inline-item me-4">
                            <a href="#!"><i class="bi-facebook fs-3"></i></a>
                        </li>
                  
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap core JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Core theme JS-->
    <script src="{{ asset('landing/js/scripts.js') }}"></script>
</body>
</html>
