@extends('layout.app')
<link rel="stylesheet" href="{{ asset(env('IMAGE_PATH').'admin/assets/css/appointment-create.css') }}">


@section('content')
    <div class="page-wrapper">
        <div class="content">

            <div class="row pt-3">
                <div class="col-6">
                    <h4 class="page-title appointment-title">Add Appointment</h4>
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
                        <input type="hidden" name="branch_id" id="branch_id">
                        <!-- STEP 1 -->
                        <div class="form-step" id="step-1">

                            <!-- Patient -->
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <label><i class="fas fa-medkit icon-style"></i> Patient <span
                                                    class="text-danger">*</span></label>

                                            @if (app('hasPermission')(5, 'create'))
                                                <button type="button" class="btn btn-primary btn-sm"
                                                    data-bs-toggle="modal" data-bs-target="#quickAddPatientModal">
                                                    <i class="fas fa-plus"></i> Add 
                                                </button>
                                            @endif
                                        </div>

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
                                        <div class="d-flex align-items-center justify-content-between">
                                            <label><i class="fas fa-medkit icon-style"></i> Treatment <span
                                                    class="text-danger">*</span></label>

                                            @if (app('hasPermission')(7, 'create'))
                                                <button type="button" class="btn btn-primary btn-sm"
                                                    data-bs-toggle="modal" data-bs-target="#quickAddTreatmentModal">
                                                    <i class="fas fa-plus"></i> Add 
                                                </button>
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
                                            <label><i class="fas fa-medkit icon-style"></i> Doctor <span
                                                    class="text-danger">*</span></label>

                                            @if (app('hasPermission')(3, 'create'))
                                                <button type="button" class="btn btn-primary btn-sm"
                                                    data-bs-toggle="modal" data-bs-target="#quickAddDoctorModal">
                                                    <i class="fas fa-plus"></i> Add 
                                                </button>
                                            @endif
                                        </div>

                                        <select class="form-control select2 doctorSelect" name="doctor_id" id="doctorSelect"
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
                                        <select class="form-control select2" id="select-type" name="appoint_type" required>
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
                                        <select class="form-control select2" id="select-status" name="status" required>
                                            <option value="">Select</option>
                                            <option value="upcoming">Upcoming</option>
                                            <option value="confirmed">Confirmed</option>
                                            <option value="follow-up">Follow-Up</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($currentProjectTypeId !== 3)
                            <button type="button" class="btn btn-primary next-btn">Next</button>
                            @endif
                        </div>

                        <!-- STEP 2 -->
                        <div class="form-step" id="step-2" @if($currentProjectTypeId === 3) style="display: block;" @else style="display: none;" @endif>

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-clock icon-style"></i> Date <span
                                                class="text-danger">*</span></label>
                                        <input type="date" class="form-control timepicker" name="date" required>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-hourglass-half icon-style"></i> Time <span
                                                class="text-danger">*</span></label>
                                        <input type="time" class="form-control" name="duration" required>
                                    </div>
                                </div>
                            </div>

                            @if($currentProjectTypeId !== 3)
                            <div class="form-group">
                                <label><i class="fas fa-map-marker-alt icon-style"></i> Clinic Location <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="clinic_location">
                            </div>

                            <div class="form-group">
                                <label><i class="fas fa-pencil-alt icon-style"></i> Follow-up Update</label>
                                <textarea class="form-control" name="followup_update" rows="4" style="border-radius:10px"></textarea>
                            </div>
                            @endif

                            <div id="appointsuccessMessage" class="alert alert-success" style="display:none;"></div>
                            <div id="appointerrorMessage" class="alert alert-danger" style="display:none;"></div>

                            @if($currentProjectTypeId !== 3)
                            <button type="button" class="btn btn-danger prev-btn">Previous</button>
                            @endif
                            <button type="submit" class="btn btn-primary submit-btn">Submit</button>

                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>

    <!-- Quick Add Patient Modal -->
    <div class="modal fade" id="quickAddPatientModal" tabindex="-1" aria-labelledby="quickAddPatientModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px; overflow: hidden;">
                <div class="modal-header" style="background-color: #CFECE0; color: #000;">
                    <h5 class="modal-title font-weight-bold" id="quickAddPatientModalLabel">
                        <i class="fas fa-user-plus mr-2"></i> Add Patient
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="quickAddPatientForm">
                    <div class="modal-body p-4">
                        <div id="quickPatientAlert" class="alert alert-danger" style="display: none;"></div>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold"><i class="fas fa-user icon-style"></i> Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="fullname" placeholder="Enter Full Name" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold"><i class="fas fa-phone icon-style"></i> Phone <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="phone" placeholder="Enter 10 Digit Phone" maxlength="10" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold"><i class="fas fa-venus-mars icon-style"></i> Gender</label>
                                    <select class="form-control" name="gender">
                                        <option value="">Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold"><i class="fas fa-calendar-alt icon-style"></i> Age</label>
                                    <input type="number" class="form-control" name="age" placeholder="Age" min="0" max="150">
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-2">
                            <label class="font-weight-bold"><i class="fas fa-envelope icon-style"></i> Email</label>
                            <input type="email" class="form-control" name="email" placeholder="Enter Email (Optional)">
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4" id="btnSaveQuickPatient">
                            <span class="spinner-border spinner-border-sm me-1" style="display:none;"></span> Save Patient
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Quick Add Treatment Modal -->
    <div class="modal fade" id="quickAddTreatmentModal" tabindex="-1" aria-labelledby="quickAddTreatmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px; overflow: hidden;">
                <div class="modal-header" style="background-color: #CFECE0; color: #000;">
                    <h5 class="modal-title font-weight-bold" id="quickAddTreatmentModalLabel">
                        <i class="fas fa-pills mr-2"></i> Add Treatment
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="quickAddTreatmentForm">
                    <div class="modal-body p-4">
                        <div id="quickTreatmentAlert" class="alert alert-danger" style="display: none;"></div>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold"><i class="fas fa-cogs icon-style"></i> Treatment Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" placeholder="Enter Treatment Name" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold"><i class="fas fa-money-bill-wave icon-style"></i> Price <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="price" placeholder="Enter Price" min="0" step="0.01" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold"><i class="fas fa-user-md icon-style"></i> Doctor</label>
                            <select class="form-control" name="doctor_id" id="quickTreatmentDoctorSelect">
                                <option value="">Select Doctor (Optional)</option>
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label class="font-weight-bold"><i class="fas fa-clipboard-list icon-style"></i> Description</label>
                            <textarea class="form-control" name="description" rows="2" placeholder="Brief description (Optional)" style="border-radius: 10px;"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4" id="btnSaveQuickTreatment">
                            <span class="spinner-border spinner-border-sm me-1" style="display:none;"></span> Save Treatment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Quick Add Doctor Modal -->
    <div class="modal fade" id="quickAddDoctorModal" tabindex="-1" aria-labelledby="quickAddDoctorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px; overflow: hidden;">
                <div class="modal-header" style="background-color: #CFECE0; color: #000;">
                    <h5 class="modal-title font-weight-bold" id="quickAddDoctorModalLabel">
                        <i class="fas fa-user-md mr-2"></i> Add Doctor
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="quickAddDoctorForm">
                    <div class="modal-body p-4">
                        <div id="quickDoctorAlert" class="alert alert-danger" style="display: none;"></div>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold"><i class="fas fa-user icon-style"></i> Doctor Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="fullname" placeholder="Enter Full Name" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold"><i class="fas fa-envelope icon-style"></i> Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" placeholder="Enter Email" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold"><i class="fas fa-phone icon-style"></i> Phone <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="phone" placeholder="Enter 10 Digit Phone" maxlength="10" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold"><i class="fas fa-lock icon-style"></i> Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="password" placeholder="Min 6 characters" minlength="6" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold"><i class="fas fa-venus-mars icon-style"></i> Gender</label>
                                    <select class="form-control" name="gender">
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4" id="btnSaveQuickDoctor">
                            <span class="spinner-border spinner-border-sm me-1" style="display:none;"></span> Save Doctor
                        </button>
                    </div>
                </form>
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


        $('#select-type').select2({
            placeholder: "Select Appointment type",
            width: '100%'
        });
        $('#select-type').on('select2:open', function() {
            $('.select2-search__field').attr('placeholder', 'Search Appointment type');
        });

        $('#select-status').select2({
            placeholder: "Select Status",
            width: '100%'
        });
        $('#select-status').on('select2:open', function() {
            $('.select2-search__field').attr('placeholder', 'Search Status');
        });

        $(document).ready(function() {
            // Initialize form validation
            var form = $("#appointmentForm");
            const today = new Date().toISOString().split("T")[0];
            form.find("input[name='date']").attr("min", today);
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

            let token = @json(session('access_token'));
            let branchId = localStorage.getItem('selectedBranchId');

            // Reusable loader for Patients
            function loadPatients(selectedId = null) {
                $.ajax({
                    url: "/api/patientss",
                    type: "GET",
                    data: { branch_id: branchId },
                    dataType: "json",
                    xhrFields: { withCredentials: true },
                    success: function(response) {
                        let patientDropdown = $('#patientDropdown');
                        if (patientDropdown.length === 0) return;

                        patientDropdown.empty().append('<option value="">Select Patient</option>');
                        if (!response.patients || response.patients.length === 0) {
                            return;
                        }

                        $.each(response.patients, function(index, patient) {
                            const capitalizedName = patient.fullname.charAt(0).toUpperCase() + patient.fullname.slice(1);
                            patientDropdown.append(
                                `<option value="${patient.id}" data-treatment-id="${patient.treatment_id || ''}">
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

                        if (selectedId) {
                            patientDropdown.val(selectedId).trigger('change');
                        }
                    },
                    error: function(xhr) {
                        console.error("API Error:", xhr.status, xhr.responseText);
                    }
                });
            }

            // Reusable loader for Treatments
            function loadTreatments(selectedId = null) {
                $.ajax({
                    url: "/api/treatments",
                    type: "GET",
                    data: { branch_id: branchId },
                    xhrFields: { withCredentials: true },
                    success: function(data) {
                        let treatmentDropdown = $('select[name="treatment_id"]');
                        if (!treatmentDropdown.length) return;

                        treatmentDropdown.empty().append('<option value="">Select Treatment</option>');
                        let treatmentsList = data.treatments || [];

                        $.each(treatmentsList, function(key, treatment) {
                            const name = treatment.name.charAt(0).toUpperCase() + treatment.name.slice(1);
                            treatmentDropdown.append(
                                `<option value="${treatment.id}" data-doctor-id="${treatment.doctor_id || ''}">${name}</option>`
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

                        if (selectedId) {
                            treatmentDropdown.val(selectedId).trigger('change');
                        }
                    },
                    error: function(xhr) {
                        console.log("API Error:", xhr.status, xhr.responseText);
                    }
                });
            }

            // Reusable loader for Doctors
            function loadDoctors(selectedId = null) {
                $.ajax({
                    url: "/api/doctors",
                    type: "GET",
                    data: { branch_id: branchId },
                    dataType: "json",
                    headers: { "Authorization": "Bearer " + token },
                    success: function(response) {
                        let doctorDropdown = $('.doctorSelect');
                        doctorDropdown.empty().append('<option value="">Select Doctor</option>');

                        let quickDoctorDropdown = $('#quickTreatmentDoctorSelect');
                        quickDoctorDropdown.empty().append('<option value="">Select Doctor (Optional)</option>');

                        let doctorsList = response.doctors || [];
                        $.each(doctorsList, function(index, doctor) {
                            const capitalizedName = doctor.fullname.charAt(0).toUpperCase() + doctor.fullname.slice(1);
                            doctorDropdown.append(`<option value="${doctor.id}">${capitalizedName}</option>`);
                            quickDoctorDropdown.append(`<option value="${doctor.id}">${capitalizedName}</option>`);
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

                        if (selectedId) {
                            doctorDropdown.val(selectedId).trigger('change');
                        }
                    },
                    error: function(xhr) {
                        console.error("API Error:", xhr.status, xhr.responseText);
                    }
                });
            }

            // Initial load of dropdowns
            loadPatients();
            loadTreatments();
            loadDoctors();

            // Handle Patient Selection
            $('#patientDropdown').on("change", function() {
                let selectedPatient = $(this).find("option:selected");
                let treatmentId = selectedPatient.data("treatment-id");

                if (!treatmentId) {
                    return;
                }
                $('select[name="treatment_id"]').val(treatmentId).trigger("change");
            });

            // Handle Treatment Selection
            $('select[name="treatment_id"]').on("change", function() {
                let selectedTreatment = $(this).find("option:selected");
                let doctorId = selectedTreatment.data("doctor-id");

                if (!doctorId) {
                    return;
                }
                $('.doctorSelect').val(doctorId).trigger("change");
            });

            // ---------------- Quick Add Form Submissions ---------------- //

            // 1. Quick Add Patient Submit
            $('#quickAddPatientForm').on('submit', function(e) {
                e.preventDefault();
                let $btn = $('#btnSaveQuickPatient');
                let $alert = $('#quickPatientAlert');
                $alert.hide().empty();

                let formData = new FormData(this);
                formData.append('branch_id', branchId);

                $btn.prop('disabled', true).find('.spinner-border').show();

                $.ajax({
                    url: "{{ url('api/patient') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: { "Authorization": "Bearer " + token },
                    success: function(response) {
                        $btn.prop('disabled', false).find('.spinner-border').hide();
                        $('#quickAddPatientForm')[0].reset();
                        let modalEl = document.getElementById('quickAddPatientModal');
                        let modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                        modal.hide();

                        // Reload patients and auto-select new patient
                        let newPatientId = response.patient?.id || response.data?.id;
                        loadPatients(newPatientId);
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).find('.spinner-border').hide();
                        let errorMsg = 'Failed to create patient.';
                        if (xhr.status === 422 || xhr.status === 401) {
                            let errors = xhr.responseJSON?.errors;
                            if (errors) {
                                errorMsg = '';
                                $.each(errors, function(k, v) {
                                    errorMsg += v[0] + '<br>';
                                });
                            } else if (xhr.responseJSON?.message) {
                                errorMsg = xhr.responseJSON.message;
                            }
                        }
                        $alert.html(errorMsg).show();
                    }
                });
            });

            // 2. Quick Add Treatment Submit
            $('#quickAddTreatmentForm').on('submit', function(e) {
                e.preventDefault();
                let $btn = $('#btnSaveQuickTreatment');
                let $alert = $('#quickTreatmentAlert');
                $alert.hide().empty();

                let formData = new FormData(this);
                formData.append('branch_id', branchId);
                formData.append('gst_option', 'Without GST');

                $btn.prop('disabled', true).find('.spinner-border').show();

                $.ajax({
                    url: "{{ url('api/treatments') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: { "Authorization": "Bearer " + token },
                    success: function(response) {
                        $btn.prop('disabled', false).find('.spinner-border').hide();
                        $('#quickAddTreatmentForm')[0].reset();
                        let modalEl = document.getElementById('quickAddTreatmentModal');
                        let modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                        modal.hide();

                        // Reload treatments and auto-select new treatment
                        let newTreatmentId = response.treatment?.id || response.data?.id || response.id;
                        loadTreatments(newTreatmentId);
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).find('.spinner-border').hide();
                        let errorMsg = 'Failed to create treatment.';
                        if (xhr.status === 422 || xhr.status === 401) {
                            let errors = xhr.responseJSON?.errors;
                            if (errors) {
                                errorMsg = '';
                                $.each(errors, function(k, v) {
                                    errorMsg += v[0] + '<br>';
                                });
                            } else if (xhr.responseJSON?.message) {
                                errorMsg = xhr.responseJSON.message;
                            }
                        }
                        $alert.html(errorMsg).show();
                    }
                });
            });

            // 3. Quick Add Doctor Submit
            $('#quickAddDoctorForm').on('submit', function(e) {
                e.preventDefault();
                let $btn = $('#btnSaveQuickDoctor');
                let $alert = $('#quickDoctorAlert');
                $alert.hide().empty();

                let formData = new FormData(this);
                formData.append('branch_id', branchId);
                formData.append('role_id', 3); // Doctor Role ID is 3
                formData.append('address', 'Clinic Address');
                formData.append('city', 'Surat');
                formData.append('state', 'Gujarat');

                // Default basic doctor permissions
                let defaultPermissions = [
                    { module_id: 1, view: true, create: true, update: true, delete: false },
                    { module_id: 6, view: true, create: true, update: true, delete: false },
                    { module_id: 7, view: true, create: true, update: true, delete: false }
                ];
                formData.append('permissions', JSON.stringify(defaultPermissions));

                $btn.prop('disabled', true).find('.spinner-border').show();

                $.ajax({
                    url: "{{ url('api/users') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: { "Authorization": "Bearer " + token },
                    success: function(response) {
                        $btn.prop('disabled', false).find('.spinner-border').hide();
                        if (response.status === false) {
                            $alert.html(response.message || 'Error creating doctor.').show();
                            return;
                        }
                        $('#quickAddDoctorForm')[0].reset();
                        let modalEl = document.getElementById('quickAddDoctorModal');
                        let modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                        modal.hide();

                        // Reload doctors and auto-select new doctor
                        let newDoctorId = response.user?.id || response.data?.id;
                        loadDoctors(newDoctorId);
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).find('.spinner-border').hide();
                        let errorMsg = 'Failed to create doctor.';
                        if (xhr.status === 422 || xhr.status === 401) {
                            let errors = xhr.responseJSON?.errors;
                            if (errors) {
                                errorMsg = '';
                                $.each(errors, function(k, v) {
                                    errorMsg += v[0] + '<br>';
                                });
                            } else if (xhr.responseJSON?.message) {
                                errorMsg = xhr.responseJSON.message;
                            }
                        } else if (xhr.responseJSON?.error) {
                            errorMsg = xhr.responseJSON.error;
                        }
                        $alert.html(errorMsg).show();
                    }
                });
            });



            // // Handle form submission
            form.on('submit', function(e) {
                // let userId = sessionStorage.getItem('user_id');


                e.preventDefault();
                if (form.valid()) {
                    let formData = new FormData(this);

                    $.ajax({
                        url: "{{ url('/api/appointments') }}",
                        type: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        headers: {
                            "Authorization": "Bearer " + token
                        },
                        success: function(response) {
                            $('#appointsuccessMessage').text(response.message ||
                                'Appointment created successfully').show();
                            $('#appointmentForm')[0].reset();

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
                            $('#appointerrorMessage').html(errorMessage).show();
                        }
                    });
                }
            });
        });
    </script>
@endsection
