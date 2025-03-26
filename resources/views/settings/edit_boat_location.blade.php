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
					<form method="POST" action="{{ route('update-boat-location', $boat_location->id) }}" enctype="multipart/form-data">
						@csrf

						<h1 class="fs-2x text-dark mb-3 common-head">Update Boat Location</h1>
						<div class="row">
							<div class="col-md-6">
								<div class="card p-5 w-100">
									<div class="align-items-center d-flex justify-content-between">
										<div>
											<h2>Boat Location</h2>
										</div>
										<div class="row">
											<div class="col-md-6">
												<label class="fw-semibold fs-6">Status</label>
											</div>
											<div class="col-md-6">
												<div class="status-container">
													@if (in_array($boat_location->id, $boat_location_id) && $boat_location->boat_location_status === 'A' )
													<label class="switch" data-bs-toggle="tooltip" data-bs-placement="bottom" title="This boat location is currently in use and cannot be changed.">

														<input type="checkbox" name="job_role_status"
															{{ $boat_location->boat_location_status === 'A' ? 'checked' : '' }}
															disabled>
														<input type="hidden" name="boat_location_status" value="{{ $boat_location->boat_location_status }}">
														<span class="slider"></span>
													</label>
													@else
													<label class="switch">
														<input type="checkbox" name="boat_location_status" {{ $boat_location->boat_location_status === 'A' ? 'checked' : '' }}>
														<span class="slider"></span>
													</label>
													@endif
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<!--begin::Label-->
										<label class="col-lg-12 col-form-label required fw-semibold fs-6">Location</label>
										<!--end::Label-->
										<!--begin::Col-->
										<div class="col-lg-12 fv-row fv-plugins-icon-container">
											<input type="text" name="boat_location" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Boat Type" value="{{ $boat_location->boat_location }}">
											<div class="fv-plugins-message-container invalid-feedback"></div>
										</div>
										<!--end::Col-->
									</div>

								</div>
							</div>
						</div>


						<div class="card-footer d-flex justify-content-end px-0 py-3">
							<a href="{{ route('boat-location') }}" style="margin-right: 10px;" class="btn btn-primary">
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
	@include('common.common_functions')
	@include('layouts.dashboard_footer')
</div>

@endsection