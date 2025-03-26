@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-root justify-content-center login-inner"  id="kt_app_root">
        <!--begin::Authentication - Sign-in -->
        <div class="d-flex flex-column flex-lg-row flex-column-fluid">
            <!--begin::Body-->
            <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10 order-2 order-lg-1">
                <!--begin::Form-->
                <div class="d-flex flex-center flex-column flex-lg-row-fluid">
                    <!--begin::Wrapper-->
                    <div class="w-lg-500px p-10">
                        <!--begin::Form-->
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <!--begin::Heading-->
                            <div class="text-center mb-11">
                                <!--begin::Title-->
                                <h1 class="text-dark fw-bolder mb-3">Sign In</h1>
                                <!--end::Title-->
                            </div>
                            <!--begin::Heading-->
                            <div class="fv-row mb-8">
                                <!--begin::Email-->
                                <input id="email" placeholder="Email" type="email" class="form-control bg-transparent @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                <!--end::Email-->
                            </div>
                            
                            <div class="fv-row mb-3">
                                <!--begin::Password-->
                                <input id="password" placeholder="Password" type="password" class="form-control bg-transparent @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                <!--end::Password-->
                            </div>

                            <div class="fv-row mb-3">
                                
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                <label class="form-check-label" for="remember">
                                    {{ __('Remember Me') }}
                                </label>
                                   
                            </div>

                            <!-- <div class="d-flex flex-stack flex-wrap gap-3 fs-base fw-semibold mb-8">
                                <div></div>
                                @if (Route::has('password.request'))
                                        <a class="btn btn-link" href="{{ route('password.request') }}">
                                            {{ __('Forgot Your Password?') }}
                                        </a>
                                    @endif
                            </div> -->

                            <div class="d-grid mb-10">
                                <button type="submit" id="kt_sign_in_submit" class="btn btn-primary">
                                    <!--begin::Indicator label-->
                                    <span class="indicator-label">Sign In</span>
                                    <!--end::Indicator label-->
                                    <!--begin::Indicator progress-->
                                    <span class="indicator-progress">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                    <!--end::Indicator progress-->
                                </button>
                            </div>
                            <!--end::Submit button-->
                            <!--begin::Sign up-->
                            <!-- <div class="text-gray-500 text-center fw-semibold fs-6">Not a Member yet?
                            @if (Route::has('register'))
                                    <a class="link-primary" href="{{ route('register') }}">{{ __('Register') }}</a>
                            @endif
                            </div> -->
                            <!--end::Sign up-->
                            <!--end::Wrapper-->
                            <!--begin::Submit button-->
                            
                        </form>
                        <!--end::Form-->
                    </div>
                    <!--end::Wrapper-->
                </div>
                <!--end::Form-->
            </div>
            <!--end::Body-->
            <!--begin::Aside-->
            <div class="d-flex flex-lg-row-fluid w-lg-50 bgi-size-cover bgi-position-center order-1 order-lg-2 login-right-block">
                <!--begin::Content-->
                <div class="d-flex flex-column flex-center w-100">
                    <!--begin::Logo-->
                    
                    <!--end::Logo-->
                    <!--begin::Image-->
                    <img class="d-none d-lg-block logo-cover" src="{{asset('assets/images/logo-cover.svg')}}" alt="" />
                    <!--end::Image-->
                    
                    <!--begin::Text-->

                    <!--end::Text-->
                </div>
                <!--end::Content-->
            </div>
            <!--end::Aside-->
        </div>
        <!--end::Authentication - Sign-in-->
    </div>
</div>

@endsection
