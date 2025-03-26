<script src="{{ asset('assets/js/jquery.validate.min.js')}}"></script>
<script type="text/javascript">
  $(document).ready(function() {
    let index = $('.referral-type-entry').length;
    $('#settingsForm').validate({
      ignore: [],
      rules: {
        full_membership_fee: {
          required: true,
          number: true,
        },
        monthly_membership_fee: {
          required: true,
          number: true,
        },
        specialist_booking_fee: {
          required: true,
          number: true,
        },
        member_cancel_fee: {
          required: true,
          number: true,
        },
        referral_bonus: {
          required: true,
          number: true,
          range: [1, 100]
        },
      },
      messages: {
        full_membership_fee: 'Please enter annual membership fee.',
        monthly_membership_fee: 'Please enter monthly membership fee.',
        specialist_booking_fee: 'Please enter experts booking fee.',
        member_cancel_fee: 'Please enter member cancellation fee.',
        referral_bonus: {
          required: "Please enter referral bonus.",
          number: "Please enter a valid number.",
          range: "Please enter a value between 1 and 100."
        },
      },
      submitHandler: function(form) {
        form.submit();
      }
    });

    // Function to add validation rules to dynamic fields
    function addValidationRules(element) {
      element.find('input[name$="[type]"]').rules("add", {
        required: true,
        messages: {
          required: "Referral type is required."
        }
      });

      element.find('input[name$="[rate]"]').rules("add", {
        required: true,
        number: true,
        range: [0.01, 100],
        messages: {
          required: "Referral rate is required.",
          number: "Please enter a valid number.",
          range: "Please enter a value between 0.01 and 100."
        }
      });
    }

    // Add validation rules to existing referral type entries
    $('.referral-type-entry').each(function() {
      addValidationRules($(this));
    });

    // Add new referral type
    $('.add-referral-type').click(function() {
      const newEntry = $(`
            <div class="row mb-3 referral-type-entry">
                <input type="hidden" name="referral_types[${index}][id]" value="">
                <div class="col-md-5">
                    <label class="col-lg-12 col-form-label required fw-semibold fs-6">Referral Type </label>
                    <input type="text" name="referral_types[${index}][type]" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Referral Type">
                </div>
                <div class="col-md-5">
                    <label class="col-lg-12 col-form-label required fw-semibold fs-6">Referral Bonus (%)</label>
                    <input type="number" name="referral_types[${index}][rate]" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Referral Rate (%)" step="0.01">
                </div>
                <div class="col-md-2">
                    <label class="col-lg-12 col-form-label fw-semibold fs-6"></label>
                    <button type="button" class="btn btn-primary remove-referral-type">-</button>
                </div>
            </div>
        `);

      $('#referralTypesContainer').append(newEntry);
      addValidationRules(newEntry);
      index++;
    });

    // Remove referral type
    $('#referralTypesContainer').on('click', '.remove-referral-type', function() {
      // Get the element to be removed
      const entryToRemove = $(this).closest('.referral-type-entry');

      // Remove validation rules before removing the element
      entryToRemove.find('input').each(function() {
        $(this).rules('remove');
      });

      // Remove the entry
      entryToRemove.remove();
    });
  });
</script>