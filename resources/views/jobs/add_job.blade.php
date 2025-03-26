@extends('layouts.app')

@section('content')

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->

    <div class="d-flex flex-column flex-column-fluid">
    	<div class="breadcrumb-wrapper">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('jobs') }}">Jobs</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add Job</li>
                </ol>
            </nav>
        </div>
    	
        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <!--begin::Toolbar container-->
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">

                <div class="event-outer w-100">
                	
                	@if ($errors->any())
			        <div class="alert alert-danger">
			            <ul>
			                @foreach ($errors->all() as $error)
			                    <li>{{ $error }}</li>
			                @endforeach
			            </ul>
			        </div>
				    @endif

                	<form method="POST" id="jobForm" action="{{ route('save-job') }}" enctype="multipart/form-data">
                        @csrf

	                    <h1 class="fs-2x text-dark mb-3 common-head">Add Job</h1>
						<div class="row">
					      	<div class="col-md-6 mb-3">
                            	<div class="card p-5 w-100">
							  		<div class="align-items-center d-flex justify-content-between">
										<div>
										   <h2>Job Details</h2>
										</div>
										<div class="row">
	                                        <div class="col-md-6">
	                                            <label class="fw-semibold fs-6">Status</label>
	                                        </div>
	                                        <div class="col-md-6">
	                                            <div class="status-container">
	                                                <label class="switch">
	                                                    <input type="checkbox" name="job_status" >
	                                                    <span class="slider"></span>
	                                                </label>
	                                            </div>
	                                        </div>
	                                    </div> 
							  		</div>
									<div class="row">
			                            <!--begin::Label-->
			                            <label class="col-lg-12 col-form-label required fw-semibold fs-6">Select Member</label>
			                            <!--end::Label-->
			                            <!--begin::Col-->
			                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
			                                <select name="member_id" aria-label="Select a Member" data-control="select2" data-placeholder="Select a member..." class="form-select form-select-solid form-select-lg fw-semibold">
		                                        <option value="" data-select2-id="select2-data-12-8p9k">Select Member...</option>
		                                        @foreach($membersList as $members)
		                                        <option value="{{ $members->id }}"  data-select2-id="select2-data-12-8p9k">{{ $members->members_fname.' '.$members->members_lname }}</option>
		                                        @endforeach
		                                    </select>
			                                <div class="fv-plugins-message-container invalid-feedback"></div>
			                            </div>
			                            <!--end::Col-->
	                       			</div>
									<div class="row">
			                            <!--begin::Label-->
			                            <label class="col-lg-12 col-form-label fw-semibold fs-6">Job Role</label>
			                            <!--end::Label-->
			                            <!--begin::Col-->
			                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
			                                <select name="job_role" aria-label="Select a Role" data-control="select2" data-placeholder="Select a Role..." class="form-select form-select-solid form-select-lg fw-semibold">
		                                        <option value="" >Select Role...</option>
		                                        @foreach($jobRoles as $roles)
		                                        <option value="{{ $roles->id }}" >{{ $roles->job_role }}</option>
		                                        @endforeach
		                                    </select>

			                                <div class="fv-plugins-message-container invalid-feedback"></div>
			                            </div>
			                        </div>
                            
									<div class="row">
			                            <!--begin::Label-->

			                            <label class="col-lg-12 col-form-label required fw-semibold fs-6">Boat Type</label>
			                            <!--end::Label-->
			                            <!--begin::Col-->
			                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
		                                    <select name="boat_type" aria-label="Select a Country" data-control="select2" data-placeholder="Select a country..." class="form-select form-select-solid form-select-lg fw-semibold">
		                                        <option value="" >Select Boat Type...</option>
		                                        @foreach($boatType as $type)
		                                        <option value="{{ $type->id }}" >{{ $type->boat_type }}</option>
		                                        @endforeach
		                                    </select>
		                                </div>
			                        </div>
									<div class="row">
			                            <!--begin::Label-->
			                            <label class="col-lg-12 col-form-label required fw-semibold fs-6">Duration</label>
			                            <!--end::Label-->
			                            <!--begin::Col-->
			                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
		                                    <select name="job_duration" aria-label="Select a Country" data-control="select2" data-placeholder="Select a country..." class="form-select form-select-solid form-select-lg fw-semibold">
		                                        <option value="" data-select2-id="select2-data-12-8p9k">Select Duration...</option>
		                                        @foreach($jobDuration as $duration)
		                                        <option value="{{ $duration->id }}"  data-select2-id="select2-data-12-8p9k">{{ $duration->job_duration }}</option>
		                                        @endforeach
		                                    </select>
		                                </div>
			                        </div>

			                        <div class="row">
			                            <!--begin::Label-->
			                            <label class="col-lg-12 col-form-label required fw-semibold fs-6">Start Date</label>
			                            <!--end::Label-->
			                            <!--begin::Col-->
			                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
			                                <input type="text" name="job_start_date" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Job Start Date" value="" id="startdate-datepicker">
			                                <div class="fv-plugins-message-container invalid-feedback"></div>
			                            </div>
			                        </div>
			                        <div class="row">
			                            <label class="col-lg-12 col-form-label required fw-semibold fs-6">Job Location</label>
			                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
			                            	<select name="job_location" aria-label="Select a Location" data-control="select2" data-placeholder="Select a Location..." class="form-select form-select-solid form-select-lg fw-semibold">
		                                        <option value="" >Select Location...</option>
		                                        @foreach($boatLocation as $location)
		                                        <option value="{{ $location->id }}" >{{ $location->boat_location }}</option>
		                                        @endforeach
		                                    </select>
			                                <div class="fv-plugins-message-container invalid-feedback"></div>
				                        </div>
			                        </div>
			                        
							  		<div class="align-items-center d-flex justify-content-between mt-5">
										<div>
										   <h2>Vessel Details</h2>
										</div>
							  		</div>
									<div class="row">
			                            <!--begin::Label-->
			                            <label class="col-lg-12 col-form-label required fw-semibold fs-6">Vessel</label>
			                            <!--end::Label-->
			                            <!--begin::Col-->
			                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
			                                <input type="text" name="vessel_desc" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Describe the boat here" value="" >
			                                <div class="fv-plugins-message-container invalid-feedback"></div>
			                            </div>
			                            <!--end::Col-->
	                       			</div>
	                       			
	                       			<div class="row">
			                            <!--begin::Label-->
			                            <label class="col-lg-12 col-form-label required fw-semibold fs-6">Vessel Type</label>
			                            <!--end::Label-->
			                            <!--begin::Col-->
			                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
			                                <select name="vessel_type" aria-label="Select a Country" data-control="select2" data-placeholder="Select a country..." class="form-select form-select-solid form-select-lg fw-semibold">
		                                        <option value="" >Select Vessel Type...</option>
		                                        @foreach($vesselType as $vessel)
		                                        <option value="{{ $vessel->vessel_id }}" >{{ $vessel->vessel_type }}</option>
		                                        @endforeach
		                                    </select>
			                                <div class="fv-plugins-message-container invalid-feedback"></div>
			                            </div>
			                            <!--end::Col-->
	                       			</div>
	                       			<div class="row">
			                            <!--begin::Label-->
			                            <label class="col-lg-12 col-form-label required fw-semibold fs-6">Vessel Size</label>
			                            <!--end::Label-->
			                            <!--begin::Col-->
			                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
			                                <input type="text" name="vessel_size" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Vessel Size" value="" >
			                                <div class="fv-plugins-message-container invalid-feedback"></div>
			                            </div>
			                            <!--end::Col-->
	                       			</div>
							   
								</div>  
						  	</div>
						  	<div class="col-md-6 mb-3">
							   	<div class="card p-5">
									<div class="row">
										<label class="col-lg-12 col-form-label fw-semibold fs-6">Job Image</label>
										<div class="col-lg-12 fv-row fv-plugins-icon-container events-container">
											<div class="event-image">
											   <img src="{{ asset('assets/images/no_event.jpg') }}" id="preview">
											</div>
											<input type="file" name="job_images[]" class="mt-3" onchange="previewImage(event, 'preview')" multiple>
										</div>
									</div> 

		                            
			                        <div class="row mt-10">
			                            <label class="col-lg-12 col-form-label required fw-semibold fs-6">Job Summary</label>
			                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
				                            <textarea name="job_summary" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0 text_editor" placeholder="Summary" value=""></textarea>
			                                <div class="fv-plugins-message-container invalid-feedback"></div>
				                        </div>
			                        </div>
							   	</div>

							</div>

							<div class="col-md-6 mb-3">
							   	

							</div>
							
	                    <div class="card-footer d-flex justify-content-end px-0 py-3">
	                    	<a href="{{ route('jobs') }}" style="margin-right: 10px;" class="btn btn-primary">
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
    @include('common.datepicker', ['id' => 'startdate-datepicker','maxDate' => '+1y'])
    @include('common.text_editor')
    @include('common.preview_image')
    @include('scripts.job')
    @include('layouts.dashboard_footer')
</div>

@endsection