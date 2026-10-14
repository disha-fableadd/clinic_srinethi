document.addEventListener('DOMContentLoaded', function() {
    let storedBranchId = localStorage.getItem('selectedBranchId');
    if (storedBranchId) {
        document.getElementById('branch_id').value = storedBranchId;
    }
});
$(document).ready(function() {

    // Initialize form validation
    $('#createTreatmentForm').validate({
        rules: {
            name: {
                required: true,
            },
            doctor_id: {
                required: true
            },
            price: {
                required: true
            },
            description: {
                required: true
            },
            gst_option: {
                required: true
            },
            'product_gst[]': {
                required: function() {
                    return $('#gst_option').val() === 'With GST';
                }
            }
        },
        messages: {
            name: {
                required: "Please enter the treatment name",
            },
            doctor_id: {
                required: "Please select a doctor"
            },
            price: {
                required: "Please enter price"
            },
            description: {
                required: "Please enter description"
            },
            gst_option: {
                required: "Please select GST option"
            },
            'product_gst[]': {
                required: "Please select at least one GST"
            }
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

    // GST Option Toggle
    $('#gst_option').select2({
        placeholder: "Select GST Option",
        width: '100%'
    });

    function ensureProductGstSelect2() {
        const $el = $('#product_gst');
        if (!$el.length) return;
        if ($el.hasClass('select2-hidden-accessible')) {
            $el.select2('destroy');
        }
        $el.select2({
            placeholder: "Select GST",
            allowClear: true,
            closeOnSelect: false,
            width: '100%'
        });
    }

    $('#gst_option').on('change', function() {
        if ($(this).val() === 'With GST') {
            $('#product_gst_div').show();
            ensureProductGstSelect2(); // fix width when showing from hidden
        } else {
            $('#product_gst_div').hide();
            $('#product_gst').val(null).trigger('change');
        }
    });

    // Fetch Tax Rates
    function fetchTaxRates() {
        let branchId = localStorage.getItem('selectedBranchId');
        $.ajax({
            url: '/api/tax-rates',
            type: 'GET',
            data: { branch_id: branchId },
            headers: { "Authorization": "Bearer " + token },
            success: function(data) {
                let gstDropdown = $('#product_gst');
                gstDropdown.empty().append('<option value="">Select GST</option>');
                
                let taxRates = Array.isArray(data) ? data : (data.data || []);
                $.each(taxRates, function(index, tax) {
                    if (tax.status === 'active') {
                        gstDropdown.append(`<option value="${tax.id}">${tax.tax_name} (${tax.tax_rate}%)</option>`);
                    }
                });

                gstDropdown.select2({
                    placeholder: "Select GST",
                    allowClear: true,
                    closeOnSelect: false,
                    width: '100%'
                });
            },
            error: function(xhr) {
                console.error("Error loading tax rates:", xhr.responseText);
            }
        });
    }

    fetchTaxRates();

    // Submit Treatment Form
    $('#createTreatmentForm').on('submit', function(e) {
        e.preventDefault();

        if ($('#createTreatmentForm').valid()) {
            let formData = new FormData(this);
            formData.append("user_id", userId); // Attach logged-in user ID

            $.ajax({
                url: '/api/treatments',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    "Authorization": "Bearer " + token
                },
                success: function(response) {
                    $('#treatmentsuccessMessage').text(response.message ||
                        'Treatment created successfully!').show();
                    $('#createTreatmentForm')[0].reset();
                    setTimeout(function() {
                        window.location.href = '/treatment';
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
                    $('#treatmenterrorMessage').html(errorMessage).show();
                }
            });
        }
    });

    let branchId = localStorage.getItem('selectedBranchId');

    $.ajax({
        url: "/api/doctors",
        type: "GET",
        data: {
            branch_id: branchId
        }, // branch filter
        dataType: "json",
        headers: {
            "Authorization": "Bearer " + token
        },
        success: function(response) {
            let doctorDropdown = $('.doctorSelect');
            doctorDropdown.empty().append('<option value="">Select Doctor</option>');

            let doctorsList = response.doctors || [];
            $.each(doctorsList, function(index, doctor) {
                const capitalizedName = doctor.fullname.charAt(0).toUpperCase() + doctor
                    .fullname.slice(1);
                doctorDropdown.append(
                    `<option value="${doctor.id}">${capitalizedName}</option>`);
            });

            if (!doctorDropdown.hasClass('select2-hidden-accessible')) {
                doctorDropdown.select2({
                    placeholder: "Select Doctor",
                    allowClear: true,
                    width: '100%'
                });
            }

            doctorDropdown.on('select2:open', function() {
                $('.select2-search__field').attr('placeholder', 'Search Doctor');
            });
        },
        error: function(xhr) {
            console.error("API Error:", xhr.status, xhr.responseText);
        }
    });

});
