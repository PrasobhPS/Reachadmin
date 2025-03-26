@extends('layouts.app')

@section('content')

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->

    <div class="d-flex flex-column flex-column-fluid">

    	<div class="breadcrumb-wrapper">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('specialists') }}">Experts</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Stripe Settings</li>
                </ol>
            </nav>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
    
    	@if ($errors->any())
	        <div class="alert alert-danger">
	            <ul>
	                @foreach ($errors->all() as $error)
	                    <li>{{ $error }}</li>
	                @endforeach
	            </ul>
	        </div>
	    @endif

        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <!--begin::Toolbar container-->
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">

                <div class="event-outer w-100">
                	<form method="POST" action="" enctype="multipart/form-data">
                        @csrf

	                    <h1 class="fs-2x text-dark mb-3 common-head">{{ $specialist->members_fname.' '.$specialist->members_lname }} - Stripe Settings</h1>
						<div class="row">
					      <div class="col-md-6">
                            <div class="card p-5 w-100">

		                        @if (!empty($connect_stripe_account) && $express_access_url!='')
							    <div class="row">
                                    <div class="col-md-4">
							            <a href="{{ $express_access_url }}" target="_blank" class="btn btn-info">Access Stripe</a>
                                    </div>
                                    <div class="col-md-4">
							            <a href="{{ $express_dashboard_access_url }}" target="_blank" class="btn btn-warning">Stripe Dashboard</a>
                                    </div>
                                    <div class="col-md-4">
							            <a href="{{ url('settings/disconnect') }}/{{ $specialist->id }}" class="btn btn-danger">Unlink Stripe</a>
                                    </div>
							    </div>
								@else
								    <a href="{{ $connect_url }}" target="_blank" class="btn btn-primary w-50">Setup Stripe Payments</a>
								@endif

							</div>  
						  </div>
						</div>

                    </form>

                    <div class="card p-5 w-100 mt-10">
                    <form action="{{ route('create-connected-account') }}" method="POST" id="account-form">
                        @csrf

                        <input type="hidden" name="specialist_id" value="{{ $specialist->id }}">
                        <input type="hidden" name="city" value="{{ $specialist->members_town }}">
                        <input type="hidden" name="members_dob" value="{{ $specialist->members_dob }}">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="first_name">First Name</label>
                                    <input type="text" id="first_name" name="first_name" class="form-control" value="{{ $specialist->members_fname }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="last_name">Last Name</label>
                                    <input type="text" id="last_name" name="last_name" class="form-control" value="{{ $specialist->members_lname }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email_id">Email Address</label>
                                    <input type="text" id="email_id" name="email_id" class="form-control" value="{{ $specialist->members_email }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="text" id="phone" name="phone" class="form-control" value="{{ $specialist->members_phone }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="address_line1">Address</label>
                                    <input type="text" id="address_line1" name="address_line1" class="form-control" value="{{ $specialist->members_address }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="postal_code">Postcode</label>
                                    <input type="text" id="postal_code" name="postal_code" class="form-control" value="{{ $specialist->members_postcode }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary mt-3">Create Connected Account</button>
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
    @include('common.common_functions')
    @include('layouts.dashboard_footer')
</div>

@endsection