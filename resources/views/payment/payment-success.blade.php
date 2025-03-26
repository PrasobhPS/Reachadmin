@extends('layouts.app') @section('content')

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->

    <div class="d-flex flex-column flex-column-fluid">

        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <!--begin::Toolbar container-->
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">

                <div class="outer-data  w-100">

                    <div class="container mt-5">
                        <div class="alert alert-success">
                            <strong>Success!</strong> Your payment was successful.
                        </div>
                    </div>

                </div>
            </div>
            <!--end::Toolbar container-->
        </div>
    </div>
    <!--end::Content wrapper-->

    @include('layouts.dashboard_footer')
</div>

@endsection