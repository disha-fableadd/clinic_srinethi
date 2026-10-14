@extends('layout.app')
<link rel="stylesheet" href="{{ asset(env('IMAGE_PATH').'admin/assets/css/dietchart-index.css') }}"> 

@section('content')
    <div class="page-wrapper">
        <div class="content" style="height:100vh">
            <div class="row" style="padding-top:15px"></div>

            <div class="row mt-2">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header" style="background-color:#f89884;">
                            <h3 class="card-title d-inline-block text-white">
                                <i class="fa fa-cutlery px-2" style="font-size:20px"></i>
                                All Diet Charts
                            </h3>
                            <button class="btn btn-rounded btn-hdr" id="dietExportButton"
                                style="background-color: #fed9cf;">
                                <i class="fa fa-download"></i> <span class="hdr-btn-text">Export</span>
                            </button>
                            @if (app('hasPermission')(37, 'create'))
                                {{-- Adjust permission ID --}}
                                <a href="{{ route('dietchart.create') }}" class="btn btn-rounded btn-hdr"
                                    style="background-color: #fed9cf;">
                                    <i class="fa fa-plus"></i> <span class="hdr-btn-text">Add</span>
                                </a>
                            @endif
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="dietcharttbl" class="table">
                                    <thead>
                                        <tr>
                                            <th>Template Name</th>
                                            <th>Titles</th>
                                            <th class="d-none d-md-table-cell">Descriptions</th>
                                            <th class="d-none d-md-table-cell">Times</th>
                                            <th class="d-none d-md-table-cell">Actions</th>
                                            <th class="d-table-cell d-md-none">Details</th>
                                        </tr>
                                    </thead>
                                    <tbody id="dietchartTableBody"></tbody>
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
        // --- Export Button ---
        $(document).on('click', '#dietExportButton', function() {
            let branchId = localStorage.getItem('selectedBranchId');
            if (!branchId) {
                alert("Please select a branch first.");
                return;
            }
            window.location.href = "{{ route('dietchart.export') }}" + "?branch_id=" + branchId;
        });

        let branchId = localStorage.getItem('selectedBranchId');

        $(document).ready(function() {
            let token = localStorage.getItem("token");

            function ucfirst(str) {
                if (!str) return 'N/A';
                return str.charAt(0).toUpperCase() + str.slice(1);
            }

            function renderList(items) {
                if (!items || items.length === 0) return '<em>N/A</em>';
                let html = '<ul style="padding-left: 20px;">';
                items.forEach(item => {
                    html += `<li>${item ? ucfirst(item) : 'N/A'}</li>`;
                });
                html += '</ul>';
                return html;
            }

            // --- Fetch Diet Charts ---
           $.ajax({
        url: '/api/dietchart',
        type: 'GET',
        dataType: 'json',
        data: { branch_id: branchId },
        headers: { "Authorization": "Bearer " + token },
        beforeSend: function () {
            $('#dietchartTableBody').html(`<tr><td colspan="6" class="text-center">Loading...</td></tr>`);
        },
        success: function (response) {
            let dietList = response.data ?? [];
            $('#dietchartTableBody').empty();

            dietList.forEach(function (chart) {
                let titles = Array.isArray(chart.title) ? chart.title : JSON.parse(chart.title || '[]');
                let descriptions = Array.isArray(chart.description) ? chart.description : JSON.parse(chart.description || '[]');
                let times = Array.isArray(chart.time) ? chart.time : JSON.parse(chart.time || '[]');

                let actionsHtml = `<div class="icon" style="cursor:pointer">`;
                @if (app('hasPermission')(30, 'view'))
                    actionsHtml += `<i class="fa fa-eye m-r-5 icon3 view-diet" data-id="${chart.id}" title="View"></i>`;
                @endif
                @if (app('hasPermission')(30, 'update'))
                    actionsHtml += `<i class="fa fa-pencil m-r-5 icon1 edit-diet" data-id="${chart.id}" title="Edit"></i>`;
                @endif
                @if (app('hasPermission')(30, 'delete'))
                    actionsHtml += `<i class="fa fa-trash-o m-r-5 icon2 delete-diet" data-id="${chart.id}" title="Delete"></i>`;
                @endif
                let downloadUrl = `{{ route('dietchart.download', ':id') }}`.replace(':id', chart.id);
                actionsHtml += `<a href="${downloadUrl}" class="btn btn-sm downloadpdf" title="Download PDF">
                                    <i class="fa-solid fa-download"></i>
                                </a>`;
                actionsHtml += `</div>`;

                $('#dietchartTableBody').append(`
                    <tr>
                        <td>${ucfirst(chart.name)}</td>
                        <td>${renderList(titles)}</td>
                        <td class="d-none d-md-table-cell">${renderList(descriptions)}</td>
                        <td class="d-none d-md-table-cell">${renderList(times)}</td>
                        <td class="d-none d-md-table-cell">${actionsHtml}</td>
                        <td class="d-table-cell d-md-none text-center">
                            <button class="btn btn-link expand-btn">
                                <i class="fa fa-chevron-down"></i>
                            </button>
                        </td>
                    </tr>
                `);
            });
                    $('#dietcharttbl').DataTable({
                        paging: true,
                        searching: true,
                        ordering: false,
                        responsive: false
                    });
                },
                error: function(xhr) {
                    console.error("Error loading diet charts:", xhr.responseText);
                }
            });
            $(document).on("click", ".expand-btn", function (e) {
        e.stopPropagation();
        const btn = $(this);
        const icon = btn.find("i");
        const tr = btn.closest("tr");

        // Close other open dropdowns
        $(".details-row").not(tr.next(".details-row")).slideUp(200, function () {
            $(this).remove();
        });
        $(".expand-btn i").not(icon).removeClass("fa-chevron-up").addClass("fa-chevron-down");

        const existingRow = tr.next(".details-row");
        if (existingRow.length) {
            existingRow.slideUp(200, function () {
                $(this).remove();
            });
            icon.toggleClass("fa-chevron-down fa-chevron-up");
            return;
        }

        // Get data from hidden columns
        const descriptionHtml = tr.find("td.d-none.d-md-table-cell").eq(0).html() || "<em>No Description</em>";
        const timeHtml = tr.find("td.d-none.d-md-table-cell").eq(1).html() || "<em>No Time</em>";
        const actionHtml = tr.find("td.d-none.d-md-table-cell").eq(2).html() || "";

        // Build details row
        const detailsRow = $(`
            <tr class="details-row">
                <td colspan="6">
                    <div class="details-content">
                        <div><strong>Description:</strong> ${descriptionHtml}</div>
                        <div class="mt-2"><strong>Time:</strong> ${timeHtml}</div>
                        <div class="mt-2"><strong>Actions:</strong> ${actionHtml}</div>
                    </div>
                </td>
            </tr>
        `);

        // Insert below and animate
        tr.after(detailsRow);
        detailsRow.hide().slideDown(300);
        icon.toggleClass("fa-chevron-down fa-chevron-up");
    });


            // --- View ---
            $(document).on('click', '.view-diet', function() {
                var id = $(this).data('id');
                window.location.href = '/dietchart/show/' + id;
            });

            // --- Edit ---
            $(document).on('click', '.edit-diet', function() {
                var id = $(this).data('id');
                window.location.href = '/dietchart/edit/' + id;
            });

            // --- Delete ---
            $(document).on('click', '.delete-diet', function() {
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to delete this diet chart!",
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
                            url: '/api/dietchart/' + id,
                            type: 'DELETE',
                            headers: {
                                "Authorization": "Bearer " + token
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'Diet Chart deleted successfully!',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire('Error', xhr.responseJSON?.message ||
                                    'Failed to delete.', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
