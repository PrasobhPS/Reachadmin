@extends('layouts.app')

@section('content')
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->
    
    <div class="d-flex flex-column flex-column-fluid">
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
                        <h1 class="fs-2x text-dark mb-0">Events List</h1>
                        <a href="{{ route('add-event') }}" class="btn btn-dark btn-sm">
                            <span title="Add Events"><i class="fas fa-plus p-0"></i></span>
                        </a>
                    </div>
                    <div class="members-list common-table-block">
                        <div class="table-responsive">
                            <!--begin::Table-->
                            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                <!--begin::Table head-->
                                <thead>
                                    <tr class="fw-bold fs-6 text-gray-800 border-0 bg-light">
                                        <th class="rounded-start">Event Picture</th>
                                        <th>Name</th>
                                        <th>Details</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Allowed Number of Members</th>
                                        <th>Members Ony</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <!--end::Table head-->
                                <!--begin::Table body-->
                                <tbody class="border-bottom border-dashed">
                                    @foreach($events as $event)
                                    <tr>
                                        <td>
                                            @if(isset($event->event_picture))
                                                <img style="width:32px;" src="{{ asset('storage/' . $event->event_picture) }}" alt="Profile Picture">
                                            @else
                                                <img style="width:32px;" src="{{ asset('assets/images/no_event.jpg') }}" alt="Profile Picture">
                                            @endif
                                        </td>
                                        <td>
                                            {!! $event->event_name !!}
                                        </td>
                                        <td>
                                            {!! Illuminate\Support\Str::words($event->event_details, 20, '...') !!}
                                        </td>
                                        <td>
                                            {{ date('d-m-Y', strtotime($event->event_start_date)) }}
                                        </td>
                                        <td>
                                            {{ date('d-m-Y', strtotime($event->event_end_date)) }}
                                        </td>
                                        <td>
                                            {{ $event->event_allowed_members }}
                                        </td>
                                        <td class="text-center">
                                            @if($event->event_members_only=='Y')
                                                <span class="active" title="Yes"><i class="fas fa-check"></i></span>
                                            @elseif($event->event_members_only=='N')
                                                <span class="inactive" title="No"><i class="fas fa-times p-0"></i></span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($event->event_status=='A')
                                                <span class="active" title="Active"><i class="fas fa-check"></i></span>
                                            @elseif($event->event_status=='I')
                                                <span class="inactive" title="Inactive"><i class="fas fa-times p-0"></i></span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <a class="m-1" href="{{ route('event-edit', ['id' => $event->id]) }}"><span class="edit m-0"><i class="fas fa-pencil"></i></span></a> 
                                                <a class="m-1" href="#" onclick="confirmDelete('{{ route('event-delete', ['id' => $event->id]) }}')"><span class="delete m-0"><i class="fas fa-trash-alt"></i></span></a>
                                            </div>
                                        </td>
                                    </tr>
                                   @endforeach
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
    @include('common.confirm_delete', ['msg' => 'Are you sure you want to delete this event?'])
</div>

@endsection
