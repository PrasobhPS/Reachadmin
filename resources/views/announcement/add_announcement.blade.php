@extends('layouts.app')

@section('content')

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
	<!--begin::Content wrapper-->

	<div class="d-flex flex-column flex-column-fluid">
		@if ($errors->any())
		<div class="alert alert-danger">
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
					<form method="POST" action="{{ route('save_announcement') }}" enctype="multipart/form-data">
						@csrf

						<h1 class="fs-2x text-dark mb-3 common-head">Add Announcement</h1>
						<div class="row">
							<div class="col-md-6">
								<div class="card p-5 w-100">

									<div class="row">

										<!--begin::Col-->
										<div class="col-md-12">
											<label class="col-lg-12 col-form-label required fw-semibold fs-6">Member Type</label>
											<div class="col-lg-12 fv-row fv-plugins-icon-container">
												<select id="members_type" name="members_type" aria-label="Select a Type" data-control="select2" data-placeholder="Select a Type..." class="form-select form-select-solid form-select-lg fw-semibold">
													<option value="All">All</option>
													<option value="F">Free Member</option>
													<option value="M">Full Member</option>

												</select>
											</div>
										</div>
										<!--end::Col-->
									</div>


									<div class="row">
										<!--begin::Label-->
										<label class="col-lg-12 col-form-label required fw-semibold fs-6">Title</label>
										<!--end::Label-->
										<!--begin::Col-->
										<div class="col-lg-12 fv-row fv-plugins-icon-container">
											<input type="text" name="title" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Title" value="{{ old('title') }}">
											<div class="fv-plugins-message-container invalid-feedback"></div>
										</div>
										<!--end::Col-->
									</div>
									<div class="row">
										<label class="col-lg-12 col-form-label required fw-semibold fs-6">Message</label>

										<textarea name="message" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Message"></textarea>

									</div>
									<!--end::Col-->
								</div>

							</div>
						</div>

						<div class="card-footer d-flex justify-content-end px-0 py-3">
							<a href="{{ route('announcement_list') }}" style="margin-right: 10px;" class="btn btn-primary">
								Cancel
							</a>
							<button type="submit" class="btn btn-primary">Submit</button>
						</div>
				</div>



				</form>

			</div>
		</div>
		<!--end::Toolbar container-->
	</div>
</div>
<!--end::Content wrapper-->


@include('layouts.dashboard_footer')
</div>

@endsection