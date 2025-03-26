<script src="{{ asset('assets/js/jquery.validate.min.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function() {

        $('.text_editor2').richText();

        $.validator.addMethod("richTextRequired", function(value, element) {
            // Get the content of the rich text editor
            var editorContent = $(element).closest('.richText').find('.richText-editor').html();
            // Check if the editor content is empty or just contains HTML tags with no text
            return $.trim(editorContent.replace(/<[^>]*>/g, '')) !== "";
        }, "Please enter a description.");

        $('#partnerForm').validate({
            ignore: [],
            rules: {
                partner_name: {
                    required: true
                },
                partner_discount: {
                    number: true,
                    range: [1, 100]
                },
                partner_display_order: {
                    required: true,
                    number: true
                },
                partner_side_image: {
                    required: function(element) {
                        return !$(element).closest('form').hasClass('edit-page');
                    }
                },
                partner_side_image_mob: {
                    required: function(element) {
                        return !$(element).closest('form').hasClass('edit-page');
                    }
                },
                partner_logo: {
                    required: function(element) {
                        return !$(element).closest('form').hasClass('edit-page');
                    }
                },
                /* partner_video_thumb: {
                     required: function(element) {
                         return !$(element).closest('form').hasClass('edit-page');
                     }
                 },*/
                partner_description: {
                    richTextRequired: true,
                },
                partner_details: {
                    richTextRequired: true,
                },
                /* partner_video: {
                     required: function () {
                         return $('input[name="video_file_type"]:checked').val() == "File";
                     },
                     extension: "mp4|avi|mov|mkv"
                 },
                 video_url: {
                     required: function () {
                         return $('input[name="video_file_type"]:checked').val() == "Url";
                     },
                 },*/

            },
            messages: {
                partner_name: 'Please enter partner name.',
                partner_display_order: {
                    required: 'Please enter partner display order.',
                },
                partner_logo: {
                    required: 'Please upload partner logo.',
                },
                // partner_video_thumb: {
                //     required: 'Please upload thumb image.',
                // },
                partner_side_image: {
                    required: 'Please upload partner side image.',
                },
                partner_side_image_mob: {
                    required: 'Please upload partner side image.',
                },
                partner_discount: {
                    number: "Please enter a valid number.",
                    range: "Please enter a value between 1 and 100."
                },
                partner_description: 'Please enter description.',
                partner_details: 'Please enter description.',
                /* partner_video: {
                     required: 'Please upload a video file.',
                     extension: 'Only MP4, AVI, MOV, and MKV formats are allowed.'
                 },
                 video_url: {
                     required: 'Please add a video url.',
                 },*/

            },
            submitHandler: function(form) {
                form.submit();
            }
        });
    });

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

    document.getElementById('partner_video').addEventListener('change', function(event) {

        var fileInput = document.getElementById('partner_video');
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
                if (data.finalFilename != '')
                    $('#finalFilename').val(data.finalFilename);
            }).catch(error => {
                console.error('There was a problem with the fetch operation:', error);
            });
        }

    });
</script>