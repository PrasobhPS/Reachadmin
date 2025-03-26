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
                    <li class="breadcrumb-item active" aria-current="page">Positions</li>
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
                        <h1 class="fs-2x text-dark mb-0"> Positions</h1>
                        <a href="{{ route('add-positions') }}" class="btn btn-dark btn-sm">
                            <span title="Add Positions"><i class="fas fa-plus p-0"></i></span>
                        </a>
                    </div>
                    <div class="members-list common-table-block">
                        <div class="table-responsive">
                            <!--begin::Table-->
                            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                <!--begin::Table head-->
                                <thead>
                                    <tr class="fw-bold fs-6 text-gray-800 border-0 bg-light">
                                        <th class="rounded-start">Positions</th>
                                        <th  class="text-center">Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <!--end::Table head-->
                                <!--begin::Table body-->
                                <tbody class="border-bottom border-dashed">
                                    @forelse($positions as $value)
                                        <tr>
                                            <td>
                                                {{ $value->position_name }}
                                            </td>
                                            <td class="text-center">
                                                @if($value->position_status=='A')
                                                    <span class="active" title="Active"><i class="fal fa-thumbs-up"></i></span>
                                                @elseif($value->position_status=='I')
                                                    <span class="inactive" title="Inactive"><i class="fal fa-thumbs-down p-0"></i></span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    <a class="m-1" href="{{ route('positions-edit', ['id' => $value->position_id]) }}"><span class="edit m-0"><i class="fas fa-pencil"></i></span></a>
                                                    @if (in_array($value->position_id, $employee_position))
                                                    <a href="#" disabled data-bs-toggle="tooltip" data-bs-placement="bottom" title="Delete disabled because this position is in use" class="secondary m-1"  ><span class="cancel m-0" ><i class="fas fa-trash-alt"></i></span></a>
                                                    @else
                                                    <a class="m-1" href="#" onclick="confirmDelete('{{ route('delete-positions', ['id' => $value->position_id]) }}')"><span class="delete m-0"><i class="fas fa-trash-alt"></i></span></a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" style="text-align: center; color: red;"> No details to show</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <!--end::Table body-->
                            </table>
                            <!--end::Table-->
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Toolbar container-->
        </div>
    </div>
    <!--end::Content wrapper-->
    @include('layouts.dashboard_footer')
    @include('common.confirm_delete', ['msg' => 'Are you sure you want to delete this role?'])
</div>

@endsection
