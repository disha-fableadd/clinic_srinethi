$(document).on('click', '#exportButton', function() {
    let branchId = localStorage.getItem('selectedBranchId');
    if (!branchId) {
        alert("Please select a branch first.");
        return;
    }
    window.location.href = "/therapy/export" + "?branch_id=" + branchId;
});

$(document).ready(function() {
    function ucfirst(str) {
        return str ? str.charAt(0).toUpperCase() + str.slice(1) : 'N/A';
    }
    let branchId = localStorage.getItem('selectedBranchId');

    // Load therapy tests
    $.ajax({
        url: '/api/therapies',
        type: 'GET',
        data: {
            branch_id: branchId
        },
        dataType: 'json',
        success: function(data) {
            if ($.fn.DataTable.isDataTable("#therapytbl")) {
                $('#therapytbl').DataTable().destroy();
            }
            let tbody = $('#therapyTableBody');
            tbody.empty();
            data.forEach(function(therapy) {
                let row = `
                <tr>
                    <td class=" view-therapy" data-id="${therapy.id}" style="cursor:pointer">${ucfirst(therapy.name)}</td>
                    <td class=" view-therapy" data-id="${therapy.id}" style="cursor:pointer">${therapy.description ?? 'N/A'}</td>
                    <td class="d-none d-md-table-cell view-therapy" data-id="${therapy.id}" style="cursor:pointer">${therapy.duration_minutes ?? 'N/A'}</td>
                    <td class="d-none d-md-table-cell view-therapy" data-id="${therapy.id}" style="cursor:pointer">₹ ${parseFloat(therapy.cost).toFixed(2)}</td>
                    <td class="d-none d-md-table-cell view-therapy" data-id="${therapy.id}" style="cursor:pointer">${ucfirst(therapy.status)}</td>
                    <td class="d-none d-md-table-cell">
                        <div class="icon">
                            @if (app('hasPermission')(26, 'view'))<i class="fa fa-eye m-r-5 icon3 view-therapy" style="cursor:pointer" data-id="${therapy.id}" title="View"></i>@endif
                            @if (app('hasPermission')(26, 'update'))<i class="fa fa-pencil m-r-5 icon1 edit-therapy" style="cursor:pointer" data-id="${therapy.id}" title="Edit"></i>@endif
                            @if (app('hasPermission')(26, 'delete'))<i class="fa fa-trash-o m-r-5 icon2 delete-therapy" style="cursor:pointer" data-id="${therapy.id}" title="Delete"></i>@endif
                        </div>
                    </td>
                    <!-- Mobile-only expand button -->
                    <td class="d-table-cell d-md-none text-center">
                        <button class="btn btn-link expand-btn">
                            <i class="fa fa-chevron-down"></i>
                        </button>
                    </td>
                </tr>`;
                tbody.append(row);
            });
            $('#therapytbl').DataTable({
                paging: true,
                searching: true,
                ordering: true
            });
        },
        error: function(xhr) {
            console.log(xhr.responseText);
        }
    });

    // Handle expandable rows for mobile
    $(document).on('click', '.expand-btn', function() {
        const btn = $(this);
        const icon = btn.find('i');
        const tr = btn.closest('tr');
        const existingRow = tr.next('.details-row');

        // Toggle visibility if row already exists
        if (existingRow.length) {
            existingRow.slideToggle(300);
            icon.toggleClass('fa-chevron-down fa-chevron-up');
            return;
        }

        // Fetch hidden data for mobile view
        const name = tr.find('td').eq(0).text();
        const description = tr.find('td').eq(1).text();
        const duration = tr.find('td').eq(2).text();
        const cost = tr.find('td').eq(3).text();
        const status = tr.find('td').eq(4).text();
        const actionHtml = tr.find('td').eq(5).html();

        // Create and insert the details row
        const detailsRow = $(`
        <tr class="details-row">
            <td colspan="3">
                <div class="details-content">
                    <div><strong>Duration:</strong> ${duration}</div>
                    <div><strong>Cost:</strong> ${cost}</div>
                    <div><strong>Status:</strong> ${status}</div>
                    <div class="mt-2"><strong>Actions:</strong> ${actionHtml}</div>
                </div>
            </td>
        </tr>
    `);

        tr.after(detailsRow);
        icon.toggleClass('fa-chevron-down fa-chevron-up');
    });

    // Delete therapy
    $(document).on('click', '.delete-therapy', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "Delete this therapy?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#cfece0',
            cancelButtonColor: '#f89884',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            customClass: {
                confirmButton: 'swal-confirm-btn',
                cancelButton: 'swal-cancel-btn'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/api/therapies/${id}`,
                    type: 'DELETE',
                    success: function(res) {
                        Swal.fire('Deleted!', res.message, 'success');
                        location.reload();
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

    // View therapy
    $(document).on('click', '.view-therapy', function() {
        let id = $(this).data('id');
        window.location.href = `/therapy/show/${id}`;
    });

    // Edit therapy
    $(document).on('click', '.edit-therapy', function() {
        let id = $(this).data('id');
        window.location.href = `/therapy/edit/${id}`;
    });
});