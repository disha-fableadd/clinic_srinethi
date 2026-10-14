@extends('layout.app')

<link rel="stylesheet" href="{{ asset(env('IMAGE_PATH').'admin/assets/css/assign-therapy-edit.css') }}">

@section('content')

    <div class="page-wrapper">
        <div class="content">
            <div class="row top-padding">
                <div class="col-8">
                    <h4 class="page-title discharge-title">Edit Assign Therapy</h4>
                </div>
                @if(app('hasPermission')(27, 'view'))
                    <div class="col-4  m-b-2 view-discharge discharge-btn">
                        <a href="{{ route('assign-therapy.index') }}" class="btn btn-primary btn-rounded btn-hdr ">
                            <i class="fa fa-arrow-left m-r-5 icon3"></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    </div>
                @endif
            </div>
            <div class="row">
                <div class="col-12">
                    <form class="form-container" id="editassign-therapyform" action="">
                        @csrf
                        <input type="hidden" id="assignTherapyId" value="{{ $assign_therapy_id ?? '' }}">

                        <!-- Step 1 -->
                        <div class="form-step discharge-form1" id="step-1">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <div class="d-flex align-items-center justify-content-between">


                                            <label class="mr-2"><i class="fas fa-medkit icon-style"></i> Patient <span
                                                    class="text-danger">*</span></label>

                                            @if(app('hasPermission')(5, 'create'))
                                                <a href="{{ route('patients.create') }}" target="_blank"
                                                    class="btn btn-primary btn-sm">
                                                    <i class="fas fa-plus"></i> <span class="hdr-btn-text">Add</span>
                                                </a>
                                            @endif

                                        </div>
                                        <select class="form-control select2" name="patient_id" id="patientDropdown"
                                            required>
                                            <option value="">Select Patient</option>
                                        </select>

                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <label class="mr-2"><i class="fas fa-medkit icon-style"></i> Therapy <span
                                                    class="text-danger">*</span></label>
                                            @if(app('hasPermission')(26, 'create'))
                                                <a href="{{ route('therapy.create') }}" target="_blank"
                                                    class="btn btn-primary btn-sm">
                                                    <i class="fas fa-plus"></i> <span class="hdr-btn-text">Add</span>
                                                </a>
                                            @endif

                                        </div>
                                        <select class="form-control select2" name="therapy_id" id="therapyDropdown"
                                            required>
                                            <option value="">Select Therapy</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <label class="mr-2"><i class="fas fa-medkit icon-style"></i> Doctor <span
                                                    class="text-danger">*</span></label>
                                            @if(app('hasPermission')(3, 'create'))
                                                <a href="{{ route('user.create') }}" target="_blank"
                                                    class="btn btn-primary btn-sm">
                                                    <i class="fas fa-plus"></i> <span class="hdr-btn-text">Add</span>
                                                </a>
                                            @endif

                                        </div>
                                        <select class="form-control select2 doctorSelect" name="doctor_id" id="doctorSelect"
                                            required>
                                            <option value="">Select Doctor</option>
                                        </select>

                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-clock icon-style"></i>Start Date <span
                                                class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="start_date" max="{{ date('Y-m-d') }}"
                                            required>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-hotel icon-style"></i> End Date <span
                                                class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="end_date" min="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-calendar-day icon-style"></i> Therapy Type <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control select2" id="select-type" name="type" required>
                                            <option value="">Select</option>
                                            <option value="virtual">Virtual</option>
                                            <option value="inperson">In-person</option>
                                        </select>

                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-calendar-alt icon-style"></i> Status <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control select2" id="select-status" name="status" required>
                                            <option value="">Select</option>
                                            <option value="scheduled">scheduled</option>
                                            <option value="ongoing">ongoing</option>
                                            <option value="completed">Completed</option>
                                            <option value="cancelled">Cancelled</option>

                                        </select>

                                    </div>
                                </div>
                            </div>



                            <div id="editassigntherapysuccessMessage" class="alert alert-success">
                            </div>
                            <div id="editassigntherapyerrorMessage" class="alert alert-danger"></div>



                            <button type="submit" class="btn btn-primary d-block m-auto">
                                Submit
                            </button>
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
          let branchId = localStorage.getItem('selectedBranchId');
        $('#type').select2({
            placeholder: "Select Type",
            width: '100%'
        });
        $('#type').on('select2:open', function () {
            $('.select2-search__field').attr('placeholder', 'Search Type');
        });

        $('#select-type').select2({
            placeholder: "Select therapy type",
            width: '100%'
        });
        $('#select-type').on('select2:open', function () {
            $('.select2-search__field').attr('placeholder', 'Search therapy type');
        });

        $('#select-status').select2({
            placeholder: "Select Status",
            width: '100%'
        });
        $('#select-status').on('select2:open', function () {
            $('.select2-search__field').attr('placeholder', 'Search Status');
        });

        $.validator.addMethod("dateCheck", function (value, element) {
            var admit = $("input[name='start_date']").val();
            if (!admit) return true; // Skip if admit_date is empty, let 'required' handle it
            var startdate = new Date(admit);
            var endDate = new Date(value);
            return endDate >= startDate;
        }, "End Date must be greater than or equal to Start Date");



        // Fetch Patients
        $.ajax({
            url: "/api/patientss",
            type: "GET",
            data: {
                branch_id: branchId
            },
            dataType: "json",
            success: function (response) {
                let patientDropdown = $('#patientDropdown');

                if (patientDropdown.length === 0) {
                    console.error("Dropdown not found! Check your HTML.");
                    return;
                }

                patientDropdown.empty().append('<option value="">Select Patient</option>');

                if (!response.patients || response.patients.length === 0) {
                    console.warn("No patients found for this user.");
                    return;
                }

                $.each(response.patients, function (index, patient) {
                    // Capitalize first letter only
                    const capitalizedName = patient.fullname.charAt(0).toUpperCase() +
                        patient.fullname.slice(1);

                    patientDropdown.append(
                        `<option value="${patient.id}" data-treatment-id="${patient.treatment_id}">${capitalizedName}</option>`
                    );
                });

                // Initialize Select2 plugin
                patientDropdown.select2({
                    placeholder: "Select Patient",
                    allowClear: true,
                    width: '100%'
                });

                // Set search input placeholder after opening dropdown
                patientDropdown.on('select2:open', function () {
                    $('.select2-search__field').attr('placeholder', 'Search Patient');
                });
            },
            error: function (xhr) {
                console.error("API Error:", xhr.status, xhr.responseText);
            }
        });

      
        //fetch therapy

        $.ajax({
            url: '/api/therapy-list',
            type: 'GET',
            data: {
                branch_id: branchId
            },
            dataType: "json",
            success: function (response) {
                let therapyDropdown = $('#therapyDropdown');

                if (therapyDropdown.length === 0) {
                    console.error("Dropdown not found! Check your HTML.");
                    return;
                }

                therapyDropdown.empty().append('<option value="">Select therapy</option>');

                if (!response.data || response.data.length === 0) {
                    console.warn("No therapies found for this user.");
                    return;
                }

                $.each(response.data, function (index, therapy) {
                    // Capitalize first letter only
                    const capitalizedName = therapy.name.charAt(0).toUpperCase() + therapy
                        .name.slice(1);

                    therapyDropdown.append(
                        `<option value="${therapy.id}">${capitalizedName}</option>`
                    );
                });

                // Initialize Select2 plugin
                therapyDropdown.select2({
                    placeholder: "Select Therapy",
                    allowClear: true,
                    width: '100%'
                });

                // Set search input placeholder after opening dropdown
                therapyDropdown.on('select2:open', function () {
                    $('.select2-search__field').attr('placeholder', 'Search therapy');
                });
            },
            error: function () {
                alert('Failed to load therapies.');
            }
        });

        // Fetch Doctors
        $.ajax({
            url: "/api/doctors",
            type: "GET",
            dataType: "json",
            headers: {
                "Authorization": "Bearer " + token
            },
            success: function (response) {
                let doctorDropdown = $('.doctorSelect');

                if (doctorDropdown.length === 0) {
                    console.error("Dropdown not found! Check your HTML ID.");
                    return;
                }

                doctorDropdown.empty().append('<option value="">Select Doctor</option>');

                let doctorsList = response.doctors || [];

                if (doctorsList.length === 0) {
                    console.warn("No doctors found.");
                    return;
                }

                $.each(doctorsList, function (index, doctor) {
                    // Capitalize first letter only
                    const capitalizedName = doctor.fullname.charAt(0).toUpperCase() + doctor
                        .fullname.slice(1);

                    doctorDropdown.append(
                        `<option value="${doctor.id}">${capitalizedName}</option>`
                    );
                });

                // Initialize Select2 plugin
                doctorDropdown.select2({
                    placeholder: "Select Doctor",
                    allowClear: true,
                    width: '100%'
                });

                // Set search input placeholder after opening dropdown
                doctorDropdown.on('select2:open', function () {
                    $('.select2-search__field').attr('placeholder', 'Search Doctor');
                });
            },
            error: function (xhr) {
                console.error("API Error:", xhr.status, xhr.responseText);
            }
        });


        let assignTherapyId = "{{ $assign_therapy_id ?? '' }}";

        if (assignTherapyId) {
            $.ajax({

                url: `/api/assigned-therapies/${assignTherapyId}`,
                type: "GET",
                headers: {
                    "Authorization": "Bearer " + token
                },
                success: function (response) {
                    // Populate dropdowns
                    $('#patientDropdown').val(response.patient_id).trigger('change');
                    $('#therapyDropdown').val(response.therapy_id).trigger('change');
                    $('#doctorSelect').val(response.doctor_id).trigger('change');

                    // Populate input fields
                    $('input[name="start_date"]').val(response.start_date);
                    $('input[name="end_date"]').val(response.end_date);

                    // Populate select fields
                    $('#select-type').val(response.type).trigger('change');
                    $('#select-status').val(response.status).trigger('change');
                },
                error: function (xhr) {
                    console.error("Failed to fetch assigned therapy", xhr.responseText);
                }
            });
        }


        $(document).ready(function () {
            // Initialize form validation
            var form = $("#editassign-therapyform");
            form.validate({
                rules: {
                    patient_id: "required",
                    therapy_id: "required",
                    doctor_id: "required",
                    start_date: "required",
                    type: "required",
                    status: "required"
                },
                messages: {
                    patient_id: "Please select a patient",
                    therapy_id: "Please select a therapy",
                    doctor_id: "Please select a doctor",
                    start_date: "Please enter the start date",
                    type: "Please select the therapy type",
                    status: "Please select the status"
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



            form.on('submit', function (e) {
                e.preventDefault();

                if (form.valid()) {
                    const formData = {
                        patient_id: $("select[name='patient_id']").val(),
                        therapy_id: $("select[name='therapy_id']").val(),
                        doctor_id: $("select[name='doctor_id']").val(),
                        start_date: $("input[name='start_date']").val(),
                        end_date: $("input[name='end_date']").val(),
                        type: $("select[name='type']").val(),
                        status: $("select[name='status']").val()
                    };

                    const method = assignTherapyId ? 'PUT' : 'POST';
                    const url = assignTherapyId ? `/api/assigned-therapies/${assignTherapyId}` :
                        `/api/assigned-therapies`;

                    $.ajax({
                        url: url,
                        type: method,
                        data: formData,

                        headers: {
                            "Authorization": "Bearer " + token
                        },
                        success: function (response) {
                            $('#editassigntherapysuccessMessage').text(response.message ||
                                'Therapy updated successfully').show();

                            setTimeout(function () {
                                window.location.href =
                                    "{{ route('assign-therapy.index') }}";
                            }, 1500);
                        },
                        error: function (xhr) {
                            var errorMessage = '';
                            if (xhr.status === 422) {
                                var errors = xhr.responseJSON.errors;
                                $.each(errors, function (key, messages) {
                                    errorMessage += messages[0] + '<br>';
                                });
                            } else {
                                errorMessage = "Something went wrong.";
                            }
                            $('#editassigntherapyerrorMessage').html(errorMessage).show();
                        }
                    });

                }
            });

        });
    </script>

@endsection