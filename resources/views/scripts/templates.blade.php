<script src="{{ asset('assets/js/jquery.validate.min.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function () {

        // Add custom method to validate rich text editor content
        $.validator.addMethod("richTextRequired", function(value, element) {
            // Get the content of the rich text editor
            var editorContent = $(element).closest('.richText').find('.richText-editor').html();
            // Check if the editor content is empty or just contains HTML tags with no text
            return $.trim(editorContent.replace(/<[^>]*>/g, '')) !== "";
        }, "Please enter a description.");

        $('#templateForm').validate({
          ignore: [],
          rules: {
            template_title: {
              required: true
            },
            template_subject: {
              required: true,
            },
            template_tags: {
              required: true
            },
            template_message: {
              richTextRequired: true,
            },
            template_to_address: {
              required: function(element) {
                return $('#template_to_status option:selected').val() === 'A';
              },
            }
            
          },
          messages: {
            template_tags: 'Please enter email tags.',
            template_subject: 'Please enter email subject.',
            template_message: 'Please enter email message.',
            template_to_address: {
              required: "Please enter To email address.",
            },
          },
          submitHandler: function (form) {
            form.submit();
          }
        });

        $('#template_to_status').on('change', function() {
          var type = $(this).val();
          if(type=="A"){
            $("#to_address").show();
          } else{
            $("#to_address").hide();
          }

        });

    });
</script>