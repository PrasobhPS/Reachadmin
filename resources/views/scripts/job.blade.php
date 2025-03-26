<script src="{{ asset('assets/js/jquery.validate.min.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function () {

        $.validator.addMethod("richTextRequired", function(value, element) {
            // Get the content of the rich text editor
            var editorContent = $(element).closest('.richText').find('.richText-editor').html();
            // Check if the editor content is empty or just contains HTML tags with no text
            return $.trim(editorContent.replace(/<[^>]*>/g, '')) !== "";
        }, "Please enter a description.");

        $('#jobForm').validate({
            ignore: [],
            rules: {
                member_id: {
                  required: true
                },
                job_role: {
                  required: true,
                },
                boat_type: {
                  required: true,
                },
                job_duration: {
                  required: true,
                },
                job_location: {
                  required: true,
                },
                vessel_desc: {
                  required: true,
                },
                vessel_type: {
                  required: true,
                },
                vessel_size: {
                  required: true,
                },
                'job_images[]': {
                    required: function(element) {
                        return !$(element).closest('form').hasClass('edit-page');
                    }
                },
                job_summary: {
                  richTextRequired: true,
                },
            },
            messages: {
                member_id: 'Please select a member.',
                job_role: 'Please select job role.',
                boat_type: 'Please select boat type.',
                job_duration: 'Please select duration.',
                job_location: 'Please select job location.',
                vessel_desc: 'Please enter vessel details.',
                vessel_type: 'Please select vessel type.',
                vessel_size: 'Please enter vessel size.',
                job_images: {
                  required: 'Please upload job image.',
                },
                job_summary: 'Please enter job summary.',
            },
            submitHandler: function (form) {
                form.submit();
            }
        });
    });
</script>