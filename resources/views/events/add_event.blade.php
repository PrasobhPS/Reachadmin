@extends('layouts.app')

@section('content')

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
        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <!--begin::Toolbar container-->
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">

                <div class="event-outer w-100">
                	<form method="POST" action="{{ route('save-event') }}" enctype="multipart/form-data">
                        @csrf

	                    <h1 class="fs-2x text-dark mb-3 common-head">Add Events</h1>
						<div class="row">
					      <div class="col-md-6">
                            <div class="card p-5 w-100">
							  	<div class="align-items-center d-flex justify-content-between">
									<div>
									   <h2>Event Details</h2>
									</div>
									<div class="row">
	                                    <div class="col-md-6">
	                                        <label class="fw-semibold fs-6">Status</label>
	                                    </div>
	                                    <div class="col-md-6">
	                                        <div class="status-container">
	                                            <label class="switch">
	                                                <input type="checkbox" name="event_status" >
	                                                <span class="slider"></span>
	                                            </label>
	                                        </div>
	                                    </div>
	                                </div>
	                                <div class="row">
	                                    <div class="col-md-8">
	                                        <label class="fw-semibold fs-6">Members Only?</label>
	                                    </div>
	                                    <div class="col-md-4">
	                                        <div class="status-container">
	                                            <label class="switch">
	                                                <input type="checkbox" name="event_members_only" >
	                                                <span class="slider"></span>
	                                            </label>
	                                        </div>
	                                    </div>
	                                </div> 
								</div>
								<div class="row">
	                            <!--begin::Label-->
	                            <label class="col-lg-12 col-form-label required fw-semibold fs-6">Event Name</label>
	                            <!--end::Label-->
	                            <!--begin::Col-->
	                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
	                                <input type="text" name="event_name" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Event Name" value="">
	                                <div class="fv-plugins-message-container invalid-feedback"></div>
	                            </div>
	                            <!--end::Col-->
	                        </div>
							<div class="row">
	                            <!--begin::Label-->
	                            <label class="col-lg-12 col-form-label fw-semibold fs-6">Event Details</label>
	                            <!--end::Label-->
	                            <!--begin::Col-->
	                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
	                                <textarea name="event_details" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Event Details" value=""></textarea>
	                                <div class="fv-plugins-message-container invalid-feedback"></div>
	                            </div>
	                        </div>
                            
							<div class="row">
	                            <!--begin::Label-->
	                            <label class="col-lg-12 col-form-label required fw-semibold fs-6">Start Date</label>
	                            <!--end::Label-->
	                            <!--begin::Col-->
	                            <div class="col-lg-12 fv-row fv-plugins-icon-container datepicker-outer">
	                                <input type="text" name="event_start_date" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Start Date" value="" id="startdate-datepicker">
	                                <div class="fv-plugins-message-container invalid-feedback"></div>
	                            </div>
	                        </div>
							<div class="row">
	                            <!--begin::Label-->
	                            <label class="col-lg-12 col-form-label required fw-semibold fs-6">End Date</label>
	                            <!--end::Label-->
	                            <!--begin::Col-->
	                            <div class="col-lg-12 fv-row fv-plugins-icon-container datepicker-outer">
		                            <input type="text" name="event_end_date" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="End Date" value="" id="enddate-datepicker">
		                        </div>
	                        </div>
                            <div class="row">
	                            <label class="col-lg-12 col-form-label required fw-semibold fs-6">Allowed Number of Members</label>
	                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
		                            <input type="number" name="event_allowed_members" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="" value="" min="1" >
		                        </div>
	                        </div>
							</div>  
						  </div>
						  <div class="col-md-6">
						   	<div class="card p-5">
								<div class="row">
									<label class="col-lg-12 col-form-label fw-semibold fs-6">Event Picture</label>
									<div class="col-lg-12 fv-row fv-plugins-icon-container events-container">
										<div class="event-image">
										   <img src="{{ asset('assets/images/no_event.jpg') }}" id="preview">
										</div>
										<input type="file" name="event_picture" class="mt-3" placeholder="" value="" onchange="previewImage(event)" >
									</div>
								</div> 
						   	</div>

						</div>
						

	                    <div class="card-footer d-flex justify-content-end px-0 py-3">
	                    	<a href="{{ route('events') }}" style="margin-right: 10px;" class="btn btn-primary">
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
    @include('common.datepicker', ['id' => 'startdate-datepicker'])
    @include('common.datepicker', ['id' => 'enddate-datepicker','maxDate' => '+1y'])
    @include('common.text_editor')
    @include('layouts.dashboard_footer')
</div>

@endsection