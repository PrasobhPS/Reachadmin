@extends('layouts.app')
@section('content')
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
	<div class="d-flex flex-column flex-column-fluid">
		<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
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

					<form method="POST" action="{{ route('save-home-page') }}" enctype="multipart/form-data">
						@csrf

						<h1 class="fs-2x text-dark mb-3 common-head">Add Home Page</h1>
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
										<label class="col-lg-12 col-form-label required fw-semibold fs-6">Home Page Header</label>
										<!--end::Label-->
										<!--begin::Col-->
										<div class="col-lg-12 fv-row fv-plugins-icon-container">
											<input type="text" name="home_page_section_header" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Home Page Header Name" value="{{ old('home_page_section_header') }}">
											<div class="fv-plugins-message-container invalid-feedback"></div>
										</div>
										<!--end::Col-->
									</div>
									<div class="row">
										<!--begin::Label-->
										<label class="col-lg-12 col-form-label required fw-semibold fs-6">Home Page Type</label>
										<!--end::Label-->
										<!--begin::Col-->
										<div class="col-lg-12 fv-row fv-plugins-icon-container">
											<select name="home_page_section_type" class="required form-select form-select-lg form-select-solid fw-semibold">
												<option value="">Select</option>
												<option value="F">Full Width</option>
												<option value="H">Half Width</option>
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
											<textarea name="home_page_section_details" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Home Page Details" value="">{{ old('home_page_section_details') }}</textarea>
											<div class="fv-plugins-message-container invalid-feedback"></div>
										</div>
									</div>
									<div class="row">
										<label class="col-lg-12 col-form-label fw-semibold fs-6">Home Page Button</label>
										<div class="col-lg-12 fv-row fv-plugins-icon-container">
											<input type="text" name="home_page_section_button" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Button Name" value="{{old('home_page_section_button') }}">
											<div class="fv-plugins-message-container invalid-feedback"></div>
										</div>
									</div>
									<div class="row">
										<label class="col-lg-12 col-form-label fw-semibold fs-6">Home Page Button Link</label>
										<div class="col-lg-12 fv-row fv-plugins-icon-container">
											<input type="text" name="home_page_section_button_link" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Button Link" value="{{old('home_page_section_button_link') }}">
											<div class="fv-plugins-message-container invalid-feedback"></div>
										</div>
									</div>
									<div class="row">
										<label class="col-lg-12 col-form-label fw-semibold fs-6">Display Order</label>
										<div class="col-lg-12 fv-row fv-plugins-icon-container">
											<input type="number" name="order" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Display Order" value="{{old('order') }}">
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
											<input type="file" name="site_page_images" class="mt-3" placeholder="" value="" onchange="previewImage(event,'preview')" accept="image/*">
										</div>
									</div>




								</div>
							</div>

							<div class="card-footer d-flex justify-content-end px-0 py-3">
								<a href="{{ route('home-page') }}" style="margin-right: 10px;" class="btn btn-primary">
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
	.hidden {
		display: none;
	}
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

		$('#site_page_slug').on('change', function() {
			let value = $(this).val();

			if (value == 'home_chandlery')
				$('.chandlery_form').show();
			else
				$('.chandlery_form').hide();
		});

	});
</script>
@endsection