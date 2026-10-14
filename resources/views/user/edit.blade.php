@extends('layout.app')
<style>
    .container {
        width: 60%;
        margin: auto;
    }

    .header1 {
        background: #cfece0;
        color: black;
        padding: 10px 15px;
        font-size: 18px;
        font-weight: bold;
        margin-top: 50px;
        border-radius: 15px;
    }

    .card-body {

        height: auto;
        padding: 20px !important;
    }

    .card {
        margin: 0;
    }

    table {

        margin-top: 10px;
        border-radius: 10px;
        border: 2px solid #cfece0;
        border-collapse: collapse;
        width: 100%;

    }

    table thead tr th {
        background-color: #cfece0 !important;
    }


    th,
    td {
        padding: 10px;
        text-align: center;
    }

    thead,
    tr {
        border-bottom: 2px solid #cfece0;
    }

    th {
        background: #f4f4f4;
    }

    .select-all {
        margin: 10px 5px;
    }

    .staff-title {
        padding-right: 200px !important;
        text-align: center !important;
    }

    .staff-button {
        padding-left: 220px !important;
        text-align: center !important;
    }

    .form-container {
        width: 80% !important;
        padding-bottom: 30px !important;
    }


    @media screen and (max-width: 767px) {
        .form-container {
            width: 100% !important;
            padding: 16px 14px 25px 14px !important;
            margin: 10px auto !important;
        }

        .staff-title {
            padding-right: 0px !important;
            padding-left: 0px !important;
            text-align: left !important;
        }

        .staff-button {
            padding-left: 0px !important;
            padding-right: 15px !important;
            text-align: right !important;
        }

        .permission-scroll-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border: 1px solid #ccc;
            /* Optional: visual scroll area */
        }

        .permission-table {
            min-width: 600px;
            /* or however wide your full table needs */
            width: max-content;
        }
    }
