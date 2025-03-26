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
					<li class="breadcrumb-item active" aria-current="page">Edit Partner</li>
				</ol>
			</nav>
		</div>

		<!--begin::Toolbar-->
		<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
			<!--begin::Toolbar container-->
			<div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">

				<div class="event-outer w-100">
					@if(session('success'))
					<div class="alert alert-success">
						{{ session('success') }}
					</div>
					@endif
					@if ($errors->any())
					<div class="alert alert-danger">
						<ul>
							@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
							@endforeach
						</ul>
					</div>
					@endif

					<form method="POST" id="partnerForm" class="edit-page" action="{{ route('update-partner', $partner->id) }}" enctype="multipart/form-data">
						@csrf

						<h1 class="fs-2x text-dark mb-3 common-head">Edit Partner</h1>
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
														<input type="checkbox" name="partner_status" {{ $partner->partner_status === 'A' ? 'checked' : '' }}>
														<span class="slider"></span>
													</label>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<label class="col-lg-12 col-form-label required fw-semibold fs-6">Partner Name</label>
										<div class="col-lg-12 fv-row fv-plugins-icon-container">
											<input type="text" name="partner_name" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Partner Name" value="{{ $partner->partner_name }}">
											<div class="fv-plugins-message-container invalid-feedback"></div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-12">
											<label class="col-lg-12 col-form-label required fw-semibold fs-6">Discription</label>
											<div class="col-lg-12 fv-row fv-plugins-icon-container">
												<textarea name="partner_description" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0 text_editor" placeholder="Discription">{{ $partner->partner_description}}</textarea>
												<label id="partner_description-error" class="error" for="partner_description"></label>
											</div>
										</div>
									</div>
									<div class="row">
										<label class="col-lg-12 col-form-label required fw-semibold fs-6">Offer Details</label>
										<div class="col-lg-12 fv-row fv-plugins-icon-container">
											<textarea name="partner_details" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0 text_editor2" placeholder="Partner Details" value="">{{ $partner->partner_details }}</textarea>
											<div class="fv-plugins-message-container invalid-feedback"></div>
										</div>
									</div>
									<div class="row">
										<!-- <div class="col-md-6">
											<label class="col-lg-12 col-form-label fw-semibold fs-6">Coupon Code</label>
											<div class="col-lg-12 fv-row fv-plugins-icon-container">
												<input type="text" name="partner_coupon_code" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Enter Coupon Code" value="{{ $partner->partner_coupon_code}}">
												<div class="fv-plugins-message-container invalid-feedback"></div>
											</div>
										</div> -->
										<div class="col-md-6">
											<label class="col-lg-12 col-form-label fw-semibold fs-6">Discount</label>
											<div class="col-lg-12 fv-row fv-plugins-icon-container">
												<input type="text" name="partner_discount" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Enter Discount %" value="{{ $partner->partner_discount }}">
												<div class="fv-plugins-message-container invalid-feedback"></div>
											</div>
										</div>
									</div>
									<div class="row">
										<label class="col-lg-12 col-form-label fw-semibold fs-6">Website Url</label>
										<div class="col-lg-12 fv-row fv-plugins-icon-container">
											<input type="text" id="partner_web_url" name="partner_web_url" class="form-control mt-3" value="{{ $partner->partner_web_url }}">
										</div>
									</div>
									<div class="row">
										<label class="col-lg-12 col-form-label fw-semibold fs-6">Video Title</label>
										<div class="col-lg-12 fv-row fv-plugins-icon-container">
											<input type="text" id="partner_video_title" name="partner_video_title" class="form-control mt-3" value="{{ $partner->partner_video_title }}">
										</div>
									</div>
									<div class="row">
										<label class="col-lg-12 col-form-label fw-semibold fs-6">Video File Type</label>
										<div class="col-lg-12 fv-row fv-plugins-icon-container">
											<label class="form-label px-5"><input type="radio" id="file_type1" name="video_file_type" value="File" {{ $partner->video_file_type == 'File' ? 'checked' : '' }}> File</label>
											<label class="form-label px-5"><input type="radio" id="file_type2" name="video_file_type" value="Url" {{ $partner->video_file_type == 'Url' ? 'checked' : '' }}> URL</label>
											<input type="hidden" id="finalFilename" name="finalFilename" value="{{ $partner->partner_video }}">
										</div>
									</div>
									<div class="row" id="file_div" style="{{ $partner->video_file_type == 'Url' ? 'display: none;' : '' }}">
										<label class="col-lg-12 col-form-label fw-semibold fs-6">Upload Video File</label>
										<div class="col-lg-12 fv-row fv-plugins-icon-container">
											<input type="file" id="partner_video" name="partner_video" class="form-control mt-3">
										</div>
									</div>
									<div class="row" id="url_div" style="{{ $partner->video_file_type == 'File' ? 'display: none;' : '' }}">
										<label class="col-lg-12 col-form-label fw-semibold fs-6">Video File Link</label>
										<div class="col-lg-12 fv-row fv-plugins-icon-container">
											<input type="text" id="video_url" name="video_url" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="" value="{{ $partner->partner_video }}">
										</div>
									</div>


									<div class="row">
										<div class="col-md-12 mt-5">
											@if ($partner->partner_video)
											@if($partner->video_file_type == 'File')
											<video width="320" height="240" controls>
												<source src="{{ asset('storage/' . $partner->partner_video) }}" type="video/mp4">
												Your browser does not support the video tag.
											</video>
											@elseif ($partner->video_file_type == 'Url')
											<div class="col-lg-12 fv-row fv-plugins-icon-container events-container">
												<div class="event-image">
													<a href="{{ $partner->partner_video }}" target="_blank"><img src="{{ asset('assets/images/play-video.jpg') }}"></a>
												</div>
											</div>
											@endif
											<a class="m-1" href="#" style="position:absolute;" onclick="confirmDelete('{{ route('delete-video', ['id' => $partner->id]) }}')"><span class="delete m-0"><i class="fas fa-trash-alt"></i></span></a>
											@else
											<p>Video not found.</p>
											@endif
										</div>
									</div>
									<!-- <div class="row">
										<div class="col-md-12">
											<label class="col-lg-12 col-form-label required fw-semibold fs-6">Coupon Code</label>
											<div class="col-lg-12 fv-row fv-plugins-icon-container">
												<textarea name="coupon_code" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Coupon Code">{{ $couponCodesString }}</textarea>
												<label id="coupon_code-error" class="error" for="coupon_code_error"></label>
											</div>
										</div>
									</div> -->
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
													<input type="checkbox" name="is_chandlery" value="Y" @if($partner->is_chandlery=='Y') checked @endif>
													<span class="slider"></span>
												</label>
											</div>
										</div>
									</div> -->
									<div class="row">
										<label class="col-lg-12 col-form-label required fw-semibold fs-6">Partner Display Order</label>
										<div class="col-lg-6 fv-row fv-plugins-icon-container">
											<input type="number" name="partner_display_order" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Display Order" value="{{ $partner->partner_display_order }}">
											<div class="fv-plugins-message-container invalid-feedback"></div>
										</div>
									</div>
									<!-- <div class="row common-head show_chandlery">
										<div class="col-md-6">
											<label class="fw-semibold fs-6">Show Chandlery Coupon Code</label>
										</div>
										<div class="col-md-6">
											<div class="status-container">
												<label class="switch">
													<input type="checkbox" name="show_coupon_code" {{ $partner->show_coupon_code == '1' ? 'checked' : '' }}>
													<span class="slider"></span>
												</label>
											</div>
										</div>
									</div> -->



									<div class="row">
										<div class="col-lg-6 d-flex align-items-center justify-content-between">
											<label class="col-form-label fw-semibold fs-6">Partner Cover Image</label>
											@if(isset($partner->partner_cover_image))
											<a class="m-1" href="#"
												onclick="confirmDelete('{{ route('delete-images', ['id' => $partner->id, 'name' => 'partner_cover_image']) }}')">
												<span class="delete m-0"><i class="fas fa-trash-alt"></i></span>
											</a>
											@endif
										</div>
										<div class="col-lg-6 d-flex align-items-center justify-content-between">
											<label class="col-form-label fw-semibold fs-6">Mobile Image</label>
											@if(isset($partner->partner_cover_image_mob))
											<a class="m-1" href="#"
												onclick="confirmDelete('{{ route('delete-images', ['id' => $partner->id, 'name' => 'partner_cover_image_mob']) }}')">
												<span class="delete m-0"><i class="fas fa-trash-alt"></i></span>
											</a>
											@endif
										</div>
										<div class="col-lg-6 fv-row fv-plugins-icon-container events-container">
											@if(isset($partner->partner_cover_image))
											<div class="event-image">
												<img src="{{ asset('storage/' . $partner->partner_cover_image) }}" id="partner_cover_image">
											</div>
											<input type="file" name="partner_cover_image" class="mt-3" placeholder="" value="" onchange="previewImage(event, 'partner_cover_image')">
											@else
											<div class="event-image">
												<img src="{{ asset('assets/images/no_event.jpg') }}" id="partner_cover_image">
											</div>
											<input type="file" name="partner_cover_image" class="mt-3" placeholder="" value="" onchange="previewImage(event, 'partner_cover_image')">
											@endif
										</div>
										<div class="col-lg-6 fv-row fv-plugins-icon-container events-container">
											@if(isset($partner->partner_cover_image_mob))
											<div class="event-image">
												<img src="{{ asset('storage/' . $partner->partner_cover_image_mob) }}" id="partner_cover_image_mob">
											</div>
											<input type="file" name="partner_cover_image_mob" class="mt-3" placeholder="" value="" onchange="previewImage(event, 'partner_cover_image_mob')">
											@else
											<div class="event-image">
												<img src="{{ asset('assets/images/no_event.jpg') }}" id="partner_cover_image_mob">
											</div>
											<input type="file" name="partner_cover_image_mob" class="mt-3" placeholder="" value="" onchange="previewImage(event, 'partner_cover_image_mob')">
											@endif
										</div>
									</div>
									<div class="row mt-5">
										<div class="col-lg-6  d-flex align-items-center justify-content-between">
											<label class="required col-form-label fw-semibold fs-6">Partner Side Image</label>
											@if(isset($partner->partner_side_image))
											<a class="m-1" href="#"
												onclick="confirmDelete('{{ route('delete-images', ['id' => $partner->id, 'name' => 'partner_side_image']) }}')">
												<span class="delete m-0"><i class="fas fa-trash-alt"></i></span>
											</a>
											@endif
										</div>
										<div class="col-lg-6  d-flex align-items-center justify-content-between">
											<label class="required col-form-label fw-semibold fs-6">Mobile Image</label>
											@if(isset($partner->partner_side_image_mob))
											<a class="m-1" href="#"
												onclick="confirmDelete('{{ route('delete-images', ['id' => $partner->id, 'name' => 'partner_side_image_mob']) }}')">
												<span class="delete m-0"><i class="fas fa-trash-alt"></i></span>
											</a>
											@endif
										</div>
										<div class="col-lg-6 fv-row fv-plugins-icon-container events-container">
											@if(isset($partner->partner_side_image))
											<div class="event-image">
												<img src="{{ asset('storage/' . $partner->partner_side_image) }}" id="preview_side">
											</div>
											<input type="file" name="partner_side_image" class="mt-3" placeholder="" value="" onchange="previewImage(event, 'preview_side')">

											@else
											<div class="event-image">
												<img src="{{ asset('assets/images/no_event.jpg') }}" id="preview_side">
											</div>
											<input type="file" name="partner_side_image" class="mt-3" placeholder="" value="" onchange="previewImage(event, 'preview_side')">
											@endif
										</div>
										<div class="col-lg-6 fv-row fv-plugins-icon-container events-container">
											@if(isset($partner->partner_side_image_mob))
											<div class="event-image">
												<img src="{{ asset('storage/' . $partner->partner_side_image_mob) }}" id="partner_side_image_mob">
											</div>
											<input type="file" name="partner_side_image_mob" class="mt-3" placeholder="" value="" onchange="previewImage(event, 'partner_side_image_mob')">
											@else
											<div class="event-image">
												<img src="{{ asset('assets/images/no_event.jpg') }}" id="partner_side_image_mob">
											</div>
											<input type="file" name="partner_side_image_mob" class="mt-3" placeholder="" value="" onchange="previewImage(event, 'partner_side_image_mob')">
											@endif
										</div>
									</div>
									<div class="row mt-5">
										<div class="col-lg-6  d-flex align-items-center justify-content-between">
											<label class="col-form-label fw-semibold fs-6">Partner Side Video</label>
											@if(isset($partner->partner_side_video))
											<a class="m-1" href="#"
												onclick="confirmDelete('{{ route('delete-images', ['id' => $partner->id, 'name' => 'partner_side_video']) }}')">
												<span class="delete m-0"><i class="fas fa-trash-alt"></i></span>
											</a>
											@endif
										</div>
										<div class="col-lg-6  d-flex align-items-center justify-content-between">
											<label class="col-form-label fw-semibold fs-6">Mobile Video</label>
											@if(isset($partner->partner_side_video_mob))
											<a class="m-1" href="#"
												onclick="confirmDelete('{{ route('delete-images', ['id' => $partner->id, 'name' => 'partner_side_video_mob']) }}')">
												<span class="delete m-0"><i class="fas fa-trash-alt"></i></span>
											</a>
											@endif
										</div>
										<div class="col-lg-6 fv-row fv-plugins-icon-container events-container">
											<input type="file" name="partner_side_video" class="mt-3" placeholder="" value="" style="max-width:225px;">
											@if ($partner->partner_side_video)
											<video width="220" height="180" controls>
												<source src="{{ asset('storage/' . $partner->partner_side_video) }}" type="video/mp4">
												Your browser does not support the video tag.
											</video>
											@else
											<p>Video not found.</p>
											@endif
										</div>
										<div class="col-lg-6 fv-row fv-plugins-icon-container events-container">
											<input type="file" name="partner_side_video_mob" class="mt-3" placeholder="" value="" style="max-width:225px;">
											@if ($partner->partner_side_video_mob)
											<video width="220" height="180" controls>
												<source src="{{ asset('storage/' . $partner->partner_side_video_mob) }}" type="video/mp4">
												Your browser does not support the video tag.
											</video>
											@else
											<p>Video not found.</p>
											@endif
										</div>
									</div>
								</div>
								<div class="card p-5">
									<div class="row mt-5">
										<div class="col-lg-6 d-flex align-items-center justify-content-between">
											<label class="col-form-label required fw-semibold fs-6">Partner Logo</label>
											@if(isset($partner->partner_logo))
											<a class="m-1" href="#"
												onclick="confirmDelete('{{ route('delete-images', ['id' => $partner->id, 'name' => 'partner_logo']) }}')">
												<span class="delete m-0"><i class="fas fa-trash-alt"></i></span>
											</a>
											@endif
										</div>
										<div class="col-lg-6 d-flex align-items-center justify-content-between">
											<label class="col-form-label fw-semibold fs-6">Video Thumbnail</label>
											@if(isset($partner->partner_video_thumb))
											<a class="m-1" href="#"
												onclick="confirmDelete('{{ route('delete-images', ['id' => $partner->id, 'name' => 'partner_video_thumb']) }}')">
												<span class="delete m-0"><i class="fas fa-trash-alt"></i></span>
											</a>
											@endif
										</div>
										<div class="col-lg-6 fv-row fv-plugins-icon-container events-container">
											@if(isset($partner->partner_logo))
											<div class="">
												<img src="{{ asset('storage/' . $partner->partner_logo) }}" id="preview_logo" style="width: 100%;">
											</div>
											<input type="file" name="partner_logo" class="mt-3" onchange="previewImage(event, 'preview_logo')">
											@else
											<div class="event-image">
												<img src="{{ asset('assets/images/no_event.jpg') }}" id="preview_logo">
											</div>
											<input type="file" name="partner_logo" class="mt-3" onchange="previewImage(event, 'preview_logo')">
											@endif
										</div>
										<div class="col-lg-6 fv-row fv-plugins-icon-container events-container">
											@if(isset($partner->partner_video_thumb))
											<div class="event-image">
												<img src="{{ asset('storage/' . $partner->partner_video_thumb) }}" id="preview_thumb">
											</div>
											<input type="file" name="partner_video_thumb" class="mt-3" onchange="previewImage(event, 'preview_thumb')">
											@else
											<div class="event-image">
												<img src="{{ asset('assets/images/no_event.jpg') }}" id="preview_thumb">
											</div>
											<input type="file" name="partner_video_thumb" class="mt-3" onchange="previewImage(event, 'preview_thumb')">
											@endif
										</div>
									</div>
								</div>


							</div>


							<div class="card-footer d-flex justify-content-end px-0 py-3">
								<a href="{{ route('partners') }}" style="margin-right: 10px;" class="btn btn-primary">
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
	@include('common.sanitize_input')
	@include('common.preview_image')
	@include('common.text_editor')
	@include('scripts.partner')
	@include('layouts.dashboard_footer')
	@include('common.confirm_delete', ['msg' => 'Are you sure you want to delete this event?'])
</div>
<script>
	$(document).ready(function() {
		if ($("input[name='is_chandlery']").is(":checked")) {
			$(".show_chandlery").show();
		} else {
			$(".show_chandlery").hide();
		}
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