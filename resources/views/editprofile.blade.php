@extends('layout.app')
<style>
    .icon-style {
        margin-right: 5px;
        color: #007bff;
    }

    #convert_text {
        height: 120px;
        resize: none;
    }

    #click_to_convert {
        display: flex;
        align-items: center;
        gap: 5px;
        padding: 8px 12px;
        border-radius: 10px;
        margin-bottom: 10px;
    }


    .audio-player-container {
        flex-grow: 1;
        margin-right: 1rem;
    }

    .audio-player-container audio {
        width: 100%;
        max-width: 300px;
    }

    @media (max-width: 991.98px) {
        .audio-player-container {
            margin-right: 0;
            margin-bottom: 1rem;
        }

        .audio-player-container audio {
            max-width: 100%;
        }
    }


    .select2-container--default .select2-selection--multiple {
        box-shadow: none;
        font-size: 14px;
        min-height: 40px !important;
        border-radius: 50px !important;
        padding: 0.469rem 0.75rem;
        border-color: rgb(207, 236, 224) !important;
    }

    .select2-container--default .select2-selection--multiple {
        padding: 4px 8px;
        text-align: left;
        min-height: 38px;
    }

    .select2-selection__rendered {
        text-align: left !important;
    }


    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        background-color: rgb(207, 236, 224) !important;
        color: black !important;
        border-radius: 20px !important;
        /* padding: 2px 10px !important; */
        border: none;
        font-size: 14px;
        font-weight: normal !important;
        text-align: center;
        max-width: fit-content;
    }

    .select2-container--default .select2-results__option--selected {
        background-color: rgb(207 236 224) !important;
        border-radius: 15px !important;
        margin: 2px !important;
        color: black !important;
    }

    .image-preview-container {
        position: relative;
        display: inline-block;
    }

    .preview-img {
        height: 30px;
        width: auto;
        border-radius: 8px;
        margin-right: 5px;
        margin-left: 7px;
    }

    .remove-btn {
        position: absolute;
        top: -6px;
        right: -6px;
        background-color: red;
        color: white;
        border: none;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        font-size: 12px;
        cursor: pointer;
        line-height: 18px;
        text-align: center;
    }

    .patient-title {
        padding-left: 70px !important;
        text-align: center !important;
    }

    .patient-button {
        padding-right: 60px !important;
        text-align: center !important;
    }

    @media (max-width:768px) {
        .patient-title {
            padding-left: 15px !important;
            text-align: left !important;
        }

        .patient-button {
            padding-right: 15px !important;
            text-align: right !important;
        }

    }


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


    @media (max-width: 767px) {
        .profile-btn {
            padding-right: 15px !important;
            text-align: right !important;
        }

        .profile-title {
            text-align: center;
            padding-right: 15px !important;
        }

        .page-title {
            font-size: 19px !important;
            padding-left: 10px !important;
            text-align: left !important;
            padding-top: 6px !important;
        }
    }
