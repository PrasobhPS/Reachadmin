@extends('layouts.app')

@section('content')
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->

    <div class="d-flex flex-column flex-column-fluid">

        <div class="breadcrumb-wrapper">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Transactions</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Transaction History</li>
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
                        <h1 class="fs-2x text-dark mb-0">Transaction History</h1>
                    </div>
                    <div class="members-list common-table-block">

                        <form method="GET" action="{{ route('transaction_history') }}">
                            <div class="row mb-10">
                                <div class="col-md-3">
                                    <label for="member_name" class="form-label">Member Name</label>
                                    <input type="text" name="member_name" id="member_name" class="form-control" value="{{ request('member_name') }}">
                                </div>
                                <div class="col-md-2">
                                    <label for="status" class="form-label">Payment Status:</label>
                                    <select name="status" id="status" class="form-select">
                                        <option value="">All</option>
                                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="Withdraw" {{ request('status') == 'Withdraw' ? 'selected' : '' }}>Withdraw</option>
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="{{ route('transaction_history') }}" class="btn btn-secondary mx-2">Reset</a>
                                </div>
                            </div>
                        </form>

                        <div class="common-table-container table-responsive">
                            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                <thead>
                                    <tr class="fw-bold">
                                        <th>#</th>
                                        <th>Transaction ID</th>
                                        <th>Transaction Date</th>
                                        <!-- <th>Member</th>-->
                                        <!--<th>Connected Member</th>-->


                                        <th>Type</th>
                                        <!--<th>Converted Original Amount</th>
                                        <th>Converted Reduced Amount</th>-->
                                        <th>Amount</th>
                                        <th class="text-center">Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody class="border-bottom border-dashed">
                                    @php
                                    $index = $i;
                                    @endphp
                                    @if($transactions->isNotEmpty())
                                    @foreach($transactions as $value)
                                    @php
                                    $currencySymbols = [
                                    'USD' => '$',
                                    'EUR' => '€',
                                    'GBP' => '£',
                                    ];
                                    $toCurrencySymbol = $currencySymbols[$value->to_currency] ?? $value->to_currency;
                                    @endphp
                                    <tr>
                                        <td>{{ $index++ }}</td>
                                        <td>{{ $value->transaction_id ?$value->transaction_id  : '' }}</td>
                                        <td>{{ $value->payment_date ? date('d-m-Y', strtotime($value->payment_date)) : '' }}</td>
                                        <!--<td>{{ $value->member ? $value->member->members_fname . ' ' . $value->member->members_lname : 'N/A' }}</td>-->
                                        <!--<td> {{ $value->connectedMember ? $value->connectedMember->members_fname . ' ' . $value->connectedMember->members_lname : 'N/A' }}</td>-->


                                        <td>{{ $value->type ?$value->type  : '' }}</td>
                                        <!-- <td>{!! $value->converted_original_amount ? $toCurrencySymbol . ' ' . number_format($value->converted_original_amount, 2) : '' !!}</td>
                                        <td>{!! $value->converted_reduced_amount ? $toCurrencySymbol . ' ' . number_format($value->converted_reduced_amount, 2) : '' !!}</td>-->
                                        <td>{!! $value->converted_actual_amount ? $toCurrencySymbol . ' ' . number_format($value->converted_actual_amount, 2) : '' !!}</td>
                                        <td>{{ $value->	status ?$value->status  : '' }}</td>
                                        <td> <a class="m-1 view_transaction" href="#" title="View Transaction" data-id="{{ $value->id }}"><span class="primary m-0"><i class="fal fa-eye"></i></span></a></td>
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
<!-- Modal HTML -->
<div class="modal fade transactionModal" id="transactionModal" tabindex="-1" role="dialog" aria-labelledby="transactionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="moderatorForm" method="POST">
                @csrf

                <div class="modal-header" style="background-color: #f8f9fa; color: #6c757d;">
                    <h1 class="modal-title fs-5" id="transactionModalLabel">Transaction Details</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    <div class="row mb-4">
                        <div class="col-6">
                            <strong>Transaction ID:</strong>
                            <p id="transaction_id" class="mb-0 text-muted"></p>
                        </div>
                        <div class="col-6">
                            <strong>Transaction Date:</strong>
                            <p id="transaction_date" class="mb-0 text-muted"></p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-6">
                            <strong>Member Name:</strong>
                            <p id="member_names" class="mb-0 text-muted"></p>
                        </div>
                        <div class="col-6">
                            <strong>Type:</strong>
                            <p id="type" class="mb-0 text-muted"></p>
                        </div>
                    </div>
                    <div class="membership_section">
                    <div class="row mb-4">
                        <div class="col-6">
                            <strong>Membership Fee:</strong>
                            <p id="transaction_amount" class="mb-0 text-muted"></p>
                        </div>
                        <div class="col-6">
                            <strong>Referral Discount:</strong>
                            <p id="discount_amount" class="mb-0 text-muted"></p>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-6">
                            <strong>Referral By:</strong>
                            <p id="referrerd_by" class="mb-0 text-muted"></p>
                        </div>
                        <div class="col-6">
                            <strong>Amount Paid:</strong>
                            <p id="amount_paid" class="mb-0 text-muted"></p>
                        </div>
                        
                    </div>
                    
                 </div>
                 <div class="referral_section">
                    <div class="row mb-4">
                            <div class="col-6">
                                <strong>Referral Amount:</strong>
                                <p id="referal_amount" class="mb-0 text-muted"></p>
                            </div>
                            <div class="col-6">
                                <strong>Converted Referral Amount:</strong>
                                <p id="converted_amount" class="mb-0 text-muted"></p>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-6">
                                <strong>Converted Rate:</strong>
                                <p id="converted_rate" class="mb-0 text-muted"></p>
                            </div>
                            <div class="col-6">
                                <strong>Status:</strong>
                                <p id="status1" class="mb-0 text-muted"></p>
                            </div>
                        </div>
                       
                  </div>
                  <div class="bookacall_section">
                  <div class="row mb-4">
                            <div class="col-6">
                                <strong>Call Amount:</strong>
                                <p id="bookacall_amount" class="mb-0 text-muted"></p>
                            </div>
                            <div class="col-6">
                                <strong>Converted Amount:</strong>
                                <p id="bookacall_converted_amount" class="mb-0 text-muted"></p>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-6">
                                <strong>Convert Rate:</strong>
                                <p id="bookacall_convert_rate" class="mb-0 text-muted"></p>
                            </div>
                            <div class="col-6">
                                <strong>Expert Name:</strong>
                                <p id="expert_name" class="mb-0 text-muted"></p>
                            </div>
                        </div>    
                  </div>
                  <div class="withdraw_section">
                    <div class="row mb-4">
                            <div class="col-6">
                                <strong>Member Original Amount:</strong>
                                <p id="member_original_amount" class="mb-0 text-muted"></p>
                            </div>
                            <div class="col-6">
                                <strong>Member Converted Amount:</strong>
                                <p id="member_converted_amount" class="mb-0 text-muted"></p>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-6">
                                <strong>Expert Original Amount:</strong>
                                <p id="expert_original_amount" class="mb-0 text-muted"></p>
                            </div>
                            <div class="col-6">
                                <strong>Expert Converted Amount:</strong>
                                <p id="expert_converted_amount" class="mb-0 text-muted"></p>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-6">
                                <strong>Fee for Boat:</strong>
                                <p id="original_boat_fee" class="mb-0 text-muted"></p>
                            </div>
                            <div class="col-6">
                                <strong>Converted Fee for Boat:</strong>
                                <p id="converted_boat_fee" class="mb-0 text-muted"></p>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-6">
                                <strong>Rate:</strong>
                                <p id="expert_rate" class="mb-0 text-muted"></p>
                            </div>
                            <div class="col-6">
                                <strong></strong>
                                <p id="" class="mb-0 text-muted"></p>
                            </div>
                        </div>
                       
                  </div>
                  <div class="row mb-4">
                            <div class="col-6">
                                <strong>Transaction Type:</strong>
                                <p id="transaction_type" class="mb-0 text-muted"></p>
                            </div>
                            <div class="col-6">
                                <strong></strong>
                                <p id="" class="mb-0 text-muted"></p>
                            </div>
                        </div>
                  
                   
                  
                   
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $(".membership_section").hide();
        $('.view_transaction').on('click', function() {
            var id = $(this).data('id');
            $.ajax({
                url: '/transactions/details/' + id, // Backend route to get transaction details
                type: 'GET',
                success: function(response) {

                    if (response.success) {
                       if(response.data.type=='Membership'){
                        $(".bookacall_section").hide();
                        $(".referral_section").hide();
                        $(".withdraw_section").hide();
                        $(".membership_section").show();
                       }else if(response.data.type=='Referral'){
                        $(".membership_section").hide();
                        $(".bookacall_section").hide();
                        $(".withdraw_section").hide();
                        $(".referral_section").show();
                       }
                       else if((response.data.type=='Book A Call') && (response.data.status=='Withdraw')){
                        $(".membership_section").hide();
                        $(".referral_section").hide();
                        $(".bookacall_section").hide();
                        $(".withdraw_section").show();
                       }
                       else if(response.data.type=='Book A Call'){
                        $(".membership_section").hide();
                        $(".referral_section").hide();
                        $(".withdraw_section").hide();
                        $(".bookacall_section").show();
                       }
                        var amount = response.data.converted_original_amount; // Get the amount dynamically
                        var currency = response.data.from_currency ; // Get the currency dynamically
                        var currencySymbols = {
                        USD: '$',
                        EUR: '€',
                        GBP: '£',
                        // Add more currencies as needed
                        };

                        var symbol = currencySymbols[currency] || currency; 
                        var from_amount = response.data.original_amount;
                        var converted_currency = response.data.to_currency ;
                        var converted_symbol = currencySymbols[converted_currency] || converted_currency; 
                        // Populate the modal with transaction details
                        $('#transaction_id').text(response.data.transaction_id || 'N/A');
                        $('#transaction_date').text(response.data.payment_date || 'N/A');
                        $('#member_names').text(response.data.member_name || 'N/A');
                        $('#transaction_amount').text(symbol + amount || 'N/A');
                        $('#bookacall_amount').text(symbol + amount || 'N/A');
                        $('#referal_amount').text(symbol + from_amount || 'N/A');
                        $('#converted_amount').text(converted_symbol + amount || 'N/A');
                        $('#converted_rate').text(parseFloat(response.data.rate).toFixed(2)|| 'N/A');
                        $('#status1').text(response.data.status || 'N/A');
                        $('#transaction_type').text(response.data.transaction_type || 'N/A');
                        
                        $('#discount_amount').text(symbol + response.data.converted_reduced_amount || 'N/A');
                        $('#referrerd_by').text(response.data.connected_member_name || 'N/A');
                        $('#amount_paid').text(symbol + response.data.converted_actual_amount || 'N/A');
                        $('#transaction_currency').text(response.data.from_currency || 'N/A');
                        $('#transaction_member').text(response.data.member_name || 'N/A');
                        $('#transaction_status').text(response.data.status || 'N/A');
                        $('#type').text(response.data.type || 'N/A');
                        $('#bookacall_converted_amount').text(converted_symbol+response.data.converted_original_amount || 'N/A');
                        $("#bookacall_convert_rate").text(parseFloat(response.data.rate).toFixed(2)|| 'N/A');
                        $("#expert_name").text(response.data.connected_member_name || 'N/A');
                        $("#member_original_amount").text(symbol+response.data.original_amount || 'N/A');
                        $("#member_converted_amount").text(converted_symbol+response.data.converted_original_amount || 'N/A');
                        $("#expert_original_amount").text(symbol+response.data.actual_amount || 'N/A');
                        $("#expert_converted_amount").text(converted_symbol+response.data.converted_actual_amount || 'N/A');
                        $("#expert_rate").text(parseFloat(response.data.rate).toFixed(2) || 'N/A');
                        $("#original_boat_fee").text(symbol+response.data.reduced_amount || 'N/A');
                        $("#converted_boat_fee").text(converted_symbol+response.data.converted_reduced_amount || 'N/A');
                        // Show the modal
                        $('#transactionModal').modal('show');
                    } else {
                        alert('Failed to load transaction details.');
                    }
                },
                error: function(error) {
                    alert('An error occurred while fetching transaction details.');
                }
            });

            $('#transactionModal').modal('show');
        });
    });
</script>
@endsection