$(document).ready(function() {
    $('#report_format').select2({
        placeholder: "Select Format",
        allowClear: true
    });
});

document.addEventListener('DOMContentLoaded', function() {
    let storedBranchId = localStorage.getItem('selectedBranchId');
    if (storedBranchId) {
        document.getElementById('branch_id').value = storedBranchId;
    }
});

$('#radiology-form').on('submit', function(e) {
    e.preventDefault();

    // Clear old errors and alerts
    $('.invalid-feedback').remove();
    $('.is-invalid').removeClass('is-invalid');
    $('#radiotestsuccessMessage').hide().text('');
    $('#radiotesterrorMessage').hide().text('');

    let form = new FormData(this);

    $.ajax({
        url: '/api/radiology-tests',
        type: 'POST',
        data: form,
        processData: false,
        contentType: false,
        success: function(response) {
            $('#radiotestsuccessMessage').text(response.message).show();

            // Optional: Redirect after delay
            setTimeout(() => {
                window.location.href = '/radiology-tests/';
            }, 2000);
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                $.each(errors, function(key, messages) {
                    let input = $('[name="' + key + '"]');
                    input.addClass('is-invalid');
                    input.after(
                        '<span class="invalid-feedback" style="color:#e74c3c;font-size:0.95em;">' +
                        messages[0] + '</span>');
                });
            } else {
                $('#radiotesterrorMessage').text('Something went wrong. Please try again.')
                    .show();
            }
        }
    });
});