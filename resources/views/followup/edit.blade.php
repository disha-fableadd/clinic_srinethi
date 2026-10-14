@extends('layout.app')

<style>
    .followup-title {
        padding-left: 130px !important;
        text-align: center !important;
    }

    .followup-button {
        padding-right: 75px !important;
        text-align: center !important;
    }
    .form-container {
        width: 60% !important;
        padding-bottom: 60px !important;
    }



    @media screen and (max-width:767px) {
        .followup-title {
            padding-left: 15px !important;
            text-align: left !important;
        }

        .followup-button {
            padding-right: 15px !important;
            text-align: right !important;
        }

        .page-title {
            font-size: 20px;
        }

    }
</style>


@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="row" style="padding-top:15px">
                <div class="col-6">
                    <h4 class="page-title followup-title ">Edit Followup</h4>
                </div>
                @if (app('hasPermission')(2, 'view'))
                    <div class="col-6 followup-button  m-b-2 eye-btn">
                        <a href="{{ route('followup.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                            <i class="fa fa-arrow-left m-r-5 icon3"></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    </div>
                @endif
            </div>
            <div class="row">
                <div class="col-12">
                    <form class="form-container follow-form all-form" id="followupForm" method="POST" action="">
                        @csrf
                        <!-- Step 1 -->
                        <input type="hidden" id="followup_id" name="followup_id">
                        @php
                            $currentProjectTypeId = (int) \App\Models\Setting::getValue('project_type_id', 1);
                        @endphp

                        @if($currentProjectTypeId !== 3)
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group followup-dropdown">
                                    <label><i class="fas fa-user-md icon-style"></i> Followup Type</label>
                                    <select class="form-control dropdown select2" id="select-followup" name="followup_type">
                                        <option value="" disabled selected>Select Type</option>
                                        <option value="Regular followup">Regular Followup</option>
                                        <option value="Appointment related">Appointment related</option>
                                        <option value="Medicine related">Medicine related</option>
                                        <option value="Discharge related">Discharge related</option>
                                        <option value="Report related">Report related</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="row">

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label><i class="fas fa-user-injured icon-style"></i> Patient Name <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control select2 patientDropdown" id="patient_id" name="patient_id"
                                        required>
                                        <option value="">Select Patient</option>
                                    </select>

                                </div>
                            </div>
                        </div>

                        <div class="row">
                            @if($currentProjectTypeId !== 3)
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
                            @endif
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label><i class="fas fa-user-md icon-style"></i> Doctor <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control select2 doctorSelect" id="doctorSelect" name="doctor_id"
                                        id="doctorSelect" required>
                                        <option value="">Select Doctor</option>
                                    </select>

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label><i class="fas fa-clock icon-style"></i> Date <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control timepicker" id="date" name="date"
                                        required>
                                </div>
                            </div>

                        </div>

                        @if($currentProjectTypeId !== 3)
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label><i class="fas fa-pencil-alt icon-style"></i> Followup Comments</label>
                                    <textarea class="form-control" id="followup_update" name="followup_update" rows="4" style="border-radius:10px"></textarea>
                                </div>
                            </div>
                        </div>
                        @endif




                        <div id="editfollowupsuccessMessage" class="alert alert-success" style="display:none;"></div>
                        <div id="edit followuperrorMessage" class="alert alert-danger" style="display:none;"></div>

                        <button type="submit" class="btn btn-primary"
                            style="padding:8px 50px;border-radius:50px; float:right">
                            Submit
                        </button>

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
        $('#select-followup').select2({
            placeholder: "Select Type",
            width: '100%'
        });
        $('#select-followup').on('select2:open', function() {
            $('.select2-search__field').attr('placeholder', 'Search Type');
        });

        $(document).ready(function() {
            // Initialize form validation
            var form = $("#followupForm");
            const today = new Date().toISOString().split("T")[0];
            form.find("input[name='date']").attr("min", today);
            form.validate({
                // Validation rules for the entire form
                rules: {
                    patient_id: "required",
                    treatment_id: "required",
                    doctor_id: "required",

                    date: "required",

                },
                messages: {
                    patient_id: "Please select a patient",
                    treatment_id: "Please select a treatment",
                    doctor_id: "Please select a doctor",

                    date: "Please enter the date",

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


            let branchId = localStorage.getItem("selectedBranchId");



            $.ajax({
                url: "/api/patientss",
                type: "GET",
                data: {
                    branch_id: localStorage.getItem("selectedBranchId") // ✅ pass branch_id
                },
                dataType: "json",
                success: function(response) {
                    let patientDropdown = $('.patientDropdown');

                    if (patientDropdown.length === 0) {
                        console.error("Dropdown not found! Check your HTML.");
                        return;
                    }

                    patientDropdown.empty().append('<option value="">Select Employee</option>');

                    let patientsList = response.patients || [];

                    if (patientsList.length === 0) {
                        console.warn("No employees found for this branch.");
                        return;
                    }

                    $.each(patientsList, function(index, patient) {
                        const capitalizedName = patient.fullname.charAt(0).toUpperCase() +
                            patient.fullname.slice(1);

                        patientDropdown.append(
                            `<option value="${patient.id}" data-treatment-id="${patient.treatment_id}">
                    ${capitalizedName}
                </option>`
                        );
                    });

                    // ✅ Initialize Select2
                    patientDropdown.select2({
                        placeholder: "Select Employee",
                        allowClear: true,
                        width: '100%'
                    });

                    // ✅ Search placeholder
                    patientDropdown.on('select2:open', function() {
                        $('.select2-search__field').attr('placeholder', 'Search Employee');
                    });
                },
                error: function(xhr) {
                    console.error("API Error:", xhr.status, xhr.responseText);
                }
            });


            // Handle Patient Selection
            $('.patientDropdown').on("change", function() {
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

          
            $.ajax({
                url: "/api/treatments",
                type: "GET",
                data: {
                    branch_id: branchId // ✅ pass branch_id to API
                },
                dataType: "json",
                success: function(data) {
                    let treatmentDropdown = $('select[name="treatment_id"]');

                    if (treatmentDropdown.length === 0) {
                        console.log("Dropdown not found! Check your HTML.");
                        return;
                    }

                    treatmentDropdown.empty();
                    treatmentDropdown.append('<option value="">Select Treatment</option>');

                    let treatmentsList = data.treatments || [];

                    if (treatmentsList.length === 0) {
                        console.warn("No treatments found for this branch.");
                        return;
                    }

                    $.each(treatmentsList, function(key, treatment) {
                        const capitalizedName = treatment.name.charAt(0).toUpperCase() +
                            treatment.name.slice(1);

                        treatmentDropdown.append(
                            `<option value="${treatment.id}" data-doctor-id="${treatment.doctor_id}">${capitalizedName}</option>`
                        );
                    });

                    // Initialize Select2 plugin
                    treatmentDropdown.select2({
                        placeholder: "Select Treatment",
                        allowClear: true,
                        width: '100%'
                    });

                    // Set search input placeholder after opening dropdown
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

                    if (doctorDropdown.length === 0) {
                        console.error("Dropdown not found! Check your HTML class.");
                        return;
                    }

                    doctorDropdown.empty().append('<option value="">Select Doctor</option>');

                    let doctorsList = response.doctors || [];

                    if (doctorsList.length === 0) {
                        console.warn("No doctors found for this branch.");
                        return;
                    }

                    $.each(doctorsList, function(index, doctor) {
                        const capitalizedName = doctor.fullname.charAt(0).toUpperCase() + doctor
                            .fullname.slice(1);

                        doctorDropdown.append(
                            `<option value="${doctor.id}">${capitalizedName}</option>`
                        );
                    });

                    doctorDropdown.select2({
                        placeholder: "Select Doctor",
                        allowClear: true,
                        width: '100%'
                    });

                    doctorDropdown.on('select2:open', function() {
                        $('.select2-search__field').attr('placeholder', 'Search Doctor');
                    });
                },
                error: function(xhr) {
                    console.error("API Error:", xhr.status, xhr.responseText);
                }
            });



            $(document).ready(function() {
                if (followupId && token) {
                    fetchFollowupData(followupId);
                }
            });

            let followupId = "{{ $followup_id }}";


            function fetchFollowupData(followupId) {
                $.ajax({
                    url: "/api/followup/" + followupId,
                    type: "GET",
                    headers: {
                        "Authorization": "Bearer " + token
                    },
                    success: function(response) {
                        console.log("Followup Response:", response);

                        if (response && response.data) {
                            let followup = response.data;

                            // Set input values
                            $('#followup_id').val(followup.id);
                            $('#date').val(followup.date);
                            $('#followup_update').val(followup.followup_update);

                            if (followup.followup_type) {
                                $('select[name="followup_type"]').val(followup.followup_type).trigger(
                                    'change');
                            }
                            // **Set Patient Dropdown**
                            if (followup.patient) {
                                let patientDropdown = $('.patientDropdown');
                                if (patientDropdown.length) {
                                    patientDropdown.val(followup.patient.id).trigger("change");
                                }
                            }

                            // **Set Doctor Dropdown**
                            if (followup.doctor) {
                                let doctorDropdown = $('select[name="doctor_id"]');
                                if (doctorDropdown.length) {
                                    doctorDropdown.val(followup.doctor.id).trigger("change");
                                }
                            }

                            // **Set Treatment Dropdown**
                            if (followup.treatment) {
                                let treatmentDropdown = $('select[name="treatment_id"]');
                                if (treatmentDropdown.length) {
                                    treatmentDropdown.val(followup.treatment.id).trigger("change");
                                }
                            }

                        } else {
                            console.warn("No followup data found.");
                        }
                    },
                    error: function(error) {
                        console.error("Error fetching followup data:", error);
                    }
                });
            }


            // Handle form submission
            form.on('submit', function(e) {
                e.preventDefault();
                if (form.valid()) {
                    let formData = new FormData(this);
                    formData.append('_method', 'PUT');

                    $.ajax({
                        url: "/api/followup/" + followupId,
                        type: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        headers: {
                            "Authorization": "Bearer " + token
                        },
                        success: function(response) {
                            $('#editfollowupsuccessMessage').text(response.message ||
                                'Followup updated successfully').show();
                            setTimeout(function() {
                                window.location.href = "{{ route('followup.index') }}";
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
                            $('#editfollowuperrorMessage').html(errorMessage).show();
                        }
                    });
                }
            });

        });
    </script>
@endsection
