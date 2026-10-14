@extends('layout.app')


<style>
    .discharge-title {
        text-align: center;
        /* padding-left: 180px; */
    }

    .card-footer {
        background-color: #87ceb0 !important;
    }

    button.btn.btn-sm.btn-outline-success.generate-discharge-link-btn {
        margin-right: 10px;
        border-radius: 50px;
        color: black !important;
        background-color: #cfece0 !important;
        border-color: #cfece0 !important;
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
            <div class="row" style="padding-top:15px">
                <div class="col-sm-6 col-8">

                </div>
                @if (app('hasPermission')(10, 'view'))
                    <div class="col-sm-6 col-4 text-right m-b-2">
                        <a href="{{ route('discharge.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                            <i class="fa fa-arrow-left m-r-5 icon3"></i> <span class="hdr-btn-text">Back</span></a>
                    </div>
                @endif
            </div>


            <div class="row mt-3">
                <div class="col-md-12  col-sm-12 col-lg-12">
                    <div class="card">
                        <div class="card-footer text-right">
                            <h3 style="float:left" class="text-dark">
                                <i class="fa fa-info-circle icon-style2"></i>
                                <span class="">Discharge Details</span>
                            </h3>
                        </div>

                        <div class="card-body mt-3">

                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="row">
                                        <div class="col-lg-6 col-4 text-center margin-img">

                                            <p class="text-dark">
                                                <!-- <strong><i class="fa fa-image icon-style1 "></i> Patient Image: </strong><br> -->
                                                <img id="file" src="" class="userProfile" width="200" height="200"
                                                    alt="report Image" style="border-radius:20px"
                                                    onerror="this.onerror=null;this.src='{{ asset(env('IMAGE_PATH') . 'admin/assets/img/img1.png') }}';">
                                            </p>
                                        </div>
                                        <div class="col-lg-6 col-8">

                                            <p class="text-dark margin-text">
                                                <strong><i class="fas fa-user-injured icon-style1"></i> Patient :
                                                </strong>
                                                <span id="patient"></span>
                                            </p>

                                            <hr class="margin-text">
                                            <p class="text-dark margin-text">
                                                <strong><i class="fas fa-user-md icon-style1"></i> Doctor : </strong>
                                                <span id="doctor"></span>
                                            </p>

                                            <hr class="margin-text">
                                            <p class="text-dark margin-text">
                                                <strong><i class="fas fa-stethoscope icon-style1"></i> Treatment :
                                                </strong>
                                                <span id="treatment"></span>
                                            </p>

                                            <hr class="margin-text">
                                            <p class="text-dark margin-text">
                                                <strong><i class="fas fa-clock icon-style1"></i> Admit :
                                                </strong>
                                                <span id="admit"></span>
                                            </p>
                                            <hr class="margin-text">
                                            <p class="text-dark margin-text">
                                                <strong><i class="fas fa-calendar-day icon-style1"></i> Discharge :
                                                </strong>
                                                <span id="discharge"></span>
                                            </p>
                                            <hr class="margin-text">
                                            <p class="text-dark margin-text">
                                        <strong><i class="fas fa-check-circle icon-style"></i> Payment Status:
                                        </strong>
                                        <span id="payment"></span>
                                    </p>
                                    <hr class="margin-text">

                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <p class="text-dark margin-text">
                                        <strong><i class="fas fa-sticky-note icon-style"></i> Discharge Note:
                                        </strong>
                                        <span id="note"></span>
                                    </p>
                                    <hr class="margin-text">
                                    <p class="text-dark margin-text">
                                        <strong><i class="fas fa-hotel icon-style1"></i>Room Number:
                                        </strong>
                                        <span id="room"></span>
                                    </p>

                                    <hr class="margin-text">
                                    <p class="text-dark margin-text">
                                        <strong><i class="fas fa-bed icon-style1"></i>Bed Number: </strong>
                                        <span id="bed"></span>
                                    </p>

                                    <hr class="margin-text">

                                    <p class="text-dark margin-text">
                                        <strong><i class="fas fa-money-bill-wave icon-style1"></i>Total Bill:
                                        </strong>
                                        <span id="total"></span>
                                    </p>
                                    <hr class="margin-text">
                                    <p class="text-dark margin-text">
                                        <strong><i class="fas fa-credit-card icon-style"></i>Amount Paid:
                                        </strong>
                                        <span id="paid"></span>
                                    </p>
                                    <hr class="margin-text">
                                    <p class="text-dark margin-text">
                                        <strong><i class="fas fa-percent icon-style"></i> GST Option:
                                        </strong>
                                        <span id="gst_option"></span>
                                    </p>
                                    <hr class="margin-text">
                                    <div id="product_gst_block">
                                        <p class="text-dark margin-text">
                                            <strong><i class="fas fa-file-invoice-dollar icon-style"></i> Product GST:
                                            </strong>
                                            <span id="product_gst"></span>
                                        </p>
                                        <hr class="margin-text">
                                    </div>
                                    
                                </div>
                            </div>







                            <div class="button mb-4" style="display: flex; justify-content: end; margin: 0 5px;">
                                <button type="button"
                                    class="btn btn-sm btn-primary btn-outline-success generate-discharge-link-btn"
                                    data-id="{{ $discharge_id }}" data-amount="" data-paid="" data-status="">
                                    <i class="fa fa-link"></i> Generate Payment Link
                                </button>


                                @if (app('hasPermission')(10, 'update'))
                                    <a href="#" class="btn btn-primary btn-rounded btn-hdr edit-discharge-btn"
                                        style="color:black; margin-right:10px">
                                        <i class="fa fa-pencil-alt"></i> <span class="hdr-btn-text">Edit</span>
                                    </a>
                                @endif
                                @if (app('hasPermission')(10, 'delete'))
                                    <button type="button" class="btn btn-danger btn-rounded btn-hdr delete-report"
                                        data-id="{{ $discharge_id }}">
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
            var dischargeId = "{{ $discharge_id }}";


            $.ajax({
                url: "/api/patient-discharge-details/" + dischargeId,
                method: "GET",
                dataType: "json",
                headers: {
                    "Authorization": "Bearer " + token
                },
                success: function (response) {
                    if (response.success) {
                        var discharge = response.data;

                        // If the default image also fails, set the placeholder image
                        function ucfirst(str) {
                            return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
                        }

                        $("#file").on('error', function () {
                            $(this).attr("src", "https://placehold.co/400");
                        });

                        // Populate data
                        $("#file").attr("src", discharge.patient.profile);
                        $("#patient").text(ucfirst(discharge.patient.fullname));
                        $("#doctor").text(discharge.ipd && discharge.ipd.doctor ? ucfirst(discharge.ipd
                            .doctor.fullname) : 'N/A');
                        $("#treatment").text(discharge.ipd && discharge.ipd.treatment ? ucfirst(
                            discharge.ipd.treatment.name) : 'N/A');
                        $("#discharge").text(discharge.discharge_date);
                        $("#room").text(discharge.ipd ? discharge.ipd.room_number : 'N/A');
                        $("#bed").text(discharge.ipd ? discharge.ipd.bed_number : 'N/A');
                        $("#admit").text(discharge.ipd ? discharge.ipd.admission_date : 'N/A');
                        $("#total").text(discharge.total_bill);
                        $("#paid").text(discharge.amount_paid);
                        $("#gst_option").text(discharge.gst_option || 'N/A');
                        if (discharge.product_gst && discharge.product_gst.length) {
                            let gstDisplay = discharge.product_gst;
                            if (typeof gstDisplay[0] === 'object') {
                                gstDisplay = gstDisplay.map(function(item) {
                                    if (item.tax_name && item.tax_rate !== undefined) {
                                        return `${item.tax_name} (${parseFloat(item.tax_rate).toFixed(2)}%)`;
                                    }
                                    return item.tax_name || '';
                                });
                            }
                            $("#product_gst").text(gstDisplay.filter(Boolean).join(', '));
                        } else {
                            $("#product_gst").text('N/A');
                        }
                        if ((discharge.gst_option || '').toLowerCase() !== 'with gst') {
                            $("#product_gst_block").hide();
                        } else {
                            $("#product_gst_block").show();
                        }
                        $("#payment").text(ucfirst(discharge.payment_status));
                        $("#note").text(ucfirst(discharge.discharge_note));

                        $('.generate-discharge-link-btn')
                            .data('amount', discharge.total_bill)
                            .data('paid', discharge.amount_paid)
                            .data('status', discharge.payment_status)
                            .data('id', discharge.id);


                        $(".edit-discharge-btn").attr("href", "/discharge/edit/" + discharge.id);
                    } else {
                        alert("Failed to fetch discharge details");
                    }
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                    alert("Error fetching discharge details");
                }
            });

            // Delete functionality
            $(".delete-report").on("click", function () {
                var dischargeId = $(this).data('id');

                if (!dischargeId) {
                    Swal.fire('Error', 'Discharge ID not found!', 'error');
                    return;
                }

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d10',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/api/patient-discharge-details/' + dischargeId,
                            method: 'DELETE',
                            success: function (response) {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'Discharge deleted successfully!',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    $('button[data-id="' + dischargeId + '"]')
                                        .closest('tr').remove();
                                    setTimeout(function () {
                                        window.location.href =
                                            "{{ route('discharge.index') }}";
                                    }, 1500);
                                });
                            },
                            error: function () {
                                Swal.fire('Error', 'Error deleting discharge.',
                                    'error');
                            }
                        });
                    }
                });
            });

        });
        $(document).on('click', '.generate-discharge-link-btn', function () {

            let dischargeId = $(this).data('id');
            let totalBill = parseFloat($(this).data('amount')) || 0;
            let paidAmount = parseFloat($(this).data('paid')) || 0;
            let status = $(this).data('status');

            let remainingAmount = totalBill - paidAmount;

            // ✅ Already fully paid
            if (status === 'paid' || remainingAmount <= 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'No Pending Amount',
                    text: 'This discharge payment is already completed.',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // ❌ Invalid remaining amount
            if (remainingAmount <= 0) {
                Swal.fire('Info', 'No payable amount found.', 'info');
                return;
            }

            // ✅ Generate payment link for REMAINING amount
            $.ajax({
                url: `/api/razorpay/discharge/generate-payment-link`,
                type: 'POST',
                data: {
                    discharge_id: dischargeId,
                    amount: remainingAmount,
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
                        <p><strong>Remaining Amount:</strong> ₹${remainingAmount}</p>

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
                        />

                        <div style="text-align:center;font-size:13px;margin-top:6px;">
                            Link copied to clipboard ✔
                        </div>
                    `,
                        showCancelButton: true,
                        showConfirmButton: false,
                        cancelButtonText: 'Close',
                        customClass: {
                            cancelButton: 'swal-cancel-btn'
                        }
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Failed to generate payment link',
                        showCancelButton: true,
                        showConfirmButton: false,
                        cancelButtonText: 'Close',
                        customClass: {
                            cancelButton: 'swal-cancel-btn'
                        }
                    });
                }
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
