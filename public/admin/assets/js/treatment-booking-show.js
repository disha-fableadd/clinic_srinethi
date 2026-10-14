$(document).ready(function() {
    let bookingId = window.treatmentBookingId;
    let token = window.accessToken;

    // ---------- Load booking details ----------
    $.ajax({
        url: "/api/treatment_booking/" + bookingId,
        method: "GET",
        dataType: "json",
        headers: {
            "Authorization": "Bearer " + token
        },
        success: function(response) {
            if (response.booking) {
                let booking = response.booking;

                function ucfirst(str) {
                    return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
                }

                $("#file").attr("src", booking.patient.profile).on("error", function() {
                    $(this).attr("src", window.defaultImage);
                });

                $("#patient").text(ucfirst(booking.patient?.fullname ?? 'N/A'));
                $("#treatment").text(ucfirst(booking.treatment?.name ?? 'N/A'));
                // $("#machine").text(ucfirst(booking.machine?.name ?? 'N/A'));
                $("#machine").text(
                    booking.machine_details?.map(m => m.name).join(', ') || 'N/A'
                );
                $("#plan").text(ucfirst(booking.plan ?? 'N/A'));

                $("#status").text(ucfirst(booking.status ?? 'N/A'));
                $("#payment_date").text(booking.payment_date ?? 'N/A');

                $(".edit-appointment-btn").attr("href", "/treatment_booking/edit/" + booking
                    .id);

                if ((booking.status ?? '').toLowerCase() === 'completed') {
                    $(".edit-appointment-btn").hide();
                }
            } else {
                alert("Failed to fetch booking details");
            }
        },
        error: function(xhr) {
            console.error(xhr.responseText);
            alert("Error fetching booking details");
        }
    });

    $(document).on('click', '.download-treatment-booking', function() {
        const treatmentId = $(this).data('id');
        window.location.href = `/api/treatment-booking/${treatmentId}/pdf`;
    });

    // ---------- Delete booking ----------
    $(".delete-report").on("click", function() {
        let id = $(this).data('id');
        if (!id) {
            Swal.fire('Error', 'Booking ID not found!', 'error');
            return;
        }

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/api/treatment_booking/' + id,
                    method: 'DELETE',
                    headers: {
                        "Authorization": "Bearer " + token
                    },
                    success: function() {
                        Swal.fire({
                            title: 'Deleted!',
                            text: 'Booking deleted successfully!',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = window.treatmentBookingIndexRoute;
                        });
                    },
                    error: function() {
                        Swal.fire('Error', 'Error deleting booking.', 'error');
                    }
                });
            }
        });
    });

    // ---------- Load payment history ----------
    function loadPaymentHistory() {
        $.ajax({
            url: "/api/treatment_booking/" + bookingId + "/payment-history",
            method: "GET",
            dataType: "json",
            headers: {
                "Authorization": "Bearer " + token
            },
            success: function(response) {
                if (response.status && response.history.length > 0) {
                    let rows = '';
                    response.history.forEach(function(payment) {
                        rows += `
                        <tr>
                            <td>${payment.created_at}</td>
                            <td>${payment.total_amount ?? '0'}</td>
                            <td>${payment.amount ?? '0'}</td>
                            <td>${capitalize(payment.payment_type)}</td>
                            <td>${capitalize(payment.paid_type)}</td>

                        </tr>
                    `;
                    });

                    $('#paymentRecords').html(rows);
                    $('#noPaymentHistory').hide();
                    $('#paymentTable').show();

                    // ✅ Initialize DataTable only once
                    if (!$.fn.DataTable.isDataTable('#paymentTable')) {
                        $('#paymentTable').DataTable({
                            paging: true,
                            searching: true,
                            ordering: true
                        });
                    } else {
                        // ✅ If already initialized, refresh data
                        let table = $('#paymentTable').DataTable();
                        table.clear().rows.add($('#paymentRecords tr')).draw();
                    }
                } else {
                    $('#paymentRecords').empty();
                    $('#paymentTable').hide();
                    $('#noPaymentHistory').show();
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                alert("Error fetching payment history");
            }
        });
    }


    // ---------- Helper functions ----------
    function capitalize(str) {
        return str ? str.charAt(0).toUpperCase() + str.slice(1) : 'N/A';
    }

    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        let d = new Date(dateString);
        return d.toLocaleDateString() + ' ' +
            d.toLocaleTimeString([], {
                hour: '2-digit',
                minute: '2-digit'
            });
    }

    loadPaymentHistory();
});