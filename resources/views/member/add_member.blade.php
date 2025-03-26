@extends('layouts.app') @section('content')

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->

    <div class="d-flex flex-column flex-column-fluid">

        <div class="breadcrumb-wrapper">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Members</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add Member</li>
                </ol>
            </nav>
        </div>

        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <!--begin::Toolbar container-->
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">

                <div class="outer-data  w-100">

                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form method="POST" id="memberForm" action="{{ route('save-member') }} " enctype="multipart/form-data">
                        @csrf

                        <h1 class="fs-2x text-dark mb-3 common-head">Add Member</h1>

                        <div class="add-details pt-3 common-form-container">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card w-100 p-5">
                                        <h2>Profile Photo</h2>
                                        <div class="profile-upload mb-5">
                                            <div class="d-flex align-items-center">

                                                <div class="mx-1">
                                                    <img src="{{ asset('assets/images/noimage.jpg') }}" alt="Profile Picture" class="thumbnail photo" id="preview">
                                                </div>
                                                <div class="buton-area mx-1">
                                                    <input type="file" name="members_profile_picture" class="w-100 mb-1" placeholder="" value="" onchange="previewImage(event,'preview')">

                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label class="fw-semibold fs-6">Status</label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="status-container">
                                                            <label class="switch">
                                                                <input type="checkbox" name="members_status">
                                                                <span class="slider"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- <div class="row">
                                                    <div class="col-md-6">
                                                        <label class="fw-semibold fs-6">Payment status</label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="status-container">
                                                            <label class="switch">
                                                                <input type="checkbox" name="members_payment_status" >
                                                                <span class="slider"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div> -->
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <!--begin::Label-->
                                                <label class="col-lg-12 col-form-label  fw-semibold fs-6">About Me</label>
                                                <!--end::Label-->
                                                <!--begin::Col-->
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <textarea name="members_biography" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0 text_editor" placeholder="About Me">{{ old('members_biography') }}</textarea>
                                                    <label id="members_biography-error" class="error" for="members_biography"></label>
                                                </div>
                                            </div>
                                            <!-- <div class="col-md-12">
                                                <label class="col-lg-12 col-form-label fw-semibold fs-6">About Me</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <textarea name="members_about_me" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0 text_editor2" placeholder="About me">{{ old('members_about_me') }}</textarea>
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                            </div> -->
                                        </div>
                                        <div class="row">
                                            <!--begin::Label-->
                                            <label class="col-lg-12 col-form-label fw-semibold fs-6">Member Interests</label>
                                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                <input type="text" name="members_interest" class="js-example-tags form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Interests" value="{{ old('members_interest') }}">
                                            </div>
                                        </div>

                                        <h2 class="mt-10">Employment Details</h2>
                                        <div class="row">
                                            <label class="col-lg-12 col-form-label fw-semibold fs-6">Current Employment</label>
                                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                <input type="text" name="members_employment" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Current Employment" value="{{ old('members_employment') }}">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label class="col-lg-12 col-form-label fw-semibold fs-6">Employment History</label>
                                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                <textarea name="members_employment_history" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0 text_editor3" placeholder="Employment History">{{ old('members_employment_history') }}</textarea>
                                                <div class="fv-plugins-message-container invalid-feedback"></div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card w-100 p-5">
                                        <h2>Personal Details</h2>
                                        <div class="row mt-3 common-head">
                                            <div class="col-md-6">
                                                <label class="fw-semibold fs-6">Add Member As Expert</label>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="status-container">
                                                    <label class="switch">
                                                        <input type="checkbox" name="is_specialist" value="Y">
                                                        <span class="slider"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-5">
                                            <div class="col-md-6">
                                                <label class="col-lg-12 col-form-label required fw-semibold fs-6">Title</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="text" name="members_name_title" id="members_name_title" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Enter Title" value="{{ old('members_name_title') }}" required>
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="col-lg-12 col-form-label required fw-semibold fs-6">First Name</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="text" name="members_fname" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="First name" value="{{ old('members_fname') }}">
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="col-lg-12 col-form-label required fw-semibold fs-6">Last Name</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="text" name="members_lname" class="form-control form-control-lg form-control-solid" placeholder="Last name" value="{{ old('members_lname') }}">
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="col-lg-12 col-form-label required fw-semibold fs-6">Email Address</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="text" name="members_email" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Email" value="{{ old('members_email') }}">
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                            </div>
                                            <!-- <div class="col-md-6">
                                                <label class="col-lg-12 col-form-label required fw-semibold fs-6">Date of Birth</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="text" name="members_dob" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="DOB" id="dob-datepicker" value="{{ old('members_dob') }}" readonly>
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                            </div> -->
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="col-lg-12 col-form-label required fw-semibold fs-6">Password</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="password" name="members_password" id="members_password" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Password" value="{{ old('members_password') }}" autocomplete="off">
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="col-lg-12 col-form-label required fw-semibold fs-6">Confirm Password</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="password" id="password-confirm" name="members_password_confirmation" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Confirm Password" value="{{ old('members_password_confirmation') }}">
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <!--begin::Label-->
                                                <label class="col-lg-12 col-form-label required fw-semibold fs-6">Address</label>
                                                <!--end::Label-->
                                                <!--begin::Col-->
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="text" name="members_address" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Address" value="{{ old('members_address') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <!--begin::Label-->
                                                <label class="col-lg-12 col-form-label required fw-semibold fs-6">Country</label>
                                                <!--end::Label-->
                                                <!--begin::Col-->
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <select name="members_country" aria-label="Select a Country" data-control="select2" data-placeholder="Select a country..." class="form-select form-select-solid form-select-lg fw-semibold">
                                                        <option value="" data-select2-id="select2-data-12-8p9k">Select a Country...</option>
                                                        @foreach($countries as $country)
                                                        <option value="{{ $country->country_name }}" @if($country->country_name==old('members_country')) selected="selected" @endif >{{ $country->country_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="col-lg-12 col-form-label fw-semibold fs-6">Town</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="text" name="members_town" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Town" value="{{ old('members_town') }}">
                                                </div>
                                            </div>
                                            <!-- <div class="col-md-6">
                                                <label class="col-lg-12 col-form-label fw-semibold fs-6">Street</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="text" name="members_street" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Street" value="{{ old('members_street') }}">
                                                </div>
                                            </div> -->
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="col-lg-12 col-form-label fw-semibold fs-6">Region</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="text" name="members_region" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Region" value="{{ old('members_region') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="col-lg-12 col-form-label required fw-semibold fs-6">Postcode</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="text" name="members_postcode" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Postcode" value="{{ old('members_postcode') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="col-lg-12 col-form-label required fw-semibold fs-6">Phone Number</label>
                                                <div class="row">
                                                    <div class="col-lg-4 fv-row fv-plugins-icon-container">
                                                        <select name="members_phone_code" aria-label="Select Phone Code" data-control="select2" data-placeholder="Select Phone Code..." class="form-select form-select-solid form-select-lg fw-semibold px-4">
                                                            <option value="">Select Phone Code</option>
                                                            @foreach($countries as $country)
                                                            <option value="{{ $country->id }}" @if($country->id==old('members_phone_code') || $country->id=='225') selected="selected" @endif>+{{ $country->country_phonecode }} - {{ $country->country_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-8 fv-row fv-plugins-icon-container px-1">
                                                        <input type="text" name="members_phone" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Phone number" value="{{ old('members_phone') }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="col-lg-12 col-form-label required fw-semibold fs-6">Member Type</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <select id="members_type" name="members_type" aria-label="Select a Type" data-control="select2" data-placeholder="Select a Type..." class="form-select form-select-solid form-select-lg fw-semibold">
                                                        <option value="F">Free Member</option>

                                                        <option value="M">Full Member</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="expiry_date" class="row" style="display:none;">
                                            <div class="col-md-6">
                                                <label class="col-lg-12 col-form-label fw-semibold fs-6">Expiry Date</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="text" name="members_subscription_end_date" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Expiry Date" id="members_subscription_end_date" value="{{ date('d-m-Y', strtotime('+1 month')) }}" readonly>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">

                                </div>

                            </div>


                        </div>
                        <div class="card-footer d-flex justify-content-end px-0 py-3">
                            <a href="{{ route('home') }}" style="margin-right: 10px;" class="btn btn-primary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">Add</button>
                        </div>

                    </form>

                </div>
            </div>
            <!--end::Toolbar container-->
        </div>
    </div>
    <!--end::Content wrapper-->

    @include('common.datepicker', ['id' => 'dob-datepicker', 'class' => 'members_subscription_end_date'])
    @include('common.text_editor')
    @include('layouts.dashboard_footer')
    @include('common.preview_image')
    @include('scripts.member')
    @include('common.sanitize_input')
</div>

@endsection