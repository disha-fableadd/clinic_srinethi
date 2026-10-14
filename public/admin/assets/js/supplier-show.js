$(document).ready(function () {
    const token = localStorage.getItem('token');

    $.ajax({
        url: '/api/suppliers/' + supplierId,
        type: 'GET',
        dataType: 'json',
        headers: { "Authorization": "Bearer " + token },
        success: function (supplier) {
            function ucfirst(str) {
                return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
            }

            $('.company_name').text(ucfirst(supplier.name));
            $('#contact_person').text(ucfirst(supplier.contact_person));
            $('#supplier_phone').text(supplier.phone);
            $('#supplier_email').text(supplier.email);
            $('#supplier_address').text(supplier.address);
            $('#supplier_created_at').text(supplier.created_at.split('T')[0]);

        },
        error: function () {
            alert('Failed to fetch supplier details.');
        }
    });
});

$(document).on('click', '.delete-supplier', function () {
    var supplierId = $(this).data('id');
    console.log("Deleting Supplier ID:", supplierId);

    if (!supplierId) {
        Swal.fire('Error', 'Supplier ID is missing!', 'error');
        return;
    }

    Swal.fire({
        title: 'Are you sure?',
        text: "This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/api/suppliers/' + supplierId,
                type: 'DELETE',
                success: function (response) {
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'Supplier deleted successfully!',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = "/supplier";
                    });
                },
                error: function () {
                    Swal.fire('Error', 'Failed to delete supplier. Please try again.', 'error');
                }
            });
        }
    });
});


$(document).ready(function () {
    console.log("Supplier ID:", supplierId);

    if (!supplierId || isNaN(supplierId)) {
        alert("Error: Supplier ID is missing or invalid.");
        return;
    }

    $.ajax({
        url: "/api/suppliers/" + supplierId,
        type: "GET",
        success: function (supplier) {
            $(".edit-supplier-btn").attr("href", "/supplier/edit/" + supplier.id);
        },
        error: function () {
            alert("Failed to fetch supplier details.");
        }
    });
});