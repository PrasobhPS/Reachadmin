@extends('layouts.app') @section('content')

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->

    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <!--begin::Toolbar container-->
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">


                <div class="outer-data  w-100">

                        <h1 class="fs-2x text-dark mb-3 common-head">View Employer</h1>

                        <div class="add-details pt-3 common-form-container">

                            <div class="row">
                            <div class="col-md-6 mb-3">
                                    <div class="card w-100 p-5">
                                        <h2>Profile Photo</h2>
                                        <div class="profile-upload mb-3">
                                            <div class="d-flex align-items-center">
                                                @if(isset($employer->employer_profile_picture))
                                                <div class="mx-1">
                                                    <img src="{{ asset('storage/' . $employer->employer_profile_picture) }}" alt="Profile Picture" class="thumbnail photo">
                                                </div>
                                                @else
                                                <div class="mx-1">
                                                    <img src="{{ asset('assets/images/noimage.jpg') }}" alt="Profile Picture" class="thumbnail photo">
                                                </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="col-lg-12 col-form-label fw-semibold fs-6">Company Name</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    {{ $employer->employer_company_name }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="col-lg-12 col-form-label fw-semibold fs-6">Email Id</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    {{ $employer->employer_email }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="col-lg-12 col-form-label fw-semibold fs-6">Phone Number</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    {{ $employer->employer_phone }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="col-lg-12 col-form-label fw-semibold fs-6">Vessel Name</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    {{ $employer->employer_vessel_name }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="col-lg-12 col-form-label fw-semibold fs-6">Country of Origin</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    {{ $employer->employer_country }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card w-100 p-5">
                                        <h2>Personal Details</h2>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="col-lg-12 col-form-label required fw-semibold fs-6">First Name</label>
                                                <div class="col-lg-6 fv-row fv-plugins-icon-container">
                                                        {{ $employer->member->members_fname }}
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="col-lg-12 col-form-label required fw-semibold fs-6">Last Name</label>
                                                <div class="col-lg-6 fv-row fv-plugins-icon-container">
                                                        {{ $employer->member->members_lname }}
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                            </div>
                                        </div>
    
                                        <div class="row">
                                            <div class="col-md-6">
                                                <!--begin::Label-->
                                                <label class="col-lg-12 col-form-label required fw-semibold fs-6">Email Address</label>
                                                <!--end::Label-->
                                                <!--begin::Col-->
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    {{ $employer->member->members_email }}
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
                                                    {{ $employer->member->members_address }}
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <!--begin::Label-->
                                                <label class="col-lg-12 col-form-label required fw-semibold fs-6">Country</label>
                                                <!--end::Label-->
                                                <!--begin::Col-->
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    {{ $employer->member->members_country }}
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <!--begin::Label-->
                                                <label class="col-lg-12 col-form-label required fw-semibold fs-6">Region</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    {{ $employer->member->members_region }}
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-end px-0 py-3">
                            <a href="{{ route('employer') }}" style="margin-right: 10px;" class="btn btn-primary">
                            	Cancel
                        	</a>
                            
                        </div>

                </div>
            </div>
            <!--end::Toolbar container-->
        </div>
    </div>
    <!--end::Content wrapper-->
    @include('layouts.dashboard_footer')
</div>

@endsection