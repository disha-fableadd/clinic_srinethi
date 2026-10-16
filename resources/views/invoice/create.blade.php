@extends('layout.app')
@section('content')
<style>
    @media screen and (max-width: 767px) {
        .page-title {
            font-size: 19px !important;
            /* padding-left: 10px !important; */
            text-align: left !important;
            padding-top: 6px !important;
        }
    }
    /* Invoice item row boxed card styling (like Purchase page) */
    .invoice-item-row {
        background: #ffffff;
        border: 1.5px solid #cfece0;
        border-radius: 12px;
        padding: 16px 14px 14px 14px;
        margin-bottom: 16px !important;
        position: relative;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03);
        transition: all 0.2s ease-in-out;
    }
    .invoice-item-row:hover {
        border-color: #cfece0;
        box-shadow: 0 3px 8px rgba(246, 148, 96, 0.12);
    }
    .invoice-item-row .form-control {
        height: 40px !important;
        border-radius: 50px !important;
        font-size: 14px !important;
        border: 1px solid rgb(207, 236, 224) !important;
    }
    .invoice-item-row .select2-container .select2-selection--single {
        height: 40px !important;
        border-radius: 25px !important;
        border: 1px solid rgb(207, 236, 224) !important;
        display: flex;
        align-items: center;
    }
    .invoice-item-row .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: 38px !important;
        padding-left: 12px !important;
    }
    .invoice-item-row .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px !important;
        right: 10px !important;
    }
    .invoice-item-row label {
        font-size: 13px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 6px;
        display: block;
    }
    .row-action-col {
        display: flex;
        align-items: flex-end;
        justify-content: flex-end;
        padding-bottom: 0px;
    }
    .btn-row-action {
        width: 40px;
        height: 40px;
        min-width: 40px;
        border-radius: 50px !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        padding: 0 !important;
        border: none;
        cursor: pointer;
        transition: transform 0.15s, opacity 0.15s;
    }
    .btn-row-action:hover {
        transform: scale(1.04);
        opacity: 0.92;
    }
    .btn-row-add {
        background-color: #cfece0 !important;
        color: #106346 !important;
    }
    .btn-row-remove {
        background-color: #f89884 !important;
        color: #ffffff !important;
    }

    /* Mobile Responsive Optimizations */
    @media (max-width: 767px) {
        .invoice-item-row {
            padding: 14px 12px;
            margin-bottom: 14px !important;
        }
        .invoice-item-row .row > [class*="col-"] {
            margin-bottom: 10px;
        }
        .invoice-item-row .item-line-1,
        .invoice-item-row .item-line-2 {
            margin-top: 0 !important;
        }
        .invoice-item-row .row-action-col {
            margin-top: 6px;
            margin-bottom: 4px;
            justify-content: flex-end;
            width: 100%;
        }
        .invoice-item-row .btn-row-action {
            width: 44px;
            height: 44px;
        }
        .invoice-item-row .type-service-col,
        .invoice-item-row .price-col,
        .invoice-item-row .amount-col,
        .invoice-item-row .qty-col,
        .invoice-item-row .gst-option-col,
        .invoice-item-row .gst-pct-col,
        .invoice-item-row .gst-amt-col {
            width: 100% !important;
            max-width: 100% !important;
            flex: 0 0 100% !important;
        }
    }
</style>
@php
    $gstEnabled = \App\Models\Setting::getValue('gst_status', 'on') === 'on';
