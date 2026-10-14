@extends('layout.app')

<link rel="stylesheet" href="{{ asset(env('IMAGE_PATH').'admin/assets/css/category-index.css') }}">
@section('content')
    <div class="page-wrapper">
        <div class="content content-height">
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header card-header-bg">
                            <h2 class="card-title d-inline-block text-white">
                                <i class="fas fa-user-tag px-2" style="font-size:20px"></i> All Categories
                            </h2>
                            <button class="btn btn-rounded float-right ml-2 btn-bg-fed9cf" id="exportButton">
                                <i class="fa fa-download"></i> <span class="hdr-btn-text">Export</span>
                            </button>
                            <a href="{{ route('category.create') }}" class="btn btn-rounded float-right btn-bg-fed9cf btn-padding">
                                <i class="fa fa-plus"></i> <span class="hdr-btn-text">Add</span>
                            </a>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                               <table id="categorytbl" class="table custom-table mt-3 display">
                                   <thead class="thead-bg">
                                       <tr>
                                           <th>Category Name</th>
                                           <th class="d-none d-md-table-cell">Description</th>
                                           <th class="d-none d-md-table-cell">Date</th>
                                           <th class="d-none d-md-table-cell th-width">Action</th>
                                           <th class="d-table-cell d-md-none text-center">Details</th>
                                       </tr>
                                   </thead>
                                    <tbody id="categoryTableBody"></tbody>
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
        $(document).ready(function() {

            const token = localStorage.getItem('token');
            const branchId = localStorage.getItem('selectedBranchId');

            function ucfirst(str) {
                if (!str) return 'N/A';
                return str.charAt(0).toUpperCase() + str.slice(1);
            }

            // Fetch categories from API
            function fetchCategories() {
                $.ajax({
                    url: '/api/category',
                    type: 'GET',
                    data: {
                        branch_id: branchId
                    },
                    dataType: 'json',
                    headers: {
                        "Authorization": "Bearer " + token
                    },
                    beforeSend: function() {
                        $('#categoryTableBody').html(`
                    <tr><td colspan="5" class="text-center">Loading...</td></tr>
                `);
                    },
                    success: function(data) {
                        if (!data.categories || !Array.isArray(data.categories)) return;

                        const tableBody = $('#categoryTableBody');
                        tableBody.empty();

                        data.categories.forEach(category => {
                            let row = `
                    <tr>
                        <td>${ucfirst(category.name)}</td>
                        <td class="d-none d-md-table-cell">${ucfirst(category.description)}</td>
                        <td class="d-none d-md-table-cell">${category.created_at}</td>
                        <td class="d-none d-md-table-cell">
                           <div class="icon icon-cursor">
                               <i class="fa fa-eye m-r-5 icon3 view-category" data-id="${category.id}"></i>
                               <i class="fa fa-pencil m-r-5 icon1 edit-category" data-id="${category.id}"></i>
                               <i class="fa fa-trash-o m-r-5 icon2 delete-category" data-id="${category.id}"></i>
                           </div>
                       </td>
                        <td class="d-table-cell d-md-none text-center">
                            <button class="expand-btn"><i class="fa fa-chevron-down"></i></button>
                        </td>
                    </tr>`;
                            tableBody.append(row);
                        });

                        if ($.fn.DataTable.isDataTable('#categorytbl')) {
                            $('#categorytbl').DataTable().destroy();
                        }
                        $('#categorytbl').DataTable({
                            paging: true,
                            searching: true,
                            ordering: true
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 401) {
                            window.location.href = "{{ route('login') }}";
                        } else {
                            console.error(xhr.responseText);
                        }
                    }
                });
            }

            fetchCategories();

            // Expand details on mobile
            $(document).on('click', '.expand-btn', function() {
                let btn = $(this);
                let icon = btn.find('i');
                let row = btn.closest('tr');
                let nextRow = row.next('.details-row');

                if (nextRow.length) {
                    nextRow.slideToggle(300);
                    icon.toggleClass('fa-chevron-down fa-chevron-up');
                } else {
                    let name = row.find('td').eq(0).text().trim();
                    let description = row.find('td').eq(1).text().trim() || 'N/A';
                    let date = row.find('td').eq(2).text().trim() || 'N/A';
                    let actions = row.find('td').eq(3).html() || '';

                    let detailsRow = $(`
                <tr class="details-row">
                    <td colspan="5">
                        <div class="details-content">
                            <div><strong>Description:</strong> ${description}</div>
                            <div><strong>Date:</strong> ${date}</div>
                            <div class="mt-2"><strong>Actions:</strong> ${actions}</div>
                        </div>
                    </td>
                </tr>
            `);

                    row.after(detailsRow);
                    icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
                }
            });

            // Event Handlers
            $(document).on('click', '.view-category', function() {
                const id = $(this).data('id');
                window.location.href = '/category/show/' + id;
            });

            $(document).on('click', '.edit-category', function() {
                const id = $(this).data('id');
                window.location.href = '/category/edit/' + id;
            });

            // $(document).on('click', '.delete-category', function() {
            //     const id = $(this).data('id');
            //     Swal.fire({
            //         title: "Are you sure?",
            //         text: "You won't be able to revert this!",
            //         icon: "warning",
            //         showCancelButton: true,
            //         confirmButtonColor: '#cfece0',
            //         cancelButtonColor: '#f89884',
            //         confirmButtonText: 'Yes, delete it!',
            //         cancelButtonText: 'Cancel',
            //         customClass: {
            //             confirmButton: 'swal-confirm-btn',
            //             cancelButton: 'swal-cancel-btn'
            //         }
            //     }).then((result) => {
            //         if (result.isConfirmed) {
            //             $.ajax({
            //                 url: '/api/category/' + id,
            //                 type: 'DELETE',
            //                 headers: {
            //                     "Authorization": "Bearer " + token
            //                 },
            //                 success: function() {
            //                     Swal.fire({
            //                         title: "Deleted!",
            //                         text: "The category has been deleted.",
            //                         icon: "success",
            //                         timer: 1500,
            //                         showConfirmButton: false
            //                     }).then(fetchCategories);
            //                 },
            //                 error: function(xhr) {
            //                     Swal.fire({
            //                         title: "Error",
            //                         text: xhr.responseText ||
            //                             "Something went wrong",
            //                         icon: "error"
            //                     });
            //                 }
            //             });
            //         }
            //     });
            // });
            $(document).on('click', '.delete-category', function() {
                const categoryId = $(this).data('id');
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
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
                            url: '/api/category/' + categoryId,
                            type: 'DELETE',
                            headers: {
                                "Authorization": "Bearer " + token
                            },
                            success: function() {
                                Swal.fire({
                                        title: "Deleted!",
                                        text: "The category has been deleted.",
                                        icon: "success",
                                        timer: 1500,
                                        showConfirmButton: false
                                    })
                                    .then(fetchCategories);
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: "Error",
                                    text: xhr.responseText ||
                                        "Something went wrong",
                                    icon: "error"
                                });
                            }
                        });
                    }
                });
            });

            // Export
            $(document).on('click', '#exportButton', function() {
                let branchId = localStorage.getItem('selectedBranchId');
                if (!branchId) {
                    alert("Branch ID not found in localStorage!");
                    return;
                }
                window.location.href = "{{ route('category.export') }}" + "?branch_id=" + branchId;
            });
        });
    </script>
@endsection
