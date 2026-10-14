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
        padding-left: 120px !important;
        /* text-align: center !important; */
    }

    .staff-button {
        padding-right: 130px !important;
        text-align: right !important;
    }

    @media screen and (max-width: 767px) {
        .form-container {
            width: 100% !important;
            padding: 16px 14px 25px 14px !important;
            margin: 10px auto !important;
        }

        .staff-title {
            padding-left: 0px !important;
            text-align: left !important;
        }

        .staff-button {
            padding-right: 15px !important;
            text-align: right !important;
        }

        .permission-scroll-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            /* border: 1px solid #ccc; */
            /* Optional: visual scroll area */
        }

        .permission-table {
            min-width: 600px;
            /* or however wide your full table needs */
            width: max-content;
        }

        .card-footer {
            padding: .75rem .50rem !important;

        }

        .card-footer h3 {
            margin-top: 5px !important;

        }
    }

    @media screen and (max-width: 767px) {

        a.mobile_btn {
            padding: 7px 20px !important;
        }
    }



    /* Style the visible select box */
    .select2-container--default .select2-selection--single {
        border: 1px solid #28a745;
        border-radius: 25px;
        height: 38px;
        padding: 5px 12px;
        font-family: 'Segoe UI', sans-serif;
        font-size: 14px;
        color: #333;
    }

    /* Style the dropdown list */
    .select2-container--default .select2-results__option {
        padding: 10px;
        font-size: 14px;
        color: #333;
    }

    /* Style hover effect */
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #28a745;
        color: #fff;
    }
    .form-container {
        width: 80% !important;
        padding-bottom: 30px !important;
    }

