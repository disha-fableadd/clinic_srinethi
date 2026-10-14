@extends('layout.app')

<style>
    .service-btn {
        /* padding-right: 143px !important; */
        text-align: left !important;
    }

    .page-title {

        text-align: center !important;
        padding-left: 0 !important;
    }

    .form-container {
        width: 60% !important;
        padding-bottom: 60px !important;
    }

    @media screen and (max-width:767px) {

        .page-title {
            font-size: 20px !important;
            margin-top: 7px !important;
            padding-left: 0px !important;

        }

        .service-btn {
            text-align: right !important;
            padding-right: 15px !important;

        }
    }

    .service-btn {
        text-align: center;

    }
</style>

@section('content')
    <div class="page-wrapper">
        <div class="content" style="height:100vh;">
            <div class="row" style="padding-top:15px">
                <div class="col-sm-8 col-6">
                    <h4 class="page-title service-text" style="">Edit OT Procedure</h4>
                </div>
                @if (app('hasPermission')(24, 'view'))
                    <div class="col-sm-4 col-6 service-btn" style=" ">
                        <a href="{{ route('ot.index') }}" class="btn btn-primary btn-rounded btn-hdr view-service">
                            <i class="fa fa-arrow-left m-r-5 icon3"></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    </div>
                @endif
            </div>
            <div class="row">
                <div class="col-12">
                    <form class="form-container" id="editOTProcedureForm">
                        <div class="row">
                            <!-- Patient -->
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label><i class="fas fa-user icon-style"></i> Patient Name <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control select2" name="patient_id" id="patientDropdown">
                                        <option value="">Select Patient</option>
                                    </select>
                                    <div class="invalid-feedback d-block" id="error_patient_id"></div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label><i class="fa fa-user-md icon-style"></i> Doctor Name <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control select2 doctorSelect" name="doctor_id" id="doctorSelect">
                                        <option value="">Select Doctor</option>
                                    </select>
                                    <div class="invalid-feedback d-block" id="error_doctor_id"></div>
                                </div>
                            </div>

                            <!-- Procedure Name -->
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label><i class="fas fa-notes-medical icon-style"></i> Procedure Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="procedure_name" id="procedure_name" class="form-control"
                                        placeholder="Enter Procedure Name">
                                    <div class="invalid-feedback d-block" id="error_procedure_name"></div>
                                </div>
                            </div>

                            <!-- Procedure Date -->
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label><i class="fas fa-calendar-alt icon-style"></i> Procedure Date <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="procedure_date" id="procedure_date" class="form-control">
                                    <div class="invalid-feedback d-block" id="error_procedure_date"></div>
                                </div>
                            </div>

                            <!-- Operation Notes -->
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label><i class="fas fa-file-medical-alt icon-style"></i> Operation Notes</label>
                                    <textarea name="operation_notes" id="operation_notes" class="form-control" rows="3" style="border-radius: 10px;"
                                        placeholder="Enter Notes.."></textarea>
                                    <div class="invalid-feedback d-block" id="error_operation_notes"></div>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="display-block"><i class="fas fa-check-circle icon-style"></i> <span class="hdr-btn-text">Status</span></label>
                                    <div class="form-control">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="status" id="scheduled"
                                                value="scheduled" checked>
                                            <label class="form-check-label" for="scheduled">Scheduled</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="status" id="completed"
                                                value="completed">
                                            <label class="form-check-label" for="completed">Completed</label>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback d-block" id="error_status"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Alerts -->
                        <div id="editotsuccessMessage" class="alert alert-success" style="display:none;"></div>
                        <div id="editoterrorMessage" class="alert alert-danger" style="display:none;"></div>

                        <!-- Submit -->
                        <div class="m-t-20 text-center">
                            <button type="submit" class="btn btn-primary submit-btn">Edit OT Procedure</button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>

    <!-- Include jQuery Validation Plugin -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>

    <script>
        $(document).ready(function() {
            $(document).ready(function() {
                const OTId = "{{ $id }}"; // ID passed from Blade

                $.ajax({
                    url: `/api/ot/edit-show/${OTId}`,
                    method: 'GET',
                    success: function(response) {
                        if (response.status) {
                            const ot = response.data;

                            $('#patientDropdown').val(ot.patient_id).trigger('change');
                            $('#doctorSelect').val(ot.doctor_id).trigger('change');
                            $('#procedure_name').val(ot.procedure_name);
                            $('#procedure_date').val(ot
                                .procedure_date); // Should already be YYYY-MM-DD
                            $('#operation_notes').val(ot.operation_notes);

                            if (ot.status === 'completed') {
                                $('#completed').prop('checked', true);
                            } else {
                                $('#scheduled').prop('checked', true);
                            }
                        } else {
                            $('#editoterrorMessage').text('OT Procedure not found.').show();
                        }
                    },
                    error: function() {
                        $('#editoterrorMessage').text('Something went wrong.').show();
                    }
                });
            });


            $('#editOTProcedureForm').on('submit', function(e) {
                e.preventDefault();

                let otId = "{{ $id }}"; // Blade ID
                let formData = {
                    patient_id: $('#patientDropdown').val(),
                    doctor_id: $('#doctorSelect').val(),
                    procedure_name: $('#procedure_name').val(),
                    procedure_date: $('#procedure_date').val(),
                    operation_notes: $('#operation_notes').val(),
                    status: $('input[name="status"]:checked').val()
                };

                $.ajax({
                    url: `/api/ot/update/${otId}`,
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(formData),
                    success: function(response) {
                        if (response.status) {
                            $('#editotsuccessMessage').text(response.message).show();
                            $('#editoterrorMessage').hide();
                            setTimeout(function() {
                                window.location.href = "{{ route('ot.index') }}";
                            }, 1500);
                        } else {
                            $('#editoterrorMessage').text(response.message).show();
                            $('#editotsuccessMessage').hide();
                        }
                    },
                    error: function(xhr) {
                        $('#editotsuccessMessage').hide();
                        let errors = xhr.responseJSON.errors;
                        if (errors) {
                            for (let field in errors) {
                                $(`#error_${field}`).text(errors[field][0]);
                            }
                        } else {
                            $('#editoterrorMessage').text('Something went wrong.').show();
                        }
                    }
                });
            });

            let branchId = localStorage.getItem("selectedBranchId"); // ✅ branch from localStorage
            let token = localStorage.getItem("authToken"); // ✅ your auth token
          
            $.ajax({
                url: "/api/patientss",
                type: "GET",
                data: {
                    branch_id: branchId // ✅ pass branch_id to API
                },
                dataType: "json",
                success: function(response) {
                    let patientDropdown = $('#patientDropdown');

                    if (patientDropdown.length === 0) {
                        console.error("Dropdown not found! Check your HTML.");
                        return;
                    }

                    patientDropdown.empty().append('<option value="">Select Patient</option>');

                    let patientsList = response.patients || [];

                    if (patientsList.length === 0) {
                        console.warn("No patients found for this branch.");
                        return;
                    }

                    $.each(patientsList, function(index, patient) {
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
                    patientDropdown.on('select2:open', function() {
                        $('.select2-search__field').attr('placeholder', 'Search Patient');
                    });
                },
                error: function(xhr) {
                    console.error("API Error:", xhr.status, xhr.responseText);
                }
            });

         




            $.ajax({
                url: "/api/doctors",
                type: "GET",
                data: {
                    branch_id: branchId // ✅ pass branch id
                },
                dataType: "json",
                headers: {
                    "Authorization": "Bearer " + token
                },
                success: function(response) {
                    let doctorDropdown = $('.doctorSelect');

                    if (doctorDropdown.length === 0) {
                        console.error("Dropdown not found! Check your HTML.");
                        return;
                    }

                    doctorDropdown.empty().append('<option value="">Select Doctor</option>');

                    let doctorsList = response.doctors || [];

                    if (doctorsList.length === 0) {
                        console.warn("No doctors found for this branch.");
                        return;
                    }

                    $.each(doctorsList, function(index, doctor) {
                        doctorDropdown.append(
                            `<option value="${doctor.id}">${doctor.fullname}</option>`
                        );
                    });

                    // Initialize Select2 plugin
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


        });
    </script>
@endsection
