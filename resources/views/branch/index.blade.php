@extends('layout.app')

@section('content')
<style>
.swal-confirm-btn {
    color: rgb(58, 58, 58) !important;
}

.swal-cancel-btn {
    color: white !important;
}

a.btn.btn-sm.downloadpdf {
    background: #f89884;
    border-radius: 10px;
    padding-top: 7px;
    color: white;
    width: 32px;
}

colgroup {
    display: none;
}

button.btn.btn-link.expand-btn {
    background: #f89884;
    color: white;
    padding: 2px 8px;
    border-radius: 9px;
}

.details-row {
    background-color: #f8f9fa;
}

.details-content {
    padding: 10px;
    border-left: 3px solid #f89884;
    margin-left: 10px;
}

@media (min-width: 768px) {
    .d-table-cell.d-md-none {
        display: none !important;
    }

    .details-row {
        display: none !important;
    }
}

@media (max-width: 767px) {
    .d-none.d-md-table-cell {
        display: none !important;
    }
}

.content {
    height: 100vh;
}

.top-padding {
    padding-top: 15px;
}

.card-header {
    background-color: #f89884 !important;
}

.fa-calendar-check-o {
    font-size: 20px;
}

.btn-rounded {
    background-color: #fed9cf;
}

.icon {
    cursor: pointer;
}
</style>

<div class="page-wrapper">
    <div class="content">
        <div class="row top-padding"></div>

        <div class="row mt-2">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title d-inline-block text-white">
                            <i class="fa fa-calendar-check-o px-2"></i>
                            All Branches
                        </h3>

                        {{-- Add Button --}}
                        @if (app('hasPermission')(28, 'create'))
                        <a href="{{ route('branch.create') }}" class="btn btn-rounded btn-hdr">
                            <i class="fa fa-plus"></i> <span class="hdr-btn-text">Add</span>
                        </a>
                        @endif
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="treatmenttbl" class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th class="d-none d-md-table-cell">Address</th>
                                        <th class="d-none d-md-table-cell">City</th>
                                        <th class="d-none d-md-table-cell">State</th>
                                        <th class="d-none d-md-table-cell">Country</th>
                                        <th class="d-none d-md-table-cell">Actions</th>
                                        <th class="d-table-cell d-md-none text-center">Details</th>
                                    </tr>
                                </thead>
                                <tbody id="branchTableBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- DataTables + SweetAlert --}}
<link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css">
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    let token = localStorage.getItem('token');

    function loadBranches() {
        $.ajax({
            url: '/api/branches',
            type: 'GET',
            dataType: 'json',
            headers: {
                "Authorization": "Bearer " + token
            },
            success: function(response) {
                let branchesList = response.data ?? [];

                // clear old rows
                $('#branchTableBody').empty();

                branchesList.forEach(function(branch) {
                    let actionsHtml = `<div class="icon">`;

                    @if(app('hasPermission')(34, 'view'))
                    actionsHtml +=
                        `<i class="fa fa-eye m-r-5 icon3 view-branch" data-id="${branch.id}" title="View"></i>`;
                    @endif

                    @if(app('hasPermission')(34, 'update'))
                    actionsHtml +=
                        `<i class="fa fa-pencil m-r-5 icon1 edit-branch" data-id="${branch.id}" title="Edit"></i>`;
                    @endif

                    @if(app('hasPermission')(34, 'delete'))
                    // Only show delete if branch name is NOT 'Main' (case-insensitive)
                    if (branch.name.toLowerCase() !== 'main') {
                        actionsHtml +=
                            `<i class="fa fa-trash-o m-r-5 icon2 delete-branch" data-id="${branch.id}" title="Delete"></i>`;
                    }
                    @endif

                    actionsHtml += `</div>`;

                    $('#branchTableBody').append(`
    <tr data-id="${branch.id}">
        <td>${branch.name ?? ''}</td>
        <td>${branch.email ?? ''}</td>
        <td class="d-none d-md-table-cell">${branch.address ?? ''}</td>
        <td class="d-none d-md-table-cell">${branch.city ?? ''}</td>
        <td class="d-none d-md-table-cell">${branch.state ?? ''}</td>
        <td class="d-none d-md-table-cell">${branch.country ?? ''}</td>
        <td class="d-none d-md-table-cell">${actionsHtml}</td>
        <td class="d-table-cell d-md-none text-center">
            <button class="btn btn-link expand-btn" data-id="${branch.id}">
                <i class="fa fa-chevron-down"></i>
            </button>
        </td>
    </tr>
`);

                });

                // Reinitialize DataTable
                if ($.fn.DataTable.isDataTable('#treatmenttbl')) {
                    $('#treatmenttbl').DataTable().clear().destroy();
                }

                $('#treatmenttbl').DataTable({
                    paging: true,
                    searching: true,
                    ordering: true
                });
            },
            error: function(xhr) {
                console.log("Error loading branches:", xhr.responseText);
            }
        });
    }
    // === Mobile dropdown expand (Details button) ===
    $(document).on("click", ".expand-btn", function(e) {
        e.stopPropagation();
        const btn = $(this);
        const icon = btn.find("i");
        const tr = btn.closest("tr");

        // Close other open rows
        $(".details-row").not(tr.next(".details-row")).remove();
        $(".expand-btn i").not(icon).removeClass("fa-chevron-up").addClass("fa-chevron-down");

        const existingRow = tr.next(".details-row");
        if (existingRow.length) {
            existingRow.slideUp(250, function() {
                $(this).remove();
            });
            icon.toggleClass("fa-chevron-down fa-chevron-up");
            return;
        }

        // Extract hidden columns
        const address = tr.find("td.d-none.d-md-table-cell").eq(0).text() || "N/A";
        const city = tr.find("td.d-none.d-md-table-cell").eq(1).text() || "N/A";
        const state = tr.find("td.d-none.d-md-table-cell").eq(2).text() || "N/A";
        const country = tr.find("td.d-none.d-md-table-cell").eq(3).text() || "N/A";
        const actions = tr.find("td.d-none.d-md-table-cell").eq(4).html() || "";

        const detailsRow = $(`
        <tr class="details-row">
            <td colspan="8">
                <div class="details-content">
                    <div><strong>Address:</strong> ${address}</div>
                    <div><strong>City:</strong> ${city}</div>
                    <div><strong>State:</strong> ${state}</div>
                    <div><strong>Country:</strong> ${country}</div>
                    <div class="mt-2"><strong>Actions:</strong> ${actions}</div>
                </div>
            </td>
        </tr>
    `);

        tr.after(detailsRow);
        icon.toggleClass("fa-chevron-down fa-chevron-up");
    });

    // initial load
    loadBranches();

    // View
    $(document).on('click', '.view-branch', function() {
        var branchId = $(this).data('id');
        window.location.href = '/branch/' + branchId; // UI view page
    });

    // Edit
    $(document).on('click', '.edit-branch', function() {
        var branchId = $(this).data('id');
        window.location.href = '/branch/' + branchId + '/edit';
    });

    // Delete
    $(document).on('click', '.delete-branch', function() {
        var branchId = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "You want to delete this branch!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#cfece0',
            cancelButtonColor: '#f89884',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/api/branches/' + branchId,
                    type: 'DELETE',
                    headers: {
                        "Authorization": "Bearer " + token
                    },
                    success: function(response) {
                        Swal.fire({
                            title: 'Deleted!',
                            text: 'Branch deleted successfully!',
                            icon: 'success',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#cfece0'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Error',
                            xhr.responseJSON?.message ||
                            'Failed to delete branch.',
                            'error'
                        );
                    }
                });
            }
        });
    });
});
</script>
@endsection