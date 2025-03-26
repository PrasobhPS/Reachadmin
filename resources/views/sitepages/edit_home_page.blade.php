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
                    <form method="POST" action="{{ route('update-home-page-cms', $sitepage->id) }}" enctype="multipart/form-data">
                        @csrf
                        <h1 class="fs-2x text-dark mb-3 common-head">Edit Home Page CMS</h1>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card p-5 w-100">
                                    <div class="align-items-center d-flex justify-content-between">
                                        <div>
                                            <h2>Home Page Details</h2>
                                        </div>
                                    </div>
                                   
                                    <div class="row">
                                        <label class="col-lg-12 col-form-label required fw-semibold fs-6">Home Page Header</label>
                                        <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                            <input type="text" name="home_page_section_header" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Header Name" value="{{ $sitepage->home_page_section_header }}">
                                            <div class="fv-plugins-message-container invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-lg-12 col-form-label fw-semibold fs-6">Home Page Type</label>
                                        <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                            <select name="home_page_section_type" class="required form-select form-select-lg form-select-solid fw-semibold">
                                                <option value="">Select</option>
                                                <option value="F" {{ $sitepage->home_page_section_type == 'F' ? 'selected' : '' }}>Full Width</option>
                                                <option value="H" {{ $sitepage->home_page_section_type == 'H' ? 'selected' : '' }}>Half Width</option>
                                            </select>
                                            <div class="fv-plugins-message-container invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-lg-12 col-form-label fw-semibold fs-6">Home Page Details</label>
                                        <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                            <textarea name="home_page_section_details" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0 text_editor" placeholder="Page Details" value="">{{ strip_tags($sitepage->home_page_section_details) }}</textarea>
                                            <div class="fv-plugins-message-container invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-lg-12 col-form-label fw-semibold fs-6">Home Page Button</label>
                                        <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                            <input type="text" name="home_page_section_button" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Button Name" value="{{ $sitepage->home_page_section_button }}">
                                            <div class="fv-plugins-message-container invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-lg-12 col-form-label fw-semibold fs-6">Home Page Button Link</label>
                                        <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                            <input type="text" readonly name="home_page_section_button_link" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="HButton Link" value="{{ $sitepage->home_page_section_button_link }}">
                                            <div class="fv-plugins-message-container invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="row">
										<label class="col-lg-12 col-form-label fw-semibold fs-6">Display Order</label>
										<div class="col-lg-12 fv-row fv-plugins-icon-container">
											<input type="number" name="order" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Display Order" value="{{ $sitepage->order }}">
											<div class="fv-plugins-message-container invalid-feedback"></div>
										</div>
									</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card p-5">
                                    <div class="row">
                                        <label class="col-lg-12 col-form-label fw-semibold fs-6">Header Image</label>
                                        @if(isset($sitepage->home_page_section_images))
                                        <div class="col-lg-12 fv-row fv-plugins-icon-container events-container">
                                            <div class="event-image">
                                                <img src="{{ asset('storage/' . $sitepage->home_page_section_images) }}" id="preview">
                                            </div>
                                            <input type="file" name="site_page_images" class="mt-3" placeholder="" value="" onchange="previewImage(event, 'preview')" accept="image/*">
                                        </div>
                                        @else
                                        <div class="col-lg-12 fv-row fv-plugins-icon-container events-container">
                                            <div class="event-image">
                                                <img src="{{ asset('assets/images/no_event.jpg') }}" id="preview">
                                            </div>
                                            <input type="file" name="site_page_images" class="mt-3" placeholder="" value="" onchange="previewImage(event, 'preview')" accept="image/*">
                                        </div>
                                        @endif
                                    </div>
                                    <div class="row">
                                    <div class="col-md-12 mt-5">
									
                                        @if(isset($sitepage->home_page_video)&&($sitepage->home_page_video))	
										
                                            <video width="320" height="240" controls>
                                                <source src="{{ asset('storage/' . $sitepage->home_page_video) }}" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>  
											<a class="m-1" href="#" style="position:absolute;" onclick="confirmDelete('{{ route('delete-home-video', ['id' => $sitepage->id]) }}')"><span class="delete m-0" style="font-size:25px;">&times;</span></a>
                                        @endif
										<div class="row" id="file_div">
											<label class="col-lg-12 col-form-label fw-semibold fs-6">Upload Video File</label>
											<div class="col-lg-12 fv-row fv-plugins-icon-container">
												<input type="file" id="home_page_video" name="home_page_video" class="form-control mt-3" accept="video/*">
											</div>
										</div>
										<input type="hidden" name="site_page_type" value="A">
                                       
                                    </div>
                                </div>
                                </div>
                            </div>
                            <div class="card-footer d-flex justify-content-end px-0 py-3">
                                <a href="{{ route('home-page') }}" style="margin-right: 10px;" class="btn btn-primary">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @include('common.preview_image')
    @include('common.text_editor')
    @include('layouts.dashboard_footer')
    @include('common.confirm_delete', ['msg' => 'Are you sure you want to delete this event?'])
</div>
@endsection