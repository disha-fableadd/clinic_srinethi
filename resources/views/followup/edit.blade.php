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
                        {{-- Row 1: Patient and Date (col-6 col-6) --}}
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-12">
                                 <div class="form-group">
                                     <div class="d-flex align-items-center justify-content-between">
                                         <label><i class="fas fa-user-injured icon-style"></i> Patient Name <span
                                                 class="text-danger">*</span></label>
                                         @if (app('hasPermission')(5, 'create'))
                                             <button type="button" class="btn btn-sm quick-modal-add-sm-btn"
                                                 data-bs-toggle="modal" data-bs-target="#quickAddPatientModal"
                                                 data-toggle="modal" data-target="#quickAddPatientModal">
                                                 <i class="fas fa-plus"></i> Add
                                             </button>
                                         @endif
                                     </div>
                                     <select class="form-control select2 patientDropdown" id="patient_id" name="patient_id"
                                         required>
                                         <option value="">Select Patient</option>
                                     </select>

                                 </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-12">
                                <div class="form-group">
                                    <label><i class="fas fa-clock icon-style"></i> Date <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control timepicker" id="date" name="date"
                                        required>
                                </div>
                            </div>
                        </div>

                        {{-- Row 2: Treatment and Doctor (col-6 col-6) --}}
                        <div class="row">
                            @if($currentProjectTypeId !== 3)
                            <div class="col-lg-6 col-md-6 col-12">
                                <div class="form-group">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <label><i class="fas fa-stethoscope icon-style"></i> Treatment <span
                                                class="text-danger">*</span></label>
                                        @if (app('hasPermission')(7, 'create'))
                                            <button type="button" class="btn btn-sm quick-modal-add-sm-btn"
                                                data-bs-toggle="modal" data-bs-target="#quickAddTreatmentModal"
                                                data-toggle="modal" data-target="#quickAddTreatmentModal">
                                                <i class="fas fa-plus"></i> Add
                                            </button>
                                        @endif
                                    </div>
                                    <select class="form-control select2 treatmentDropdown" id="treatment_id"
                                        name="treatment_id" required>
                                        <option value="">Select Treatment</option>
                                    </select>

                                </div>
                            </div>
                            @endif

                            <div class="{{ $currentProjectTypeId !== 3 ? 'col-lg-6 col-md-6 col-12' : 'col-lg-12' }}">
                                <div class="form-group">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <label><i class="fas fa-user-md icon-style"></i> Doctor <span
                                                class="text-danger">*</span></label>
                                        @if (app('hasPermission')(3, 'create'))
                                            <button type="button" class="btn btn-sm quick-modal-add-sm-btn"
                                                data-bs-toggle="modal" data-bs-target="#quickAddDoctorModal"
                                                data-toggle="modal" data-target="#quickAddDoctorModal">
                                                <i class="fas fa-plus"></i> Add
                                            </button>
                                        @endif
                                    </div>
                                    <select class="form-control select2 doctorSelect" id="doctorSelect" name="doctor_id"
                                        required>
                                        <option value="">Select Doctor</option>
                                    </select>

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

    @include('components.quick-add-modals')

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

            // Reusable loader for Patients
            function loadPatients(selectedId = null) {
                $.ajax({
                    url: "/api/patientss",
                    type: "GET",
                    data: {
                        branch_id: branchId
                    },
                    dataType: "json",
                    xhrFields: {
                        withCredentials: true
                    },
                    success: function(response) {
                        let patientDropdown = $('.patientDropdown');
                        if (patientDropdown.length === 0) return;

                        let currentVal = selectedId || patientDropdown.val();
                        patientDropdown.empty().append('<option value="">Select Employee</option>');

                        let patientsList = response.patients || [];
                        $.each(patientsList, function(index, patient) {
                            const capitalizedName = patient.fullname.charAt(0).toUpperCase() + patient.fullname.slice(1);
                            patientDropdown.append(
                                `<option value="${patient.id}" data-treatment-id="${patient.treatment_id || ''}">
                                    ${capitalizedName}
                                </option>`
                            );
                        });

                        patientDropdown.select2({
                            placeholder: "Select Employee",
                            allowClear: true,
                            width: '100%'
                        });

                        patientDropdown.on('select2:open', function() {
                            $('.select2-search__field').attr('placeholder', 'Search Employee');
                        });

                        if (currentVal) {
                            patientDropdown.val(currentVal).trigger('change');
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
                    data: {
                        branch_id: branchId
                    },
                    dataType: "json",
                    xhrFields: {
                        withCredentials: true
                    },
                    success: function(data) {
                        let treatmentDropdown = $('select[name="treatment_id"]');
                        if (treatmentDropdown.length === 0) return;

                        let currentVal = selectedId || treatmentDropdown.val();
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

                        if (currentVal) {
                            treatmentDropdown.val(currentVal).trigger('change');
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
                    data: {
                        branch_id: branchId
                    },
                    dataType: "json",
                    headers: (typeof token !== 'undefined' && token) ? {
                        "Authorization": "Bearer " + token
                    } : {},
                    success: function(response) {
                        let doctorDropdown = $('.doctorSelect');
                        if (doctorDropdown.length === 0) return;

                        let currentVal = selectedId || doctorDropdown.val();
                        doctorDropdown.empty().append('<option value="">Select Doctor</option>');

                        let quickDoctorDropdown = $('#quickTreatmentDoctorSelect');
                        quickDoctorDropdown.empty().append('<option value="">Select Doctor (Optional)</option>');

                        let doctorsList = response.doctors || [];
                        $.each(doctorsList, function(index, doctor) {
                            const capitalizedName = doctor.fullname.charAt(0).toUpperCase() + doctor.fullname.slice(1);
                            doctorDropdown.append(
                                `<option value="${doctor.id}">${capitalizedName}</option>`
                            );
                            quickDoctorDropdown.append(
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

                        if (currentVal) {
                            doctorDropdown.val(currentVal).trigger('change');
                        }
                        if (selectedId) {
                            quickDoctorDropdown.val(selectedId);
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

            // Initialize shared Quick Add Modals
            if (typeof window.initQuickAddModals === 'function') {
                window.initQuickAddModals({
                    getToken: function() { return (typeof token !== 'undefined' && token) ? token : ''; },
                    getBranchId: function() { return branchId; },
                    onPatientAdded: function(newId) {
                        loadPatients(newId);
                    },
                    onTreatmentAdded: function(newId) {
                        loadTreatments(newId);
                    },
                    onDoctorAdded: function(newId) {
                        loadDoctors(newId);
                    }
                });
            }

            // Handle Patient Selection
            $('.patientDropdown').on("change", function() {
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
