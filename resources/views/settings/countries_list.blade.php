@extends('layouts.app')

@section('content')
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid">
        
        <div class="breadcrumb-wrapper">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Settings</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Countries</li>
                </ol>
            </nav>
        </div>

        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <!--begin::Toolbar container-->
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">

                <div class="card w-100 p-5">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
    
                    <div class="d-flex align-items-center justify-content-between  mb-6 common-head">
                        <h1 class="fs-2x text-dark mb-0">Countries List</h1>
                    </div>
                    <div class="members-list common-table-block">

                    <form action="{{ route('update-countries') }}" method="POST">
                            @csrf
                        <label for="select_all" class="form-label"> <input type="checkbox" name="select_all" id="select_all" > Select All </label>


                        <div class="common-table-container table-responsive mt-5">
                            @if($countries->isNotEmpty())
                                <div class="row">
                                    @foreach($countries as $index => $value)
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label"><input type="checkbox" class="select_country" name="country_id[]" value="{{ $value->id }}" @if(in_array($value->id, $selectedCountries)) checked @endif>&nbsp; {{ $value->country_name }} </label>
                                        </div>
                                        @if(($index + 1) % 4 == 0)
                                            </div><div class="row">
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <p>No countries found.</p>
                            @endif

                            <div class="card-footer d-flex justify-content-end px-0 py-3">
                                <button type="submit" class="btn btn-primary mt-3">Update Status</button>
                            </div>
                        </div>
                        
                    </form>
                        
                    </div>
                </div>
            </div>
            <!--end::Toolbar container-->
        </div>
    </div>
    <!--end::Content wrapper-->

    <script>
    $(document).ready(function() {
        $('#select_all').on('click', function() {
            $('.select_country').prop('checked', this.checked);
        });

        $('.select_country').on('click', function() {
            if ($('.select_country:checked').length == $('.select_country').length) {
                $('#select_all').prop('checked', true);
            } else {
                $('#select_all').prop('checked', false);
            }
        });
    });
    </script>

    @include('layouts.dashboard_footer')

</div>

@endsection


