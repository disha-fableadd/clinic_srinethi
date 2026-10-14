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
    }
</style>


@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="row" style="padding-top:15px">
                <div class="col-sm-8 col-6">
                    <h4 class="page-title report-title ">Edit Medical Report</h4>
                </div>
                @if (app('hasPermission')(13, 'view'))
                    <div class="col-sm-4 col-6 view-discharge report-btn report-btn m-b-2">
                        <a href="{{ route('report.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                            <i class="fa fa-arrow-left m-r-5 icon3"></i> <span class="hdr-btn-text">Back</span></a>
                    </div>
                @endif
            </div>
            <div class="row">
                <div class="col-12">
                    <form id="medicalReportForm" class="form-container" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="report_id" name="report_id">



                        <!-- Doctor Name & Service Name -->
                        <div class="row">
                            <div class="col-lg-12">
                                <!-- Patient Name -->
                                <div class="form-group">
                                    <label><i class="fas fa-user icon-style"></i> Patient Name <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control select2 patientDropdown" id="patient_id" name="patient_id"
                                        required>
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
                                        value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label><i class="fas fa-align-left icon-style"></i> Description <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control" rows="3" id="description" name="description" style="border-radius:10px" required></textarea>
                        </div>

                        <!-- File Upload -->
                        <div class="form-group">
                            <label><i class="fas fa-file-upload icon-style"></i> Upload Report</label>
                            <input type="file" class="form-control" name="file_path">
                            <div class="mt-2">
                                <a id="current_file" href="#" target="_blank" style="display: none;">View Current
                                    Report</a>
                            </div>
                        </div>

                        <div id="editmedicalsuccessMessage" class="alert alert-success" style="display:none;"></div>
                        <div id="editmedicalerrorMessage" class="alert alert-danger" style="display:none;"></div>

                        <div class="m-t-20 text-center">
                            <button type="submit" class="btn btn-primary submit-btn">Update Report</button>
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

        $(document).ready(function() {





            // Initialize form validation
            var form = $("#medicalReportForm");
            form.validate({
                // Validation rules for the entire form
                rules: {
                    patient_id: "required",
                    date: "required",
                    report_type: "required",
                    description: "required"
                },
                messages: {
                    patient_id: "Please select a patient",
                    date: "Please select a date",
                    report_type: "Please enter report typr",
                    description: "Please enter a description"
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

            let reportId = "{{ request()->route('id') }}"; // Get the ID from the route







            // Fetch report details
            // Fetch report details
            function fetchReportDetails() {
                if (reportId) {
                    $.ajax({
                        url: "/api/medical-reports/" + reportId,
                        type: "GET",
                        headers: {
                            "Authorization": "Bearer " + token
                        },
                        success: function(response) {
                            if (response.success) {
                                let report = response.data;
                                $('#report_id').val(report.id);
                                $('#description').val(report.description);

                                if (report.file_path) {
                                    $('#current_file').attr("href", report.file_path).show();
                                }

                                $("#patient_name").val(report.patient_id);
                                $("#date").val(report.date);
                                $("#report_type").val(report.report_type);



                                // Fetch and populate dropdowns
                                fetchAndPopulateDropdowns(report);
                            }
                        },
                        error: function(error) {
                            console.log("Error fetching report data:", error);
                        }
                    });
                }
            }

          
            function fetchAndPopulateDropdowns(report) {
                let branchId = localStorage.getItem('selectedBranchId'); // ✅ get branch_id from localStorage

                // Fetch and populate Patients
                $.ajax({
                    url: "/api/patientss",
                    type: "GET",
                    data: {
                        branch_id: branchId
                    }, // ✅ send branch_id to backend
                    dataType: "json",
                    success: function(response) {
                        let patientDropdown = $('.patientDropdown');

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

                        // ✅ Auto-select patient when editing
                        if (report && report.patient_id) {
                            patientDropdown.val(report.patient_id).trigger('change');
                        }

                        // Set search input placeholder after opening dropdown
                        patientDropdown.on('select2:open', function() {
                            $('.select2-search__field').attr('placeholder', 'Search Patient');
                        });
                    },
                    error: function(xhr) {
                        console.error("API Error:", xhr.status, xhr.responseText);
                    }
                });
            }



            form.on('submit', function(e) {
                e.preventDefault();

                if (form.valid()) {


                    let formData = new FormData(this);
                    formData.append('_method', 'PUT');


                    $.ajax({
                        url: "{{ url('/api/medical-reports') }}/" + reportId,
                        type: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        headers: {
                            "Authorization": "Bearer " + token
                        },
                        success: function(response) {
                            $('#editmedicalsuccessMessage').text(response.message ||
                                'Report updated successfully').show();
                            setTimeout(function() {
                                window.location.href = "{{ route('report.index') }}";
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
                                errorMessage = xhr.responseJSON?.error ||
                                    'An unknown error occurred';
                            }
                            $('#editmedicalerrorMessage').html(errorMessage).show();
                        }
                    });
                }
            });


            // Call fetchReportDetails directly
            fetchReportDetails();

        });
    </script>
@endsection
