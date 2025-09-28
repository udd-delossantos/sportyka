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
        <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />

        <!-- SB Admin 2 Fonts and Styles -->
        <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet" />

        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="{{ asset('landing/css/styles.css') }}" rel="stylesheet" />
        <!-- In <head> -->
<link href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" rel="stylesheet">

        <style>
            /* Remove default overlay in masthead */
            header.masthead::before {
                content: none !important;
                background: none !important;
            }

            body {
                font-family: "Poppins", sans-serif;
            }
            .branding {
                font-family: "Orbitron", sans-serif;
            }
            /* GLightbox button styling */
.glightbox-clean .gclose {
  top: 20px !important;
  right: 30px !important;
  font-size: 28px !important;
  color: #fff !important;
}

.glightbox-clean .gnext, 
.glightbox-clean .gprev {
  font-size: 34px !important;
  color: #fff !important;
  background: rgba(0, 0, 0, 0.5);
  border-radius: 50%;
  width: 50px;
  height: 50px;
  line-height: 50px;
  text-align: center;
}

/* Position */
.glightbox-clean .gnext { right: 30px !important; }
.glightbox-clean .gprev { left: 30px !important; }

/* Hover effect */
.glightbox-clean .gnext:hover,
.glightbox-clean .gprev:hover,
.glightbox-clean .gclose:hover {
  background: rgba(255, 255, 255, 0.2);
  color: #ffd700 !important;
}

.gallery-img {
  width: 100%;
  aspect-ratio: 1 / 1; /* perfect square */
  object-fit: cover;
}

            
        </style>
    </head>
    <body>
        <!-- Navigation-->
        <!-- Navigation-->
        <nav class="navbar navbar-expand-lg navbar-dark sticky-top shadow bg-dark" >
            <!-- Google Fonts Nunito -->

            <div class="container">
                <!-- Branding -->
                <a class="navbar-brand d-flex align-items-center" href="{{ url('img/') }}" >
                    <img src="{{ asset('img/sk-logo-2.png') }}" alt="Sporty Ka Logo" width="35" height="35" class="me-2 rounded-circle border border-light" />
                    <span class="fw-bold">Sporty Ka? Book Na!</span>
                </a>

                <!-- Mobile Toggler -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Right Side Buttons -->
                <div class="collapse navbar-collapse" id="navbarResponsive" style="font-family: 'Nunito', sans-serif;">
                    <ul class="navbar-nav ms-auto">
                        @auth @php $role = Auth::user()->role; $dashboardRoute = match ($role) { 'admin' => route('admin.dashboard'), 'staff' => route('staff.dashboard'), 'customer' => route('customer.dashboard'), default => url('/'), };
                        @endphp

                        <li class="nav-item">
                            <a href="{{ $dashboardRoute }}" class="btn btn-sm ms-2 px-4 shadow-sm bg-primary" >
                                Dashboard
                            </a>
                        </li>
                        @else
                        <li class="nav-item">
                            <a href="{{ route('login') }}" class="btn btn-sm ms-2 px-4 shadow-sm bg-primary text-white" >
                                Log in
                            </a>
                        </li>
                        @if (Route::has('register'))
                        <li class="nav-item">
                            <a href="{{ route('register') }}" class="btn btn-sm ms-2 px-4 shadow-sm btn-outline-light" >
                                Register
                            </a>
                        </li>
                        @endif @endauth
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="hero-section py-5" style="background-color: #242424;">
            <div class="container"  data-aos="fade-up" data-aos-delay="100">
                <div class="row gy-4 align-items-center">
                    <!-- Left Content -->
                    <div class="col-lg-6 text-white" data-aos="fade-right" data-aos-delay="200">
                        <div class="hero-content">
                            <h1 class="mb-3 fw-bold">Your Court.</h1>
                            <h1 class="mb-3 fw-bold">Your Game.</h1>
                            <h1 class="mb-3 fw-bold">Your Proving Grounds.</h1>
                            <p class="lead mb-4">
                                Whether you’re stepping onto the court for fun, fitness, or fierce competition, Proving Grounds Sports Center provides the perfect arena to fuel your passion, elevate your performance, and create unforgettable moments.
                            </p>

                            <!-- Features -->
                            <div class="hero-features mb-4">
                                <div class="feature-item d-flex align-items-center mb-2">
                                    <i class="bi bi-check-circle me-2 text-warning"></i>
                                    <span>Basketball</span>
                                </div>
                                <div class="feature-item d-flex align-items-center mb-2">
                                    <i class="bi bi-check-circle me-2 text-warning"></i>
                                    <span>Badminton</span>
                                </div>
                                <div class="feature-item d-flex align-items-center mb-2">
                                    <i class="bi bi-check-circle me-2 text-warning"></i>
                                    <span>Pickleball</span>
                                </div>
                                <div class="feature-item d-flex align-items-center">
                                    <i class="bi bi-check-circle me-2 text-warning"></i>
                                    <span>Tennis</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Image / Showcase -->
                    <div class="col-lg-6" data-aos="fade-left" data-aos-delay="300">
                        <div class="hero-images position-relative">
                            <div class="main-image mb-3">
                                <img src="{{ asset('landing/assets/img/pg.jpg') }}" alt="Proving Grounds Sports Center" class="img-fluid rounded shadow-lg border border-4 border-primary" />
                            </div>

                            <!-- Floating Card 
                             <div class="floating-card position-absolute bg-light shadow p-3 rounded text-white" style="bottom: -20px; right: 20px; max-width: 250px;">
                                <div class="card-content">
                                    <div class="rating text-warning mb-2">
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark">Exceptional Experience</h6>
                                    <p class="small mb-2 text-muted">"Spacious courts, bright lighting, and a friendly environment. Perfect for both casual games and competitive play."</p>
                                    <div class="guest-info d-flex align-items-center">
                                        <img src="assets/img/person/player.jpg" alt="Local Player" class="rounded-circle me-2" style="width: 30px; height: 30px;" />
                                        <span class="small text-dark">Local Player</span>
                                    </div>
                                </div>
                            </div>-->
                            
                        </div>
                    </div>
                </div>

                <!-- Stats Section -->
            </div>
        </section>

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
                        <h2 class="text-primary">Well-Maintained & Well-Lit Courts</h2>
                        <p class="lead mb-0">Play with confidence on courts that are clean, safe, and brightly lit for both day and night games.</p>
                    </div>
                </div>
                <div class="row g-0">
                    <div class="col-lg-6 showcase-img" style="background-image: url('{{ asset('landing/assets/img/gal-11.jpg') }}')"></div>
                    <div class="col-lg-6 my-auto showcase-text">
                        <h2 class="text-primary">Wide Parking Space</h2>
                        <p class="lead mb-0">Hassle-free parking with ample space for players and guests.</p>
                    </div>
                </div>
            </div>
            <div class="row g-0">
                <div class="col-lg-6 order-lg-2 showcase-img" style="background-image: url('{{ asset('landing/assets/img/store.jpg') }}')"></div>
                <div class="col-lg-6 order-lg-1 my-auto showcase-text">
                    <h2 class="text-primary">Mini-Store</h2>
                    <p class="lead mb-0">Grab your game-day essentials, snacks, and drinks—all within reach.</p>
                </div>
            </div>
        </section>
