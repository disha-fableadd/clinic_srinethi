@extends('layout.app')

<style>
    .followup-title {
        padding-left: 180px !important;
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
                    <h4 class="page-title followup-title">Edit Pathology Report</h4>
                </div>
                @if (app('hasPermission')(20, 'view'))
                    <div class="col-6 followup-button m-b-2 eye-btn">
                        <a href="{{ route('pathology_reports.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                            <i class="fa fa-arrow-left m-r-5 icon3"></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    </div>
                @endif
            </div>

            <div class="row">
                <div class="col-12">
                    <form class="form-container all-form follow-form" id="pathologyReportForm" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label>Patient <span class="text-danger">*</span></label>
                            <select class="form-control select2" name="patient_id" id="patientDropdown" required>
                                <option value="">Select Patient</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Test <span class="text-danger">*</span></label>
                            <select class="form-control select2" name="test_id" id="testDropdown" required>
                                <option value="">Select Test</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Sample Collected Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="sample_collected_date" id="sampleDate"
                                        max="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Report Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="report_date" id="reportDate"
                                        min="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Result <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="result" id="resultField" rows="4" required style="border-radius: 10px;"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Report File (PDF)</label>
                            <input type="file" class="form-control" name="report_file" accept="application/pdf">
                            <small class="form-text text-muted">Leave blank to keep existing file.</small>
                            <a id="existingFileLink" href="#" target="_blank" class="d-block mt-2">View Existing
                                File</a>
                        </div>

                        <div id="editpathologyreportsuccessMessage" class="alert alert-success" style="display:none;"></div>
                        {{-- <div id="editpathologyreporterrorMessage" class="alert alert-danger" style="display:none;"></div> --}}

                        <button type="submit" class="btn btn-primary d-block m-auto"
                            style="padding:8px 50px; border-radius:50px;">
                            Update
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        // Add extension rule if not already defined
        $.validator.addMethod("extension", function(value, element, param) {
            param = typeof param === "string" ? param.replace(/,/g, '|') : "pdf";
            return this.optional(element) || value.match(new RegExp("\\.(" + param + ")$", "i"));
        }, $.validator.format("Please upload a valid file ({0})."));

        // Form validation
        let form = $("#pathologyReportForm");
        form.validate({
            rules: {
                patient_id: "required",
                test_id: "required",
                sample_collected_date: {
                    required: true,
                    date: true
                },
                report_date: {
                    required: true,
                    date: true
                },
                result: "required",
                report_file: {
                    extension: "pdf" // Optional, but if present must be pdf
                }
            },
            messages: {
                patient_id: "Please select a patient",
                test_id: "Please select a test",
                sample_collected_date: "Please enter the sample collected date",
                report_date: "Please enter the report date",
                result: "Please enter the result",
                report_file: {
                    extension: "Only PDF files are allowed"
                }
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




        $(document).ready(function() {
            let reportId = `{{ $pathology_report_id }}`;

            // Load patient list
            $.get("/api/patientss", function(res) {
                let dropdown = $('#patientDropdown');
                dropdown.empty().append('<option value="">Select Patient</option>');
                $.each(response.patients || [], function(i, patient) {
                    // Capitalize first letter only
                    const capitalizedName = patient.fullname.charAt(0).toUpperCase() + patient
                        .fullname.slice(1);

                    dropdown.append(
                        `<option value="${patient.id}">${capitalizedName}</option>`
                    );
                });
                dropdown.select2({
                    width: '100%'
                });
            });

            // Load test list
            $.get("/api/pathology-tests", function(res) {
                let dropdown = $('#testDropdown');
                dropdown.empty().append('<option value="">Select Test</option>');
                $.each(response.data || response, function(i, test) {
                    // Capitalize first letter only
                    const capitalizedName = test.test_name.charAt(0).toUpperCase() + test.test_name
                        .slice(1);

                    dropdown.append(
                        `<option value="${test.id}">${capitalizedName}</option>`
                    );
                });
                dropdown.select2({
                    width: '100%'
                });
            });

            // Fetch existing report data
            $.get(`/api/pathology-reports/${reportId}`, function(report) {
                $('#patientDropdown').val(report.patient_id).trigger('change');
                $('#testDropdown').val(report.test_id).trigger('change');
                $('#sampleDate').val(report.sample_collected_date);
                $('#reportDate').val(report.report_date);
                $('#resultField').val(report.result);
                if (report.report_file) {
                    $('#existingFileLink').attr('href', '/' + report.report_file);
                } else {
                    $('#existingFileLink').hide();
                }
            });

            // Submit form via PUT
            $('#pathologyReportForm').on('submit', function(e) {
                e.preventDefault();

                if (!form.valid()) {
                    return;
                }
                let formData = new FormData(this);
                formData.append('_method', 'PUT'); // Laravel method spoofing

                $.ajax({
                    url: `/api/pathology-reports/${reportId}`,
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        $('#editpathologyreportsuccessMessage').text(res.message ||
                            "Report updated successfully").fadeIn();
                        setTimeout(() => {
                            window.location.href =
                                "{{ route('pathology_reports.index') }}";
                        }, 1500);
                    },
                    error: function(xhr) {
                        let msg = 'Something went wrong.';
                        if (xhr.status === 422) {
                            msg = Object.values(xhr.responseJSON.errors).map(e => e[0]).join(
                                '<br>');
                        }
                        $('#editpathologyreporterrorMessage').html(msg).fadeIn();
                        setTimeout(() => $('#editpathologyreporterrorMessage').fadeOut(), 5000);
                    }
                });
            });
        });
    </script>
@endsection
