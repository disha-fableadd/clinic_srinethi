@extends('layout.app')

<style>
    .radiology-btn {
        text-align: left !important;
    }

    .page-title {
        text-align: center !important;
        padding-left: 86px !important;
    }

    @media screen and (max-width:767px) {
        .page-title {
            font-size: 20px !important;
            margin-top: 7px !important;
            padding-left: 0px !important;
        }

        .radiology-btn {
            text-align: right !important;
            padding-right: 15px !important;
        }
    }

    .radiology-btn {
        text-align: center;
    }

    .is-invalid {
        border: 2px solid #e74c3c !important;
        background-color: #fff6f6;
    }

    .error-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #e74c3c;
        font-size: 22px;
        pointer-events: none;
    }

    .input-wrapper {
        position: relative;
    }

    .invalid-feedback {
        display: block;
        color: #e74c3c;
        font-size: 0.95em;
        margin-top: 2px;
    }
</style>

@section('content')
    <div class="page-wrapper">
        <div class="content" style="height:100vh;">
            <div class="row" style="padding-top:15px">
                <div class="col-sm-8 col-6">
                    <h4 class="page-title">Edit Radiology Report</h4>
                </div>
                @if (app('hasPermission')(22, 'view'))
                    <div class="col-sm-4 col-6 radiology-btn">
                        <a href="{{ route('radiology-reports.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                            <i class="fa fa-arrow-left m-r-5"></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    </div>
                @endif
            </div>
            <div class="row">
                <div class="offset-lg-2">
                    <form class="form-container" style="width:60%;padding-bottom:60px;" id="radiology-report-form"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <input type="hidden" name="id" id="id">
                            <label><i class="fas fa-vial icon-style"></i>Patient Select <span
                                                        class="text-danger">*</span></label>
                            <div class="input-wrapper">
                                <select class="form-control @error('patient_id') is-invalid @enderror" name="patient_id"
                                    id="patientSelect">
                                    <option value="">Select Patient</option>
                                </select>
                            </div>
                            @error('patient_id')
                                <span class="invalid-feedback"
                                    style="color:#e74c3c;font-size:0.95em;">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-barcode icon-style"></i> Test Select <span
                                                        class="text-danger">*</span></label>
                            <div class="input-wrapper">
                                <select class="form-control @error('test_id') is-invalid @enderror" name="test_id"
                                    id="testSelect">
                                    <option value="">Select Test</option>
                                </select>
                            </div>
                            @error('test_id')
                                <span class="invalid-feedback"
                                    style="color:#e74c3c;font-size:0.95em;">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-user-md icon-style"></i> Report Date <span
                                                        class="text-danger">*</span></label>
                            <div class="input-wrapper">
                                <input class="form-control @error('report_date') is-invalid @enderror" type="date"
                                    name="report_date" value="{{ old('report_date') }}">
                            </div>
                            @error('report_date')
                                <span class="invalid-feedback"
                                    style="color:#e74c3c;font-size:0.95em;">{{ $message }}</span>
                            @enderror
                        </div>

                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                const today = new Date().toISOString().split('T')[0];
                                document.querySelector("input[name='report_date']").setAttribute('max', today);
                            });
                        </script>

                        <div class="form-group">
                            <label><i class="fas fa-file-alt icon-style"></i> Report File <span
                                                        class="text-danger">*</span></label>
                            <div class="input-wrapper">
                                <input class="form-control @error('report_file') is-invalid @enderror" type="file"
                                    name="report_file">
                            </div>
                            @error('report_file')
                                <span class="invalid-feedback"
                                    style="color:#e74c3c;font-size:0.95em;">{{ $message }}</span>
                            @enderror
                            <a id="existingFileLink" href="#" target="_blank" class="d-block mt-2">View Existing
                                File</a>

                        </div>

                        <!-- Alerts -->
                        <div id="editradioreportsuccessMessage" class="alert alert-success" style="display:none;"></div>
                        <div id="editradioreporterrorMessage" class="alert alert-danger" style="display:none;"></div>
                        <div class="m-t-20 text-center">
                            <button type="submit" class="btn btn-primary submit-btn">Update Radiology Report</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#patientSelect').select2({
                placeholder: 'Select Patient',
                allowClear: true,
                width: '100%'
            });

            $('#testSelect').select2({
                placeholder: 'Select Test',
                allowClear: true,
                width: '100%'
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            // Get report ID from URL
            function getIdFromUrl() {
                const segments = window.location.pathname.split('/');
                return segments.pop() || segments.pop();
            }
            const reportId = getIdFromUrl();
            let reportData = null;

            const branchId = localStorage.getItem('selectedBranchId');

            // Fetch patients and tests in parallel, then fetch report
            $.when(
                $.ajax({
                    url: '/api/patientss',
                    type: 'GET',
                    data: {
                        branch_id: branchId
                    },
                    dataType: 'json',
                    xhrFields: {
                        withCredentials: true
                    }
                }),
                $.ajax({
                    url: '/api/radiology-tests',
                    type: 'GET',
                    data: {
                        branch_id: branchId
                    },
                    dataType: 'json',
                    xhrFields: {
                        withCredentials: true
                    }
                }),
                $.ajax({
                    url: '/api/radiology-reports/' + reportId,
                    type: 'GET',
                    dataType: 'json',
                    xhrFields: {
                        withCredentials: true
                    }
                })
            ).done(function(patientsRes, testsRes, reportRes) {
                const patients = patientsRes[0].patients;
                const tests = testsRes[0].radiology;
                reportData = reportRes[0];

                // Populate patients
                var patientSelect = $('#patientSelect');
                patientSelect.empty().append('<option value="">Select Patient</option>');
                if (patients) {
                    patients.forEach(function(patient) {
                        var fullName = patient.fullname;
                        var formattedName = fullName.charAt(0).toUpperCase() + fullName.slice(
                            1);
                        patientSelect.append('<option value="' + patient.id + '">' +
                            formattedName + '</option>');
                    });
                }

                // Populate tests
                var testSelect = $('#testSelect');
                testSelect.empty().append('<option value="">Select Test</option>');
                if (tests) {
                    tests.forEach(function(test) {
                        // Capitalize first letter of each word
                        let capitalizedName = test.test_name.replace(/\b\w/g, c => c
                            .toUpperCase());
                        testSelect.append('<option value="' + test.id + '">' + capitalizedName +
                            '</option>');
                    });
                }

                // Set selected values and other fields
                if (reportData) {
                    $('#id').val(reportData.id);
                    $('#patientSelect').val(reportData.patient_id).trigger('change');
                    $('#testSelect').val(reportData.test_id).trigger('change');
                    $('[name="report_date"]').val(reportData.report_date);

                    if (reportData.converted_image) {
                        $('#existingFileLink').attr('href', '/public/' + reportData.converted_image);
                    } else {
                        $('#existingFileLink').hide();
                    }
                }
            });

            // Handle form submission for update
            $('#radiology-report-form').on('submit', function(e) {
                e.preventDefault();

                // Clear previous errors
                $('.invalid-feedback').remove();
                $('.is-invalid').removeClass('is-invalid');
                $('#editradioreporterrorMessage').hide().text('');
                $('#editradioreportsuccessMessage').hide().text('');

                let form = new FormData(this);

                $.ajax({
                    url: '/api/radiology-reports-update/',
                    type: 'POST',
                    data: form,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#editradioreportsuccessMessage').text(response.message ||
                            'Radiology report updated successfully!').fadeIn();
                        setTimeout(() => {
                            window.location.href = '/radiology-reports';
                        }, 1500);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let errorText = '<ul>';
                            $.each(errors, function(key, messages) {
                                let input = $('[name="' + key + '"]');
                                input.addClass('is-invalid');
                                input.after(
                                    '<span class="invalid-feedback" style="color:#e74c3c;font-size:0.95em;">' +
                                    messages[0] + '</span>');
                                errorText += `<li>${messages[0]}</li>`;
                            });
                            errorText += '</ul>';
                            $('#editradioreporterrorMessage').html(errorText).fadeIn();
                        } else {
                            $('#editradioreporterrorMessage').text(
                                'Something went wrong. Please try again.').fadeIn();
                        }
                    }
                });
            });

        });
    </script>
@endsection

