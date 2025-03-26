@extends('layouts.app')

@section('content')
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->

    <div class="d-flex flex-column flex-column-fluid">
        <div class="breadcrumb-wrapper">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>

                    <li class="breadcrumb-item active" aria-current="page">Reported List</li>
                </ol>
            </nav>
        </div>
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
                        <h1 class="fs-2x text-dark mb-0">Reported List</h1>
                        <!-- <a href="{{ route('add-visa') }}" class="btn btn-dark btn-sm">
                            <span title="Add Visa"><i class="fas fa-plus p-0"></i></span>
                        </a> -->
                    </div>
                    <div class="members-list common-table-block">
                        <div class="table-responsive">
                            <!--begin::Table-->
                            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                <!--begin::Table head-->
                                <thead>
                                    <tr class="fw-bold fs-6 text-gray-800 border-0 bg-light">
                                        <th class="rounded-start">Title</th>
                                        <th class="rounded-start">Reported Time</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <!--end::Table head-->
                                <!--begin::Table body-->
                                <tbody class="border-bottom border-dashed">
                                    @if($chatRequest->isNotEmpty())
                                    @foreach($chatRequest as $chatRequest)
                                    <tr>
                                        <!-- <td> {{$chatRequest->sender_first_name .' '.$chatRequest->sender_last_name}}
                                            blocked
                                            {{$chatRequest->receiver_first_name .' '.$chatRequest->receiver_last_name}}

                                        </td> -->
                                        <td>
                                            @if($chatRequest->status == 3 && $chatRequest->is_reported == 1)
                                            {{$chatRequest->sender_first_name .' '.$chatRequest->sender_last_name}} Blocked & Reported by {{$chatRequest->receiver_first_name .' '.$chatRequest->receiver_last_name}}
                                            @elseif($chatRequest->status == 3 && $chatRequest->is_reported == 0)
                                            {{$chatRequest->sender_first_name .' '.$chatRequest->sender_last_name}} blocked {{$chatRequest->receiver_first_name .' '.$chatRequest->receiver_last_name}}
                                            @elseif($chatRequest->status == 0 && $chatRequest->is_reported == 1)
                                            {{$chatRequest->sender_first_name .' '.$chatRequest->sender_last_name}} reported {{$chatRequest->receiver_first_name .' '.$chatRequest->receiver_last_name}}
                                            @elseif($chatRequest->is_reported == 1)
                                            {{$chatRequest->sender_first_name .' '.$chatRequest->sender_last_name}} reported {{$chatRequest->receiver_first_name .' '.$chatRequest->receiver_last_name}}
                                            @endif
                                        </td>
                                        <td>
                                            @if($chatRequest->reported_time)
                                            {{ \Carbon\Carbon::parse($chatRequest->reported_time)->format('d/m/Y H:i') }}
                                            @endif

                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary view-messages" data-id="{{ $chatRequest->receiver_id }}" data-sender_id="{{$chatRequest->sender_id}}">
                                                View Messages
                                            </button>
                                        </td>
                                        <td>
                                            <div class="d-flex">


                                            </div>
                                        </td>
                                    </tr>
                                    <!-- Hidden messages row -->
                                    <tr class="messages-container" id="messages-{{ $chatRequest->receiver_id }}-{{ $chatRequest->sender_id }}" style="display: none;">
                                        <td colspan="3">
                                            <div class="messages-content p-3 border rounded bg-light"></div>
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
                    </div>
                </div>
            </div>
            <!--end::Toolbar container-->
        </div>
    </div>
    <!--end::Content wrapper-->
    @include('layouts.dashboard_footer')
    @include('common.confirm_delete', ['msg' => 'Are you sure you want to delete this role?'])
    <script>
        $(document).ready(function() {
            $(".view-messages").on("click", function() {
                let receiverId = $(this).data("id");
                let senderId = $(this).data("sender_id");
                let messageRow = $("#messages-" + receiverId + '-' + senderId);
                let messageContent = messageRow.find(".messages-content");

                if (messageRow.is(":visible")) {
                    messageRow.slideUp(); // Hide if already visible
                } else {
                    $.ajax({
                        url: "/get-last-messages/" + receiverId + '/' + senderId,
                        type: "GET",
                        success: function(response) {

                            if (response.success) {

                                let messagesHtml = "<ul class='list-unstyled'>";
                                response.messages.forEach(function(msg) {
                                    messagesHtml += `<li class="p-2 border-bottom"><strong>${msg.sender_name}:</strong> ${msg.message}</li>`;
                                });
                                messagesHtml += "</ul>";
                                messageContent.html(messagesHtml);
                                messageRow.slideDown();
                            } else {
                                messageContent.html("<p class='text-danger'>No messages found</p>");
                                messageRow.slideDown();
                            }
                        },
                        error: function() {
                            messageContent.html("<p class='text-danger'>Error fetching messages</p>");
                            messageRow.slideDown();
                        },
                    });
                }
            });
        });
    </script>

</div>

@endsection