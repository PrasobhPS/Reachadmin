@extends('layouts.app') @section('content')

    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <!--begin::Content wrapper-->

        <div class="d-flex flex-column flex-column-fluid">
            <div class="breadcrumb-wrapper">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('chandlery') }}">Chandlery</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Add Chandlery</li>
                    </ol>
                </nav>
            </div>

            <!--begin::Toolbar-->
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <!--begin::Toolbar container-->
                <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">


                    <div class="outer-data  w-100">
                        @if ($errors->any())
                            <div style="color:red">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" id="chandleryForm" action="{{ route('save-chandlery') }}"
                            enctype="multipart/form-data">
                            @csrf

                            <h1 class="fs-2x text-dark mb-3 common-head">Add Chandlery</h1>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card w-100 p-5">
                                        <div class="align-items-center d-flex justify-content-between">
                                            <div>
                                                <h2>Chandlery Details</h2>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="fw-semibold fs-6">Status</label>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="status-container">
                                                        <label class="switch">
                                                            <input type="checkbox" name="chandlery_status">
                                                            <span class="slider"></span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="col-lg-12 col-form-label required fw-semibold fs-6">Chandlery
                                                    Name</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="text" name="chandlery_name"
                                                        class="form-control form-control-lg form-control-solid mb-3 mb-lg-0"
                                                        placeholder="Enter Chandlery Name" value="">
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <label
                                                    class="col-lg-12 col-form-label required fw-semibold fs-6">Discription</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <textarea id="chandlery_description" name="chandlery_description"
                                                        class="form-control form-control-lg form-control-solid mb-3 mb-lg-0 text_editor"
                                                        placeholder="Discription"></textarea>
                                                    <label id="chandlery_description-error" class="error"
                                                        for="chandlery_description"></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">

                                            <div class="col-md-12">
                                                <label
                                                    class="col-lg-12 col-form-label required fw-semibold fs-6">Discount</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="text" name="chandlery_discount"
                                                        class="form-control form-control-lg form-control-solid mb-3 mb-lg-0"
                                                        placeholder="Enter Discount %" value="">
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="col-lg-12 col-form-label required fw-semibold fs-6">Website
                                                    Url</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="text" name="chandlery_website"
                                                        class="form-control form-control-lg form-control-solid mb-3 mb-lg-0"
                                                        placeholder="Website Url" value="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label class="col-lg-12 col-form-label required fw-semibold fs-6">Display
                                                Order</label>
                                            <div class="col-lg-6 fv-row fv-plugins-icon-container">
                                                <input type="number" name="chandlery_order"
                                                    class="form-control form-control-lg form-control-solid mb-3 mb-lg-0"
                                                    placeholder="Display Order" min="0" value="">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="col-lg-12 col-form-label required fw-semibold fs-6">Coupon
                                                    Code</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <textarea name="coupon_code"
                                                        class="form-control form-control-lg form-control-solid mb-3 mb-lg-0"
                                                        placeholder="Coupon Code"></textarea>
                                                    <label id="coupon_code-error" class="error"
                                                        for="coupon_code_error"></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row common-head show_chandlery">
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
                                        </div>


                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card w-100 p-5">
                                        <div class="row">
                                            <label class="col-lg-12 col-form-label fw-semibold fs-6">Cover Image</label>
                                            <div class="col-lg-12 fv-row fv-plugins-icon-container events-container">
                                                <div class="event-image">
                                                    <img src="{{ asset('assets/images/no_event.jpg') }}" id="preview">
                                                </div>
                                                <input type="file" name="chandlery_image" class="mt-3" placeholder=""
                                                    value="" onchange="previewImage(event,'preview')">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="card p-5">
                                                <label class="col-form-label fw-semibold fs-6"> Logo</label>

                                                <div class="col-lg-6 fv-row fv-plugins-icon-container events-container">
                                                    <div class="event-image">
                                                        <img src="{{ asset('assets/images/no_event.jpg') }}"
                                                            id="preview_logo">
                                                    </div>
                                                    <input type="file" name="chandlery_logo" class="mt-3"
                                                        onchange="previewImage(event,'preview_logo')">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="card-footer d-flex justify-content-end px-0 py-3">
                                <a href="{{ route('chandlery') }}" style="margin-right: 10px;" class="btn btn-primary">
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
        @include('scripts.chandlery')
    </div>

@endsection