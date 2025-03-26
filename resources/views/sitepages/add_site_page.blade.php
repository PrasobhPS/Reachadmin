@extends('layouts.app')

@section('content')

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->

    <div class="d-flex flex-column flex-column-fluid">
    	
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
	    
                	<form method="POST" action="{{ route('save-site-page') }}" enctype="multipart/form-data">
                        @csrf

	                    <h1 class="fs-2x text-dark mb-3 common-head">Add Site Page</h1>
						<div class="row">
					      <div class="col-md-6">
                            <div class="card p-5 w-100">
								<div class="align-items-center d-flex justify-content-between">
									<div>
									   <h2>Page Details</h2>
									</div>
								</div>
								<div class="row">
		                            <!--begin::Label-->
		                            <label class="col-lg-12 col-form-label required fw-semibold fs-6">Page Header</label>
		                            <!--end::Label-->
		                            <!--begin::Col-->
		                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
		                                <input type="text" name="site_page_header" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Header Name" value="{{ old('site_page_header') }}">
		                                <div class="fv-plugins-message-container invalid-feedback"></div>
		                            </div>
		                            <!--end::Col-->
		                        </div>
		                        <div class="row">
		                            <!--begin::Label-->
		                            <label class="col-lg-12 col-form-label required fw-semibold fs-6">Page Slug</label>
		                            <!--end::Label-->
		                            <!--begin::Col-->
		                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
		                                <input type="text" name="site_page_slug" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Header Name" value="{{ old('site_page_slug') }}">
		                                <div class="fv-plugins-message-container invalid-feedback"></div>
		                            </div>
		                            <!--end::Col-->
		                        </div>

								<div class="row">
		                            <!--begin::Label-->
		                            <label class="col-lg-12 col-form-label required fw-semibold fs-6">Page Details</label>
		                            <!--end::Label-->
		                            <!--begin::Col-->
		                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
		                                <textarea name="site_page_details" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0 text_editor" placeholder="Page Details" value="">{{ old('site_page_details') }}</textarea>
		                                <div class="fv-plugins-message-container invalid-feedback"></div>
		                            </div>
		                        </div>

							</div>  
						  </div>
						  <div class="col-md-6">
						  	<div class="card p-5">
								<div class="row">
									<label class="col-lg-12 col-form-label fw-semibold fs-6">Header Image</label>
									<div class="col-lg-12 fv-row fv-plugins-icon-container events-container">
										<div class="event-image">
										   <img src="{{ asset('assets/images/no_event.jpg') }}" id="preview">
										</div>
										<input type="file" name="site_page_images" class="mt-3" placeholder="" value="" onchange="previewImage(event,'preview')" >
									</div>
								</div> 
						   	</div>


						</div>
						

	                    <div class="card-footer d-flex justify-content-end px-0 py-3">
	                    	<a href="{{ route('site-pages') }}" style="margin-right: 10px;" class="btn btn-primary">
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
    @include('common.preview_image')
    @include('common.text_editor')
    @include('layouts.dashboard_footer')
</div>

@endsection