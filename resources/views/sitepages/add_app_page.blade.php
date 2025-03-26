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
	    
                	<form method="POST" action="{{ route('save-app-page') }}" enctype="multipart/form-data">
                        @csrf

	                    <h1 class="fs-2x text-dark mb-3 common-head">Add App Page</h1>
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
										<select name="site_page_slug" id="site_page_slug" class="required form-select form-select-lg form-select-solid fw-semibold">
											<option value="" >Select</option><option value="home_expert">Expert</option>
											<option value="home_charter">Charter</option>
											<option value="home_cruz">Cruz</option>
											<option value="home_chandlery">Chandlery</option>
										</select> 
		                                <!-- <input type="text" name="site_page_slug" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Paage Slug" value="{{ old('site_page_slug') }}"> -->
		                                <div class="fv-plugins-message-container invalid-feedback"></div>
		                            </div>
		                            <!--end::Col-->
		                        </div>

								<div class="row">
		                            <!--begin::Label-->
		                            <label class="col-lg-12 col-form-label fw-semibold fs-6">Page Details</label>
		                            <!--end::Label-->
		                            <!--begin::Col-->
		                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
		                                <textarea name="site_page_details" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Page Details" value="">{{ old('site_page_details') }}</textarea>
		                                <div class="fv-plugins-message-container invalid-feedback"></div>
		                            </div>
		                        </div>
								<div class="row">
		                            <label class="col-lg-12 col-form-label required fw-semibold fs-6"> Display Order</label>
		                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
		                                <input type="number" name="order" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Display Order" min="0" value="{{ old('order') }}">
		                            </div>
		                        </div>
								<div class="row chandlery_form hidden">
									<h3>Chandlery Details</h2>
								</div>
								<div class="row chandlery_form hidden">
		                            <!--begin::Label-->
		                            <label class="col-lg-12 col-form-label fw-semibold fs-6">Category 1</label>
		                            <!--end::Label-->
		                            <!--begin::Col-->
		                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
		                                <input type="text" name="site_chandlery_category1" id="site_chandlery_category1" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Category" value="{{ old('site_chandlery_category1') }}">
		                                <div class="fv-plugins-message-container invalid-feedback"></div>
		                            </div>
		                            <!--end::Col-->
		                        </div>
								<div class="row chandlery_form hidden">
		                            <!--begin::Label-->
		                            <label class="col-lg-12 col-form-label fw-semibold fs-6">Category 2</label>
		                            <!--end::Label-->
		                            <!--begin::Col-->
		                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
		                                <input type="text" name="site_chandlery_category2" id="site_chandlery_category2" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Category" value="{{ old('site_chandlery_category2') }}">
		                                <div class="fv-plugins-message-container invalid-feedback"></div>
		                            </div>
		                            <!--end::Col-->
		                        </div>
								<div class="row chandlery_form hidden">
		                            <!--begin::Label-->
		                            <label class="col-lg-12 col-form-label fw-semibold fs-6">Discount Percentage</label>
		                            <!--end::Label-->
		                            <!--begin::Col-->
		                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
		                                <input type="text" name="site_chandlery_percentage" id="site_chandlery_percentage" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Discount Percentage" value="{{ old('site_chandlery_percentage') }}">
		                                <div class="fv-plugins-message-container invalid-feedback"></div>
		                            </div>
		                            <!--end::Col-->
		                        </div>
								<div class="row chandlery_form hidden">
		                            <!--begin::Label-->
		                            <label class="col-lg-12 col-form-label fw-semibold fs-6">Coupon Code</label>
		                            <!--end::Label-->
		                            <!--begin::Col-->
		                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
		                                <input type="text" name="site_chandlery_coupon" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Coupon Code" value="{{ old('site_chandlery_coupon') }}">
		                                <div class="fv-plugins-message-container invalid-feedback"></div>
		                            </div>
		                            <!--end::Col-->
		                        </div>
								<div class="row chandlery_form hidden">
		                            <!--begin::Label-->
		                            <label class="col-lg-12 col-form-label fw-semibold fs-6">Site Url</label>
		                            <!--end::Label-->
		                            <!--begin::Col-->
		                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
		                                <input type="text" name="site_chandlery_url" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Site Url" value="{{ old('site_chandlery_url') }}">
		                                <div class="fv-plugins-message-container invalid-feedback"></div>
		                            </div>
		                            <!--end::Col-->
		                        </div>
								<div class="row chandlery_form hidden">
		                            <!--begin::Label-->
		                            <label class="col-lg-12 col-form-label fw-semibold fs-6">Url Text</label>
		                            <!--end::Label-->
		                            <!--begin::Col-->
		                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
		                                <input type="text" name="site_chandlery_text" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Url Text" value="{{ old('site_chandlery_text') }}">
		                                <div class="fv-plugins-message-container invalid-feedback"></div>
		                            </div>
		                            <!--end::Col-->
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
										<input type="file" name="site_page_images" class="mt-3" placeholder="" value="" onchange="previewImage(event,'preview')" accept="image/*">
									</div>
								</div> 

                                <div class="row" id="file_div">
		                            <label class="col-lg-12 col-form-label fw-semibold fs-6">Upload Video File</label>
		                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
		                                <input type="file" id="site_page_video" name="site_page_video" class="form-control mt-3" accept="video/*">
		                            </div>
		                        </div>
								<div class="row chandlery_form hidden">
									<label class="col-lg-12 col-form-label fw-semibold fs-6">Chandlery Logo</label>
									<div class="col-lg-12 fv-row fv-plugins-icon-container events-container">
										<div class="event-image">
										   <img src="{{ asset('assets/images/no_event.jpg') }}" id="preview">
										</div>
										<input type="file" name="site_chandlery_logo" class="mt-3" placeholder="" value="" onchange="previewImage(event,'preview')" accept="image/*">
									</div>
								</div> 
                                <input type="hidden" name="site_page_type" value="A">
						   	</div>
						</div>

	                    <div class="card-footer d-flex justify-content-end px-0 py-3">
	                    	<a href="{{ route('app-pages') }}" style="margin-right: 10px;" class="btn btn-primary">
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
<style>
        .hidden { display: none; }
</style>
<script>
	$(document).ready(function() {
		$('#site_chandlery_percentage').on('change', function() {
			let value = $(this).val();

			// Remove any existing '%' sign to avoid duplication
			value = value.replace('%', '');

			// If value is not empty, append '%' sign
			if (value !== '') {
				$(this).val(value + '%');
			}
		});

		$('#site_page_slug').on('change',function(){
			let value = $(this).val();

			if(value == 'home_chandlery')
				$('.chandlery_form').show();
			else
				$('.chandlery_form').hide();
		});

	});

</script>
@endsection