@endphp

    <div class="page-wrapper">
        <div class="content">
            <div class="row" style="padding-top:15px">
                <div class="col-6">
                    <h4 class="page-title">Invoice</h4>
                </div>
                @if(app('hasPermission')(18, 'view'))
                    <div class="col-6  m-b-2 eye-btn" style="text-align:end">
                        <a href="{{ route('invoice.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                            <i class="fa fa-arrow-left m-r-5 icon3"></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    </div>
                @endif
            </div>
            <div class="row">
                <div class="col-12">

                    <form id="invoiceForm" class="form-container all-form" novalidate>
                        @csrf
                        <input type="hidden" name="branch_id" id="branch_id">
                        <input type="hidden" name="gst_enabled" id="gst_enabled" value="{{ $gstEnabled ? 1 : 0 }}">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <label class="mb-0">Patient <span class="text-danger">*</span></label>
                                    @if (app('hasPermission')(5, 'create'))
                                        <button type="button" class="btn btn-sm quick-modal-add-sm-btn"
                                            data-bs-toggle="modal" data-bs-target="#quickAddPatientModal"
                                            data-toggle="modal" data-target="#quickAddPatientModal">
                                            <i class="fas fa-plus"></i> Add
                                        </button>
                                    @endif
                                </div>
                                <select class="form-control select2" name="patient_id" id="patientDropdown" required>
                                    <option value="">Select Patient</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Type <span class="text-danger">*</span></label>
                                <select name="invoice_type" class="form-control select2" id="invoiceType" required>
                                    <option value="">Select Type</option>
                                    {{-- <option value="service">Service</option> --}}
                                    <option value="treatment" selected>Treatment</option>
                                    {{-- <option value="medicine">Medicine</option> --}}
                                    <option value="appointment">Appointment</option>
                                    {{-- <option value="therapy">Therapy</option> --}}
                                    {{-- <option value="discharge">Discharge</option> --}}
                                </select>
                            </div>
                            
                            <div class="col-md-4 mb-3" id="dateSection">
                                <label>Date <span class="text-danger">*</span></label>
                                <input type="date" name="date1" class="form-control" id="date1">
                            </div>

                            <script>
                                document.addEventListener("DOMContentLoaded", function () {
                                    const today = new Date().toISOString().split('T')[0];
                                    const dateInput = document.getElementById('date1');
                                    dateInput.value = today;
                                    dateInput.min = today;
                                    dateInput.max = today;
                                });
                            </script>
                        </div>

                        <div id="dynamicFieldsWrapper">
                            <div class="invoice-item-row">
                                <!-- Line 1 of the card -->
                                <div class="row align-items-end item-line-1">
                                    <div class="col-md-3 type-service-col">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <label class="mb-0">Type/Service</label>
                                            @if (app('hasPermission')(7, 'create'))
                                                <button type="button" class="btn btn-sm quick-modal-add-sm-btn quick-add-treatment-btn"
                                                    data-bs-toggle="modal" data-bs-target="#quickAddTreatmentModal"
                                                    data-toggle="modal" data-target="#quickAddTreatmentModal">
                                                    <i class="fas fa-plus"></i> 
                                                </button>
                                            @endif
                                        </div>
                                        <select name="items[0][type_service]" class="form-control type-service-select select2">
                                            <option value="">Select</option>
                                            {{-- options will be added dynamically --}}
                                        </select>
                                    </div>
                                    <div class="col-md-2 qty-col">
                                        <label>Qty</label>
                                        <input type="number" name="items[0][quantity]" class="form-control quantity" min="1"
                                            value="1">
                                        <div class="text-danger error-message quantity-error"></div>
                                    </div>
                                    <div class="col-md-2 price-col">
                                        <label>Price</label>
                                        <input type="number" name="items[0][price]" class="form-control price" min="0" value="0"
                                            required step="0.01">
                                        <div class="text-danger error-message price-error"></div>
                                    </div>
                                    <div class="col-md-2 amount-col">
                                        <label class="amount-label-default">Amount</label>
                                        <label class="amount-label-total-bill" style="display:none;"><i class="fas fa-money-bill-wave icon-style"></i> Total Bill <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" min="0" name="items[0][total]" class="form-control amount invoice-amount-input" value="0.00">
                                    </div>
                                    @if($gstEnabled)
                                    <div class="col-md-2 gst-option-col">
                                        <label>GST Option</label>
                                        <select class="form-control gst-option-select">
                                            <option value="Without GST" selected>Without GST</option>
                                            <option value="With GST">With GST</option>
                                        </select>
                                        <input type="hidden" name="items[0][gst_option]" class="gst-option-hidden" value="Without GST">
                                    </div>
                                    @else
                                    <input type="hidden" name="items[0][gst_option]" class="gst-option-hidden" value="Without GST">
                                    @endif
                                    <div class="col-md-1 row-action-col">
                                        <button type="button" class="btn btn-row-action btn-row-add" title="Add Row">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Line 2 of the card (Appears when With GST is selected) -->
                                @if($gstEnabled)
                                <div class="row align-items-end mt-2 item-line-2" style="display: none;">
                                    <div class="col-md-3 gst-pct-col">
                                        <label>GST (%)</label>
                                        <input type="number" step="0.01" min="0" max="100" name="items[0][gst_percent_display]" class="form-control gst-percent-display" placeholder="0">
                                    </div>
                                    <div class="col-md-3 gst-amt-col">
                                        <label>GST Amount</label>
                                        <input type="number" step="0.01" min="0" name="items[0][gst_amount_display]" class="form-control gst-amount-display" placeholder="0">
                                    </div>
                                </div>
                                @endif
                                <input type="hidden" name="items[0][product_gst]" class="product-gst-json" value="">
                            </div>
                        </div>



                        <div class="row mt-3">
                        <div class="col-md-4 mb-3">
                                <label>Instruction</label>
                                <textarea name="instruction" class="form-control" rows="1"></textarea>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Discount Type</label>
                                <select name="discount_type" class="form-control select2" id="discount-type">
                                    <option value="">Select Type</option>
                                    <option value="fixed_amount">Fixed Amount</option>
                                    <option value="percentage">Percentage</option>
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label>Discount</label>
                                <input type="number" class="form-control discount" name="discount">
                                <div class="text-danger error-message discount-error"></div>
                            </div>
                        </div>

                        <script>
                            $(document).ready(function () {
                                $('#dynamicFieldsWrapper').on('input', '.quantity, .price', function () {
                                    let $input = $(this);
                                    let value = parseFloat($input.val());
                                    let isQuantity = $input.hasClass('quantity');
                                    let $errorDiv = isQuantity ? $input.closest('.col-md-3').find(
                                        '.quantity-error') : $input.closest('.col-md-3').find(
                                            '.price-error');

                                    $errorDiv.text('');
                                    if (value < 0) {
                                        $errorDiv.text('Value cannot be negative');
                                        $input.val('');
                                    }
                                });

                                // Validate discount
                                $('.discount').on('input', function () {
                                    let $input = $(this);
                                    let value = parseFloat($input.val());
                                    let $errorDiv = $('.discount-error');

                                    $errorDiv.text('');
                                    if (value < 0) {
                                        $errorDiv.text('Value cannot be negative');
                                        $input.val('');
                                    }
                                });
                            });
                        </script>


                        <div class="row mt-3 justify-content-between">
                            <div class="col-md-4">
                                <label>Payment Type <span class="text-danger">*</span></label>
                                <select name="payment_type" class="form-control select2" id="payment-type" required>
                                    <option value="">-- Select Payment Type --</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Online">Online</option>

                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>Payment Status <span class="text-danger">*</span></label>
                                <select name="payment_status" class="form-control select2" id="payment-status" required>
                                    <option value="">-- Select Payment Status --</option>
                                    <option value="Paid">Paid</option>
                                    <option value="Pending">Pending</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label>Grand Total (₹)</label>
                                <input type="number" name="grandTotal" class="form-control" id="grandTotal" readonly
                                    value="0.00">
                            </div>
                        </div>




                        <button type="submit" class="btn btn-primary d-flex ml-auto mt-5">Submit</button>
                    </form>

                    <div id="invoicesuccessMessage" class="alert alert-success success-message" style="display:none;"></div>
                    {{-- <div id="invoiceerrorMessage" class="alert alert-danger error-message" style="display:none;"></div>
                    --}}

                </div>
            </div>

        </div>
    </div>

    @include('components.quick-add-modals')


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Invoice Details Section -->


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let storedBranchId = localStorage.getItem('selectedBranchId');
            if (storedBranchId) {
                document.getElementById('branch_id').value = storedBranchId;
            }

            console.log('branch is ', storedBranchId);
            
        });

        function toggleRemoveButton() {
            const rowCount = $('.invoice-item-row').length;
            const invoiceType = $('#invoiceType').val();

            // Appointment / Discharge → never show remove
            if (invoiceType === 'appointment' || invoiceType === 'discharge') {
                $('#removeRowBtn').hide();
                return;
            }

            // Show remove ONLY when more than 1 row
            if (rowCount > 1) {
                $('#removeRowBtn').show();
            } else {
                $('#removeRowBtn').hide();
            }
        }


        $('#invoiceType').select2({
            placeholder: "Select Type",
            width: '100%'
        });
        $('#invoiceType').on('select2:open', function () {
            $('.select2-search__field').attr('placeholder', 'Search Type');
        });

        $('#discount-type').select2({
            placeholder: "Select Type",
            width: '100%'
        });
        $('#discount-type').on('select2:open', function () {
            $('.select2-search__field').attr('placeholder', 'Search Type');
        });

        $('#payment-type').select2({
            placeholder: "Select Type",
            width: '100%'
        });
        $('#payment-type').on('select2:open', function () {
            $('.select2-search__field').attr('placeholder', 'Search Type');
        });

        $('#payment-status').select2({
            placeholder: "Select Type",
            width: '100%'
        });
        $('#payment-status').on('select2:open', function () {
            $('.select2-search__field').attr('placeholder', 'Search Type');
        });

        function getBranchId() {
            let b = localStorage.getItem('selectedBranchId');
            return (b && b !== 'null' && b !== 'undefined' && b !== '') ? b : '1';
        }

        $(document).ready(function () {

            let branchId = getBranchId();
            $('#branch_id').val(branchId);

            function loadPatients(selectedId = null) {
                let currentBranchId = getBranchId();
                $.ajax({
                    url: "/api/patientss",
                    type: "GET",
                    dataType: "json",
                    data: { branch_id: currentBranchId },
                    success: function (response) {
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

                        $.each(response.patients, function (index, patient) {
                            patientDropdown.append(
                                `<option value="${patient.id}" data-treatment-id="${patient.treatment_id}">${patient.fullname}</option>`
                            );
                        });

                        patientDropdown.select2({
                            placeholder: "Select Patient",
                            allowClear: true,
                            width: '100%'
                        });

                        patientDropdown.on('select2:open', function () {
                            $('.select2-search__field').attr('placeholder', 'Search Patient');
                        });

                        if (selectedId) {
                            patientDropdown.val(selectedId).trigger('change');
                        }
                    },
                    error: function (xhr) {
                        console.error("API Error:", xhr.status, xhr.responseText);
                    }
                });
            }
            window.loadPatients = loadPatients;
            loadPatients();





            function updateRowGstVisibility($row) {
                const selectedType = $('#invoiceType').val();
                const $typeService = $row.find('.type-service-col');
                const $quantity = $row.find('.qty-col');
                const $priceCol = $row.find('.price-col');
                const $amountCol = $row.find('.amount-col');
                const $amount = $row.find('.amount');
                const $gstOptCol = $row.find('.gst-option-col');
                const $gstPctCol = $row.find('.gst-pct-col');
                const $gstAmtCol = $row.find('.gst-amt-col');
                const gstOptVal = $row.find('.gst-option-select').val();

                const $actionCol = $row.find('.row-action-col');
                const rowCount = $('.invoice-item-row').length;

                const isGstEnabled = @json($gstEnabled);

                if (!isGstEnabled) {
                    $gstOptCol.hide();
                    $gstPctCol.hide();
                    $gstAmtCol.hide();
                    $row.find('.item-line-2').hide();
                }

                if (selectedType === 'appointment') {
                    $typeService.hide();
                    $quantity.hide();
                    $gstOptCol.hide();
                    $gstPctCol.hide();
                    $gstAmtCol.hide();
                    $actionCol.hide();
                    $row.find('.item-line-2').hide();
                    $priceCol.removeClass('col-md-1 col-md-2 col-md-3 col-md-4 col-md-5 col-md-6').addClass('col-md-6');
                    $amountCol.removeClass('col-md-1 col-md-2 col-md-3 col-md-4 col-md-5 col-md-6').addClass('col-md-6');
                    $amountCol.find('.amount-label-default').show();
                    $amountCol.find('.amount-label-total-bill').hide();
                    $amount.removeClass('invoice-total-bill');
                } else if (selectedType === 'discharge') {
                    $typeService.hide();
                    $quantity.hide();
                    $actionCol.hide();
                    $amountCol.find('.amount-label-default').hide();
                    $amountCol.find('.amount-label-total-bill').show();
                    $amount.addClass('invoice-total-bill');

                    if (!isGstEnabled) {
                        $gstOptCol.hide();
                        $gstPctCol.hide();
                        $gstAmtCol.hide();
                        $row.find('.item-line-2').hide();
                        $priceCol.removeClass('col-md-1 col-md-2 col-md-3 col-md-4 col-md-6').addClass('col-md-6');
                        $amountCol.removeClass('col-md-1 col-md-2 col-md-3 col-md-4 col-md-6').addClass('col-md-6');
                    } else if (gstOptVal === 'With GST') {
                        $gstOptCol.show();
                        $row.find('.item-line-2').show();
                        $gstPctCol.show().removeClass('col-md-1 col-md-2 col-md-3 col-md-4 col-md-6').addClass('col-md-3');
                        $gstAmtCol.show().removeClass('col-md-1 col-md-2 col-md-3 col-md-4 col-md-6').addClass('col-md-3');
                        $priceCol.removeClass('col-md-1 col-md-2 col-md-3 col-md-4 col-md-6').addClass('col-md-4');
                        $amountCol.removeClass('col-md-1 col-md-2 col-md-3 col-md-4 col-md-6').addClass('col-md-4');
                        $gstOptCol.removeClass('col-md-1 col-md-2 col-md-3 col-md-4 col-md-6').addClass('col-md-4');
                    } else {
                        $gstOptCol.show();
                        $row.find('.item-line-2').hide();
                        $gstPctCol.hide();
                        $gstAmtCol.hide();
                        $priceCol.removeClass('col-md-1 col-md-2 col-md-3 col-md-4 col-md-6').addClass('col-md-4');
                        $amountCol.removeClass('col-md-1 col-md-2 col-md-3 col-md-4 col-md-6').addClass('col-md-4');
                        $gstOptCol.removeClass('col-md-1 col-md-2 col-md-3 col-md-4 col-md-6').addClass('col-md-4');
                    }
                } else {
                    // Standard multi-row types: treatment, medicine, service, therapy
                    $typeService.show();
                    $quantity.show();
                    $actionCol.show();
                    $amountCol.find('.amount-label-default').show();
                    $amountCol.find('.amount-label-total-bill').hide();
                    $amount.removeClass('invoice-total-bill');

                    if (!isGstEnabled) {
                        $gstOptCol.hide();
                        $row.find('.item-line-2').hide();
                        $gstPctCol.hide();
                        $gstAmtCol.hide();
                        // 12 cols: Type/Service (4) + Qty (2) + Price (2) + Amount (3) + Action (1) = 12 cols
                        $typeService.removeClass('col-md-1 col-md-2 col-md-3 col-md-4 col-md-5 col-md-6').addClass('col-md-4');
                        $quantity.removeClass('col-md-1 col-md-2 col-md-3 col-md-4 col-md-5 col-md-6').addClass('col-md-2');
                        $priceCol.removeClass('col-md-1 col-md-2 col-md-3 col-md-4 col-md-5 col-md-6').addClass('col-md-2');
                        $amountCol.removeClass('col-md-1 col-md-2 col-md-3 col-md-4 col-md-5 col-md-6').addClass('col-md-3');
                        $actionCol.removeClass('col-md-1 col-md-2 col-md-3 col-md-4 col-md-5 col-md-6').addClass('col-md-1');
                    } else {
                        $gstOptCol.show();
                        $row.find('.item-line-2').show();

                        // Line 1: Type/Service (3) + Qty (2) + Price (2) + Amount (2) + GST Option (2) + Action (1) = 12 cols
                        $typeService.removeClass('col-md-1 col-md-2 col-md-3 col-md-4 col-md-5 col-md-6').addClass('col-md-3');
                        $quantity.removeClass('col-md-1 col-md-2 col-md-3 col-md-4 col-md-5 col-md-6').addClass('col-md-2');
                        $priceCol.removeClass('col-md-1 col-md-2 col-md-3 col-md-4 col-md-5 col-md-6').addClass('col-md-2');
                        $amountCol.removeClass('col-md-1 col-md-2 col-md-3 col-md-4 col-md-5 col-md-6').addClass('col-md-2');
                        $gstOptCol.removeClass('col-md-1 col-md-2 col-md-3 col-md-4 col-md-5 col-md-6').addClass('col-md-2');
                        $actionCol.removeClass('col-md-1 col-md-2 col-md-3 col-md-4 col-md-5 col-md-6').addClass('col-md-1');

                        // Line 2: GST (%) (3) + GST Amt (3) - only displayed when With GST is selected
                        if (gstOptVal === 'With GST') {
                            $row.find('.item-line-2').show();
                            $gstPctCol.show().removeClass('col-md-1 col-md-2 col-md-3 col-md-4 col-md-6').addClass('col-md-3');
                            $gstAmtCol.show().removeClass('col-md-1 col-md-2 col-md-3 col-md-4 col-md-6').addClass('col-md-3');
                        } else {
                            $row.find('.item-line-2').hide();
                            $gstPctCol.hide();
                            $gstAmtCol.hide();
                        }
                    }
                }
            }
            window.updateRowGstVisibility = updateRowGstVisibility;

            function toggleFieldsByInvoiceType() {
                const selectedType = $('#invoiceType').val();

                if (selectedType === 'appointment' || selectedType === 'discharge') {
                    $('#addRowBtn, #removeRowBtn').hide();
                } else {
                    $('#addRowBtn').show();
                    toggleRemoveButton();
                }

                $('.invoice-item-row').each(function () {
                    updateRowGstVisibility($(this));
                });

                if (typeof window.updateRowActionButtons === 'function') {
                    window.updateRowActionButtons();
                }

                // Toggle Quick Add Treatment button (only visible when type === 'treatment')
                if (selectedType === 'treatment') {
                    $('.quick-add-treatment-btn').show();
                } else {
                    $('.quick-add-treatment-btn').hide();
                }
            }

            // Initial load
            toggleFieldsByInvoiceType();

            // Load discharge for selected patient when type = Discharge
            function loadDischargeForPatient() {
                const selectedType = $('#invoiceType').val();
                const patientId = $('#patientDropdown').val();
                const branchId = localStorage.getItem('selectedBranchId');
                if (selectedType !== 'discharge' || !patientId || !branchId) return;

                $.ajax({
                    url: '/api/patient-discharge-details/by-patient',
                    type: 'GET',
                    data: { patient_id: patientId, branch_id: branchId },
                    dataType: 'json',
                    success: function (res) {
                        const row = $('.invoice-item-row').first();
                        if (!res.discharge) {
                            row.find('.price').val('');
                            row.find('.amount').val('0.00');
                            row.find('.quantity').val(1);
                            row.find('.gst-option-select').val('Without GST');
                            row.find('.gst-option-hidden').val('Without GST');
                            row.find('.gst-percent-display').val('');
                            row.find('.gst-amount-display').val('');
                            row.find('.product-gst-json').val('');
                            updateGrandTotal();
                            return;
                        }
                        const d = res.discharge;
                        const totalBill = parseFloat(d.total_bill) || 0;
                        row.find('.quantity').val(1);
                        row.find('.price').val(totalBill.toFixed(2));
                        row.find('.amount').val(totalBill.toFixed(2));
                        const gstOption = (d.gst_option || '').replace(/_/g, ' ');
                        if ((gstOption === 'With GST' || gstOption === 'with gst') && d.product_gst && (Array.isArray(d.product_gst) ? d.product_gst.length : true)) {
                            row.find('.gst-option-select').val('With GST');
                            row.find('.gst-option-hidden').val('With GST');
                            const productGst = Array.isArray(d.product_gst) ? d.product_gst : [d.product_gst];
                            window.updateRowGstDisplay?.(row, productGst, totalBill);
                        } else {
                            row.find('.gst-option-select').val('Without GST');
                            row.find('.gst-option-hidden').val('Without GST');
                            window.updateRowGstDisplay?.(row, null);
                        }
                        updateGrandTotal();
                    },
                    error: function () {
                        $('.invoice-item-row').first().find('.price').val('');
                        $('.invoice-item-row').first().find('.amount').val('0.00');
                        updateGrandTotal();
                    }
                });
            }

            $('#patientDropdown').on('change', function () {
                if ($('#invoiceType').val() === 'discharge') {
                    loadDischargeForPatient();
                }
            });

            // On change
            $('#invoiceType').on('change', function () {
                $('.invoice-item-row').each(function () {
                    $(this).find('.type-service-select').val('');
                    $(this).find('.price').val('');
                    $(this).find('.amount').val('0.00');
                    $(this).find('.quantity').val(1);
                    $(this).find('.gst-option-select').val('Without GST');
                    $(this).find('.gst-option-hidden').val('Without GST');
                    $(this).find('.gst-percent-display').val('');
                    $(this).find('.gst-amount-display').val('');
                    $(this).find('.product-gst-json').val('');
                });
                toggleFieldsByInvoiceType();
                if ($(this).val() === 'discharge') {
                    loadDischargeForPatient();
                }
                updateGrandTotal();
            });
        });


        
    </script>


    <script>
        const services = @json($services);
        let treatments = @json($treatments);
        const medicines = @json($medicines);
        const appointments = @json($appointments);
        const therapies = @json($therapies ?? []);

        let lastQuickAddTreatmentRow = null;
        $(document).on('click', '.quick-add-treatment-btn', function () {
            lastQuickAddTreatmentRow = $(this).closest('.invoice-item-row');
        });

        function loadTreatments(selectedId = null, targetRow = null) {
            let branchId = (typeof getBranchId === 'function') ? getBranchId() : (localStorage.getItem('selectedBranchId') || '1');
            const $target = (targetRow && targetRow.length) ? targetRow : (lastQuickAddTreatmentRow && lastQuickAddTreatmentRow.length ? lastQuickAddTreatmentRow : null);
            
            return $.ajax({
                url: "/api/treatments",
                type: "GET",
                data: { branch_id: branchId },
                xhrFields: { withCredentials: true },
                success: function(data) {
                    treatments = data.treatments || [];
                    const currentType = $('#invoiceType').val();
                    if (currentType === 'treatment') {
                        const allRows = $('#dynamicFieldsWrapper .invoice-item-row');
                        
                        // First, if we have a target row and a newly created treatment, assign it to that target row
                        if (selectedId && $target && $target.length) {
                            populateTypeServiceSelect($target, 'treatment');
                            $target.find('.type-service-select').val(selectedId).trigger('change');
                        } else if (selectedId && allRows.length > 0) {
                            // Fallback: if no target row tracked, find first empty row or default to row 0
                            let $emptyRow = allRows.filter(function() {
                                return !$(this).find('.type-service-select').val();
                            }).first();
                            if (!$emptyRow.length) {
                                $emptyRow = allRows.first();
                            }
                            populateTypeServiceSelect($emptyRow, 'treatment');
                            $emptyRow.find('.type-service-select').val(selectedId).trigger('change');
                        }

                        // Then refresh remaining rows so the new item shows in their dropdown options as well
                        allRows.each(function() {
                            const $r = $(this);
                            if ($target && $r.is($target)) {
                                return; // already populated and selected
                            }
                            populateTypeServiceSelect($r, 'treatment');
                        });
                    }
                    lastQuickAddTreatmentRow = null; // reset
                },
                error: function(xhr) {
                    console.error("API Error loading treatments:", xhr.status, xhr.responseText);
                    lastQuickAddTreatmentRow = null;
                }
            });
        }
        window.loadTreatments = loadTreatments;

        let rowIndex = 1;

        function getSelectedIds() {
            return $('.type-service-select').map((_, el) => $(el).val()).get().filter(v => v);
        }

        function populateTypeServiceSelect(row, type) {
            let data = [];

            if (type === 'service') {
                data = services;
            } else if (type === 'treatment') {
                data = treatments;
            } else if (type === 'medicine') {
                data = medicines;
            } else if (type === 'appointment') {
                data = appointments;
            } else if (type === 'therapy') {
                data = therapies;
            } else if (type === 'discharge') {
                data = []; // manual entry, no dropdown
            }

            const currentVal = row.find('.type-service-select').val();
            const selectedIds = $('.type-service-select')
                .not(row.find('.type-service-select'))
                .map((_, el) => $(el).val())
                .get()
                .filter(v => v);

            const select = row.find('.type-service-select');
            select.empty().append('<option value="">Select</option>');

           
            data.forEach(item => {
                const isSelected = (currentVal && currentVal == item.id) ? 'selected' : '';
                if (!selectedIds.includes(String(item.id)) || (currentVal && currentVal == item.id)) {
                    const gstOpt = (item.gst_option || '') ? ` data-gst-option="${item.gst_option}"` : '';
                    const productGst = (item.product_gst && (Array.isArray(item.product_gst) ? item.product_gst.length : true)) ? ` data-product-gst='${JSON.stringify(item.product_gst || [])}'` : '';
                    select.append(`
                <option value="${item.id}" data-service-type="${item.service_type ?? ''}"${gstOpt}${productGst} ${isSelected}>${item.name}</option>
            `);
                }
            });


            // Re-initialize Select2
            select.select2({
                placeholder: "Select Type",
                width: '100%'
            });

            // Set custom search placeholder when dropdown opens
            select.on('select2:open', function () {
                $('.select2-search__field').attr('placeholder', 'Search Type');
            });
        }








        function calculateRowAmount(row) {
            const qtyVal = row.find('.quantity').val();
            const priceVal = row.find('.price').val();
            if (priceVal === '' || priceVal === null || priceVal === undefined) {
                row.find('.amount').val('');
                return 0;
            }
            const qty = parseFloat(qtyVal) || 0;
            const price = parseFloat(priceVal) || 0;
            const amt = qty * price;
            row.find('.amount').val(amt.toFixed(2));
            return amt;
        }



        function updateGrandTotal() {
            let subtotal = 0;
            let totalGstAmount = 0;
            $('.invoice-item-row').each(function () {
                const row = $(this);
                const amt = parseFloat(row.find('.amount').val()) || 0;
                subtotal += amt;
                if (row.find('.gst-option-select').val() === 'With GST') {
                    const gstAmt = parseFloat(row.find('.gst-amount-display').val()) || 0;
                    totalGstAmount += gstAmt;
                }
            });

            const discountType = $('select[name="discount_type"]').val();
            const discountValue = parseFloat($('input[name="discount"]').val()) || 0;

            let discountAmount = 0;
            if (discountType === 'fixed_amount') {
                discountAmount = discountValue;
            } else if (discountType === 'percentage') {
                discountAmount = ((subtotal + totalGstAmount) * discountValue) / 100;
            }

            let grandTotal = subtotal + totalGstAmount - discountAmount;
            grandTotal = Math.max(grandTotal, 0);
            $('#grandTotal').val(grandTotal.toFixed(2));
        }



        $(function () {
            const wrapper = $('#dynamicFieldsWrapper');
            const type = $('#invoiceType').val();

            // Initial population
            populateTypeServiceSelect($('.invoice-item-row').first(), type);
            updateRowActionButtons();

            $('#invoiceType').change(function () {
                const selected = $(this).val();
                $('.to-date-wrapper').toggle(selected === 'treatment');
                wrapper.find('.invoice-item-row').each(function () {
                    populateTypeServiceSelect($(this), selected);
                });
                updateGrandTotal();
            });



            function updateRowActionButtons() {
                const rows = wrapper.find('.invoice-item-row');
                const selectedType = $('#invoiceType').val();

                if (selectedType === 'appointment' || selectedType === 'discharge') {
                    rows.find('.row-action-col').hide();
                    return;
                }

                rows.each(function (index) {
                    const $col = $(this).find('.row-action-col');
                    $col.show();
                    if (index === 0) {
                        $col.html(`
                            <button type="button" class="btn btn-row-action btn-row-add" title="Add Row">
                                <i class="fas fa-plus"></i>
                            </button>
                        `);
                    } else {
                        $col.html(`
                            <button type="button" class="btn btn-row-action btn-row-remove" title="Remove Row">
                                <i class="fas fa-times"></i>
                            </button>
                        `);
                    }
                });
            }
            window.updateRowActionButtons = updateRowActionButtons;

            function addNewRow() {
                const selected = $('#invoiceType').val();

                // destroy select2 before cloning
                $('.type-service-select').select2('destroy');

                const row = $('.invoice-item-row:first').clone();
                row.find('.type-service-select').empty().append('<option value="">Select</option>').val('');

                row.find('input, select').each(function () {
                    const name = $(this).attr('name')?.replace(/\d+/, rowIndex);
                    if (name) {
                        $(this).attr('name', name);
                        if ($(this).hasClass('quantity')) {
                            $(this).val(1);
                        } else if ($(this).hasClass('price')) {
                            $(this).val('');
                        } else if ($(this).hasClass('amount')) {
                            $(this).val('');
                        } else if ($(this).hasClass('gst-option-select')) {
                            $(this).val('Without GST');
                        } else if ($(this).hasClass('gst-option-hidden')) {
                            $(this).val('Without GST');
                        } else if ($(this).hasClass('gst-percent-display') || $(this).hasClass('gst-amount-display') || $(this).hasClass('product-gst-json')) {
                            $(this).val('');
                        } else if (!$(this).hasClass('type-service-select')) {
                            $(this).val('');
                        }
                    }
                });

                // Ensure quick-add button visibility matches current invoice type
                if (selected === 'treatment') {
                    row.find('.quick-add-treatment-btn').show();
                } else {
                    row.find('.quick-add-treatment-btn').hide();
                }

                $('#dynamicFieldsWrapper').append(row);

                // re-init select2 on all existing rows
                $('.invoice-item-row').not(row).find('.type-service-select').select2({
                    placeholder: "Select Type",
                    width: '100%'
                });

                populateTypeServiceSelect(row, selected);
                row.find('.type-service-select').val('').trigger('change.select2');
                updateRowGstVisibility(row);

                rowIndex++;
                updateGrandTotal();
                updateRowActionButtons();
            }

            // Click event for + (Add row)
            wrapper.on('click', '.btn-row-add', function () {
                addNewRow();
            });

            // Click event for x (Remove row)
            wrapper.on('click', '.btn-row-remove', function () {
                $(this).closest('.invoice-item-row').remove();
                updateGrandTotal();
                updateRowActionButtons();
            });

            // Update row GST display from product_gst (array). Recalculates tax_amount from row amount when rowAmount provided.
            function updateRowGstDisplay(row, productGst, rowAmount) {
                const $percent = row.find('.gst-percent-display');
                const $amount = row.find('.gst-amount-display');
                const $hidden = row.find('.product-gst-json');
                if (!productGst || !Array.isArray(productGst) || productGst.length === 0) {
                    $percent.val('');
                    $amount.val('');
                    $hidden.val('');
                    return;
                }
                const rates = productGst.map(t => parseFloat(t.tax_rate) || 0);
                const totalRate = rates.reduce((a, b) => a + b, 0);
                let gstList = productGst;
                let totalAmt;
                if (rowAmount != null && !isNaN(rowAmount)) {
                    totalAmt = 0;
                    gstList = productGst.map(t => {
                        const rate = parseFloat(t.tax_rate) || 0;
                        const taxAmt = (rowAmount * rate) / 100;
                        totalAmt += taxAmt;
                        return { ...t, tax_amount: taxAmt.toFixed(2) };
                    });
                } else {
                    totalAmt = productGst.map(t => parseFloat(t.tax_amount) || 0).reduce((a, b) => a + b, 0);
                }
                $percent.val(totalRate.toFixed(2));
                $amount.val(totalAmt.toFixed(2));
                $hidden.val(JSON.stringify(gstList));
            }
            // make available for discharge loader (different script block)
            window.updateRowGstDisplay = updateRowGstDisplay;

            function setRowGstOption(row, option) {
                row.find('.gst-option-select').val(option);
                row.find('.gst-option-hidden').val(option);
                updateRowGstVisibility(row);
            }

            function syncRowGstFromPercent(row) {
                const rowAmt = parseFloat(row.find('.amount').val()) || 0;
                const gstOpt = row.find('.gst-option-select').val();
                if (gstOpt !== 'With GST') {
                    return;
                }
                const pctVal = parseFloat(row.find('.gst-percent-display').val()) || 0;
                const gstAmt = (rowAmt * pctVal) / 100;
                row.find('.gst-amount-display').val(gstAmt.toFixed(2));

                let existingGst = [];
                try {
                    const raw = row.find('.product-gst-json').val();
                    if (raw) existingGst = JSON.parse(raw);
                } catch(e) {}

                if (Array.isArray(existingGst) && existingGst.length > 0) {
                    const oldTotalRate = existingGst.reduce((sum, t) => sum + (parseFloat(t.tax_rate) || 0), 0);
                    if (oldTotalRate > 0 && Math.abs(oldTotalRate - pctVal) < 0.01) {
                        existingGst = existingGst.map(t => {
                            const r = parseFloat(t.tax_rate) || 0;
                            return { ...t, tax_amount: ((rowAmt * r) / 100).toFixed(2) };
                        });
                    } else {
                        const name = existingGst.length === 1 ? (existingGst[0].tax_name || 'GST') : 'GST';
                        existingGst = [{ tax_name: name, tax_rate: pctVal.toFixed(2), tax_amount: gstAmt.toFixed(2) }];
                    }
                } else if (pctVal > 0 || gstAmt > 0) {
                    existingGst = [{ tax_name: 'GST', tax_rate: pctVal.toFixed(2), tax_amount: gstAmt.toFixed(2) }];
                } else {
                    existingGst = [];
                }

                row.find('.product-gst-json').val(existingGst.length > 0 ? JSON.stringify(existingGst) : '');
            }

            function syncRowGstFromAmount(row) {
                const rowAmt = parseFloat(row.find('.amount').val()) || 0;
                const gstOpt = row.find('.gst-option-select').val();
                if (gstOpt !== 'With GST') {
                    return;
                }
                const gstAmt = parseFloat(row.find('.gst-amount-display').val()) || 0;
                let pctVal = 0;
                if (rowAmt > 0) {
                    pctVal = (gstAmt / rowAmt) * 100;
                    row.find('.gst-percent-display').val(pctVal.toFixed(2));
                }

                let existingGst = [];
                try {
                    const raw = row.find('.product-gst-json').val();
                    if (raw) existingGst = JSON.parse(raw);
                } catch(e) {}

                const name = (Array.isArray(existingGst) && existingGst.length === 1) ? (existingGst[0].tax_name || 'GST') : 'GST';
                existingGst = [{ tax_name: name, tax_rate: pctVal.toFixed(2), tax_amount: gstAmt.toFixed(2) }];
                row.find('.product-gst-json').val(JSON.stringify(existingGst));
            }

            // Handle changes to rows
            wrapper.on('input change', '.quantity, .price, .type-service-select', function () {
                const row = $(this).closest('.invoice-item-row');
                const selectedId = row.find('.type-service-select').val();
                const selectedType = $('#invoiceType').val();
                const isServiceChange = $(this).hasClass('type-service-select');

                // Appointment: GST is not applicable; keep it hidden/cleared and just recalc totals.
                if (selectedType === 'appointment') {
                    setRowGstOption(row, 'Without GST');
                    row.find('.gst-percent-display').val('');
                    row.find('.gst-amount-display').val('');
                    row.find('.product-gst-json').val('');
                    calculateRowAmount(row);
                    updateGrandTotal();
                    return;
                }

                let selectedItem = null;
                if (selectedType === 'medicine') {
                    selectedItem = medicines.find(i => i.id == selectedId);
                } else if (selectedType === 'service') {
                    selectedItem = services.find(i => i.id == selectedId);
                } else if (selectedType === 'treatment') {
                    selectedItem = treatments.find(i => i.id == selectedId);
                } else if (selectedType === 'appointment') {
                    selectedItem = appointments.find(i => i.id == selectedId);
                } else if (selectedType === 'therapy') {
                    selectedItem = therapies.find(i => i.id == selectedId);
                }

                if (isServiceChange && selectedItem) {
                    let price = '';
                    if (selectedType === 'medicine') {
                        price = selectedItem.unit ?? '';
                    } else if (selectedType === 'treatment') {
                        price = selectedItem.price ?? '';
                    } else if (selectedType === 'service') {
                        price = selectedItem.cost ?? '';
                    } else if (selectedType === 'therapy') {
                        price = selectedItem.cost ?? '';
                    } else if (selectedType === 'appointment') {
                        price = selectedItem.unit ?? '';
                    }

                    row.find('.price').val('');
                    if (price !== '') {
                        row.find('.price').val(parseFloat(price).toFixed(2));
                    }

                    // Auto-fill and calculate row amount (price * quantity)
                    const rowAmt = calculateRowAmount(row);

                    // GST: if product has With GST, auto-fill and show GST cols
                    const gstOption = selectedItem.gst_option || '';
                    const productGst = selectedItem.product_gst || (selectedItem.product_gst !== undefined && typeof selectedItem.product_gst === 'string' ? (() => { try { return JSON.parse(selectedItem.product_gst); } catch(e) { return null; } })() : null);
                    if ((gstOption === 'With GST' || gstOption === 'with_gst') && productGst && (Array.isArray(productGst) ? productGst.length : productGst)) {
                        setRowGstOption(row, 'With GST');
                        updateRowGstDisplay(row, Array.isArray(productGst) ? productGst : [productGst], rowAmt);
                    } else {
                        setRowGstOption(row, 'Without GST');
                        updateRowGstDisplay(row, null);
                    }
                } else if (!isServiceChange) {
                    // Quantity or price changed: recalc amount and GST for this row
                    calculateRowAmount(row);
                    if (row.find('.gst-option-select').val() === 'With GST') {
                        syncRowGstFromPercent(row);
                    }
                } else if (!selectedItem) {
                    row.find('.price').val('');
                    calculateRowAmount(row);
                    setRowGstOption(row, 'Without GST');
                    updateRowGstDisplay(row, null);
                }

                $('.invoice-item-row').each(function () {
                    populateTypeServiceSelect($(this), selectedType);
                });

                updateGrandTotal();
            });

            // When GST option changes, clear or show GST display and adjust column widths
            wrapper.on('change', '.gst-option-select', function () {
                const row = $(this).closest('.invoice-item-row');
                const val = $(this).val();
                row.find('.gst-option-hidden').val(val);
                updateRowGstVisibility(row);

                if (val !== 'With GST') {
                    row.find('.gst-percent-display').val('');
                    row.find('.gst-amount-display').val('');
                    row.find('.product-gst-json').val('');
                } else {
                    let pct = parseFloat(row.find('.gst-percent-display').val());
                    if (isNaN(pct) || pct <= 0) {
                        const opt = row.find('.type-service-select option:selected');
                        const productGstData = opt.attr('data-product-gst');
                        if (productGstData) {
                            try {
                                const parsed = JSON.parse(productGstData);
                                const rowAmt = parseFloat(row.find('.amount').val()) || 0;
                                updateRowGstDisplay(row, parsed, rowAmt);
                            } catch(e) {}
                        }
                    }
                    syncRowGstFromPercent(row);
                }
                updateGrandTotal();
            });

            // When Amount is manually changed
            wrapper.on('input change', '.amount', function () {
                const row = $(this).closest('.invoice-item-row');
                const amt = parseFloat($(this).val()) || 0;
                const qty = parseFloat(row.find('.quantity').val()) || 1;
                if (qty > 0) {
                    row.find('.price').val((amt / qty).toFixed(2));
                }
                syncRowGstFromPercent(row);
                updateGrandTotal();
            });

            // When GST (%) is manually changed
            wrapper.on('input change', '.gst-percent-display', function () {
                const row = $(this).closest('.invoice-item-row');
                const pct = parseFloat($(this).val());
                if (!isNaN(pct) && pct > 0 && row.find('.gst-option-select').val() !== 'With GST') {
                    setRowGstOption(row, 'With GST');
                }
                syncRowGstFromPercent(row);
                updateGrandTotal();
            });

            // When GST Amount is manually changed
            wrapper.on('input change', '.gst-amount-display', function () {
                const row = $(this).closest('.invoice-item-row');
                const amt = parseFloat($(this).val());
                if (!isNaN(amt) && amt > 0 && row.find('.gst-option-select').val() !== 'With GST') {
                    setRowGstOption(row, 'With GST');
                }
                syncRowGstFromAmount(row);
                updateGrandTotal();
            });

            // Handle discount changes
            $('select[name="discount_type"], input[name="discount"]').on('change input', function () {
                updateGrandTotal();
            });
        });
    </script>



    <script>
        $(document).ready(function () {
            function isRowValid(row) {
                let isValid = true;
                const typeService = row.find('.type-service-select');
                const qty = row.find('.quantity');
                const price = row.find('.price');
                const invoiceType = $('#invoiceType').val(); // Get selected invoice type

                row.find('.error-msg').remove(); // Clear old errors

                // Skip validation for type-service/qty when invoice type is appointment or discharge
                if (invoiceType === 'appointment' || invoiceType === 'discharge') {
                    if (!price.val() || isNaN(price.val()) || parseFloat(price.val()) < 0) {
                        isValid = false;
                        price.after('<div class="text-danger error-msg">Invalid price / amount</div>');
                    }
                    return isValid;
                }

                // Validate type-service
                if (!typeService.val()) {
                    isValid = false;
                    typeService.after('<div class="text-danger error-msg">Required</div>');
                }

                // Validate quantity
                if (!qty.val() || isNaN(qty.val()) || parseFloat(qty.val()) <= 0) {
                    isValid = false;
                    qty.after('<div class="text-danger error-msg">Quantity required</div>');
                }

                // Validate price
                if (!price.val() || isNaN(price.val()) || parseFloat(price.val()) <= 0) {
                    isValid = false;
                    price.after('<div class="text-danger error-msg">Invalid price</div>');
                }

               // console.log('isValid row', isValid);
                return isValid;
            }


            // Initialize jQuery Validation for #invoiceForm
            const invoiceValidator = $('#invoiceForm').validate({
                ignore: [],
                rules: {
                    patient_id: {
                        required: true
                    },
                    invoice_type: {
                        required: true
                    },
                    date1: {
                        required: true
                    },
                    payment_type: {
                        required: true
                    },
                    payment_status: {
                        required: true
                    }
                },
                messages: {
                    patient_id: {
                        required: "Please select a patient"
                    },
                    invoice_type: {
                        required: "Please select an invoice type"
                    },
                    date1: {
                        required: "Please select a date"
                    },
                    payment_type: {
                        required: "Please select a payment type"
                    },
                    payment_status: {
                        required: "Please select a payment status"
                    }
                },
                errorElement: 'span',
                errorClass: 'text-danger error-msg',
                errorPlacement: function (error, element) {
                    if (element.hasClass('select2-hidden-accessible')) {
                        error.insertAfter(element.next('.select2-container'));
                    } else if (element.closest('.input-group').length) {
                        error.insertAfter(element.closest('.input-group'));
                    } else {
                        error.insertAfter(element);
                    }
                },
                highlight: function (element) {
                    $(element).addClass('is-invalid');
                    if ($(element).hasClass('select2-hidden-accessible')) {
                        $(element).next('.select2-container').find('.select2-selection').addClass('is-invalid');
                    }
                },
                unhighlight: function (element) {
                    $(element).removeClass('is-invalid');
                    if ($(element).hasClass('select2-hidden-accessible')) {
                        $(element).next('.select2-container').find('.select2-selection').removeClass('is-invalid');
                    }
                }
            });

            // Revalidate Select2 fields immediately upon user change
            $('#patientDropdown, #invoiceType, #payment-type, #payment-status').on('change.select2 change', function () {
                $(this).valid();
            });

            $('#invoiceForm').on('submit', function (e) {
                e.preventDefault();

                // Check jQuery Validation first
                const isFormValid = $('#invoiceForm').valid();

                // Validate dynamic invoice item rows
                let areRowsValid = true;
                $('.invoice-item-row').each(function () {
                    const rowValid = isRowValid($(this));
                    if (!rowValid) {
                        areRowsValid = false;
                    }
                });

                if (!isFormValid || !areRowsValid) {
                    return; // stop submission and display inline validation messages
                }
                let branchId = getBranchId();
                $('#branch_id').val(branchId);

                const formData = new FormData(this);
                if (!formData.get('discount_type')) {
                    formData.set('discount_type', 'fixed_amount');
                }
                if (!formData.get('discount')) {
                    formData.set('discount', '0');
                }
                console.log('branch_id from form:', formData.get('branch_id'));
                $.ajax({
                    url: '/api/invoice', // update to your endpoint
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    success: function (response) {
                        if (response.status) {
                            $('#invoicesuccessMessage')
                                .text(response.message || 'Invoice created successfully!')
                                .show();

                            setTimeout(() => {
                                if (response?.url) {
                                    window.open(response.url, '_blank');
                                }
                                $('#invoicesuccessMessage').hide();
                                location.reload();
                            }, 1500);
                            $('#invoiceerrorMessage').text('').hide();
                        }
                    },
                    error: function (xhr) {
                        let message = xhr.responseJSON?.message || 'Something went wrong!';
                        $('#invoiceerrorMessage').text(message).show();
                    }
                });
            });

            // Initialize shared Quick Add Modals
            let token = @json(session('access_token')) || localStorage.getItem('authToken');
            if (typeof window.initQuickAddModals === 'function') {
                window.initQuickAddModals({
                    getToken: function() { return token; },
                    getBranchId: function() { return getBranchId(); },
                    onPatientAdded: function(newId) {
                        if (typeof window.loadPatients === 'function') {
                            window.loadPatients(newId);
                        }
                    },
                    onTreatmentAdded: function(newId) {
                        if (typeof window.loadTreatments === 'function') {
                            window.loadTreatments(newId);
                        }
                    }
                });
            }

        });
    </script>


@endsection