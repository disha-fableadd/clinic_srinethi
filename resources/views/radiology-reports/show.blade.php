@extends('layout.app')
<style>
    .container {
        width: 60%;
        margin: auto;
    }

    .header1 {
        background: #cfece0;
        color: black;
        padding: 10px 15px;
        font-size: 18px;
        font-weight: bold;
        margin-top: 50px;
        border-radius: 15px;
    }

    .card-body {
        height: auto;
        padding: 20px !important;
    }

    .card {
        margin: 0;
    }

    .info-label {
        font-weight: bold;
        color: #333;
    }

    .info-value {
        color: #555;
    }
    .card-footer{
        background-color:#87ceb0 !important;
    }
</style>
@section('content')
    <div class="page-wrapper">
        <div class="content" style="height:100vh">
            <div class="row mt-3">
                <div class="col-sm-8 col-8">
                    <h4 class="page-title" style="text-align:left;">
                        Radiology Report Details
                    </h4>
                </div>
                @if(app('hasPermission')(22, 'view'))

                    <div class="col-sm-4 col-4 text-right m-b-2">
                        <a href="{{ route('radiology-reports.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                            <i class="fa fa-arrow-left"></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    </div>
                @endif

            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-footer text-right">
                            <h3 style="float:left" class="text-dark">
                                <i class="fa fa-info-circle icon-style2"></i>
                                <span>Test Information</span>
                            </h3>
                        </div>

                        <div class="card-body mt-3">


                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="row">
                                        <div class="col-lg-6 col-4 text-center margin-img">
                                            <p class="text-dark">

                                                <img id="file" class="userProfile" src="" width="200" height="200"
                                                    alt="report Image" style="border-radius:20px"
                                                    onerror="this.onerror=null;this.src='{{ asset(env('IMAGE_PATH') . 'admin/assets/img/img1.png') }}';">
                                            </p>
                                        </div>
                                        <div class="col-lg-6 col-8">
                                            <p class="text-dark margin-text">
                                                <strong><i class="fas fa-user-md icon-style1"></i> Patient Name: </strong>
                                                <span class="info-value" id="patientName">N/A</span>
                                            </p>
                                            <hr class="margin-text">

                                            <p class="text-dark margin-text">
                                                <strong><i class="fas fa-flask icon-style1"></i> Test Name: </strong>
                                                <span class="info-value" id="testName">N/A</span>
                                            </p>
                                            <hr class="margin-text">


                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <p class="text-dark margin-text">
                                        <strong><i class="fas fa-calendar-check icon-style1"></i> Report Date:
                                        </strong>
                                        <span class="info-value" id="reportDate">N/A</span>
                                    </p>
                                    <hr class="margin-text">

                                    <p class="text-dark margin-text">
                                        <strong><i class="fas fa-file-medical icon-style1"></i> Report File:</strong>
                                        <a class="report-file" id="reportFile" href="#" target="_blank"
                                            style="color: #007bff;">N/A</a>
                                    </p>

                                    <hr class="margin-text">

                                    <p class="text-dark margin-text">

                                </div>
                            </div>

                            <div class="button mb-4" style="display: flex; justify-content: end; margin: 0 5px;">
                                @if(app('hasPermission')(22, 'update'))
                                    <a href="#" class="btn btn-primary btn-rounded btn-hdr edit-test-btn"
                                        style="color:black; margin-right:10px">
                                        <i class="fa fa-pencil-alt"></i> <span class="hdr-btn-text">Edit</span>
                                    </a>
                                @endif

                                @if(app('hasPermission')(22, 'delete'))
                                    <button type="button" class="btn btn-danger btn-rounded btn-hdr delete-reports" data-id="">
                                        <i class="fa fa-trash"></i> <span class="hdr-btn-text">Delete</span>
                                    </button>

                                @endif



                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function getIdFromUrl() {
            const segments = window.location.pathname.split('/');
            return segments.pop() || segments.pop();
        }

        function renderReportData(report) {
            if (report.patient && report.patient.profile) {
                $('.userProfile').attr('src', report.patient.profile);
            } else {
                $('.userProfile').attr('src', "{{ asset(env('IMAGE_PATH') . 'admin/assets/img/img1.png') }}");
            }

            $('#patientName').text(report.patient ? report.patient.fullname : 'N/A');
            $('#testName').text(report.test ? report.test.test_name : 'N/A');
            $('#reportDate').text(report.report_date || 'N/A');

            if (report.converted_image) {
                let fileUrl = '/' + report.converted_image;

                $('#reportFile')
                    .attr('href', fileUrl)
                    .text('View Report')
                    .show();
            } else {
                $('#reportFile')
                    .removeAttr('href')
                    .text('N/A');
            }


            $(".edit-test-btn").attr("href", "/radiology-reports/edit/" + report.id);
            $(".delete-reports").attr("data-id", report.id);


        }


        function showNA() {

            $('#patientName').text('N/A');
            $('#testName').text('N/A');
            $('#reportDate').text('N/A');
            $('#reportFile').text('N/A');
        }

        $(document).ready(function () {
            const id = getIdFromUrl();
            $.ajax({
                url: `/api/radiology-reports/${id}`,
                method: 'GET',
                dataType: 'json',
                success: function (data) {
                    if (!data || $.isEmptyObject(data)) {
                        showNA();
                    } else {
                        renderReportData(data);
                    }
                },
                error: function () {
                    showNA();
                }
            });
        });

        $(document).on('click', '.delete-reports', function () {
            var testId = $(this).data('id');
            if (!testId) {
                Swal.fire('Error', 'Test ID is missing!', 'error');
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: "You want to delete this radiology test?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/api/radiology-reports/' + testId,
                        type: 'DELETE',
                        success: function (response) {
                            Swal.fire('Deleted!', 'Radiology test deleted successfully!', 'success')
                                .then(() => {
                                    window.location.href = "{{ route('radiology-reports.index') }}";
                                });
                        },
                        error: function (xhr) {
                            Swal.fire('Error', 'Failed to delete radiology test.', 'error');
                        }
                    });
                }
            });
        });



    </script>

@endsection