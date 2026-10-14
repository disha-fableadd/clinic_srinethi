@extends('layout.app')

<style>
    .radiology-btn {
        text-align: left !important;
    }

    .page-title {
        text-align: center !important;
        padding-left: 90px !important;
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
                    <h4 class="page-title">Add Radiology Report</h4>
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
                    <form class="form-container"  id="radiology-repor-form"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="branch_id" id="branch_id">
                        <div class="form-group">
                            <label>Patient <span class="text-danger">*</span></label>
                            <select class="form-control select2" name="patient_id" id="patientDropdown" required>
                                <option value="">Select Patient</option>
                            </select>
                        </div>
                        @error('patient_id')
                            <span class="invalid-feedback" style="color:#e74c3c;font-size:0.95em;">{{ $message }}</span>
                        @enderror
                        <div class="form-group">
                            <label><i class="fas fa-barcode icon-style"></i> Test Select <span
                                                        class="text-danger">*</span></label>
                            <div class="input-wrapper">
                                <select class="form-control " name="test_id" id="testSelect">
                                    <option value="">Select Test</option>
                                </select>
                            </div>

                        </div>
                        @error('test_id')
                            <span class="invalid-feedback" style="color:#e74c3c;font-size:0.95em;">{{ $message }}</span>
                        @enderror
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
                        </div>

                        <!-- Alerts -->
                        <div id="radioreportsuccessMessage" class="alert alert-success" style="display:none;"></div>
                        <div id="radioreporterrorMessage" class="alert alert-danger" style="display:none;"></div>
                        <div class="m-t-20 text-center">
                            <button type="submit" class="btn btn-primary submit-btn">Create Radiology Report</button>
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
                placeholder: "Select Patient",
                width: '100%'
            });

            $('#testSelect').select2({
                placeholder: "Select Test",
                width: '100%'
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let storedBranchId = localStorage.getItem('selectedBranchId');
            if (storedBranchId) {
                document.getElementById('branch_id').value = storedBranchId;
            }
        });
        $(document).ready(function() {
            // Fetch patients
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
            })

            // Fetch tests
            $.ajax({
                url: '/api/radiology-tests',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    var tests = data.radiology;
                    var testSelect = $('#testSelect');
                    tests.forEach(function(test) {
                        // Capitalize first letter of each word
                        let capitalizedName = test.test_name.replace(/\b\w/g, c => c
                            .toUpperCase());
                        testSelect.append('<option value="' + test.id + '">' + capitalizedName +
                            '</option>');
                    });

                }

            });
        });


        $('#radiology-repor-form').on('submit', function(e) {
            e.preventDefault();

            // Clear previous messages and errors
            $('#radioreportsuccessMessage').hide().text('');
            $('#radioreporterrorMessage').hide().text('');
            $('.invalid-feedback').remove();
            $('.is-invalid').removeClass('is-invalid');

            let form = new FormData(this);

            $.ajax({
                url: '/api/radiology-reports',
                type: 'POST',
                data: form,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#radioreportsuccessMessage').text(response.message).show();
                    $('#radiology-repor-form')[0].reset(); // optional: reset form
                    // Optionally reload or redirect after a delay
                    setTimeout(() => window.location.href = '/radiology-reports', 2000);
                },
                error: function(xhr) {
                    console.log(xhr); // Add this line
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        console.log(errors); // Add this line

                        $.each(errors, function(key, messages) {
                            let input = $('[name="' + key + '"]');
                            input.addClass('is-invalid');
                            input.after(
                                '<span class="invalid-feedback" style="color:#e74c3c;font-size:0.95em;">' +
                                messages[0] + '</span>');
                        });
                    } else if (xhr.responseJSON && xhr.responseJSON.error) {
                        $('#radioreporterrorMessage').text(xhr.responseJSON.error).show();
                    } else {
                        $('#radioreporterrorMessage').text('Something went wrong. Please try again.')
                            .show();
                    }
                }

            });
        });
    </script>
@endsection