</style>
@section('content')

    <div class="page-wrapper">
        <div class="content">
            <div class="row" style="padding-top:15px">
                <div class="col-6">
                    <h4 class="page-title staff-title">Edit Staff</h4>
                </div>
                @if(app('hasPermission')(3, 'view'))
                    <div class="col-6 staff-button m-b-2">
                        <a href="{{ route('user.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                            <i class="fa fa-arrow-left m-r-5"></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    </div>
                @endif
            </div>


            <div class="row">
                <div class="col-12">
                    <form class="form-container all-form" id="multiStepForm" method="POST" action="">
                        @csrf
                        <input type="hidden" name="user_id" id="user_id">


                        <!-- Step 1: Personal Information -->
                        <div class="form-step" id="step-1">
                            <div class="row">
                                @php
                                    $currentProjectTypeId = (int) \App\Models\Setting::getValue('project_type_id', 1);
                                @endphp
                                @if ($currentProjectTypeId !== 3)
                                <div class="col-12">
                                    <div class="form-group">
                                        <label><i class="fas fa-user-tag icon-style"></i> Role <span
                                                        class="text-danger">*</span></label>
                                        <select class="form-control select2" name="role_id" id="role-select" required>
                                            <option value="">Select Role</option>
                                        </select>
                                    </div>
                                </div>
                                @endif

                                <div class="col-12">
                                    <div class="row">
                                       
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label><i class="fas fa-user icon-style"></i> Full Name <span
                                                        class="text-danger">*</span></label>
                                                <input class="form-control" type="text" name="fullname"
                                                    placeholder="Enter Full Name" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>



                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-12 col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label><i class="fas fa-envelope icon-style"></i> Email <span
                                                        class="text-danger">*</span></label>
                                                <input class="form-control" type="email" name="email"
                                                    placeholder="Enter Email" required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label><i class="fas fa-phone icon-style"></i> Phone <span
                                                        class="text-danger">*</span></label>
                                                <input class="form-control" type="number" min="0" name="phone"
                                                    placeholder="Enter Phone" required>
                                            </div>
                                        </div>
                                    </div>

                                </div>



                                <div class="col-12">
                                    <div class="form-group">
                                        <label><i class="fas fa-venus-mars icon-style"></i> Gender</label>
                                        <div class="form-control">
                                            <div class="form-check-inline">
                                                <input type="radio" name="gender" class="form-check-input" value="Male"
                                                    required> Male
                                            </div>
                                            <div class="form-check-inline">
                                                <input type="radio" name="gender" class="form-check-input" value="Female"
                                                    required> Female
                                            </div>
                                            <div class="form-check-inline">
                                                <input type="radio" name="gender" class="form-check-input" value="Other"
                                                    required> Other
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-12 col-md-6 col-lg-6">
                                            <div class="form-group">

                                                <label><i class="fas fa-image icon-style"></i> Profile Picture</label>
                                        <input type="file" accept="image/*" class="form-control" name="profile">
                                                <div id="imagePreviewContainer">
                                                    <img id="imagePreview" src="" alt="profile Image"
                                                        style="max-width: 100px; display: none;">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label><i class="fas fa-cake-candles icon-style"></i> Birthdate</label>
                                                <input type="date" class="form-control" name="birth_date" id="birth_date">
                                            </div>
                                        </div>

                                        <script>
                                            document.addEventListener("DOMContentLoaded", function () {
                                                const birthDateInput = document.getElementById("birth_date");

                                                const today = new Date();
                                                const yesterday = new Date(today);
                                                yesterday.setDate(today.getDate() - 1);

                                                const maxDate = yesterday.toISOString().split("T")[0];
                                                birthDateInput.setAttribute("max", maxDate);
                                            });
                                        </script>


                                    </div>
                                </div>
                            </div>


                            <button type="button" class="btn btn-primary text-center d-flex" onclick="nextStep()"
                                style="padding:8px 50px ;float:right">Next</button>
                        </div>

                        <!-- Step 2: Profile & Address -->
                        <div class="form-step" id="step-2" style="display:none;">
                            <div class="row">


                                <div class="col-12">
                                    <div class="form-group">
                                        <label><i class="fas fa-map-marker-alt icon-style"></i> Address <span
                                                        class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="address" placeholder="Enter Address"
                                            required>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-12 col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label><i class="fas fa-city icon-style"></i> City <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control select2" id="select-city" name="city" required>
                                                    <option value="">Select City</option>
                                                    <option value="Surat">Surat</option>
                                                    <option value="Ahemdabad">Ahemdabad</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label><i class="fas fa-flag icon-style"></i> State <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control select2" id="select-state" name="state"
                                                    required>
                                                    <option value="">Select State</option>
                                                    <option value="Gujarat">Gujarat</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Shift Field -->
                                <div class="col-12">
                                    <div class="form-group">
                                        <label><i class="fas fa-clock icon-style"></i> Shift</label>
                                        <select class="form-control select2" id="select-shift" name="shift">
                                            <option value="">Select Shift</option>
                                            <option value="Morning">Morning</option>
                                            <option value="Afternoon">Afternoon</option>
                                            <option value="Night">Night</option>
                                            <option value="FullDay">Full Day</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Salary Field
                                                            <div class="col-12">
                                                                <div class="form-group">
                                                                    <label><i class="fas fa-dollar-sign icon-style"></i> Salary</label>
                                                                    <input type="number" class="form-control" name="salary" placeholder="Enter Salary"
                                                                        >
                                                                </div>
                                                            </div> -->

                            </div>
                            <div id="editusersuccessMessage" class="alert alert-success" style="display:none;"></div>
                            <div id="editusererrorMessage" class="alert alert-danger" style="display:none;"></div>






                            <button type="button" class="btn btn-danger"
                                style="padding:8px 50px;border-radius:50px; float:left" onclick="prevStep()">
                                Previous
                            </button>

                            <button type="button" class="btn btn-primary"
                                style="padding:8px 50px;border-radius:50px; float:right" onclick="nextStep()">
                                Submit
                            </button>
                        </div>


                        <br><br>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="card" style="border: 1px solid #87ceb0;">
                                    <div class="card-footer text-right" style="background-color:#87ceb0">
                                        <h3 style="float:left" class="text-dark"><i
                                                class="fa fa-info-circle icon-style2"></i> Permissions</h3>
                                        <span class="btn btn-primary btn-rounded btn-hdr" style="color:black">
                                            <input type="checkbox" id="selectAll"> Select All Modules
                                        </span>
                                    </div>

                                    <div class="card-body">
                                        <div class="permission-scroll-wrapper">
                                            <table class="pb-5 permission-table">
                                                <thead class="text-center">
                                                    <tr>
                                                        <th>Module Name</th>
                                                        <th>View</th>
                                                        <th>Insert</th>
                                                        <th>Edit</th>
                                                        <th>Delete</th>
                                                        <th>All</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>



                    </form>


                </div>
            </div>
        </div>
    </div>

    <!-- in permission if all view,create,update,delete checkbox checked also check the checkbox all  -->

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script>


        $('#select-city').select2({
            placeholder: "Select City",
            width: '100%'
        });
        $('#select-city').on('select2:open', function () {
            $('.select2-search__field').attr('placeholder', 'Search City');
        });

        $('#select-state').select2({
            placeholder: "Select State",
            width: '100%'
        });
        $('#select-state').on('select2:open', function () {
            $('.select2-search__field').attr('placeholder', 'Search State');
        });

        $('#select-shift').select2({
            placeholder: "Select Shift",
            width: '100%'
        });
        $('#select-shift').on('select2:open', function () {
            $('.select2-search__field').attr('placeholder', 'Search Shift');
        });





        $(document).ready(function () {
            // Preview the selected image
            $('#profileImageInput').on('change', function (event) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#imagePreview').attr('src', e.target.result).show();
                };
                reader.readAsDataURL(event.target.files[0]);
            });
        });

        document.getElementById("selectAll").addEventListener("change", function () {
            let checkboxes = document.querySelectorAll("tbody input[type='checkbox']");
            checkboxes.forEach(cb => cb.checked = this.checked);
        });

        let currentStep = 1;
        function nextStep() {
            let isValid = true;

            // Validate all required fields in the current step
            $(`#step-${currentStep} [required]`).each(function () {
                const $input = $(this);
                const inputName = $input.attr('name');
                const inputValue = $input.val().trim();

                // Clear previous errors
                $input.removeClass('is-invalid');
                $input.next('.error-message').remove();

                // Custom error messages
                const fieldMessages = {
                    role_id: "Role is required.",
                    fullname: "Fullname is required.",
                    email: "Email is required.",

                };

                // Check if input is empty
                if (inputValue === '') {
                    const message = fieldMessages[inputName] || "This field is required.";
                    $input.addClass('is-invalid');
                    $input.after(`<span class="error-message text-danger">${message}</span>`);
                    isValid = false;
                    return; // Skip further validation for this input
                }

                // Email validation
                if (inputName === 'email') {
                    const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                    if (!emailPattern.test(inputValue)) {
                        $input.addClass('is-invalid');
                        $input.after(`<span class="error-message text-danger">Please enter a valid email address.</span>`);
                        isValid = false;
                    }
                }

                // Phone validation
                if (inputName === 'phone') {
                    const phonePattern = /^\d{10}$/;
                    if (!phonePattern.test(inputValue)) {
                        $input.addClass('is-invalid');
                        $input.after(`<span class="error-message text-danger">Please enter a valid 10-digit phone number.</span>`);
                        isValid = false;
                    }
                }
            });

            // Image validation (if selected)
            const $imageInput = $(`#step-${currentStep} input[type="file"][name="profile"]`);
            if ($imageInput.length && $imageInput[0].files.length > 0) {
                const file = $imageInput[0].files[0];
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg+xml', 'image/webp'];
                const maxSize = 2 * 1024 * 1024; // 2MB

                $imageInput.removeClass('is-invalid');
                $imageInput.next('.error-message').remove();

                if (!allowedTypes.includes(file.type)) {
                    $imageInput.addClass('is-invalid');
                    $imageInput.after(`<span class="error-message text-danger">Only jpeg, png, jpg, gif, svg, webp formats are allowed.</span>`);
                    isValid = false;
                }

                if (file.size > maxSize) {
                    $imageInput.addClass('is-invalid');
                    $imageInput.after(`<span class="error-message text-danger">File size must be less than 2MB.</span>`);
                    isValid = false;
                }
            }

            if (isValid) {
                // Check if it's the last step
                if ($(`#step-${currentStep + 1}`).length === 0) {
                    $('#multiStepForm').trigger('submit');
                } else {
                    $(`#step-${currentStep}`).hide();
                    currentStep++;
                    $(`#step-${currentStep}`).show();
                }
            }
        }

        function prevStep() {
            $(`#step-${currentStep}`).hide();
            currentStep--;
            $(`#step-${currentStep}`).show();
        }

        // Reset validation on input change
        $('input, select').on('input change', function () {
            const $input = $(this);
            if ($input.val().trim() !== '') {
                $input.removeClass('is-invalid');
                $input.next('.error-message').remove();
            }
        });



        $(document).ready(function () {


            var userId = "{{ $user_id }}";


           




            function fetchRolesDropdown(userRole, selectedRoleId = null) {
                $.ajax({
                    url: '/api/rolee',
                    method: 'GET',
                    headers: { "Authorization": "Bearer " + token },
                    success: function (response) {
                        const roleSelect = $('#role-select');
                        roleSelect.empty().append('<option value="">Select Role</option>');

                        response.forEach(roleItem => {
                            // Apply role restriction logic
                            if (userRole === 'SuperAdmin' && roleItem.name === 'SuperAdmin') return;
                            if (userRole === 'Admin' && (roleItem.name === 'SuperAdmin' || roleItem.name === 'Admin')) return;
                            if (userRole !== 'SuperAdmin' && userRole !== 'Admin' &&
                                (roleItem.name === 'SuperAdmin' || roleItem.name === 'Admin')) return;

                            // Append option
                            const isSelected = selectedRoleId && selectedRoleId == roleItem.id ? 'selected' : '';
                            roleSelect.append(`<option value="${roleItem.id}" ${isSelected}>${roleItem.name}</option>`);
                        });

                        // Initialize Select2 plugin
                        roleSelect.select2({
                            placeholder: "Select Role",
                            allowClear: true,
                            width: '100%'
                        });

                        roleSelect.on('select2:open', function () {
                            $('.select2-search__field').attr('placeholder', 'Search Role');
                        });
                    },
                    error: function (error) {
                        console.error('Error fetching roles:', error);
                    }
                });
            }





            fetch('/api/modules')
                .then(response => response.json())
                .then(modules => {
                    const tbody = $('tbody');
                    modules.forEach(module => {
                        const row = $('<tr>').attr('data-module-id', module.id);
                        row.append(`<td>${module.name}</td>`);

                        const permissions = ['view', 'create', 'update', 'delete', 'all'];

                        permissions.forEach(permission => {
                            const checkbox = $('<input>', { type: 'checkbox', class: permission });
                            const td = $('<td>').append(checkbox);
                            row.append(td);

                            // Check/Uncheck all checkboxes in the row if "All" is checked
                            if (permission === 'all') {
                                checkbox.change(function () {
                                    row.find('input[type=checkbox]').prop('checked', checkbox.prop('checked'));
                                });
                            }
                        });

                        tbody.append(row);
                    });

                    // Automatically check/uncheck "All" based on other checkboxes
                    $(document).on('change', 'tbody input[type="checkbox"]', function () {
                        let row = $(this).closest('tr');
                        let view = row.find('.view').prop('checked');
                        let create = row.find('.create').prop('checked');
                        let update = row.find('.update').prop('checked');
                        let del = row.find('.delete').prop('checked');

                        // Check "All" if all others are checked
                        if (view && create && update && del) {
                            row.find('.all').prop('checked', true);
                        } else {
                            row.find('.all').prop('checked', false);
                        }
                    });

                    if (userId) {
                        $.ajax({
                            url: '/api/users/' + userId,
                            method: 'GET',
                            headers: { "Authorization": "Bearer " + token },
                            success: function (data) {
                                console.log('User Data:', data);

                                data.permissions.forEach(permission => {
                                    var moduleRow = $('tr[data-module-id="' + permission.module_id + '"]');

                                    if (moduleRow.length === 0) {
                                        console.log('Module row not found for module_id:', permission.module_id);
                                    }

                                    ['view', 'create', 'update', 'delete'].forEach(function (permissionType) {
                                        var checkbox = moduleRow.find('.' + permissionType);
                                        if (checkbox.length) {
                                            if (permission[permissionType] == 1) {
                                                checkbox.prop('checked', true);
                                            }
                                        }
                                    });
                                });

                                // Trigger change event to update "All" checkboxes on page load
                                $('tbody input[type="checkbox"]').trigger('change');
                            },
                            error: function (error) {
                                console.log('Error fetching user data:', error);
                            }
                        });
                    }
                });

            if (userId) {
                $.ajax({
                    url: '/api/users/' + userId,
                    method: 'GET',
                    headers: { "Authorization": "Bearer " + token },
                    success: function (data) {

                        // $('input[name="username"]').val(data.username);
                        $('input[name="fullname"]').val(data.fullname);
                        $('input[name="email"]').val(data.email);
                        $('input[name="phone"]').val(data.phone);
                        $('input[name="gender"][value="' + data.gender + '"]').prop('checked', true);
                        $('input[name="birth_date"]').val(data.birth_date);
                        $('input[name="address"]').val(data.address);
                        $('select[name="city"]').val(data.city).trigger('change');
                        $('select[name="state"]').val(data.state).trigger('change');
                        $('select[name="shift"]').val(data.shift).trigger('change');

                        $('input[name="salary"]').val(data.salary);
                        $('#role-select').val(data.role_id);
                        fetchRolesDropdown(data.roleName, data.role_id);


                        if (data.profile) {
                            $('#imagePreview').attr('src', data.profile).show();
                            $('#imagePreviewContainer').show();
                        }
                    },
                    error: function (error) {
                        console.log('Error fetching user data:', error);
                    }
                });

                $("#multiStepForm").on('submit', function (e) {
                    e.preventDefault();

                    // Show loader
                    Swal.fire({
                        title: 'Please wait...',
                        text: 'Updating user information and sending email.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    var formData = new FormData(this);
                    formData.append('_method', 'PUT');
                    var permissions = [];
                    $('tbody tr').each(function () {
                        var moduleId = $(this).data('module-id');
                        var modulePermissions = {
                            module_id: moduleId,
                            create: $(this).find('.create').prop('checked'),
                            view: $(this).find('.view').prop('checked'),
                            update: $(this).find('.update').prop('checked'),
                            delete: $(this).find('.delete').prop('checked'),
                        };
                        permissions.push(modulePermissions);
                    });

                    formData.append('permissions', JSON.stringify(permissions));

                    $.ajax({
                        url: '/api/users/' + userId,
                        method: 'POST',
                        headers: {
                            "Authorization": "Bearer " + token,
                        },
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function (response) {
                            const updatedUserId = response.user.id;
                            // Hide loader
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                html: response.message, // + '<br>Email Sent: ' + (response.email_sent ? '✅' : '❌'),
                                timer: 3000,
                                showConfirmButton: false
                            });

                            $('#editusersuccessMessage').text(response.message).show();
                            $('#editusererrorMessage').hide();
                            setTimeout(function () {
                                window.location.href = "{{ route('user.index') }}";
                            }, 2000);

                            // 👇 Call the second API to send update notification
                            $.ajax({
                                url: `/api/users/${updatedUserId}/notify?type=edit`,
                                method: 'POST',
                                headers: {
                                    "Authorization": "Bearer " + token
                                },
                                success: function () {
                                    // Notification sent (optional: log or toast)
                                },
                                error: function (xhr, status, error) {
                                    // console.log('Notification error:', error);
                                    // console.log('Response:', xhr.responseText);
                                }
                            });
                        },
                        error: function (xhr, status, error) {
                            // Hide loader
                            Swal.close();

                            const response = xhr.responseJSON || {};
                            const errorMessage = response.message || (response.errors && Object.values(response.errors)[0]?.[0]) || 'Unable to update user.';

                            $('#editusererrorMessage').text(errorMessage).show();
                            $('#editusersuccessMessage').hide();
                        }
                    });
                });
            }
        });
    </script>



@endsection
