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

    .card-header {
        background-color: #f89884 !important;
    }

    .btn-rounded {
        background-color: #fed9cf !important;
    }

    colgroup {
        display: none;
    }

    @media screen and (max-width:767px) {
        .page-title {
            font-size: 20px !important;
            padding-left: 7px !important;
        }

        .d-none.d-md-table-cell {
            display: none !important;
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
                                <i class="fa fa-hospital-o px-2" style="font-size:20px"></i> All Services
                            </h3>
                            <button class="btn btn-rounded btn-hdr" id="exportButton">
                                <i class="fa fa-download"></i> <span class="hdr-btn-text">Export</span>
                            </button>
                            @if (app('hasPermission')(8, 'create'))
                                <a href="{{ route('service.create') }}" class="btn btn-rounded btn-hdr">
                                    <i class="fa fa-plus"></i> <span class="hdr-btn-text">Add</span>
                                </a>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <div id="demo_info" class="box"></div>
                                <table id="servicetbl" class="table custom-table">
                                    <thead style="background-color:#ff8e29;" class="text-center">
                                        <tr>
                                            <th>Patient</th>
                                            <th>Department</th>
                                            <th class="d-none d-md-table-cell">Service</th>
                                            <th class="d-none d-md-table-cell">Action</th>
                                            <th class="d-table-cell d-md-none">Details</th>
                                        </tr>
                                    </thead>
                                    <tbody id="servicesTableBody">
                                        <!-- Data will be loaded dynamically here -->
                                    </tbody>
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
        $(document).on('click', '#exportButton', function() {
            let branchId = localStorage.getItem('selectedBranchId');
            if (!branchId) {
                alert("Please select a branch first.");
                return;
            }
            window.location.href = "{{ route('service.export') }}" + "?branch_id=" + branchId;
        });

        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('toggle_btn');
            const sidebar = document.querySelector('.sidebar');
            if (toggleBtn && sidebar) {
                toggleBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('mini-sidebar');
                });
            }
        });

        $(document).ready(function() {
            function ucfirst(str) {
                if (!str) return 'N/A';
                return str.charAt(0).toUpperCase() + str.slice(1);
            }

            let branchId = localStorage.getItem('selectedBranchId');
            let userId = {{ auth()->user()->id }};
            let token = localStorage.getItem("authToken");

            function getServiceName(service) {
                if (service?.department === "Pathology" && service?.pathelogy_service) {
                    return service.pathelogy_service.test_name ?? 'N/A';
                }
                if (service?.department === "Radiology" && service?.radiology_service) {
                    return service.radiology_service.test_name ?? 'N/A';
                }
                return 'N/A';
            }

            function initServiceTable() {
                if ($.fn.DataTable.isDataTable("#servicetbl")) {
                    try {
                        let existingTable = $('#servicetbl').DataTable();
                        existingTable.destroy();
                        $('#servicetbl').removeClass('dataTable');
                        $('#servicetbl tbody').empty();
                        $.removeData($('#servicetbl')[0], 'DataTable');
                        $.removeData($('#servicetbl')[0], 'DataTables_DataTable');
                        $('#servicetbl').off();
                    } catch (e) {
                        $('#servicetbl tbody').empty();
                        $.removeData($('#servicetbl')[0]);
                    }
                }

                if ($('#servicetbl tbody').length === 0) {
                    $('#servicetbl').append('<tbody id="servicesTableBody"></tbody>');
                }

                setTimeout(function () {
                    $('#servicetbl').DataTable({
                        processing: true,
                        serverSide: true,
                        retrieve: true,
                        destroy: true,
                        paging: true,
                        searching: true,
                        ordering: true,
                        ajax: function (data, callback) {
                            const page = Math.floor(data.start / data.length) + 1;
                            const perPage = data.length;
                            $.ajax({
                                url: '/api/services',
                                type: 'GET',
                                data: {
                                    branch_id: branchId,
                                    page: page,
                                    per_page: perPage,
                                    search: data.search?.value || ''
                                },
                                headers: { "Authorization": "Bearer " + token },
                                success: function (response) {
                                    let rows = response.data || response || [];
                                    rows = rows.filter(service => service.user_id == userId);
                                    callback({
                                        draw: data.draw,
                                        recordsTotal: response.pagination?.total || response.recordsTotal || rows.length || 0,
                                        recordsFiltered: response.pagination?.total || response.recordsFiltered || rows.length || 0,
                                        data: rows
                                    });
                                },
                                error: function (xhr) {
                                    console.error(xhr.responseText);
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
                                    return `<span class="view-service" data-id="${row.id}" style="cursor:pointer;">${ucfirst(row.patient?.fullname || 'N/A')}</span>`;
                                }
                            },
                            {
                                data: "department",
                                render: function (data, type, row) {
                                    return `<span class="view-service" data-id="${row.id}" style="cursor:pointer;">${ucfirst(data)}</span>`;
                                }
                            },
                            {
                                data: null,
                                render: function (data, type, row) {
                                    return `<span class="view-service" data-id="${row.id}" style="cursor:pointer;">${getServiceName(row)}</span>`;
                                },
                                className: "d-none d-md-table-cell"
                            },
                            {
                                data: "id",
                                render: function (data, type, row) {
                                    return `
                                        <div class="icon" style="cursor:pointer">
                                            @if (app('hasPermission')(8, 'view'))
                                                <i class="fa fa-eye m-r-5 icon3 view-service" data-id="${row.id}" title="View"></i>
                                            @endif
                                            @if (app('hasPermission')(8, 'update'))
                                                <i class="fa fa-pencil m-r-5 icon1 edit-service" data-id="${row.id}" title="Edit"></i>
                                            @endif
                                            @if (app('hasPermission')(8, 'delete'))
                                                <i class="fa fa-trash-o m-r-5 icon2 delete-service" data-id="${row.id}" title="Delete"></i>
                                            @endif
                                        </div>
                                    `;
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

            initServiceTable();

            // Expand/collapse logic for mobile
            $(document).on('click', '.expand-btn', function() {
                let btn = $(this);
                let icon = btn.find('i');
                let tr = btn.closest('tr');

                if (!$.fn.DataTable.isDataTable('#servicetbl')) {
                    return;
                }

                let table = $('#servicetbl').DataTable();
                let row = table.row(tr);
                let data = row.data();
                if (!data) return;

                let nextRow = tr.next('.details-row');
                if (nextRow.length) {
                    nextRow.slideToggle(300);
                    icon.toggleClass('fa-chevron-down fa-chevron-up');
                    return;
                }

                let department = ucfirst(data.department);
                let service = getServiceName(data);
                let actions = `
                    <div class="icon" style="cursor:pointer">
                        @if (app('hasPermission')(8, 'view'))
                            <i class="fa fa-eye m-r-5 icon3 view-service" data-id="${data.id}" title="View"></i>
                        @endif
                        @if (app('hasPermission')(8, 'update'))
                            <i class="fa fa-pencil m-r-5 icon1 edit-service" data-id="${data.id}" title="Edit"></i>
                        @endif
                        @if (app('hasPermission')(8, 'delete'))
                            <i class="fa fa-trash-o m-r-5 icon2 delete-service" data-id="${data.id}" title="Delete"></i>
                        @endif
                    </div>
                `;

                let detailsRow = $(`
                    <tr class="details-row">
                        <td colspan="5">
                            <div class="details-content">
                                <div><strong>Department:</strong> ${department}</div>
                                <div><strong>Service:</strong> ${service}</div>
                                <div class="mt-2"><strong>Actions:</strong> ${actions}</div>
                            </div>
                        </td>
                    </tr>
                `);

                tr.after(detailsRow);
                icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
            });

            // Delete service
            $(document).on('click', '.delete-service', function() {
                var serviceId = $(this).data('id');
                if (!serviceId) {
                    Swal.fire('Error', 'Service ID is missing!', 'error');
                    return;
                }
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
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
                            url: '/api/services/' + serviceId,
                            type: 'DELETE',
                            success: function(response) {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'Service deleted successfully!',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire('Error', 'Failed to delete service.',
                                    'error');
                            }
                        });
                    }
                });
            });

            // View service
            $(document).on('click', '.view-service', function() {
                var serviceId = $(this).data('id');
                window.location.href = '/service/show/' + serviceId;
            });

            // Edit service
            $(document).on('click', '.edit-service', function() {
                var serviceId = $(this).data('id');
                window.location.href = '/service/edit/' + serviceId;
            });
        });
    </script>
@endsection
