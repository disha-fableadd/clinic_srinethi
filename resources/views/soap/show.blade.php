@extends('layout.app')

@section('content')
    <style>
        .soap-card {
            max-width: 1300px;
            /* margin: 40px auto; */
            background: white;
            border-radius: 20px;
            padding: 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .soap-header {
            color: black;
            background-color: #87ceb0;
            padding: 16px 20px;
            font-weight: 600;
            font-size: 1.5rem;
        }

        .soap-body {
            padding: 20px;
        }

        .soap-field {
            display: flex;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #c8e6c9;
        }

        .soap-field:last-child {
            border-bottom: none;
        }

        .soap-field i {
            width: 25px;
            color: #87ceb0;
            margin-right: 12px;
            font-size: 1.1rem;
        }

        .soap-label {
            font-weight: 500;
            min-width: 130px;
        }

        .soap-value {
            flex: 1;
            word-break: break-word;
        }

        .back-btn {
            margin-bottom: 15px;
        }

        .action-buttons {
            text-align: right;
            padding: 20px;
            background-color: white;
        }

        .action-buttons .btn {
            margin-left: 8px;
            min-width: 80px;
        }

        .icon-style2 {
            color: white
        }

        a.btn.btn-primary.back {
            margin-left: 57rem;
            margin-top: 20px;
        }
        .card-footer{
        background-color:#87ceb0 !important;
    }
    </style>

    <div class="page-wrapper">
        <div class="content">

            <div class="row mt-3" style="padding-top:15px">

               

            </div>

            <div class="soap-card mt-2">
                <div class="card-footer text-right">
                    <h3 style="float:left" class="text-dark">
                        <i class="fa fa-info-circle icon-style2 text-white"></i>
                        <span class="Patient_name"></span> SOAP' s Details
                    </h3>
                    <div class="d-flex text-right" style="justify-content: end;">

                        <div class="mr-2 m-b-2">
                            <a href="javascript:void(0)" class="btn btn-primary btn-rounded btn-hdr download-soap"
                                data-id="{{ $soap_id}}">
                                <i class="fa fa-download"></i> <span class="hdr-btn-text">PDF</span>
                            </a>
                        </div>


                        @if (app('hasPermission')(32, 'view'))
                            <div class="  m-b-2">
                                <a href="{{ route('soap.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                                    <i class="fa fa-arrow-left"></i> <span class="hdr-btn-text">Back</span>
                                </a>
                            </div>
                        @endif
                    </div>

                </div>

                <div class="soap-body">
                    <div class="soap-field">
                        <i class="fa fa-user-circle"></i>
                        <div class="soap-label">Patient Name:</div>
                        <div class="soap-value" id="patient_name">--</div>
                    </div>

                    <div class="soap-field">
                        <i class="fa fa-calendar"></i>
                        <div class="soap-label">Date:</div>
                        <div class="soap-value" id="soap_date">--</div>
                    </div>

                    <div class="soap-field">
                        <i class="fa fa-user"></i>
                        <div class="soap-label">Subjective:</div>
                        <div class="soap-value">
                            <strong>Title:</strong> <span id="subjective_title">--</span><br>
                            <strong>Description:</strong> <span id="subjective_description">--</span>
                        </div>
                    </div>

                    <div class="soap-field">
                        <i class="fa fa-eye"></i>
                        <div class="soap-label">Objective:</div>
                        <div class="soap-value">
                            <strong>Title:</strong> <span id="objective_title">--</span><br>
                            <strong>Description:</strong> <span id="objective_description">--</span>
                        </div>
                    </div>

                    <div class="soap-field">
                        <i class="fa fa-stethoscope"></i>
                        <div class="soap-label">Assessment:</div>
                        <div class="soap-value">
                            <strong>Title:</strong> <span id="assessment_title">--</span><br>
                            <strong>Description:</strong> <span id="assessment_description">--</span>
                        </div>
                    </div>

                    <div class="soap-field">
                        <i class="fa fa-pencil-alt"></i>
                        <div class="soap-label">Plan:</div>
                        <div class="soap-value">
                            <strong>Title:</strong> <span id="plan_title">--</span><br>
                            <strong>Description:</strong> <span id="plan_description">--</span>
                        </div>
                    </div>
                </div>

                <div class="action-buttons">
                    <a href="{{ route('soap.edit', $soap_id) }}" class="btn btn-primary">
                        <i class="fa fa-edit"></i> <span class="hdr-btn-text">Edit</span>
                    </a>
                    <button class="btn btn-danger delete-soap" data-id="{{ $soap_id }}" style="border-radius: 50px;">
                        <i class="fa fa-trash"></i> <span class="hdr-btn-text">Delete</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
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

    </script>
@endsection