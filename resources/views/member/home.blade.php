@extends('layouts.app')

@section('content')
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">

    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid">

        <div class="breadcrumb-wrapper">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Members</li>
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
                        <h1 class="fs-2x text-dark mb-0">Members</h1>
                        <a href="{{ route('add-member') }}" class="btn btn-dark btn-sm">
                            <span title="Add Member"><i class="fas fa-plus p-0"></i></span>
                        </a>
                    </div>

                    <div class="members-list common-table-block">

                        <form action="{{ route('home') }}" method="GET" class="form-inline">
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
                                    <a href="{{ route('home') }}" class="btn btn-secondary mx-2">Reset</a>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                <thead>
                                    <tr class="fw-bold fs-6 text-gray-800 border-0 bg-light align-items-center">
                                        <th class="rounded-start">Profile Picture</th>
                                        <th>Member Name</th>
                                        <th>Email</th>
                                        <th>Address</th>
                                        <th>Phone Number</th>
                                        <th>Current Employment</th>
                                        <th>Member Type</th>

                                        <th>Referral</th>

                                        <th class="align-items-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="border-bottom border-dashed">
                                    @if($members->isNotEmpty())
                                    @foreach($members as $member)
                                    <tr>
                                        <td class="text-center">
                                            @if($member->members_profile_picture!='')
                                            <img style="width:50px;" src="{{ asset('storage/' . $member->members_profile_picture) }}" alt="Profile Picture">
                                            @else
                                            <img style="width:50px;" src="{{ asset('assets/images/noimage.jpg') }}" alt="Profile Picture">
                                            @endif
                                        </td>
                                        <td>
                                            {{ $member->members_fname}} {{ $member->members_lname}}
                                        </td>
                                        <td>
                                            {{ $member->members_email}}
                                        </td>

                                        <td>
                                            {!! Illuminate\Support\Str::words($member->members_address, 20, '...') !!}
                                        </td>
                                        <td>
                                            {{ $member->members_phone }}
                                        </td>
                                        <td>
                                            {{ $member->members_employment }}
                                        </td>
                                        <td>
                                            @if($member->members_type == 'T')
                                            Trial Member
                                            @elseif($member->members_type == 'F')
                                            Free Member
                                            @else
                                            Full Member
                                            @endif
                                        </td>

                                        <td class="p-0" style="min-width:72px;"> @if($member->members_type == 'M')<span class=" m-0 referral_popup" data-id="{{ $member->id }}" data-referal_type_id="{{ $member->referral_type_id }}" data-referal_rate="{{ $member->referral_rate }}" title="Referral"><a class="position-relative" style="cursor:pointer;">{{$member->referral_type}}<i class="fa-solid fa-link position-relative" style="left:3px;"></i></a></span> @endif</td>

                                        <td>
                                            <div class="d-flex">
                                                <div class="m-1">
                                                    @if($member->members_status=='A')
                                                    <span class="active" title="Active"><i class="fal fa-user-alt"></i></span>
                                                    @elseif($member->members_status=='I')
                                                    <span class="inactive" title="Inactive"><i class="fal fa-user-alt-slash "></i></span>
                                                    @endif
                                                </div>
                                                <!-- <a class="m-1" href="{{ route('member-view', ['id' => $member->id]) }}"><span class="edit m-0"><i class="fas fa-eye"></i></span></a>  -->
                                                <a class="m-1" href="{{ route('member-edit', ['id' => $member->id]) }}"><span class="edit m-0"><i class="fas fa-pencil"></i></span></a>
                                                @if ($member->memberSchedules->isEmpty())
                                                <div class="m-1"><span class="secondary m-0"><i class="fas fa-calendar"></i></span></div>
                                                @else
                                                <a class="m-1" href="{{ route('member-history', ['id' => $member->id]) }}" title="Booking History"><span class="info m-0"><i class="fas fa-calendar"></i></span></a>
                                                @endif
                                                @if ($member->memberTransaction->isEmpty())
                                                <div class="m-1"><span class="secondary m-0"><i class="fal fa-university"></i></span></div>
                                                @else
                                                <a class="m-1" href="{{ route('member-transaction', ['id' => $member->id]) }}" title="Transaction History"><span class="warning m-0"><i class="fal fa-university"></i></span></a>
                                                @endif
                                                @if ($member->memberSchedules->isEmpty() && $member->memberTransaction->isEmpty())
                                                <a class="m-1" href="#" onclick="confirmDelete('{{ route('member-delete', ['id' => $member->id]) }}')"><span class="delete m-0"><i class="fas fa-trash-alt"></i></span></a>
                                                @else
                                                <a href="#" disabled data-bs-toggle="tooltip" data-bs-placement="bottom" title="Delete disabled because this member is linked to existing schedules or transactions." class="secondary m-1"><span class="cancel m-0"><i class="fas fa-trash-alt"></i></span></a>
                                                @endif
                                                @if ($member->is_email_verified == 0 &&$member->members_type == 'F')
                                                <a href="{{ route('resendEmail', ['id' => $member->id]) }}" disabled data-bs-toggle="tooltip" data-bs-placement="bottom" title="Resend verification Mail" class="secondary m-1"><span class="cancel m-0"><i class="fas fa-redo-alt"></i></span></a>
                                                @endif
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
                        </div>
                        {{ $members->appends(request()->input())->links("pagination::bootstrap-5") }}
                    </div>
                </div>
            </div>

        </div>
    </div>

    @include('layouts.dashboard_footer')
    @include('common.confirm_delete', ['msg' => 'Are you sure you want to delete this member?'])
