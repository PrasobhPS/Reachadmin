@extends('layouts.app')

@section('content')
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->
    
    <div class="d-flex flex-column flex-column-fluid">
        
        <div class="breadcrumb-wrapper">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Experts</li>
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
                    <div class="d-flex align-items-center justify-content-between  mb-6 common-head">
                        <h1 class="fs-2x text-dark mb-0">Experts List</h1>
                        <!-- <a href="{{ route('add-specialist') }}" class="btn btn-dark btn-sm">
                            <span title="Add Specialist"><i class="fas fa-plus p-0"></i></span>
                        </a> -->
                    </div>
                    <div class="members-list common-table-block">
                        <div class="table-responsive">
                            <!--begin::Table-->
                            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                <!--begin::Table head-->
                                <thead>
                                    <tr class="fw-bold fs-6 text-gray-800 border-0 bg-light">
                                        <th class="rounded-start">Profile Pic</th>
                                        <th>Name</th>
                                        <th>Title</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Biography</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <!--end::Table head-->
                                <!--begin::Table body-->
                                <tbody class="border-bottom border-dashed">
                                    @foreach($specialists as $specialist)
                                    <tr>
                                        <td class="text-center">
                                            @if(isset($specialist->members_profile_picture))
                                                <img style="width:62px;" src="{{ asset('storage/' . $specialist->members_profile_picture) }}" alt="Profile Picture">
                                            @else
                                                <img style="width:62px;" src="{{ asset('assets/images/noimage.jpg') }}" alt="Profile Picture">
                                            @endif
                                        </td>
                                        <td>
                                            <a class="m-1" href="{{ route('member-view', ['id' => $specialist->id]) }}">{{ $specialist->members_fname }} {{ $specialist->members_lname }}</a>
                                        </td>
                                        <td>
                                            {{ $specialist->members_employment}}
                                        </td>
                                        <td>
                                            {{ $specialist->members_email}}
                                        </td>
                                        <td>
                                            {{ $specialist->members_phone}}
                                        </td>
                                        <td>
                                            {!! Illuminate\Support\Str::words(strip_tags($specialist->members_biography), 20, '...') !!}
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <a class="m-1" href="{{ route('member-edit', ['id' => $specialist->id]) }}" title="Edit"><span class="edit m-0"><i class="fas fa-pencil"></i></span></a> 
                                                <a class="m-1" href="{{ route('specialists-videos', ['id' => $specialist->id]) }}" title="Video"><span class="delete m-0"><i class="fas fa-video"></i></span></a>
                                                <a class="m-1" href="{{ route('specialists-history', ['id' => $specialist->id]) }}" title="Booking History"><span class="info m-0"><i class="fas fa-calendar"></i></span></a>
                                                <!-- <a class="m-1" href="{{ route('schedule-add', ['id' => $specialist->id]) }}" title="Schedule Timings"><span class="warning m-0"><i class="fas fa-clock"></i></span></a> -->

                                                <!-- <a class="m-1" href="{{ route('settings.stripe', ['id' => $specialist->id]) }}" title="Stripe Payments"><span class="info m-0"><i class="fas fa-stripe"></i></span></a> -->

                                                <!-- <a class="m-1" href="#" onclick="confirmDelete('{{ route('specialist-delete', ['id' => $specialist->id]) }}')"><span class="delete m-0"><i class="fas fa-trash-alt"></i></span></a> -->
                                            </div>
                                        </td>
                                    </tr>
                                   @endforeach
                                </tbody>
                                <!--end::Table body-->
                            </table>
                            <!--end::Table-->
                        </div>
                        {{ $specialists->appends(request()->input())->links("pagination::bootstrap-5") }}
                    </div>
                </div>
            </div>
            <!--end::Toolbar container-->
        </div>
    </div>
    <!--end::Content wrapper-->
    @include('layouts.dashboard_footer')
    @include('common.confirm_delete', ['msg' => 'Are you sure you want to delete this expert?'])
</div>

@endsection
