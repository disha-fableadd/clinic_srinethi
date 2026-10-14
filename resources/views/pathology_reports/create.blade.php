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
                    <h4 class="page-title followup-title">Add Pathology Report</h4>
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
                    <form id="pathologyReportForm" method="POST" action="javascript:void(0)" enctype="multipart/form-data"
                        class="form-container all-form follow-form">

                        @csrf
                        <input type="hidden" name="branch_id" id="branch_id">
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
                                    <input type="date" class="form-control" name="sample_collected_date"
                                        max="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Report Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="report_date" min="{{ date('Y-m-d') }}"
                                        required>
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <label>Result <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="result" rows="4" required style="border-radius: 10px;"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Report File (PDF) <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="report_file" accept="application/pdf" required>
                        </div>

                        <div id="pathologyreportsuccessMessage" class="alert alert-success" style="display:none;"></div>
                        {{-- <div id="pathologyreporterrorMessage" class="alert alert-danger" style="display:none;"></div> --}}

                        <button type="submit" class="btn btn-primary d-block m-auto"
                            style="padding:8px 50px; border-radius:50px; ">
                            Submit
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
        document.addEventListener('DOMContentLoaded', function() {
            let storedBranchId = localStorage.getItem('selectedBranchId');
            if (storedBranchId) {
                document.getElementById('branch_id').value = storedBranchId;
            }
        });
        $.validator.addMethod("extension", function(value, element, param) {
            param = typeof param === "string" ? param.replace(/,/g, '|') : "pdf";
            return this.optional(element) || value.match(new RegExp("\\.(" + param + ")$", "i"));
        }, $.validator.format("Please enter a file with a valid extension ({0})."));

        $(document).ready(function() {
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
                        required: true,
                        extension: "pdf"
                    }
                },
                messages: {
                    patient_id: "Please select a patient",
                    test_id: "Please select a test",
                    sample_collected_date: "Please enter the sample collected date",
                    report_date: "Please enter the report date",
                    result: "Please enter the result",
                    report_file: {
                        required: "Please upload the report file",
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

            // Load Pathology Tests
          $.get("/api/pathology-tests", { branch_id: localStorage.getItem('selectedBranchId') }, function(response) {
    let dropdown = $('#testDropdown');
    dropdown.empty().append('<option value="">Select Test</option>');

    $.each(response.data || response, function(i, test) {
        const capitalizedName = test.test_name.charAt(0).toUpperCase() + test.test_name.slice(1);
        dropdown.append(`<option value="${test.id}">${capitalizedName}</option>`);
    });

    dropdown.select2({
        placeholder: "Select Test",
        width: '100%'
    });
});


            // Submit form
            $('#pathologyReportForm').on('submit', function(e) {
                e.preventDefault();
                if (!form.valid()) {
                    return;
                }
                let formData = new FormData(this);

                $.ajax({
                    url: "/api/pathology-reports",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('#pathologyreportsuccessMessage').text(response.message ||
                            "Pathology Report created successfully").show();
                        $('#pathologyReportForm')[0].reset();
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
                        $('#pathologyreporterrorMessage').html(msg).fadeIn();
                        setTimeout(() => $('#pathologyreporterrorMessage').fadeOut(), 5000);
                    }
                });
            });
        });
    </script>
@endsection
