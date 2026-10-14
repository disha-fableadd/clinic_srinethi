document.addEventListener('DOMContentLoaded', function() {
    let storedBranchId = localStorage.getItem('selectedBranchId');
    if (storedBranchId) {
        document.getElementById('branch_id').value = storedBranchId;
    }
});
$('#patient-id').select2({
    placeholder: "Select Patient",
    width: '100%'
});
$('#patient-id').on('select2:open', function() {
    $('.select2-search__field').attr('placeholder', 'Search Patient');
});


$(document).ready(function() {
    $('.patient-select').select2({
        placeholder: "Select Patient",
        allowClear: true
    });
});
$(document).ready(function() {
    const token = localStorage.getItem('token');
    $('#createSoapForm').validate({
        rules: {
            patient_id: {
                required: true
            },
            date: {
                required: true
            },
            subjective_title: {
                required: true
            },
            objective_title: {
                required: true
            },
            assessment_title: {
                required: true
            },
            plan_title: {
                required: true
            }
        },
        messages: {
            patient_id: "Please select a patient",
            date: "Please select a date",
            subjective_title: "Enter subjective title",
            objective_title: "Enter objective title",
            assessment_title: "Enter assessment title",
            plan_title: "Enter plan title"
        },
        errorElement: 'span',
        errorPlacement: function(error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },
        highlight: function(element) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function(element) {
            $(element).removeClass('is-invalid');
        }
    });

    // fetch patient
    let branchId = localStorage.getItem('selectedBranchId');


    $.ajax({
        url: "/api/patientss",
        type: "GET",
        data: {
            branch_id: branchId
        },
        dataType: "json",
        xhrFields: {
            withCredentials: true // ✅ send session cookie for Sanctum
        },
        success: function(response) {
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

            $.each(response.patients, function(index, patient) {
                const capitalizedName = patient.fullname.charAt(0).toUpperCase() +
                    patient.fullname.slice(1);

                patientDropdown.append(
                    `<option value="${patient.id}" data-treatment-id="${patient.treatment_id}">
                    ${capitalizedName}
                </option>`
                );
            });

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


    $('#createSoapForm').on('submit', function(e) {
        e.preventDefault();
        if (!$('#createSoapForm').valid()) return;

        let formData = $(this).serialize();

        $.ajax({
            url: '/api/soap',
            type: 'POST',
            data: formData,
            headers: {
                "Authorization": "Bearer " + token
            },
            success: function(response) {
                $('#soapSuccessMessage').text('SOAP created successfully!').show();
                setTimeout(() => {
                    window.location.href = "/soap";
                }, 1500);
            },
            error: function(xhr) {
                let errorMessage = 'Something went wrong';
                if (xhr.status === 422) {
                    errorMessage = '';
                    $.each(xhr.responseJSON.errors, function(key, messages) {
                        errorMessage += messages[0] + '<br>';
                    });
                }
                $('#soapErrorMessage').html(errorMessage).show();
            }
        });
    });
});