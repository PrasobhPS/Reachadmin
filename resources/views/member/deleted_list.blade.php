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
                        <h1 class="fs-2x text-dark mb-0">Deleted Members</h1>
                    </div>

                    <div class="members-list common-table-block">

                        <form action="{{ route('member-deleted') }}" method="GET" class="form-inline">
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
                                    <a href="{{ route('member-deleted') }}" class="btn btn-secondary mx-2">Reset</a>
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
                                        <th>Phone Number</th>
                                        <th>Current Employment</th>
                                        <th>Member Type</th>
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
                                        <td>
                                            <div class="d-flex">
                                                <!-- <div class="m-1">
                                                    <a href="#" onclick="confirmActive('{{ route('member-active', ['id' => $member->id]) }}')">
                                                        <span class="active" title="Active"><i class="fal fa-user-alt"></i></span></a>
                                                </div> -->

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

    <script>
        function confirmActive(url) {
            Swal.fire({
                title: 'Do you want to activate this member?',
                input: 'textarea',
                inputLabel: 'Reason for activating member',
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
                                Swal.fire('Activated', 'Member activated now.', 'success').then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                            }
                        },
                        error: function(response) {
                            Swal.fire('Error!', 'There was an error activate member.', 'error');
                        }
                    });
                }
            });
        }
    </script>

</div>


@endsection