<script src="{{ asset('assets/js/jquery.validate.min.js')}}"></script>
<script type="text/javascript">
  $(document).ready(function() {

    $('.text_editor2').richText();
    $('.text_editor3').richText();

    // Add custom method to validate rich text editor content
    $.validator.addMethod("richTextRequired", function(value, element) {
      // Get the content of the rich text editor
      var editorContent = $(element).closest('.richText').find('.richText-editor').html();
      // Check if the editor content is empty or just contains HTML tags with no text
      return $.trim(editorContent.replace(/<[^>]*>/g, '')) !== "";
    }, "Please enter a description.");

    $.validator.addMethod("validPhone", function(value, element) {
      return this.optional(element) || /^\d{5,15}$/.test(value);
    }, "Please enter a valid Phone Number.");

    $.validator.addMethod("validemail",
      function(value, element) {
        return /^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/.test(value);
      },
      "Please enter a valid email."
    );
    $(document).ready(function() {
      function updateValidationRules() {
        let membersType = $("#members_type").val(); // Get the selected member type
        let isFreeMember = membersType === "F"; // Check if it's a free member

        // Remove existing validation rules
        $("#memberForm").validate().destroy();

        // Reinitialize validation with conditional rules
        $("#memberForm").validate({
          ignore: [],
          rules: {
            members_fname: {
              required: true
            },
            members_lname: {
              required: true
            },
            members_email: {
              required: true,
              email: true
            },
            members_password: {
              required: function(element) {
                return !$(element).closest("form").hasClass("edit-page");
              },
              minlength: 8,
            },
            members_password_confirmation: {
              required: function(element) {
                return !$(element).closest("form").hasClass("edit-page");
              },
              equalTo: "#members_password",
            },
            // Apply validation only if not a free member
            members_phone_code: {
              required: !isFreeMember
            },
            members_phone: {
              required: !isFreeMember,
              validPhone: !isFreeMember
            },
            members_dob: {
              required: !isFreeMember
            },
            members_country: {
              required: !isFreeMember
            },
            members_address: {
              required: !isFreeMember
            },
            members_postcode: {
              required: !isFreeMember
            },
          },
          messages: {
            members_fname: "Please enter First Name.",
            members_lname: "Please enter Last Name.",
            members_email: {
              required: "Please enter email id.",
              email: "Please enter a valid email id.",
            },
            members_phone: {
              required: "Please enter Phone Number.",
              validPhone: "Please enter a valid Phone Number.",
            },
            members_country: "Please select Country.",
            members_address: "Please enter Address.",
            members_postcode: "Please enter Postcode.",
            members_password: {
              required: "Please enter Password.",
              minlength: "Password must be at least 8 characters long.",
            },
            members_password_confirmation: {
              required: "Please confirm your Password.",
              equalTo: "Passwords do not match.",
            },
            members_dob: "Please enter Date of Birth.",
          },
          submitHandler: function(form) {
            form.submit();
          },
        });
      }

      // Call the function when the page loads
      updateValidationRules();

      // Revalidate when `members_type` changes
      $("#members_type").change(function() {
        updateValidationRules();
      });
    });

    /*$('#memberForm').validate({
      ignore: [],
      rules: {
        members_fname: {
          required: true
        },
        members_lname: {
          required: true,
        },
        // members_biography: {
        //   richTextRequired: true,
        // },
        members_email: {
          required: true,
          validemail: true
        },
        members_phone_code: {
          required: true,
        },
        members_phone: {
          required: true,
          validPhone: true
        },
        members_dob: {
          required: true,
        },
        members_country: {
          required: true,
        },
        members_address: {
          required: true,
        },
        members_postcode: {
          required: true,
        },
        members_password: {
          required: function(element) {
            return !$(element).closest('form').hasClass('edit-page');
          },
          minlength: 8,
        },
        members_password_confirmation: {
          required: function(element) {
            return !$(element).closest('form').hasClass('edit-page');
          },
          equalTo: "#members_password"
        },
      
      },
      messages: {
        members_fname: 'Please enter First Name.',
        members_lname: 'Please enter Last Name.',
        members_email: {
          required: 'Please enter email id.',
          email: 'Please enter valid email id.',
        },
        members_phone: {
          required: 'Please enter Phone Number.',
          validPhone: 'Please enter a valid Phone Number.'
        },
        members_country: 'Please select Country.',
        members_address: 'Please enter Address.',
        members_region: 'Please enter Region.',
        members_password: {
          required: 'Please enter Password.',
          minlength: "Password must be at least 8 characters long."
        },
        members_password_confirmation: {
          required: 'Please confirm your Password.',
          equalTo: 'Passwords do not match.'
        },
        members_dob: 'Please enter Date of Birth.',
        //members_biography: 'Please enter biography.',
        //members_profile_picture: 'Please upload Profile Picture.',
      },
      submitHandler: function(form) {
        form.submit();
      }
    });*/

    $('#members_type').on('change', function() {
      var type = $(this).val();
      let currentDate = new Date();
      currentDate.setMonth(currentDate.getMonth() + 1);

      let formattedDate =
        ('0' + (currentDate.getMonth() + 1)).slice(-2) + '/' +
        ('0' + currentDate.getDate()).slice(-2) + '/' +
        currentDate.getFullYear();
      if (type === "F") {
        $("#expiry_date").hide();
        $("#members_subscription_end_date").val();
      } else {
        $("#expiry_date").show();
        $("#members_subscription_end_date").val(formattedDate);
      }
    });

  });
</script>