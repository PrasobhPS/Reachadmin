<script src="{{ asset('assets/js/jquery.validate.min.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function () {

        // Extend jQuery Validation plugin to validate TinyMCE editor
        $.validator.addMethod("tinymceValidation", function (value, element, params) { console.log('haiiii');
            // Check if TinyMCE editor is initialized
            if (tinymce.get(element.id)) {
                // Get content from TinyMCE editor
                var content = tinymce.get(element.id).getContent().trim();
                // Check if content is not empty
                return content.length > 0;
            }
            // If TinyMCE is not initialized or element not found, return true
            return false;
        }, "Please enter content.");

        $('#clubhouseForm').validate({
          rules: {
            club_name: {
              required: true
            },
            club_short_desc: {
              required: true,
            },
            club_image: {
              // Check if the field is required based on certain conditions
              required: function(element) {
                // If it's an edit page, don't require the partner_logo field
                return !$(element).closest('form').hasClass('edit-page');
              }
            },
            club_image_mob: {
              required: function(element) {
                return !$(element).closest('form').hasClass('edit-page');
              }
            },
            club_order: {
              required: true,
              number: true,
            },
          },
          messages: {
            club_name: 'Please enter club house name.',
            club_short_desc: 'Please enter description.',
            club_image: 'Please upload cover image.',
            club_image_mob: 'Please upload cover image for mobile.',
            club_order: {
              required: "Please enter a order value.",
              number: "Please enter a valid number.",
            },
          },
          submitHandler: function (form) {
            form.submit();
          }
        });

    });
</script>