@extends('layouts.app') 

@section('content')

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->

    <div class="d-flex flex-column flex-column-fluid">

        <div class="breadcrumb-wrapper">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('templates') }}">Templates</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add Templates</li>
                </ol>
            </nav>
        </div>
      
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

                    <form method="POST" id="templateForm" action="{{ route('save-template') }}"  enctype="multipart/form-data">
                        @csrf

                        <h1 class="fs-2x text-dark mb-3 common-head">Add Template</h1>
                        <input type="hidden" name="template_id" value="">

                        <div class="add-details  pt-3 common-form-container">

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="card w-100 p-5">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="col-lg-12 col-form-label required fw-semibold fs-6">Template Name</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="text" name="template_title" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" value="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="col-lg-12 col-form-label fw-semibold fs-6">Type</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="text" name="template_type" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" value="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="col-lg-12 col-form-label required fw-semibold fs-6">Subject</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="text" name="template_subject" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" value="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="col-lg-12 col-form-label required fw-semibold fs-6">Tags</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="text" name="template_tags" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" value="">
                                                    <small>Tag separated by a comma</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="col-lg-12 col-form-label required fw-semibold fs-6">Message</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <textarea name="template_message" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0 text_editor" placeholder="Message"></textarea>
                                                    <label id="template_message-error" class="error" for="template_message"></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="col-lg-12 col-form-label required fw-semibold fs-6">Email Send To</label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <select id="template_to_status" name="template_to_status" class="form-select">
                                                        <option value="U" selected>Member</option>
                                                        <option value="A">Admin</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="to_address" class="row" style="display:none;">
                                            <div class="col-md-12">
                                                <label class="col-lg-12 col-form-label required fw-semibold fs-6">Add To </label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="text" name="template_to_address" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" value="" placeholder="To Email Address">
                                                    <small>Email separated by a comma</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="col-lg-12 col-form-label fw-semibold fs-6">Add CC </label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="text" name="template_cc_address" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" value="" placeholder="CC Email Address">
                                                    <small>Email separated by a comma</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="col-lg-12 col-form-label fw-semibold fs-6">Add BCC </label>
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="text" name="template_bcc_address" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" value="" placeholder="BCC Email Address">
                                                    <small>Email separated by a comma</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-4">
                                            <div class="col-md-6">
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label class="fw-semibold fs-6">Status</label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="status-container">
                                                            <label class="switch">
                                                                <input type="checkbox" name="template_status">
                                                                <span class="slider"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                </div>

                            </div>

                        </div>
                        <div class="card-footer d-flex justify-content-end px-0 py-3">
                            <a href="{{ route('templates') }}" style="margin-right: 10px;" class="btn btn-primary">
                                Cancel
                            </a> 
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>

                    </form>

                </div>
            </div>
            <!--end::Toolbar container-->
        </div>
    </div>
    <!--end::Content wrapper-->

    @include('common.text_editor')
    @include('layouts.dashboard_footer')
    @include('scripts.templates')
</div>

@endsection