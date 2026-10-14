@extends('layout.app')

<style>
    @media screen and (max-width:767px) {
        .page-title {
            font-size: 20px !important;
        }
    }

    .icon-style1 {
        background-color: white;
        color: #9dc3b3;
        padding: 5px;
        font-size: 20px;
        border-radius: 50%;
    }

    .icon-style2 {
        color: white;
        padding: 5px;
        font-size: 20px;
        border-radius: 50%;
    }
    .card-footer{
        background-color:#87ceb0 !important;
    }
</style>

@section('content')
    <div class="page-wrapper">
        <div class="content" style="height:100vh">
            <div class="row" style="padding-top:15px">
                <div class=" col-8">
                    <h4 class="page-title"><i class="fa fa-file-medical"></i> Pathology Report Details</h4>
                </div>
                @if(app('hasPermission')(20, 'view'))
                    <div class=" col-4 text-right m-b-2">
                        <a href="{{ route('pathology_reports.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                            <i class="fa fa-arrow-left"></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    </div>
                @endif
            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-footer text-right">

                            <h3 class="text-dark" style="float:left"><i class="fa fa-info-circle icon-style2"></i> <span
                                    class="patient-name"></span>'s <span class="test-name"></span> Details</h3>
                        </div>


                        <div class="card-body mt-3">

                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="row">
                                        <div class="col-lg-6 col-4 text-center margin-img">
                                            <p class="text-dark">
                                                <img id="file" src="" class="userProfile" width="200" height="200"
                                                    alt="Patient Image" style="border-radius:20px"
                                                    onerror="this.onerror=null;this.src='{{ asset(env('IMAGE_PATH') . 'admin/assets/img/img1.png') }}';">
                                            </p>
                                        </div>
                                        <div class="col-lg-6 col-8">
                                            <p><strong><i class="fa fa-user icon-style1"></i> Patient: </strong>
                                                <span class="patient-name"></span>
                                            </p>
                                            <hr class="margin-text">
                                            <p><strong><i class="fa fa-user-md icon-style1"></i> Test Name: </strong> <span
                                                    class="test-name"></span></p>
                                            <hr class="margin-text">
                                            <p><strong><i class="fa fa-calendar icon-style1"></i> Sample Collected Date:
                                                </strong> <span class="sample-date"></span></p>
                                            <hr class="margin-text">

                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <p><strong><i class="fa fa-calendar-alt icon-style1"></i> Report Date: </strong> <span
                                            class="report-date"></span></p>
                                    <hr class="margin-text">
                                    <p><strong><i class="fa fa-notes-medical icon-style1"></i> Result: </strong> <span
                                            class="result"></span></p>
                                    <hr class="margin-text">
                                    <p><strong><i class="fa fa-file-pdf icon-style1"></i> Report File: </strong>
                                        <a class="report-file" href="#" target="_blank">View PDF</a>
                                    </p>
                                    <hr class="margin-text">
                                </div>
                            </div>

                            <div class="button mt-4 mb-4" style="display: flex; justify-content: end;">
                                @if(app('hasPermission')(20, 'update'))
                                    <a href="#" class="btn btn-primary btn-rounded btn-hdr edit-report-btn"
                                        style="margin-right:10px; color:black">
                                        <i class="fa fa-pencil-alt"></i> <span class="hdr-btn-text">Edit</span>
                                    </a>
                                @endif
                                @if(app('hasPermission')(20, 'delete'))
                                    <button type="button" class="btn btn-danger btn-rounded btn-hdr delete-report"
                                        data-id="{{ $pathology_report_id }}">
                                        <i class="fa fa-trash"></i> <span class="hdr-btn-text">Delete</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="successMessage" class="alert alert-success" style="display:none;"></div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            var reportId = "{{ $pathology_report_id }}";

            $.ajax({
                url: '/api/pathology-reports/' + reportId,
                type: 'GET',
                success: function (report) {
                    $('.report-id').text(report.id);
                       $("#file").attr("src", report.patient?.profile || '');
                    $('.patient-name').text(report.patient?.fullname ?? 'N/A');
                    $('.test-name').text(report.test?.test_name ?? 'N/A');
                    $('.sample-date').text(report.sample_collected_date ?? 'N/A');
                    $('.report-date').text(report.report_date ?? 'N/A');
                    $('.result').text(report.result ?? 'N/A');

                    if (report.report_file) {
                        $('.report-file').attr('href', '/' + report.report_file);
                    } else {
                        $('.report-file').text('No File').removeAttr('href');
                    }

                    $('.edit-report-btn').attr('href', '/pathology_reports/edit/' + report.id);
                },
                error: function () {
                    alert('Failed to fetch report details.');
                }
            });

            $(document).on('click', '.delete-report', function () {
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to undo this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/api/pathology-reports/' + id,
                            type: 'DELETE',
                            success: function (response) {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'Report deleted successfully!',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.href = "{{ route('pathology-reports.index') }}";
                                });
                            },
                            error: function () {
                                Swal.fire('Error', 'Failed to delete the report.', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection