$(document).ready(function () {
    let token = localStorage.getItem('token');
    let soapId = "{{ $soap_id }}";

    $.ajax({
        url: '/api/soap/' + soapId,
        type: 'GET',
        headers: {
            "Authorization": "Bearer " + token
        },
        success: function (soap) {
            $('#soap_date').text(soap.date ?? '--');

            // Patient Name
            $('#patient_name').text(soap.patient?.fullname ?? '--');

            $('#subjective_title').text(soap.subjective?.title ?? '--');
            $('#subjective_description').text(soap.subjective?.description ?? '--');

            $('#objective_title').text(soap.objective?.title ?? '--');
            $('#objective_description').text(soap.objective?.description ?? '--');

            $('#assessment_title').text(soap.assessment?.title ?? '--');
            $('#assessment_description').text(soap.assessment?.description ?? '--');

            $('#plan_title').text(soap.plan?.title ?? '--');
            $('#plan_description').text(soap.plan?.description ?? '--');
        },

        error: function (xhr) {
            console.error("Failed to fetch SOAP:", xhr.responseText);
        }
    });

    $(document).on('click', '.delete-soap', function () {
        if (!confirm('Are you sure you want to delete this SOAP?')) return;

        $.ajax({
            url: '/api/soap/' + soapId,
            type: 'DELETE',
            headers: {
                "Authorization": "Bearer " + token
            },
            success: function () {
                alert('SOAP deleted successfully!');
                window.location.href = "{{ route('soap.index') }}";
            },
            error: function (xhr) {
                alert('Failed to delete SOAP.');
            }
        });
    });
});

$(document).on('click', '.download-soap', function () {
    const soapId = $(this).data('id');

    $.ajax({
        url: `/api/soaps/${soapId}/download`,
        type: 'GET',
        xhrFields: {
            responseType: 'blob' // handle file download
        },
        success: function (data, status, xhr) {
            // Create Blob & trigger download
            const blob = new Blob([data], { type: xhr.getResponseHeader('Content-Type') });
            const link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = `soap_${soapId}.pdf`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        },
        error: function (xhr) {
            if (xhr.status === 404) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Not Found',
                    text: 'The requested SOAP PDF does not exist.',
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Download Failed',
                    text: 'Something went wrong while downloading the SOAP file.',
                });
            }
        }
    });
});