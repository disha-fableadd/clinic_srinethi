document.addEventListener('DOMContentLoaded', function () {
    let storedBranchId = localStorage.getItem('selectedBranchId');
    if (storedBranchId) {
        document.getElementById('branch_id').value = storedBranchId;
    }
});

$(document).ready(function () {
    $('.timepicker').timepicker({
        showMeridian: true,
        defaultTime: '12:00 PM',
    });
});


$(document).ready(function () {


    // jQuery validation
    $('#createSupplierForm').validate({
        rules: {
            name: {
                required: true,
                minlength: 3
            },
            contact_person: {
                required: true,
                minlength: 3
            },
            phone: {
                required: true,
                digits: true,
                minlength: 10,
                maxlength: 10
            },
            email: {
                required: true,
                email: true
            },
            address: {
                required: true,
                minlength: 10
            }
        },
        messages: {
            name: {
                required: "Please enter the company name",
                minlength: "Company name must be at least 3 characters long"
            },
            contact_person: {
                required: "Please enter the contact person's name",
                minlength: "Contact person's name must be at least 3 characters long"
            },
            phone: {
                required: "Please enter a phone number",
                digits: "Please enter only numbers",
                minlength: "Phone number must be exactly 10 digits",
                maxlength: "Phone number must be exactly 10 digits"
            },
            email: {
                required: "Please enter an email address",
                email: "Please enter a valid email address"
            },
            address: {
                required: "Please enter an address",
                minlength: "Address must be at least 10 characters long"
            }
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        },


        submitHandler: function (form) {
            let formData = new FormData(form);
            const token = localStorage.getItem('token');
            $.ajax({
                url: '/api/suppliers',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: { "Authorization": "Bearer " + token },
                success: function (response) {
                    $('#suppliersuccessMessage').text(response.message || 'Supplier details created successfully').show();
                    $('#createSupplierForm')[0].reset();
                    setTimeout(function () {
                        window.location.href = "/supplier";
                    }, 1500);
                },
                error: function (xhr) {
                    var errorMessage = '';
                    if (xhr.status === 422) { // Validation error
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function (key, messages) {
                            errorMessage += messages[0] + '<br>';
                        });
                    }
                    $('#suppliererrorMessage').html(errorMessage).show();
                }
            });
        }
    });
});