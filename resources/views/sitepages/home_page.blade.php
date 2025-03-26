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
                        <h1 class="fs-2x text-dark mb-0">Home Page CMS </h1>
                        <!--<a href="{{ route('add-home-page-section') }}" class="btn btn-dark btn-sm">
                            <span title="Add Home CMS"><i class="fas fa-plus p-0"></i></span>
                        </a>-->
                    </div>
                    <div class="members-list common-table-block">
                        <div class="table-responsive">
                            <!--begin::Table-->
                            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                <!--begin::Table head-->
                                <thead>
                                    <tr class="fw-bold fs-6 text-gray-800 border-0 bg-light">
                                    
                                        <th class="rounded-start">Home Page Header</th>
                                        <th>Home Page Section Type</th>
                                        <th>Home Page Section Button</th>
                                        <th>Display Order</th>
                                        <th>Home Page Section Images</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <!--end::Table head-->
                                <!--begin::Table body-->
                                <tbody class="border-bottom border-dashed">
                                    @if(!empty($details))
                                        @foreach($details as $pages)
                                        <tr>
                                         
                                            <td>
                                                {{ $pages->home_page_section_header }}
                                            </td>
                                            <td>
                                               
                                                {{ $pages->home_page_section_type == 'F' ? 'Full' : 'Half' }}
                                            </td>
                                            <td>
                                                
                                            {!! $pages->home_page_section_button !!}
                                            </td>
                                            <td>{{ $pages->order}}</td>
                                            <td>
                                            @if(isset($pages->home_page_section_images))
                                                    <img style="width:150px;" src="{{ asset('storage/' . $pages->home_page_section_images) }}" alt="Site Picture">
                                                @else
                                                    <img style="width:150px;" src="{{ asset('assets/images/no_event.jpg') }}" alt="Site Picture">
                                                @endif
                                            </td>


                                            <td>
                                                <div class="d-flex">
                                                    <a class="m-1" href="{{ route('home-page-edit', ['id' => $pages->id]) }}"><span class="edit m-0"><i class="fas fa-pencil"></i></span></a> 
                                                    <a class="m-1" href="#" onclick="confirmDelete('{{ route('delete-home-page', ['id' => $pages->id]) }}')"><span class="delete m-0"><i class="fas fa-trash-alt"></i></span></a>
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
