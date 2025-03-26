@extends('layouts.app')

@section('content')
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->
    
    <div class="d-flex flex-column flex-column-fluid">

        <div class="breadcrumb-wrapper">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('specialists') }}">Experts</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Booking History</li>
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
                        <h1 class="fs-2x text-dark mb-0"> Scheduled Call</h1>
                    </div>
                    <div class="members-list common-table-block">

                        <form method="GET" action="{{ route('scheduled-call') }}">
                            <div class="row mb-10">
                                <div class="col-md-4">
                                    <label for="call_status">Call Status</label>
                                    <select name="call_status" id="call_status" class="form-select">
                                        <option value="">All</option>
                                        <option value="C" {{ request('call_status') == 'C' ? 'selected' : '' }}>Completed</option>
                                        <option value="A" {{ request('call_status') == 'A' ? 'selected' : '' }}>Accepted</option>
                                        <option value="P" {{ request('call_status') == 'P' ? 'selected' : '' }}>Pending</option>
                                        <option value="R" {{ request('call_status') == 'R' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="specialist_name">Expert Name</label>
                                    <input type="text" name="specialist_name" id="specialist_name" class="form-control" value="{{ request('specialist_name') }}">
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="{{ route('scheduled-call') }}" class="btn btn-secondary mx-2">Reset</a>
                                </div>
                            </div>
                        </form>

                        <div class="common-table-container table-responsive">
                            <!--begin::Table-->
                            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                <thead>
                                    <tr class="fw-bold fs-6 text-gray-800 border-0 bg-light">
                                        <th class="rounded-start">Meeting With</th>
                                        <th>Expert Name</th>
                                        <th>Schedule Time</th>
                                        <th>Schedule Date</th>
                                        <th>Timeslot</th>
                                        <th>Fee</th>
                                        <th class="text-center">Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($schedule->isNotEmpty())
                                        @foreach($schedule as $value)
                                        <tr>
                                            <td>
                                                @if( optional($value->member)->members_profile_picture!='')
                                                    <img style="width: 62px" src="{{ asset('storage/' .  optional($value->member)->members_profile_picture) }}" alt="Profile Picture">
                                                @else
                                                    <img style="width: 62px" src="{{ asset('assets/images/noimage.jpg') }}" alt="Profile Picture">
                                                @endif
                                                
                                                {{ optional($value->member)->members_fname . ' ' . optional($value->member)->members_lname }}
                                            </td>
                                            <td>
                                            {{ optional($value->specialist)->members_fname . ' ' . optional($value->specialist)->members_lname }}
                                            </td>
                                            <td>
                                                {{ date('h:i A', strtotime($value->call_scheduled_time)) }}
                                            </td>
                                            <td>
                                                {{ date('d-m-Y', strtotime($value->call_scheduled_date)) }}
                                            </td>
                                            <td>
                                            @if($value->timeSlot == '1hr' || $value->timeSlot == '1 hour')
                                            {{ '1 Hour' }}
                                            @else
                                            {{ $value->timeSlot }}
                                            @endif
                                            </td>
                                            <td>
                                                £{{ number_format($value->call_fee, 2) }}</label>
                                            </td>
                                            <td class="text-center">
                                                <div class="status-area">
                                                @if($value->call_status=='C')
                                                    <label class="completed">Completed</label>
                                                @elseif($value->call_status=='A')
                                                    <label class="accepted">Accepted</label>
                                                @elseif($value->call_status=='P')
                                                    <label class="pending">Pending</label>
                                                @elseif($value->call_status=='R')
                                                    <label class="cancel">Cancelled</label>
                                                @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    <a class="m-1" href="#" onclick="cancelBooking('{{ route('cancel-call', ['id' => $value->id]) }}')"><span class="cancel m-0"><i class="fas fa-trash-alt"></i></span></a>
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
                        {{ $schedule->links("pagination::bootstrap-5") }}
                    </div>
                </div>
            </div>
            <!--end::Toolbar container-->
        </div>
    </div>
    <!--end::Content wrapper-->

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script>
function cancelBooking(url) {
    Swal.fire({
        title: 'Cancel Booking',
        input: 'textarea',
        inputLabel: 'Reason for cancellation',
        inputPlaceholder: 'Enter your reason here...',
        showCancelButton: true,
        confirmButtonText: 'Submit',
        cancelButtonText: 'Cancel',
        inputValidator: (value) => {
            if (!value) {
                return 'You need to provide a reason!'
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    reason: result.value
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Cancelled!', 'Your booking has been cancelled.', 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                },
                error: function(response) {
                    Swal.fire('Error!', 'There was an error cancelling your booking.', 'error');
                }
            });
        }
    });
}
</script>


    @include('layouts.dashboard_footer')
    @include('common.confirm_delete', ['msg' => 'Are you sure you want to cancel this call?'])
</div>

@endsection
