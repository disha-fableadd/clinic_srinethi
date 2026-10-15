@extends('layout.app')
@section('content')
    <style>
        .badge-bg {
            background-color: #F89884;
            color: #fff;
            font-weight: 600;
        }

        .action-icon-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            padding: 0;
            background-color: #f89884;
            color: #ffffff !important;
            border-radius: 10px;
            border: none;
            font-size: 14px;
            line-height: 1;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .action-icon-btn:hover {
            opacity: 0.9;
            color: #ffffff !important;
        }

        .action-icon-btn i {
            font-size: 14px;
            color: #ffffff !important;
        }

        .swal-confirm-btn {
            background-color: #cfece0 !important;
            color: #000000 !important;
            border-radius: 23px !important;
            padding: 8px 24px !important;
            font-weight: 600 !important;
            border: none !important;
            box-shadow: none !important;
        }

        .swal-cancel-btn {
            background-color: #f89884 !important;
            color: #ffffff !important;
            border-radius: 23px !important;
            padding: 8px 24px !important;
            font-weight: 600 !important;
            border: none !important;
            box-shadow: none !important;
        }
    </style>
    <div class="page-wrapper">
        <div class="content" style="height:100vh">
            <!-- <div class="row">
                    <div class="col-sm-12 col-12">
                        <h4 class="page-title" style="text-align:left; !important">All Medicines Details</h4>
                    </div>
                </div> -->

            <div class="row mt-2">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center" style="background-color:#f89884;">
                            <h3 class="card-title d-inline-block text-white mb-0"> <i class="fa fa-bell px-2"
                                    style="font-size:20px"></i>All Notifications </h3>
                            <div class="d-flex align-items-center gap-5">
                                <div style="margin-right: 8px;">
                                <button type="button" id="markAllReadBtn" class="btn btn-sm text-dark btn-danger font-weight-bold"
                                    style="border-radius: 20px; padding: 6px 16px;">
                                    <i class="fa fa-check-double me-1"></i> Mark All as Read
                                </button>
                                </div>
                                <div>
                                <button type="button" id="deleteAllBtn" class="btn btn-sm btn-danger font-weight-bold ms-2"
                                    style="border-radius: 20px; padding: 6px 16px;">
                                    <i class="fa fa-trash-alt me-1"></i> Delete All
                                </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body ">
                            <div class="table-responsive">
                                <div id="demo_info" class="box"></div>
                                <table id="notificationTbl" class="table custom-table">
                                    <thead style="background-color:#ff8e29;">
                                        <tr>
                                            <th>Message</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($notifications && $notifications->isNotEmpty())
                                            @foreach ($notifications as $notification)
                                                @php
                                                    $module = $notification->info['module'] ?? '';
                                                    $moduleId = $notification->info['module_id'] ?? '';
                                                    $href = '#'; // Default

                                                    if ($module === 'appointment') {
                                                        $href = route('appointment.show', $moduleId);
                                                    } elseif ($module === 'dailydata') {
                                                        $href = route('dailydata.show', $moduleId); // Payment detail page
                                                    } elseif ($module === 'followup') {
                                                        $href = route('followup.show', $moduleId);
                                                    } else {
                                                        $href = url()->current(); // Stay on the current page
                                                    }
                                                @endphp
                                                <tr id="notification-row-{{ $notification->id }}">
                                                    <td>{{ $notification->info['message'] ?? 'New Message' }}
                                                        @if (!$notification->is_read)
                                                             <span class="badge badge-bg ms-2">New</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ optional($notification->created_at)->format('d/m/Y') ?? '' }}
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center" style="gap: 6px;">
                                                            <a href="{{ $href }}"
                                                                class="notification-link1 action-icon-btn"
                                                                data-id="{{ $notification->id }}"
                                                                data-url="{{ route('notification.update', $notification->id) }}"
                                                                title="View Notification">
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                                            <button type="button"
                                                                class="action-icon-btn delete-single-notification"
                                                                data-id="{{ $notification->id }}"
                                                                data-url="{{ route('notification.destroy', $notification->id) }}"
                                                                title="Delete Notification">
                                                                <i class="fa fa-trash-o"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
    <script>
        $(document).ready(function() {
            new DataTable('#notificationTbl');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            $('.notification-link1').on('click', async function(e) {
                e.preventDefault();

                var notificationId = $(this).data('id');
                var updateUrl = $(this).data('url');
                var redirectUrl = $(this).attr('href');

                var $button = $(this);
                $button.prop('disabled', true);
                var btnHtml =
                    '<span class="ms-2 spinner-border spinner-border-sm Btnloader" role="status" aria-hidden="true"></span>';
                $button.append(btnHtml);

                $.ajax({
                    url: updateUrl,
                    method: 'POST',
                    data: {
                        _method: 'POST',
                        read: true
                    },
                    success: function(response) {
                        if (redirectUrl && redirectUrl !== '#' && redirectUrl !== window.location.href) {
                            window.location.href = redirectUrl;
                        } else {
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        console.error('Error while updating notification:', xhr.responseText);
                        if (redirectUrl && redirectUrl !== '#') {
                            window.location.href = redirectUrl;
                        }
                    },
                    complete: function() {
                        $button.prop('disabled', false);
                        $button.find('.Btnloader').remove();
                    }
                });
            });

            $('#markAllReadBtn').on('click', function() {
                var $btn = $(this);
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Marking...');

                $.ajax({
                    url: '{{ route("notification.markAllAsRead") }}',
                    method: 'POST',
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('Failed to mark notifications as read. Please try again.');
                        $btn.prop('disabled', false).html('<i class="fa fa-check-double me-1"></i> Mark All as Read');
                    }
                });
            });

            // Single Notification Delete
            $(document).on('click', '.delete-single-notification', function(e) {
                e.preventDefault();
                var notificationId = $(this).data('id');
                var deleteUrl = $(this).data('url');
                var $row = $('#notification-row-' + notificationId);

                Swal.fire({
                    title: 'Delete Notification?',
                    text: 'Are you sure you want to delete this notification?',
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
                            url: deleteUrl,
                            type: 'DELETE',
                            success: function(res) {
                                if (res.status) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted!',
                                        text: res.message || 'Notification deleted.',
                                        timer: 1200,
                                        showConfirmButton: false
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire('Error', res.message || 'Failed to delete notification.', 'error');
                                }
                            },
                            error: function(xhr) {
                                let msg = xhr.responseJSON?.message || 'Failed to delete notification.';
                                Swal.fire('Error', msg, 'error');
                            }
                        });
                    }
                });
            });

            // Delete All Notifications
            $('#deleteAllBtn').on('click', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Delete All Notifications?',
                    text: 'Are you sure you want to permanently delete all notifications?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#cfece0',
                    cancelButtonColor: '#f89884',
                    confirmButtonText: 'Yes, delete all!',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        confirmButton: 'swal-confirm-btn',
                        cancelButton: 'swal-cancel-btn'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        var $btn = $('#deleteAllBtn');
                        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Deleting...');

                        $.ajax({
                            url: '{{ route("notification.deleteAll") }}',
                            type: 'DELETE',
                            success: function(res) {
                                if (res.status) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'All Deleted!',
                                        text: res.message || 'All notifications deleted.',
                                        timer: 1200,
                                        showConfirmButton: false
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    $btn.prop('disabled', false).html('<i class="fa fa-trash-alt me-1"></i> Delete All');
                                    Swal.fire('Error', res.message || 'Failed to delete notifications.', 'error');
                                }
                            },
                            error: function(xhr) {
                                $btn.prop('disabled', false).html('<i class="fa fa-trash-alt me-1"></i> Delete All');
                                let msg = xhr.responseJSON?.message || 'Failed to delete notifications.';
                                Swal.fire('Error', msg, 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