</style>
@section('content')

    <div class="page-wrapper">
        <div class="content">
            <div class="row" style="padding-top:15px">
                <div class="col-6">
                    <h4 class="page-title profile-title" style="">Edit Profile</h4>
                </div>
                <div class="col-6  profile-btn m-b-2" style="">
                    <a href="{{ route('profile') }}" class="btn btn-primary btn-rounded float-right">
                        <i class="fa fa-eye"></i> Go To Profile
                    </a>
                </div>
            </div>


            <div class="row">
                <div class="col-12">
                    @php
                        $userRole = optional(Auth::user()->role)->name;
                    @endphp
                    @if($userRole !== 'Patient')
                        <style>
                            .profile-title {
                                text-align: center !important;
                                padding-right: 200px;
                            }

                            .profile-btn {
                                padding-right: 126px !important;
                                text-align: right !important;
                            }
                        </style>
                        <form class="form-container" id="editProfileForm" method="POST" action=""
                            style="width:80% ;padding-bottom: 30px;">
                            @csrf
                            <input type="hidden" name="user_id" id="user_id">


                            <!-- Step 1: Personal Information -->
                            <div class="form-step" id="step-1" style="height:575px">
                                <div class="row">


                                    <div class="col-12">
                                        <div class="row">

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label><i class="fas fa-user icon-style"></i> Full Name</label>
                                                    <input class="form-control" type="text" name="fullname"
                                                        placeholder="Enter Full Name" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>



                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label><i class="fas fa-envelope icon-style"></i> Email <span
                                                            class="text-danger">*</span></label>
                                                    <input class="form-control" type="email" name="email"
                                                        placeholder="Enter Email" required>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label><i class="fas fa-phone icon-style"></i> Phone</label>
                                                    <input class="form-control" type="text" name="phone"
                                                        placeholder="Enter Phone">
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
                                            <div class="col-6">
                                                <div class="form-group">

                                                    <label><i class="fas fa-image icon-style"></i> Profile Picture</label>
                                                    <input type="file" class="form-control" name="profile">

                                                    <div id="imagePreviewContainer" style="margin-top:10px">
                                                        <img id="imagePreview" src="" alt="profile Image"
                                                            style="max-width: 100px; display: none;">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label><i class="fas fa-cake-candles icon-style"></i> Birthdate</label>
                                                    <input type="date" class="form-control" name="birth_date">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <button type="button" class="btn btn-primary text-center d-flex mb-4" onclick="nextStep()"
                                    style="padding:8px 50px ;float:right">Next</button>
                            </div>

                            <!-- Step 2: Profile & Address -->
                            <div class="form-step" id="step-2" style="display:none; height:350px">
                                <div class="row">


                                    <div class="col-12">
                                        <div class="form-group">
                                            <label><i class="fas fa-map-marker-alt icon-style"></i> Address</label>
                                            <input type="text" class="form-control" name="address" placeholder="Enter Address">
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label><i class="fas fa-city icon-style"></i> City</label>
                                                    <select class="form-control select2" name="city" id="user-city">
                                                        <option value="">Select City</option>
                                                        <option value="Surat">Surat</option>
                                                        <option value="Ahemdabad">Ahemdabad</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label><i class="fas fa-flag icon-style"></i> State</label>
                                                    <select class="form-control select2" name="state" id="user-state">
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
                                            <select class="form-control select2" name="shift" id="user-shift">
                                                <option value="">Select Shift</option>
                                                <option value="Morning">Morning</option>
                                                <option value="Afternoon">Afternoon</option>
                                                <option value="Night">Night</option>
                                                <option value="FullDay">FullDay</option>
                                            </select>
                                        </div>
                                    </div>



                                </div>

                                <button type="button" class="btn btn-danger"
                                    style="padding:8px 50px;border-radius:50px; float:left" onclick="prevStep()">
                                    Previous
                                </button>

                                <button type="submit" class="btn btn-primary"
                                    style="padding:8px 50px;border-radius:50px; float:right">
                                    Submit
                                </button>
                            </div>

                            <div id="editprofilesuccessMessage" class="alert alert-success" style="display:none;"></div>
                            <div id="editprofileerrorMessage" class="alert alert-danger" style="display:none;"></div>





                        </form>
                    @endif

                    @if($userRole == 'Patient')
                        <style>
                            .profile-title {
                                text-align: center !important;
                                /* padding-right: 200px; */
                            }

                            .profile-btn {
                                padding-right: 240px !important;
                                text-align: right !important;
                            }
                        </style>
                        <form class="form-container patient-form all-form" id="multiStepForm" method="POST"
                            action="javascript:void(0);" style="width:60% ;padding-bottom: 60px;" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" id="patientId" value="{{ $patient_id ?? '' }}">



                            <div class="form-step  edit-patient-form" id="step-1">
                                <div class="row">

                                    <div class="col-6">
                                        <div class="form-group">
                                            <label><i class="fas fa-user icon-style"></i> Full Name <span
                                                    class="text-danger">*</span></label>
                                            <input class="form-control" type="text" name="fullname"
                                                placeholder="Enter Full Name" required>
                                        </div>
                                    </div>
                                    <div class="col-6 ">
                                        <div class="form-group">
                                            <label><i class="fas fa-phone icon-style"></i> Phone <span
                                                    class="text-danger">*</span></label>
                                            <input class="form-control" type="text" name="phone" placeholder="Enter Phone"
                                                required>
                                        </div>
                                    </div>

                                    <div class="col-6 col-md-6 col-lg-6">

                                        <div class="form-group">
                                            <label><i class="fas fa-envelope icon-style"></i> Email </label>
                                            <input class="form-control" type="email" name="email" placeholder="Enter Email">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label><i class="fas fa-birthday-cake icon-style"></i> Age </label>
                                            <input class="form-control" type="number" name="age" placeholder="Enter Age">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group ">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <label class="mr-2"><i class="fas fa-medkit icon-style"></i> Treatment <span
                                                        class="text-danger">*</span></label>
                                                @if(app('hasPermission')(7, 'create'))
                                                    <a href="{{ route('treatment.create') }}" target="_blank"
                                                        class="btn btn-primary btn-sm">
                                                        <i class="fas fa-plus"></i> Add Treatment
                                                    </a>
                                                @endif
                                            </div>
                                            <input type="hidden" id="selectedTreatmentId"
                                                value="{{ old('treatment_id', $selectedTreatmentId ?? '') }}">

                                            <select class="form-control select2" name="treatment_id" id="treatmentDropdown"
                                                required>
                                                <!-- <option value="">Select Treatment</option> -->
                                            </select>
                                        </div>
                                    </div>








                                    <div class="col-12">
                                        <div class="form-group">
                                            <label><i class="fas fa-tint icon-style"></i> Blood Group</label>
                                            <select class="form-control select2" id="select-blood" name="blood_group">
                                                <option value=""></option>
                                                <option value="A+">A+</option>
                                                <option value="A-">A-</option>
                                                <option value="B+">B+</option>
                                                <option value="B-">B-</option>
                                                <option value="O+">O+</option>
                                                <option value="O-">O-</option>
                                                <option value="O-">O-</option>
                                                <option value="AB-">AB-</option>
                                                <option value="AB+">AB+</option>
                                            </select>
                                        </div>
                                    </div>

                                </div>
                                <button type="button" class="btn btn-primary next-btn"
                                    style="margin-left:5px;padding:8px 50px;float:right;">
                                    Next
                                </button>
                            </div>
                            <!-- Step 2: Additional Information -->
                            <div class="form-step patient-form2" id="step-2" style="display:none;">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label><i class="fas fa-image icon-style"></i> Profile Image</label>
                                            <div>
                                                <img id="profilePreview"
                                                    src="{{ asset($patient->profile ?? 'admin/assets/img/img1.png') }}"
                                                    alt="Profile Image" width="100" height="100"
                                                    style="border-radius: 10px; object-fit: cover; border: 1px solid #ccc;">
                                            </div>
                                            <input type="file" class="form-control mt-2" name="profile" id="profileInput">
                                        </div>
                                    </div>




                                    <div class="col-12">

                                        <div class="form-group">
                                            <label><i class="fas fa-map-marker-alt icon-style"></i> Address</label>
                                            <input type="text" class="form-control" name="address" placeholder="Enter Address">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-6">
                                        <div class="form-group">
                                            <label><i class="fas fa-city icon-style"></i> City</label>
                                            <select class="form-control select2" id="select-city" name="city">
                                                <option value="">Select City</option>
                                                <option value="Surat">Surat</option>
                                                <option value="Ahmedabad">Ahmedabad</option>
                                                <option value="Mumbai">Mumbai</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-6">
                                        <div class="form-group">
                                            <label><i class="fas fa-flag icon-style"></i> State</label>
                                            <select class="form-control select2" id="select-state" name="state">
                                                <option value="">Select State</option>
                                                <option value="Gujarat">Gujarat</option>
                                                <option value="Maharashtra">Maharashtra</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label><i class="fas fa-notes-medical icon-style"></i> Medical History</label>
                                            <textarea class="form-control" name="medical_history"
                                                placeholder="Enter Medical History" style="border-radius:10px"></textarea>
                                        </div>
                                    </div>

                                </div>
                                <div id="editprofilesuccessMessage" class="alert alert-success" style="display:none;"></div>
                                <div id="editprofileerrorMessage" class="alert alert-danger" style="display:none;"></div>
                                <button type="button" class="btn btn-danger prev-btn"
                                    style="padding:8px 50px;border-radius:50px; float:left">
                                    Previous
                                </button>

                                <button type="submit" class="btn btn-primary"
                                    style="padding:8px 50px;border-radius:50px; float:right">
                                    Submit
                                </button>

                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js"></script>

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- //user -->
    <script>
        let currentStep = 1;

        function nextStep() {
            document.getElementById(`step-${currentStep}`).style.display = 'none';
            currentStep++;
            document.getElementById(`step-${currentStep}`).style.display = 'block';
        }

        function prevStep() {
            document.getElementById(`step-${currentStep}`).style.display = 'none';
            currentStep--;
            document.getElementById(`step-${currentStep}`).style.display = 'block';
        }

        $(document).ready(function () {
            $.ajax({
                url: '/api/profile',  // Fetch user profile data
                method: 'GET',
                headers: { "Authorization": "Bearer " + token },
                success: function (response) {

                    if (response.user && response.userDetails) {
                        // $('input[name="username"]').val(response.user.username);
                        $('input[name="fullname"]').val(response.user.fullname);
                        $('input[name="email"]').val(response.user.email);
                        $('input[name="phone"]').val(response.user.phone);
                        $('input[name="birth_date"]').val(response.userDetails.birth_date);
                        $('input[name="address"]').val(response.userDetails.address);
                        $('select[name="city"]').val(response.userDetails.city).trigger('change');
                        $('select[name="state"]').val(response.userDetails.state).trigger('change');
                        $('select[name="shift"]').val(response.userDetails.shift).trigger('change');

                        // ✅ Set Gender Selection
                        $('input[name="gender"][value="' + response.userDetails.gender + '"]').prop("checked", true);

                        // ✅ Show Profile Image Preview
                        if (response.user.profile) {
                            $("#imagePreview").attr("src", response.user.profile).show();
                        }
                    } else {
                        console.log('User or user details not found');
                    }
                },
                error: function (error) {
                    console.log('Error fetching profile:', error);
                }
            });


            $("#editProfileForm").submit(function (e) {
                e.preventDefault(); // Prevent default form submission

                let formData = new FormData(this);


                $.ajax({
                    url: "/api/profile/edit",  // Ensure this matches your API route
                    method: "POST", // Use POST because PUT requests don't send FormData properly in jQuery

                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'Authorization': 'Bearer ' + token,
                    },

                    success: function (response) {
                        $('#editprofilesuccessMessage').text(response.message).show();
                        setTimeout(() => window.location.href = "/profile", 1500);
                    },
                    error: function (response) {
                        $('#editprofileerrorMessage').text("Failed to update profile!").show();
                        console.log('Error fetching profile:', error);
                        if (error.status === 401) {

                            window.location.href = "{{ route('login') }}";
                        }
                    }
                });
            });




        });


    </script>


    <!-- patient -->
    <script>
        $('#profileInput').on('change', function (event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    $('#profilePreview').attr('src', e.target.result);
                };
                reader.readAsDataURL(file);
            }
        });

    </script>


    @php
        $patientId = Auth::check() && Auth::user()->role->name === 'Patient'
            ? \App\Models\Patients::where('login_patient_id', Auth::id())->value('id')
            : null;
    @endphp

    <script>
        $('#user-city').select2({
            placeholder: "Select City",
            width: '100%'
        });

        $('#user-state').select2({
            placeholder: "Select State",
            width: '100%'
        });

        $('#user-shift').select2({
            placeholder: "Select Shift",
            width: '100%'
        });

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

        $('#select-note').select2({
            placeholder: "Select Note type",
            width: '100%'
        });
        $('#select-note').on('select2:open', function () {
            $('.select2-search__field').attr('placeholder', 'Search Note type');
        });

        $('#select-time').select2({
            placeholder: "Select Duration",
            width: '100%'
        });
        $('#select-time').on('select2:open', function () {
            $('.select2-search__field').attr('placeholder', 'Search Duration');
        });



        $('#select-blood').select2({
            placeholder: "Select Blood Group",
            width: '100%'
        });
        $('#select-blood').on('select2:open', function () {
            $('.select2-search__field').attr('placeholder', 'Search Blood Group');
        });
        $(document).ready(function () {
            // Initialize form validation
            var form = $("#multiStepForm");
            form.validate({
                // Validation rules for the entire form
                rules: {
                    treatment_id: "required",
                    fullname: "required",
                    // email: {
                    //     required: true,
                    //     email: true
                    // },
                    phone: {
                        required: true,
                        number: true,
                        minlength: 10,
                        maxlength: 15
                    },
                    age: {
                        required: true,
                        number: true,
                        min: 0
                    },
                    address: "required",
                    city: "required",
                    state: "required",
                    // status: "required",
                    // blood_group: "required",
                    // medical_history: "required",

                },
                messages: {
                    treatment_id: "Please select a treatment",
                    fullname: "Please enter your full name",
                    // email: "Please enter a valid email address",
                    phone: "Please enter a valid phone number",
                    age: {
                        required: "Please enter your age",
                        number: "Age must be a number",
                        min: "Age cannot be negative"
                    },
                    address: "Please enter your address",
                    city: "Please select your city",
                    state: "Please select your state",
                    

                },
                errorElement: 'span',
                errorPlacement: function (error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function (element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });

            // Function to validate the current step
            function validateStep(step) {
                var valid = true;
                step.find('input, select, textarea').each(function () {
                    if (!$(this).valid()) {
                        valid = false;
                    }
                });
                return valid;
            }

            // Next step button click event
            $('.next-btn').click(function () {
                var currentStep = $('#step-1');
                if (validateStep(currentStep)) {
                    currentStep.hide();
                    $('#step-2').show();
                }
            });

            // Previous step button click event
            $('.prev-btn').click(function () {
                var currentStep = $('#step-2');
                currentStep.hide();
                $('#step-1').show();
            });


            fetchCategories();

            function fetchCategories() {
                let selectedTreatmentId = "{{ old('treatment_id', $selectedTreatmentId ?? '') }}";

                $('#treatmentDropdown').select2({
                    placeholder: "Select Treatment",
                    allowClear: true,
                    width: '100%'
                });

                $('#treatmentDropdown').on('select2:open', function () {
                    $('.select2-search__field').attr('placeholder', 'Search Treatment');
                });

                $.ajax({
                    url: "{{ url('/api/treatments') }}",
                    type: "GET",
                    success: function (data) {
                        console.log("API Response:", data);

                        let treatmentDropdown = $('select[name="treatment_id"]');
                        if (treatmentDropdown.length === 0) {
                            console.log("Dropdown not found! Check your HTML.");
                            return;
                        }

                        treatmentDropdown.empty();

                        let treatmentsList = data.treatments || data;

                        $.each(treatmentsList, function (key, treatment) {
                            let isSelected = (treatment.id == selectedTreatmentId) ? 'selected' : '';
                            treatmentDropdown.append('<option value="' + treatment.id + '" ' + isSelected + '>' + treatment.name + '</option>');
                        });
                    },
                    error: function (xhr) {
                        console.log("API Error:", xhr.status, xhr.responseText);
                    }
                });
            }


            var patientId = "{{ $patientId }}";
            let removedSymptomImages = []; // To track removed images


            $.ajax({
                url: `/api/patient/${patientId}`,
                type: "GET",
                dataType: "json",
                headers: { "Authorization": "Bearer " + token },
                success: function (response) {
                    if (response && response.patient) {
                        let patient = response.patient;

                        // Populate other fields
                        $("input[name='fullname']").val(patient.fullname);
                        if (patient.profile) {
                            $('#profilePreview').attr('src', '' + patient.profile); // Ensure path is correct
                        } else {
                            $('#profilePreview').attr('src', '{{ asset("admin/assets/img/img1.png") }}');
                        }
                        $("input[name='email']").val(patient.email);
                        $("input[name='phone']").val(patient.phone);
                        $("input[name='age']").val(patient.age);
                        $("select[name='blood_group']").val(patient.blood_group).trigger('change');
                        $("input[name='address']").val(patient.address);
                        $("select[name='city']").val(patient.city).trigger('change');
                        $("select[name='state']").val(patient.state).trigger('change');
                        // $('#select-city').val(patient.city).trigger('change');
                        // $('#select-state').val(patient.state).trigger('change');
                        $("textarea[name='medical_history']").val(patient.medical_history);
                        $("select[name='status']").val(patient.status);
                        $("select[name='treatment_id']").val(patient.treatment_id);

                    }
                },
                error: function (xhr) {
                    console.error("Error fetching patient data:", xhr);
                    alert("Failed to fetch patient data.");
                }
            });

            // Submit form
            form.on('submit', function (e) {
                e.preventDefault();
                if (form.valid()) {
                    let formData = new FormData(this);
                    formData.append('_method', 'PUT');

                    $.ajax({
                        url: `/api/patient/${patientId}`,
                        type: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        headers: { "Authorization": "Bearer " + token },
                        success: function (response) {
                            $("#editprofilesuccessMessage").text(response.message || 'Patient updated successfully').show();
                            setTimeout(() => window.location.href = "/profile", 1500);
                        },
                        error: function (xhr) {
                            let errorMessage = '';
                            if (xhr.status === 422) {
                                const errors = xhr.responseJSON.errors;
                                $.each(errors, function (key, messages) {
                                    errorMessage += messages[0] + '<br>';
                                });
                            }
                            $('#editprofileerrorMessage').html(errorMessage).show();
                        }
                    });
                }
            });

        });
    </script>
@endsection