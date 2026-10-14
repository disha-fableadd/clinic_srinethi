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
#medicine-select {
        cursor: pointer; /* Makes the dropdown pointer */
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
/* 
    .select2-container--default .select2-selection--single {
        padding-left: 0 !important;
    }

    .form-control,
    .select2-container--default .select2-selection--single {
        border-radius: 50px !important;
        border: 1px solid rgb(207, 236, 224) !important;
        height: 45px !important;
        font-size: 14px !important;
        line-height: 45px !important;
        display: flex;
        align-items: center;
    }

    .form-control::placeholder {
        line-height: normal;
        text-align: center;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
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

    .form-group label {
        font-weight: 500;
        color: #555;
        margin-bottom: 8px;
        font-size: 14px;
        padding-left: 5px;
    }

    .action-buttons {
        padding-top: 28px;
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

        }
    }
</style>



@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="row" style="padding-top:15px">
                <div class="col-sm-8 col-8">
                    <h4 class="page-title pm-title">Add Patient Medicine</h4>
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
                        <input type="hidden" name="branch_id" id="branch_id">
                        <!-- Patient Selection -->
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <div class="d-flex align-items-center justify-content-between">


                                        <label class="mr-2"><i class="fas fa-medkit icon-style"></i> Patient <span
                                                class="text-danger">*</span></label>
                                        @if (app('hasPermission')(5, 'create'))
                                            <a href="{{ route('patients.create') }}" target="_blank"
                                                class="btn btn-primary btn-sm">
                                                <i class="fas fa-plus"></i> Add Patient
                                            </a>
                                        @endif
                                    </div>
                                    <select class="form-control select2" name="patient_id" id="patientDropdown" required>
                                        <option value="">Select Patient</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <div class="d-flex align-items-center justify-content-between">


                                        <label class="mr-2"><i class="fas fa-medkit icon-style"></i> Treatment <span
                                                class="text-danger">*</span></label>
                                        @if (app('hasPermission')(7, 'create'))
                                            <a href="{{ route('treatment.create') }}" target="_blank"
                                                class="btn btn-primary btn-sm">
                                                <i class="fas fa-plus"></i> Add Treatment
                                            </a>
                                        @endif
                                    </div>
                                    <select class="form-control select2" name="treatment_id" id="treatmentDropdown"
                                        required>
                                        <option value="">Select Treatment</option>
                                    </select>

                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <label class="mr-2"><i class="fas fa-medkit icon-style"></i> Medicines <span
                                            class="text-danger">*</span></label>
                                    @if (app('hasPermission')(4, 'create'))
                                        <a href="{{ route('medicine.create') }}" target="_blank"
                                            class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus"></i> Add Medicine
                                        </a>
                                    @endif
                                </div>
                                
                                <div id="medicine-rows-container">
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
                                                        <input type="text" name="medicine_values[]" class="form-control" placeholder="Value">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group mb-0">
                                                        <label>Value Type</label>
                                                        <input type="text" name="medicine_types[]" class="form-control" placeholder="Type">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="action-buttons">
                                            <button type="button" class="btn add-medicine-row"><i class="fas fa-plus"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="form-group">
                            <label><i class="fas fa-clipboard-list icon-style"></i> Note</label>
                            <textarea cols="7" rows="4" class="form-control" name="note" style="border-radius:10px"></textarea>
                        </div>

                        <!-- Success & Error Messages -->
                        <div id="patientmedisuccessMessage" class="alert alert-success" style="display:none;"></div>
                        {{-- <div id="patientmedierrorMessage" class="alert alert-danger" style="display:none;"></div> --}}

                        <!-- Submit Button -->
                        <div class="m-t-20 text-center">
                            <button type="submit" class="btn btn-primary submit-btn">Add Patient Medicines</button>
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
        $(document).ready(function() {
            // ✅ Initialize Select2 immediately for better UX
            $('.select2').select2({
                width: '100%'
            });

            $('.medicine-select-row').select2({
                placeholder: "Select Medicine",
                allowClear: true,
                width: '100%'
            });

            $('.code-select-row').select2({
                placeholder: "Select Code",
                allowClear: true,
                width: '100%'
            });

            let branchId = localStorage.getItem('selectedBranchId');
            if (branchId) {
                $('#branch_id').val(branchId);
            }
            let token = localStorage.getItem("authToken");
            let userId = localStorage.getItem("userId");
            let allMedicines = []; // Store medicines globally
            let allCodes = []; // Store codes globally

            // ✅ Medicines AJAX
            function fetchMedicines() {
                $.ajax({
                    url: "/api/medicine",
                    type: "GET",
                    data: {
                        branch_id: branchId
                    },
                    headers: {
                        "Authorization": "Bearer " + token
                    },
                    success: function(response) {
                        if (response.medicines && response.medicines.length > 0) {
                            allMedicines = response.medicines;
                            populateMedicineDropdown($('.medicine-select-row'));
                        } else {
                            console.warn("No medicines found for this branch.");
                        }
                    },
                    error: function(xhr) {
                        console.error("API Error:", xhr.status, xhr.responseText);
                    }
                });
            }

            // ✅ Codes AJAX
            function fetchCodes() {
                $.ajax({
                    url: "/api/codemaster",
                    type: "GET",
                    data: {
                        branch_id: branchId
                    },
                    headers: {
                        "Authorization": "Bearer " + token
                    },
                    success: function(response) {
                        if (response && response.length > 0) {
                            allCodes = response;
                            populateCodeDropdown($('.code-select-row'));
                        } else {
                            console.warn("No codes found for this branch.");
                        }
                    },
                    error: function(xhr) {
                        console.error("API Error:", xhr.status, xhr.responseText);
                    }
                });
            }

            function populateMedicineDropdown(dropdown) {
                dropdown.empty().append('<option value="">Select Medicine</option>');
                $.each(allMedicines, function(index, medicine) {
                    dropdown.append(
                        `<option value="${medicine.id}">${medicine.name}</option>`
                    );
                });
                dropdown.select2({
                    placeholder: "Select Medicine",
                    allowClear: true,
                    width: '100%'
                });

                // ✅ Add search placeholder inside Select2
                dropdown.on('select2:open', function() {
                    $('.select2-search__field').attr('placeholder', 'Search Medicine');
                });
            }

            function populateCodeDropdown(dropdown) {
                dropdown.empty().append('<option value="">Select Code</option>');
                $.each(allCodes, function(index, item) {
                    dropdown.append(
                        `<option value="${item.id}">${item.code}</option>`
                    );
                });
                dropdown.select2({
                    placeholder: "Select Code",
                    allowClear: true,
                    width: '100%'
                });
            }

            fetchMedicines();
            fetchCodes();

            // ✅ Add Row
            $(document).on('click', '.add-medicine-row', function() {
                let newRow = `
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
                                        <input type="text" name="medicine_values[]" class="form-control" placeholder="Value">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-0">
                                        <label>Value Type</label>
                                        <input type="text" name="medicine_types[]" class="form-control" placeholder="Type">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="action-buttons">
                            <button type="button" class="btn remove-medicine-row"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>`;
                $('#medicine-rows-container').append(newRow);
                populateMedicineDropdown($('.medicine-select-row').last());
                populateCodeDropdown($('.code-select-row').last());
            });

            // ✅ Remove Row
            $(document).on('click', '.remove-medicine-row', function() {
                $(this).closest('.medicine-row-wrapper').remove();
            });


            // ✅ Patients AJAX
            $.ajax({
                url: "/api/patientss",
                type: "GET",
                data: {
                    branch_id: branchId
                },
                headers: {
                    "Authorization": "Bearer " + token
                },
                success: function(response) {
                    let patientDropdown = $('#patientDropdown');
                    patientDropdown.empty().append('<option value="">Select Patient</option>');

                    if (response.patients) {
                        $.each(response.patients, function(index, patient) {
                            patientDropdown.append(
                                `<option value="${patient.id}">${patient.fullname}</option>`
                            );
                        });
                    }
                    patientDropdown.select2({
                        placeholder: "Select Patient",
                        width: '100%'
                    });
                }
            });

            // ✅ Treatments AJAX
            $.ajax({
                url: "/api/treatments",
                type: "GET",
                data: {
                    branch_id: branchId
                },
                headers: {
                    "Authorization": "Bearer " + token
                },
                success: function(data) {
                    let treatmentDropdown = $('#treatmentDropdown');
                    treatmentDropdown.empty().append('<option value="">Select Treatment</option>');
                    $.each(data.treatments, function(key, treatment) {
                        treatmentDropdown.append(
                            `<option value="${treatment.id}">${treatment.name}</option>`
                        );
                    });
                    treatmentDropdown.select2({
                        placeholder: "Select Treatment",
                        width: '100%'
                    });
                }
            });

            // ✅ Form submit
            $('#createmedicineForm').on('submit', function(e) {
                e.preventDefault();
                let formData = new FormData(this);
                formData.append("user_id", userId);

                $.ajax({
                    url: '/api/patient-medicines',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        "Authorization": "Bearer " + token
                    },
                    success: function(response) {
                        $('#patientmedisuccessMessage').text(response.message || 'Saved')
                            .show();
                        $('#createmedicineForm')[0].reset();
                        setTimeout(() => {
                            window.location.href =
                                "{{ route('patient_medicine.index') }}";
                        }, 1500);
                    },
                    error: function(xhr) {
                        console.error("Error:", xhr.responseText);
                    }
                });
            });
        });
    </script>
@endsection
