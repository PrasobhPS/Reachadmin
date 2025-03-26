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

                    <form method="POST" action="{{ route('update-membership-page', $membership->id) }}" enctype="multipart/form-data">
                        @csrf


                        <h1 class="fs-2x text-dark mb-3 common-head">Edit Membership Page</h1>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card p-5 w-100">
                                    <div class="align-items-center d-flex justify-content-between">
                                        <div>
                                            <h2>Page Details</h2>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-lg-12 col-form-label required fw-semibold fs-6">Membership Title</label>
                                        <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                            <input type="text" name="membership_title" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Membership Title" value="{{ $membership->membership_title }}">
                                            <div class="fv-plugins-message-container invalid-feedback"></div>
                                        </div>
                                    </div>


                                    <div class="row">

                                        <label class="col-lg-12 col-form-label required fw-semibold fs-6">Membership Description</label>
                                        <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                            <textarea name="membership_description" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0 text_editor" placeholder="Membership Description">{{ $membership->membership_description }}</textarea>
                                            <div class="fv-plugins-message-container invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-lg-12 col-form-label required fw-semibold fs-6">Button Name</label>
                                        <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                            <input type="text" name="membership_button" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Button Name" value="{{ $membership->membership_button }}">
                                            <div class="fv-plugins-message-container invalid-feedback"></div>
                                        </div>
                                    </div>

                                </div>
                            </div>



                            <div class="card-footer d-flex justify-content-end px-0 py-3">
                                <a href="{{ route('reach-membership-page') }}" style="margin-right: 10px;" class="btn btn-primary">
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
    @include('common.datepicker', ['id' => 'startdate-datepicker'])
    @include('common.datepicker', ['id' => 'enddate-datepicker','maxDate' => '+1y'])
    @include('common.preview_image')
    @include('common.text_editor')
    @include('layouts.dashboard_footer')
</div>
<script>
    $(document).ready(function() {
        $('.text_editor2').richText();
    });
</script>
@endsection