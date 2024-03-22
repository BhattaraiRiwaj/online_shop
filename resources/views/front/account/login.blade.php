@extends('front.layouts.app')

@section('front-content')
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="{{ route('front.home') }}">Home</a></li>
                    <li class="breadcrumb-item">Login</li>
                </ol>
            </div>
        </div>
    </section>

    <section class=" section-10">
        <div class="container">

            <div class="login-form">
                @if (session()->has('success'))
                <div class="form-group col-md-12" id="message">
                    <div class="alert alert-success d-flex align-items-center" role="alert">
                        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img"
                            aria-label="Success:">
                            <use xlink:href="#check-circle-fill" />
                        </svg>
                        <div>
                            {!! session()->get('success') !!}
                        </div>
                    </div>
                </div>
            @endif
            @if (session()->has('error'))
                <div class="col-md-12" id="message">
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img"
                            aria-label="Danger:">
                            <use xlink:href="#check-circle-fill" />
                        </svg>
                        <div>
                            {{ session()->get('error') }}
                        </div>
                    </div>
                </div>
            @endif
                <form action="{{ route('account.authenticate') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <h4 class="modal-title">Login to Your Account</h4>
                    <div class="form-group">
                        <input type="email" class="form-control  @error('email') is-invalid @enderror "
                            placeholder="Email" name="email" value="{{ old('email') }}">
                        @error('email')
                            <p class="invalid-feedback">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control @error('password') is-invalid @enderror "
                            placeholder="Password" name="password">
                        @error('password')
                            <p class="invalid-feedback">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group small">
                        <a href="#" class="forgot-link">Forgot Password?</a>
                    </div>
                    <input type="submit" class="btn btn-dark btn-block btn-lg" value="Login">
                </form>
                <div class="text-center small">Don't have an account? <a href="{{ route('account.register') }}">Sign up</a>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('front')
@endsection
