@extends('layouts.app')

@section('content')
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->

    <div class="d-flex flex-column flex-column-fluid">

        <div class="breadcrumb-wrapper">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Jobs</li>
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
                        <h1 class="fs-2x text-dark mb-0">Jobs List</h1>
                        <!-- <a href="{{ route('add-job') }}" class="btn btn-dark btn-sm">
                            <span title="Add Jobs"><i class="fas fa-plus p-0"></i></span>
                        </a> -->
                    </div>
                    <div class="members-list common-table-block">

                        <form method="GET" action="{{ route('jobs') }}">
                            <div class="row mb-10">
                                <div class="col-md-3">
                                    <label for="member_name" class="form-label">Member Name</label>
                                    <input type="text" name="member_name" id="member_name" class="form-control" value="{{ request('member_name') }}">
                                </div>
                                <div class="col-md-3">
                                    <label for="job_role" class="form-label">Job Role</label>
                                    <select name="job_role" id="job_role" class="form-select">
                                        <option value="">Select Job Role</option>
                                        @foreach($jobRoles as $role)
                                        <option value="{{ $role->id }}" {{ request('job_role') == $role->id ? 'selected' : '' }}>{{ $role->job_role }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="job_status" class="form-label">Job Status:</label>
                                    <select name="job_status" id="job_status" class="form-select">
                                        <option value="">All</option>
                                        <option value="A" {{ request('job_status') == 'A' ? 'selected' : '' }}>Active</option>
                                        <option value="I" {{ request('job_status') == 'I' ? 'selected' : '' }}>Inactive</option>
                                        <option value="D" {{ request('job_status') == 'D' ? 'selected' : '' }}>Draft</option>
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="{{ route('jobs') }}" class="btn btn-secondary mx-2">Reset</a>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive common-table-container">
                            <!--begin::Table-->
                            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                <thead>
                                    <tr class="fw-bold fs-6 text-gray-800 border-0 bg-light">
                                        <th class="rounded-start">Member Name</th>
                                        <th>Job Role</th>
                                        <th>Boat Type</th>
                                        <th>Vessel Type</th>
                                        <th>Start Date</th>
                                        <th>Job Location</th>
                                        <th>Job Summary</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody class="border-bottom border-dashed">
                                    @if($jobs->isNotEmpty())
                                    @foreach($jobs as $job)
                                    <tr>
                                        <td>
                                            <?php if ($job->member) { ?>
                                                <a class="m-1" href="{{ route('member-view', ['id' => $job->member->id]) }}">{{ $job->member->members_fname.' '.$job->member->members_lname }}</a>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            {{ $job->role->job_role }}
                                        </td>
                                        <td>
                                            {{ $job->boat ? $job->boat->boat_type : '' }}
                                        </td>
                                        <td>
                                            {{ $job->vessel ? $job->vessel->vessel_type : '' }}
                                        </td>
                                        <td>
                                            {{ $job->job_start_date ? date('d-m-Y', strtotime($job->job_start_date)) : '' }}
                                        </td>
                                        <td>
                                            {{ $job->location ? $job->location->boat_location : '' }}
                                        </td>
                                        <td>
                                            {!! Illuminate\Support\Str::words(strip_tags($job->job_summary), 20, '...') !!}
                                        </td>
                                        <td class="text-center">
                                            <div class="status-area">
                                                @if($job->job_status=='A')
                                                <label class="completed">Active</label>
                                                @elseif($job->job_status=='I')
                                                <label class="accepted">Paused</label>
                                                @elseif($job->job_status=='D')
                                                <label class="pending">Draft</label>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <a class="m-1" href="{{ route('job-edit', ['id' => $job->id]) }}"><span class="edit m-0"><i class="fas fa-pencil"></i></span></a>
                                                <a class="m-1" href="#" onclick="confirmDelete('{{ route('delete-job', ['id' => $job->id]) }}')"><span class="delete m-0"><i class="fas fa-trash-alt"></i></span></a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="10" style="text-align: center; color: red;"> No details to show</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                            <!--end::Table-->
                        </div>
                        {{ $jobs->appends(request()->input())->links("pagination::bootstrap-5") }}
                    </div>
                </div>
            </div>
            <!--end::Toolbar container-->
        </div>
    </div>
    <!--end::Content wrapper-->
    @include('layouts.dashboard_footer')
    @include('common.confirm_delete', ['msg' => 'Are you sure you want to delete this job?'])
</div>

@endsection