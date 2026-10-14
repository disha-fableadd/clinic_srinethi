@extends('layout.app')
@section('content')
    <style>
        .badge-bg {
            background-color: #F89884;
            color: #fff;
            font-weight: 600;
        }

        .action-btn {
            padding: 8px;
            background-color: #f89884;
            color: white;
            border-radius: 10px;
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
                        <div class="card-header" style="background-color:#f89884;">
                            <h3 class="card-title d-inline-block text-white"> <i class="fa fa-bell px-2"
                                    style="font-size:20px"></i>All Notifications </h3>
                        </div>
                        <div class="card-body ">
                            <div class="table-responsive">
                                <div id="demo_info" class="box"></div>
                                <table id="notificationTbl" class="table  custom-table">
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
                                                <tr>
                                                    <td>{{ $notification->info['message'] ?? 'New Message' }}
                                                        @if (!$notification->is_read)
                                                            <!-- Check if notification is unread -->
                                                            <span class="badge badge-bg ms-2">New</span>
                                                            <!-- Badge for new notifications -->
                                                        @endif
                                                    </td>
                                                    <td>{{ optional($notification->created_at)->format('d/m/Y') ?? '' }}
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <a href="{{ $href }}"
                                                                class="notification-link1 action-btn btn notification-btn"
                                                                data-id="{{ $notification->id }}"
                                                                data-url="{{ route('notification.update', $notification->id) }}">
                                                                <!-- <div class="viewsenq p-1">
                                                                    <button type="button" class="btn btn-primary notification-btn"> -->
                                                                <i class="fas fa-eye"></i>
                                                                <!-- </button>
                                                                </div> -->
                                                            </a>
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
                console.log('notification-link1')
                e.preventDefault(); // Prevent the default link behavior

                var notificationId = $(this).data('id'); // Get the notification ID
                var updateUrl = $(this).data('url'); // Get the URL for the update method
                var redirectUrl = $(this).attr('href');

                // var $button = $(this).find('.notification-btn');
                var $button = $(this);
                $button.prop('disabled', true);
                var btnHtml =
                    '<span class="ms-2 spinner-border spinner-border-sm Btnloader" role="status" aria-hidden="true"></span>';
                $button.append(btnHtml);

                $.ajax({
                    url: updateUrl,
                    method: 'POST', // Use PUT for updating
                    data: {
                        _method: 'POST', // This is necessary for Laravel to interpret the request as a PUT
                        read: true // Optionally send additional data (mark as read)
                    },
                    success: function(response) {
                        console.log('Error notification:', response.message);
                        location.reload();
                        $button.prop('disabled', false);
                        $button.find('.Btnloader').remove();
                        // $('.Btnloader').remove();

                        // window.location.href = redirectUrl;
                    },
                    error: function(xhr) {
                        console.error('Error while notification:', xhr.responseText);
                        // window.location.href = redirectUrl;
                    },
                    complete: function() {
                        // Remove spinner and re-enable button after AJAX completes, whether successful or not
                        $button.prop('disabled', false);
                        $button.find('.Btnloader').remove();
                        // window.location.href = redirectUrl; // Redirect to the link
                    }

                });
            });
        });
    </script>
@endsection
