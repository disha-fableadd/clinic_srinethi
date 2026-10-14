@extends('layout.app')

<style>
    .form-group label {
        display: block;
        width: 100%;
        margin-bottom: 5px;
        font-weight: 600;
    }

    .swal-confirm-btn {
        color: rgb(58, 58, 58) !important;
    }

    /* Cancel button text white (for visibility on red background) */
    .swal-cancel-btn {
        color: white !important;
    }

    /* Make the Select2 dropdown 100% width */
    .select2-container {
        width: 100% !important;
    }

    /* Apply styling to Select2 multiple selection */
    .select2-container--default .select2-selection--multiple {
        border-radius: 50px !important;
        border: 1px solid rgb(207 236 224) !important;
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
        border-color: rgb(207 236 224) !important;
        box-shadow: 0 0 5px rgb(207 236 224) !important;
        outline: none;
    }

    /* Style selected tags */
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        background-color: rgb(207 236 224) !important;
        color: black !important;
        border-radius: 20px;
        /* padding: 2px 10px !important; */
        border: none;
        font-size: 14px;
        font-weight: 500;
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
        background-color: rgb(207 236 224) !important;
        color: black !important;
    }
</style>
@section('content')
    <div class="page-wrapper">
        <div class="content" style="height:100vh">
            <div class="row">
                <div class="col-sm-8 col-8">

                </div>

            </div>

            <div class="row mt-2">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header" style="background-color:#f89884;">
                            <h3 class="card-title d-inline-block text-white"><i class="fas fa-sms px-2"
                                    style="font-size:20px"></i>SMS </h3>

                            <button class="btn btn-rounded btn-hdr" id="exportButton"
                                style="background-color: #fed9cf;">
                                <i class="fa fa-download"></i> <span class="hdr-btn-text">Export</span>
                            </button>
                            <a href="{{ route('sms.create') }}" class="btn btn-rounded btn-hdr"
                                style="background-color: #fed9cf;"><i class="fa fa-plus"></i> <span class="hdr-btn-text">Create</span>
                            </a>


                            <a href="#" class="btn btn-rounded btn-hdr"
                                style="background-color: #fed9cf; margin-right:10px" data-toggle="modal"
                                data-target="#sendSmsModal">
                                <i class="fa fa-paper-plane"></i> <span class="hdr-btn-text">Send</span>
                            </a>



                        </div>
                        <div class="card-body ">
                            <div class="table-responsive">
                                <div id="demo_info" class="box"></div>
                                <table id="smstbl" class="table  custom-table">
                                    <thead style="background-color:#ff8e29;" class="text-center">
                                        <tr>


                                            <th>Name</th>
                                            <th>Template Name</th>
                                            <th>Service</th>
                                            <th>Sender Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="inventoryBody">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="sendSmsModal" tabindex="-1" role="dialog" aria-labelledby="sendSmsModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color:#f89884;">
                            <h5 class="modal-title text-white" id="sendSmsModalLabel"><i class="fa fa-paper-plane"></i> <span class="hdr-btn-text">Send</span>
                                SMS</h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="sendSmsForm">
                                <div class="form-group">
                                    <label for="selectUser">Select User</label>
                                    <select class="form-control" id="selectUser" name="selectUser[]" multiple>
                                        <!-- Options will be populated dynamically -->
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="selectTemplate">Select Template</label>
                                    <select class="form-control" id="selectTemplate" name="selectTemplate">
                                        <!-- Options will be populated dynamically -->
                                    </select>
                                </div>

                                <!-- Display Selected Users -->
                                <div class="form-group">
                                    <label>Selected Template Details:</label>
                                    <ul id="selectedTemplateDetails" class="list-group">
                                        <!-- Selected template details will appear here -->
                                    </ul>
                                </div>

                            </form>


                        </div>
                        <div class="modal-footer">

                            <button type="button" class="btn btn-primary" onclick="sendSms()">Send SMS</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css">
    <script src=" https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>






