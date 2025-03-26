<!-- <script src="https://cdn.tiny.cloud/1/r8xauxgq9re2o8ukibav6rx13u4muopvjp7n4skyu3it84fy/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: 'textarea',
        height: 200,
        plugins: [
            'advlist autolink lists link image charmap print preview anchor',
            'searchreplace visualblocks code fullscreen',
            'insertdatetime media table paste code help wordcount'
        ],
        toolbar: 'undo redo | formatselect | ' +
            'bold italic backcolor | alignleft aligncenter ' +
            'alignright alignjustify | bullist numlist outdent indent | ' +
            'removeformat | help',
        content_style: 'body { font-family: Arial, sans-serif; font-size: 14px }'
    });
</script> -->

<link rel="stylesheet" href="{{ asset('assets/css/richtext.min.css') }}">
<script type="text/javascript" src="{{ asset('assets/js/jquery.richtext.js') }}"></script>
<script>
$(document).ready(function() {
    $('.text_editor').richText();
    
});
</script>
