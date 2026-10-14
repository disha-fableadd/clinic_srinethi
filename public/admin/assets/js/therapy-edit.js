$(document).ready(function() {
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

    const therapyId = $('#therapyId').val();
    const form = $('#edittherapyForm');

    fetchTaxRates().always(function() {
        loadTherapy();
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

    // Prefill form data for edit
    function loadTherapy() {
        if (!therapyId) {
            return;
        }
        $.ajax({
            url: `/api/therapies/${therapyId}`,
            type: 'GET',
            success: function(response) {
                $("input[name='name']").val(response.name);
                $("textarea[name='description']").val(response.description);
                $("input[name='duration_minutes']").val(response.duration_minutes);
                $("input[name='cost']").val(response.cost);
                $("select[name='status']").val(response.status).trigger('change');
                $("select[name='gst_option']").val(response.gst_option || 'Without GST').trigger('change');

                if (response.gst_option === 'With GST') {
                    let productGst = response.product_gst;
                    if (typeof productGst === 'string') {
                        try {
                            productGst = JSON.parse(productGst);
                        } catch (e) {
                            console.error("Error parsing product_gst", e);
                            productGst = [];
                        }
                    }

                    if (Array.isArray(productGst)) {
                        let selectedValues = productGst.map(function(item) {
                            let rate = parseFloat(item.tax_rate || 0).toFixed(2);
                            return `${item.tax_name}|${rate}`;
                        });
                        $("#product_gst").val(selectedValues).trigger('change');
                    }
                }
            },
            error: function() {
                $('#edittherapyerrorMessage').text("Failed to load therapy details.").fadeIn();
            }
        });
    }


    // Apply validation
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
            },

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

    // Submit form for add or update
    form.on('submit', function(e) {
        e.preventDefault();

        if (!form.valid()) {
            return;
        }

        $('#edittherapysuccessMessage').hide().text('');
        $('#edittherapyerrorMessage').hide().text('');

        let gstOption = $("select[name='gst_option']").val();
        let costValue = parseFloat($("input[name='cost']").val() || 0);
        let productGstPayload = gstOption === 'With GST' ? buildProductGstPayload(costValue) : null;

        const formData = {
            name: $("input[name='name']").val(),
            description: $("textarea[name='description']").val(),
            duration_minutes: $("input[name='duration_minutes']").val(),
            cost: $("input[name='cost']").val(),
            status: $("select[name='status']").val(),
            gst_option: gstOption,
            product_gst: productGstPayload
        };

        const method = therapyId ? 'PUT' : 'POST';
        const url = therapyId ? `/api/therapies/${therapyId}` : `/api/therapies`;

        $.ajax({
            url: url,
            type: method,
            data: JSON.stringify(formData),
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            success: function(response) {
                $('#edittherapysuccessMessage').text(response.message).fadeIn();

                setTimeout(function() {
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
                $('#edittherapyerrorMessage').html(errorMsg).fadeIn();
                setTimeout(() => $('#edittherapyerrorMessage').fadeOut(), 5000);
            }
        });

    });
});
