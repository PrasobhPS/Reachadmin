@extends('layouts.app')

@section('content')
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->
    
    <div class="d-flex flex-column flex-column-fluid">
        <div class="breadcrumb-wrapper">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('partners') }}">Partners</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Chandlery</li>
                </ol>
            </nav>
        </div>
        <!--begin::Toolbar-->
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
                        <h1 class="fs-2x text-dark mb-0">Chandlery List</h1>
                        <!-- <a href="{{ route('add-partner') }}" class="btn btn-dark btn-sm">
                            <span title="Add Partner"><i class="fas fa-plus p-0"></i></span>
                        </a> -->
                    </div>
                    <div class="members-list common-table-block">
                        <div class="table-responsive">
                            <!--begin::Table-->
                            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                <!--begin::Table head-->
                                <thead>
                                    <tr class="fw-bold fs-6 text-gray-800 border-0 bg-light">
                                        <th class="rounded-start">Image</th>
                                        <th>Name</th>
                                        <th>Coupon Code</th>
                                        <th>Discount %</th>
                                        <th>Discription</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <!--end::Table head-->
                                <!--begin::Table body-->
                                <tbody class="border-bottom border-dashed">
                                    @if($chandlery->isNotEmpty())
                                        @foreach($chandlery as $value)
                                        <tr>
                                            <td class="text-center">
                                                @if(isset($value->partner_logo))
                                                    <img style="width:150px;" src="{{ asset('storage/' . $value->partner_cover_image) }}" alt="Profile Picture">
                                                @else
                                                    <img style="width:100px;" src="{{ asset('assets/images/noimage.jpg') }}" alt="Profile Picture">
                                                @endif
                                            </td>
                                            <td>{{ $value->partner_name}}</td>
                                            <td>{{ $value->partner_coupon_code}}</td>
                                            <td>{{ $value->partner_discount}}%</td>
                                            <td>{!! Illuminate\Support\Str::words(strip_tags($value->partner_description), 20, '...') !!}</td>
                                            <td>
                                                <div class="d-flex">
                                                    <a class="m-1" href="{{ route('partner-edit', ['id' => $value->id]) }}"><span class="edit m-0"><i class="fas fa-pencil"></i></span></a> 
                                                </div>
                                            </td>
                                        </tr>
                                       @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5" style="text-align: center; color: red;"> No details to show</td>
                                        </tr>
                                    @endif
                                </tbody>
                                <!--end::Table body-->
                            </table>
                            <!--end::Table-->
                        </div>
                        {{ $chandlery->appends(request()->input())->links("pagination::bootstrap-5") }}
                    </div>
                </div>
            </div>
            <!--end::Toolbar container-->
        </div>
    </div>
    <!--end::Content wrapper-->
    @include('layouts.dashboard_footer')
    @include('common.confirm_delete', ['msg' => 'Are you sure you want to delete this partner?'])
</div>

@endsection
