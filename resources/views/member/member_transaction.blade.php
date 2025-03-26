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
                    <li class="breadcrumb-item"><a href="#">Transactions</a></li>
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
                        <h1 class="fs-2x text-dark mb-0">{{ $member->members_fname.' '.$member->members_lname }} - Transactions History</h1>
                    </div>
                    <div class="members-list common-table-block">

                        <form method="GET" action="{{ route('member-transaction',$member->id) }}">
                            <div class="row mb-10">
                                <div class="col-md-2">
                                    <label for="status" class="form-label">Payment Status:</label>
                                    <select name="status" id="status" class="form-select">
                                        <option value="">All</option>
                                        <option value="A" {{ request('status') == 'A' ? 'selected' : '' }}>Completed</option>
                                        <option value="P" {{ request('status') == 'P' ? 'selected' : '' }}>Pending</option>
                                        <option value="R" {{ request('status') == 'R' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="{{ route('member-transaction',$member->id) }}" class="btn btn-secondary mx-2">Reset</a>
                                </div>
                            </div>
                        </form>

                        <div class="common-table-container table-responsive">
                            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                <thead>
                                    <tr class="fw-bold">
                                        <th>#</th>
                                        <th>Payment To</th>
                                        <th>Amount Paid</th>
                                        <th>Payment Date</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="border-bottom border-dashed">
                                @php
                                    $index = $i;
                                @endphp
                                @if($transactions->isNotEmpty())
                                    @foreach($transactions as $value)
                                    <tr>
                                        <td>{{ $index++ }}</td>
                                        <td>{{ $value->specialist->members_fname.' '.$value->specialist->members_lname }}</td>
                                        <td>
                                            <div class="fee-area">
                                              £{{ number_format($value->amount_paid, 2) }}
                                              <span></span>
                                            </div>
                                        </td>
                                        <td>{{ $value->payment_date ? date('d-m-Y', strtotime($value->payment_date)) : '' }}</td>
                                        <td class="text-center">
                                            <div class="status-area">
                                            @if($value->status=='A')
                                                <label class="completed">Completed</label>
                                            @elseif($value->status=='P')
                                                <label class="pending">Pending</label>
                                            @elseif($value->status=='R')
                                                <label class="cancel">Cancelled</label>
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
                            <!--end::Table-->
                        </div>
                        {{ $transactions->appends(request()->input())->links("pagination::bootstrap-5") }}
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
