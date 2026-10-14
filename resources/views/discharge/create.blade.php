@extends('layout.app')

<style>
    .select2-container--default .select2-selection--multiple {
        box-shadow: none;
        font-size: 14px;
        min-height: 40px !important;
        border-radius: 50px !important;
        padding: 0.469rem 0.75rem;
        border-color: rgb(207, 236, 224) !important;
    }

    .select2-container--default .select2-selection--multiple {
        padding: 4px 8px;
        text-align: left;
        min-height: 38px;
    }

    .select2-container--default .select2-selection--single {
        border-radius: 50px !important;
        height: 40px !important;
        border-color: rgb(207, 236, 224) !important;
        padding-top: 4px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        text-align: left !important;
        padding-left: 12px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px !important;
        top: 1px !important;
        right: 10px !important;
    }

    .select2-selection__rendered {
        text-align: left !important;
    }


    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        background-color: rgb(207, 236, 224) !important;
        color: black !important;
        border-radius: 20px !important;
        /* padding: 2px 10px !important; */
        border: none;
        font-size: 14px;
        font-weight: normal !important;
        text-align: center;
        max-width: fit-content;
    }

    .discharge-title {
        text-align: center !important;
        padding-left: 180px;
    }

    .discharge-btn {
        padding-right: 173px !important;
        text-align: center;
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
        }

    }
</style>

