@extends('layouts.admin.app')

@section('title', 'Login')

@section('content')
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100%; margin-top: 40px;">
        <div class="row w-100" style="border-radius: 50px; overflow: hidden;">
            <!-- Left side: Custom Section -->
            <div class="col-12 col-md-6 p-5 bg-danger text-white d-none d-md-block">
                <h4 class="text-center" style="font-size: 2rem;">Welcome, Candratama Granites!</h4>
                <p class="text-center">Login to start using our services</p>
                <div class="text-center mt-4">
                    <img src="images/logo .png" alt="Logo" class="img-fluid"
                        style="max-width: 300px; margin-bottom: 20px;">
                </div>
            </div>

            <!-- Right side: Login form -->
            <div class="col-12 col-md-6 p-5" style="background-color: #ffffff;">
                <h3 class="text-center mb-5 text-danger" style="font-size: 3rem;">Login</h3>

                @if (session('error'))
                    <div class="alert alert-danger mb-3">{{ session('error') }}</div>
                @endif

                <form action="{{ route('login.submit') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="username" class="form-label" style="font-size: 1.2rem;">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required autofocus
                            style="font-size: 1.1rem;">
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label" style="font-size: 1.2rem;">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required
                            style="font-size: 1.1rem;">
                    </div>

                    <button type="submit" class="btn btn-danger w-100 mt-4" style="font-size: 1.2rem;">Login</button>
                </form>
            </div>
        </div>
    </div>

    <style>
        body {
            font-family: 'Arial', sans-serif;
        }

        .form-control {
            border-radius: 8px;
        }

        .btn-danger {
            background-color: #ca3329;
            border: none;
        }

        .btn-danger:hover {
            background-color: #9e2b24;
        }

        .bg-danger {
            background-color: #a62624 !important;
        }

        /* Ensure the sections adjust on smaller screens */
        @media (max-width: 768px) {
            .row {
                flex-direction: column;
            }
        }
    </style>
@endsection
