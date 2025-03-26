@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-root  login-inner" id="kt_app_root">
        <!--begin::Authentication - Sign-in -->
        <div class="d-flex flex-column flex-lg-row flex-column-fluid">
            <!--begin::Body-->
            <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10 order-2 order-lg-1">
                <!--begin::Form-->
                <div class="d-flex flex-center flex-column flex-lg-row-fluid">
                    <!--begin::Wrapper-->
                    <div class="w-lg-500px p-10">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="text-center mb-11">
                            <!--begin::Title-->
                            <h1 class="text-dark fw-bolder mb-3">Register</h1>
                            <!--end::Title-->
                        </div>

                        <div class="fv-row mb-8">
                            <!--begin::Name-->
                            <input id="name" type="text" placeholder="Name" class="form-control bg-transparent @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <!--end::name-->
                        </div>


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
                            <input id="password" placeholder="Password" type="password" class="form-control bg-transparent @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            <!--end::Password-->
                        </div>

                        <div class="fv-row mb-3">
                            <!--begin::Password-->
                            <input id="confirm" placeholder="Confirm Password" type="password" class="form-control bg-transparent @error('password') is-invalid @enderror" name="password_confirmation" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            <!--end::Password-->
                        </div>

                        <div class="d-grid mb-10">
                                <button type="submit" id="kt_sign_in_submit" class="btn btn-primary">
                                    <!--begin::Indicator label-->
                                    <span class="indicator-label">Register</span>
                                    <!--end::Indicator label-->
                                    <!--begin::Indicator progress-->
                                    <span class="indicator-progress">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                    <!--end::Indicator progress-->
                                </button>
                            </div>
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
