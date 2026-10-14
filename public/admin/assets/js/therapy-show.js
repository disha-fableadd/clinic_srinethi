$(document).ready(function() {
    var therapyId = $('.delete-therapy').data('id');

    $.ajax({
        url: '/api/therapies/' + therapyId,
        type: 'GET',
        success: function(response) {
            $('.therapy_name').text(response.name);
            $('.cost').text(response.cost);
            $('.duration_minutes').text(response.duration_minutes);
            $('.description').text(response.description);
            $('.therapy_status').text(response.status);
            $('.gst_option').text(response.gst_option || 'Without GST');

            if (response.gst_option === 'With GST' && Array.isArray(response.product_gst)) {
                let gstText = response.product_gst.map(function(item) {
                    let rate = parseFloat(item.tax_rate || 0).toFixed(2);
                    let amount = parseFloat(item.tax_amount || 0).toFixed(2);
                    return `${item.tax_name} (${rate}%) = ${amount}`;
                }).join(', ');
                $('.product_gst').text(gstText);
                $('#product_gst_p, #product_gst_hr, #product_gst_hr_after').show();
            } else {
                $('#product_gst_p, #product_gst_hr, #product_gst_hr_after').hide();
            }

            $('.edit-therapy-btn').attr('href', '/therapy/edit/' + response.id);
        },
        error: function() {
            alert('Failed to fetch therapy details.');
        }
    });


    $(document).on('click', '.delete-therapy', function() {
        let id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "Delete this therapy?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
           confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/api/therapies/${id}`,
                    type: 'DELETE',
                    success: function(res) {


                        Swal.fire({
                            title: 'Deleted!',
                            text: 'Therapy deleted successfully!',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href =
                                "/therapy";
                        });
                    },
                    error: function(xhr) {
                        let errorMsg = 'Failed to delete therapy.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        Swal.fire('Error', errorMsg, 'error');
                    }
                });
            }
        });
    });


});
