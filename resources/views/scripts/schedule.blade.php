<script src="{{ asset('assets/js/jquery.validate.min.js')}}"></script>
<script type="text/javascript">
  $(document).ready(function ($) {

    $("#time_slots").on("change", function() {
      var min = $(this).val();
      var day = $('input[name="day[]"]:checked').val();

      if (typeof day !== "undefined") {
        getTimeSlots(day, min);
      }

    });

    $('.day-checkbox input[type="checkbox"]').on('change', function() {
      $('.day-checkbox input[type="checkbox"]').not(this).prop('checked', false);
      $('.day-checkbox').removeClass('active');
      $(this).closest('.day-checkbox').toggleClass('active', this.checked);

      var min = $('#time_slots').val();
      var day = $(this).val();
      getTimeSlots(day, min);
    });

    $("#scheduleForm").validate({
      rules: {
        doctor_id: {
          required: true,
        },
        'day[]': {
          required: true,
        },
        'schedule_time[]': {
          required: function(element) {
            return $('input[name="schedule_time[]"]:checked').length === 0;
          }
        },
      },
      messages: {
        'day[]': {
          required: "Please select at least one day.",
        },
        'schedule_time[]': "Please select at least one time slot.",
      },
    });

    $('#submitBtn').on('click', function () {
      if ($("#scheduleForm").valid()) {
        saveUpdate();
      }
    });

    $(document).on("change", ".appointment_time input[type='checkbox']", function() {
      $(this).closest('.appointment_time').toggleClass('active', this.checked);
    });

  });

  let getTimeSlots = (day, min) => {
    //ShowLoading();
    var member_id = $("#member_id").val();

    $.ajax({
      url: "{{ route('time-slots') }}",
      type: "POST",
      data: { day: day, min: min, member_id: member_id, _token: $('meta[name="csrf-token"]').attr('content') },
      dataType: "JSON",
      success: function (response) {
        //removeLoading();
        $("#time-slots-list").html(response.html);
      },
    });
  };

  let saveUpdate = (form) => {
    //ShowLoading();
    let formData = new FormData(document.getElementById("scheduleForm"));

    $.ajax({
      url: "{{ route('schedule-save') }}",
      type: "POST",
      cache: false,
      data: formData,
      processData: false,
      contentType: false,
      async: false,
      dataType: "JSON",
      success: function (response) {
        //removeLoading();
        if (response.status) {
          alert(response.msg);
        } else {
          alert(response.msg);
        }
      },
    });
  };
</script>