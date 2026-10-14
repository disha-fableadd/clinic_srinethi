@extends('layout.app')
<style>
    @media screen and (max-width: 767px) {
        .page-title {
            font-size: 19px !important;
            /* padding-left: 10px !important; */
            text-align: left !important;
            padding-top: 6px !important;
        }
    }
    /* Total Bill amount field style (like Add Discharge form) when type = Discharge */
    .invoice-amount-input.invoice-total-bill {
        border-radius: 50px;
        border-color: rgb(207, 236, 224);
        font-weight: 500;
    }
    .amount-col .icon-style {
        margin-right: 6px;
        color: #28a745;
    }
</style>
@section('content')

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

                    <form id="invoiceForm" class="form-container all-form ">
                        @csrf
                        <input type="hidden" name="branch_id" id="branch_id">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label>Patient <span class="text-danger">*</span></label>


                                <select class="form-control select2" name="patient_id" id="patientDropdown" required>
                                    <option value="">Select Patient</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Type <span class="text-danger">*</span></label>
                                <select name="invoice_type" class="form-control select2" id="invoiceType" required>
                                    <option value="">Select Type</option>
                                    <option value="service">Service</option>
                                    <option value="treatment" selected>Treatment</option>
                                    <option value="medicine">Medicine</option>
                                    <option value="appointment">Appointment</option>
                                    <option value="therapy">Therapy</option>
                                    <option value="discharge">Discharge</option>
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
                            <div class="row mb-2 invoice-item-row align-items-end">
                                <div class="col-md-2">
                                    <label>Type/Service</label>
                                    <select name="items[0][type_service]" class="form-control type-service-select select2">
                                        <option value="">Select</option>
                                        {{-- options will be added dynamically --}}
                                    </select>
                                </div>
                                <div class="col-md-1">
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
                                    <input type="text" name="items[0][total]" class="form-control amount invoice-amount-input" readonly>
                                </div>
                                <div class="col-md-2 gst-option-col">
                                    <label>GST Option</label>
                                    <select class="form-control gst-option-select" disabled>
                                        <option value="Without GST" selected>Without GST</option>
                                        <option value="With GST">With GST</option>
                                    </select>
                                    <input type="hidden" name="items[0][gst_option]" class="gst-option-hidden" value="Without GST">
                                </div>
                                <div class="col-md-1 gst-pct-col">
                                    <label>GST (%)</label>
                                    <input type="text" name="items[0][gst_percent_display]" class="form-control gst-percent-display" readonly placeholder="0">
                                </div>
                                <div class="col-md-2 gst-amt-col">
                                    <label>GST Amount</label>
                                    <input type="text" name="items[0][gst_amount_display]" class="form-control gst-amount-display" readonly placeholder="0">
                                </div>
                                <input type="hidden" name="items[0][product_gst]" class="product-gst-json" value="">
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

                                    // Reset error message
                                    $errorDiv.text('');

                                    if (value < 0) {
                                        $errorDiv.text('Value cannot be negative');
                                        $input.val(''); // optional: clear the field
                                    }
                                });
                            });
                        </script>
                        <div class="col-md-3 my-auto">
                            <button type="button" id="addRowBtn" class="btn btn-sm btn-primary mr-3">
                                + Add More
                            </button>
                            <button type="button" id="removeRowBtn" class="btn btn-sm btn-danger "
                                style="border-radius:20px">
                                - Remove
                            </button>
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
                                <label>Payment Type</label>
                                <select name="payment_type" class="form-control select2" id="payment-type" required>
                                    <option value="">-- Select Payment Type --</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Online">Online</option>

                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>Payment Status</label>
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

        $(document).ready(function () {



            let branchId = localStorage.getItem('selectedBranchId');

            if (!branchId) {
                Swal.fire('Error', 'Please select a branch first.', 'error');
                return;
            }

            $.ajax({
                url: "/api/patientss",
                type: "GET",
                dataType: "json",
                data: { branch_id: branchId }, // ✅ send branch_id as query param
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

                    // Initialize Select2
                    patientDropdown.select2({
                        placeholder: "Select Patient",
                        allowClear: true,
                        width: '100%'
                    });

                    // Set search input placeholder
                    patientDropdown.on('select2:open', function () {
                        $('.select2-search__field').attr('placeholder', 'Search Patient');
                    });
                },
                error: function (xhr) {
                    console.error("API Error:", xhr.status, xhr.responseText);
                }
            });





            function toggleFieldsByInvoiceType() {
                const selectedType = $('#invoiceType').val();

                $('.invoice-item-row').each(function () {
                    const $row = $(this);
                    const $typeService = $row.find('.type-service-select').closest('[class*="col-"]');
                    const $quantity = $row.find('.quantity').closest('[class*="col-"]');
                    const $priceCol = $row.find('.price').closest('.price-col');
                    const $amountCol = $row.find('.amount').closest('.amount-col');
                    const $amount = $row.find('.amount');
                    const $gstOptCol = $row.find('.gst-option-select').closest('.gst-option-col');
                    const $gstPctCol = $row.find('.gst-pct-col');
                    const $gstAmtCol = $row.find('.gst-amt-col');

                    if (selectedType === 'appointment' || selectedType === 'discharge') {
                        $typeService.hide();
                        $quantity.hide();
                        $('#addRowBtn, #removeRowBtn').hide();
                        // reset sizing first
                        $priceCol.removeClass('col-md-1 col-md-2 col-md-3 col-md-4 col-md-6').addClass('col-md-6');
                        $amountCol.removeClass('col-md-1 col-md-2 col-md-3 col-md-4 col-md-6').addClass('col-md-6');
                        $gstOptCol.show();
                        $gstPctCol.show();
                        $gstAmtCol.show();

                        if (selectedType === 'discharge') {
                            $amountCol.find('.amount-label-default').hide();
                            $amountCol.find('.amount-label-total-bill').show();
                            $amount.addClass('invoice-total-bill');

                            // Discharge single row: col-md-3, col-md-3, col-md-2, col-md-2, col-md-2
                            $priceCol.removeClass('col-md-6').addClass('col-md-3');
                            $amountCol.removeClass('col-md-6').addClass('col-md-3');
                            $gstOptCol.removeClass('col-md-6 col-md-4 col-md-2 col-md-1').addClass('col-md-2');
                            $gstPctCol.removeClass('col-md-6 col-md-4 col-md-2 col-md-1').addClass('col-md-2');
                            $gstAmtCol.removeClass('col-md-6 col-md-4 col-md-2 col-md-1').addClass('col-md-2');
                        } else {
                            $amountCol.find('.amount-label-default').show();
                            $amountCol.find('.amount-label-total-bill').hide();
                            $amount.removeClass('invoice-total-bill');

                            // Appointment: remove GST fields entirely (only price + amount)
                            $gstOptCol.hide();
                            $gstPctCol.hide();
                            $gstAmtCol.hide();
                            $row.find('.gst-option-select').val('Without GST');
                            $row.find('.gst-option-hidden').val('Without GST');
                            $row.find('.gst-percent-display').val('');
                            $row.find('.gst-amount-display').val('');
                            $row.find('.product-gst-json').val('');
                        }
                    } else {
                        $typeService.show();
                        $quantity.show();
                        $('#addRowBtn').show();
                        toggleRemoveButton();
                        $priceCol.removeClass('col-md-6 col-md-4 col-md-3').addClass('col-md-2');
                        $amountCol.removeClass('col-md-6 col-md-4 col-md-3').addClass('col-md-2');
                        $amountCol.find('.amount-label-default').show();
                        $amountCol.find('.amount-label-total-bill').hide();
                        $amount.removeClass('invoice-total-bill');
                        $gstOptCol.show();
                        $gstPctCol.show();
                        $gstAmtCol.show();
                    }
                });
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
        const treatments = @json($treatments);
        const medicines = @json($medicines);
        const appointments = @json($appointments);
        const therapies = @json($therapies ?? []);

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
                const isSelected = currentVal == item.id ? 'selected' : '';
                if (!selectedIds.includes(String(item.id)) || currentVal == item.id) {
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
            const qty = parseFloat(row.find('.quantity').val()) || 0;
            const price = parseFloat(row.find('.price').val()) || 0;
            const amt = qty * price;
            row.find('.amount').val(amt.toFixed(2));
            return amt;
        }



        function updateGrandTotal() {
            let subtotal = 0;
            let totalGstAmount = 0;
            $('.invoice-item-row').each(function () {
                const row = $(this);
                subtotal += calculateRowAmount(row);
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

            $('#invoiceType').change(function () {
                const selected = $(this).val();
                $('.to-date-wrapper').toggle(selected === 'treatment');
                wrapper.find('.invoice-item-row').each(function () {
                    populateTypeServiceSelect($(this), selected);
                });
                updateGrandTotal();
            });



            $('#addRowBtn').click(function () {
                const selected = $('#invoiceType').val();

                // destroy select2 before cloning
                $('.type-service-select').select2('destroy');

                const row = $('.invoice-item-row:first').clone();

                row.find('input, select').each(function () {
                    const name = $(this).attr('name')?.replace(/\d+/, rowIndex);
                    if (name) {
                        $(this).attr('name', name);
                        if ($(this).hasClass('quantity')) {
                            $(this).val(1);
                        } else if ($(this).hasClass('price')) {
                            $(this).val(0);
                        } else if ($(this).hasClass('amount')) {
                            $(this).val('0.00');
                        } else if ($(this).hasClass('gst-option-select')) {
                            $(this).val('Without GST');
                        } else if ($(this).hasClass('gst-option-hidden')) {
                            $(this).val('Without GST');
                        } else if ($(this).hasClass('gst-percent-display') || $(this).hasClass('gst-amount-display') || $(this).hasClass('product-gst-json')) {
                            $(this).val('');
                        } else {
                            $(this).val('');
                        }
                    }
                });

                $('#dynamicFieldsWrapper').append(row);

                // re-init select2
                $('.type-service-select').select2({
                    placeholder: "Select Type",
                    width: '100%'
                });

                populateTypeServiceSelect(row, selected);

                rowIndex++;
                updateGrandTotal();
                toggleRemoveButton(); // 🔥 add this
            });


            $('#removeRowBtn').click(function () {
                const rows = wrapper.find('.invoice-item-row');
                if (rows.length > 1) {
                    rows.last().remove();
                    updateGrandTotal();
                }
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

                    // GST: for service/medicine/therapy, if product has With GST, auto-fill
                    const gstOption = selectedItem.gst_option || '';
                    const productGst = selectedItem.product_gst || (selectedItem.product_gst !== undefined && typeof selectedItem.product_gst === 'string' ? (() => { try { return JSON.parse(selectedItem.product_gst); } catch(e) { return null; } })() : null);
                    if ((gstOption === 'With GST' || gstOption === 'with_gst') && productGst && (Array.isArray(productGst) ? productGst.length : productGst)) {
                        setRowGstOption(row, 'With GST');
                        const rowAmt = calculateRowAmount(row);
                        updateRowGstDisplay(row, Array.isArray(productGst) ? productGst : [productGst], rowAmt);
                    } else {
                        setRowGstOption(row, 'Without GST');
                        updateRowGstDisplay(row, null);
                    }
                } else if (!isServiceChange) {
                    // Quantity or price changed: recalc amount and GST for this row
                    calculateRowAmount(row);
                    if (row.find('.gst-option-select').val() === 'With GST') {
                        const productGstJson = row.find('.product-gst-json').val();
                        if (productGstJson) {
                            try {
                                const productGst = JSON.parse(productGstJson);
                                const rowAmt = parseFloat(row.find('.amount').val()) || 0;
                                updateRowGstDisplay(row, productGst, rowAmt);
                            } catch (e) {}
                        }
                    }
                } else if (!selectedItem) {
                    row.find('.price').val('');
                    setRowGstOption(row, 'Without GST');
                    updateRowGstDisplay(row, null);
                }

                $('.invoice-item-row').each(function () {
                    populateTypeServiceSelect($(this), selectedType);
                });

                updateGrandTotal();
            });

            // When GST option changes, clear or show GST display
            wrapper.on('change', '.gst-option-select', function () {
                const row = $(this).closest('.invoice-item-row');
                const val = $(this).val();
                row.find('.gst-option-hidden').val(val);
                if (val !== 'With GST') {
                    row.find('.gst-percent-display').val('');
                    row.find('.gst-amount-display').val('');
                    row.find('.product-gst-json').val('');
                }
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


            $('#invoiceForm').on('submit', function (e) {
                e.preventDefault();

                let isFormValid = true;

                // Validate all rows
                $('.invoice-item-row').each(function () {
                    const rowValid = isRowValid($(this));
                    if (!rowValid) {
                        isFormValid = false;
                    }
                });

                if (!isFormValid) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please fill all required fields and enter valid price .',
                    });
                    return; // stop submission
                }
                let branchId = localStorage.getItem('selectedBranchId');
                if (!branchId) {
                    Swal.fire('Error', 'Please select a branch first', 'error');
                    return;
                }

                $('#branch_id').val(branchId);

                const formData = new FormData(this);
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

        });
    </script>


@endsection