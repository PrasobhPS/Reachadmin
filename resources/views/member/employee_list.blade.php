@extends('layouts.app')

@section('content')
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">

    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Toolbar-->
        <div class="breadcrumb-wrapper">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Members</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Employee</li>
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
                        <h1 class="fs-2x text-dark mb-0">Employee</h1>
                    </div>

                    <div class="members-list common-table-block">

                        <form action="{{ route('employee') }}" method="GET" class="form-inline">
                            <div class="row mb-10">
                                <div class="col-md-3">
                                    <label for="member_type" class="mr-2">Member Type:</label>
                                    <select name="member_type" id="member_type" class="form-select">
                                        <option value="">All</option>

                                        <option value="F" {{ request('member_type') == 'F' ? 'selected' : '' }}>Free Member</option>
                                        <option value="M" {{ request('member_type') == 'M' ? 'selected' : '' }}>Full Member</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="search" class="mr-2">Search:</label>
                                    <input type="text" name="search" id="search" class="form-control" value="{{ request('search') }}" placeholder="Search">
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="{{ route('employee') }}" class="btn btn-secondary mx-2">Reset</a>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <!--begin::Table-->
                            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                <!--begin::Table head-->
                                <thead>
                                    <tr class="fw-bold fs-6 text-gray-800 border-0 bg-light">
                                        <th class="rounded-start">Profile Picture</th>
                                        <th>Member Name</th>
                                        <th>Job Role</th>
                                        <th>Vessel</th>
                                        <th>Location</th>
                                        <th>Position</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <!--end::Table head-->
                                <!--begin::Table body-->
                                <tbody class="border-bottom border-dashed">
                                    @if($employee_list->isNotEmpty())
                                    @foreach($employee_list as $employee)
                                    <tr>
                                        <td class="text-center">
                                            @if($employee->member->members_profile_picture!='')
                                            <img style="width:62px;" src="{{ asset('storage/' . $employee->member->members_profile_picture) }}" alt="Profile Picture">
                                            @else
                                            <img style="width:62px;" src="{{ asset('assets/images/noimage.jpg') }}" alt="Profile Picture">
                                            @endif
                                        </td>
                                        <td>
                                            {{ $employee->member->members_fname}} {{ $employee->member->members_lname}}
                                        </td>
                                        <td>
                                            {{ $employee->jobRole ? $employee->jobRole->job_role : '' }}
                                        </td>
                                        <td>
                                            {{ $employee->vessel ? $employee->vessel->vessel_type : '' }}
                                        </td>
                                        <td>
                                            {{ $employee->country ? $employee->country->country_name : '' }}
                                        </td>
                                        <td>
                                            {{ $employee->position ? $employee->position->position_name : '' }}
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <div class="m-1">
                                                    @if($employee->employee_status=='A')
                                                    <span class="active" title="Active"><i class="fal fa-user-alt"></i></span>
                                                    @elseif($employee->employee_status=='I')
                                                    <span class="inactive" title="Inactive"><i class="fal fa-user-alt-slash "></i></span>
                                                    @endif
                                                </div>
                                                <a class="m-1" href="{{ route('employee-view', ['id' => $employee->employee_id]) }}"><span class="edit m-0"><i class="fas fa-eye"></i></span></a>
                                                <a class="m-1" href="#" onclick="confirmDelete('{{ route('employee-delete', ['id' => $employee->employee_id]) }}')"><span class="delete m-0"><i class="fas fa-trash-alt"></i></span></a>
                                            </div>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="10" style="text-align: center; color: red;"> No details to show</td>
                                    </tr>
                                    @endif
                                </tbody>
                                <!--end::Table body-->
                            </table>
                            <!--end::Table-->
                        </div>
                        {{ $employee_list->appends(request()->input())->links("pagination::bootstrap-5") }}
                    </div>
                </div>
            </div>
            <!--end::Toolbar container-->
        </div>
    </div>
    <!--end::Content wrapper-->
    @include('layouts.dashboard_footer')
    @include('common.confirm_delete', ['msg' => 'Are you sure you want to delete this employee?'])
</div>

@endsection