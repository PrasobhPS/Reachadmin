<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<!-- Include jQuery UI JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

<script>
    $(document).ready(function() {

        @if(isset($maxDate))
        var maxDateOption = '{{ $maxDate }}';
        @else
        var maxDateOption = new Date();
        @endif
        var disable = false;
        $('#{{ $id }}').datepicker({
            dateFormat: 'dd-mm-yy',
            changeYear: true,
            yearRange: '1900:{{ date("Y") }}',
            maxDate: maxDateOption,
            defaultDate: 'y',
            disabled: disable,
            onSelect: function(selectedDate) {
                if ("{{ $id }}" == "startdate-datepicker") {
                    // If Start Date is selected, set minDate of End Date datepicker
                    $('#enddate-datepicker').datepicker('option', 'minDate', selectedDate);
                } else if ("{{ $id }}" == "enddate-datepicker") {
                    // If End Date is selected, set maxDate of Start Date datepicker
                    $('#startdate-datepicker').datepicker('option', 'maxDate', selectedDate);
                }
            }
        });

        @if(isset($class))
        $('#{{ $class }}').datepicker({
            dateFormat: 'dd-mm-yy',
            changeYear: true,
            defaultDate: 'y',
            minDate: new Date(),
        });

        @if($class === 'members_subscription_end_date_edit')
        $('#{{ $id }}').prop('disabled', true).datepicker('destroy');
        $('#{{ $class }}').prop('disabled', true).datepicker('destroy');
        @endif

        @endif
    });
</script>