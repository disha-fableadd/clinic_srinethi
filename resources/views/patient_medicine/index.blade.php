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

    #patientMedicinetbl td {
        vertical-align: middle;
    }

    .details-row {
        background-color: #f8f9fa;
    }

    .details-content {
        padding: 10px;
        border-left: 3px solid #f89884;
        margin-left: 10px;
    }

    button.expand-btn {
        background: #f89884;
        color: white;
        padding: 6px 8px;
        border-radius: 8px;
        border: none;
    }
      .card-header{
        background-color:#f89884 !important; 
    }
    .btn-rounded{
        background-color: #fed9cf !important;
    }

    @media screen and (max-width:767px) {

        .page-title {
            font-size: 19px !important;
            padding-left: 10px !important;
            text-align: left !important;
        }

        .report-icon {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding-top: 15px !important;
        }

        .report-icon .download {
            margin-left: 5px !important;
        }

    }

    @media (min-width: 768px) {
        .d-table-cell.d-md-none {
            display: none !important;
        }

        .details-row {
            display: none !important;
        }
    }

    /* Hide desktop columns on mobile */
    @media (max-width: 767px) {
        .d-none.d-md-table-cell {
            display: none !important;
        }
    }
</style>
@section('content')
    <div class="page-wrapper">
        <div class="content" style="height:100vh">
            <div class="row" style="padding-top:15px">

            </div>

            <div class="row mt-2">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title d-inline-block text-white"><i class="fa fa-calendar-check-o px-2"
                                    style="font-size:20px"></i>All Patients Medicine </h3>

                            <button class="btn btn-rounded btn-hdr" id="exportButton">
                                <i class="fa fa-download"></i> <span class="hdr-btn-text">Export</span>
                            </button>
                            @if (app('hasPermission')(17, 'create'))
                                <a href="{{ route('patient_medicine.create') }}" class="btn btn-rounded btn-hdr">
                                    <i class="fa fa-plus"></i> <span class="hdr-btn-text">Add</span>
                                </a>
                            @endif

                        </div>
                        <div class="card-body ">
                            <div class="table-responsive">
                                <div id="demo_info" class="box"></div>
                                <table id="patientMedicinetbl" class="table">
                                    <thead>
                                        <tr>

                                            <th>Patient</th>
                                            <th>Treatment</th>
                                            <th class="d-none d-md-table-cell">Medicine</th>
                                            <th class="d-none d-md-table-cell">Description</th>
                                            <th class="d-none d-md-table-cell">Code</th>
                                            <th class="d-none d-md-table-cell">Value</th>
                                            <th class="d-none d-md-table-cell">Value Type</th>
                                            <th class="d-none d-md-table-cell">Actions</th>
                                            <th class="d-table-cell d-md-none">Details</th>
                                        </tr>
                                    </thead>
                                    <tbody id="patientMedicinetable"></tbody>
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
    <!-- Bootstrap Bundle JS (includes Popper.js) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).on('click', '#exportButton', function() {
            let branchId = localStorage.getItem('selectedBranchId');
            if (!branchId) {
                alert("Branch ID not found in localStorage!");
                return;
            }
            window.location.href = "{{ route('patientmedicine.export') }}" + "?branch_id=" + branchId;
        });

        // Expand/Collapse details for mobile
        $(document).on('click', '.expand-btn', function() {
            const $btn = $(this);
            const $row = $btn.closest('tr');

            // Toggle if already open
            if ($row.next().hasClass('details-row')) {
                $row.next().toggle();
                $btn.find('i').toggleClass('fa-chevron-down fa-chevron-up');
                return;
            }

            // Extract data
            const patientName = $row.find('td').eq(0).text().trim();
            const treatment = $row.find('td').eq(1).text().trim();
            const medicines = $row.find('td.d-none.d-md-table-cell').eq(0).text().trim() || 'N/A';
            const description = $row.find('td.d-none.d-md-table-cell').eq(1).text().trim() || 'N/A';
            const code = $row.find('td.d-none.d-md-table-cell').eq(2).text().trim() || 'N/A';
            const value = $row.find('td.d-none.d-md-table-cell').eq(3).text().trim() || 'N/A';
            const valueType = $row.find('td.d-none.d-md-table-cell').eq(4).text().trim() || 'N/A';
            const recordId = $row.find('[data-id]').first().data('id');

            // ✅ Create details row including same action icons
            const detailsRow = `
        <tr class="details-row">
            <td colspan="6">
                <div class="details-content">

                    <p><strong>Medicines:</strong> ${medicines}</p>
                    <p><strong>Description:</strong> ${description}</p>
                    <p><strong>Code:</strong> ${code}</p>
                    <p><strong>Value:</strong> ${value}</p>
                    <p><strong>Value Type:</strong> ${valueType}</p>

                    <div class="icon mt-2 text-center" style="font-size:18px;">
                        @if (app('hasPermission')(17, 'view'))
                            <i class="fa fa-eye m-r-5 icon3 view-patientMedicine" data-id="${recordId}" title="View"></i>
                        @endif
                        @if (app('hasPermission')(17, 'update'))
                            <i class="fa fa-pencil m-r-5 icon1 edit-patientMedicine" data-id="${recordId}" title="Edit"></i>
                        @endif
                        @if (app('hasPermission')(17, 'delete'))
                            <i class="fa fa-trash-o m-r-5 icon2 delete-patientMedicine" data-id="${recordId}" title="Delete"></i>
                        @endif
                        @if (app('hasPermission')(17, 'view'))
                            <i class="fa fa-download m-r-5 icon4 download-medicine" data-id="${recordId}" title="Download PDF"></i>
                        @endif
                    </div>
                </div>
            </td>
        </tr>
    `;

            // Insert below current row
            $row.after(detailsRow);

            // Change icon
            $btn.find('i').removeClass('fa-chevron-down').addClass('fa-chevron-up');
        });

        $(document).ready(function() {
            function ucfirst(str) {
                if (!str) return 'N/A';
                return str.charAt(0).toUpperCase() + str.slice(1);
            }
            let branchId = localStorage.getItem('selectedBranchId');
            $.ajax({
                url: '/api/patient-medicines',
                type: 'GET',
                data: {
                    branch_id: branchId
                },
                dataType: 'json',
                headers: {
                    "Authorization": "Bearer " + token
                },
                beforeSend: function() {
                    // Show loader row in tbody
                    $('#patientMedicinetable').html(`
                    <tr>
                        <td colspan="6" class="text-center">
                            <span class="s"></span> Loading...
                        </td>
                    </tr>
                `);
                },
                success: function(data) {
//console.log("API Response:", data);

                    if (!Array.isArray(data) || data.length === 0) {
                        console.warn("No data found!");


                    }


                    if ($.fn.DataTable.isDataTable("#patientMedicinetbl")) {
                        $('#patientMedicinetbl').DataTable().destroy();
                    }

                    var tableBody = $('#patientMedicinetable');
                    tableBody.empty();
                    let defaultImage = "{{ asset(env('IMAGE_PATH') . 'admin/assets/img/img1.png') }}";

                    data.forEach(function(patientMedicine, index) {

                        var APP_ENV = "{{ app()->environment() }}";
                        var BASE_URL = "{{ url('/') }}/";
                        let imageUrl = '';

                        if (patientMedicine.profile) {
                            let cleanPath = patientMedicine.profile.replace(/^\/?/, '');
                            if (APP_ENV === 'production' && !cleanPath.startsWith('public/')) {
                                cleanPath = 'public/' + cleanPath;
                            }
                            imageUrl = BASE_URL + cleanPath;
                        } else {
                            imageUrl =
                                "{{ asset(env('IMAGE_PATH') . 'admin/assets/img/img1.png') }}";
                        }


                        let medicines = (Array.isArray(patientMedicine.medicines) &&
                                patientMedicine.medicines.length > 0) ?
                            patientMedicine.medicines.map(med => ucfirst(med)).join(', ') :
                            'N/A';

                        $('#patientMedicinetbl tbody').append(`
                                              <tr>
                                                <td style="cursor:pointer" class="view-patientMedicine" data-id="${patientMedicine.id}">
                                                    <img src="${imageUrl}" alt="Patient Image" style="width: 50px; height: 50px; border-radius: 50%;">
                                                    ${ucfirst(patientMedicine.patient_name) ?? 'N/A'}
                                                </td>
                                                <td style="cursor:pointer" class="view-patientMedicine" data-id="${patientMedicine.id}">
                                                    ${ucfirst(patientMedicine.treatment_name) ?? 'N/A'}
                                                </td>
                                                <td style="cursor:pointer" class="d-none d-md-table-cell view-patientMedicine" data-id="${patientMedicine.id}">
                                                    ${medicines}
                                                </td>
                                                <td style="cursor:pointer" class="d-none d-md-table-cell view-patientMedicine" data-id="${patientMedicine.id}">
                                                    ${ucfirst(patientMedicine.note) ?? 'N/A'}
                                                </td>
                                                 <td style="cursor:pointer" class="d-none d-md-table-cell view-patientMedicine" data-id="${patientMedicine.id}">
                                                    ${ucfirst(patientMedicine.codes) ?? 'N/A'}
                                                </td>
                                                 <td style="cursor:pointer" class="d-none d-md-table-cell view-patientMedicine" data-id="${patientMedicine.id}">
                                                    ${ucfirst(patientMedicine.values) ?? 'N/A'}
                                                </td>
                                                 <td style="cursor:pointer" class="d-none d-md-table-cell view-patientMedicine" data-id="${patientMedicine.id}">
                                                    ${ucfirst(patientMedicine.value_types) ?? 'N/A'}
                                                </td>

                                                <td class="align-content-center d-none d-md-table-cell">
                                                    <div class="icon" style="cursor:pointer">
                                                        @if (app('hasPermission')(17, 'view'))
                                                            <i class="fa fa-eye m-r-5 icon3 view-patientMedicine" data-id="${patientMedicine.id}"></i>
                                                        @endif
                                                        @if (app('hasPermission')(17, 'update'))
                                                            <i class="fa fa-pencil m-r-5 icon1 edit-patientMedicine" data-id="${patientMedicine.id}"></i>
                                                        @endif
                                                        @if (app('hasPermission')(17, 'delete'))
                                                            <i class="fa fa-trash-o m-r-5 icon2 delete-patientMedicine" data-id="${patientMedicine.id}"></i>
                                                        @endif
                                                        @if (app('hasPermission')(17, 'view'))
                                                            <i class="fa fa-download m-r-5 icon4 download-medicine" title="Download PDF" data-id="${patientMedicine.id}"></i>
                                                        @endif
                                                    </div>
                                                </td>
                                                <!-- ✅ Added this final column for Details -->
                                                <td class="d-table-cell d-md-none text-center">
                                                    <button class="expand-btn"><i class="fa fa-chevron-down"></i></button>
                                                </td>
                                            </tr>
                                        `);
                    });



                    $('#patientMedicinetbl').DataTable({
                        paging: true,
                        searching: true,
                        ordering: true,
                        destroy: true
                    });

                    // Reinitialize Bootstrap dropdowns (for dynamically added content)
                    var dropdownTriggerList = [].slice.call(document.querySelectorAll(
                        '[data-bs-toggle="dropdown"]'));
                    dropdownTriggerList.map(function(dropdownTriggerEl) {
                        return new bootstrap.Dropdown(dropdownTriggerEl);
                    });

                },
                error: function(xhr) {
                    console.error("AJAX Error:", xhr.responseText);
                }
            });
        });



        $(document).on('click', '.edit-patientMedicine', function() {
            var patientmedicineId = $(this).data('id');
            window.location.href = '/patientmedicine/edit/' + patientmedicineId;
        });
        $(document).on('click', '.view-patientMedicine', function() {
            var patientmedicineId = $(this).data('id');
            window.location.href = '/patientmedicine/show/' + patientmedicineId;
        });


        $(document).on('click', '.download-medicine', function() {
            const id = $(this).data('id');

            $.ajax({
                url: `/api/medicines/${id}/download`,
                type: 'GET',
                xhrFields: {
                    responseType: 'blob' // so file download works
                },
                success: function(data, status, xhr) {
                    // ✅ Create a download link dynamically
                    const blob = new Blob([data], {
                        type: xhr.getResponseHeader('Content-Type')
                    });
                    const link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = `medicine_${id}.pdf`;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                },
                error: function(xhr) {
                    if (xhr.status === 404) {
                        Swal.fire({
                            icon: 'error',
                            title: 'File Not Found',
                            text: 'The requested medicine PDF does not exist.',
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Download Failed',
                            text: 'Something went wrong while downloading the file.',
                        });
                    }
                }
            });
        });

        $(document).on('click', '.delete-patientMedicine', function() {
            var patientmedicineId = $(this).data('id');


            Swal.fire({
                title: 'Are you sure?',
                text: "You want to delete Patientmedicine!",
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
                        url: '/api/patient-medicines/' + patientmedicineId,
                        type: 'DELETE',
                        // //headers: { "Authorization": "Bearer " + token },
                        success: function(response) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Patientmedicine deleted successfully!',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload(); // Reload page to reflect changes
                            });
                        },
                        error: function(xhr) {
                            Swal.fire('Error', xhr.responseJSON?.message ||
                                'Failed to delete Patientmedicine. Please try again.',
                                'error');
                        }
                    });
                }
            });
        });
    </script>
@endsection
