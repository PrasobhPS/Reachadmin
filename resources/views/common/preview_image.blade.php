<script>
    function previewImage(event, id) {
        var input = event.target;
        var preview = document.getElementById(id);

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }

            reader.readAsDataURL(input.files[0]);
        } else {
            preview.src = '#';
            preview.style.display = 'none';
        }
    }

    function previewThumbImage(src) {
        var preview = document.getElementById('preview');
        preview.src = src;
    }

    // Show larger image in modal
    document.getElementById('preview').addEventListener('click', function () {
        var modalImage = document.getElementById('modalImage');
        modalImage.src = this.src;
    });
</script>