</style>
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="row" style="padding-top:15px">



                <div class="col-8">
                    <h4 class="page-title staff-title">Add Staff</h4>
                </div>
                @if (app('hasPermission')(3, 'view'))
                    <div class="col-4 staff-button m-b-2 eye-btn">
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

                        <input type="hidden" name="branch_id" id="branch_id">
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
                                       
                                        <div class="col-12 col-md-12 col-lg-12">
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
                                                <input class="form-control email" type="email" name="email"
                                                    placeholder="Enter Email" required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label><i class="fas fa-phone icon-style"></i> Phone <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <input type="text" name="country_code" value="+91" class="form-control" 
                                                            style="width: 70px; background-color: transparent; border-radius: 25px 0 0 25px; border-right: none; text-align: center;">
                                                    </div>
                                                    <input class="form-control" type="text" name="phone"
                                                        placeholder="Enter 10 Digit Phone" maxlength="10" pattern="\d{10}"
                                                        style="border-radius: 0 25px 25px 0;"
                                                        oninput="this.value = this.value.replace(/[^0-9]/g, '');" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>



                                <div class="col-12 col-md-12 col-lg-12">
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

                                <!-- <div class="col-12"> -->
                                <!-- <div class="row"> -->
                                <div class="col-12 col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-image icon-style"></i> Profile Picture</label>
                                        <input type="file" accept="image/*" class="form-control" name="profile">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-cake-candles icon-style"></i> Birthdate</label>
                                        <input type="date" class="form-control" name="birth_date" id="birth_date">
                                    </div>
                                </div>

                                <script>
                                    document.addEventListener("DOMContentLoaded", function() {
                                        const birthInput = document.getElementById("birth_date");

                                        const today = new Date();
                                        const yesterday = new Date(today);
                                        yesterday.setDate(today.getDate() - 1);

                                        const maxDate = yesterday.toISOString().split("T")[0];
                                        birthInput.setAttribute("max", maxDate);
                                    });
                                </script>

                                <!-- </div> -->
                                <!-- </div> -->
                            </div>


                            <button type="button" class="btn btn-primary text-center d-flex" onclick="nextStep()"
                                style="padding:8px 50px ;float:right">Next</button>
                        </div>

                        <!-- Step 2: Profile & Address -->
                        <div class="form-step" id="step-2" style="display:none;">
                            <div class="row">
                                <div class="col-12">
                                    <!-- Password Fields -->
                                    <div class="row">
                                        <div class="col-12 col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label><i class="fas fa-lock icon-style"></i> Password</label>
                                                <input type="password" class="form-control" id="password"
                                                    name="password" placeholder="Enter Password" required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label><i class="fas fa-lock icon-style"></i> Confirm Password</label>
                                                <input type="password" class="form-control" id="confirm_password"
                                                    name="password_confirmation" placeholder="Confirm Password" required>
                                                <small id="passwordError" class="text-danger"></small>
                                                <!-- Error Message -->
                                            </div>
                                        </div>
                                    </div>



                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <label><i class="fas fa-map-marker-alt icon-style"></i> Address <span
                                                        class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="address"
                                            placeholder="Enter Address" required>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-12 col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label><i class="fas fa-city icon-style"></i> City <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control select2" name="city" id="select-city"
                                                    required>
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
                                                <select class="form-control select2" name="state" id="select-state" required>
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
                                        <select class="form-control select2" name="shift" id="select-shift">
                                            <option value="">Select Shift</option>
                                            <option value="FullDay">Full Day</option>
                                            <option value="Morning">Morning</option>
                                            <option value="Afternoon">Afternoon</option>
                                            <option value="Night">Night</option>
                                        </select>
                                    </div>
                                </div>



                            </div>

                            <button type="button" class="btn btn-danger prv-btn"
                                style="padding:8px 50px;border-radius:50px; float:left" onclick="prevStep()">
                                Previous
                            </button>

                            <button type="button" class="btn btn-primary prv-btn"
                                style="padding:8px 50px;border-radius:50px; float:right" onclick="nextStep()">
                                Submit
                            </button>
                        </div>
                        <br><br><br>
                        <div id="usersuccessMessage" class="alert alert-success" style="display:none;"></div>
                        <div id="usererrorMessage" class="alert alert-danger" style="display:none;"></div>

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
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="table-responsive">
                                                    <div class="permission-scroll-wrapper">
                                                        <table class="table table-bordered text-center permission-table">
                                                            <thead>
                                                                <tr class="text-dark">
                                                                    <th>Module Name</th>
                                                                    <th>View</th>
                                                                    <th>Insert</th>
                                                                    <th>Edit</th>
                                                                    <th>Delete</th>
                                                                    <th>All</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <!-- Your table rows will go here -->
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let storedBranchId = localStorage.getItem('selectedBranchId');
            if (storedBranchId) {
                document.getElementById('branch_id').value = storedBranchId;
            }
        });
        $('#select-city').select2({
            placeholder: "Select City",
            width: '100%'
        });
        $('#select-city').on('select2:open', function() {
            $('.select2-search__field').attr('placeholder', 'Search City');
        });

        $('#select-state').select2({
            placeholder: "Select State",
            width: '100%'
        });
        $('#select-state').on('select2:open', function() {
            $('.select2-search__field').attr('placeholder', 'Search State');
        });

        $('#select-shift').select2({
            placeholder: "Select Shift",
            width: '100%'
        });
        $('#select-shift').on('select2:open', function() {
            $('.select2-search__field').attr('placeholder', 'Search Shift');
        });

        document.getElementById("selectAll").addEventListener("change", function() {
            let checkboxes = document.querySelectorAll("tbody input[type='checkbox']");
            checkboxes.forEach(cb => cb.checked = this.checked);
        });


        let currentStep = 1;

        function nextStep() {
            let isValid = true;

            // Validate all required fields in the current step
            $(`#step-${currentStep} [required]`).each(function() {
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
                        $input.after(
                            `<span class="error-message text-danger">Please enter a valid email address.</span>`
                            );
                        isValid = false;
                    }
                }

                // Phone validation
                if (inputName === 'phone') {
                    const phonePattern = /^\d{10}$/;
                    if (!phonePattern.test(inputValue)) {
                        $input.addClass('is-invalid');
                        $input.after(
                            `<span class="error-message text-danger">Please enter a valid 10-digit phone number.</span>`
                            );
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
                    $imageInput.after(
                        `<span class="error-message text-danger">Only jpeg, png, jpg, gif, svg, webp formats are allowed.</span>`
                        );
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
        $('input, select').on('input change', function() {
            const $input = $(this);
            if ($input.val().trim() !== '') {
                $input.removeClass('is-invalid');
                $input.next('.error-message').remove();
            }
        });




        $(document).ready(function() {

            $('#role-select').select2({
                placeholder: "Select Role",
                width: '100%'
            });
            $('#role-select').on('select2:open', function() {
                $('.select2-search__field').attr('placeholder', 'Search Role');
            });

            $.ajax({
                url: '/api/rolee',
                method: 'GET',
                headers: {
                    "Authorization": "Bearer " + token
                },
                success: function(response) {
                    const roleSelect = $('#role-select');
                    const userRole = role;

                    response.forEach(role => {



                        if (userRole === 'Admin') {
                            if (role.name === 'Admin') {
                                return;
                            }
                        } else {
                            if (role.name === 'Admin') {
                                return;
                            }
                        }


                        const option = $('<option>').val(role.id).text(role.name);
                        roleSelect.append(option);
                    });
                },
                error: function(error) {
                    console.log('Error fetching roles:', error);
                }
            });
        });





        $(document).ready(function() {

            $('#multiStepForm').submit(function(e) {
                // let userId = sessionStorage.getItem('user_id');
                e.preventDefault();
                let isValid = true;

                $('.form-step [required]').each(function() {
                    const $input = $(this);
                    const inputName = $input.attr('name') || '';
                    const inputValue = $input.val()?.trim() || '';

                    $input.removeClass('is-invalid');
                    $input.next('.error-message').remove();

                    // Define custom error messages
                    const fieldMessages = {
                        address: "Address is required.",
                        city: "City is required.",
                        state: "State is required.",

                    };

                    if (inputValue === '') {
                        const message = fieldMessages[inputName];
                        $input.addClass('is-invalid');
                        $input.after(`<span class="error-message text-danger">${message}</span>`);
                        isValid = false;
                    }
                });



                // Validate Password & Confirm Password
                let password = $('#password').val().trim();
                let confirmPassword = $('#confirm_password').val().trim();

                $('#password, #confirm_password').removeClass('is-invalid');
                $('#passwordError').remove();

                if (password === '') {
                    $('#password').addClass('is-invalid');
                    $('#password').after(
                        `<span id="passwordError" class="error-message text-danger">Password is required.</span>`
                        );
                    isValid = false;
                } else if (password.length < 6) {
                    $('#password').addClass('is-invalid');
                    $('#password').after(
                        `<span id="passwordError" class="error-message text-danger">Password must be at least 6 characters.</span>`
                        );
                    isValid = false;
                } else if (password !== confirmPassword) {
                    $('#confirm_password').addClass('is-invalid');
                    $('#confirm_password').after(
                        `<span id="passwordError" class="error-message text-danger">Passwords do not match.</span>`
                        );
                    isValid = false;
                }

                // Validate at least one permission is selected
                let hasPermission = false;
                $('tbody input[type="checkbox"]').each(function() {
                    if ($(this).prop('checked')) {
                        hasPermission = true;
                        return false;
                    }
                });

                if (!hasPermission) {
                    $('#usererrorMessage').html(
                            `<span class="text-danger">At least one permission must be selected.</span>`)
                        .show();
                    isValid = false;
                } else {
                    $('#usererrorMessage').hide();
                }

                if (isValid) {
                    var formData = new FormData(this);
                    var permissions = [];

                    $('tbody tr').each(function() {
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

                    // **Show SweetAlert loader**
                    Swal.fire({
                        title: 'Creating User...',
                        text: 'Please wait while we create the user, send email, and SMS.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: '/api/users',
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            "Authorization": "Bearer " + token
                        },
                        success: function(response) {
                            if (response.status === false) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    html: response.message || 'Something went wrong.',
                                    timer: 4000
                                });
                                return;
                            }

                            const userId = response.user?.id;

                            if (!userId) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    html: 'User ID not returned from API.',
                                    timer: 3000
                                });
                                return;
                            }

                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                html: response.message,
                                timer: 3000,
                                showConfirmButton: false
                            });

                            $('#multiStepForm')[0].reset();

                            setTimeout(function() {
                                window.location.href = "{{ route('user.index') }}";
                            }, 1500);

                            $.ajax({
                                url: `/api/users/${userId}/notify`,
                                method: 'POST',
                                headers: {
                                    "Authorization": "Bearer " + token
                                },
                                success: function() {},
                                error: function(xhr, status, error) {
                                    console.log('Notification error:', error);
                                    console.log('Response:', xhr.responseText);
                                },
                            });
                        },
                        error: function(xhr) {
                            Swal.close();
                            var errorMessage = '';

                            // Handle JSON error with status: false
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;

                                // Validation errors (422)
                            } else if (xhr.status === 422 && xhr.responseJSON?.errors) {
                                var errors = xhr.responseJSON.errors;
                                $.each(errors, function(key, messages) {
                                    errorMessage += messages[0] + '<br>';
                                });

                                // Forbidden or custom errors (403)
                            } else if (xhr.status === 403 && xhr.responseJSON?.error) {
                                errorMessage = xhr.responseJSON.error;

                            } else {
                                errorMessage = 'An unexpected error occurred.';
                            }

                            Swal.fire({
                                icon: 'error',
                                title: 'Opps Sorry!',
                                html: errorMessage,
                                timer: 4000
                            });
                        }
                    });
                }
            });

            // Real-time validation for password match
            $('#password, #confirm_password').on('keyup', function() {
                let password = $('#password').val().trim();
                let confirmPassword = $('#confirm_password').val().trim();

                if (password !== confirmPassword) {
                    $('#passwordError').remove();
                    $('#confirm_password').after(
                        `<span id="passwordError" class="error-message text-danger">Passwords do not match.</span>`
                        );
                } else {
                    $('#passwordError').remove();
                }
            });

            // Fetch Modules for permissions
            fetch('/api/modules')
                .then(response => response.json())
                .then(modules => {
                    const tbody = $('tbody');
                    modules.forEach(module => {
                        const row = $('<tr>').attr('data-module-id', module.id);
                        row.append(`<td>${module.name}</td>`);

                        const permissions = ['view', 'create', 'update', 'delete', 'all'];

                        permissions.forEach(permission => {
                            const checkbox = $('<input>', {
                                type: 'checkbox',
                                class: permission
                            });
                            const td = $('<td>').append(checkbox);
                            row.append(td);

                            if (permission === 'all') {
                                checkbox.change(function() {
                                    row.find('input[type=checkbox]').prop('checked',
                                        checkbox.prop('checked'));
                                });
                            }
                        });

                        tbody.append(row);
                    });
                });
        });
    </script>
@endsection
