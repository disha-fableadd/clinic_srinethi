$(document).ready(function () {
    const token = localStorage.getItem('token');

    if (!supplierId || isNaN(supplierId)) {
        alert("Error: Supplier ID is missing or invalid.");
        return;
    }

    // Fetch supplier details
    $.ajax({
        url: "/api/suppliers/" + supplierId,
        type: "GET",
        headers: { "Authorization": "Bearer " + token },
        success: function (supplier) {
            $("#company_name").val(supplier.name);
            $("#contact_person").val(supplier.contact_person);
            $("#phone").val(supplier.phone);
            $("#email").val(supplier.email);
            $("#address").val(supplier.address);
        },
        error: function () {
            alert("Failed to fetch supplier details.");
        }
    });
    $("#editSupplierForm").validate({
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
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
        }
    });
    // Handle form submission for updating supplier
    $("#editSupplierForm").submit(function (event) {
        event.preventDefault();

        let supplierData = {
            name: $("#company_name").val(),
            contact_person: $("#contact_person").val(),
            phone: $("#phone").val(),
            email: $("#email").val(),
            address: $("#address").val(),
        };

        $.ajax({
            url: "/api/suppliers/" + supplierId,
            type: "PUT",
            data: JSON.stringify(supplierData),
            contentType: "application/json",
            headers: { "Authorization": "Bearer " + token },
            success: function (response) {
                $("#editsuppliersuccessMessage").text("Supplier updated successfully!").fadeIn().delay(3000).fadeOut();
                setTimeout(function () {
                    window.location.href = "/supplier";
                }, 2000);
            },
            error: function (xhr) {
                var errorMessage = '';
                if (xhr.status === 422) { // Validation error
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function (key, messages) {
                        errorMessage += messages[0] + '<br>';
                    });
                }
                $('#editsuppliererrorMessage').html(errorMessage).show();
            }

        });
    });
});