<script>
    // $(document).on('click', '#exportButton', function() {
    //     window.location.href = "{{ route('sms.export') }}";
    // });


     $(document).on('click', '#exportButton', function() {
    let branchId = localStorage.getItem('selectedBranchId');

    if (!branchId) {
        alert("Branch ID not found in localStorage!");
        return;
    }

    window.location.href = "{{ route('sms.export') }}" + "?branch_id=" + branchId;
});


    $(document).ready(function() {
        // Fetch branchId from localStorage
        let branchId = localStorage.getItem('selectedBranchId');

        // When the modal is opened
        $('#sendSmsModal').on('show.bs.modal', function() {
            // Fetch users
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
                    $('#selectUser').select2({
                        placeholder: "Select users",
                        allowClear: true,
                        width: '100%',
                        dropdownParent: $('#sendSmsModal')
                    });
                },
                error: function() {
                    alert('Failed to fetch users');
                }
            });

            // Fetch templates
            $.ajax({
                url: '/api/sms-templates',
                method: 'GET',
                success: function(response) {
                    var templates = response.data || [];
                    var options = '<option value="">-- Select Template --</option>';
                    $.each(templates, function(index, template) {
                        options += `<option value="${template.id}">${template.name}</option>`;
                    });
                    $('#selectTemplate').html(options);
                    $('#selectTemplate').select2({
                        placeholder: "-- Select Template --",
                        allowClear: true,
                        width: '100%',
                        minimumResultsForSearch: 0,
                        dropdownParent: $('#sendSmsModal')
                    });
                    $('#selectTemplate').on('select2:open', function() {
                        $('.select2-search__field').attr('placeholder', 'Search templates');
                    });
                },
                error: function() {
                    alert('Failed to fetch templates');
                }
            });
        });

        // Fetch and display template details when a template is selected
        $('#selectTemplate').on('change', function() {
            var templateId = $(this).val();
            if (!templateId) {
                $('#selectedTemplateDetails').empty();
                return;
            }
            $.ajax({
                url: `/api/sms-templates/${templateId}`,
                method: 'GET',
                success: function(response) {
                    var template = response.data;
                    var detailsHtml = `
                        <li class="list-group-item">
                            <strong>Name:</strong> ${template.name || 'N/A'}<br>
                            <strong>Content:</strong> ${template.content || 'N/A'}
                        </li>
                    `;
                    $('#selectedTemplateDetails').html(detailsHtml);
                },
                error: function() {
                    $('#selectedTemplateDetails').html('<li class="list-group-item text-danger">Failed to load template details.</li>');
                }
            });
        });

        // Fetch SMS data and populate the table
        $.ajax({
            url: '/sms/data',
            method: 'GET',
            data: {
                branch_id: branchId
            },
            success: function(response) {
                var smsData = response.data || response;
                var table = $('#smstbl').DataTable();
                if ($.fn.DataTable.isDataTable("#smstbl")) {
                    table.destroy();
                }
                var rows = '';
                $.each(smsData, function(index, sms) {
                    var userFullName = 'N/A';
                    if (Array.isArray(sms.user) && sms.user.length > 0) {
                        userFullName = sms.user.map(function(user) {
                            return user.fullname;
                        }).join(', ');
                    } else if (sms.user && sms.user.fullname) {
                        userFullName = sms.user.fullname;
                    }
                    var smsTemplateName = (sms.template && sms.template.name) ? sms.template.name : 'N/A';
                    var createdAt = sms.created_at ? new Date(sms.created_at).toLocaleDateString() : 'N/A';
                    rows += `
                        <tr>
                            <td>${userFullName}</td>
                            <td>${smsTemplateName}</td>
                            <td>${sms.service}</td>
                            <td>${createdAt}</td>
                            <td class="text-center">
                                <i class="fa fa-trash-o m-r-5 icon2 delete-sms" style="cursor:pointer;padding: 8px; background-color: #f89884; color: white; border-radius: 10px;" data-id="${sms.id}"></i>
                            </td>
                        </tr>
                    `;
                });
                $('#inventoryBody').html(rows);
                $('#smstbl').DataTable({
                    "paging": true,
                    "searching": true,
                    "ordering": true,
                    "destroy": true
                });
            },
            error: function() {
                alert('Failed to fetch SMS data');
            }
        });

        // Update selected users list when the selection changes
        $('#selectUser').on('change', function() {
            var selectedUsers = $(this).val();
            var selectedUsersList = $('#selectedUsersList');
            selectedUsersList.empty();
            $.each(selectedUsers, function(index, userId) {
                selectedUsersList.append('<li class="list-group-item" data-user-id="' + userId + '">' + userId + '</li>');
            });
        });
    });

    function sendSms() {
        var selectedUsers = $('#selectUser').val();
        var selectedTemplate = $('#selectTemplate').val();
        var selectedService = "twilio";
        var senderId = "{{ auth()->user()->id }}";

        if (!selectedUsers || selectedUsers.length === 0 || !selectedTemplate) {
            Swal.fire({
                icon: 'warning',
                title: 'Validation Error',
                text: 'Please select at least one user and a template.'
            });
            return;
        }

        $.ajax({
            url: "/api/send-sms",
            type: 'POST',
            data: {
                user_ids: selectedUsers,
                template_id: selectedTemplate,
                sender_id: senderId,
                service: selectedService,
                  branch_id: branchId
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: response.message || 'SMS sent successfully!',
                }).then(() => {
                    $('#sendSmsModal').modal('hide');
                    $('#sendSmsForm')[0].reset();
                    $('#selectUser').val(null).trigger('change');
                    $('#selectTemplate').val(null).trigger('change');
                    $('#selectedUsersList').empty();
                    location.reload();
                });
            },
            error: function(xhr) {
                let message = 'Failed to send SMS. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message,
                });
            }
        });
    }

    $(document).on('click', '.delete-sms', function() {
        var smsId = $(this).data('id');
        var row = $(this).closest('tr');
        if (!smsId) {
            Swal.fire('Error', 'SMS ID is missing!', 'error');
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
                    url: `/api/sms/${smsId}`,
                    type: 'DELETE',
                    success: function(response) {
                        Swal.fire({
                            title: 'Deleted!',
                            text: 'SMS deleted successfully!',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            row.remove();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Failed to delete SMS.', 'error');
                    }
                });
            }
        });
    });
</script>


@endsection
