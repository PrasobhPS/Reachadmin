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

        $('#chandleryForm').validate({
          ignore: [],
          rules: {
            chandlery_name: {
              required: true
            },
            chandlery_description: {
              richTextRequired: true,
            },
            chandlery_coupon_code: {
              required: true,
            },
            chandlery_website: {
              required: true,
              url: true
            },
            chandlery_discount: {
              required: true,
              number: true,
              range: [1, 100]
            },
            chandlery_image: {
                // Check if the field is required based on certain conditions
                required: function(element) {
                    // If it's an edit page, don't require the partner_logo field
                    return !$(element).closest('form').hasClass('edit-page');
                }
            },
            chandlery_order: {
              required: true,
              number: true,
            },
          },
          messages: {
            chandlery_name: 'Please enter chandlery name.',
            chandlery_description: 'Please enter description.',
            chandlery_coupon_code: 'Please enter coupon code.',
            chandlery_website: {
              required: "Please enter a website URL.",
              url: "Please enter a valid URL."
            },
            chandlery_discount: {
              required: "Please enter a discount value.",
              number: "Please enter a valid number.",
              range: "Please enter a value between 1 and 100."
            },
            chandlery_image: 'Please upload Profile Picture.',
            chandlery_order: {
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