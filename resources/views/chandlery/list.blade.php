@extends('layouts.app')

@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <!--begin::Content wrapper-->

        <div class="d-flex flex-column flex-column-fluid">
            <div class="breadcrumb-wrapper">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
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
                            <a href="{{ route('add-chandlery') }}" class="btn btn-dark btn-sm">
                                <span title="Add Chandlery"><i class="fas fa-plus p-0"></i></span>
                            </a>
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
                                            <th>Discount %</th>
                                            <th>Discription</th>
                                            <th>Display Order</th>
                                            <th>Status</th>
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
                                                        @if(isset($value->chandlery_image))
                                                            <img style="width:150px;"
                                                                src="{{ asset('storage/' . $value->chandlery_image) }}"
                                                                alt="Profile Picture">
                                                        @else
                                                            <img style="width:100px;" src="{{ asset('assets/images/noimage.jpg') }}"
                                                                alt="Profile Picture">
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $value->chandlery_name }}
                                                    </td>
                                                    <td>
                                                        {{ $value->chandlery_discount}}%
                                                    </td>
                                                    <td>
                                                        {!! Illuminate\Support\Str::words(strip_tags($value->chandlery_description), 20, '...') !!}
                                                    </td>
                                                    <td>
                                                        {{ $value->chandlery_order}}
                                                    </td>
                                                    <td class="text-center">
                                                        @if($value->chandlery_status == 'A')
                                                            <span class="active" title="Active"><i class="fal fa-user-alt "></i></span>
                                                        @elseif($value->chandlery_status == 'I')
                                                            <span class="inactive" title="Inactive"><i
                                                                    class="fal fa-user-alt-slash "></i></span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="d-flex">
                                                            <a class="m-1"
                                                                href="{{ route('chandlery-edit', ['id' => $value->id]) }}"><span
                                                                    class="edit m-0"><i class="fas fa-pencil"></i></span></a>
                                                            <a class="m-1" href="#"
                                                                onclick="confirmDelete('{{ route('delete-chandlery', ['id' => $value->id]) }}')"><span
                                                                    class="delete m-0"><i class="fas fa-trash-alt"></i></span></a>
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
        @include('common.confirm_delete', ['msg' => 'Are you sure you want to delete this chandlery?'])
    </div>
@endsection