@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="row" style="padding-top:15px">
                <div class="col-8">
                    <h4 class="page-title discharge-title" style="">Add Discharge Details</h4>
                </div>
                @if (app('hasPermission')(10, 'view'))
                    <div class="col-4  m-b-2 view-discharge discharge-btn">
                        <a href="{{ route('discharge.index') }}" class="btn btn-primary btn-rounded btn-hdr ">
                            <i class="fa fa-arrow-left m-r-5 icon3"></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    </div>
                @endif
            </div>
            <div class="row">
                <div class="col-12">
                    <form class="form-container" id="dischargeform" method="POST" action="">
                        @csrf
                        <input type="hidden" name="branch_id" id="branch_id">
                        <!-- Step 1 -->
                        <div class="form-step discharge-form1" id="step-1">
                            <div class="row">
                                <div class="col-6">
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
                                        <select class="form-control select2" name="patient_id" id="patientDropdown"
                                            required>
                                            <option value="">Select Patient</option>
                                        </select>

                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-calendar-day icon-style"></i> Discharge Date <span
                                                class="text-danger">*</span></label>
                                        <input type="date" class="form-control timepicker" name="discharge_date"
                                            id="discharge_date" min="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-money-bill-wave icon-style"></i> Total Bill <span
                                                class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="total_bill" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-credit-card icon-style"></i> Amount Paid <span
                                                class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="amount_paid" required>
                                    </div>
                                </div>
                            </div>

                            

                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-check-circle icon-style"></i> Payment Status <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control select2" id="type" name="payment_status" required>
                                            <option value="">Select</option>
                                            <option value="paid">Paid</option>
                                            <option value="unpaid">UnPaid</option>
                                        </select>
                                        <span class="invalid-feedback">Please select the payment status.</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-sticky-note icon-style"></i> Discharge Note <span
                                                class="text-danger">*</span></label>
                                        <textarea class="form-control" name="discharge_note" style="border-radius:10px" required></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <!-- GST Option -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-percent icon-style"></i> GST Option <span
                                                class="text-danger">*</span></label>
                                        <select name="gst_option" id="gst_option" class="form-control select2" required>
                                            <option value="Without GST">Without GST</option>
                                            <option value="With GST">With GST</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Product GST -->
                                <div class="col-md-6" id="product_gst_div" style="display: none;">
                                    <div class="form-group">
                                        <label><i class="fas fa-file-invoice-dollar icon-style"></i> Product GST <span
                                                class="text-danger">*</span></label>
                                        <select name="product_gst[]" id="product_gst" class="form-control select2"
                                            multiple>
                                            <option value="">Select GST</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div id="dischargesuccessMessage" class="alert alert-success" style="display:none;"></div>
                            {{-- <div id="dischargeerrorMessage" class="alert alert-danger" style="display:none;"></div> --}}


                            <button type="submit" class="btn btn-primary d-block m-auto"
                                style="padding:8px 50px;border-radius:50px; ">
                                Submit
                            </button>
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

        $('#type').on('select2:open', function() {
            $('.select2-search__field').attr('placeholder', 'Search Type');
        });

        $('#gst_option').on('change', function() {
            if ($(this).val() === 'With GST') {
                $('#product_gst_div').show();
                $('#product_gst').attr('required', true);
            } else {
                $('#product_gst_div').hide();
                $('#product_gst').attr('required', false).val(null).trigger('change');
            }
        });

        function fetchTaxRates() {
            let branchId = localStorage.getItem('selectedBranchId');
            $.ajax({
                url: '/api/tax-rates',
                type: 'GET',
                data: {
                    branch_id: branchId
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                },
                success: function(data) {
                    let gstDropdown = $('#product_gst');
                    gstDropdown.empty().append('<option value="">Select GST</option>');
                    data.forEach(function(tax) {
                        if (tax.status === 'active') {
                            gstDropdown.append(
                                `<option value="${tax.tax_name}" data-rate="${tax.tax_rate}">${tax.tax_name} (${tax.tax_rate}%)</option>`
                            );
                        }
                    });
                },
                error: function(xhr) {
                    console.error("Error loading tax rates:", xhr.responseText);
                }
            });
        }

        $.validator.addMethod("dateCheck", function(value, element) {
            var admit = $("input[name='admit_date']").val();
            if (!admit) return true; // Skip if admit_date is empty, let 'required' handle it
            var admitDate = new Date(admit);
            var dischargeDate = new Date(value);
            return dischargeDate >= admitDate;
        }, "Discharge Date must be greater than or equal to Admit Date");


        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                width: '100%'
            });

            fetchTaxRates();

            // Initialize form validation
            var form = $("#dischargeform");
            form.validate({
                // Validation rules for the entire form
                rules: {
                    patient_id: "required",


                    discharge_date: {
                        required: true,
                        dateCheck: true
                    },

                    total_bill: {
                        required: true,
                        number: true,
                        min: 0 // <--- ensure non-negative
                    },
                    amount_paid: {
                        required: true,
                        number: true,
                        min: 0 // <--- ensure non-negative
                    },
                    gst_option: "required",
                    "product_gst[]": {
                        required: function() {
                            return $("#gst_option").val() === "With GST";
                        }
                    },
                    payment_status: "required"
                },

                messages: {
                    patient_id: "Please select a patient",
                    discharge_date: "Discharge Date must be greater than or equal to Admit Date",

                    total_bill: {
                        required: "Please enter the total bill",
                        number: "Please enter a valid number",
                        min: "Total bill cannot be negative"
                    },
                    amount_paid: {
                        required: "Please enter the amount paid",
                        number: "Please enter a valid number",
                        min: "Amount paid cannot be negative"
                    },
                    gst_option: "Please select a GST option",
                    "product_gst[]": "Please select a product GST",
                    payment_status: "Please select the payment status",

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

            // Function to validate the current step
            function validateStep(step) {
                var valid = true;
                step.find('input, select, textarea').each(function() {
                    if (!$(this).valid()) {
                        valid = false;
                    }
                });
                return valid;
            }






            // Fetch Patients
            $.ajax({
                url: "/api/IPDpatientss",
                type: "GET",
                dataType: "json",
                success: function(response) {
                    let patientDropdown = $('#patientDropdown');

                    if (patientDropdown.length === 0) {
                        console.error("Dropdown not found! Check your HTML.");
                        return;
                    }

                    patientDropdown.empty().append('<option value="">Select Patient</option>');

                    if (!response.patients || response.patients.length === 0) {
                        console.warn("No patients found for this user.");
                        return;
                    }

                    $.each(response.patients, function(index, patient) {
                        patientDropdown.append(
                            `<option value="${patient.id}">${patient.fullname}</option>`
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

            let branchId = localStorage.getItem('selectedBranchId');


            // $.ajax({
            //     url: "/api/patientss",
            //     type: "GET",
            //     data: {
            //         branch_id: branchId
            //     },
            //     dataType: "json",
            //     xhrFields: {
            //         withCredentials: true // ✅ send session cookie for Sanctum
            //     },
            //     success: function(response) {
            //         let patientDropdown = $('#patientDropdown');

            //         if (patientDropdown.length === 0) {
            //             console.error("Dropdown not found! Check your HTML.");
            //             return;
            //         }

            //         patientDropdown.empty().append('<option value="">Select Patient</option>');

            //         if (!response.patients || response.patients.length === 0) {
            //             console.warn("No patients found for this branch.");
            //             return;
            //         }

            //         $.each(response.patients, function(index, patient) {
            //             const capitalizedName = patient.fullname.charAt(0).toUpperCase() +
            //                 patient.fullname.slice(1);

            //             patientDropdown.append(
            //                 `<option value="${patient.id}" data-treatment-id="${patient.treatment_id}">
            //         ${capitalizedName}
            //     </option>`
            //             );
            //         });

            //         patientDropdown.select2({
            //             placeholder: "Select Patient",
            //             allowClear: true,
            //             width: '100%'
            //         });

            //         patientDropdown.on('select2:open', function() {
            //             $('.select2-search__field').attr('placeholder', 'Search Patient');
            //         });
            //     },
            //     error: function(xhr) {
            //         console.error("API Error:", xhr.status, xhr.responseText);
            //     }
            // });

            // Handle form submission
            form.on('submit', function(e) {
                e.preventDefault();
                if (form.valid()) {
                    let totalBill = parseFloat($("input[name='total_bill']").val()) || 0;
                    let gstDetails = null;

                    if ($('#gst_option').val() === 'With GST') {
                        gstDetails = [];
                        $('#product_gst option:selected').each(function() {
                            let taxName = $(this).val();
                            let taxRate = parseFloat($(this).data('rate')) || 0;
                            let taxAmount = (totalBill * taxRate) / 100;
                            gstDetails.push({
                                tax_name: taxName,
                                tax_rate: taxRate.toFixed(2),
                                tax_amount: taxAmount
                            });
                        });
                    }

                    let formData = {
                        branch_id: $("#branch_id").val(),
                        patient_id: $("#patientDropdown").val(),
                        discharge_date: $("#discharge_date").val(),
                        total_bill: totalBill,
                        amount_paid: $("input[name='amount_paid']").val(),
                        gst_option: $("#gst_option").val(),
                        product_gst: gstDetails,
                        payment_status: $("#type").val(),
                        discharge_note: $("textarea[name='discharge_note']").val(),
                    };

                    $.ajax({
                        url: "{{ url('/api/patient-discharge-details') }}",
                        type: "POST",
                        data: JSON.stringify(formData),
                        contentType: "application/json",
                        headers: {
                            "Authorization": "Bearer " + token,
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content'),
                            "Accept": "application/json"
                        },
                        success: function(response) {
                            $('#dischargesuccessMessage').text(response.message ||
                                'Discharge created successfully').show();
                            $('#dischargeform')[0].reset();

                            setTimeout(function() {
                                window.location.href = "{{ route('discharge.index') }}";
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
                            $('#dischargeerrorMessage').html(errorMessage).show();
                        }
                    });
                }
            });
        });
    </script>
@endsection
