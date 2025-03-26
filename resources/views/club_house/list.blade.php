@extends('layouts.app')

@section('content')
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->
    
    <div class="d-flex flex-column flex-column-fluid">
        <div class="breadcrumb-wrapper">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Club House</li>
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
                        <h1 class="fs-2x text-dark mb-0">Club House List</h1>
                        <a href="{{ route('add-club-house') }}" class="btn btn-dark btn-sm">
                            <span title="Add Club House"><i class="fas fa-plus p-0"></i></span>
                        </a>
                    </div>
                    <div class="members-list common-table-block">
                        <div class="table-responsive">
                            <!--begin::Table-->
                            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                <!--begin::Table head-->
                                <thead>
                                    <tr class="fw-bold fs-6 text-gray-800 border-0 bg-light">
                                        <th class="rounded-start">Image</th>
                                        <th>Name</th>
                                        <th>Discription</th>
                                        <th>Display Order</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <!--end::Table head-->
                                <!--begin::Table body-->
                                <tbody class="border-bottom border-dashed">
                                @if($club_house->isNotEmpty())
                                    @foreach($club_house as $value)
                                    
                                    <tr>
                                        <td class="text-center">
                                            @if(isset($value->club_image_thumb))
                                                <img style="width:100px;height:80px;" src="{{ asset('storage/' .  $value->club_image_thumb) }}" alt="Profile Picture">
                                            @else
                                                <img style="width:100px;" src="{{ asset('assets/images/noimage.jpg') }}" alt="Profile Picture">
                                            @endif
                                        </td>
                                        <td>
                                            {{ $value->club_name }}
                                        </td>
                                        <td>
                                            {!! Illuminate\Support\Str::words(strip_tags($value->club_short_desc), 20, '...') !!}
                                        </td>
                                        <td>
                                            {{ $value->club_order}}
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <div class="m-1">
                                                @if($value->club_status=='A')
                                                <span class="active" title="Active"><i class="fal fa-check"></i></span>
                                                @elseif($value->club_status=='I')
                                                <span class="inactive" title="Inactive"><i class="fal fa-ban"></i></span>
                                                @endif
                                                </div>
                                                <a class="m-1" href="{{ route('club-house-edit', ['id' => $value->id]) }}" title="Edit"><span class="edit m-0"><i class="fas fa-pencil"></i></span></a> 
                                                <a class="m-1 add_moderator" href="#" title="Add Moderator" data-id="{{ $value->id }}"><span class="primary m-0"><i class="fal fa-user"></i></span></a>
                                                <a class="m-1" href="#" onclick="confirmDelete('{{ route('delete-club-house', ['id' => $value->id]) }}')" title="Delete"><span class="delete m-0"><i class="fas fa-trash-alt"></i></span></a>
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
                        {{ $club_house->appends(request()->input())->links("pagination::bootstrap-5") }}
                    </div>
                </div>
            </div>
            <!--end::Toolbar container-->
        </div>
    </div>
    <!--end::Content wrapper-->

<!-- Modal HTML -->
<div class="modal fade moderatorModal" id="moderatorModal" tabindex="-1" role="dialog" aria-labelledby="moderatorModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="moderatorForm" method="POST">
                @csrf

                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="moderatorModalLabel">Add Moderator</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="club_id" name="club_id">

                    <div class="form-group mb-5">
                        <label class="form-label">Select Moderator</label>
                        <select name="member_id" class="form-select form-select-solid form-select-lg fw-semibold">
                            <option value="">Select a Moderator...</option>
                            @foreach($members as $value)
                            <option value="{{ $value->id }}" >{{ $value->members_fname.' '.$value->members_lname }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-5">
                        <h4>Existing Moderators</h4>
                        <ul id="moderatorList"></ul>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {

    $('.add_moderator').on('click', function() {
        var id = $(this).data('id');
        $('#club_id').val(id);

        get_moderator(id);
  
        $('#moderatorModal').modal('show');
    });

    $('#moderatorForm').on('submit', function(event) {
        event.preventDefault();

        var id = $('#club_id').val();
        var url = '{{ url("club-house/add-moderator") }}/' + id;

        $.ajax({
            url: url,
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    alert('Moderator added successfully!');
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

    // Handle delete moderator
    $(document).on('click', '.delete-moderator', function() {
        var id = $(this).data('id');
        if (confirm('Are you sure you want to delete this moderator?')) {
            $.ajax({
                url: '{{ url("club-house/delete-moderator") }}/' + id,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        alert('Moderator deleted successfully!');
                        get_moderator(response.club_id);
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    alert('An error occurred while deleting the moderator.');
                }
            });
        }
    });
});

function get_moderator(id){
    $.ajax({
        url: '{{ url("club-house/get-moderators") }}/' + id,
        method: 'GET',
        success: function(response) {
            var moderatorList = $('#moderatorList');
            moderatorList.empty();
            response.forEach(function(moderator) {
                if (moderator.member) {
                    moderatorList.append('<li class="align-items-center d-flex justify-content-between"><div>' + moderator.member.members_fname + ' ' + moderator.member.members_lname + '</div><div><button type="button" class="btn btn-danger btn-sm delete-moderator" data-id="' + moderator.id + '">Delete</button></div></li>');
                }
            });
        },
        error: function(xhr) {
            alert('An error occurred while fetching moderators.');
        }
    });
}
</script>

    @include('layouts.dashboard_footer')
    @include('common.confirm_delete', ['msg' => 'Are you sure you want to delete this club house?'])
</div>
@endsection
