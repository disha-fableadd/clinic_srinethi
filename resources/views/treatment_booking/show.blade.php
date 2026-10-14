@extends('layout.app')

<style>
    @media screen and (max-width:767px) {
        .page-title {
            font-size: 16px !important;
            margin-top: 8px !important;
        }

        .table-responsive-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            /* Smooth scroll on iOS */
        }

        .table-responsive-wrapper table {
            width: 600px;
            /* or more, depending on number of columns */
            min-width: 100%;
            display: block;
        }
    }

    .card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
    }

    /* Buttons container */
    .card-footer .d-flex {
        display: flex !important;
        justify-content: flex-end;
        flex-wrap: wrap;
        /* wrap if no space */
        gap: 8px;
        /* space between buttons */
    }

    /* Ensure buttons look good */
    .card-footer .btn {
        font-size: 13px;
        padding: 6px 12px;
        white-space: nowrap;
        /* prevent breaking text */
    }

    .card-footer {
        background-color: #87ceb0 !important;
    }

    /* Small phones (iPhone SE, Galaxy S8, etc.) */
    @media (max-width: 414px) {
        .card-footer {
            flex-direction: column;
            /* stack title + buttons */
            text-align: center;
        }

        .card-footer h3 {
            float: none !important;
            /* cancel float */
            margin-bottom: 10px;
            font-size: 14px;
        }

        .card-footer .d-flex {
            justify-content: center;
            /* center buttons */
        }
    }
</style>


