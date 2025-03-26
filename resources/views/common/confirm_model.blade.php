<!-- Modal for confirmation -->
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationModalLabel">Confirm Status Change</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to change the status?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmStatusChange">Confirm</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var checkbox = document.getElementById({{ $id }});
        checkbox.addEventListener('change', function() {
            $('#confirmationModal').modal('show'); // Show confirmation modal when checkbox changes
        });

        document.getElementById({{ $id }}).addEventListener('click', function() {
            // Update status here, you can use AJAX to send a request to update the status
            console.log('Status changed!');
            $('#confirmationModal').modal('hide'); // Hide modal after confirmation
        });
    });
</script>