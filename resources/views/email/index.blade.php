@extends('layout.app')
<style>
    .swal-confirm-btn {
        color: rgb(58, 58, 58) !important;
    }

    /* Cancel button text white (for visibility on red background) */
    .swal-cancel-btn {
        color: white !important;
    }

    .nav-tabs .nav-link:focus,
    .nav-tabs .nav-link:hover {
        background-color: transparent !important;
        border-color: #dee2e6 #dee2e6 #fff !important;
        color: black;
    }

    .nav-tabs .nav-link:focus,
    .nav-tabs .nav-link {
        /* background-color:; */
        border-color: transparent;
        color: #495057;
    }

    .form-control {
        border-color: rgb(207, 236, 224) !important;
    }
</style>
<!-- Select2 CSS -->


@section('content')
    <div class="page-wrapper">
        <div class="content" style="height:100vh">
            <div class="row" style="padding-top:15px">

            </div>

            <div class="row mt-2">
                <div class="col-md-12">

                    <div class="card">

                        <div class="card-header" style="background-color:#f89884;">
                            <div class="all">
                                <div class="head">
                                    <h3 class="card-title d-inline-block text-white"><i class="fas fa-sms px-2"
                                            style="font-size:20px"></i>Template</h3>
                                </div>

                                <div class="buttons">
                                    @if (app('hasPermission')(16, 'create'))
                                        <a href="{{ route('email.create') }}" class="btn  btn-rounded float-right emailtemp"
                                            style="background-color: #fed9cf;text-decoration:none"><i
                                                class="fa fa-plus"></i> <span class="hdr-btn-text">Create</span>

                                        </a>
                                    @endif

                                    <a href="" class="btn btn-rounded btn-hdr"
                                        style="background-color: #fed9cf; margin-right:10px; text-decoration:none;color:black"
                                        data-toggle="modal" data-target="#sendEmailModal">
                                        <i class="fa fa-paper-plane"></i> Send Email
                                    </a>

                                </div>
                            </div>



                        </div>





                        <ul class="nav nav-tabs mt-5 mb-3" id="smsTabs" role="tablist" style="padding: 0 20px;">
                            <li class="nav-item">
                                <a class="nav-link active" id="template-tab" data-toggle="tab" href="#template"
                                    role="tab" aria-controls="template" aria-selected="true"
                                    style="text-decoration:none;">
                                    <i class="fas fa-envelope-open-text px-2" style="font-size:20px"></i> Email Template
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="log-tab" data-toggle="tab" href="#log" role="tab"
                                    aria-controls="log" aria-selected="false" style="text-decoration:none;">
                                    <i class="fas fa-clipboard-list px-2" style="font-size:20px"></i> Email Log
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="credentials-tab" data-toggle="tab" href="#credentials"
                                    role="tab" aria-controls="credentials" aria-selected="false"
                                    style="text-decoration:none;">
                                    <i class="fas fa-key px-2" style="font-size:20px"></i> Email Credentials
                                </a>
                            </li>
                        </ul>


                        <div class="card-body">
                            <div class="tab-content" id="smsTabsContent">
                                <!-- Template Tab -->

                                <div class="tab-pane fade show active" id="template" role="tabpanel"
                                    aria-labelledby="template-tab">

                                    <button class="btn btn-rounded btn-hdr" id="exportButton1"
                                        style="background-color: #fed9cf;">
                                        <i class="fa fa-download"></i> <span class="hdr-btn-text">Export</span>
                                    </button>
                                    <div class="table-responsive">
                                        <div id="demo_info" class="box"></div>
                                        <table id="temptbl" class="table custom-table">
                                            <thead style="background-color:#ff8e29;" class="text-center">
                                                <tr>

                                                    <th>Name</th>

                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="inventoryBody">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>




                                <!-- log Tab -->
                                <div class="tab-pane fade  " id="log" role="tabpanel" aria-labelledby="log-tab">
                                    <button class="btn btn-rounded btn-hdr" id="exportButton2"
                                        style="background-color: #fed9cf;">
                                        <i class="fa fa-download"></i> <span class="hdr-btn-text">Export</span>
                                    </button>
                                    <div class="table-responsive">
                                        <div id="demo_info" class="box"></div>
                                        <table id="logtbl" class="table custom-table">
                                            <thead style="background-color:#ff8e29;">
                                                <tr>

                                                    <th>User Name</th>
                                                    <th>Send Date</th>
                                                    <th>Template Name</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="emaillogBody">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>






                                <!-- Credentials Tab -->
                                <div class="tab-pane fade" id="credentials" role="tabpanel"
                                    aria-labelledby="credentials-tab">


                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <h4>Email Configuration</h4>
                                            <hr>
                                            <form class="mt-2" id="emailForm">
                                                <div id="email-credentials">

                                                    <div class="row">
                                                        <div class="col-md-6 col-lg-6 col-sm-6">
                                                            <div class="form-group">
                                                                <label for="mailmailer">Mail Mailer</label>
                                                                <input type="text" class="form-control"
                                                                    id="mailMailer" name="mail_mailer">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-lg-6 col-sm-6">
                                                            <div class="form-group">
                                                                <label for="mailHost">Mail Host</label>
                                                                <input type="text" class="form-control" id="mailHost"
                                                                    name="mail_host">
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="row">
                                                        <div class="col-md-6 col-lg-6 col-sm-6">
                                                            <div class="form-group">
                                                                <label for="mailPort">Mail Port</label>
                                                                <input type="number" class="form-control" id="mailPort"
                                                                    name="mail_port">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6 col-lg-6 col-sm-6">
                                                            <div class="form-group">
                                                                <label for="mailUsername">Mail Username</label>
                                                                <input type="email" class="form-control"
                                                                    id="mailUsername" name="mail_username">
                                                            </div>
                                                        </div>

                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6 col-lg-6 col-sm-6">
                                                            <div class="form-group">
                                                                <label for="mailPassword">Mail Password</label>
                                                                <input type="password" class="form-control"
                                                                    id="mailPassword" name="mail_password">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-lg-6 col-sm-6">
                                                            <div class="form-group">
                                                                <label for="mailEncryption">Mail Encryption</label>
                                                                <input type="text" class="form-control"
                                                                    id="mailEncryption" name="mail_encryption">
                                                            </div>
                                                        </div>

                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6 col-lg-6 col-sm-6">
                                                            <div class="form-group">
                                                                <label for="mailFromAddress">Mail From Address</label>
                                                                <input type="email" class="form-control"
                                                                    id="mailFromAddress" name="mail_from_address">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-lg-6 col-sm-6">
                                                            <div class="form-group">
                                                                <label for="mailFromName">Mail From Name</label>
                                                                <input type="text" class="form-control"
                                                                    id="mailFromName" name="mail_from_name">
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="text-center mt-4">
                                                        <button type="submit" class="btn btn-primary">Save
                                                            Settings</button>
                                                    </div>

                                            </form>
                                            <div id="successMessage" class="alert alert-success" style="display:none;">
                                            </div>
                                            <div id="errorMessage" class="alert alert-danger" style="display:none;">
                                            </div>
                                        </div>
                                    </div>
                                </div>



                            </div>
                        </div>

                    </div>
                </div>
            </div>


            <div class="modal fade" id="sendEmailModal" tabindex="-1" role="dialog"
                aria-labelledby="sendEmailModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color:rgb(207, 236, 224) !important;">
                            <h5 class="modal-title text-dark" id="sendEmailModalLabel">
                                <i class="fa fa-paper-plane"></i> Send Email
                            </h5>
                            <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="sendEmailForm">
                                <!-- Email Subject -->
                                <div class="form-group">
                                    <label for="emailSubject">Email Subject</label>
                                    <input type="text" class="form-control" id="emailSubject" name="emailSubject"
                                        required>
                                </div>


                                <div class="form-group">
                                    <label for="selectUser">Select User</label>
                                    <select class="form-control select2" id="selectUser" name="selectUser[]" multiple
                                        required>

                                    </select>
                                </div>

                                <!-- Select Template -->
                                <div class="form-group">
                                    <label for="selectTemplate">Select Template</label>
                                    <select class="form-control select2 email" id="selectTemplate" name="selectTemplate"
                                        required>

                                    </select>
                                </div>
                                <input type="hidden" name="branch_id" id="branch_id">


                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" onclick="sendEmail()">Send Email</button>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>

    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css">
    <script src=" https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    {{-- <script>
        $(document).on('click', '#exportButton1', function() {
            window.location.href = "{{ route('export.email.templates') }}";
        });


        $(document).on('click', '#exportButton2', function() {
            window.location.href = "{{ route('export.send.emails') }}";
        });


        // Credential
        $('form').on('submit', function(e) {
            e.preventDefault();



            var mailHost = $('#mailHost').val();
            var mailMailer = $('#mailMailer').val();
            var mailPort = $('#mailPort').val();
            var mailUsername = $('#mailUsername').val();
            var mailPassword = $('#mailPassword').val();
            var mailEncryption = $('#mailEncryption').val();
            var mailFromAddress = $('#mailFromAddress').val();
            var mailFromName = $('#mailFromName').val();

            // Prepare data to store in key-value format
            var formData = {

                // Email configuration data
                'mail_host': mailHost,
                'mail_mailer': mailMailer,
                'mail_port': mailPort,
                'mail_username': mailUsername,
                'mail_password': mailPassword,
                'mail_encryption': mailEncryption,
                'mail_from_address': mailFromAddress,
                'mail_from_name': mailFromName
            };


            $.ajax({
                url: '/api/save-settings',
                type: 'POST',
                data: JSON.stringify(formData),
                contentType: 'application/json',
                success: function(response) {

                    $('#successMessage').text(response.message || 'Settings saved successfully!')
                .show();
                    setTimeout(function() {
                        $('#successMessage').fadeOut();
                    }, 1000);
                },
                error: function(xhr) {

                    alert('Failed to save settings');
                }
            });
        });


        // get Credential
        $(document).ready(function() {
             let branchId = localStorage.getItem('selectedBranchId');
            // Fetch email settings when the page is loaded
            $.ajax({
                url: '/api/get-sms-settings',
                method: 'GET',

                success: function(response) {
                    if (response.data) {
                        // Fill the email form with the received data
                        $('#mailMailer').val(response.data.mail_mailer);
                        $('#mailHost').val(response.data.mail_host);
                        $('#mailPort').val(response.data.mail_port);
                        $('#mailUsername').val(response.data.mail_username);
                        $('#mailPassword').val(response.data.mail_password);
                        $('#mailEncryption').val(response.data.mail_encryption);
                        $('#mailFromAddress').val(response.data.mail_from_address);
                        $('#mailFromName').val(response.data.mail_from_name);
                    }
                },
                error: function() {
                    alert('Failed to load email settings');
                }
            });
        });




        // email-templates


        $(document).ready(function() {
            // Fetch email templates
            $.ajax({
                url: '/api/email-templates',
                method: 'GET',
                data: {
                    branch_id: branchId
                },
                success: function(response) {
                    // Clear any previous data
                    $('#inventoryBody').empty();

                    // Destroy existing DataTable if initialized
                    if ($.fn.DataTable.isDataTable("#temptbl")) {
                        $('#temptbl').DataTable().destroy();
                    }

                    // Loop through the email templates and append them to the table
                    response.forEach(function(template, index) {
                        let statusClass = template.status === 'active' ? 'btn-primary' :
                            'btn-danger';
                        let statusText = template.status === 'active' ? 'Active' : 'Inactive';
                        let extraStyles = 'border-radius: 20px; padding:1px 15px;';

                        var row = `<tr>
                            <td style="cursor:pointer" class="view-user" data-id="${template.id}">${template.name}</td>
                            <td style="cursor:pointer" class="view-user" data-id="${template.id}">
                                <button class="btn ${statusClass}" style="${extraStyles}">${statusText}</button>
                            </td>
                            <td class="text-center">
                                <div class="icon" style="cursor:pointer">
                                    @if (app('hasPermission')(16, 'view'))  <i class="fa fa-eye m-r-5 icon3 view-user" data-id="${template.id}"></i>   @endif  
                                    @if (app('hasPermission')(16, 'edit'))<i class="fa fa-pencil m-r-5 icon1 edit-user" data-id="${template.id}"></i> @endif
                                    @if (app('hasPermission')(16, 'delete'))<i class="fa fa-trash-o m-r-5 icon2 delete-user" data-id="${template.id}"></i> @endif
                                </div>
                            </td>
                        </tr>`;
                        $('#inventoryBody').append(row);
                    });

                    // Reinitialize DataTable after appending all rows
                    $('#temptbl').DataTable({
                        "paging": true,
                        "searching": true,
                        "ordering": true,
                        "destroy": true
                    });
                },
                error: function() {
                    alert('Failed to load email templates');
                }
            });
        });



        $(document).on('click', '.edit-user', function() {
            var templateId = $(this).data('id');
            window.location.href = '/email-template/edit/' + templateId;
        });

        $(document).on('click', '.view-user', function() {
            var templateId = $(this).data('id');
            window.location.href = '/email-template/show/' + templateId;
        });

        $(document).on('click', '.delete-user', function() {
            var templateId = $(this).data('id');

            if (!templateId) {
                Swal.fire('Error', 'Template ID is missing!', 'error');
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
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
                        url: '/api/email-templates/' + templateId,
                        type: 'DELETE',
                        success: function(response) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Template deleted successfully!',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                                fetchTemplates();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire('Error', 'Failed to delete template.', 'error');
                        }
                    });
                }
            });
        });



        // send-email

        $(document).ready(function() {
            $('#selectUser').select2({
                placeholder: "Select users",
                allowClear: true
            });
            $.ajax({
                url: '/api/users',
                method: 'GET',
                success: function(response) {
                    var users = response.data;
                    var userOptions = '<option value="">-- Select User --</option>';
                    $.each(users, function(index, user) {
                        userOptions += `<option value="${user.id}">${user.fullname}</option>`;
                    });
                    $('#selectUser').html(userOptions);
                },
                error: function() {
                    alert('Failed to fetch users');
                }
            });

            // Fetch templates
            $.ajax({
                url: '/api/emails-template',
                method: 'GET',
                success: function(response) {
                    console.log(response);
                    var templates = response.data || [];
                    var templateOptions = '<option value="">-- Select Template --</option>';
                    $.each(templates, function(index, template) {
                        templateOptions +=
                            `<option value="${template.id}">${template.name}</option>`;
                    });

                    var $select = $('#selectTemplate');
                    $select.html(templateOptions);

                    // Initialize Select2 with proper config
                    $select.select2({
                        placeholder: "-- Select Template --",
                        allowClear: true,
                        width: '100%',
                        minimumResultsForSearch: 0, // Always show search
                        dropdownParent: $('#sendEmailModal') // Required if inside modal
                    });

                    // Optional: Set search placeholder
                    $select.on('select2:open', function() {
                        $('.select2-search__field').attr('placeholder', 'Search templates');
                    });
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", status, error);
                    console.error("Response:", xhr.responseText);
                    alert('Failed to fetch templates');
                }
            });


            $('#selectUser').on('change', function() {
                var selectedUsers = $(this).val();
                var selectedUsersList = $('#selectedUsersList');
                selectedUsersList.empty();

                $.each(selectedUsers, function(index, userId) {

                    selectedUsersList.append('<li class="list-group-item" data-user-id="' + userId +
                        '">' + userId + '</li>');
                });
            });
        });



        function sendEmail() {
            let emailSubject = $("#emailSubject").val();
            let selectedUsers = $("#selectUser").val();
            let templateId = $("#selectTemplate").val();
            let senderId = "{{ auth()->user()->id }}";

            // Validation
            if (!selectedUsers || selectedUsers.length === 0 || !templateId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Validation Error',
                    text: 'Please select at least one user and a template.'
                });
                return;
            }

            // Show a loading alert
            Swal.fire({
                title: 'Sending Email...',
                text: 'Please wait while we send emails.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // AJAX request
            $.ajax({
                url: "api/send-email",
                type: "POST",
                data: {
                    emailSubject: emailSubject,
                    users: selectedUsers,
                    template_id: parseInt(templateId),
                    sender_id: senderId,
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Emails sent successfully!',
                    }).then(() => {
                        $("#sendEmailModal").modal("hide");
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to send emails. Please try again.',
                    });
                }
            });
        }




        $(document).ready(function() {
            fetchEmailLogs();
        });

        function fetchEmailLogs() {
            $.ajax({
                url: "{{ url('/api/emails') }}",
                type: "GET",
                dataType: "json",
                success: function(response) {
                    let table = $('#logtbl');

                    // Destroy existing DataTable if initialized
                    if ($.fn.DataTable.isDataTable("#logtbl")) {
                        table.DataTable().destroy();
                    }

                    let tbody = $("#emaillogBody");
                    tbody.empty(); // Clear previous data

                    if (response.length > 0) {
                        $.each(response, function(index, email) {
                            let row = `<tr >
                                            <td>${email.user ? email.user.fullname : 'N/A'}</td>
                                            <td>${email.created_at ? new Date(email.created_at).toLocaleDateString() : 'N/A'}</td>
                                            <td>${email.emailtemplate ? email.emailtemplate.name : 'N/A'}</td>
                                            <td class="text-center">
                                                <div class="icon" style="cursor:pointer">
                                                    <i class="fa fa-trash-o m-r-5 icon2 delete-email" data-id="${email.id}"></i>
                                                </div>
                                            </td>
                                        </tr>`;
                            tbody.append(row);
                        });

                        // Reinitialize DataTable **AFTER** appending all rows
                        table.DataTable({
                            "paging": true,
                            "searching": true,
                            "ordering": true,
                            "destroy": true
                        });
                    }
                },
                error: function(error) {
                    console.error("Error fetching email logs:", error);
                }
            });
        }



        $(document).on('click', '.delete-email', function() {
            var emailId = $(this).data('id');

            if (!emailId) {
                Swal.fire('Error', 'Email ID is missing!', 'error');
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/api/emails/' + emailId,
                        type: 'DELETE',
                        success: function(response) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Email record deleted successfully!',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                fetchEmailLogs(); // Refresh table after delete
                            });
                        },
                        error: function(xhr) {
                            Swal.fire('Error', 'Failed to delete the email record.', 'error');
                        }
                    });
                }
            });
        });
    </script> --}}

    <script>
        // Set up CSRF token for Laravel AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Global branchId
        let branchId = localStorage.getItem('selectedBranchId');

        $(document).ready(function() {

            // ===== Export Buttons =====
            // $(document).on('click', '#exportButton1', function() {
            //     window.location.href = "{{ route('export.email.templates') }}";
            // });

            $(document).on('click', '#exportButton1', function() {
                let branchId = localStorage.getItem('selectedBranchId');

                if (!branchId) {
                    alert("Branch ID not found in localStorage!");
                    return;
                }

                window.location.href = "{{ route('export.email.templates') }}" + "?branch_id=" + branchId;
            });


            // $(document).on('click', '#exportButton2', function() {
            //     window.location.href = "{{ route('export.send.emails') }}";
            // });

            $(document).ready(function () {
        $('#exportButton2').on('click', function () {
            let branchId = localStorage.getItem('selectedBranchId');

            if (!branchId) {
                alert("Branch ID not found in localStorage!");
                return;
            }

            // ✅ Append branch_id to the export route
            window.location.href = "{{ route('export.send.emails') }}" + "?branch_id=" + branchId;
        });
    });

           


            // ===== Save Email Credentials =====
            $('form').on('submit', function(e) {
                e.preventDefault();

                const formData = {
                    'mail_host': $('#mailHost').val(),
                    'mail_mailer': $('#mailMailer').val(),
                    'mail_port': $('#mailPort').val(),
                    'mail_username': $('#mailUsername').val(),
                    'mail_password': $('#mailPassword').val(),
                    'mail_encryption': $('#mailEncryption').val(),
                    'mail_from_address': $('#mailFromAddress').val(),
                    'mail_from_name': $('#mailFromName').val()
                };

                $.ajax({
                    url: '/api/save-settings',
                    type: 'POST',
                    data: JSON.stringify(formData),
                    contentType: 'application/json',
                    success: function(response) {
                        $('#successMessage').text(response.message ||
                            'Settings saved successfully!').show();
                        setTimeout(() => $('#successMessage').fadeOut(), 1000);
                    },
                    error: function() {
                        alert('Failed to save settings');
                    }
                });
            });

            // ===== Fetch Email Settings =====
            $.ajax({
                url: '/api/get-sms-settings',
                method: 'GET',
                success: function(response) {
                    if (response.data) {
                        $('#mailMailer').val(response.data.mail_mailer);
                        $('#mailHost').val(response.data.mail_host);
                        $('#mailPort').val(response.data.mail_port);
                        $('#mailUsername').val(response.data.mail_username);
                        $('#mailPassword').val(response.data.mail_password);
                        $('#mailEncryption').val(response.data.mail_encryption);
                        $('#mailFromAddress').val(response.data.mail_from_address);
                        $('#mailFromName').val(response.data.mail_from_name);
                    }
                },
                error: function() {
                    alert('Failed to load email settings');
                }
            });

            // ===== Fetch Email Templates =====
            function fetchTemplates() {
                $.ajax({
                    url: '/api/email-templates',
                    method: 'GET',
                    data: {
                        branch_id: branchId
                    },
                    success: function(response) {
                        const tbody = $('#inventoryBody');
                        tbody.empty();

                        // Destroy existing DataTable if exists
                        if ($.fn.DataTable.isDataTable("#temptbl")) {
                            $('#temptbl').DataTable().destroy();
                        }

                        response.forEach(template => {
                            const statusClass = template.status === 'active' ? 'btn-primary' :
                                'btn-danger';
                            const statusText = template.status === 'active' ? 'Active' :
                                'Inactive';
                            const extraStyles = 'border-radius: 20px; padding:1px 15px;';

                            const row = `<tr>
                            <td style="cursor:pointer" class="view-user" data-id="${template.id}">${template.name}</td>
                            <td style="cursor:pointer" class="view-user" data-id="${template.id}">
                                <button class="btn ${statusClass}" style="${extraStyles}">${statusText}</button>
                            </td>
                            <td class="text-center">
                                <div class="icon" style="cursor:pointer">
                                    @if (app('hasPermission')(16, 'view'))<i class="fa fa-eye m-r-5 icon3 view-user" data-id="${template.id}"></i>@endif
                                    @if (app('hasPermission')(16, 'edit'))<i class="fa fa-pencil m-r-5 icon1 edit-user" data-id="${template.id}"></i>@endif
                                    @if (app('hasPermission')(16, 'delete'))<i class="fa fa-trash-o m-r-5 icon2 delete-user" data-id="${template.id}"></i>@endif
                                </div>
                            </td>
                        </tr>`;
                            tbody.append(row);
                        });

                        // Reinitialize DataTable
                        $('#temptbl').DataTable({
                            paging: true,
                            searching: true,
                            ordering: true,
                            destroy: true
                        });
                    },
                    error: function() {
                        alert('Failed to load email templates');
                    }
                });
            }

            fetchTemplates();

            // ===== Edit/View/Delete Template Handlers =====
            $(document).on('click', '.edit-user', function() {
                const templateId = $(this).data('id');
                window.location.href = '/email-template/edit/' + templateId;
            });

            $(document).on('click', '.view-user', function() {
                const templateId = $(this).data('id');
                window.location.href = '/email-template/show/' + templateId;
            });

            $(document).on('click', '.delete-user', function() {
                const templateId = $(this).data('id');
                if (!templateId) return Swal.fire('Error', 'Template ID is missing!', 'error');

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
                }).then(result => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/api/email-templates/' + templateId,
                            type: 'DELETE',
                            success: function() {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'Template deleted successfully!',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(fetchTemplates);
                            },
                            error: function() {
                                Swal.fire('Error', 'Failed to delete template.',
                                    'error');
                            }
                        });
                    }
                });
            });

            // ===== Send Email Modal Setup =====
            $('#selectUser').select2({
                placeholder: "Select users",
                allowClear: true
            });

            $.ajax({ // Users
                url: '/api/users',
                method: 'GET',
                success: function(response) {
                    let userOptions = '<option value="">-- Select User --</option>';
                    response.data.forEach(user => userOptions +=
                        `<option value="${user.id}">${user.fullname}</option>`);
                    $('#selectUser').html(userOptions);
                },
                error: function() {
                    alert('Failed to fetch users');
                }
            });

            $.ajax({ // Email Templates
                url: '/api/emails-template',
                method: 'GET',
                success: function(response) {
                    const templates = response.data || [];
                    let templateOptions = '<option value="">-- Select Template --</option>';
                    templates.forEach(t => templateOptions +=
                        `<option value="${t.id}">${t.name}</option>`);
                    const $select = $('#selectTemplate');
                    $select.html(templateOptions).select2({
                        placeholder: "-- Select Template --",
                        allowClear: true,
                        width: '100%',
                        minimumResultsForSearch: 0,
                        dropdownParent: $('#sendEmailModal')
                    });
                },
                error: function(xhr) {
                    console.error("Failed to fetch templates:", xhr);
                    alert('Failed to fetch templates');
                }
            });

            $('#selectUser').on('change', function() {
                const selectedUsers = $(this).val();
                const list = $('#selectedUsersList');
                list.empty();
                selectedUsers.forEach(u => list.append('<li class="list-group-item" data-user-id="' + u +
                    '">' + u + '</li>'));
            });

            // ===== Send Email Function =====
            window.sendEmail = function() {
                const emailSubject = $("#emailSubject").val();
                const selectedUsers = $("#selectUser").val();
                const templateId = $("#selectTemplate").val();
                const senderId = "{{ auth()->user()->id }}";

                if (!selectedUsers || !templateId) {
                    return Swal.fire({
                        icon: 'warning',
                        title: 'Validation Error',
                        text: 'Please select at least one user and a template.'
                    });
                }

                Swal.fire({
                    title: 'Sending Email...',
                    text: 'Please wait while we send emails.',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                $.ajax({
                    url: "api/send-email",
                    type: "POST",
                    contentType: 'application/json',
                    data: JSON.stringify({
                        emailSubject,
                        users: selectedUsers,
                        template_id: parseInt(templateId),
                        sender_id: senderId,
                        branch_id: branchId
                    }),
                    success: function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Emails sent successfully!'
                        }).then(() => $("#sendEmailModal").modal("hide"));
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to send emails. Please try again.'
                        });
                    }
                });
            };

            // ===== Fetch Email Logs =====
            // function fetchEmailLogs() {
            //     $.ajax({
            //         url: "{{ url('/api/emails') }}",
            //         type: "GET",
            //         dataType: "json",
            //         success: function(response) {
            //             const table = $('#logtbl');
            //             if ($.fn.DataTable.isDataTable("#logtbl")) table.DataTable().destroy();

            //             const tbody = $("#emaillogBody");
            //             tbody.empty();

            //             response.forEach(email => {
            //                 const row = `<tr>
        //                     <td>${email.user?.fullname || 'N/A'}</td>
        //                     <td>${email.created_at ? new Date(email.created_at).toLocaleDateString() : 'N/A'}</td>
        //                     <td>${email.emailtemplate?.name || 'N/A'}</td>
        //                     <td class="text-center">
        //                         <div class="icon" style="cursor:pointer">
        //                             <i class="fa fa-trash-o m-r-5 icon2 delete-email" data-id="${email.id}"></i>
        //                         </div>
        //                     </td>
        //                 </tr>`;
            //                 tbody.append(row);
            //             });

            //             table.DataTable({ paging: true, searching: true, ordering: true, destroy: true });
            //         },
            //         error: function(error) { console.error("Error fetching email logs:", error); }
            //     });
            // }

            // fetchEmailLogs();

            function fetchEmailLogs() {
                // Get branch_id from localStorage
                const branchId = localStorage.getItem('selectedBranchId');

                if (!branchId) {
                    console.error("Branch ID not found in localStorage.");
                    return;
                }

                $.ajax({
                    url: "{{ url('/api/emails') }}",
                    type: "GET",
                    dataType: "json",
                    data: {
                        branch_id: branchId
                    }, // send branch_id to API
                    success: function(response) {
                        const table = $('#logtbl');
                        if ($.fn.DataTable.isDataTable("#logtbl")) table.DataTable().destroy();

                        const tbody = $("#emaillogBody");
                        tbody.empty();

                        response.forEach(email => {
                            const row = `<tr>
                    <td>${email.user?.fullname || 'N/A'}</td>
                    <td>${email.created_at ? new Date(email.created_at).toLocaleDateString() : 'N/A'}</td>
                    <td>${email.emailtemplate?.name || 'N/A'}</td>
                    <td class="text-center">
                        <div class="icon" style="cursor:pointer">
                            <i class="fa fa-trash-o m-r-5 icon2 delete-email" data-id="${email.id}"></i>
                        </div>
                    </td>
                </tr>`;
                            tbody.append(row);
                        });

                        table.DataTable({
                            paging: true,
                            searching: true,
                            ordering: true,
                            destroy: true
                        });
                    },
                    error: function(error) {
                        console.error("Error fetching email logs:", error);
                    }
                });
            }

            // Call the function
            fetchEmailLogs();


            // ===== Delete Email Logs =====
            $(document).on('click', '.delete-email', function() {
                const emailId = $(this).data('id');
                if (!emailId) return Swal.fire('Error', 'Email ID is missing!', 'error');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This action cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then(result => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/api/emails/' + emailId,
                            type: 'DELETE',
                            success: function() {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'Email record deleted successfully!',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(fetchEmailLogs);
                            },
                            error: function() {
                                Swal.fire('Error', 'Failed to delete the email record.',
                                    'error');
                            }
                        });
                    }
                });
            });

        }); // end document.ready
    </script>



    <style>
        /* Ensure the label is on its own line */
        .form-group label {
            display: block;
            width: 100%;
            margin-bottom: 5px;
            font-weight: 600;
        }

        /* Make the Select2 dropdown 100% width */
        .select2-container {
            width: 100% !important;
        }

        /* Apply styling to Select2 multiple selection */
        .select2-container--default .select2-selection--multiple {
            border-radius: 50px !important;
            border: 1px solid rgb(207, 236, 224) !important;
            min-height: 40px !important;
            padding: 5px;
            font-size: 14px;
            background-color: #fff;
            width: 100%;
            transition: border-color 0.3s ease-in-out;
        }

        /* Ensure the Select2 input field appears full-width */
        .select2-container--default .select2-selection--single {
            width: 100% !important;
        }

        /* Focus effect */
        .select2-container--default .select2-selection--multiple:focus,
        .select2-container--default .select2-selection--single:focus {
            border-color: rgb(207, 236, 224) !important;
            box-shadow: 0 0 5px rgb(207, 236, 224) !important;
            outline: none;
        }

        /* Style selected tags */
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background-color: rgb(207, 236, 224) !important;
            color: white !important;
            border-radius: 20px;
            /* padding: 2px 10px !important; */
            border: none;
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            max-width: fit-content;
        }


        /* Style dropdown items */
        .select2-container--default .select2-results__option {
            padding: 10px;
            font-size: 14px;
        }

        /* Highlight hovered item */
        .select2-container--default .select2-results__option--highlighted {
            background-color: rgb(207, 236, 224) !important;
            color: black !important;
        }

        .select2-selection__choice__display {
            color: #495057 !important;
        }
    </style>
@endsection
