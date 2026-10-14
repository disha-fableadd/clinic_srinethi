@extends('layout.app')
<style>
    .swal-confirm-btn {
        color: rgb(58, 58, 58) !important;
    }

    /* Cancel button text white (for visibility on red background) */
    .swal-cancel-btn {
        color: white !important;
    }

    colgroup {
        display: none;
    }

    button.btn.btn-link.expand-btn {
        background: #f89884;
        color: white;
        padding: 7px;
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

    #treatmenttbl thead th {
        vertical-align: middle !important;
        padding: 8px !important;
    }

    .expand-btn {
        border: none;
        background: none;
        color: #f89884;
        font-size: 16px;
    }

    .expand-btn:hover {
        color: #f89884;
    }
      .card-header{
        background-color:#f89884 !important; 
    }
    .btn-rounded{
        background-color: #fed9cf !important;
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

    @media (max-width: 767px) {
        .card-header .card-title {
            font-size: 16px !important;
            /* smaller font on phones */
            display: flex;
            align-items: center;
            /* vertical middle with icon */
            margin-bottom: 0;
            /* remove extra spacing */
        }

        .exportbtn {
            padding: .375rem 0.375rem !important;
        }

        #exportButton {
            padding: .375rem 0.375rem !important;
        }
    }
</style>
@section('content')
    <div class="page-wrapper">
        <div class="content" style="height:100vh">
            <div class="row " style="padding-top:15px">
            </div>

            <div class="row mt-2">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header" >
                            <h3 class="card-title d-inline-block text-white"><i class="fa fa-calendar-check-o px-2"
                                    style="font-size:20px"></i> All Treatment </h3>
                            <button class="btn btn-rounded btn-hdr" id="exportButton">
                                <i class="fa fa-download"></i> <span class="hdr-btn-text">Export</span>
                            </button>
                            @if (app('hasPermission')(7, 'create'))
                                <a href="{{ route('treatment.create') }}" class="btn  btn-rounded float-right exportbtn">
                                    <i class="fa fa-plus"></i> <span class="hdr-btn-text">Add</span>
                                </a>
                            @endif
                        </div>
                        <div class="card-body ">
                            <div class="table-responsive">
                                <div id="demo_info" class="box"></div>
                                <table id="treatmenttbl" class="table">
                                    <thead>
                                        <tr>

                                            <th>Name</th>
                                            <th>Doctor</th>
                                            <th class="d-none d-md-table-cell">Price</th>

                                            <th class="d-none d-md-table-cell">Description</th>
                                            <th class="d-none d-md-table-cell">Actions</th>
                                            <th class="d-table-cell d-md-none">Details</th>
                                        </tr>
                                    </thead>
                                    <tbody id="treatmentTableBody"></tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css">
    <script src=" https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>
    <script>
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
    </script>
@endsection
