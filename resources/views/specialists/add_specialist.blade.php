@extends('layouts.app') @section('content')

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->

    <div class="d-flex flex-column flex-column-fluid">
        @if ($errors->any())
        <div style="color:red">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <!--begin::Toolbar container-->
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">

                <div class="outer-data  w-100">
                    <form method="POST" id="specialistForm" action="{{ route('save-specialist') }}" enctype="multipart/form-data">
                        @csrf

                        <h1 class="fs-2x text-dark mb-3 common-head">Add Expert</h1>

                        <div class="add-details  pt-3 common-form-container">

                            <div class="row">
                            <div class="col-md-6 mb-3">
                                    <div class="card w-100 p-5">
                                        <h2>Profile Photo</h2>
                                        <div class="profile-upload mb-3">
                                            <div class="d-flex align-items-center">
                                                
                                                <div class="mx-1">
                                                    <img src="{{ asset('assets/images/noimage.jpg') }}" alt="Profile Picture" class="thumbnail photo" id="preview">
                                                </div>
                                                <div class="buton-area mx-1">
                                                    <input type="file" name="specialist_profile_picture" class="w-100 mb-1" placeholder="" value="" onchange="previewImage(event,'preview')">
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
                                                                <input type="checkbox" name="specialist_status" >
                                                                <span class="slider"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <!--begin::Label-->
                                                <label class="col-lg-12 col-form-label required fw-semibold fs-6">Biography</label>
                                                <!--end::Label-->
                                                <!--begin::Col-->
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <textarea id="specialist_biography" name="specialist_biography" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0 text_editor" placeholder="Biography"></textarea>
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                    <label id="specialist_biography-error" class="error" for="specialist_biography"></label>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <!--begin::Label-->
                                                <label class="col-lg-12 col-form-label required fw-semibold fs-6">Title</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <select name="specialist_title" aria-label="Select Title" data-control="select2" data-placeholder="Select Title..." class="form-select form-select-solid form-select-lg fw-semibold">
                                                        <option value="" data-select2-id="select2-data-12-8p9k">Select Title...</option>
                                                        @foreach($jobRoles as $roles)
                                                        <option value="{{ $roles->job_role }}"  data-select2-id="select2-data-12-8p9k">{{ $roles->job_role }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <!--begin::Label-->
                                                <label class="col-lg-12 col-form-label fw-semibold fs-6">Specialist Interests</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="text" name="specialist_interest" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Interests" value="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card w-100 p-5">
                                        <h2>Personal Details</h2>
                                        <div class="row">
                                            <label class="col-lg-12 col-form-label fw-semibold fs-6">Select Member</label>
                                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                <select name="member_id" class="form-select form-select-solid form-select-lg fw-semibold" onchange="getMemberDetails(this.value)">
                                                    <option value="">Select a Member...</option>
                                                    @foreach($members as $value)
                                                    <option value="{{ $value->id }}" >{{ $value->members_fname.' '.$value->members_lname }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="col-lg-12 col-form-label required fw-semibold fs-6">First Name</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="text" name="specialist_fname" id="specialist_fname" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="First name" value="">
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="col-lg-12 col-form-label required fw-semibold fs-6">Last Name</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="text" name="specialist_lname" id="specialist_lname" class="form-control form-control-lg form-control-solid" placeholder="Last name" value="">
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
                                                    <input type="text" name="specialist_email" id="specialist_email" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Email" value="">
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="col-lg-12 col-form-label required fw-semibold fs-6">Phone Number</label>
                                                <div class="row">
                                                <div class="col-lg-4 fv-row fv-plugins-icon-container">
                                                    <select name="specialist_phone_code" id="specialist_phone_code" aria-label="Select Phone Code" data-control="select2" data-placeholder="Select Phone Code..." class="form-select form-select-solid form-select-lg fw-semibold px-4">
                                                        <option value="">Select Phone Code...</option>
                                                        @foreach($countries as $country)
                                                        <option value="{{ $country->id }}" >+{{ $country->country_phonecode }} - {{ $country->country_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-lg-8 fv-row fv-plugins-icon-container px-1">
                                                    <input type="text" name="specialist_phone" id="specialist_phone" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Phone number" value="">
                                                </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <!--begin::Label-->
                                                <label class="col-lg-12 col-form-label required fw-semibold fs-6">Date of Birth</label>
                                                <!--end::Label-->
                                                <!--begin::Col-->
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="text" name="specialist_dob" id="specialist_dob" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="DOB" value="" readonly>
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <!--begin::Label-->
                                                <label class="col-lg-12 col-form-label required fw-semibold fs-6">Address</label>
                                                <!--end::Label-->
                                                <!--begin::Col-->
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="text" name="specialist_address" id="specialist_address" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Address" value="" >
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <!--begin::Label-->
                                                <label class="col-lg-12 col-form-label required fw-semibold fs-6">Country</label>
                                                <!--end::Label-->
                                                <!--begin::Col-->
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <select name="specialist_country" id="specialist_country" aria-label="Select a Country" data-control="select2" data-placeholder="Select a country..." class="form-select form-select-solid form-select-lg fw-semibold">
                                                        <option value="" data-select2-id="select2-data-12-8p9k">Select a Country...</option>
                                                        @foreach($countries as $country)
                                                        <option value="{{ $country->country_name }}"  data-select2-id="select2-data-12-8p9k">{{ $country->country_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <!--begin::Label-->
                                                <label class="col-lg-12 col-form-label required fw-semibold fs-6">Region</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="text" name="specialist_region" id="specialist_region" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Region" value="">
                                                </div>
                                            </div>
                                            <!-- <div class="col-md-6">
					                            <label class="col-lg-12 col-form-label fw-semibold fs-6">Specialist Video</label>
					                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
						                            <input type="file" name="specialist_video" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="" value="" >
						                        </div>
					                        </div> -->
                                        </div>
                                    </div>
                                </div>

                                

                                <div class="col-md-6 mb-3">
                                    <div class="card w-100 p-5">
                                        <h2>Employment Details</h2>
                                        <div class="row">
                                            <label class="col-lg-12 col-form-label fw-semibold fs-6">Current Employment</label>
                                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                <input type="text" name="specialist_employment" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Current Employment" value="">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <label class="col-lg-12 col-form-label fw-semibold fs-6">Employment History</label>
                                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                <textarea id="specialist_employment_history" name="specialist_employment_history" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0 text_editor2" placeholder="Employment History"></textarea>
                                                <div class="fv-plugins-message-container invalid-feedback"></div>
                                                <label id="specialist_employment_history-error" class="error" for="specialist_employment_history"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>


                        </div>
                        <div class="card-footer d-flex justify-content-end px-0 py-3">
	                    	<a href="{{ route('specialists') }}" style="margin-right: 10px;" class="btn btn-primary">
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

    <script>
    function getMemberDetails(memberId) {
        if (!memberId) {
            $('#specialist_fname').val('');
            $('#specialist_lname').val('');
            $('#specialist_email').val('');
            $('#specialist_phone').val('');
            $('#specialist_dob').val('');
            $('#specialist_country').val('');
            $('#specialist_region').val('');
            $('#specialist_address').val('');
            return;
        }

        $.ajax({
            url: `/specialist/member-details/${memberId}`,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                $('#specialist_fname').val(data.members_fname);
                $('#specialist_lname').val(data.members_lname);
                $('#specialist_email').val(data.members_email);
                $('#specialist_phone_code').val(data.members_phone_code);
                $('#specialist_phone').val(data.members_phone);
                $('#specialist_dob').val(data.members_dob);
                $('#specialist_country').val(data.members_country);
                $('#specialist_region').val(data.members_region);
                $('#specialist_address').val(data.members_address);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching member details:', error);
            }
        });
    }
    </script>


    @include('common.datepicker', ['id' => 'specialist_dob'])
    @include('common.text_editor')
    @include('layouts.dashboard_footer')
    @include('common.preview_image')
    @include('scripts.specialist')
</div>

@endsection