<section id="gallery" class="gallery section py-5 bg-light">

  <!-- Section Title -->
  <div class="container section-title text-center mb-4" data-aos="fade-up">
    <h2 class="text-primary">Gallery</h2>
    <p class="text-muted">Explore our facilities and captured moments.</p>
  </div><!-- End Section Title -->

  <div class="container-fluid px-2" data-aos="fade-up" data-aos-delay="100">
    <div class="row g-2">

      <!-- Gallery Item -->
      <div class="col-lg-3 col-md-4 col-6">
        <div class="gallery-item">
          <a href="{{ asset('landing/assets/img/gal-1.jpg') }}" 
             class="glightbox" data-gallery="images-gallery">
            <img src="{{ asset('landing/assets/img/gal-1.jpg') }}" 
                 alt="Gallery Image 1" class="gallery-img">
          </a>
        </div>
      </div><!-- End Gallery Item -->

      <!-- Gallery Item -->
      <div class="col-lg-3 col-md-4 col-6">
        <div class="gallery-item">
          <a href="{{ asset('landing/assets/img/gal-6.jpg') }}" 
             class="glightbox" data-gallery="images-gallery">
            <img src="{{ asset('landing/assets/img/gal-6.jpg') }}" 
                 alt="Gallery Image 1" class="gallery-img">
          </a>
        </div>
      </div><!-- End Gallery Item -->

      <!-- Gallery Item -->
      <div class="col-lg-3 col-md-4 col-6">
        <div class="gallery-item">
          <a href="{{ asset('landing/assets/img/gal-3.jpg') }}" 
             class="glightbox" data-gallery="images-gallery">
            <img src="{{ asset('landing/assets/img/gal-3.jpg') }}" 
                 alt="Gallery Image 1" class="gallery-img">
          </a>
        </div>
      </div><!-- End Gallery Item -->

      <!-- Gallery Item -->
      <div class="col-lg-3 col-md-4 col-6">
        <div class="gallery-item">
          <a href="{{ asset('landing/assets/img/gal-4.jpg') }}" 
             class="glightbox" data-gallery="images-gallery">
            <img src="{{ asset('landing/assets/img/gal-4.jpg') }}" 
                 alt="Gallery Image 1" class="gallery-img">
          </a>
        </div>
      </div><!-- End Gallery Item -->
      <!-- Gallery Item -->
      <div class="col-lg-3 col-md-4 col-6">
        <div class="gallery-item">
          <a href="{{ asset('landing/assets/img/gal-8.jpg') }}" 
             class="glightbox" data-gallery="images-gallery">
            <img src="{{ asset('landing/assets/img/gal-8.jpg') }}" 
                 alt="Gallery Image 1" class="gallery-img">
          </a>
        </div>
      </div><!-- End Gallery Item -->
      <!-- Gallery Item -->
      <div class="col-lg-3 col-md-4 col-6">
        <div class="gallery-item">
          <a href="{{ asset('landing/assets/img/gal-9.jpg') }}" 
             class="glightbox" data-gallery="images-gallery">
            <img src="{{ asset('landing/assets/img/gal-9.jpg') }}" 
                 alt="Gallery Image 1" class="gallery-img">
          </a>
        </div>
      </div><!-- End Gallery Item -->
      <!-- Gallery Item -->
      <div class="col-lg-3 col-md-4 col-6">
        <div class="gallery-item">
          <a href="{{ asset('landing/assets/img/gal-7.jpg') }}" 
             class="glightbox" data-gallery="images-gallery">
            <img src="{{ asset('landing/assets/img/gal-7.jpg') }}" 
                 alt="Gallery Image 1" class="gallery-img">
          </a>
        </div>
      </div><!-- End Gallery Item -->
      <!-- Gallery Item -->
      <div class="col-lg-3 col-md-4 col-6">
        <div class="gallery-item">
          <a href="{{ asset('landing/assets/img/gal-10.jpg') }}" 
             class="glightbox" data-gallery="images-gallery">
            <img src="{{ asset('landing/assets/img/gal-10.jpg') }}" 
                 alt="Gallery Image 1" class="gallery-img">
          </a>
        </div>
      </div><!-- End Gallery Item -->

    </div>
  </div>

