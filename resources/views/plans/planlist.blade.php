@extends('layout.app')

<style>
    .card-header {
        background-color: #f89884  !important;
    }

    .btn-rounded {
        background-color: #fed9cf !important;
        color: black !important;
        border: none !important;
    }
    .icon .icon3 {
   
}

    .icon {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .icon i {
        cursor: pointer;
        font-size: 14px;
        padding: 8px 10px;
        border-radius: 5px;
        transition: all 0.3s ease;
         padding: 8px;
    background-color: #f89884;
    color: white;
    border-radius: 10px;
    }

    

    .swal-confirm-btn {
        color: rgb(58, 58, 58) !important;
    }

    .swal-cancel-btn {
        color: white !important;
    }

    .table-responsive {
        border-radius: 5px;
    }

    @media (max-width: 768px) {
        .mobile-only {
            display: block !important;
        }

        .desktop-only {
            display: none !important;
        }
    }

    @media (min-width: 769px) {
        .mobile-only {
            display: none !important;
        }

        .desktop-only {
            display: table-cell !important;
        }
    }
</style>

@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="row mt-2">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title d-inline-block text-white">
                            <i class="fa fa-cube px-2" style="font-size:20px"></i>All Plans
                        </h3>
                        <!-- @if (app('hasPermission')(26, 'create')) -->
                            <a href="{{ route('plans.create') }}" class="btn btn-rounded btn-hdr">
                                <i class="fa fa-plus"></i> Add Plan
                            </a>
                        <!-- @endif -->
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table custom-table mt-4" id="plansTable">
                                <thead style="background-color:#f89884;">
                                    <tr>
                                        <th style="display: none;">ID</th>
                                        <th>Plan Name</th>
                                        <th class="desktop-only">Price</th>
                                        <th class="desktop-only">Duration</th>
                                        <th class="desktop-only">Status</th>
                                        <th class="desktop-only">User Limit</th>
                                        <th class="desktop-only">Branch Limit</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="planBody">
                                    <!-- Data will be loaded via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2 & jQuery -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

<script>
   

    $(document).ready(function() {
        loadPlans();
    });

    function loadPlans() {
        $.ajax({
            url: "{{ route('plans.api-list') }}",
            type: "GET",
            headers: {
                "Authorization": "Bearer " + token
            },
            success: function(response) {
                let tbody = $('#planBody');
                tbody.empty();

                if (response.plans && response.plans.length > 0) {
                    response.plans.forEach(function(plan) {
                        let statusBadge = plan.is_active === 1 || plan.is_active === '1'
                            ? '<span class="badge badge-success">Active</span>'
                            : '<span class="badge badge-danger">Inactive</span>';

                        let actionButtons = `<div class="icon">`;

                        @if (app('hasPermission')(26, 'view'))
                            actionButtons += `<i class="fa fa-eye view-plan icon" data-id="${plan.id}" title="View Plan"></i>`;
                        @endif

                        @if (app('hasPermission')(26, 'update'))
                            actionButtons += `<i class="fa fa-pencil edit-plan icon" data-id="${plan.id}" title="Edit Plan"></i>`;
                        @endif

                        @if (app('hasPermission')(26, 'delete'))
                            actionButtons += `<i class="fa fa-trash-o delete-plan icon" data-id="${plan.id}" title="Delete Plan"></i>`;
                        @endif

                        actionButtons += `</div>`;

                        let row = `
                            <tr>
                                <td style="display: none;">${plan.id}</td>
                                <td><strong>${plan.name}</strong></td>
                                <td class="desktop-only">₹${parseFloat(plan.price || 0).toFixed(2)}</td>
                                <td class="desktop-only"><span class="badge badge-info">${plan.duration === 'month' ? 'Monthly' : 'Yearly'}</span></td>
                                <td class="desktop-only">${statusBadge}</td>
                                <td class="desktop-only">${plan.user_limit || 'Unlimited'}</td>
                                <td class="desktop-only">${plan.branch_limit || 'Unlimited'}</td>
                                <td>${actionButtons}</td>
                            </tr>
                        `;
                        tbody.append(row);
                    });
                } else {
                    tbody.append('<tr><td colspan="8" class="text-center text-muted">No plans found</td></tr>');
                }

                attachEventListeners();
            },
            error: function() {
                Swal.fire('Error', 'Failed to load plans', 'error');
            }
        });
    }

    function attachEventListeners() {
        // View plan
        $(document).off('click', '.view-plan').on('click', '.view-plan', function() {
            let planId = $(this).data('id');
            window.location.href = '/plans/' + planId;
        });

        // Edit plan
        $(document).off('click', '.edit-plan').on('click', '.edit-plan', function() {
            let planId = $(this).data('id');
            window.location.href = '/plans/' + planId + '/edit';
        });

        // Delete plan
        $(document).off('click', '.delete-plan').on('click', '.delete-plan', function() {
            let planId = $(this).data('id');

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
                        url: '/plans/' + planId,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            "Authorization": "Bearer " + token
                        },
                        success: function() {
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Plan deleted successfully!',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                loadPlans();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire('Error', xhr.responseJSON?.message || 'Failed to delete plan', 'error');
                        }
                    });
                }
            });
        });
    }
</script>

@if (session('success'))
    <script>
        Swal.fire({
            title: 'Success!',
            text: '{{ session('success') }}',
            icon: 'success',
            timer: 2000,
            showConfirmButton: false
        });
    </script>
@endif

@endsection