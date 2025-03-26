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
                    <li class="breadcrumb-item active" aria-current="page">Employers</li>
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
                        <h1 class="fs-2x text-dark mb-0">Employers</h1>
                    </div>
                    
                    <div class="members-list common-table-block">
                        <div class="table-responsive">
                            <!--begin::Table-->
                            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                <!--begin::Table head-->
                                <thead>
                                    <tr class="fw-bold fs-6 text-gray-800 border-0 bg-light">
                                        <th class="rounded-start">Profile Picture</th>
                                        <th>Member Name</th>
                                        <th>Company Name</th>
                                        <th>Email Id</th>
                                        <th>Phone Number</th>
                                        <th>Account Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <!--end::Table head-->
                                <!--begin::Table body-->
                                <tbody class="border-bottom border-dashed">
                                    @if($employer_list->isNotEmpty())
                                    @foreach($employer_list as $employer)
                                    <tr>
                                        <td class="text-center">
                                            @if($employer->employer_profile_picture!='')
                                                <img style="width:62px;" src="{{ asset('storage/' . $employer->employer_profile_picture) }}" alt="Profile Picture">
                                            @else
                                                <img style="width:62px;" src="{{ asset('assets/images/noimage.jpg') }}" alt="Profile Picture">
                                            @endif
                                        </td>
                                        <td>
                                            {{ $employer->member->members_fname}} {{ $employer->member->members_lname}}
                                        </td>
                                        <td>
                                            {{ $employer->employer_company_name}}
                                        </td>
                                        <td>
                                            {{ $employer->employer_email}}
                                        </td>
                                        <td>
                                            {{ $employer->employer_phone }}
                                        </td>
                                        <td class="text-center">
                                            @if($employer->employer_status=='A')
                                                <span class="active" title="Active"><i class="fal fa-user-alt"></i></span>
                                            @elseif($employer->employer_status=='I')
                                                <span class="inactive" title="Inactive"><i class="fal fa-user-alt-slash "></i></span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <a class="m-1" href="{{ route('employer-view', ['id' => $employer->employer_id]) }}"><span class="edit m-0"><i class="fas fa-eye"></i></span></a>
                                                <a class="m-1" href="#" onclick="confirmDelete('{{ route('employer-delete', ['id' => $employer->employer_id]) }}')"><span class="delete m-0"><i class="fas fa-trash-alt"></i></span></a>
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
                    </div>
                </div>
            </div>
            <!--end::Toolbar container-->
        </div>
    </div>
    <!--end::Content wrapper-->
    @include('layouts.dashboard_footer')
    @include('common.confirm_delete', ['msg' => 'Are you sure you want to delete this employer?'])
</div>

@endsection
