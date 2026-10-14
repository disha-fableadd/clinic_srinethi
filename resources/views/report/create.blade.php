@extends('layout.app')



<style>
    .report-title {
        text-align: center !important;
        /* padding-left: 160px !important; */
    }

    .report-btn {
        text-align: center !important;
        padding-right: 194px !important;
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
            padding-top: 6px !important;
        }

        .report-btn {
            text-align: right !important;
            /* / padding-right: 100px; */
        }

        .report-title {
            text-align: left !important;
            /* padding-left: 160px !important; */
        }
    }
</style>


@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="row" style="padding-top:15px">
                <div class="col-sm-8 col-6">
                    <h4 class="page-title report-title ">Add Medical Report</h4>
                </div>
                @if (app('hasPermission')(13, 'view'))
                    <div class="col-sm-4 col-6  m-b-2 view-discharge report-btn report-btn">
                        <a href="{{ route('report.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                            <i class="fa fa-arrow-left m-r-5 icon3"></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    </div>
                @endif
            </div>
            <div class="row">
                <div class="col-12">
                    <form id="medicalReportForm" class="form-container" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="branch_id" id="branch_id">

                        <!-- Doctor Name -->
                        <div class="row">
                            <!-- Patient Name -->
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label><i class="fas fa-user icon-style"></i> Patient Name <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control select2" name="patient_id" id="patientDropdown" required>
                                        <option value="">Select Patient</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-6 ">
                                <div class="form-group">
                                    <label><i class="fas fa-calendar-alt icon-style"></i> Report Type <span
                                                        class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="report_type" id="report_type"
                                        placeholder="Eg:Scan,X-Ray,Blood">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label><i class="fas fa-calendar-alt icon-style"></i> Date <span
                                                        class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="date" id="date"
                                        value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                                </div>

                            </div>
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label><i class="fas fa-align-left icon-style"></i> Description <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control" rows="3" name="description" style="border-radius:10px" required></textarea>
                        </div>

                        <!-- File Upload -->
                        <div class="form-group">
                            <label><i class="fas fa-file-upload icon-style"></i> Upload Report <span
                                    class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="file_path" required>
                        </div>

                        <div id="medicalsuccessMessage" class="alert alert-success" style="display:none;"></div>
                        <div id="medicalerrorMessage" class="alert alert-danger" style="display:none;"></div>

                        <div class="m-t-20 text-center">
                            <button type="submit" class="btn btn-primary submit-btn">Create Report</button>
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
        document.addEventListener('DOMContentLoaded', function() {
            let storedBranchId = localStorage.getItem('selectedBranchId');
            if (storedBranchId) {
                document.getElementById('branch_id').value = storedBranchId;
            }
        });
        $('#type').select2({
            placeholder: "Select Type",
            width: '100%'
        });
        $('#type').on('select2:open', function() {
            $('.select2-search__field').attr('placeholder', 'Search Type');
        });
        $(document).ready(function() {





            // Initialize form validation
            var form = $("#medicalReportForm");
            form.validate({
                // Validation rules for the entire form
                rules: {
                    patient_id: "required",
                    date: "required",
                    report_type: "required",
                    description: "required",
                    file_path: "required"
                },
                messages: {
                    patient_id: "Please select a patient",
                    date: "Please select a date",
                    report_type: "Please enter report type",
                    description: "Please enter a description",
                    file_path: "Please upload a report"
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



            // Form Submission
            form.on('submit', function(e) {
                e.preventDefault();


                if (form.valid()) {
                    let formData = new FormData(this);



                    $.ajax({
                        url: "{{ url('/api/medical-reports') }}",
                        type: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        headers: {
                            "Authorization": "Bearer " + token
                        },
                        success: function(response) {
                            $('#medicalsuccessMessage').text(response.message ||
                                'Report created successfully').show();
                            $('#medicalReportForm')[0].reset();

                            setTimeout(function() {
                                window.location.href = "{{ route('report.index') }}";
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
                            $('#medicalerrorMessage').html(errorMessage).show();
                        }
                    });
                }
            });
        });
    </script>
@endsection
