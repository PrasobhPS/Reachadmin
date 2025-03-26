@extends('layouts.app') @section('content')

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->

    <div class="d-flex flex-column flex-column-fluid">

        <div class="breadcrumb-wrapper">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Members</a></li>
                    <li class="breadcrumb-item active" aria-current="page">View Employee</li>
                </ol>
            </nav>
        </div>
        
        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <!--begin::Toolbar container-->
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">


                <div class="outer-data  w-100">

                        <h1 class="fs-2x text-dark mb-3 common-head">View Employee</h1>

                        <div class="add-details pt-3 common-form-container">

                            <div class="row">
                            <div class="col-md-6 mb-3">
                                    <div class="card w-100 p-5">
                                        

                                        <div class="row">
                                            <div class="col-md-12 d-flex align-items-center">
                                                <label class="col-lg-6 col-form-label fw-semibold fs-4">Job Role</label>
                                                <div class="col-lg-6 fv-row fv-plugins-icon-container">
                                                    {{ $employee->jobRole->job_role }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 d-flex align-items-center">
                                                <label class="col-lg-6 col-form-label fw-semibold fs-4">Passport</label>
                                                <div class="col-lg-6 fv-row fv-plugins-icon-container">
                                                    {{ $employee->passport->country_name }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 d-flex align-items-center">
                                                <label class="col-lg-6 col-form-label fw-semibold fs-4">Current Availability</label>
                                                <div class="col-lg-6 fv-row fv-plugins-icon-container">
                                                    {{ $employee->availability ? $employee->availability->availability_name : '' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 d-flex align-items-center">
                                                <label class="col-lg-6 col-form-label fw-semibold fs-4">Current location</label>
                                                <div class="col-lg-6 fv-row fv-plugins-icon-container">
                                                    @if($employee->country)
                                                        {{ $employee->country->country_name }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 d-flex align-items-center">
                                                <label class="col-lg-6 col-form-label fw-semibold fs-4">Position</label>
                                                <div class="col-lg-6 fv-row fv-plugins-icon-container">
                                                    {{ $employee->position ? $employee->position->position_name : '' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 d-flex align-items-center">
                                                <label class="col-lg-6 col-form-label fw-semibold fs-4">Vessel type</label>
                                                <div class="col-lg-6 fv-row fv-plugins-icon-container">
                                                @if($employee->vessel)
                                                    {{ $employee->vessel->vessel_type }}
                                                @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 d-flex align-items-center">
                                                <label class="col-lg-6 col-form-label fw-semibold fs-4">Salary Expectations</label>
                                                <div class="col-lg-6 fv-row fv-plugins-icon-container">
                                                    {{ $employee->expectations ? $employee->expectations->expectation_name : '' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 d-flex align-items-center">
                                                <label class="col-lg-6 col-form-label fw-semibold fs-4">Experience</label>
                                                <div class="col-lg-6 fv-row fv-plugins-icon-container">
                                                    {{ $employee->experience ? $employee->experience->experience_name : '' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card w-100 p-5 personal-details">
                                       <div class="d-flex align-items-center justify-content-between head">
                                         <h2>Personal Details</h2>
                                        <div class="profile-upload mb-3">
                                            <div class="d-flex align-items-center">
                                                @if(isset($employee->member->members_profile_picture))
                                                <div class="mx-1 pro-img">
                                                    <img src="{{ asset('storage/' . $employee->member->members_profile_picture) }}" alt="Profile Picture" class="thumbnail photo">
                                                </div>
                                                @else
                                                <div class="mx-1 pro-img">
                                                    <img src="{{ asset('assets/images/noimage.jpg') }}" alt="Profile Picture" class="thumbnail photo">
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                       </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-1">
                                                <div class="card-block">
                                                    <label>Name</label>
                                                    <span> {{ $employee->member->members_fname }}  {{ $employee->member->members_lname }} </span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-1">
                                                 <div class="card-block">
                                                    <label>Email Address</label>
                                                    <span> {{ $employee->member->members_email }} </span>
                                                     <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>    
                                            </div>
                                            <div class="col-md-6 mb-1">
                                                 <div class="card-block">
                                                    <label>Date of Birth</label>
                                                    <span>{{ date('d-m-Y', strtotime($employee->member->members_dob)) }}</span>
                                                     <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>    
                                            </div>
                                            <div class="col-md-6 mb-1">
                                               <div class="card-block">
                                                    <label>Address</label>
                                                <span>
                                                     {{ $employee->member->members_address }}
                                                </span>
                                               </div>
                                            </div>
                                             <div class="col-md-6 mb-1">
                                               <div class="card-block">
                                                 <label>Country</label>
                                                <span>
                                                    {{ $employee->member->members_country }}
                                                </span>
                                               </div>
                                            </div>
                                              <div class="col-md-6 mb-1">
                                                 <div class="card-block">
                                                     <label>Region</label>
                                                    <span>
                                                       {{ $employee->member->members_region }}
                                                    </span>
                                                  </div>
                                               </div>
                                           </div>
                                    </div>
                                    <div class="card w-100 p-5">
                                        <div class="row">
                                        <h2>Upload Media</h2>
                                        <div class="col-lg-12 fv-row fv-plugins-icon-container events-container">

                                            @if($upload_media->isNotEmpty())
                                                <div class="event-image">
                                                   <img src="{{ asset('storage/' . $upload_media[0]->media_file) }}" id="preview">
                                                </div>
                                                <div class="row">
                                                    @foreach($upload_media as $image)
                                                    <div class="col-md-3">
                                                        <img src="{{ asset('storage/' . $image->media_file) }}" class="img-thumbnail" style="max-height: 100px;" onclick="previewThumbImage('{{ asset('storage/' . $image->media_file) }}')">
                                                    </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                        </div> 
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-end px-0 py-3">
                            <a href="{{ route('employee') }}" style="margin-right: 10px;" class="btn btn-primary">
                                Cancel
                            </a>
                            
                        </div>

                </div>
            </div>
            <!--end::Toolbar container-->
        </div>
    </div>
    <!--end::Content wrapper-->

    @include('common.preview_image')
    @include('layouts.dashboard_footer')
</div>

@endsection