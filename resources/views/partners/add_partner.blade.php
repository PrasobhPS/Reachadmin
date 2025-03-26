@extends('layouts.app')

@section('content')

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
	<!--begin::Content wrapper-->

	<div class="d-flex flex-column flex-column-fluid">
		<div class="breadcrumb-wrapper">
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
					<li class="breadcrumb-item"><a href="{{ route('partners') }}">Partners</a></li>
					<li class="breadcrumb-item active" aria-current="page">Add Partner</li>
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

					<form method="POST" id="partnerForm" action="{{ route('save-partner') }}"
						enctype="multipart/form-data">
						@csrf

						<h1 class="fs-2x text-dark mb-3 common-head">Add Partner</h1>
						<div class="row">
							<div class="col-md-6">
								<div class="card p-5 w-100">
									<div class="align-items-center d-flex justify-content-between">
										<div>
											<h2>Partner Details</h2>
										</div>
										<div class="row">
											<div class="col-md-6">
												<label class="fw-semibold fs-6">Status</label>
											</div>
											<div class="col-md-6">
												<div class="status-container">
													<label class="switch">
														<input type="checkbox" name="partner_status">
														<span class="slider"></span>
													</label>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<label class="col-lg-12 col-form-label required fw-semibold fs-6">Partner
											Name</label>
										<div class="col-lg-12 fv-row fv-plugins-icon-container">
											<input type="text" name="partner_name"
												class="form-control form-control-lg form-control-solid mb-3 mb-lg-0"
												placeholder="Partner Name" value="{{ old('partner_name') }}">
											<div class="fv-plugins-message-container invalid-feedback"></div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-12">
											<label
												class="col-lg-12 col-form-label required fw-semibold fs-6">Discription</label>
											<div class="col-lg-12 fv-row fv-plugins-icon-container">
												<textarea id="partner_description" name="partner_description"
													class="form-control form-control-lg form-control-solid mb-3 mb-lg-0 text_editor">{{ old('partner_description') }}</textarea>
												<label id="partner_description-error" class="error"
													for="partner_description"></label>
											</div>
										</div>
									</div>
									<div class="row">
										<label class="col-lg-12 col-form-label required fw-semibold fs-6">Offer
											Details</label>
										<div class="col-lg-12 fv-row fv-plugins-icon-container">
											<textarea name="partner_details"
												class="form-control form-control-lg form-control-solid mb-3 mb-lg-0 text_editor2">{{ old('partner_details') }}</textarea>
											<label id="partner_details-error" class="error"
												for="partner_details"></label>
										</div>
									</div>
									<div class="row">
										<!-- <div class="col-md-6">
											<label class="col-lg-12 col-form-label fw-semibold fs-6">Coupon Code</label>
											<div class="col-lg-12 fv-row fv-plugins-icon-container">
												<input type="text" name="partner_coupon_code"
													class="form-control form-control-lg form-control-solid mb-3 mb-lg-0"
													placeholder="Enter Coupon Code" value="">
												<div class="fv-plugins-message-container invalid-feedback"></div>
											</div>
										</div> -->
										<div class="col-md-6">
											<label class="col-lg-12 col-form-label fw-semibold fs-6">Discount</label>
											<div class="col-lg-12 fv-row fv-plugins-icon-container">
												<input type="text" name="partner_discount"
													class="form-control form-control-lg form-control-solid mb-3 mb-lg-0"
													placeholder="Enter Discount %" value="">
												<div class="fv-plugins-message-container invalid-feedback"></div>
											</div>
										</div>
									</div>
									<div class="row">
										<label class="col-lg-12 col-form-label fw-semibold fs-6">Website Url</label>
										<div class="col-lg-12 fv-row fv-plugins-icon-container">
											<input type="text" id="partner_web_url" name="partner_web_url"
												class="form-control mt-3" value="{{ old('partner_web_url') }}">
										</div>
									</div>
									<div class="row">
										<label class="col-lg-12 col-form-label fw-semibold fs-6">Video Title</label>
										<div class="col-lg-12 fv-row fv-plugins-icon-container">
											<input type="text" id="partner_video_title" name="partner_video_title"
												class="form-control mt-3" value="{{ old('partner_video_title') }}">
										</div>
									</div>
									<div class="row">
										<label class="col-lg-12 col-form-label fw-semibold fs-6">Video File Type</label>
										<div class="col-lg-12 fv-row fv-plugins-icon-container">
											<label class="form-label px-5"><input type="radio" id="file_type1"
													name="video_file_type" value="File"> File</label>
											<label class="form-label px-5"><input type="radio" id="file_type2"
													name="video_file_type" value="Url"> URL</label>
											<input type="hidden" id="finalFilename" name="finalFilename" value=""
												checked>
										</div>
									</div>
									<div class="row" id="file_div">
										<label class="col-lg-12 col-form-label fw-semibold fs-6">Upload Video
											File</label>
										<div class="col-lg-12 fv-row fv-plugins-icon-container">
											<input type="file" id="partner_video" name="partner_video"
												class="form-control mt-3">
										</div>
									</div>
									<div class="row" id="url_div" style="display: block;">
										<label class="col-lg-12 col-form-label fw-semibold fs-6">Video File Link</label>
										<div class="col-lg-12 fv-row fv-plugins-icon-container">
											<input type="text" id="video_url" name="video_url"
												class="form-control form-control-lg form-control-solid mb-3 mb-lg-0"
												placeholder="" value="{{ old('video_url') }}">
										</div>
									</div>

								</div>
							</div>
							<div class="col-md-6">
								<div class="card p-5">
									<!-- <div class="row mt-3 common-head">
										<div class="col-md-6">
											<label class="fw-semibold fs-6">Add Partner As Chandlery</label>
										</div>
										<div class="col-md-6">
											<div class="status-container">
												<label class="switch">
													<input type="checkbox" name="is_chandlery" value="Y">
													<span class="slider"></span>
												</label>
											</div>
										</div>
									</div> -->

									<div class="row">
										<label class="col-lg-12 col-form-label required fw-semibold fs-6">Partner
											Display Order</label>
										<div class="col-lg-6 fv-row fv-plugins-icon-container">
											<input type="number" name="partner_display_order"
												class="form-control form-control-lg form-control-solid mb-3 mb-lg-0"
												placeholder="Display Order" min="0"
												value="{{ old('partner_display_order') }}">
										</div>
									</div>
									<!-- <div class="row  mt-3 common-head show_chandlery">
										<div class="col-md-6">
											<label class="fw-semibold fs-6">Show Chandlery Coupon Code</label>
										</div>
										<div class="col-md-6">
											<div class="status-container">
												<label class="switch">
													<input type="checkbox" name="show_coupon_code">
													<span class="slider"></span>
												</label>
											</div>
										</div>
									</div> -->
									<div class="row">
										<label class="col-lg-6 col-form-label fw-semibold fs-6">Partner Cover
											Image</label>
										<label class="col-lg-6 col-form-label fw-semibold fs-6">Mobile Image</label>

										<div class="col-lg-6 fv-row fv-plugins-icon-container events-container">
											<div class="event-image">
												<img src="{{ asset('assets/images/no_event.jpg') }}"
													id="partner_cover_image">
											</div>
											<input type="file" name="partner_cover_image" class="mt-3"
												onchange="previewImage(event,'partner_cover_image')">
										</div>
										<div class="col-lg-6 fv-row fv-plugins-icon-container events-container">
											<div class="event-image">
												<img src="{{ asset('assets/images/no_event.jpg') }}"
													id="partner_cover_image_mob">
											</div>
											<input type="file" name="partner_cover_image_mob" class="mt-3"
												onchange="previewImage(event,'partner_cover_image_mob')">
										</div>
									</div>
									<div class="row mt-5">
										<label class="col-lg-6 col-form-label required fw-semibold fs-6">Partner Side
											Image</label>
										<label class="col-lg-6 col-form-label fw-semibold fs-6">Mobile Image</label>

										<div class="col-lg-6 fv-row fv-plugins-icon-container events-container">
											<div class="event-image">
												<img src="{{ asset('assets/images/no_event.jpg') }}" id="preview_side">
											</div>
											<input type="file" name="partner_side_image" class="mt-3"
												onchange="previewImage(event,'preview_side')">
										</div>

										<div class="col-lg-6 fv-row fv-plugins-icon-container events-container">
											<div class="event-image">
												<img src="{{ asset('assets/images/no_event.jpg') }}"
													id="partner_side_image_mob">
											</div>
											<input type="file" name="partner_side_image_mob" class="mt-3"
												onchange="previewImage(event,'partner_side_image_mob')">
										</div>
									</div>
									<div class="row mt-5">
										<label class="col-lg-6 col-form-label fw-semibold fs-6">Partner Side
											Video</label>
										<label class="col-lg-6 col-form-label fw-semibold fs-6">Mobile Video</label>

										<div class="col-lg-6 fv-row fv-plugins-icon-container events-container">
											<input type="file" name="partner_side_video" class="mt-3"
												style="max-width:225px;">
										</div>

										<div class="col-lg-6 fv-row fv-plugins-icon-container events-container">
											<input type="file" name="partner_side_video_mob" class="mt-3"
												style="max-width:225px;">
										</div>
									</div>
								</div>
								<div class="card p-5">
									<div class="row">
										<div class="col-lg-6 fv-row fv-plugins-icon-container events-container">
											<label class="col-lg-6 col-form-label required fw-semibold fs-6">Partner
												Logo</label>
											<div class="event-image">
												<img src="{{ asset('assets/images/no_event.jpg') }}" id="preview_logo">
											</div>
											<input type="file" name="partner_logo" class="mt-3"
												onchange="previewImage(event,'preview_logo')">
										</div>
										<div class="col-lg-6 fv-row fv-plugins-icon-container events-container">
											<label class="col-lg-6 col-form-label fw-semibold fs-6">Video
												Thumbnail</label>
											<div class="event-image">
												<img src="{{ asset('assets/images/no_event.jpg') }}" id="preview_thumb">
											</div>
											<input type="file" name="partner_video_thumb" class="mt-3"
												onchange="previewImage(event,'preview_thumb')">
										</div>
									</div>
								</div>

							</div>


							<div class="card-footer d-flex justify-content-end px-0 py-3">
								<a href="{{ route('partners') }}" style="margin-right: 10px;" class="btn btn-primary">
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
	@include('common.sanitize_input')
	@include('common.preview_image')
	@include('common.text_editor')
	@include('scripts.partner')
	@include('layouts.dashboard_footer')
</div>
<script>
	$(document).ready(function() {
		$(".show_chandlery").hide();
		// Using .change() event to detect changes in the checkbox
		$("input[name='is_chandlery']").change(function() {
			if ($(this).is(":checked")) {
				$(".show_chandlery").show();
			} else {
				$(".show_chandlery").hide();
			}
		});
	});
</script>
@endsection