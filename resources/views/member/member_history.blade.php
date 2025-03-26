@extends('layouts.app')

@section('content')
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->
    
    <div class="d-flex flex-column flex-column-fluid">
        
        <div class="breadcrumb-wrapper">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Members</a></li>
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
                        <h1 class="fs-2x text-dark mb-0">{{ $member->members_fname.' '.$member->members_lname }} - Booking History</h1>
                    </div>
                    <div class="members-list common-table-block">


                        <form action="{{ route('member-history',$member->id) }}" method="GET" class="form-inline">
                            <div class="row mb-10">
                                <div class="col-md-4">
                                    <label for="search" class="mr-2">Search:</label>
                                    <input type="text" name="search" id="search" class="form-control" value="{{ request('search') }}" placeholder="Search">
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary">Filter</button> 
                                    <a href="{{ route('member-history',$member->id) }}" class="btn btn-secondary mx-2">Reset</a>
                                </div>
                            </div>
                        </form>

                        <div class="common-table-container table-responsive">
                            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                <thead>
                                    <tr class="fw-bold">
                                        <th>Date</th>
                                        <th>Meeting With</th>
                                        <th>Schedule Time</th>
                                        <th>Fee</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="border-bottom border-dashed">
                                    @if($schedule->isNotEmpty())
                                        @foreach($schedule as $value)
                                        <tr>
                                            <td>
                                                <div class="date-block">
                                                  <span class="date">{{ date('d', strtotime($value->call_scheduled_date)) }}</span>
                                                  <span class="month">{{ date('F', strtotime($value->call_scheduled_date)) }}</span>
                                                </div>
                                            </td>
                                            <td>
                                               <div class="user-block d-flex">
                                                    <div class="user-img">
                                                        @if($value->specialist->members_profile_picture!='')
                                                            <img src="{{ asset('storage/' . $value->specialist->members_profile_picture) }}" alt="Profile Picture">
                                                        @else
                                                            <img src="{{ asset('assets/images/noimage.jpg') }}" alt="Profile Picture">
                                                        @endif
                                                    </div>
                                                    <div class="user-label">
                                                        <label>{{ $value->specialist->members_fname.' '.$value->specialist->members_lname }}</label>
                                                        <span>{{ $value->specialist->members_employment }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="time-area">
                                                    @php
                                                        $startTime = strtotime($value->uk_scheduled_time);
                                                        $endTime = strtotime('+60 minutes', $startTime);

                                                        $formattedStartTime = date('g:ia', $startTime);
                                                        $formattedEndTime = date('g:ia', $endTime);
                                                    @endphp
                                                    <label class="time">{{ $formattedStartTime }} - {{ $formattedEndTime }}</label>
                                                    <span>London (GMT)</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fee-area">
                                                  <label class="fee">£{{ number_format($value->call_fee, 2) }}</label>
                                                  <span></span>
                                                </div>
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
                                                    @if($value->call_status!='R')
                                                    <button type="button" class="btn btn-common-table" onclick="cancelBooking('{{ route('cancel-call', ['id' => $value->id]) }}')">Cancel</button>
                                                    <button type="button" class="btn btn-common-table re_arrange" data-id="{{ $value->id }}" data-date="{{ $value->call_scheduled_date }}" data-time="{{ $value->uk_scheduled_time }}" data-timezone="{{ $value->call_scheduled_timezone }}">Rearrange</button>
                                                    @endif
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

<!-- Modal HTML -->
<div class="modal fade" id="rearrangeModal" tabindex="-1" role="dialog" aria-labelledby="rearrangeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="rearrangeForm" method="POST">
                @csrf

                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="rearrangeModalLabel">Rearrange Call</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="schedule_id" name="schedule_id">
                    <input type="hidden" id="call_scheduled_timezone" name="call_scheduled_timezone">

                    <div class="form-group mb-5">
                        <label for="call_scheduled_date" class="form-label">Scheduled Date</label>
                        <input type="text" class="form-control" id="call_scheduled_date" name="call_scheduled_date" readonly required>
                    </div>
                    <div class="form-group">
                        <label for="call_scheduled_time" class="form-label">Scheduled Time</label>
                        <div>
                        @for ($i = 7 * 60; $i <= 20 * 60; $i += 60)
                            @php
                                $time = sprintf('%02d:%02d', intdiv($i, 60), $i % 60);
                            @endphp
                            <label class="btn border border-2 border-secondary m-1">
                                <input type="radio" name="call_scheduled_time" value="{{ date('H:i', strtotime($time)) }}">
                                {{ date('h:i A', strtotime($time)) }}
                            </label>
                        @endfor
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

<script>
$(document).ready(function() {

    $('.re_arrange').on('click', function() {
        var id = $(this).data('id');
        var date = $(this).data('date');
        var time = $(this).data('time');
        var timezone = $(this).data('timezone');

        var formattedTime = time.split(":").slice(0, 2).join(":");
        var dateParts = date.split('-');
        var formattedDate = dateParts[1] + '/' + dateParts[2] + '/' + dateParts[0];

        $('#schedule_id').val(id);
        $('#call_scheduled_date').val(formattedDate);
        $('input[name="call_scheduled_time"][value="' + formattedTime + '"]').prop('checked', true);
        $('#call_scheduled_timezone').val(timezone);

        $('#rearrangeModal').modal('show');
    });

    $('#rearrangeForm').on('submit', function(event) {
        event.preventDefault();

        var id = $('#schedule_id').val();
        var url = '{{ url("specialist/update-scheduled-call") }}/' + id;

        $.ajax({
            url: url,
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    alert('Call rescheduled successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('An error occurred while rescheduling the call.');
            }
        });
    });
});

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

    @include('common.flatpickr', ['id' => 'call_scheduled_date'])
    @include('layouts.dashboard_footer')
</div>

@endsection
