@extends('layouts.app')

@section('content')
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->

    <div class="d-flex flex-column flex-column-fluid">
        <div class="breadcrumb-wrapper">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Announcement</li>
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
                        <h1 class="fs-2x text-dark mb-0">Announcement List</h1>
                        <a href="{{ route('add_announcement') }}" class="btn btn-dark btn-sm">
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
                                        <th>Type</th>
                                        <th>Announcement</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <!--end::Table head-->
                                <!--begin::Table body-->
                                <tbody class="border-bottom border-dashed">
                                    @if($notification->isNotEmpty())
                                    @foreach($notification as $item)
                                    <tr>


                                        <td>
                                            {{ $item->member_type === 'F' ? 'Free Members' : ($item->member_type === 'M' ? 'Full Members' : 'All Members') }}
                                        </td>
                                        <td>
                                            {!! Illuminate\Support\Str::words(strip_tags($item->message), 20, '...') !!}
                                        </td>
                                        <td>
                                            {{ $item->created_at->format('m/d/Y') }}
                                        </td>
                                        <td>
                                            {{ $item->created_at->format('H:i') }}
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <div class="m-1">

                                                    <a class="m-1" href="#" onclick="confirmDelete('{{ route('delete_announcement', ['id' => $item->id]) }}')"><span class="delete m-0"><i class="fas fa-trash-alt"></i></span></a>
                                                </div>
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
                        {{ $notification->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
            <!--end::Toolbar container-->
        </div>
    </div>
    <!--end::Content wrapper-->
    @include('layouts.dashboard_footer')
    @include('common.confirm_delete', ['msg' => 'Are you sure you want to delete this Announcement?'])
</div>

@endsection