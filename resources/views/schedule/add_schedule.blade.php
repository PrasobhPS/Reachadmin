@extends('layouts.app')

@section('content')

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->

    <div class="d-flex flex-column flex-column-fluid">

    	<div class="breadcrumb-wrapper">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('specialists') }}">Experts</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Schedule Timings</li>
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
	    
                	<form method="POST" id="scheduleForm" action="{{ route('schedule-save') }}">
                        @csrf

                        <input type="hidden" id="member_id" name="member_id" value="{{ $specialist->id }}">
                        
	                    <h1 class="fs-2x text-dark mb-3 common-head">{{ $specialist->members_fname.' '.$specialist->members_lname }} - Schedule Timings</h1>
						<div class="row">
					      <div class="col-md-12">
                            <div class="card p-5 w-100">

                            	<div class="row">
		                            <label class="col-lg-12 col-form-label required fw-semibold fs-6">Choose Time Duration</label>
		                            <div class="col-md-6">
		                            	<select id="time_slots" name="time_slots" class="form-select">
		                            		<option value="30">30 Min</option>
		                            		<option value="60">1 Hr</option>
		                            		<option value="120">2 Hr</option>
		                            	</select>
		                            </div>
		                            <div class="col-md-6">
		                            </div>
		                        </div>
								<div class="row">
		                            <label class="col-lg-12 col-form-label required fw-semibold fs-6">Choose day to consult</label>
		                            <div class="col-lg-12 fv-row fv-plugins-icon-container">

	                                    <div class="col-md-12 choose-day">
	                                        <div class="time-btn-sec">
	                                            <label class="form-label day-checkbox"><input type="checkbox" name="day[]" value="1"> Monday</label>
	                                            <label class="form-label day-checkbox"><input type="checkbox" name="day[]" value="2"> Tuesday</label>
	                                            <label class="form-label day-checkbox"><input type="checkbox" name="day[]" value="3"> Wednesday</label>
	                                            <label class="form-label day-checkbox"><input type="checkbox" name="day[]" value="4"> Thursday</label>
	                                            <label class="form-label day-checkbox"><input type="checkbox" name="day[]" value="5"> Friday</label>
	                                            <label class="form-label day-checkbox"><input type="checkbox" name="day[]" value="6"> Saturday</label>
	                                            <label class="form-label day-checkbox"><input type="checkbox" name="day[]" value="0"> Sunday</label>
	                                        </div>
	                                        <label id="day[]-error" class="error" for="day[]"></label>
	                                    </div>

		                            </div>
		                        </div>

		                        <div class="row">
		                        	<div class="col-md-10">
		                         		<div id="time-slots-list"></div>
		                         	</div>
		                        </div>
								
							</div>  
						  </div>
						</div>
						
	                    <div class="card-footer d-flex justify-content-end px-0 py-3">
	                    	<a href="{{ route('specialists') }}" style="margin-right: 10px;" class="btn btn-primary">
                            	Cancel
                        	</a> 
	                        <button type="button" id="submitBtn" class="btn btn-primary">Submit</button>
	                    </div>

                    </form>

                </div>
            </div>
            <!--end::Toolbar container-->
        </div>
    </div>
    <!--end::Content wrapper-->

    @include('layouts.dashboard_footer')
    @include('scripts.schedule')

</div>

@endsection