</div>


<div class="modal fade" id="referralModal" tabindex="-1" role="dialog" aria-labelledby="referralModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="referralTypeForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="referralModalLabel">Referral Types </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">


                    <div class="row w-100 ">
                        <div class="col-md-12  mb-3">

                            <div class="row">
                                <div class="col-md-12 d-md-flex align-items-center px-5">
                                    <input type="hidden" name="member_id" id="member_id" value="">
                                    <input type="hidden" name="referral_type_id" id="referral_type_id" value="">
                                    <div class="col-md-6 mx-2">
                                        <label for="referral_type" class="col-lg-12 col-form-label  fw-semibold fs-6"> Referral Type</label>
                                        <select name="referral_type" id="referral_type" class="form-select">

                                            @foreach($referaalTypes as $type)
                                            <option value="{{$type->id}}">{{ $type->referral_type }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mx-2">
                                        <label for="referral_type" class="col-lg-12 col-form-label  fw-semibold fs-6"> Rate %</label>
                                        <input type="text" readonly name="referral_rate" id="referral_rate" value="10" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0">
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer p-3 pb-1">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="update_referral_type">Update</button>
                        </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.referral_popup').on('click', function() {
            var id = $(this).data('id');
            var referral_type_id = $(this).data('referal_type_id');
            var referral_rate = $(this).data('referal_rate');
            $('#referralModal').on('show.bs.modal', function() {
                $('#referral_type').val(referral_type_id);
            });
            $('#member_id').val(id);
            $('#referral_type_id').val(referral_type_id);
            $('#referral_rate').val(referral_rate);
            $('#referralModal').modal('show');
        });

        function updateReferralRate() {
            var selectedeferralType = $('#referral_type option:selected').val();
            $.ajax({
                url: '{{url("referral_rate") }}/' + selectedeferralType,
                method: 'GET',
                success: function(response) {
                    const referralRate = response.rate.rate;
                    $('#referral_rate').val(referralRate);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching rate:', error);
                    $('#referral_rate').text('Error fetching rate.');
                }
            });
        }
        $('#referral_type').on('change', function() {
            updateReferralRate();
        });
        $('#referralTypeForm').on('submit', function(event) {
            event.preventDefault();

            var memberId = $('#member_id').val();
            var referralTypeId = $('#referral_type_id').val();
            var url = '{{ url("member/update_referral_type") }}';
            $.ajax({
                url: url,
                method: 'POST',
                data: $("#referralTypeForm").serialize(),
                success: function(response) {
                    if (response.success) {
                        alert('Referral Type updated successfully!');
                        location.reload();

                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr) {
                    alert('An error occurred while adding the moderator.');
                }
            });
        });

    });
</script>
@endsection