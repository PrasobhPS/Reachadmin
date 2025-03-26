@extends('layouts.app') @section('content')
@php
use App\Models\StripePaymentTransaction;
$stripePaymentTransaction = StripePaymentTransaction::where('member_id', $id)->get();
$isDisabled = $stripePaymentTransaction->isNotEmpty() ? 'disabled' : '';
@endphp
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->

    <div class="d-flex flex-column flex-column-fluid">
        <div class="breadcrumb-wrapper">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Members</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Member</li>
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

                    <form method="POST" id="memberForm" class="edit-page" action="{{ route('update-member', $member->id) }}" enctype="multipart/form-data">
                        @csrf

                        <h1 class="fs-2x text-dark mb-3 common-head">Edit Member</h1>

                        <div class="add-details pt-3 common-form-container">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card w-100 p-5">
                                        <h2>Profile Photo</h2>
                                        <div class="profile-upload mb-5">
                                            <div class="d-flex align-items-center">
                                                @if(isset($member->members_profile_picture))
                                                <div class="mx-1">
                                                    <img src="{{ asset('storage/' . $member->members_profile_picture) }}" alt="Profile Picture" class="thumbnail photo" id="preview">
                                                </div>
                                                <div class="buton-area mx-1">
                                                    <input type="file" name="members_profile_picture" class="w-100 mb-1" placeholder="" value="" onchange="previewImage(event,'preview')">
                                                </div>
                                                @else
                                                <div class="mx-1">
                                                    <img src="{{ asset('assets/images/noimage.jpg') }}" alt="Profile Picture" class="thumbnail photo" id="preview">
                                                </div>
                                                <div class="buton-area mx-1">

                                                    <input type="file" style="display: inline-block;" name="members_profile_picture" class="w-100 mb-1 " placeholder="" value="" onchange="previewImage(event,'preview')">
                                                </div>
                                                @endif
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
                                                            @if ($member->memberSchedules->isEmpty() && $member->memberTransaction->isEmpty())
                                                            <label class="switch">

                                                                <input type="checkbox" name="members_status" @if($member->members_status=='A') checked @endif>
                                                                <span class="slider"></span>
                                                            </label>

                                                            @else
                                                            <label class="switch" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                                title="Delete disabled because this member is linked to existing schedules or transactions.">
                                                                <input type="hidden" name="members_status" value="{{ $member->members_status }}">
                                                                <input type="checkbox" name="members_status" disabled @if($member->members_status=='A') checked @endif>
                                                                <span class="slider"></span>
                                                            </label>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <!--begin::Label-->
                                                <label class="col-lg-12 col-form-label required fw-semibold fs-6">About Me</label>
                                                <!--end::Label-->
                                                <!--begin::Col-->
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <textarea name="members_biography" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0 text_editor" placeholder="About Me">{{ $member->members_biography }}</textarea>
                                                    <label id="members_biography-error" class="error" for="members_biography"></label>
                                                </div>
                                            </div>
                                            <!-- <div class="col-md-12">
                                                <label class="col-lg-12 col-form-label fw-semibold fs-6">About Me</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <textarea name="members_about_me" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0 text_editor2" placeholder="About me">{{ $member->members_about_me }}</textarea>
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                            </div> -->
                                        </div>
                                        <div class="row">
                                            <label class="col-lg-12 col-form-label fw-semibold fs-6">Member Interests</label>
                                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                <input type="text" name="members_interest" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Interests" value="{{ $member->members_interest }}">
                                            </div>
                                        </div>

                                        <h2 class="mt-10">Employment Details</h2>
                                        <div class="row">
                                            <label class="col-lg-12 col-form-label fw-semibold fs-6">Current Employment</label>
                                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                <input type="text" name="members_employment" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Current Employment" value="{{ $member->members_employment }}">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label class="col-lg-12 col-form-label fw-semibold fs-6">Employment History</label>
                                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                <textarea name="members_employment_history" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0 text_editor3" placeholder="Employment History">{{ $member->members_employment_history }}</textarea>
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
                                                        <input type="checkbox" name="is_specialist" value="Y" @if($member->is_specialist=='Y') checked @endif>
                                                        <span class="slider"></span>
                                                    </label>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="row mt-5">
                                            <div class="col-md-6">
                                                <label class="col-lg-12 col-form-label required fw-semibold fs-6">Title</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="text" name="members_name_title" id="members_name_title" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Enter Title" value="{{$member->members_name_title}}" required>
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="col-lg-12 col-form-label required fw-semibold fs-6">First Name</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="text" name="members_fname" id="members_fname" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="First name" value="{{$member->members_fname}}">
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="col-lg-12 col-form-label required fw-semibold fs-6">Last Name</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="text" name="members_lname" id="members_lname" class="form-control form-control-lg form-control-solid" placeholder="Last name" value="{{$member->members_lname}}">
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
                                                    <input type="text" name="members_email" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Email" value="{{ $member->members_email }}">
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                            </div>
                                            <!-- <div class="col-md-6">

                                                <label class="col-lg-12 col-form-label required fw-semibold fs-6">Date of Birth</label>

                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="text" name="members_dob" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="DOB" value="{{ ($member->members_dob && $member->members_dob != '0000-00-00') ? date('d-m-Y', strtotime($member->members_dob)) : '' }}" id="dob-datepicker" readonly>
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                            </div> -->
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="col-lg-12 col-form-label required fw-semibold fs-6">Password</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="password" name="members_password" id="members_password" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Password" value="" autocomplete="off">
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="col-lg-12 col-form-label required fw-semibold fs-6">Confirm Password</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="password" id="password-confirm" name="members_password_confirmation" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Confirm Password" value="">
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="col-lg-12 col-form-label required fw-semibold fs-6">Address</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="text" name="members_address" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Address" value="{{ $member->members_address }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="col-lg-12 col-form-label required fw-semibold fs-6">Country</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <select name="members_country" aria-label="Select a Country" data-control="select2" data-placeholder="Select a country..." class="form-select form-select-solid form-select-lg fw-semibold">
                                                        <option value="">Select a Country...</option>
                                                        @foreach($countries as $country)
                                                        <option value="{{ $country->country_name }}" @if($country->country_name==$member->members_country) selected="selected" @endif >{{ $country->country_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="col-lg-12 col-form-label fw-semibold fs-6">Town</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="text" name="members_town" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Town" value="{{ $member->members_town }}">
                                                </div>
                                            </div>
                                            <!-- <div class="col-md-6">
                                                <label class="col-lg-12 col-form-label fw-semibold fs-6">Street</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="text" name="members_street" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Street" value="{{ $member->members_street }}">
                                                </div>
                                            </div> -->

                                            <div class="col-md-6">
                                                <label class="col-lg-12 col-form-label fw-semibold fs-6">Region</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="text" name="members_region" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Region" value="{{ $member->members_region }}">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="col-lg-12 col-form-label required fw-semibold fs-6">Postcode</label>
                                                    <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                        <input type="text" name="members_postcode" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Postcode" value="{{ $member->members_postcode }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label class="col-lg-12 col-form-label required fw-semibold fs-6">Phone Number</label>
                                                    <div class="row">
                                                        <div class="col-lg-4 fv-row fv-plugins-icon-container">
                                                            <select name="members_phone_code" aria-label="Select Phone Code" data-control="select2" data-placeholder="Select Phone Code..." class="form-select form-select-solid form-select-lg fw-semibold pr-4 text-nowrap">
                                                                <option value="">Select Phone Code...</option>
                                                                @foreach($countries as $country)
                                                                <option value="{{ $country->id }}" @if($country->id==$member->members_phone_code) selected="selected" @endif >+{{ $country->country_phonecode }} - {{ $country->country_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-lg-8 fv-row fv-plugins-icon-container px-1">
                                                            <input type="text" name="members_phone" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Phone number" value="{{ $member->members_phone }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="col-lg-12 col-form-label required fw-semibold fs-6">Member Type</label>
                                                    <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                        <select id="members_type" name="members_type" aria-label="Select a Type" data-control="select2" data-placeholder="Select a Type..." class="form-select form-select-solid form-select-lg fw-semibold" {{ $isDisabled }}>
                                                            <option value="F" @if($member->members_type=='F') selected="selected" @endif>Free Member</option>

                                                            <option value="M" @if($member->members_type=='M') selected="selected" @endif>Full Member</option>
                                                        </select>
                                                    </div>
                                                </div>


                                                <div id="expiry_date" style="@if($member->members_type=='F') display:none; @endif">
                                                    <div class="col-md-6">
                                                        <label class="col-lg-12 col-form-label required fw-semibold fs-6">Expiry Date</label>
                                                        <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                            <input type="text" name="members_subscription_end_date" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Expiry Date" id="members_subscription_end_date" value="{{ ($member->members_subscription_end_date && $member->members_subscription_end_date != '0000-00-00') ? date('d-m-Y', strtotime($member->members_subscription_end_date)) : '' }}" readonly>
                                                        </div>
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
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>

                    </form>

                </div>
            </div>
            <!--end::Toolbar container-->
        </div>
    </div>
    <!--end::Content wrapper-->

    @include('common.datepicker', ['id' => 'dob-datepicker', 'class' => 'members_subscription_end_date_edit'])
    @include('common.text_editor')
    @include('layouts.dashboard_footer')
    @include('common.preview_image')
    @include('scripts.member')
    @include('common.sanitize_input')
</div>
<style>
    input[type="file"] {
        color: transparent;
        /* Makes the "no file chosen" text invisible */
    }
</style>

@endsection