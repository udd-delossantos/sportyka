<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SB Admin 2 - Reset Password</title>

    <!-- Fonts & Styles -->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,900" rel="stylesheet">
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
</head>

<body class="bg-gradient-primary">

<div class="container">
    <!-- Outer Row -->
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-12 col-md-9">
            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <!-- Nested Row -->
                    <div class="row">
                        <div class="col-lg-6 d-none d-lg-block bg-password-image">
                            <img src="{{ asset('img/sk-logo.png') }}" alt="Login Image" class="img-fluid" style="max-width: 100%; height: auto;">

                        </div>
                        <div class="col-lg-6">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="h4 text-gray-900 mb-2">Reset Your Password</h1>
                                    <p class="mb-4">Enter your new password below.</p>
                                </div>

                                <form method="POST" action="{{ route('password.store') }}" class="user">
                                    @csrf

                                    <!-- Token -->
                                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                                    <!-- Email -->
                                    <div class="form-group">
                                        <input type="email" name="email" value="{{ old('email', $request->email) }}"
                                               class="form-control form-control-user"
                                               placeholder="Email" required autofocus>
                                    </div>

                                    <!-- Password -->
                                    <div class="form-group">
                                        <input type="password" name="password"
                                               class="form-control form-control-user"
                                               placeholder="New Password" required>
                                    </div>

                                    <!-- Confirm Password -->
                                    <div class="form-group">
                                        <input type="password" name="password_confirmation"
                                               class="form-control form-control-user"
                                               placeholder="Confirm New Password" required>
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-user btn-block">
                                        Reset Password
                                    </button>
                                </form>

                            </div>
                        </div>
                    </div><!-- End Row -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
<script src="{{ asset('js/sb-admin-2.min.js') }}"></script>

</body>
</html>
