document.addEventListener('DOMContentLoaded', function() {
    let storedBranchId = localStorage.getItem('selectedBranchId');
    if (storedBranchId) {
        document.getElementById('branch_id').value = storedBranchId;
    }
});
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        width: '100%'
    });
    $('#gst_option').select2({
        placeholder: 'Select GST Option',
        allowClear: true,
        width: '100%'
    });
    $('#product_gst').select2({
        placeholder: 'Select GST',
        allowClear: true,
        width: '100%'
    });

    var form = $('#therapyForm');

    fetchTaxRates();

    $('#gst_option').on('change', function() {
        if ($(this).val() === 'With GST') {
            $('#product_gst_div').show();
            $('#product_gst').attr('required', true);
        } else {
            $('#product_gst_div').hide();
            $('#product_gst').attr('required', false).val(null).trigger('change');
        }
    });
    $('#gst_option').trigger('change');

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
                        let rateValue = parseFloat(tax.tax_rate || 0).toFixed(2);
                        let value = `${tax.tax_name}|${rateValue}`;
                        gstDropdown.append(
                            `<option value="${value}">${tax.tax_name} (${rateValue}%)</option>`
                        );
                    }
                });
            },
            error: function(xhr) {
                console.error("Error loading tax rates:", xhr.responseText);
            }
        });
    }

    function buildProductGstPayload(cost) {
        let selected = $('#product_gst').val() || [];
        return selected.map(function(value) {
            let parts = value.split('|');
            let taxName = parts[0] || '';
            let taxRate = parseFloat(parts[1] || 0);
            let taxAmount = Number(((cost * taxRate) / 100).toFixed(2));
            return {
                tax_name: taxName,
                tax_rate: taxRate.toFixed(2),
                tax_amount: taxAmount
            };
        });
    }

    form.validate({
        rules: {
            name: "required",
            description: "required",
            duration_minutes: "required",

            cost: {
                required: true,
                number: true,
                min: 0
            },
            status: "required",
            gst_option: "required",
            "product_gst[]": {
                required: function() {
                    return $("#gst_option").val() === "With GST";
                }
            }
        },
        messages: {
            name: "Please enter the therapy name",
            description: "Please enter the description",
            duration_minutes: "Please enter duration time",

            cost: {
                required: "Please enter the cost",
                number: "Please enter a valid number",
                min: "Cost cannot be negative"
            },
            status: "Please select a status",
            gst_option: "Please select a GST option",
            "product_gst[]": "Please select a product GST"
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

    form.on('submit', function(e) {
        e.preventDefault();
        if (!form.valid()) {
            return;
        }
        let branchId = localStorage.getItem('selectedBranchId');
        let gstOption = $("select[name='gst_option']").val();
        let costValue = parseFloat($("input[name='cost']").val() || 0);
        let productGstPayload = gstOption === 'With GST' ? buildProductGstPayload(costValue) : null;
        let formData = {
            name: $("input[name='name']").val(),
            description: $("textarea[name='description']").val(),
            duration_minutes: $("input[name='duration_minutes']").val(),
            cost: $("input[name='cost']").val(),
            status: $("select[name='status']").val(),
            gst_option: gstOption,
            product_gst: productGstPayload,
            branch_id: branchId
        };

        $.ajax({
            url: '/api/therapies',
            type: 'POST',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            success: function(response) {
                $('#therapysuccessMessage').text('Therapy created successfully.')
                    .fadeIn();
                $('#therapyerrorMessage').hide();
                $('#therapyForm')[0].reset();

                setTimeout(() => {
                    window.location.href = "/therapy";
                }, 1500);
            },
            error: function(xhr) {
                let errorMsg = 'Something went wrong.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMsg = '';
                    $.each(xhr.responseJSON.errors, function(key, value) {
                        errorMsg += value[0] + '<br>';
                    });
                }
                $('#therapyerrorMessage').html(errorMsg).fadeIn();
                $('#therapysuccessMessage').hide();
            }
        });
    });
});
