@extends('layouts.app')

@section('content')
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->
    
    <div class="d-flex flex-column flex-column-fluid">
        <div class="breadcrumb-wrapper">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Partners</li>
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
                        <h1 class="fs-2x text-dark mb-0">Partners List</h1>
                        <a href="{{ route('add-partner') }}" class="btn btn-dark btn-sm">
                            <span title="Add Partner"><i class="fas fa-plus p-0"></i></span>
                        </a>
                    </div>
                    <div class="members-list common-table-block">
                        <div class="table-responsive">
                            <!--begin::Table-->
                            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                <!--begin::Table head-->
                                <thead>
                                    <tr class="fw-bold fs-6 text-gray-800 border-0 bg-light">
                                        <th>Image</th>
                                        <th>Logo</th>
                                        <th>Partner Name</th>
                                        <th class="w-25">Offer Details</th>
                                        <th>Order</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <!--end::Table head-->
                                <!--begin::Table body-->
                                <tbody class="border-bottom border-dashed">
                                    @if($partners->isNotEmpty())
                                        @foreach($partners as $partner)
                                        <tr>
                                            <td class="text-center">
                                                @if(isset($partner->partner_side_image))
                                                    <img style="width:100px; height: 80px" src="{{ asset('storage/' . $partner->partner_side_image) }}" alt="Image">
                                                @else
                                                    <img style="width:100px;" src="{{ asset('assets/images/noimage.jpg') }}" alt="Image">
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if(isset($partner->partner_logo))
                                                    <img style="width:100px;" src="{{ asset('storage/' . $partner->partner_logo) }}" alt="Logo">
                                                @else
                                                    <img style="width:100px;" src="{{ asset('assets/images/noimage.jpg') }}" alt="Logo">
                                                @endif
                                            </td>
                                            <td>
                                                {!! $partner->partner_name !!}
                                            </td>
                                            <td>
                                                {!! Illuminate\Support\Str::words(strip_tags($partner->partner_details), 20, '...') !!}
                                            </td>
                                            <td>
                                                {{ $partner->partner_display_order }} 
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    <div class="m-1">
                                                    @if($partner->partner_status=='A')
                                                    <span class="active" title="Active"><i class="fal fa-thumbs-up"></i></span>
                                                    @elseif($partner->partner_status=='I')
                                                    <span class="inactive" title="Inactive"><i class="fal fa-thumbs-down p-0"></i></span>
                                                    @endif
                                                    </div>
                                                    <a class="m-1" href="{{ route('partner-edit', ['id' => $partner->id]) }}"><span class="edit m-0"><i class="fas fa-pencil"></i></span></a> 
                                                    <a class="m-1" href="#" onclick="confirmDelete('{{ route('delete-partner', ['id' => $partner->id]) }}')"><span class="delete m-0"><i class="fas fa-trash-alt"></i></span></a>
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
                        {{ $partners->appends(request()->input())->links("pagination::bootstrap-5") }}
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
