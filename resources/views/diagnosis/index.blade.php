@extends('layout.app')

<link rel="stylesheet" href="{{ asset(env('IMAGE_PATH') . 'admin/assets/css/diagnosis-index.css') }}">

@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="row"></div>

            <div class="row mt-2">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title d-inline-block text-white">
                                <i class="fa fa-stethoscope px-2"></i>All Diagnosis
                            </h3>
                            <button class="btn btn-rounded btn-hdr" id="exportButton">
                                <i class="fa fa-download"></i> <span class="hdr-btn-text">Export</span>
                            </button>
                            @if (app('hasPermission')(35, 'create'))
                                <a href="{{ route('diagnosis.create') }}" class="btn btn-rounded btn-hdr">
                                    <i class="fa fa-plus"></i> <span class="hdr-btn-text">Add</span>
                                </a>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <div id="demo_info" class="box"></div>
                                <table id="diagnosistbl" class="table custom-table">
                                    <thead class="text-center">
                                        <tr>
                                            <th>Diagnosis Name</th>
                                            <th class="d-none d-md-table-cell">Description</th>
                                            <th class="d-none d-md-table-cell">Action</th>
                                            <th class="d-table-cell d-md-none">Details</th>
                                        </tr>
                                    </thead>
                                    <tbody class="diagnosis"></tbody>
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
            let branchId = localStorage.getItem('selectedBranchId'); // get selected branch id

            if (!branchId) {
                alert("Please select a branch first.");
                return;
            }

            // Append branchId as query param
            window.location.href = "{{ route('diagnosis.export') }}" + "?branch_id=" + branchId;
        });

        $(document).ready(function() {
            fetchDiagnoses();
        });
        let branchId = localStorage.getItem('selectedBranchId'); // ✅ keep only this
        // let token = localStorage.getItem('token');

        function ucfirst(str) {
            if (!str) return 'N/A';
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        // function fetchDiagnoses() {
        //     let branchId = localStorage.getItem('selectedBranchId');

        //     if ($.fn.DataTable.isDataTable("#diagnosistbl")) {
        //         $('#diagnosistbl').DataTable().destroy();
        //     }

        //     $('#diagnosistbl').DataTable({
        //         processing: true,
        //         serverSide: true,
        //         ajax: function(data, callback) {
        //             const page = Math.floor(data.start / data.length) + 1;
        //             $.ajax({
        //                 url: "{{ url('/api/diagnosis') }}",
        //                 type: "GET",
        //                 data: {
        //                     branch_id: branchId,
        //                     page: page,
        //                     per_page: data.length,
        //                     search: data.search?.value || ''
        //                 },
        //                 dataType: "json",
        //                 headers: {
        //                     "Authorization": "Bearer " + token
        //                 },
        //                 success: function(response) {
        //                     callback({
        //                         draw: data.draw,
        //                         recordsTotal: response.pagination?.total || 0,
        //                         recordsFiltered: response.pagination?.total || 0,
        //                         data: response.diagnoses || []
        //                     });
        //                 },
        //                 error: function() {
        //                     callback({
        //                         draw: data.draw,
        //                         recordsTotal: 0,
        //                         recordsFiltered: 0,
        //                         data: []
        //                     });
        //                 }
        //             });
        //         },
        //         columns: [
        //             { 
        //                 data: 'name',
        //                 render: data => ucfirst(data),
        //                 className: 'view-diagnosis',
        //                 createdCell: function(td, cellData, rowData) {
        //                     $(td).attr('data-id', rowData.id).css('cursor', 'pointer');
        //                 }
        //             },
        //             { 
        //                 data: 'description',
        //                 className: 'd-none d-md-table-cell view-diagnosis',
        //                 render: data => ucfirst(data),
        //                 createdCell: function(td, cellData, rowData) {
        //                     $(td).attr('data-id', rowData.id).css('cursor', 'pointer');
        //                 }
        //             },
        //             {
        //                 data: null,
        //                 className: 'd-none d-md-table-cell',
        //                 orderable: false,
        //                 render: function(data, type, row) {
        //                     let actions = `<div class="icon">`;
        //                     @if (app('hasPermission')(35, 'view'))
        //                         actions += `<i class="fa fa-eye m-r-5 icon3 view-diagnosis" data-id="${row.id}" title="View"></i>`;
        //                     @endif
        //                     @if (app('hasPermission')(35, 'update'))
        //                         actions += `<i class="fa fa-pencil m-r-5 icon1 edit-diagnosis" data-id="${row.id}" title="Edit"></i>`;
        //                     @endif
        //                     @if (app('hasPermission')(35, 'delete'))
        //                         actions += `<i class="fa fa-trash-o m-r-5 icon2 delete-diagnosis" data-id="${row.id}" title="Delete"></i>`;
        //                     @endif
        //                     actions += `</div>`;
        //                     return actions;
        //                 }
        //             },
        //             {
        //                 data: null,
        //                 className: 'd-table-cell d-md-none text-center',
        //                 orderable: false,
        //                 render: function(data, type, row) {
        //                     return `
    //                         <button class="btn btn-link expand-btn" data-id="${row.id}">
    //                             <i class="fa fa-chevron-down"></i>
    //                         </button>
    //                     `;
        //                 }
        //             }
        //         ],
        //         order: [],
        //         pageLength: 10
        //     });

        //     // Bind expand button events
        //     $(document).off("click", ".expand-btn").on("click", ".expand-btn", function(e) {
        //         e.stopPropagation();
        //         const btn = $(this);
        //         const icon = btn.find("i");
        //         const tr = btn.closest("tr");
        //         const table = $('#diagnosistbl').DataTable();
        //         const row = table.row(tr);
        //         const data = row.data();
        //         const existingRow = tr.next(".details-row");

        //         if (existingRow.length) {
        //             existingRow.slideToggle(300);
        //             icon.toggleClass("fa-chevron-down fa-chevron-up");
        //             return;
        //         }

        //         let actionsHtml = '';
        //         @if (app('hasPermission')(35, 'view'))
        //             actionsHtml += `<i class="fa fa-eye m-r-5 icon3 view-diagnosis" data-id="${data.id}" title="View"></i>`;
        //         @endif
        //         @if (app('hasPermission')(35, 'update'))
        //             actionsHtml += `<i class="fa fa-pencil m-r-5 icon1 edit-diagnosis" data-id="${data.id}" title="Edit"></i>`;
        //         @endif
        //         @if (app('hasPermission')(35, 'delete'))
        //             actionsHtml += `<i class="fa fa-trash-o m-r-5 icon2 delete-diagnosis" data-id="${data.id}" title="Delete"></i>`;
        //         @endif

        //         const detailsRow = $(`
    //             <tr class="details-row">
    //                 <td colspan="4">
    //                     <div class="details-content">
    //                         <div><strong>Description:</strong> ${ucfirst(data.description)}</div>
    //                         <div class="mt-2"><strong>Actions:</strong> <div class="icon">${actionsHtml}</div></div>
    //                     </div>
    //                 </td>
    //             </tr>
    //         `);

        //         tr.after(detailsRow);
        //         icon.toggleClass("fa-chevron-down fa-chevron-up");
        //     });
        // }
        function fetchDiagnoses() {
            let branchId = localStorage.getItem('selectedBranchId');

            // ✅ Properly destroy existing DataTable if it exists
            if ($.fn.DataTable.isDataTable("#diagnosistbl")) {
                try {
                    let existingTable = $('#diagnosistbl').DataTable();
                    existingTable.destroy();
                    $('#diagnosistbl').removeClass('dataTable');
                    $('#diagnosistbl tbody').empty();
                    $.removeData($('#diagnosistbl')[0], 'DataTable');
                    $.removeData($('#diagnosistbl')[0], 'DataTables_DataTable');
                    $('#diagnosistbl').off();
                } catch (e) {
                    $('#diagnosistbl tbody').empty();
                    $.removeData($('#diagnosistbl')[0]);
                }
            }

            // ✅ Ensure tbody exists
            if ($('#diagnosistbl tbody').length === 0) {
                $('#diagnosistbl').append('<tbody></tbody>');
            }

            // ✅ Small delay to ensure DOM is clean
            setTimeout(function() {
                $('#diagnosistbl').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: function(data, callback) {
                        const page = Math.floor(data.start / data.length) + 1;
                        $.ajax({
                            url: "{{ url('/api/diagnosis') }}",
                            type: "GET",
                            data: {
                                branch_id: branchId,
                                page: page,
                                per_page: data.length,
                                search: data.search?.value || ''
                            },
                            dataType: "json",
                            headers: {
                                "Authorization": "Bearer " + token
                            },
                            success: function(response) {
                                callback({
                                    draw: data.draw,
                                    recordsTotal: response.pagination?.total || 0,
                                    recordsFiltered: response.pagination?.total ||
                                        0,
                                    data: response.diagnoses || []
                                });
                            },
                            error: function() {
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
                            data: 'name',
                            render: data => ucfirst(data),
                            className: 'view-diagnosis',
                            createdCell: function(td, cellData, rowData) {
                                $(td).attr('data-id', rowData.id).css('cursor', 'pointer');
                            }
                        },
                        {
                            data: 'description',
                            className: 'd-none d-md-table-cell view-diagnosis',
                            render: data => ucfirst(data),
                            createdCell: function(td, cellData, rowData) {
                                $(td).attr('data-id', rowData.id).css('cursor', 'pointer');
                            }
                        },
                        {
                            data: null,
                            className: 'd-none d-md-table-cell',
                            orderable: false,
                            render: function(data, type, row) {
                                let actions = `<div class="icon">`;
                                @if (app('hasPermission')(35, 'view'))
                                    actions +=
                                        `<i class="fa fa-eye m-r-5 icon3 view-diagnosis" data-id="${row.id}" title="View"></i>`;
                                @endif
                                @if (app('hasPermission')(35, 'update'))
                                    actions +=
                                        `<i class="fa fa-pencil m-r-5 icon1 edit-diagnosis" data-id="${row.id}" title="Edit"></i>`;
                                @endif
                                @if (app('hasPermission')(35, 'delete'))
                                    actions +=
                                        `<i class="fa fa-trash-o m-r-5 icon2 delete-diagnosis" data-id="${row.id}" title="Delete"></i>`;
                                @endif
                                actions += `</div>`;
                                return actions;
                            }
                        },
                        {
                            data: null,
                            className: 'd-table-cell d-md-none text-center',
                            orderable: false,
                            render: function(data, type, row) {
                                return `
                            <button class="btn btn-link expand-btn" data-id="${row.id}">
                                <i class="fa fa-chevron-down"></i>
                            </button>
                        `;
                            }
                        }
                    ],
                    order: [],
                    pageLength: 10,
                    language: {
                        processing: '<div></div>',
                        emptyTable: "No data available"
                    }
                });

                // ✅ Bind expand button events
                $(document).off("click", ".expand-btn").on("click", ".expand-btn", function(e) {
                    e.stopPropagation();
                    const btn = $(this);
                    const icon = btn.find("i");
                    const tr = btn.closest("tr");
                    const table = $('#diagnosistbl').DataTable();
                    const row = table.row(tr);
                    const data = row.data();
                    const existingRow = tr.next(".details-row");

                    if (existingRow.length) {
                        existingRow.slideToggle(300);
                        icon.toggleClass("fa-chevron-down fa-chevron-up");
                        return;
                    }

                    let actionsHtml = '';
                    @if (app('hasPermission')(35, 'view'))
                        actionsHtml +=
                            `<i class="fa fa-eye m-r-5 icon3 view-diagnosis" data-id="${data.id}" title="View"></i>`;
                    @endif
                    @if (app('hasPermission')(35, 'update'))
                        actionsHtml +=
                            `<i class="fa fa-pencil m-r-5 icon1 edit-diagnosis" data-id="${data.id}" title="Edit"></i>`;
                    @endif
                    @if (app('hasPermission')(35, 'delete'))
                        actionsHtml +=
                            `<i class="fa fa-trash-o m-r-5 icon2 delete-diagnosis" data-id="${data.id}" title="Delete"></i>`;
                    @endif

                    const detailsRow = $(`
                <tr class="details-row">
                    <td colspan="4">
                        <div class="details-content">
                            <div><strong>Description:</strong> ${ucfirst(data.description)}</div>
                            <div class="mt-2"><strong>Actions:</strong> <div class="icon">${actionsHtml}</div></div>
                        </div>
                    </td>
                </tr>
            `);

                    tr.after(detailsRow);
                    icon.toggleClass("fa-chevron-down fa-chevron-up");
                });
            }, 50);
        }

        // Action buttons
        $(document).on('click', '.view-diagnosis', function() {
            var id = $(this).data('id');
            window.location.href = '/diagnosis/show/' + id;
        });

        $(document).on('click', '.edit-diagnosis', function() {
            var id = $(this).data('id');
            window.location.href = '/diagnosis/edit/' + id;
        });

        $(document).on('click', '.delete-diagnosis', function() {
            var id = $(this).data('id');
            if (!id) {
                Swal.fire('Error', 'Diagnosis ID not found!', 'error');
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
                        url: '/api/diagnosis/' + id,
                        type: 'DELETE',
                        headers: {
                            "Authorization": "Bearer " + token
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Diagnosis deleted successfully!',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                fetchDiagnoses(); // refresh without reload
                            });
                        },
                        error: function(xhr) {
                            console.log(xhr.responseText);
                            Swal.fire('Error', 'Failed to delete diagnosis.', 'error');
                        }
                    });
                }
            });
        });
    </script>
@endsection
