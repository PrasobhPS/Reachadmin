@extends('layouts.app') @section('content')

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->

    <div class="d-flex flex-column flex-column-fluid">

        <div class="breadcrumb-wrapper">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Members</a></li>
                    <li class="breadcrumb-item active" aria-current="page">View Member</li>
                </ol>
            </nav>
        </div>

        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <!--begin::Toolbar container-->
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">


                <div class="outer-data  w-100">

                        <h1 class="fs-2x text-dark mb-3 common-head">View Member Details</h1>

                        <div class="add-details pt-3 common-form-container">

                            <div class="row">
                            <div class="col-md-6 mb-3">
                                    <div class="card w-100 p-5">
                                        <h2>Profile Photo</h2>
                                        <div class="profile-upload mb-3">
                                            <div class="d-flex align-items-center">
                                                @if(isset($member->members_profile_picture))
                                                <div class="mx-1">
                                                    <img src="{{ asset('storage/' . $member->members_profile_picture) }}" alt="Profile Picture" class="thumbnail photo">
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
                                                <label class="col-lg-12 col-form-label fw-semibold fs-6">Biography</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    {!! $member->members_biography !!}
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <label class="col-lg-12 col-form-label fw-semibold fs-6">About Me</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    {!! $member->members_about_me !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-10">
                                            <label class="col-lg-12 col-form-label fw-semibold fs-6">Member Interests</label>
                                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                {{ $member->members_interest }}
                                            </div>
                                        </div>

                                        <h2>Employment Details</h2>
                                        <div class="row mt-3">
                                            <label class="col-lg-12 col-form-label fw-semibold fs-6">Current Employment</label>
                                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                {{ $member->members_employment }}
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label class="col-lg-12 col-form-label fw-semibold fs-6">Employment History</label>
                                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                {!! $member->members_employment_history !!}
                                                <div class="fv-plugins-message-container invalid-feedback"></div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card w-100 p-5">
                                        <h2>Personal Details</h2>
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <label class="col-lg-12 col-form-label fw-semibold fs-6">First Name</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    {{$member->members_fname}}
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="col-lg-12 col-form-label fw-semibold fs-6">Last Name</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    {{$member->members_lname}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="col-lg-12 col-form-label fw-semibold fs-6">Email Address</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    {{ $member->members_email }}
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="col-lg-12 col-form-label fw-semibold fs-6">Date of Birth</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    {{ date('d-m-Y', strtotime($member->members_dob)) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="col-lg-12 col-form-label fw-semibold fs-6">Address</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    {{ $member->members_address }}
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="col-lg-12 col-form-label fw-semibold fs-6">Country</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    {{ $member->members_country }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="col-lg-12 col-form-label fw-semibold fs-6">Town</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    {{ $member->members_town }}
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="col-lg-12 col-form-label fw-semibold fs-6">Street</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    {{ $member->members_street }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="col-lg-12 col-form-label fw-semibold fs-6">Region</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    {{ $member->members_region }}
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="col-lg-12 col-form-label fw-semibold fs-6">Postcode</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    {{ $member->members_postcode }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="col-lg-12 col-form-label fw-semibold fs-6">Phone Number</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    {{ $member->members_phone }}
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="col-lg-12 col-form-label fw-semibold fs-6">Member Type</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    @if($member->members_type=='F') 
                                                        Free Member
                                                    @elseif($member->members_type=='T') 
                                                        Trial Member
                                                    @elseif($member->members_type=='M')
                                                        Full Member
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-end px-0 py-3">
                            <a href="{{ route('home') }}" style="margin-right: 10px;" class="btn btn-primary">
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