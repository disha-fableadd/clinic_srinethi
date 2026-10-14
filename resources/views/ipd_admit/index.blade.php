@extends('layout.app')
<style>
    .swal-confirm-btn {
        color: rgb(58, 58, 58) !important;
    }

    /* Cancel button text white (for visibility on red background) */
    .swal-cancel-btn {
        color: white !important;
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
</style>
@section('content')
    <div class="page-wrapper">
        <div class="content" style="height:100vh">
            <div class="row" style="padding-top:15px"></div>
            <div class="row mt-2">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title d-inline-block text-white">
                                <i class="fa fa-hospital px-2" style="font-size:20px"></i>All IPD Admissions
                            </h3>
                            <button class="btn btn-rounded btn-hdr" id="exportButton">
                                <i class="fa fa-download"></i> <span class="hdr-btn-text">Export</span>
                            </button>
                            @if (app('hasPermission')(25, 'create'))
                                <a href="{{ route('ipd_admit.create') }}" class="btn btn-rounded btn-hdr">
                                    <i class="fa fa-plus"></i> <span class="hdr-btn-text">Add</span>
                                </a>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="ipdtbl" class="table custom-table">
                                    <thead style="background-color:#ff8e6;" class="text-center">
                                        <tr>
                                            <th>Patient</th>
                                            <th>Doctor</th>
                                            <th class="d-none d-md-table-cell">Treatment</th>
                                            <th class="d-none d-md-table-cell">Admission Date</th>
                                            <th class="d-none d-md-table-cell">Action</th>
                                            <th class="d-table-cell d-md-none">Details</th>
                                        </tr>
                                    </thead>
                                    <tbody class="ipdtbl-body"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>
    <script>
        $('#exportButton').on('click', function() {
            let branchId = localStorage.getItem('selectedBranchId');
            if (!branchId) {
                alert("Please select a branch first.");
                return;
            }
            window.location.href = "{{ route('ipd.export') }}" + "?branch_id=" + branchId;
        });

        $(document).ready(function() {
            let branchId = localStorage.getItem('selectedBranchId');
            let token = localStorage.getItem("authToken");

            function renderPatientCell(ipd) {
                let patient = ipd?.patient ?? {};
                let patientImage = patient.profile || 'default.png';
                let patientName = patient.fullname || 'N/A';
                return `
                    <div class="view-ipd" data-id="${ipd.id}" style="cursor:pointer; display:flex; align-items:center;">
                        <img width="40" height="40" src="${patientImage}" class="rounded-circle" alt="">
                        <span>${patientName}</span>
                    </div>
                `;
            }

            function renderDoctorCell(ipd) {
                let doctor = ipd?.doctor ?? {};
                let doctorImage = doctor.profile || 'default.png';
                let doctorName = doctor.fullname || 'N/A';
                return `
                    <div class="view-ipd" data-id="${ipd.id}" style="cursor:pointer; display:flex; align-items:center;">
                        <img width="40" height="40" src="${doctorImage}" class="rounded-circle" alt="">
                        <span>${doctorName}</span>
                    </div>
                `;
            }

            function renderTreatment(ipd) {
                return ipd?.treatment?.name || 'N/A';
            }

            function renderActions(ipd) {
                return `
                    <div class="icon" style="cursor:pointer">
                        @if (app('hasPermission')(2, 'view'))
                            <i class="fa fa-eye m-r-5 icon3 view-ipd" data-id="${ipd.id}"></i>
                        @endif
                        @if (app('hasPermission')(2, 'update'))
                            <i class="fa fa-pencil m-r-5 icon1 edit-ipd" data-id="${ipd.id}"></i>
                        @endif
                        @if (app('hasPermission')(2, 'delete'))
                            <i class="fa fa-trash-o m-r-5 icon2 delete-ipd" data-id="${ipd.id}"></i>
                        @endif
                    </div>
                `;
            }

            function initIpdTable() {
                if ($.fn.DataTable.isDataTable("#ipdtbl")) {
                    try {
                        let existingTable = $('#ipdtbl').DataTable();
                        existingTable.destroy();
                        $('#ipdtbl').removeClass('dataTable');
                        $('#ipdtbl tbody').empty();
                        $.removeData($('#ipdtbl')[0], 'DataTable');
                        $.removeData($('#ipdtbl')[0], 'DataTables_DataTable');
                        $('#ipdtbl').off();
                    } catch (e) {
                        $('#ipdtbl tbody').empty();
                        $.removeData($('#ipdtbl')[0]);
                    }
                }

                if ($('#ipdtbl tbody').length === 0) {
                    $('#ipdtbl').append('<tbody class="ipdtbl-body"></tbody>');
                }

                setTimeout(function () {
                    $('#ipdtbl').DataTable({
                        processing: true,
                        serverSide: true,
                        retrieve: true,
                        destroy: true,
                        paging: true,
                        searching: true,
                        ordering: true,
                        responsive: true,
                        autoWidth: false,
                        ajax: function (data, callback) {
                            const page = Math.floor(data.start / data.length) + 1;
                            const perPage = data.length;
                            $.ajax({
                                url: "/api/ipd-admissions",
                                type: "GET",
                                data: {
                                    branch_id: branchId,
                                    page: page,
                                    per_page: perPage,
                                    search: data.search?.value || ''
                                },
                                dataType: "json",
                                headers: { "Authorization": "Bearer " + token },
                                success: function(response) {
                                    const rows = response.ipd_admissions || response.data || [];
                                    callback({
                                        draw: data.draw,
                                        recordsTotal: response.pagination?.total || response.recordsTotal || rows.length || 0,
                                        recordsFiltered: response.pagination?.total || response.recordsFiltered || rows.length || 0,
                                        data: rows
                                    });
                                },
                                error: function(xhr) {
                                    console.error("Failed to fetch IPD admissions:", xhr.responseText);
                                    callback({
                                        draw: data.draw,
                                        recordsTotal: 0,
                                        recordsFiltered: 0,
                                        data: []
                                    });
                                }
                            });
                        },
                        columns: [
                            {
                                data: null,
                                render: function (data, type, row) {
                                    return renderPatientCell(row);
                                }
                            },
                            {
                                data: null,
                                render: function (data, type, row) {
                                    return renderDoctorCell(row);
                                }
                            },
                            {
                                data: null,
                                render: function (data, type, row) {
                                    return `<span class="view-ipd" data-id="${row.id}" style="cursor:pointer;">${renderTreatment(row)}</span>`;
                                },
                                className: "d-none d-md-table-cell"
                            },
                            {
                                data: "admission_date",
                                render: function (data, type, row) {
                                    return `<span class="view-ipd" data-id="${row.id}" style="cursor:pointer;">${data || 'N/A'}</span>`;
                                },
                                className: "d-none d-md-table-cell"
                            },
                            {
                                data: "id",
                                render: function (data, type, row) {
                                    return renderActions(row);
                                },
                                className: "d-none d-md-table-cell",
                                orderable: false
                            },
                            {
                                data: null,
                                render: function () {
                                    return `
                                        <button class="expand-btn">
                                            <i class="fa fa-chevron-down"></i>
                                        </button>
                                    `;
                                },
                                className: "d-table-cell d-md-none text-center",
                                orderable: false
                            }
                        ]
                    });
                }, 50);
            }

            initIpdTable();

            // Expand/collapse logic for mobile
            $(document).on('click', '.expand-btn', function() {
                let btn = $(this);
                let icon = btn.find('i');
                let tr = btn.closest('tr');

                if (!$.fn.DataTable.isDataTable('#ipdtbl')) {
                    return;
                }

                let table = $('#ipdtbl').DataTable();
                let row = table.row(tr);
                let data = row.data();
                if (!data) return;

                let nextRow = tr.next('.details-row');
                if (nextRow.length) {
                    nextRow.slideToggle(300);
                    icon.toggleClass('fa-chevron-down fa-chevron-up');
                    return;
                }

                let treatment = renderTreatment(data);
                let admissionDate = data?.admission_date || 'N/A';
                let actions = renderActions(data);

                let detailsRow = $(`
                    <tr class="details-row">
                        <td colspan="5">
                            <div class="details-content">
                                <div><strong>Treatment:</strong> ${treatment}</div>
                                <div><strong>Admission Date:</strong> ${admissionDate}</div>
                                <div class="mt-2"><strong>Actions:</strong> ${actions}</div>
                            </div>
                        </td>
                    </tr>
                `);

                tr.after(detailsRow);
                icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
            });

            // View IPD
            $(document).on('click', '.view-ipd', function() {
                let id = $(this).data('id');
                window.location.href = '/ipd_admit/show/' + id;
            });

            // Edit IPD
            $(document).on('click', '.edit-ipd', function() {
                let id = $(this).data('id');
                window.location.href = '/ipd_admit/edit/' + id;
            });

            // Delete IPD
            $(document).on('click', '.delete-ipd', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won’t be able to revert this!",
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
                            url: '/api/ipd-admissions/' + id,
                            type: 'DELETE',
                            headers: {
                                "Authorization": "Bearer " + token
                            },
                            success: function() {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'The record has been deleted.',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    if ($.fn.DataTable.isDataTable("#ipdtbl")) {
                                        $('#ipdtbl').DataTable().ajax.reload(null, false);
                                    }
                                });
                            },
                            error: function() {
                                Swal.fire('Error', 'Failed to delete the record.',
                                    'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
