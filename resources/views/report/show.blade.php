@extends('layout.app')

<style>
    .card-footer {
        background-color: #87ceb0 !important;
    }

    @media screen and (max-width: 767px) {
        .page-title {
            font-size: 19px !important;
            padding-left: 10px !important;
            text-align: left !important;
            padding-top: 6px !important;
        }
    }
</style>

@section('content')
    <div class="page-wrapper">
        <div class="content" style="height:100vh">
            <div class="row" style="padding-top: 15px;">

            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-footer text-right">
                            <h3 style="float:left" class="text-dark">
                                <i class="fa fa-info-circle icon-style2"></i>
                                <span class="">Report Details</span>
                            </h3>

                            <div class="d-flex text-right" style="justify-content: end;">

                                <div class="mr-2 m-b-2">
                                    <a href="javascript:void(0)" class="btn btn-primary btn-rounded btn-hdr download-report"
                                        data-id="{{ $report_id}}">
                                        <i class="fa fa-download"></i> <span class="hdr-btn-text">PDF</span>
                                    </a>
                                </div>


                                @if (app('hasPermission')(13, 'view'))
                                    <div class="text-right m-b-2">
                                        <a href="{{ route('report.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                                            <i class="fa fa-arrow-left m-r-5 icon3"></i> <span class="hdr-btn-text">Back</span></a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="card-body mt-3">




                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="row">
                                        <div class="col-lg-6 col-4 text-center margin-img">
                                            <p class="text-dark">
                                                <!-- <strong><i class="fa fa-image icon-style1 "></i> File: </strong><br> -->
                                                <img id="file" src="" class="userProfile" width="200" height="200"
                                                    alt="report Image" style="border-radius:20px">
                                            </p>
                                        </div>
                                        <div class="col-lg-6 col-8">
                                            <p class="text-dark margin-text">
                                                <strong><i class="fa fa-id-badge icon-style1"></i> Patient : </strong>
                                                <span id="patient"></span>
                                            </p>

                                            <hr class="margin-text">
                                            <p class="text-dark margin-text">
                                                <strong><i class="fa fa-id-badge icon-style1"></i> Date : </strong>
                                                <span id="date"></span>
                                            </p>

                                            <hr class="margin-text">


                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">

                                    <p class="text-dark margin-text">
                                        <strong><i class="fa fa-id-badge icon-style1"></i>Report Type: </strong>
                                        <span id="type"></span>
                                    </p>

                                    <hr class="margin-text">
                                    <p class="text-dark">
                                        <strong><i class="fa fa-align-left icon-style1"></i> Description: </strong>
                                        <span id="description"></span>
                                    </p>
                                    <hr>

                                </div>
                            </div>




                            <div class="button mb-4" style="display: flex; justify-content: end; margin: 0 5px;">
                                <button type="button" class="btn btn-sm btn-primary generate-report-link-btn"
                                    data-id="{{ $report_id }}" data-amount="" data-status=""
                                    style="margin-right:10px;border-radius:50px;">
                                    <i class="fa fa-link"></i> Generate Payment Link
                                </button>

                                @if (app('hasPermission')(13, 'update'))
                                    <a href="#" class="btn btn-primary btn-rounded btn-hdr edit-report-btn"
                                        style="color:black; margin-right:10px">
                                        <i class="fa fa-pencil-alt"></i> <span class="hdr-btn-text">Edit</span>
                                    </a>
                                @endif
                                @if (app('hasPermission')(13, 'delete'))
                                    <button type="button" class="btn btn-danger btn-rounded btn-hdr delete-report"
                                        data-id="{{ $report_id }}">
                                        <i class="fa fa-trash"></i> <span class="hdr-btn-text">Delete</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div id="successMessage" class="alert alert-success" style="display:none;"></div>


            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
        </div>
    </div>



    <script>
        $(document).ready(function () {
            var reportId = "{{ $report_id }}";

            $.ajax({
                url: "/api/medical-reports/" + reportId,
                method: "GET",
                dataType: "json",
                headers: {
                    "Authorization": "Bearer " + token
                },
                success: function (response) {
                    if (response.success) {
                        var report = response.data;

                        // Image handling
                        let defaultImage = "{{ asset(env('IMAGE_PATH') . 'admin/assets/img/repot.jpg') }}";

                        $("#file")
                            .attr("src", report.file_path || defaultImage)
                            .on('error', function () {
                                $(this).attr("src", defaultImage);
                            });
                        // Capitalize first letter
                        function ucfirst(str) {
                            return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
                        }

                        // Populate basic data
                        $("#patient").text(ucfirst(report.patient.fullname));
                        $("#date").text(ucfirst(report.date));
                        $("#description").text(ucfirst(report.description));

                        $("#type").text(report.report_type.split('T')[0]);
                        $('.generate-report-link-btn')
                            .data('id', report.id)
                            .data('amount', report.amount)
                            .data('status', report.payment_status);


                        // Edit button
                        $(".edit-report-btn").attr("href", "/report/edit/" + report.id);
                    } else {
                        alert("Failed to fetch report details");
                    }
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                    alert("Error fetching report details");
                }
            });




            $(document).on('click', '.download-report', function () {
                const reportId = $(this).data('id');



                $.ajax({
                    url: `/api/medical-reports/${reportId}/download`,
                    method: 'GET',
                    xhrFields: {
                        responseType: 'blob' // important for PDF
                    },
                    success: function (response, status, xhr) {
                        Swal.close(); // close loading

                        // Extract filename from headers if provided
                        let filename = `MedicalReport_${reportId}.pdf`;
                        const disposition = xhr.getResponseHeader('Content-Disposition');
                        if (disposition && disposition.indexOf('filename=') !== -1) {
                            filename = disposition.split('filename=')[1].replace(/"/g, '');
                        }

                        // Create a blob link to download
                        const blob = new Blob([response], { type: 'application/pdf' });
                        const link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.download = filename;
                        link.click();
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Failed to download PDF.'
                        });
                    }
                });
            });



            $(document).on('click', '.generate-report-link-btn', function () {

                let reportId = $(this).data('id');
                let status = $(this).data('status');

                // ✅ Already paid
                if (status === 'paid') {
                    Swal.fire({
                        icon: 'info',
                        title: 'No Pending Amount',
                        text: 'This report payment is already completed.'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Generate Payment Link',
                    html: `
                <label style="float:left;margin-left:40px;">Enter Amount (₹)</label>
                <input
                    type="number"
                    id="paymentAmount"
                    class="swal2-input"
                    placeholder="Enter amount"
                    min="1"
                    style="width:80%;margin:10px auto;"
                >
            `,
                    showCancelButton: true,
                  confirmButtonText: '<span style="color:black">Generate Link</span>',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#cfece0',
                  
                    cancelButtonColor: '#f89884',
                    preConfirm: () => {
                        const amount = document.getElementById('paymentAmount').value;
                        if (!amount || amount <= 0) {
                            Swal.showValidationMessage('Please enter a valid amount');
                        }
                        return amount;
                    }
                }).then((result) => {

                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: `/api/razorpay/report/generate-payment-link`,
                        type: 'POST',
                        data: {
                            report_id: reportId,
                            amount: result.value,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (res) {

                            navigator.clipboard.writeText(res.payment_link);

                            Swal.fire({
                                icon: 'success',
                                title: 'Payment Link Generated',
                                html: `
                            <p><strong>Patient Name:</strong> ${res.patient_name}</p>
                            <p><strong>Contact No:</strong> ${res.patient_phone}</p>
                            <p><strong>Amount:</strong> ₹${res.amount}</p>

                            <input
                                type="text"
                                value="${res.payment_link}"
                                readonly
                                style="
                                    width:100%;
                                    padding:12px;
                                    margin-top:15px;
                                    border-radius:25px;
                                    border:1.5px solid #ff8c6b;
                                    font-size:18px;
                                "
                            >

                            <div style="text-align:center;font-size:13px;margin-top:6px;">
                                Link copied to clipboard ✔
                            </div>
                        `,
                                showConfirmButton: false,
                                showCancelButton: true,
                                cancelButtonText: 'Close',
                                cancelButtonColor: '#f89884'
                            }).then(() => location.reload());
                        },
                        error: function (xhr) {
                            Swal.fire({
                                icon: xhr.status === 403 ? 'warning' : 'error',
                                title: xhr.status === 403 ? 'Razorpay Disabled' : 'Error',
                                text: xhr.responseJSON?.message || 'Failed to generate payment link',
                                showConfirmButton: false,
                                showCancelButton: true,
                                cancelButtonText: 'Close'
                            });
                        }
                    });
                });
            });











            $(".delete-report").on("click", function () {
                var reportId = $(this).data('id'); // Get reportId from button's data attribute

                if (!reportId) {
                    Swal.fire('Error', 'Report ID not found!', 'error');
                    return;
                }

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This action cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/api/medical-reports/" + reportId,
                            method: "DELETE",
                            success: function (response) {
                                $('button[data-id="' + reportId + '"]').closest('tr')
                                    .remove();

                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'Report deleted successfully!',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.href =
                                        "{{ route('report.index') }}";
                                });
                            },
                            error: function () {
                                Swal.fire('Error',
                                    'An error occurred while deleting the report. Please try again.',
                                    'error');
                            }
                        });
                    }
                });
            });

        });
    </script>

    <style>
        /* .card-footer {
                                                                        border-top-left-radius: 20px;
                                                                        border-top-right-radius: 20px;
                                                                    } */

        .icon-style1 {
            background-color: white;
            color: rgb(157 195 179);
            padding: 5px;
            font-size: 20px;
            border-radius: 50%;
        }

        .icon-style2 {
            /* background-color: white; */
            color: white;
            padding: 5px;
            font-size: 20px;
            border-radius: 50%;
        }
    </style>
@endsection