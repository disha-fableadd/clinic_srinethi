$('.patient-id').select2({
    placeholder: "Select Patient",
    width: '100%'
});
$('.patient-id').on('select2:open', function() {
    $('.select2-search__field').attr('placeholder', 'Search Patient');
});


$(document).ready(function() {
    let token = localStorage.getItem('token');
    let branchId = localStorage.getItem('selectedBranchId'); // ✅ get branch ID

    // 🔹 First load patients into dropdown based on branch ID
    $.ajax({
        url: "/api/patientss", // your branch-wise patient API
        type: "GET",
        data: {
            branch_id: branchId // send branch ID to API
        },
        dataType: "json",
        xhrFields: {
            withCredentials: true // send session cookie if using Sanctum
        },
        success: function(response) {
            let patients = response.patients ?? response;

            let patientDropdown = $('#patient_id');
            patientDropdown.empty().append(
                '<option value="">-- Select Patient --</option>');

            if (!patients || patients.length === 0) {
                console.warn("No patients found for this branch.");
                return;
            }

            $.each(patients, function(index, patient) {
                let name = patient.fullname;
                let formattedName = name.charAt(0).toUpperCase() + name
                    .slice(1);

                patientDropdown.append(
                    `<option value="${patient.id}">${formattedName}</option>`
                );
            });

            // Initialize Select2
            patientDropdown.select2({
                placeholder: "Search Patient...",
                allowClear: true,
                width: '100%'
            });

            patientDropdown.on('select2:open', function() {
                $('.select2-search__field').attr('placeholder',
                    'Search Patient');
            });

            // 🔹 After patients are loaded, load SOAP data
            loadSoapData();
        },
        error: function(xhr) {
            console.error("Failed to load patients:", xhr.responseText);
        }
    });

    // 🔹 Fetch SOAP and prefill including patient
    function loadSoapData() {
        $.ajax({
            url: '/api/soap/' + soapId,
            type: 'GET',
            headers: {
                "Authorization": "Bearer " + token
            },
            success: function(soap) {
                $('#soap_id').val(soap.id);

                // ✅ Select patient in dropdown + refresh Select2
                $('#patient_id').val(soap.patient_id).trigger('change');

                $('#date').val(soap.date ?? '');
                $('#subjective_title').val(soap.subjective?.title ?? '');
                $('#subjective_description').val(soap.subjective?.description ??
                    '');
                $('#objective_title').val(soap.objective?.title ?? '');
                $('#objective_description').val(soap.objective?.description ?? '');
                $('#assessment_title').val(soap.assessment?.title ?? '');
                $('#assessment_description').val(soap.assessment?.description ??
                    '');
                $('#plan_title').val(soap.plan?.title ?? '');
                $('#plan_description').val(soap.plan?.description ?? '');
            },
            error: function(xhr) {
                console.error("Failed to fetch SOAP:", xhr.responseText);
            }
        });
    }

    // 🔹 Submit updated SOAP
    $('#editSoapForm').submit(function(e) {
        e.preventDefault();

        let formData = {
            patient_id: $('#patient_id').val(),
            date: $('#date').val(),
            subjective_title: $('#subjective_title').val(),
            subjective_description: $('#subjective_description').val(),
            objective_title: $('#objective_title').val(),
            objective_description: $('#objective_description').val(),
            assessment_title: $('#assessment_title').val(),
            assessment_description: $('#assessment_description').val(),
            plan_title: $('#plan_title').val(),
            plan_description: $('#plan_description').val(),
        };

        $.ajax({
            url: '/api/soap/' + soapId,
            type: 'PUT',
            headers: {
                "Authorization": "Bearer " + token
            },
            data: formData,
            success: function(response) {
                $('#soapSuccessMessage').text('SOAP updated successfully!')
                    .show();
                $('#soapErrorMessage').hide();
                setTimeout(() => window.location.href = '/soap', 1500);
            },
            error: function(xhr) {
                $('#soapErrorMessage').text(xhr.responseJSON?.message ||
                    'Failed to update SOAP.').show();
                $('#soapSuccessMessage').hide();
            }
        });
    });
});