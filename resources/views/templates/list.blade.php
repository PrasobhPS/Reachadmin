@extends('layouts.app')

@section('content')
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->
    
    <div class="d-flex flex-column flex-column-fluid">
        
        <div class="breadcrumb-wrapper">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Templates</li>
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
                        <h1 class="fs-2x text-dark mb-0">Templates List</h1>
                        <!-- <a href="{{ route('add-template') }}" class="btn btn-dark btn-sm">
                            <span title="Add Template"><i class="fas fa-plus p-0"></i></span>
                        </a> -->
                    </div>
                    <div class="members-list common-table-block">

                        <form action="{{ route('templates') }}" method="GET" class="form-inline">
                        <div class="row mb-10">
                            <div class="col-md-3">
                                <label for="template_to_status" class="mr-2">Mail To:</label>
                                <select name="template_to_status" class="form-select">
                                    <option value="">All</option>
                                    <option value="U" {{ request('template_to_status') == 'U' ? 'selected' : '' }}>Member</option>
                                    <option value="E" {{ request('template_to_status') == 'E' ? 'selected' : '' }}>Experts</option>
                                    <option value="A" {{ request('template_to_status') == 'A' ? 'selected' : '' }}>Admin</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="search" class="mr-2">Search:</label>
                                <input type="text" name="search" id="search" class="form-control" value="{{ request('search') }}" placeholder="Search">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary">Filter</button> 
                                <a href="{{ route('templates') }}" class="btn btn-secondary mx-2">Reset</a>
                            </div>
                        </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                <thead>
                                    <tr class="fw-bold fs-6 text-gray-800 border-0 bg-light">
                                        <th>Template Name</th>
                                        <th>Subject</th>
                                        <th>Mail To</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody class="border-bottom border-dashed">
                                    @foreach($templates as $value)
                                    <tr>
                                        <td>
                                            {{ $value->template_title}}
                                        </td>
                                        <td>
                                            {{ $value->template_subject}}
                                        </td>
                                        <td>
                                            @if($value->template_to_status=='U')
                                                Member
                                            @elseif($value->template_to_status=='A')
                                                Admin
                                            @elseif($value->template_to_status=='E')
                                                Experts
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <a class="m-1" href="{{ route('edit-template', ['id' => $value->id]) }}" title="Edit"><span class="edit m-0"><i class="fas fa-pencil"></i></span></a> 

                                                <!-- <a class="m-1" href="#" onclick="confirmDelete('{{ route('specialist-delete', ['id' => $value->id]) }}')"><span class="delete m-0"><i class="fas fa-trash-alt"></i></span></a> -->
                                            </div>
                                        </td>
                                    </tr>
                                   @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $templates->appends(request()->input())->links("pagination::bootstrap-5") }}
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
