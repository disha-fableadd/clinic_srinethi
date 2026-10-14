@extends('layout.app')
<link rel="stylesheet" href="{{ asset(env('IMAGE_PATH').'admin/assets/css/appointment-edit.css') }}">

@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="row top-padding">
                <div class="col-6">
                    <h4 class="page-title appointment-title">Edit Appointment</h4>
                </div>
                @if (app('hasPermission')(6, 'view'))
                    <div class="col-6 appointment-button m-b-2 eye-btn">
                        <a href="{{ route('appointment.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                            <i class="fa fa-arrow-left m-r-5 icon3"></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    </div>
                @endif
            </div>
            <div class="row">
                <div class="col-12">
                    <form class="form-container all-form appo-form" id="appointmentForm" method="POST" action="">
                        @csrf
                        <input type="hidden" id="appointment_id" name="appointment_id">
                        <!-- Step 1 -->
                        <div class="form-step" id="step-1">
                            <!-- Patient -->
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label><i class="fas fa-user-injured icon-style"></i> Patient <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control select2" name="patient_id" id="patientDropdown"
                                            required>
                                            <option value="">Select Patient</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Treatment + Doctor -->
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label><i class="fas fa-stethoscope icon-style"></i> Treatment <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control select2 treatmentDropdown" id="treatment_id"
                                            name="treatment_id" required>
                                            <option value="">Select Treatment</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label><i class="fas fa-user-md icon-style"></i> Doctor <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control select2 doctorSelect" id="doctorSelect" name="doctor_id"
                                            required>
                                            <option value="">Select Doctor</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Type + Status -->
                            @php
                                $currentProjectTypeId = (int) \App\Models\Setting::getValue('project_type_id', 1);
                            @endphp
                            @if($currentProjectTypeId !== 3)
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-calendar-day icon-style"></i> Appointment Type <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control select2" id="appointment_type" name="appoint_type"
                                            required>
                                            <option value="">Select</option>
                                            <option value="virtual">Virtual</option>
                                            <option value="in-person">In-person</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-calendar-alt icon-style"></i> Status <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control select2" id="status" name="status" required>
                                            <option value="">Select</option>
                                            <option value="upcoming">Upcoming</option>
                                            <option value="confirmed">Confirmed</option>
                                            <option value="completed">Completed</option>
                                            <option value="cancelled">Cancelled</option>
                                            <option value="follow-up">Follow-Up</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($currentProjectTypeId !== 3)
                            <button type="button" class="btn btn-primary next-btn">
                                Next
                            </button>
                            @endif
                        </div>

                        <!-- Step 2 -->
                        <div class="form-step" id="step-2" @if($currentProjectTypeId === 3) style="display: block;" @else style="display: none;" @endif>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-clock icon-style"></i> Date <span
                                                class="text-danger">*</span></label>
                                        <input type="date" class="form-control timepicker" id="date" name="date"
                                            required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-hourglass-half icon-style"></i> Time <span
                                                class="text-danger">*</span></label>
                                        <input type="time" class="form-control" name="duration" id="duration" required>
                                    </div>

                                </div>
                            </div>

                            @if($currentProjectTypeId !== 3)
                            <div class="form-group">
                                <label><i class="fas fa-map-marker-alt icon-style"></i> Clinic Location <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="clinic_location"
                                    name="clinic_location">
                            </div>

                            <div class="form-group">
                                <label><i class="fas fa-pencil-alt icon-style"></i> Follow-up Update</label>
                                <textarea class="form-control" id="followup_update" name="followup_update" rows="4" style="border-radius:10px"></textarea>
                            </div>
                            @endif

                            <div id="editappointsuccessMessage" class="alert alert-success" style="display:none;"></div>
                            <div id="editappointerrorMessage" class="alert alert-danger" style="display:none;"></div>

                            @if($currentProjectTypeId !== 3)
                            <button type="button" class="btn btn-danger prev-btn">
                                Previous
                            </button>
                            @endif

                            <button type="submit" class="btn btn-primary">
                                Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>




    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Include Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <!-- Include jQuery Validation Plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>

    <!-- Add this in your HTML <head> -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

    <script>
        $('#appointment_type').select2({
            placeholder: "Select Appointment type",
            width: '100%'
        });
        $('#appointment_type').on('select2:open', function() {
            $('.select2-search__field').attr('placeholder', 'Search Appointment type');
        });

        $('#status').select2({
            placeholder: "Select Status",
            width: '100%'
        });
        $('#status').on('select2:open', function() {
            $('.select2-search__field').attr('placeholder', 'Search Status');
        });



        $(document).ready(function() {
            $('.medicine-select').select2({
                placeholder: "Select Medicine",
                allowClear: true
            });
        });

        $(document).ready(function() {
            // let token = sessionStorage.getItem('token');
            let appointmentId = "{{ $appointment_id }}";

            $(document).ready(function() {
                // Call the function only if appointmentId is available
                if (typeof appointmentId !== "undefined" && appointmentId) {
                    fetchAppointmentData(appointmentId)
                } else {
                    console.warn("appointment ID not found or undefined.");
                }
            });
            // Initialize form validation
            var form = $("#appointmentForm");
            // const today = new Date().toISOString().split("T")[0];
            // form.find("input[name='date']").attr("min", today);
            form.validate({
                // Validation rules for the entire form
                rules: {
                    patient_id: "required",
                    treatment_id: "required",
                    doctor_id: "required",
                    @if($currentProjectTypeId !== 3)
                    appoint_type: "required", // Fixed name to appoint_type since select name is appoint_type
                    status: "required",
                    @endif
                    date: "required",
                    duration: "required",
                    @if($currentProjectTypeId !== 3)
                    clinic_location: "required"
                    @endif
                },
                messages: {
                    patient_id: "Please select a patient",
                    treatment_id: "Please select a treatment",
                    doctor_id: "Please select a doctor",
                    @if($currentProjectTypeId !== 3)
                    appoint_type: "Please select an appointment type",
                    status: "Please select a status",
                    @endif
                    date: "Please enter the date",
                    duration: "Please enter the duration",
                    @if($currentProjectTypeId !== 3)
                    clinic_location: "Please enter the clinic location"
                    @endif
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });

            // Function to validate the current step
            function validateStep(step) {
                var valid = true;
                step.find('input, select, textarea').each(function() {
                    if (!$(this).valid()) {
                        valid = false;
                    }
                });
                return valid;
            }

            // Next step button click event
            $('.next-btn').click(function() {
                var currentStep = $('#step-1');
                if (validateStep(currentStep)) {
                    currentStep.hide();
                    $('#step-2').show();
                }
            });

            // Previous step button click event
            $('.prev-btn').click(function() {
                var currentStep = $('#step-2');
                currentStep.hide();
                $('#step-1').show();
            });







            // Fetch Patients
            // $.ajax({
            //     url: "/api/patientss",
            //     type: "GET",
            //     dataType: "json",
            //     success: function (response) {
            //         let patientDropdown = $('.patientDropdown');

            //         if (patientDropdown.length === 0) {
            //             console.error("Dropdown not found! Check your HTML.");
            //             return;
            //         }

            //         patientDropdown.empty().append('<option value="">Select Patient</option>');

            //         if (!response.patients || response.patients.length === 0) {
            //             console.warn("No patients found for this user.");
            //             return;
            //         }

            //         $.each(response.patients, function(index, patient) {
            //             // Capitalize first letter only
            //             const capitalizedName = patient.fullname.charAt(0).toUpperCase() +
            //                 patient.fullname.slice(1);

            //             patientDropdown.append(
            //                 `<option value="${patient.id}" data-treatment-id="${patient.treatment_id}">${capitalizedName}</option>`
            //             );
            //         });

            //         // Initialize Select2 plugin
            //         patientDropdown.select2({
            //             placeholder: "Select Patient",
            //             allowClear: true,
            //             width: '100%'
            //         });

            //         // Set search input placeholder after opening dropdown
            //         patientDropdown.on('select2:open', function () {
            //             $('.select2-search__field').attr('placeholder', 'Search Patient');
            //         });
            //     },
            //     error: function (xhr) {
            //         console.error("API Error:", xhr.status, xhr.responseText);
            //     }
            // });





            let branchId = localStorage.getItem('selectedBranchId');


            $.ajax({
                url: "/api/patientss",
                type: "GET",
                data: {
                    branch_id: branchId
                },
                dataType: "json",
                xhrFields: {
                    withCredentials: true // ✅ send session cookie for Sanctum
                },
                success: function(response) {
                    let patientDropdown = $('#patientDropdown');

                    if (patientDropdown.length === 0) {
                        console.error("Dropdown not found! Check your HTML.");
                        return;
                    }

                    patientDropdown.empty().append('<option value="">Select Patient</option>');

                    if (!response.patients || response.patients.length === 0) {
                        console.warn("No patients found for this branch.");
                        return;
                    }

                    $.each(response.patients, function(index, patient) {
                        const capitalizedName = patient.fullname.charAt(0).toUpperCase() +
                            patient.fullname.slice(1);

                        patientDropdown.append(
                            `<option value="${patient.id}" data-treatment-id="${patient.treatment_id}">
                    ${capitalizedName}
                </option>`
                        );
                    });

                    patientDropdown.select2({
                        placeholder: "Select Patient",
                        allowClear: true,
                        width: '100%'
                    });

                    patientDropdown.on('select2:open', function() {
                        $('.select2-search__field').attr('placeholder', 'Search Patient');
                    });
                },
                error: function(xhr) {
                    console.error("API Error:", xhr.status, xhr.responseText);
                }
            });

            // Handle Patient Selection
            $('#patientDropdown').on("change", function() {
                let selectedPatient = $(this).find("option:selected");
                let treatmentId = selectedPatient.data("treatment-id"); // Get assigned treatment

                if (!treatmentId) {
                    console.warn("No treatment assigned for this patient.");
                    $('select[name="treatment_id"]').val("").trigger("change"); // Reset treatment dropdown
                    $('.doctorSelect').val("").trigger("change"); // Reset doctor dropdown
                    return;
                }

                // Automatically select treatment
                $('select[name="treatment_id"]').val(treatmentId).trigger("change");
            });

            // Fetch Treatments
            // $.ajax({
            //     url: "/api/treatments",
            //     type: "GET",
            //     success: function (data) {
            //         let treatmentDropdown = $('select[name="treatment_id"]');

            //         if (treatmentDropdown.length === 0) {
            //             console.log("Dropdown not found! Check your HTML.");
            //             return;
            //         }

            //         treatmentDropdown.empty();
            //         treatmentDropdown.append('<option value="">Select Treatment</option>');

            //         let treatmentsList = data.treatments || [];

            //         $.each(treatmentsList, function(key, treatment) {
            //             // Capitalize first letter only
            //             const capitalizedName = treatment.name.charAt(0).toUpperCase() +
            //                 treatment.name.slice(1);

            //             treatmentDropdown.append(
            //                 `<option value="${treatment.id}" data-doctor-id="${treatment.doctor_id}">${capitalizedName}</option>`
            //             );
            //         });

            //         // Initialize Select2 plugin
            //         treatmentDropdown.select2({
            //             placeholder: "Select Treatment",
            //             allowClear: true,
            //             width: '100%'
            //         });

            //         // Set search input placeholder after opening dropdown
            //         treatmentDropdown.on('select2:open', function () {
            //             $('.select2-search__field').attr('placeholder', 'Search Treatment');
            //         });
            //     },
            //     error: function (xhr) {
            //         console.log("API Error:", xhr.status, xhr.responseText);
            //     }
            // });


            $.ajax({
                url: "/api/treatments",
                type: "GET",
                data: {
                    branch_id: branchId
                },
                xhrFields: {
                    withCredentials: true // send session cookie for Sanctum
                },
                success: function(data) {
                    let treatmentDropdown = $('select[name="treatment_id"]');

                    if (!treatmentDropdown.length) return;

                    treatmentDropdown.empty().append('<option value="">Select Treatment</option>');

                    let treatmentsList = data.treatments || [];

                    $.each(treatmentsList, function(key, treatment) {
                        const name = treatment.name.charAt(0).toUpperCase() + treatment.name
                            .slice(1);
                        treatmentDropdown.append(
                            `<option value="${treatment.id}" data-doctor-id="${treatment.doctor_id}">${name}</option>`
                        );
                    });

                    treatmentDropdown.select2({
                        placeholder: "Select Treatment",
                        allowClear: true,
                        width: '100%'
                    });

                    treatmentDropdown.on('select2:open', function() {
                        $('.select2-search__field').attr('placeholder', 'Search Treatment');
                    });
                },
                error: function(xhr) {
                    console.log("API Error:", xhr.status, xhr.responseText);
                }
            });

            // Handle Treatment Selection
            $('select[name="treatment_id"]').on("change", function() {
                let selectedTreatment = $(this).find("option:selected");
                let doctorId = selectedTreatment.data("doctor-id");

                if (!doctorId) {
                    console.warn("No doctor assigned for this treatment.");
                    $('.doctorSelect').val("").trigger("change"); // Reset doctor dropdown
                    return;
                }

                // Automatically select the doctor
                $('.doctorSelect').val(doctorId).trigger("change");
            });

            // Fetch Doctors
            // $.ajax({
            //     url: "/api/doctors",
            //     type: "GET",
            //     dataType: "json",
            //     headers: { "Authorization": "Bearer " + token },
            //     success: function (response) {
            //         let doctorDropdown = $('.doctorSelect');

            //         if (doctorDropdown.length === 0) {
            //             console.error("Dropdown not found! Check your HTML ID.");
            //             return;
            //         }

            //         doctorDropdown.empty().append('<option value="">Select Doctor</option>');

            //         let doctorsList = response.doctors || [];

            //         if (doctorsList.length === 0) {
            //             console.warn("No doctors found.");
            //             return;
            //         }

            //         $.each(doctorsList, function(index, doctor) {
            //             // Capitalize first letter only
            //             const capitalizedName = doctor.fullname.charAt(0).toUpperCase() + doctor
            //                 .fullname.slice(1);

            //             doctorDropdown.append(
            //                 `<option value="${doctor.id}">${capitalizedName}</option>`
            //             );
            //         });

            //         // Initialize Select2 plugin
            //         doctorDropdown.select2({
            //             placeholder: "Select Doctor",
            //             allowClear: true,
            //             width: '100%'
            //         });

            //         // Set search input placeholder after opening dropdown
            //         doctorDropdown.on('select2:open', function () {
            //             $('.select2-search__field').attr('placeholder', 'Search Doctor');
            //         });
            //     },
            //     error: function (xhr) {
            //         console.error("API Error:", xhr.status, xhr.responseText);
            //     }
            // });


            // $.ajax({
            //     url: "/api/doctors",
            //     type: "GET",
            //     data: {
            //         branch_id: branchId
            //     }, // branch filter
            //     dataType: "json",
            //     headers: {
            //         "Authorization": "Bearer " + token
            //     },
            //     success: function(response) {
            //         let doctorDropdown = $('.doctorSelect');
            //         doctorDropdown.empty().append('<option value="">Select Doctor</option>');

            //         let doctorsList = response.doctors || [];
            //         $.each(doctorsList, function(index, doctor) {
            //             const capitalizedName = doctor.fullname.charAt(0).toUpperCase() + doctor
            //                 .fullname.slice(1);
            //             doctorDropdown.append(
            //                 `<option value="${doctor.id}">${capitalizedName}</option>`);
            //         });

            //         if (!doctorDropdown.hasClass('select2-hidden-accessible')) {
            //             doctorDropdown.select2({
            //                 placeholder: "Select Doctor",
            //                 allowClear: true,
            //                 width: '100%'
            //             });
            //         }

            //         doctorDropdown.on('select2:open', function() {
            //             $('.select2-search__field').attr('placeholder', 'Search Doctor');
            //         });
            //     },
            //     error: function(xhr) {
            //         console.error("API Error:", xhr.status, xhr.responseText);
            //     }
            // });


            let token = localStorage.getItem("authToken"); // assuming you saved JWT in localStorage

            $.ajax({
                url: "/api/doctors",
                type: "GET",
                data: {
                    branch_id: branchId // ✅ send branch_id to API
                },
                dataType: "json",
                headers: {
                    "Authorization": "Bearer " + token
                },
                success: function(response) {
                    let doctorDropdown = $('.doctorSelect');
                    doctorDropdown.empty().append('<option value="">Select Doctor</option>');

                    let doctorsList = response.doctors || [];
                    $.each(doctorsList, function(index, doctor) {
                        const capitalizedName = doctor.fullname.charAt(0).toUpperCase() + doctor
                            .fullname.slice(1);
                        doctorDropdown.append(
                            `<option value="${doctor.id}">${capitalizedName}</option>`
                        );
                    });

                    if (!doctorDropdown.hasClass('select2-hidden-accessible')) {
                        doctorDropdown.select2({
                            placeholder: "Select Doctor",
                            allowClear: true,
                            width: '100%'
                        });
                    }

                    doctorDropdown.on('select2:open', function() {
                        $('.select2-search__field').attr('placeholder', 'Search Doctor');
                    });
                },
                error: function(xhr) {
                    console.error("API Error:", xhr.status, xhr.responseText);
                }
            });


            function fetchAppointmentData(appointmentId) {
                $.ajax({
                    url: "{{ url('/api/appointments') }}/" + appointmentId,
                    type: "GET",
                    headers: {
                        "Authorization": "Bearer " + token
                    },
                    success: function(response) {
                        if (response.success) {
                            let appointment = response.data;

                            // Set input values
                            $('#appointment_id').val(appointment.id);
                            $('#appointment_type').val(appointment.appoint_type).trigger(
                                'change'); // Make sure it's an input/select
                            $('#status').val(appointment.status).trigger('change');
                            $('#date').val(appointment.date);
                            $('#date').attr('min', appointment.date);



                            function formatTimeTo24Hour(time) {
                                if (!time || time === "N/A") return "";

                                let [hours, minutes] = time.split(":");
                                hours = parseInt(hours, 10);
                                hours = hours < 10 ? `0${hours}` : hours;

                                return `${hours}:${minutes}`;
                            }


                            let originalDuration = appointment.duration || "";
                            let formattedDuration = originalDuration ? originalDuration.substring(0, 5) : "";

                            console.log("Original Duration:", originalDuration);
                            console.log("Final Formatted Duration:", formattedDuration);

                            $('#duration').val(formattedDuration);

                            $('#clinic_location').val(appointment.clinic_location);
                            $('#followup_update').val(appointment.followup_update);

                           

                            // **Set Patient Dropdown**
                            if (appointment.patient) {
                                let patientDropdown = $('#patientDropdown');
                                if (patientDropdown.length) {
                                    patientDropdown.val(appointment.patient.id).trigger("change");
                                }
                            }

                            // **Set Doctor Dropdown**
                            if (appointment.doctor) {
                                let doctorDropdown = $('select[name="doctor_id"]');
                                if (doctorDropdown.length) {
                                    doctorDropdown.val(appointment.doctor.id).trigger("change");
                                }
                            }

                            // **Set Treatment Dropdown**
                            if (appointment.treatment) {
                                let treatmentDropdown = $('select[name="treatment_id"]');
                                if (treatmentDropdown.length) {
                                    treatmentDropdown.val(appointment.treatment.id).trigger("change");
                                }
                            }

                            // **Fix Appointment Type Dropdown (if applicable)**
                            if (appointment.appointment_type) {
                                let appointmentTypeDropdown = $('select[name="appointment_type"]');
                                appointmentTypeDropdown.find("option:not(:first)").remove();
                                appointmentTypeDropdown.append(
                                    `<option value="${appointment.appoint_type}" selected>${appointment.appoint_type}</option>`
                                );
                            }

                        } else {
                            console.warn("No appointment data found.");
                        }
                    },
                    error: function(error) {
                        console.error("Error fetching appointment data:", error);
                    }
                });
            }

            form.on('submit', function(e) {
                e.preventDefault();
                if (form.valid()) {
                    let formData = new FormData(this);
                    formData.append('_method', 'PUT');

                    $.ajax({
                        url: "{{ url('/api/appointments') }}/" + appointmentId,
                        type: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        headers: {
                            "Authorization": "Bearer " + token
                        },
                        success: function(response) {
                            $('#editappointsuccessMessage').text(response.message ||
                                'Appointment updated successfully').show();
                            setTimeout(function() {
                                window.location.href =
                                    "{{ route('appointment.index') }}";
                            }, 1500);
                        },
                        error: function(xhr) {
                            var errorMessage = '';
                            if (xhr.status === 422) { // Validation error
                                var errors = xhr.responseJSON.errors;
                                $.each(errors, function(key, messages) {
                                    errorMessage += messages[0] + '<br>';
                                });
                            }
                            $('#editappointerrorMessage').html(errorMessage).show();
                        }
                    });
                }
            });

        });

        $(document).ready(function() {
            let token = sessionStorage.getItem('token'); // Ensure token is available

            function fetchMedicines(dropdownSelector) {
                $.ajax({
                    url: "/api/medicine",
                    type: "GET",
                    dataType: "json",
                    headers: {
                        "Authorization": "Bearer " + token
                    },
                    success: function(response) {
                        let medicineDropdown = $(dropdownSelector);
                        if (medicineDropdown.length === 0) {
                            console.error("Dropdown not found! Check your HTML.");
                            return;
                        }

                        medicineDropdown.empty().append('<option value="">Select Medicine</option>');

                        if (!response.medicines || response.medicines.length === 0) {
                            console.warn("No Medicine found for this user.");
                            return;
                        }

                        $.each(response.medicines, function(index, medicine) {
                            medicineDropdown.append(
                                `<option value="${medicine.id}">${medicine.name}</option>`
                            );
                        });

                        // Apply Select2 after data is loaded
                        medicineDropdown.select2({
                            placeholder: "Select Medicines",
                            allowClear: true
                        });
                    },
                    error: function(xhr) {
                        console.error("API Error:", xhr.status, xhr.responseText);
                    }
                });
            }

            // Reference to the status dropdown
            let statusDropdown = $('select[name="status"]');

            // Container where the additional fields will be added
            let extraFieldsContainer = $('<div id="extraFields"></div>');
            statusDropdown.closest('.form-group').after(extraFieldsContainer);

            // Listen for changes in the status dropdown
            statusDropdown.on('change', function() {
                if ($(this).val() === 'completed') {
                    let extraFields = `
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <div class="form-group">
                                                            <label><i class="fas fa-pills icon-style"></i> Medicines <span class="text-danger">*</span></label>
                                                            <select class="form-control medicine-select" name="medicines[]" multiple >
                                                                <option value="">Select Medicine</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6">
                                                        <div class="form-group">
                                                            <label><i class="fas fa-clipboard-list icon-style"></i> Note</label>
                                                             <input type="text" class="form-control " name="note" >
                                                        </div>
                                                    </div>
                                                </div>`;

                    // Add fields dynamically
                    $('#extraFields').html(extraFields);

                    // Fetch medicines **after** the dropdown is added
                    fetchMedicines('.medicine-select');
                } else {
                    $('#extraFields').empty(); // Remove fields if status is not "Completed"
                }
            });

        });
    </script>
@endsection
