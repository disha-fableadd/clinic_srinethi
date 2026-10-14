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
                    <h4 class="page-title discharge-title ">Edit Discharge Details</h4>
                </div>
                @if (app('hasPermission')(10, 'view'))
                    <div class="col-4 text-center m-b-2 view-discharge discharge-btn" style="">
                        <a href="{{ route('discharge.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                            <i class="fa fa-arrow-left m-r-5 icon3"></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    </div>
                @endif
            </div>
            <div class="row">
                <div class="col-12">
                    <form class="form-container" id="dischargeform" method="POST" action=""
                       >
                        @csrf
                        <input type="hidden" id="discharge_id" name="discharge_id">
                        <!-- Step 1 -->
                        <div class="form-step discharge-form1" id="step-1">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-user-injured icon-style"></i> Patient Name <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control select2 patientDropdown" id="patientDropdown"
                                            name="patient_id" required>
                                            <option value="">Select Patient</option>
                                        </select>


                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-calendar-day icon-style"></i> Discharge Date <span
                                                class="text-danger">*</span></label>
                                        <input type="date" class="form-control timepicker" name="discharge_date"
                                            id="discharge_date" required>
                                    </div>
                                </div>
                            </div>




                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-money-bill-wave icon-style"></i> Total Bill <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="total_bill" id="total_bill"
                                            required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-credit-card icon-style"></i> Amount Paid <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="amount_paid" id="amount_paid"
                                            required>
                                    </div>
                                </div>
                            </div>

                            
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-check-circle icon-style"></i> Payment Status <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control select2" name="payment_status" id="payment_status"
                                            required>
                                            <option value="">Select</option>
                                            <option value="paid">Paid</option>
                                            <option value="unpaid">UnPaid</option>
                                        </select>
                                        <span class="invalid-feedback">Please select the payment status.</span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-sticky-note icon-style"></i> Discharge Note <span
                                                class="text-danger">*</span></label>
                                        <textarea class="form-control" name="discharge_note" id="discharge_note" style="border-radius:10px" required></textarea>
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
                                        <select name="product_gst[]" id="product_gst" class="form-control select2" multiple>
                                            <option value="">Select GST</option>
                                        </select>
                                    </div>
                                </div>

                            </div>
                            <div id="editdischargesuccessMessage" class="alert alert-success" style="display:none;"></div>
                            {{-- <div id="editdischargeerrorMessage" class="alert alert-danger" style="display:none;"></div> --}}


                            <button type="submit" class="btn btn-primary"
                                style="padding:8px 50px;border-radius:50px; float:right">
                                Submit
                            </button>
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

    <!-- Add this in your HTML <head> -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('#payment_status').select2({
                placeholder: "Select Type",
                width: '100%'
            });
            $('#product_gst').select2({
                placeholder: "Select GST",
                width: '100%'
            });
            $('#payment_status').on('select2:open', function() {
                $('.select2-search__field').attr('placeholder', 'Search Type');
            });

            $.validator.addMethod("dateCheck", function(value, element) {
                var admit = $("input[name='admit_date']").val();
                if (!admit) return true;
                var admitDate = new Date(admit);
                var dischargeDate = new Date(value);
                return dischargeDate >= admitDate;
            }, "Discharge Date must be greater than or equal to Admit Date");

            var form = $("#dischargeform");
            form.validate({
                rules: {
                    patient_id: "required",
                    discharge_date: {
                        required: true,
                        dateCheck: true
                    },
                    total_bill: {
                        required: true,
                        number: true,
                        min: 0
                    },
                    amount_paid: {
                        required: true,
                        number: true,
                        min: 0
                    },
                    gst_option: "required",
                    "product_gst[]": {
                        required: function() {
                            return $("#gst_option").val() === "With GST";
                        }
                    },
                    payment_status: "required",
                    discharge_note: "required"
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
                    discharge_note: "Please enter the discharge note"
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
            return $.ajax({
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
                                `<option value="${tax.tax_name} (${tax.tax_rate}%)">${tax.tax_name} (${tax.tax_rate}%)</option>`
                            );
                        }
                    });
                },
                error: function(xhr) {
                    console.error("Error loading tax rates:", xhr.responseText);
                }
            });
        }
            let taxRatesPromise = fetchTaxRates();

            // Fetch Patients
            $.ajax({
                url: "/api/IPDpatientss",
                type: "GET",
                dataType: "json",
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

            let dischargeId = "{{ $discharge_id }}";
            if (dischargeId) {
                fetchDischargeData(dischargeId);
            }

            function fetchDischargeData(dischargeId) {
                $.ajax({
                    url: "{{ url('/api/patient-discharge-details') }}/" + dischargeId,
                    type: "GET",
                    headers: {
                        "Authorization": "Bearer " + token
                    },
                    success: function(response) {
                        if (response.success && response.data) {
                            let discharge = response.data;
                            $('#discharge_id').val(discharge.id);
                            $('#discharge_date').val(discharge.discharge_date);
                            $('#total_bill').val(discharge.total_bill);
                            $('#amount_paid').val(discharge.amount_paid);
                            $('#gst_option').val(discharge.gst_option || 'Without GST').trigger('change');
                            $('#payment_status').val(discharge.payment_status).trigger('change');
                            $('#discharge_note').val(discharge.discharge_note);

                            if (discharge.patient) {
                                let patientDropdown = $('#patientDropdown');
                                if (patientDropdown.find(`option[value="${discharge.patient.id}"]`).length === 0) {
                                    patientDropdown.append(
                                        `<option value="${discharge.patient.id}" selected>${discharge.patient.fullname}</option>`
                                    );
                                } else {
                                    patientDropdown.val(discharge.patient.id).trigger('change');
                                }
                            }

                            if (discharge.product_gst && discharge.product_gst.length) {
                                let selectedGst = discharge.product_gst;
                                if (typeof selectedGst[0] === 'object') {
                                    selectedGst = selectedGst.map(function(item) {
                                        return item.tax_name;
                                    });
                                } else if (typeof selectedGst === 'string') {
                                    try {
                                        selectedGst = JSON.parse(selectedGst);
                                    } catch (e) {
                                        selectedGst = selectedGst.split(',');
                                    }
                                }

                                taxRatesPromise.done(function() {
                                    $('#product_gst').val(selectedGst).trigger('change');
                                });
                            }
                        }
                    },
                    error: function(xhr) {
                        console.error("Error fetching discharge data:", xhr.responseText);
                    }
                });
            }

            form.on('submit', function(e) {
                e.preventDefault();
                if (form.valid()) {
                    let formData = new FormData(this);
                    formData.append('_method', 'PUT');

                    $.ajax({
                        url: "{{ url('/api/patient-discharge-details') }}/" + dischargeId,
                        type: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        headers: {
                            "Authorization": "Bearer " + token
                        },
                        success: function(response) {
                            $('#editdischargesuccessMessage').text(response.message || 'Discharge updated successfully').show();
                            setTimeout(function() {
                                window.location.href = "{{ route('discharge.index') }}";
                            }, 1500);
                        },
                        error: function(xhr) {
                            var errorMessage = '';
                            if (xhr.status === 422) {
                                var errors = xhr.responseJSON.errors;
                                $.each(errors, function(key, messages) {
                                    errorMessage += messages[0] + '<br>';
                                });
                            }
                            $('#editdischargeerrorMessage').html(errorMessage).show();
                        }
                    });
                }
            });
        });
    </script>
@endsection
