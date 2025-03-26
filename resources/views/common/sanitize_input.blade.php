<script>
    document.addEventListener("submit", function(event) {
        document.querySelectorAll("input[type='text']").forEach((field) => {
            field.value = field.value.replace(/<[^>]*>/g, ""); // Remove HTML tags
        });
    });
</script>