</section><!-- End Gallery -->



        <!-- Full-Width Google Maps Section -->
        <section class="map-section my-0 pt-0 mb-0 bg-light">
            <h2 class="text-center text-primary">Visit Us</h2>
            <p class="text-center mb-4">Find us easily at Proving Grounds Sports Center — your hub for sports and recreation in San Jacinto, Pangasinan.</p>
            <div style="width: 100%; height: 400px;">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3833.791077366151!2d120.4335241757844!3d16.076327739246516!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33916b0065847727%3A0x8477f8e3a6744582!2sProving%20Grounds%20Sports%20Center!5e0!3m2!1sen!2sph!4v1758782764822!5m2!1sen!2sph"
                    width="100%"
                    height="100%"
                    style="border: 0;"
                    allowfullscreen=""
                    loading="lazy"
                >
                </iframe>
            </div>
        </section>

        <style>
            .map-section {
                background: linear-gradient(135deg,);
                padding: 40px 0;
            }
        </style>

        

    
        <!-- Footer -->
        <footer class="text-white py-5 bg-dark">
            <div class="container">
                <div class="row">
                    <!-- About -->
                    <div class="col-md-4 mb-4">
                        <h6 class="text-uppercase fw-bold mb-3">About Us</h6>
                        <p class="small mb-0">
                            Proving Grounds Sports Center is San Jacinto’s hub for quality courts and recreation. We bring athletes, families, and friends together through sports, fitness, and fun—made easier with our Sporty Ka? booking
                            system.
                        </p>
                    </div>

                    <!-- Follow Us -->
                    <div class="col-md-4 mb-4 text-center">
                        <h6 class="text-uppercase fw-bold mb-3">Follow Us</h6>
                        <a href="https://facebook.com/YourPageHere" target="_blank" class="d-inline-block">
                            <i class="bi bi-facebook text-white" style="font-size: 3rem; "></i>
                        </a>
                    </div>

                    <!-- Contact -->
                    <div class="col-md-4 mb-4 text-md-end">
                        <h6 class="text-uppercase fw-bold mb-3">Contact</h6>
                        <p class="small mb-1">Barangay San Vicente, San Jacinto, Pangasinan</p>
                        <p class="small mb-1">Email: sportyka@example.com</p>
                        <p class="small mb-0">Phone: +63 912 345 6789</p>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <span class="small">&copy; {{ date('Y') }} Sporty Ka? | All Rights Reserved</span>
                </div>
            </div>
        </footer>

        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="{{ asset('landing/js/scripts.js') }}"></script>
        <!-- Before </body> -->
<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
<script>
    const lightbox = GLightbox({
        selector: '.glightbox'
    });
</script>

        
    </body>
</html>
