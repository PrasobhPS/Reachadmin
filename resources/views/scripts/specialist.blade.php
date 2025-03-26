<script src="{{ asset('assets/js/jquery.validate.min.js')}}"></script>
<script type="text/javascript">
  $(document).ready(function () {
      
      $('.text_editor2').richText();

      // Add custom method to validate rich text editor content
      $.validator.addMethod("richTextRequired", function(value, element) {
          // Get the content of the rich text editor
          var editorContent = $(element).closest('.richText').find('.richText-editor').html();
          // Check if the editor content is empty or just contains HTML tags with no text
          return $.trim(editorContent.replace(/<[^>]*>/g, '')) !== "";
      }, "Please enter a description.");

      $.validator.addMethod("validPhone", function(value, element) {
          return this.optional(element) || /^\d{10}$/.test(value); // Validates if phone number has 10 digits
      }, "Please enter a valid Phone Number.");

      $('#specialistForm').validate({
        ignore: [],
        rules: {
          specialist_fname: {
            required: true
          },
          specialist_lname: {
            required: true,
          },
          specialist_biography: {
            richTextRequired: true
          },
          specialist_email: {
            required: true,
            email:true,
          },
          specialist_phone: {
            required: true,
            validPhone: true
          },
          specialist_country: {
            required: true,
          },
          // specialist_employment: {
          //   required: true,
          // },
          // specialist_employment_history: {
          //   richTextRequired: true
          // },
          // specialist_interest: {
          //   required: true,
          // },
          specialist_title: {
            required: true,
          },
          specialist_dob: {
            required: true,
          },
          specialist_address: {
            required: true,
          },
          specialist_region: {
            required: true,
          },
          specialist_profile_picture: {
              // Check if the field is required based on certain conditions
              required: function(element) {
                // If it's an edit page, don't require the partner_logo field
                return !$(element).closest('form').hasClass('edit-page');
              }
          },
        },
        messages: {
          specialist_fname: 'Please enter First Name.',
          specialist_lname: 'Please enter Last Name.',
          specialist_email: {
            required: 'Please enter email id.',
            email:'Please enter valid email id.',
          },
          specialist_phone: {
            required: 'Please enter phone number.',
            validPhone: 'Please enter a valid phone number.'
          },
          specialist_country: 'Please select Country.',
          //specialist_interest: 'Please enter Interests.',
          //specialist_employment: 'Please enter Current Employment.',
          specialist_title: 'Please select Title.',
          specialist_dob: 'Please enter Date of Birth.',
          specialist_profile_picture: 'Please upload Profile Picture.',
          specialist_biography: 'Please enter Biography.',
          //specialist_employment_history: 'Please enter Employment History.',
        },
        submitHandler: function (form) {
          form.submit();
        }
      });

  });
</script>