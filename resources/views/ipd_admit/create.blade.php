@extends('layout.app')

<style>
    .discharge-title {

        padding-left: 232px !important;
    }

    .discharge-btn {
        padding-right: 173px !important;
        text-align: center;
    }
    .form-container {
        width: 60% !important;
        padding-bottom: 60px !important;
    }


   

    @media screen and (max-width: 767px) {
        .page-title {
            font-size: 19px !important;
            padding-left: 10px !important;
            text-align: left !important;
        }

    }
</style>

@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="row" style="padding-top:15px">
                <div class="col-8">
                    <h4 class="page-title discharge-title" style="">Add IPD Details</h4>
                </div>
                @if (app('hasPermission')(25, 'view'))
                    <div class="col-4  m-b-2 view-discharge discharge-btn">
                        <a href="{{ route('ipd_admit.index') }}" class="btn btn-primary btn-rounded btn-hdr ">
                            <i class="fa fa-arrow-left m-r-5 icon3"></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    </div>
                @endif
            </div>
            <div class="row">
                <div class="col-12">
                    <form class="form-container" id="ipdform" method="POST" action="">
                        @csrf
                        <input type="hidden" name="branch_id" id="branch_id">
                        <!-- Step 1 -->
                        <div class="form-step discharge-form1" id="step-1">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <div class="d-flex align-items-center justify-content-between">


                                            <label class="mr-2"><i class="fas fa-medkit icon-style"></i> Patient <span
                                                    class="text-danger">*</span></label>

                                            @if (app('hasPermission')(5, 'create'))
                                                <a href="{{ route('patients.create') }}" target="_blank"
                                                    class="btn btn-primary btn-sm">
                                                    <i class="fas fa-plus"></i> Add Patient
                                                </a>
                                            @endif

                                        </div>
                                        <select class="form-control select2" name="patient_id" id="patientDropdown"
                                            required>
                                            <option value="">Select Patient</option>
                                        </select>

                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <label class="mr-2"><i class="fas fa-medkit icon-style"></i> Treatment <span
                                                    class="text-danger">*</span></label>
                                            @if (app('hasPermission')(7, 'create'))
                                                <a href="{{ route('treatment.create') }}" target="_blank"
                                                    class="btn btn-primary btn-sm">
                                                    <i class="fas fa-plus"></i> Add Treatment
                                                </a>
                                            @endif

                                        </div>
                                        <select class="form-control select2" name="treatment_id" id="treatmentDropdown"
                                            required>
                                            <option value="">Select Treatment</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <label class="mr-2"><i class="fas fa-medkit icon-style"></i> Doctor <span
                                                    class="text-danger">*</span></label>
                                            @if (app('hasPermission')(3, 'create'))
                                                <a href="{{ route('user.create') }}" target="_blank"
                                                    class="btn btn-primary btn-sm">
                                                    <i class="fas fa-plus"></i> Add Doctor
                                                </a>
                                            @endif

                                        </div>
                                        <select class="form-control select2 doctorSelect" name="doctor_id" id="doctorSelect"
                                            required>
                                            <option value="">Select Doctor</option>
                                        </select>

                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label><i class="fas fa-clock icon-style"></i> Admit Date <span
                                                class="text-danger">*</span></label>
                                        <input type="date" class="form-control timepicker" name="admission_date"
                                            id="admission_date" max="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>
                            </div>




                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-hotel icon-style"></i> Room Number <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="room_number" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-bed icon-style"></i> Bed Number <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="bed_number" required>
                                    </div>
                                </div>
                            </div>


                            <div id="ipdsuccessMessage" class="alert alert-success" style="display:none;"></div>
                            <div id="ipderrorMessage" class="alert alert-danger" style="display:none;"></div>



                            <button type="submit" class="btn btn-primary d-block m-auto"
                                style="padding:8px 50px;border-radius:50px; ">
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
        $('#type').select2({
            placeholder: "Select Type",
            width: '100%'
        });
        $('#type').on('select2:open', function() {
            $('.select2-search__field').attr('placeholder', 'Search Type');
        });

        $.validator.addMethod("dateCheck", function(value, element) {
            var admit = $("input[name='admit_date']").val();
            if (!admit) return true; // Skip if admit_date is empty, let 'required' handle it
            var admitDate = new Date(admit);
            var dischargeDate = new Date(value);
            return dischargeDate >= admitDate;
        }, "Discharge Date must be greater than or equal to Admit Date");


        $(document).ready(function() {
            // Initialize form validation
            var form = $("#ipdform");
            form.validate({
                rules: {
                    patient_id: "required",
                    treatment_id: "required",
                    doctor_id: "required",
                    admission_date: "required",

                    room_number: "required",
                    bed_number: "required"
                },
                messages: {
                    patient_id: "Please select a patient",
                    treatment_id: "Please select a treatment",
                    doctor_id: "Please select a doctor",
                    admit_date: "Please enter the admit date",

                    room_number: "Please enter the room number",
                    bed_number: "Please enter the bed number"
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

         

            $.ajax({
                url: "/api/doctors",
                type: "GET",
                data: {
                    branch_id: branchId
                }, // branch filter
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
                            `<option value="${doctor.id}">${capitalizedName}</option>`);
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


            document.addEventListener('DOMContentLoaded', function() {
                let storedBranchId = localStorage.getItem('selectedBranchId');
                if (storedBranchId) {
                    document.getElementById('branch_id').value = storedBranchId;
                }
            });
            // Handle form submission
            form.on('submit', function(e) {
                e.preventDefault();
                if (form.valid()) {
                    let formData = new FormData(this);

                    $.ajax({
                        url: "{{ url('/api/ipd-admissions') }}", // ✅ Corrected endpoint
                        type: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        headers: {
                            "Authorization": "Bearer " + token
                        },
                        success: function(response) {
                            $('#ipdsuccessMessage').text(response.message ||
                                'IPD Admission created successfully').show();
                            $('#ipdform')[0].reset();

                            setTimeout(function() {
                                window.location.href = "{{ route('ipd_admit.index') }}";
                            }, 1500);
                        },
                        error: function(xhr) {
                            var errorMessage = '';
                            if (xhr.status === 422) {
                                var errors = xhr.responseJSON.errors;
                                $.each(errors, function(key, messages) {
                                    errorMessage += messages[0] + '<br>';
                                });
                            } else {
                                errorMessage = "Something went wrong.";
                            }
                            $('#ipderrorMessage').html(errorMessage).show();
                        }
                    });
                }
            });

        });
    </script>
@endsection
