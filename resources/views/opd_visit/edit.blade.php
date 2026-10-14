@extends('layout.app')

<style>
    .service-btn {
        /* padding-right: 143px !important; */
        text-align: left !important;
    }

    .page-title {
        padding-left: 247px !important;

        /* text-align: center !important; */
        /* padding-left: 0 !important; */
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
                    <h4 class="page-title service-text" style="">Edit OPD</h4>
                </div>
                @if (app('hasPermission')(23, 'view'))
                    <div class="col-sm-4 col-6 service-btn" style=" ">
                        <a href="{{ route('opd_visit.index') }}" class="btn btn-primary btn-rounded btn-hdr view-service">
                            <i class="fa fa-arrow-left m-r-5 icon3"></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    </div>
                @endif
            </div>
            <div class="row">
                <div class="col-12">
                    <form class="form-container" id="EditVisitForm">
                        <div class="row">
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

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label><i class="fas fa-calendar-alt icon-style"></i> Visit Date</label>
                                    <input type="date" class="form-control" name="visit_date" id="visit_date">
                                    <div class="invalid-feedback d-block" id="error_visit_date"></div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>₹ Consultation Fees</label>
                                    <input type="number" name="consultation_fees" id="consultation_fees"
                                        class="form-control" step="0.01" min="0">
                                    <div class="invalid-feedback d-block" id="error_consultation_fees"></div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <!-- Chief Complaint -->
                                <div class="form-group">
                                    <label><i class="fas fa-comment-medical icon-style"></i> Chief Complaint</label>
                                    <textarea name="chief_complaint" id="chief_complaint" class="form-control" rows="3" style="border-radius: 10px;"
                                        placeholder="Describe patient complaints..."></textarea>
                                    <div class="invalid-feedback d-block" id="error_chief_complaint"></div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <!-- Diagnosis -->
                                <div class="form-group">
                                    <label><i class="fas fa-stethoscope icon-style"></i> Diagnosis</label>
                                    <textarea name="diagnosis" id="diagnosis" class="form-control" rows="3" style="border-radius: 10px;"
                                        placeholder="Enter diagnosis..."></textarea>
                                    <div class="invalid-feedback d-block" id="error_diagnosis"></div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <!-- Prescription -->
                                <div class="form-group">
                                    <label><i class="fas fa-pills icon-style"></i> <span class="hdr-btn-text">Prescription</span></label>
                                    <textarea name="prescription" id="prescription" class="form-control" rows="3" style="border-radius: 10px;"
                                        placeholder=''></textarea>
                                    <div class="invalid-feedback d-block" id="error_prescription"></div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <!-- Status -->
                                <div class="form-group">
                                    <label class="display-block"><i class="fas fa-check-circle icon-style"></i> <span class="hdr-btn-text">Status</span></label>
                                    <div class="form-control">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="status" id="active"
                                                value="active" checked>
                                            <label class="form-check-label" for="active">Active</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="status" id="completed"
                                                value="completed">
                                            <label class="form-check-label" for="completed">Completed</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Alerts -->
                        <div id="editopdsuccessMessage" class="alert alert-success" style="display:none;"></div>
                        <div id="editopderrorMessage" class="alert alert-danger" style="display:none;"></div>

                        <!-- Submit -->
                        <div class="m-t-20 text-center">
                            <button type="submit" class="btn btn-primary submit-btn">UPdate OPD</button>
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
                const visitId = "{{ $id }}"; // passed from blade

                $.ajax({
                    url: `/api/opd/edit-show/${visitId}`,
                    method: 'GET',
                    success: function(response) {
                        if (response.status) {
                            const visit = response.data;

                            $('#patientDropdown').val(visit.patient_id).trigger('change');
                            $('#doctorSelect').val(visit.doctor_id).trigger('change');
                            $('#visit_date').val(visit.visit_date.split('T')[
                                0]); // '1974-10-23'
                            $('#consultation_fees').val(visit.consultation_fees);
                            $('#chief_complaint').val(visit.chief_complaint);
                            $('#diagnosis').val(visit.diagnosis);
                            // $('#prescription').val(visit.prescription);
                            $('#prescription').val(JSON.parse(visit.prescription).join('\n'));

                            // Set status radio
                            if (visit.status === 'completed') {
                                $('#completed').prop('checked', true);
                            } else {
                                $('#active').prop('checked', true);
                            }
                        } else {
                            $('#editopderrorMessage').text('Visit not found.').show();
                        }
                    },
                    error: function() {
                        $('#editopderrorMessage').text('Something went wrong.').show();
                    }
                });
            });

            $('#EditVisitForm').on('submit', function(e) {
                e.preventDefault();

                let visitId = "{{ $id }}"; // Blade variable
                let formData = {
                    patient_id: $('#patientDropdown').val(),
                    doctor_id: $('#doctorSelect').val(),
                    visit_date: $('#visit_date').val(),
                    consultation_fees: $('#consultation_fees').val(),
                    chief_complaint: $('#chief_complaint').val(),
                    diagnosis: $('#diagnosis').val(),
                    prescription: $('#prescription').val().split('\n').filter(line => line.trim() !==
                        ''),
                    status: $('input[name="status"]:checked').val()
                };

                $.ajax({
                    url: `/api/opd/update/${visitId}`,
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(formData),
                    success: function(response) {
                        if (response.status) {
                            $('#editopdsuccessMessage').text(response.message).show();
                            $('#editopderrorMessage').hide();
                            setTimeout(function() {
                                window.location.href = "{{ route('opd_visit.index') }}";
                            }, 1500);
                        } else {
                            $('#editopderrorMessage').text(response.message).show();
                            $('#editopdsuccessMessage').hide();
                        }
                    },
                    error: function(xhr) {
                        $('#editopdsuccessMessage').hide();
                        let errors = xhr.responseJSON.errors;
                        if (errors) {
                            // Display validation errors
                            for (let field in errors) {
                                $(`#error_${field}`).text(errors[field][0]);
                            }
                        } else {
                            $('#editopderrorMessage').text('Something went wrong.').show();
                        }
                    }
                });
            });

              let branchId = localStorage.getItem("selectedBranchId"); // ✅ branch from localStorage
            let token = localStorage.getItem("authToken"); // ✅ your auth token
            // Fetch Patients
            // $.ajax({
            //     url: "/api/patientss",
            //     type: "GET",
            //     dataType: "json",
            //     success: function(response) {
            //         let patientDropdown = $('#patientDropdown');

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
            //         patientDropdown.on('select2:open', function() {
            //             $('.select2-search__field').attr('placeholder', 'Search Patient');
            //         });
            //     },
            //     error: function(xhr) {
            //         console.error("API Error:", xhr.status, xhr.responseText);
            //     }
            // });
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

            // Fetch Doctors

            // $.ajax({
            //     url: "/api/doctors",
            //     type: "GET",
            //     dataType: "json",
            //     headers: {
            //         "Authorization": "Bearer " + token
            //     },
            //     success: function(response) {
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
            //         doctorDropdown.on('select2:open', function() {
            //             $('.select2-search__field').attr('placeholder', 'Search Doctor');
            //         });
            //     },
            //     error: function(xhr) {
            //         console.error("API Error:", xhr.status, xhr.responseText);
            //     }
            // });




          

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

