<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
  
    $(document).ready(function() {

        $('#{{ $id }}').flatpickr({
            dateFormat: 'm/d/Y',  // Set the desired date format
            minDate: "today",  // Disable selection of previous days
            showMonths: 1,  // Show only one month at a time
            enableTime: false, // Disable time selection
            monthSelectorType: "static", // Use static dropdown for months
            yearSelector: true  // Show year dropdown
        });

    });

</script>