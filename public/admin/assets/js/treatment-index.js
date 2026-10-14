$(document).ready(function() {


    let token = localStorage.getItem("token");
    let branchId = localStorage.getItem("selectedBranchId"); // ✅ get branch id


    function ucfirst(str) {
        if (!str) return 'N/A';
        return str.charAt(0).toUpperCase() + str.slice(1);
    }
    // === Export button ===
    $(document).on('click', '#exportButton', function() {
        window.location.href = "{{ route('treatments.export') }}";
    });

    $.ajax({
        url: '/api/treatments',
        type: 'GET',
        dataType: 'json',
        data: {
            branch_id: branchId
        },
        headers: {
            "Authorization": "Bearer " + token
        },
        beforeSend: function() {
            $('#treatmentTableBody').html(`
                <tr><td colspan="6" class="text-center">Loading...</td></tr>
            `);
        },
        success: function(data) {
            console.log("API Response:", data);
            let treatmentsList = data.treatments ?? [];

            $('#treatmentTableBody').empty();

            treatmentsList.forEach(function(treatment) {
                $('#treatmentTableBody').append(`
                    <tr>
                        <td class="view-treatment" data-id="${treatment.id}" style="cursor:pointer;">
                            ${ucfirst(treatment.name)}
                        </td>
                        <td class="view-treatment" data-id="${treatment.id}" style="cursor:pointer;">
                            ${ucfirst(treatment.doctor_name)}
                        </td>
                        <td class="d-none d-md-table-cell">
                            ${typeof treatment.price === 'number' ? treatment.price : ucfirst(treatment.price)}
                        </td>
                        <td class="d-none d-md-table-cell view-treatment" data-id="${treatment.id}" style="cursor:pointer;">
                            ${ucfirst(treatment.description)}
                        </td>
                        <td class="d-none d-md-table-cell">
                            <div class="icon" style="cursor:pointer;">
                                @if (app('hasPermission')(7, 'view'))
                                    <i class="fa fa-eye m-r-5 icon3 view-treatment" data-id="${treatment.id}" title="View"></i>
                                @endif
                                @if (app('hasPermission')(7, 'update'))
                                    <i class="fa fa-pencil m-r-5 icon1 edit-treatment" data-id="${treatment.id}" title="Edit"></i>
                                @endif
                                @if (app('hasPermission')(7, 'delete'))
                                    <i class="fa fa-trash-o m-r-5 icon2 delete-treatment" data-id="${treatment.id}" title="Delete"></i>
                                @endif
                            </div>
                        </td>
                        <!-- 🔹 Mobile Details Button -->
                        <td class="d-table-cell d-md-none text-center">
                            <button class="btn btn-link expand-btn" data-id="${treatment.id}">
                                <i class="fa fa-chevron-down"></i>
                            </button>
                        </td>
                    </tr>
                `);
            });

            // Reinitialize DataTable
            $('#treatmenttbl').DataTable({
                paging: true,
                searching: true,
                ordering: false,
                responsive: false,
            });

        },
        error: function(xhr) {
            console.log(xhr.responseText);
        }
    });

});

// === Mobile dropdown expand (Details button) ===
$(document).on("click", ".expand-btn", function(e) {
    e.stopPropagation(); // prevent conflict with row click
    const btn = $(this);
    const icon = btn.find("i");
    const tr = btn.closest("tr");
    const existingRow = tr.next(".details-row");

    // 🔹 Close any other open rows first
    $(".details-row").not(existingRow).slideUp(250, function() {
        $(this).remove();
    });
    $(".expand-btn i").not(icon).removeClass("fa-chevron-up").addClass("fa-chevron-down");

    // 🔹 If already open, toggle it closed
    if (existingRow.length) {
        existingRow.slideToggle(300);
        icon.toggleClass("fa-chevron-down fa-chevron-up");
        return;
    }

    // 🔹 Otherwise, get data from the current row
    const price = tr.find("td").eq(2).text() || "N/A";
    const description = tr.find("td").eq(3).text() || "N/A";
    const actionHtml = tr.find("td").eq(4).html() || "";

    // 🔹 Build details row
    const detailsRow = $(`
        <tr class="details-row">
            <td colspan="6">
                <div class="details-content">
                    <div><strong>Price:</strong> ${price}</div>
                    <div><strong>Description:</strong> ${description}</div>
                    <div class="mt-2"><strong>Actions:</strong> ${actionHtml}</div>
                </div>
            </td>
        </tr>
    `);

    // 🔹 Insert below the current row and update icon
    tr.after(detailsRow);
    icon.toggleClass("fa-chevron-down fa-chevron-up");
});

$(document).on('click', '.view-treatment', function() {
    var treatmentId = $(this).data('id');
    window.location.href = '/treatment/show/' + treatmentId;
});

$(document).on('click', '.edit-treatment', function() {
    var treatmentId = $(this).data('id');
    window.location.href = '/treatment/edit/' + treatmentId;
});




$(document).on('click', '.delete-treatment', function() {
    var treatmentId = $(this).data('id');

    Swal.fire({
        title: 'Are you sure?',
        text: "You want to delete treatment!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#cfece0', // background for Yes
        cancelButtonColor: '#f89884', // background for Cancel
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        customClass: {
            confirmButton: 'swal-confirm-btn', // ✅ custom class
            cancelButton: 'swal-cancel-btn'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/api/treatments/' + treatmentId,
                type: 'DELETE',
                headers: {
                    "Authorization": "Bearer " + token
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'Treatment deleted successfully!',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload(); // Reload page to reflect changes
                    });
                },
                error: function(xhr) {
                    Swal.fire('Error', xhr.responseJSON?.message ||
                        'Failed to delete treatment. Please try again.', 'error');
                }
            });
        }
    });
});