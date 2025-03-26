<script type="text/javascript">
    $(document).ready(function () {

      // Add the extension method if it doesn't exist
      if (!$.validator.methods.extension) {
        $.validator.addMethod("extension", function (value, element, param) {
          param = typeof param === "string" ? param.replace(/,/g, '|') : "png|jpe?g|gif";
          return this.optional(element) || value.match(new RegExp(".(" + param + ")$", "i"));
        }, "Please enter a file with a valid extension.");
      }

        $('#videoForm').validate({
          rules: {
            video_title: {
              required: true
            },
            video_sub_title: {
              required: true,
            },
            video_thumb: {
              // Check if the field is required based on certain conditions
              required: function(element) {
                // If it's an edit page, don't require the partner_logo field
                return !$(element).closest('form').hasClass('edit-page');
              }
            },
            video_file: {
              required: function(element) {
                return $('input[name="video_file_type"]:checked').val() === 'File' && !$(element).closest('form').hasClass('edit-page');
              },
              extension: "mp4|avi|mkv"
            },
            video_url: {
              required: function(element) {
                return $('input[name="video_file_type"]:checked').val() === 'Url';
              },
              url: true
            }
          },
          messages: {
            video_title: 'Please enter video title.',
            video_sub_title: 'Please enter video sub title.',
            video_thumb: 'Please upload video thumbnail.',
            video_file: {
              required: "Please upload a video file.",
              extension: "Please upload a valid video file (mp4, avi, mkv)."
            },
            video_url: {
              required: "Please enter a video URL.",
              url: "Please enter a valid URL."
            }
          },
          submitHandler: function (form) {
            form.submit();
          }
        });

    });
</script>