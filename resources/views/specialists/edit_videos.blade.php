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
                    <li class="breadcrumb-item active" aria-current="page">Edit Video</li>
                </ol>
            </nav>
        </div>

        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <!--begin::Toolbar container-->
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">

                <div class="event-outer w-100">

                	@if ($errors->any())
			        <div class="alert alert-danger">
			            <ul>
			                @foreach ($errors->all() as $error)
			                    <li>{{ $error }}</li>
			                @endforeach
			            </ul>
			        </div>
				    @endif
	    
                	<form method="POST" id="videoForm" class="edit-page" action="{{ route('update-specialist-video', $videos->video_id) }}" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="member_id" value="{{ $videos->member_id }}">
	                    <h1 class="fs-2x text-dark mb-3 common-head">Update Video</h1>
						<div class="row">
					      <div class="col-md-6">
                            <div class="card p-5 w-100">
								<div class="align-items-center d-flex justify-content-between">
									<div>
									   <h2>Video</h2>
									</div>
									<div class="row">
                                        <div class="col-md-6">
                                            <label class="fw-semibold fs-6">Status</label>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="status-container">
                                                <label class="switch">
                                                    <input type="checkbox" name="video_status"  {{ $videos->video_status === 'A' ? 'checked' : '' }} >
                                                    <span class="slider"></span>
                                                </label>
                                            </div>
                                        </div>
	                                </div> 
								</div>
								<div class="row">
		                            <label class="col-lg-12 col-form-label required fw-semibold fs-6">Video Title</label>
		                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
		                                <input type="text" name="video_title" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Enter video title" value="{{ $videos->video_title }}">
		                                <div class="fv-plugins-message-container invalid-feedback"></div>
		                            </div>
		                        </div>
		                        <div class="row">
		                            <label class="col-lg-12 col-form-label required fw-semibold fs-6">Video Sub Title</label>
		                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
		                                <input type="text" name="video_sub_title" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Enter video sub title" value="{{ $videos->video_sub_title }}">
		                                <div class="fv-plugins-message-container invalid-feedback"></div>
		                            </div>
		                        </div>
		                        <div class="row">
		                            <label class="col-lg-12 col-form-label fw-semibold fs-6">Video Description</label>
		                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
		                            	<textarea name="video_description" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0 text_editor" placeholder="Enter video description">{{ $videos->video_description }}</textarea>
		                                <div class="fv-plugins-message-container invalid-feedback"></div>
		                            </div>
		                        </div>

		                        <div class="row">
		                            <label class="col-lg-12 col-form-label required fw-semibold fs-6">Upload Video Thumbnail</label>
		                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
		                                <input type="file" id="video_thumb" name="video_thumb" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" >
		                                <input type="hidden" name="old_video_thumb" value="{{ $videos->video_thumb }}">
		                            </div>
		                            @if ($videos->video_thumb)
		                            <img class="py-3" src="{{ asset('storage/'.$videos->video_thumb) }}" alt="Video Thumbnail" style="width:100px">
		                            @endif
		                        </div>

		                        <div class="row">
		                            <label class="col-lg-12 col-form-label required fw-semibold fs-6">Video File Type</label>
		                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
		                                <label class="form-label px-5"><input type="radio" id="file_type1" name="video_file_type" value="File" {{ $videos->video_file_type == 'File' ? 'checked' : '' }}> File</label>
		                                <label class="form-label px-5"><input type="radio" id="file_type2" name="video_file_type" value="Url" {{ $videos->video_file_type == 'Url' ? 'checked' : '' }}> URL</label>
		                                <input type="hidden" id="finalFilename" name="finalFilename" value="{{ $videos->video_file }}" >
		                            </div>
		                        </div>

		                        <div class="row" id="file_div" style="{{ $videos->video_file_type == 'Url' ? 'display: none;' : '' }}">
		                            <label class="col-lg-12 col-form-label required fw-semibold fs-6">Upload Video File</label>
		                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
		                                <input type="file" id="video_file" name="video_file" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0">
		                            </div>
		                        </div>
		                        <div class="row" id="url_div" style="{{ $videos->video_file_type == 'File' ? 'display: none;' : '' }}">
		                            <label class="col-lg-12 col-form-label required fw-semibold fs-6">Video File Link</label>
		                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
		                                <input type="text" id="video_url" name="video_url" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="" value="{{ $videos->video_file }}" >
		                            </div>
		                        </div>
		                        <div class="row">
                                    <div class="col-md-12 mt-5">
                                        @if ($videos->video_file)
                                        	@if($videos->video_file_type == 'File')
                                            <video width="320" height="240" controls>
                                                <source src="{{ asset('storage/' . $videos->video_file) }}" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                            @elseif ($videos->video_file_type == 'Url')
                                            <iframe width="320" height="240" src="{{ $videos->video_file }}" frameborder="0" allowfullscreen></iframe>
                                            <a href="{{ $videos->video_file }}" target="_blank">Play Video</a>
                                            @endif
                                        @else
                                            <p>Video not found.</p>
                                        @endif
                                    </div>
                                </div>
								
							</div>  
						  </div>
						</div>

	                    <div class="card-footer d-flex justify-content-end px-0 py-3">
	                    	<a href="{{ route('specialists-videos', ['id' => $videos->member_id]) }}" style="margin-right: 10px;" class="btn btn-primary">
                            	Cancel
                        	</a> 
	                        <button type="submit" class="btn btn-primary">Update</button>
	                    </div>

                    </form>

                </div>
            </div>
            <!--end::Toolbar container-->
        </div>
    </div>
    <!--end::Content wrapper-->

    <script>

    	// Get radio buttons and divs
		const fileType1 = document.getElementById('file_type1');
		const fileType2 = document.getElementById('file_type2');
		const fileDiv = document.getElementById('file_div');
		const urlDiv = document.getElementById('url_div');

		// Add event listeners
		fileType1.addEventListener('click', function() {
		    fileDiv.style.display = 'block';
		    urlDiv.style.display = 'none';
		});

		fileType2.addEventListener('click', function() {
		    fileDiv.style.display = 'none';
		    urlDiv.style.display = 'block';
		});

        document.getElementById('video_file').addEventListener('change', function(event) {

            var fileInput = document.getElementById('video_file');
            var file = fileInput.files[0];
            var chunkSize = 1024 * 1024; // 1MB chunk size, adjust as needed
            var totalChunks = Math.ceil(file.size / chunkSize);

            // Loop through the chunks and upload them sequentially
            for (var i = 0; i < totalChunks; i++) {
                var formData = new FormData();
                formData.append('file', file.slice(i * chunkSize, (i + 1) * chunkSize));
                formData.append('filename', file.name);
                formData.append('chunkNumber', i);
                formData.append('totalChunks', totalChunks);

                // Send AJAX request to upload chunk
                fetch("{{ route('specialist-chunk') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    body: formData
                }).then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                }).then(data => {
                    console.log(data);
                    if(data.finalFilename!='')
                    $('#finalFilename').val(data.finalFilename);
                }).catch(error => {
                    console.error('There was a problem with the fetch operation:', error);
                });
            }

        });
    </script>
    
    @include('common.text_editor')
    @include('layouts.dashboard_footer')
    @include('scripts.video')
</div>

@endsection