@section('content')
    <div class="page-wrapper">
        <div class="content" style="height:100vh">
            <div class="row mt-2" style="padding-top:15px">

            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card">

                        <div class="card-footer text-right">
                            <h3 style="float:left" class="text-dark">
                                <i class="fa fa-info-circle icon-style2"></i>
                                <span>Treatment Booking Details</span>
                            </h3>


                            <div class="d-flex text-right" style="justify-content: end;">

                                <div class="mr-2 m-b-2">
                                    <a href="javascript:void(0)"
                                        class="btn btn-primary btn-rounded btn-hdr download-treatment-booking"
                                        data-id="{{ $treatment_booking_id }}">
                                        <i class="fa fa-download"></i> <span class="hdr-btn-text">PDF</span>
                                    </a>
                                </div>


                                @if (app('hasPermission')(23, 'view'))
                                    <div class="text-right m-b-2">
                                        <a href="{{ route('treatment_booking.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                                            <i class="fa fa-arrow-left"></i> <span class="hdr-btn-text">Back</span>
                                        </a>
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
                                                <img id="file" class="userProfile" src="" width="200" height="200"
                                                    alt="report Image" style="border-radius:20px"
                                                    onerror="this.onerror=null;this.src='{{ asset(env('IMAGE_PATH') . 'admin/assets/img/img1.png') }}';">
                                            </p>
                                        </div>
                                        <div class="col-lg-6 col-8">
                                            <p class="text-dark">
                                                <strong><i class="fas fa-user-injured icon-style1"></i> Patient:</strong>
                                                <span id="patient"></span>
                                            </p>
                                            <hr class="margin-text">
                                            <p class="text-dark">
                                                <strong><i class="fas fa-stethoscope icon-style1"></i> Treatment:</strong>
                                                <span id="treatment"></span>
                                            </p>
                                            <hr class="margin-text">
                                            <p class="text-dark">
                                                <strong><i class="fas fa-cogs icon-style1"></i> Machine:</strong>
                                                <span id="machine"></span>
                                            </p>
                                            <hr class="margin-text">
                                            <p class="text-dark">
                                                <strong><i class="fas fa-calendar-day icon-style1"></i> Plan:</strong>
                                                <span id="plan"></span>
                                            </p>
                                            <hr class="margin-text">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <p class="text-dark">
                                        <strong><i class="fas fa-dollar-sign icon-style1"></i> Remaining Amount:</strong>
                                        <span id="remain_amount"></span>
                                    </p>
                                    <hr class="margin-text">
                                    <p class="text-dark">
                                        <strong><i class="fas fa-info-circle icon-style1"></i> Status:</strong>
                                        <span id="status"></span>
                                    </p>
                                    <hr class="margin-text">
                                    <p class="text-dark">
                                        <strong><i class="fas fa-calendar-alt icon-style1"></i> Payment Date:</strong>
                                        <span id="payment_date"></span>
                                    </p>
                                    <hr class="margin-text">



                                </div>
                            </div>


                            <div class="button mb-4" style="display: flex; justify-content: end; margin: 0 5px;">
                                <button type="button" class="btn btn-primary btn-rounded btn-hdr generate-link-btn"
                                    style="color:black; margin-right:10px" data-id="{{ $treatment_booking_id }}"
                                    data-pending="{{ $remaining_amount ?? 0 }}">
                                    
                                    <i class="fa fa-link"></i> Generate Payment Link
                                </button>
                                @if (app('hasPermission')(31, 'update'))
                                    <a href="#" class="btn btn-primary btn-rounded btn-hdr edit-appointment-btn"
                                        style="color:black; margin-right:10px">
                                        <i class="fa fa-pencil-alt"></i> <span class="hdr-btn-text">Edit</span>
                                    </a>
                                @endif
                                @if (app('hasPermission')(31, 'delete'))
                                    <button type="button" class="btn btn-danger btn-rounded btn-hdr delete-report"
                                        data-id="{{ $treatment_booking_id }}">
                                        <i class="fa fa-trash"></i> <span class="hdr-btn-text">Delete</span>
                                    </button>
                                @endif


                            </div>
                        </div>



                    </div>
                </div>
            </div>




            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-footer text-right" style="background-color:#87ceb0">
                            <h3 style="float:left" class="text-dark">
                                <i class="fa fa-info-circle icon-style2 text-white"></i>
                                <span class="Patient_name">
                                    {{-- Show patient name dynamically if available --}}
                                    @if (isset($patientName))
                                        {{ \Illuminate\Support\Str::ucfirst($patientName) . "'s Payment History" }}
                                    @else
                                        Payment History
                                    @endif
                                </span>
                            </h3>
                        </div>

                        <div class="card-body mt-3">
                            <ul class="nav nav-tabs" id="paymentTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="payment-history-tab" data-toggle="tab"
                                        href="#payment-history" role="tab">Payment History</a>
                                </li>
                            </ul>
                            <div class="tab-content mt-3" id="paymentTabsContent">
                                <div class="tab-pane active show" id="payment-history" role="tabpanel">
                                    <div class="table-responsive-wrapper">
                                        <table class="table table-bordered" id="paymentTable">
                                            <thead>
                                                <tr>
                                                    <th>Payment Date</th>
                                                    <th>Total Amount</th>
                                                    <th>Amount Paid</th>
                                                    <th>Payment Mode</th>
                                                    <th>Paid Type</th>


                                                </tr>
                                            </thead>
                                            <tbody id="paymentRecords">
                                                {{-- Records will be loaded here by AJAX --}}
                                            </tbody>
                                        </table>
                                        <div id="noPaymentHistory" class="alert alert-info text-center"
                                            style="display:none;">
                                            No payment history available.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
        </div>
    </div>

    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css">
    <script src=" https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        window.treatmentBookingId = "{{ $treatment_booking_id }}";
        window.accessToken = @json(session('access_token'));
        window.defaultImage = "{{ asset(env('IMAGE_PATH') . 'admin/assets/img/img1.png') }}";
        window.treatmentBookingIndexRoute = "{{ route('treatment_booking.index') }}";



        $(document).on('click', '.generate-link-btn', function () {

            let bookingId = $(this).data('id');
            let pendingAmount = Number($(this).data('pending')); // convert to number explicitly

            if (isNaN(pendingAmount) || pendingAmount <= 0) {
                Swal.fire('Info', 'No pending amount for payment.', 'info');
                return;
            }


            $.ajax({
                url: `/api/treatment_booking/generate-payment-link-treatment`,
                type: 'POST',
                data: {
                    booking_id: bookingId,
                    amount: pendingAmount,
                    _token: '{{ csrf_token() }}'
                },
                success: function (res) {
                    navigator.clipboard.writeText(res.payment_link);

                    Swal.fire({
                        icon: 'success',
                        title: 'Payment Link Generated',
                        html: `
                                <p><strong>Patient Name:</strong> ${res.patient_name ?? '-'}</p>
                                <p><strong>Contact No:</strong> ${res.patient_phone ?? '-'}</p>
                                <p><strong>Amount:</strong> ₹${pendingAmount}</p>
                                <input type="text" class="swal2-input" value="${res.payment_link}" readonly>
                                <small>Link copied to clipboard ✔</small>
                            `,
                        showConfirmButton: false,
                        showCancelButton: true,
                        cancelButtonText: 'Close',
                        cancelButtonColor: '#f89884'
                    }).then((result) => {
                        if (result.dismiss === Swal.DismissReason.cancel) {
                            location.reload();
                        }
                    });
                },
                error: function (xhr) {
                    let message = xhr.responseJSON?.message || 'Failed to generate payment link';
                    Swal.fire({
                        icon: xhr.status === 403 ? 'warning' : 'error',
                        title: xhr.status === 403 ? 'Razorpay Disabled' : 'Error',
                        text: message,
                        showConfirmButton: false,
                        showCancelButton: true,
                        cancelButtonText: 'Close',
                        cancelButtonColor: '#f89884'
                    }).then((result) => {
                        if (result.dismiss === Swal.DismissReason.cancel) {
                            location.reload();
                        }
                    });
                }
            });
        });





    </script>


    <script src="{{ asset(env('IMAGE_PATH') . 'admin/assets/js/treatment-booking-show.js') }}"></script>

    <style>
        .icon-style1 {
            background-color: white;
            color: rgb(157 195 179);
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
    </style>
@endsection