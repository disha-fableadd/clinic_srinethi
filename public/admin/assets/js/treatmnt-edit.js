$(document).ready(function() {

    $('#editTreatmentForm').validate({
        rules: {
            name: {
                required: true,
                minlength: 3
            },
            doctor_id: {
                required: true
            },
            price: {
                required: true
            },

        },
        messages: {
            name: {
                required: "Please enter the treatment name",
                minlength: "Treatment name must be at least 3 characters long"
            },
            doctor_id: {
                required: "Please select a doctor"
            },
            price: {
                required: "Please enter price"
            },
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

    // Get treatmentId from Blade or some other dynamic source
    let treatmentId = "{{ $treatment_id }}"


    if (!treatmentId || treatmentId === "") {
        console.warn("No treatment ID provided.");
        return;
    }

    fetchTreatmentDetails(treatmentId);

    function fetchTreatmentDetails(treatmentId) {
        $.ajax({
            url: `/api/treatments/${treatmentId}`,
            type: 'GET',
            headers: {
                "Authorization": "Bearer " + token
            },
            success: function(data) {
                if (data && data.name) {
                    $("#name").val(data.name);
                    $("#price").val(data.price);
                    $("#description").val(data.description);
                    loadDoctors(data.doctor_id);
                } else {
                    console.warn("Treatment data not found.");
                }
            },
            error: function(xhr, status, error) {
                console.error("Error fetching treatment:", status, error);
                console.log("Response:", xhr.responseText);
            }
        });
    }

    function loadDoctors(selectedDoctorId) {
        let branchId = localStorage.getItem("selectedBranchId");
        $.ajax({
            url: "/api/doctors",
            type: "GET",
            dataType: "json",
            data: {
                branch_id: branchId // ✅ send branch_id
            },
            headers: {
                "Authorization": "Bearer " + token
            },
            success: function(response) {
                let doctorDropdown = $('.doctorSelect');

                if (doctorDropdown.length === 0) {
                    console.error("Dropdown not found! Check your HTML.");
                    return;
                }

                doctorDropdown.empty().append(
                    '<option value="">Select Doctor</option>');

                let doctorsList = response.doctors || [];

                if (doctorsList.length === 0) {
                    console.warn("No doctors found.");
                    return;
                }

                $.each(doctorsList, function(index, doctor) {
                    // Capitalize first letter only
                    const capitalizedName = doctor.fullname.charAt(0)
                        .toUpperCase() + doctor
                        .fullname.slice(1);

                    doctorDropdown.append(
                        `<option value="${doctor.id}">${capitalizedName}</option>`
                    );
                });

                // ✅ Initialize Select2 plugin
                doctorDropdown.select2({
                    placeholder: "Select Doctor",
                    allowClear: true,
                    width: '100%'
                });

                // ✅ Set the selected doctor AFTER initializing Select2
                if (selectedDoctorId) {
                    doctorDropdown.val(selectedDoctorId).trigger('change');
                }

                // Optional: Set search input placeholder
                doctorDropdown.on('select2:open', function() {
                    $('.select2-search__field').attr('placeholder',
                        'Search Doctor');
                });
            },
            error: function(xhr) {
                console.error("API Error:", xhr.status, xhr.responseText);
            }
        });
    }



    // // Handle Update Submission
    $('#editTreatmentForm').submit(function(e) {
        let treatmentId = "{{ $treatment_id }}"
        e.preventDefault();
        let formData = {
            name: $("#name").val(),
            price: $("#price").val(),
            doctor_id: $("#doctorSelect").val(),
            description: $("#description").val(),
        };

        $.ajax({
            url: `/api/treatments/${treatmentId}`,
            type: 'PUT',
            contentType: 'application/json',
            headers: {
                "Authorization": "Bearer " + token
            },
            data: JSON.stringify(formData),
            success: function(response) {
                $("#editsuccesstreat").text("treatment updated successfully!").fadeIn()
                    .delay(700).fadeOut();
                setTimeout(function() {
                    window.location.href = "{{ route('treatment.index') }}";
                }, 2000);
            },
            error: function(xhr) {
                var errorMessage = '';
                if (xhr.status === 422) { // Validation error
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, messages) {
                        errorMessage += messages[0] + '<br>';
                    });
                }
                $('#editerrortreat').html(errorMessage).show();
            }

        });
    });
});