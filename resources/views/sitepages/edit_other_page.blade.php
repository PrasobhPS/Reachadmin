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
	    
                	<form method="POST" action="{{ route('update-other-page') }}" enctype="multipart/form-data">
                        @csrf

	                    <h1 class="fs-2x text-dark mb-3 common-head">Edit CRUZ Page</h1>
						<div class="row">
							@php $currentStep = null; @endphp
					      	@foreach($sitepage as $index => $page)
								@if($currentStep != $page->page_step)
							        @if($currentStep != null)
							                </div> <!-- Close previous card -->
							            </div> <!-- Close previous column -->
							        @endif

							    @php $currentStep = $page->page_step; @endphp
								<div class="col-md-6 mb-3">
								    <div class="card p-5 w-100">
								        <div class="align-items-center d-flex justify-content-between">
								            <div>
								                <h2>Step {{ $page->page_step }}: {{ $page->page_step_title }} </h2>
								            </div>
								        </div>
							    @endif

							    	<input type="hidden" name="page_id[]" value="{{ $page->id }}">
							        <div class="row">
							            <label class="col-lg-12 col-form-label required fw-semibold fs-6">Title</label>
							            <div class="col-lg-12 fv-row fv-plugins-icon-container">
							                <input type="text" name="page_title[]" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Enter Title" value="{{ $page->page_title }}">
							            </div>
							        </div>
							        <div class="row">
							            <label class="col-lg-12 col-form-label fw-semibold fs-6">Description</label>
							            <div class="col-lg-12 fv-row fv-plugins-icon-container">
							                <input type="text" name="page_desc[]" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Enter Description" value="{{ $page->page_desc }}">
							            </div>
							        </div>
							@endforeach
							@if($currentStep != null)
							        </div> <!-- Close last card -->
							    </div> <!-- Close last column -->
							@endif

		                    <div class="card-footer d-flex justify-content-end px-0 py-3">
		                    	<a href="{{ route('other-pages') }}" style="margin-right: 10px;" class="btn btn-primary">Cancel</a> 
		                        <button type="submit" class="btn btn-primary">Update</button>
		                    </div>
	                   
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