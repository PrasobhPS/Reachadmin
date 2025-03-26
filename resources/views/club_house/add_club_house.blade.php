@extends('layouts.app') @section('content')

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->

    <div class="d-flex flex-column flex-column-fluid">
        <div class="breadcrumb-wrapper">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('club-house') }}">Club House</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add Club House</li>
                </ol>
            </nav>
        </div>

        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <!--begin::Toolbar container-->
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">

                <div class="outer-data  w-100">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form method="POST" id="clubhouseForm" action="{{ route('save-club-house') }}" enctype="multipart/form-data">
                        @csrf

                        <h1 class="fs-2x text-dark mb-3 common-head">Add Club House</h1>

                        <div class="add-details  pt-3 common-form-container">

                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <div class="card w-100 p-5">
                                        <h2>Cover Image</h2>

                                        <div class="row mb-5">
                                            <div class="col-md-12 events-container">
                                                <div class="event-image">
                                                    <img src="{{ asset('assets/images/no_event.jpg') }}" alt="Cover Image" class="thumbnail photo" id="preview">
                                                </div>
                                                <div class="buton-area mt-3">
                                                    <input type="file" name="club_image" class="w-100" placeholder="" value="" onchange="previewImage(event,'preview')">
                                                </div>
                                            </div>
                                        </div>
                                        <h2>Cover Image For Mobile</h2>
                                        <div class="row mb-10">
                                            <div class="col-md-12 events-container">
                                                <div class="event-image">
                                                    <img src="{{ asset('assets/images/no_event.jpg') }}" alt="Cover Image" class="thumbnail photo" id="preview_mob">
                                                </div>
                                                <div class="buton-area mt-3">
                                                    <input type="file" name="club_image_mob" class="w-100" placeholder="" value="" onchange="previewImage(event,'preview_mob')">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label class="fw-semibold fs-6">Status</label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="status-container">
                                                            <label class="switch">
                                                                <input type="checkbox" name="club_status" checked>
                                                                <span class="slider"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="col-lg-12 col-form-label required fw-semibold fs-6">Club House Name</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="text" name="club_name" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Enter Club House Name" value="">
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="col-lg-12 col-form-label fw-semibold fs-6">Discription</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <textarea id="club_short_desc" name="club_short_desc" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0 text_editor" placeholder="Discription"></textarea>
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                    <label id="club_short_desc-error" class="error" for="club_short_desc"></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="col-lg-12 col-form-label fw-semibold fs-6">Button Name</label>
                                                <div class="col-lg-6 fv-row fv-plugins-icon-container">
                                                    <input type="text" name="club_button_name" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Enter Button Name" value="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label class="col-lg-12 col-form-label required fw-semibold fs-6">Display Order</label>
                                            <div class="col-lg-6 fv-row fv-plugins-icon-container">
                                                <input type="number" name="club_order" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Display Order" min="0" value="">
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                </div>

                            </div>


                        </div>
                        <div class="card-footer d-flex justify-content-end px-0 py-3">
                            <a href="{{ route('club-house') }}" style="margin-right: 10px;" class="btn btn-primary">
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
    @include('common.text_editor')
    @include('layouts.dashboard_footer')
    @include('common.preview_image')
    @include('scripts.clubhouse')
</div>

@endsection