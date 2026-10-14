@extends('layout.app')
<style>
    .service-btn {
        /* padding-right: 143px !important; */
        text-align: left !important;
    }

    .page-title {

        /* text-align: center !important; */
        /* padding-left: 0 !important; */
        padding-left: 247px !important;
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
                    <h4 class="page-title service-text" style="">Add OPD</h4>
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
                    <form class="form-container" id="createVisitForm">

                        <div class="row">
                            <input type="hidden" name="branch_id" id="branch_id">
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
                                    <label><i class="fas fa-calendar-alt icon-style"></i> Visit Date <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="visit_date" id="visit_date">
                                    <div class="invalid-feedback d-block" id="error_visit_date"></div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>₹ Consultation Fees <span
                                            class="text-danger">*</span></label>
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
                        <div id="opdsuccessMessage" class="alert alert-success" style="display:none;"></div>
                        <div id="opderrorMessage" class="alert alert-danger" style="display:none;"></div>

                        <!-- Submit -->
                        <div class="m-t-20 text-center">
                            <button type="submit" class="btn btn-primary submit-btn">Create OPD</button>
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
        document.addEventListener('DOMContentLoaded', function() {
            let storedBranchId = localStorage.getItem('selectedBranchId');
            if (storedBranchId) {
                document.getElementById('branch_id').value = storedBranchId;
            }
        });
        $(document).ready(function() {
            $('#createVisitForm').on('submit', function(e) {
                e.preventDefault();

                let formData = {
                    branch_id: $('#branch_id').val(), // ✅ include branch_id from hidden input
                    patient_id: $('#patientDropdown').val(),
                    doctor_id: $('#doctorSelect').val(),
                    visit_date: $('#visit_date').val(),
                    consultation_fees: $('#consultation_fees').val(),
                    chief_complaint: $('#chief_complaint').val(),
                    diagnosis: $('#diagnosis').val(),
                    prescription: $('#prescription').val().split('\n').filter(line => line.trim() !==
                        ''),
                    status: $('input[name="status"]:checked').val(),
                };
                $.ajax({
                    url: '/api/opd-visits', // Adjust if your API route is different
                    type: 'POST',
                    data: JSON.stringify(formData),
                    contentType: 'application/json',
                    headers: {
                        Authorization: 'Bearer ' + localStorage.getItem('authToken') // if using JWT
                    },
                    success: function(response) {
                        $('#opdsuccessMessage').text(response.message ||
                            'OPD created successfully').show();
                        $('#opderrorMessage').hide();
                        $('#createVisitForm')[0].reset();
                        setTimeout(function() {
                            window.location.href = "{{ route('opd_visit.index') }}";
                        }, 1500);
                        $('.select2').val('').trigger('change');
                    },
                    error: function(xhr) {
                        // Clear previous errors
                        $('.invalid-feedback').text('');

                        let errorMsg = 'An error occurred.';

                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            let errors = xhr.responseJSON.errors;

                            // Loop through all errors
                            Object.keys(errors).forEach(function(key) {
                                $('#error_' + key).text(errors[key][0]);
                            });
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                            $('#opderrorMessage').text(errorMsg).show();
                        } else {
                            $('#opderrorMessage').text(errorMsg).show();
                        }

                        $('#opdsuccessMessage').hide();
                    }

                });
            });



         
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




        });
    </script>
@endsection

