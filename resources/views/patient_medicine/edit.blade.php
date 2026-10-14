@extends('layout.app')


<style>
    .selected-medicines {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        border: 1px solid #ced4da;
        padding: 5px;
        min-height: 40px;
        cursor: pointer;
        background: #fff;
    }

    .medicine-tag {
        background: #007bff;
        color: white;
        padding: 5px 10px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .medicine-tag .remove-medicine {
        cursor: pointer;
        color: white;
        font-weight: bold;
    }

    /* .select2-container--default .select2-selection--multiple {
        border-radius: 50px !important;
        border: 1px solid rgb(207, 236, 224) !important;
        min-height: 40px !important;
        padding: 5px;
        font-size: 14px;
        background-color: #fff;
        width: 100%;
        transition: border-color 0.3s ease-in-out;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        background-color: rgb(207, 236, 224) !important;
        color: black !important;
        border-radius: 20px !important;
     
        border: none;
        font-size: 14px;
        font-weight: normal !important;
        text-align: center;
        max-width: fit-content;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: black !important;
        cursor: pointer;
        display: inline-block;
        font-weight: bold;
        margin-right: 2px;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {

        margin-top: 2px !important;

    } */

    .pm-title {
        /* padding-left: 95px !important; */
        text-align: center !important;
    }

    .pm-button {
        padding-right: 190px !important;
        text-align: center !important;
    }

    .form-container {
        width: 60% !important;
        padding-bottom: 60px !important;
    }

    .medicine-row {
        background: #fff;
        padding: 20px;
        border-radius: 15px;
        border: 1px solid #e0e0e0;
        position: relative;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        flex-grow: 1;
    }

    .medicine-row-wrapper {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        gap: 15px;
    }

    .form-control {
        padding-left: 20px !important;
    }

    /* .select2-container--default .select2-selection--single {
        padding-left: 0 !important;
    }

    .form-control, .select2-container--default .select2-selection--single {
        border-radius: 50px !important;
        border: 1px solid rgb(207, 236, 224) !important;
        height: 45px !important;
        font-size: 14px !important;
        line-height: 45px !important; 
        display: flex;
        align-items: center;
    } */

    .form-control::placeholder {
        line-height: normal;
        text-align: center;
    }

    /* .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 45px !important;
        padding-left: 0 !important;
        padding-right: 20px !important;
        text-align: center !important;
        width: 100%;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 43px !important;
        right: 10px !important;
    } */

    .add-medicine-row, .remove-medicine-row {
        width: 28px !important;
        height: 28px !important;
        border-radius: 6px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 0 !important;
        font-size: 12px !important;
        border: none !important;
        transition: all 0.3s ease;
    }

    .add-medicine-row {
        background-color: #CFECE0 !important;
        color: #0c4b33 !important;
    }

    .add-medicine-row:hover {
        background-color: #bde7d6 !important;
    }

    .remove-medicine-row {
        background-color: #F5B6A5 !important;
        color: #721c24 !important;
    }

    .remove-medicine-row:hover {
        background-color: #f2a38d !important;
    }

    .action-buttons {
        padding-top: 28px;
    }

    .form-group label {
        font-weight: 500;
        color: #555;
        margin-bottom: 8px;
        font-size: 14px;
        padding-left: 5px;
    }


    @media screen and (max-width:767px) {
        .pm-title {
            padding-left: 15px !important;
            text-align: left !important;
        }

        .pm-button {
            padding-right: 15px !important;
            text-align: right !important;
        }

        .page-title {
            font-size: 20px !important;
            padding-top: 6px !important;

        }
    }
</style>


@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="row" style="padding-top:15px">
                <div class="col-sm-8 col-8">
                    <h4 class="page-title pm-title">Edit Patient Medicine</h4>
                </div>

                @if (app('hasPermission')(17, 'view'))
                    <div class="col-sm-4 col-4 pm-button m-b-2">
                        <a href="{{ route('patient_medicine.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                            <i class="fa fa-arrow-left m-r-5 icon3"></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    </div>
                @endif

            </div>

            <div class="row">
                <div class="col-12">
                    <form id="createmedicineForm" class="form-container">
                        @csrf

                        <!-- Patient Selection -->
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label><i class="fas fa-user-injured icon-style"></i> Patient <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control select2 patientDropdown" id="patient_id" name="patient_id"
                                        required>
                                        <option value="">Select Patient</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="mr-2"><i class="fas fa-medkit icon-style"></i> Treatment <span
                                            class="text-danger">*</span></label>

                                    <select class="form-control select2 treatmentDropdown" id="treatment_id"
                                        name="treatment_id" required>
                                        <option value="">Select Treatment</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <label><i class="fas fa-pills icon-style"></i> Medicines <span
                                        class="text-danger">*</span></label>
                                <div id="medicine-rows-container">
                                    <!-- Rows will be populated dynamically -->
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="form-group">
                            <label><i class="fas fa-clipboard-list icon-style"></i> Note</label>
                            <textarea cols="7" rows="4" class="form-control" name="note" id="note" style="border-radius:10px"></textarea>
                        </div>

                        <!-- Success & Error Messages -->
                        <div id="editpatientmedisuccessMessage" class="alert alert-success" style="display:none;"></div>
                        {{-- <div id="editpatientmedierrorMessage" class="alert alert-danger" style="display:none;"></div> --}}

                        <!-- Submit Button -->
                        <div class="m-t-20 text-center">
                            <button type="submit" class="btn btn-primary submit-btn">Update Patient Medicines</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Include Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <!-- Include jQuery Validation Plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function() {
            // ✅ Initialize Select2 immediately
            $('.select2').select2({
                width: '100%'
            });

            let token = localStorage.getItem("authToken");
            let branchId = localStorage.getItem('selectedBranchId');
            let allMedicines = [];
            let allCodes = [];
            let patientmedicineId = "{{ $patientmedicine_id }}";

            // Initialize form validation
            $('#createmedicineForm').validate({
                rules: {
                    patient_id: { required: true },
                    treatment_id: { required: true },
                    note: { required: true }
                },
                messages: {
                    patient_id: { required: "Please select a patient" },
                    treatment_id: { required: "Please select a treatment" },
                    note: { required: "Please enter a description" }
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

            function fetchMedicines() {
                return $.ajax({
                    url: "/api/medicine",
                    type: "GET",
                    data: { branch_id: branchId },
                    headers: { "Authorization": "Bearer " + token },
                    success: function(response) {
                        if (response.medicines) {
                            allMedicines = response.medicines;
                        }
                    }
                });
            }

            function fetchCodes() {
                return $.ajax({
                    url: "/api/codemaster",
                    type: "GET",
                    data: { branch_id: branchId },
                    headers: { "Authorization": "Bearer " + token },
                    success: function(response) {
                        if (response) {
                            allCodes = response;
                        }
                    }
                });
            }

            function populateMedicineDropdown(dropdown, selectedValue) {
                dropdown.empty().append('<option value="">Select Medicine</option>');
                $.each(allMedicines, function(index, medicine) {
                    let selected = (medicine.id == selectedValue) ? 'selected' : '';
                    dropdown.append(`<option value="${medicine.id}" ${selected}>${medicine.name}</option>`);
                });
                dropdown.select2({ placeholder: "Select Medicine", allowClear: true, width: '100%' });
            }

            function populateCodeDropdown(dropdown, selectedValue) {
                dropdown.empty().append('<option value="">Select Code</option>');
                $.each(allCodes, function(index, item) {
                    let selected = (item.id == selectedValue) ? 'selected' : '';
                    dropdown.append(`<option value="${item.id}" ${selected}>${item.code}</option>`);
                });
                dropdown.select2({ placeholder: "Select Code", allowClear: true, width: '100%' });
            }

            function addMedicineRow(data = {}) {
                let isFirst = $('#medicine-rows-container .medicine-row-wrapper').length === 0;
                let rowHtml = `
                    <div class="medicine-row-wrapper">
                        <div class="medicine-row">
                            <div class="row align-items-end">
                                <div class="col-md-3">
                                    <div class="form-group mb-0">
                                        <label> Medicine <span class="text-danger">*</span></label>
                                        <select class="form-control medicine-select-row" name="medicines[]" required>
                                            <option value="">Select Medicine</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-0">
                                        <label> Code</label>
                                        <select class="form-control code-select-row" name="codes[]">
                                            <option value="">Select Code</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-0">
                                        <label>Value</label>
                                        <input type="text" name="medicine_values[]" class="form-control" placeholder="Value" value="${data.value || ''}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-0">
                                        <label>Value Type</label>
                                        <input type="text" name="medicine_types[]" class="form-control" placeholder="Type" value="${data.type || ''}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="action-buttons">
                            ${isFirst 
                                ? '<button type="button" class="btn add-medicine-row"><i class="fas fa-plus"></i></button>' 
                                : '<button type="button" class="btn remove-medicine-row"><i class="fas fa-trash"></i></button>'}
                        </div>
                    </div>`;
                
                let $row = $(rowHtml);
                $('#medicine-rows-container').append($row);
                populateMedicineDropdown($row.find('.medicine-select-row'), data.medicine_id);
                populateCodeDropdown($row.find('.code-select-row'), data.code_id);
            }

            $(document).on('click', '.add-medicine-row', function() {
                addMedicineRow();
            });

            $(document).on('click', '.remove-medicine-row', function() {
                $(this).closest('.medicine-row-wrapper').remove();
            });

            // Fetch Data and Initialize
            $.when(fetchMedicines(), fetchCodes()).done(function() {
                // Fetch Patient Medicine Details
                $.ajax({
                    url: `/api/patient-medicines/${patientmedicineId}`,
                    type: 'GET',
                    headers: { "Authorization": "Bearer " + token },
                    success: function(data) {
                        $("#patient_id").val(data.patient.id).trigger("change");
                        $("#note").val(data.note);
                        
                        // Set Treatment (Wait for treatments to load if necessary, but here we assume it's already there or will be handled by the treatment fetch)
                        if (data.treatment_id) {
                             $('select[name="treatment_id"]').val(data.treatment_id).trigger("change");
                        }

                        // Populate rows
                        $('#medicine-rows-container').empty();
                        if (data.medicine_ids && data.medicine_ids.length > 0) {
                            data.medicine_ids.forEach((medId, index) => {
                                addMedicineRow({
                                    medicine_id: medId,
                                    code_id: data.codes ? data.codes[index] : '',
                                    value: data.values ? data.values[index] : '',
                                    type: data.value_types ? data.value_types[index] : ''
                                });
                            });
                        } else {
                            addMedicineRow();
                        }
                    }
                });
            });

            // Fetch Patients
            $.ajax({
                url: "/api/patientss",
                type: "GET",
                dataType: "json",
                data: { branch_id: branchId },
                headers: { "Authorization": "Bearer " + token },
                success: function(response) {
                    let patientDropdown = $('.patientDropdown');
                    patientDropdown.empty().append('<option value="">Select Patient</option>');
                    $.each(response.patients, function(index, patient) {
                        patientDropdown.append(`<option value="${patient.id}" data-treatment-id="${patient.treatment_id}">${patient.fullname}</option>`);
                    });
                    patientDropdown.select2({ placeholder: "Select Patient", allowClear: true, width: '100%' });
                }
            });

            // Handle Patient Selection
            $('.patientDropdown').on("change", function() {
                let selectedPatient = $(this).find("option:selected");
                let treatmentId = selectedPatient.data("treatment-id");
                if (treatmentId) {
                    $('select[name="treatment_id"]').val(treatmentId).trigger("change");
                }
            });

            // Fetch Treatments
            $.ajax({
                url: "/api/treatments",
                type: "GET",
                data: { branch_id: branchId },
                headers: { "Authorization": "Bearer " + token },
                success: function(data) {
                    let treatmentDropdown = $('select[name="treatment_id"]');
                    treatmentDropdown.empty().append('<option value="">Select Treatment</option>');
                    let treatmentsList = Array.isArray(data) ? data : (data.treatments || []);
                    $.each(treatmentsList, function(key, treatment) {
                        treatmentDropdown.append(`<option value="${treatment.id}">${treatment.name}</option>`);
                    });
                    treatmentDropdown.select2({ placeholder: "Select Treatment", allowClear: true, width: '100%' });
                }
            });

            // Handle Update Submission
            $('#createmedicineForm').submit(function(e) {
                e.preventDefault();
                if(!$(this).valid()) return;

                let medicines = [];
                let codes = [];
                let values = [];
                let types = [];

                $('.medicine-row').each(function() {
                    let medId = $(this).find('.medicine-select-row').val();
                    if(medId) {
                        medicines.push(medId);
                        codes.push($(this).find('.code-select-row').val());
                        values.push($(this).find('input[name="medicine_values[]"]').val());
                        types.push($(this).find('input[name="medicine_types[]"]').val());
                    }
                });

                let formData = {
                    patient_id: $("#patient_id").val(),
                    medicines: medicines,
                    codes: codes,
                    medicine_values: values,
                    medicine_types: types,
                    treatment_id: $('select[name="treatment_id"]').val(),
                    note: $("#note").val(),
                };

                $.ajax({
                    url: `/api/patient-medicines/${patientmedicineId}`,
                    type: 'PUT',
                    contentType: 'application/json',
                    headers: { "Authorization": "Bearer " + token },
                    data: JSON.stringify(formData),
                    success: function(response) {
                        $("#editpatientmedisuccessMessage").text("Medicines updated successfully!").fadeIn().delay(700).fadeOut();
                        setTimeout(function() {
                            window.location.href = "{{ route('patient_medicine.index') }}";
                        }, 2000);
                    },
                    error: function(xhr) {
                        let errorMessage = 'Update failed';
                        if (xhr.status === 422) {
                            errorMessage = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        }
                        alert(errorMessage);
                    }
                });
            });
        });
    </script>
@endsection
