@extends('layout.app')
<style>
    .swal-confirm-btn {
        color: rgb(58, 58, 58) !important;
    }

    /* Cancel button text white (for visibility on red background) */
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
                            <h3 class="card-title d-inline-block text-white">
                                <i class="fa fa-file-text px-2" style="font-size:20px"></i> All SOAP Records
                            </h3>
                            <button class="btn btn-rounded btn-hdr" id="soapExportButton">
                                <i class="fa fa-download"></i> <span class="hdr-btn-text">Export</span>
                            </button>
                            @if (app('hasPermission')(32, 'create'))
                                <a href="{{ route('soap.create') }}" class="btn btn-rounded btn-hdr">
                                    <i class="fa fa-plus"></i> <span class="hdr-btn-text">Add</span>
                                </a>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="soaptbl" class="table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Patient</th>
                                            <th class="d-none d-md-table-cell">Title</th>
                                            <th class="d-none d-md-table-cell">Description</th>
                                            <th class="d-none d-md-table-cell">Actions</th>
                                            <th class="d-table-cell d-md-none">Details</th> <!-- Mobile-only column -->
                                        </tr>
                                    </thead>
                                    <tbody id="soapTableBody"></tbody>
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
    $(document).on('click', '#soapExportButton', function() {
        let branchId = localStorage.getItem('selectedBranchId');
        if (!branchId) {
            alert("Please select a branch first.");
            return;
        }
        window.location.href = "{{ route('soap.export') }}" + "?branch_id=" + branchId;
    });

    $(document).ready(function() {
        const token = localStorage.getItem('token');
        let branchId = localStorage.getItem('selectedBranchId');
        let soapTable;

        function ucfirst(str) {
            if (!str) return 'N/A';
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        function buildTitles(soap) {
            const titles = [];
            if (soap.subjective?.title) titles.push(`<strong>Subjective : </strong> ${ucfirst(soap.subjective.title)}`);
            if (soap.objective?.title) titles.push(`<strong>Objective : </strong> ${ucfirst(soap.objective.title)}`);
            if (soap.assessment?.title) titles.push(`<strong>Assessment : </strong> ${ucfirst(soap.assessment.title)}`);
            if (soap.plan?.title) titles.push(`<strong>Plan : </strong> ${ucfirst(soap.plan.title)}`);
            return titles.join('<br>') || 'N/A';
        }

        function buildDescriptions(soap) {
            const descriptions = [];
            if (soap.subjective?.description) descriptions.push(`<strong>Subjective : </strong> ${ucfirst(soap.subjective.description)}`);
            if (soap.objective?.description) descriptions.push(`<strong>Objective : </strong> ${ucfirst(soap.objective.description)}`);
            if (soap.assessment?.description) descriptions.push(`<strong>Assessment : </strong> ${ucfirst(soap.assessment.description)}`);
            if (soap.plan?.description) descriptions.push(`<strong>Plan : </strong> ${ucfirst(soap.plan.description)}`);
            return descriptions.join('<br>') || 'N/A';
        }

        function initSoapTable() {
            if ($.fn.DataTable.isDataTable('#soaptbl')) {
                try {
                    let existingTable = $('#soaptbl').DataTable();
                    existingTable.destroy();
                    $('#soaptbl').removeClass('dataTable');
                    $('#soaptbl tbody').empty();
                    $.removeData($('#soaptbl')[0], 'DataTable');
                    $.removeData($('#soaptbl')[0], 'DataTables_DataTable');
                    $('#soaptbl').off();
                } catch (e) {
                    $('#soaptbl tbody').empty();
                    $.removeData($('#soaptbl')[0]);
                }
            }

            if ($('#soaptbl tbody').length === 0) {
                $('#soaptbl').append('<tbody id="soapTableBody"></tbody>');
            }

            setTimeout(function() {
                soapTable = $('#soaptbl').DataTable({
                    processing: true,
                    serverSide: true,
                    retrieve: true,
                    destroy: true,
                    ajax: function(data, callback) {
                        const page = Math.floor(data.start / data.length) + 1;

                        $.ajax({
                            url: '/api/soap',
                            type: 'GET',
                            dataType: 'json',
                            data: {
                                branch_id: branchId,
                                page: page,
                                per_page: data.length,
                                search: data.search?.value || ''
                            },
                            headers: {
                                "Authorization": "Bearer " + token
                            },
                            success: function(json) {
                                callback({
                                    draw: data.draw,
                                    recordsTotal: json.pagination?.total || 0,
                                    recordsFiltered: json.pagination?.total || 0,
                                    data: json.data || []
                                });
                            },
                            error: function(xhr) {
                                console.error('Failed to load soap records', xhr.responseText);
                                callback({
                                    draw: data.draw,
                                    recordsTotal: 0,
                                    recordsFiltered: 0,
                                    data: []
                                });
                            }
                        });
                    },
                    columns: [{
                            data: 'date',
                            render: data => data ?? 'N/A',
                            className: 'view-soap'
                        },
                        {
                            data: 'patient',
                            render: function(data, type, row) {
                                return row?.patient?.fullname ?? 'N/A';
                            },
                            className: 'view-soap'
                        },
                        {
                            data: null,
                            render: function(data, type, row) {
                                return buildTitles(row);
                            },
                            className: 'd-none d-md-table-cell view-soap'
                        },
                        {
                            data: null,
                            render: function(data, type, row) {
                                return buildDescriptions(row);
                            },
                            className: 'd-none d-md-table-cell view-soap'
                        },
                        {
                            data: 'id',
                            render: function(data) {
                                return `
                                    <div class="icon" style="cursor:pointer">
                                        @if (app('hasPermission')(32, 'view'))
                                            <i class="fa fa-eye m-r-5 icon3 view-soap" title="Show" data-id="${data}"></i>
                                        @endif
                                        @if (app('hasPermission')(32, 'update'))
                                            <i class="fa fa-pencil m-r-5 icon1 edit-soap" title="Edit" data-id="${data}"></i>
                                        @endif
                                        @if (app('hasPermission')(32, 'delete'))
                                            <i class="fa fa-trash-o m-r-5 icon2 delete-soap" title="Delete" data-id="${data}"></i>
                                        @endif
                                        @if (app('hasPermission')(32, 'view'))
                                            <i class="fa fa-download m-r-5 icon4 download-soap" title="Download PDF" data-id="${data}"></i>
                                        @endif
                                    </div>
                                `;
                            },
                            className: 'd-none d-md-table-cell'
                        },
                        {
                            data: null,
                            render: function() {
                                return `
                                    <button class="btn btn-link expand-btn">
                                        <i class="fa fa-chevron-down"></i>
                                    </button>
                                `;
                            },
                            className: 'd-table-cell d-md-none text-center',
                            orderable: false
                        }
                    ],
                    order: [
                        [0, 'desc']
                    ],
                    pageLength: 10
                });
                window.soapTable = soapTable;
            }, 50);
        }

        function reloadSoapTable() {
            if (!soapTable) {
                initSoapTable();
                return;
            }
            soapTable.ajax.reload(null, false);
        }

        initSoapTable();

        // View SOAP
        $(document).on('click', '.view-soap', function() {
            var soapId = $(this).data('id');
            if (!soapId) return;
            window.location.href = '/soap/show/' + soapId;
        });

        // Edit SOAP
        $(document).on('click', '.edit-soap', function() {
            var soapId = $(this).data('id');
            if (!soapId) return;
            window.location.href = '/soap/edit/' + soapId;
        });

        // Download SOAP
        $(document).on('click', '.download-soap', function() {
            const soapId = $(this).data('id');
            $.ajax({
                url: `/api/soaps/${soapId}/download`,
                type: 'GET',
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(data, status, xhr) {
                    const blob = new Blob([data], {
                        type: xhr.getResponseHeader('Content-Type')
                    });
                    const link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = `soap_${soapId}.pdf`;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                },
                error: function(xhr) {
                    if (xhr.status === 404) {
                        Swal.fire({
                            icon: 'error',
                            title: 'File Not Found',
                            text: 'The requested SOAP PDF does not exist.',
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Download Failed',
                            text: 'Something went wrong while downloading the SOAP file.',
                        });
                    }
                }
            });
        });

        // Delete SOAP
        $(document).on('click', '.delete-soap', function() {
            var soapId = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to delete soap!",
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
                        url: '/api/soap/' + soapId,
                        type: 'DELETE',
                        headers: {
                            "Authorization": "Bearer " + token
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Soap deleted successfully!',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                reloadSoapTable();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire('Error', xhr.responseJSON?.message ||
                                'Failed to delete soap. Please try again.', 'error');
                        }
                    });
                }
            });
        });

    });

    // Handle expandable rows for mobile
    $(document).on('click', '.expand-btn', function() {
        const btn = $(this);
        const icon = btn.find('i');
        const tr = btn.closest('tr');
        const existingRow = tr.next('.details-row');

        if (existingRow.length) {
            existingRow.slideToggle(300);
            icon.toggleClass('fa-chevron-down fa-chevron-up');
            return;
        }

        if (!window.soapTable || !$.fn.DataTable.isDataTable('#soaptbl')) {
            return;
        }

        const row = window.soapTable.row(tr);
        const data = row.data() || {};

        const title = (function() {
            const titles = [];
            if (data.subjective?.title) titles.push(`<strong>Subjective : </strong> ${data.subjective.title}`);
            if (data.objective?.title) titles.push(`<strong>Objective : </strong> ${data.objective.title}`);
            if (data.assessment?.title) titles.push(`<strong>Assessment : </strong> ${data.assessment.title}`);
            if (data.plan?.title) titles.push(`<strong>Plan : </strong> ${data.plan.title}`);
            return titles.join('<br>') || 'N/A';
        })();

        const description = (function() {
            const descriptions = [];
            if (data.subjective?.description) descriptions.push(`<strong>Subjective : </strong> ${data.subjective.description}`);
            if (data.objective?.description) descriptions.push(`<strong>Objective : </strong> ${data.objective.description}`);
            if (data.assessment?.description) descriptions.push(`<strong>Assessment : </strong> ${data.assessment.description}`);
            if (data.plan?.description) descriptions.push(`<strong>Plan : </strong> ${data.plan.description}`);
            return descriptions.join('<br>') || 'N/A';
        })();

        const actionHtml = `
            <div class="icon" style="cursor:pointer">
                @if (app('hasPermission')(32, 'view'))
                    <i class="fa fa-eye m-r-5 icon3 view-soap" title="Show" data-id="${data.id}"></i>
                @endif
                @if (app('hasPermission')(32, 'update'))
                    <i class="fa fa-pencil m-r-5 icon1 edit-soap" title="Edit" data-id="${data.id}"></i>
                @endif
                @if (app('hasPermission')(32, 'delete'))
                    <i class="fa fa-trash-o m-r-5 icon2 delete-soap" title="Delete" data-id="${data.id}"></i>
                @endif
                @if (app('hasPermission')(32, 'view'))
                    <i class="fa fa-download m-r-5 icon4 download-soap" title="Download PDF" data-id="${data.id}"></i>
                @endif
            </div>
        `;

        const detailsRow = $(`
            <tr class="details-row">
                <td colspan="3">
                    <div class="details-content">
                        <div><strong>Title:</strong> ${title}</div>
                        <div><strong>Description:</strong> ${description}</div>
                        <div class="mt-2"><strong>Actions:</strong> ${actionHtml}</div>
                    </div>
                </td>
            </tr>
        `);

        tr.after(detailsRow);
        icon.toggleClass('fa-chevron-down fa-chevron-up');
    });
</script>

  

@endsection

