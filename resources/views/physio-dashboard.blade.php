@extends('layout.app')
@section('content')

    @if ($planExpirationWarning)
        <div class="modal fade" id="planExpirationModal" tabindex="-1" role="dialog" aria-labelledby="planExpirationLabel"
            aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content" style=" border-radius: 15px; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);">


                    <div class="modal-body" style="padding: 40px 30px; text-align: center;">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                            style="color: black; font-size: 28px; opacity: 1;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <!-- Warning Icon -->
                        <div style="margin-bottom: 20px;">
                            <div
                                style="display: inline-flex; align-items: center; justify-content: center; width: 80px; height: 80px; background-color: #ff6b6b; border-radius: 50%; color: white; font-size: 40px;">
                                <i class="fas fa-exclamation"></i>
                            </div>
                        </div>

                        <!-- Title -->
                        <h3 style="color: #1a1a1a; font-weight: 700; margin-bottom: 15px; font-size: 24px;">Plan Expiration
                            Warning..!!</h3>

                        <!-- Message -->
                        <p style="color: #666; font-size: 15px; line-height: 1.6; margin-bottom: 10px;">
                            Your subscription plan will expire in <strong>{{ $planExpirationWarning['days_remaining'] }}
                                days</strong>.
                            Please renew it to avoid interruption in service.
                        </p>

                        <!-- Plan Details Box -->
                        <div
                            style="background-color: #f8f9fa; border-left: 4px solid #ff6b6b; border-radius: 8px; padding: 20px; margin-bottom: 5px; text-align: left;">
                            <div
                                style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                <span style="color: #666; font-size: 14px;">Plan:</span>
                                <span
                                    style="color: #1a1a1a; font-weight: 600; font-size: 15px;">{{ $planExpirationWarning['plan_name'] }}</span>
                            </div>
                            <div
                                style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                <span style="color: #666; font-size: 14px;">Expires On:</span>
                                <span
                                    style="color: #ff6b6b; font-weight: 700; font-size: 15px;">{{ $planExpirationWarning['expiry_date'] }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="color: #666; font-size: 14px;">Days Remaining:</span>
                                <span
                                    style="color: #ff6b6b; font-weight: 700; font-size: 15px;">{{ $planExpirationWarning['days_remaining'] }}
                                    days</span>
                            </div>
                        </div>

                        <!-- Contact Section -->
                        <div style=" text-align:left;">

                            <h5 style="color:#f39c12;font-weight:700;font-size:15px;margin-bottom:18px;">
                                Contact us to Renew:
                            </h5>

                            <div style="display:flex;align-items:center;margin-bottom:5px;">
                                <i class="fas fa-phone-alt" style="width:20px;color:#777;margin-right:12px;"></i>

                                <span style="color:#666;font-size:14px;">
                                    +91 98247 34531
                                </span>
                            </div>

                            <div style="display:flex;align-items:center;">
                                <i class="fas fa-envelope" style="width:20px;color:#777;margin-right:12px;"></i>

                                <span style="color:#666;font-size:14px;">
                                    info@fableadtechnolabs.com
                                </span>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            $(document).ready(function() {
                // Show modal on page load
                $('#planExpirationModal').modal('show');

                // Store in session that user has seen the warning today
                sessionStorage.setItem('planWarningShown', 'true');
            });
        </script>
    @endif

    <style>
        .height-card {
            height: 540px;
        }

        #appointmentStatusChart_admin canvas {
            position: absolute;
            left: 0px;
            top: 0px;
            /* width: 500px !important; */
            height: 454px !important;
            user-select: none;
            -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
            padding: 0px;
            margin: 0px;
            border-width: 0px;
        }



        .status-dot {
            width: 15px;
            height: 15px;
            border-radius: 50%;
            display: inline-block;
            cursor: pointer;
        }

        .upcoming {
            background-color: #007bff;
            /* Blue */
        }

        .confirmed {
            /* Cyan */
            /* background-color: #17a2b8; */
            background-color: #005c6b;
        }

        .completed {
            background-color: #28a745;
            /* Green */
        }

        .cancelled {
            background-color: #dc3545;
            /* Red */
        }

        .follow-up {
            background-color: #ffc107;
            /* Yellow */
            color: #000;
        }

        .graph {
            width: 580px;
            height: 430px;
        }


        .upcoming:hover,
        .confirmed:hover,
        .completed:hover,
        .cancelled:hover {
            color: #ffffff;
        }

        #event-modal .modal-content {
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            background: #fff;
        }

        #event-modal .modal-header {
            background: #cfece0;
            color: black;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        #event-modal .modal-title {
            font-size: 18px;
            font-weight: bold;
        }

        #event-modal .modal-body {
            font-size: 16px;
            color: #333;
            padding: 20px;
        }

        #event-modal .modal-footer {
            border-top: 1px solid #ddd;
            background: #f9f9f9;
        }

        #event-modal .btn-secondary {
            background: #6c757d;
            border: none;
        }

        #event-modal .btn-secondary:hover {
            background: #5a6268;
        }

        @media (max-width: 768px) {
            .chart-container {
                width: auto;
               
            }
        }



        .table td,
        .table th {
            padding: .75rem;
            vertical-align: middle !important;
            border-top: 1px solid #dee2e6;
        }

        .dash-widget-bg2,
        .dash-widget-bg1,
        .dash-widget-bg4,
        .dash-widget-bg3 {
            width: 50px;
            float: left;
            color: black;
            display: block;
            font-size: 50px;
            text-align: center;
            line-height: 57px;
            background: white;
            border-radius: 50%;
            font-size: 30px;
            height: 50px;
        }

        .dash-widget-info>span.widget-title3,
        span.widget-title2,
        span.widget-title1 {
            color: #666666;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 16px;
        }

        .boxes {
            display: flex !important;
        }

        .boxes > a {
            display: flex !important;
            flex-direction: column !important;
            width: 100% !important;
            height: 100% !important;
            text-decoration: none !important;
        }

        .dash-widget1,
        .dash-widget2 {
            width: 100% !important;
            height: 100% !important;
            min-height: 105px !important;
        }



        @media screen and (max-width: 767px) {

            .card-title {
                font-size: 14px !important;
            }

            #todaysAppointmentsModal .card-title {
                text-align: left !important;
            }

            /* Stack left, center, right toolbar */
            .fc-toolbar.fc-header-toolbar {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .fc-toolbar .fc-left,
            .fc-toolbar .fc-center,
            .fc-toolbar .fc-right {
                width: 100%;
                
            }

            /* Reduce button size and spacing */
            .fc-button {
                padding: 6px 8px !important;
                font-size: 12px;
                margin: 0 2px !important;

            }

            .fc button,
            .fc table,
            body .fc {
                font-size: 12px !important;
            }



            .chart-title h4 {
                font-size: 14px !important;
                padding-top: 10px !important;
            }



            .fc-toolbar .fc-right {
                float: none !important;
                display: inline !important;
              
            }


            .fc-view-container,
            .fc-day-grid {
                font-size: 12px;
            }

            .fc th,
            .fc td {
                padding: 5px;
            }

            /* Legend styling for responsiveness */
            .calendar-legend {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                font-size: 12px;
                gap: 5px;
            }

            .calendar-legend div {
                display: flex;
                align-items: center;
                margin: 2px 6px;
            }

            .calendar-legend span {
                width: 10px;
                height: 10px;
                border-radius: 50%;
                display: inline-block;
                margin-right: 4px;
            }

            .status-container,
            .status-container.row,
            .row.p-1 {
                flex-direction: row;
                flex-wrap: wrap;
                justify-content: center;
            }

            .status-container .col-lg-4,
            .row.p-1 .col-lg-4,
            .status-container .col-sm-4,
            .row.p-1 .col-sm-4 {
                width: 33% !important;
                /* show 2 per row */
                margin-bottom: 6px;
                padding: 0 !important;
            }

            .status-dot {
                width: 12px;
                height: 12px;
            }

            .status-container div,
            .row.p-1 div {
                font-size: 13px;
            }

            .status-container .d-flex,
            .row.p-1 .d-flex {
                justify-content: center;
            }


            .fc-center {
                text-align: center;
                margin: 0px auto;
                display: flex !important;
                justify-content: center !important;
              
            }

            .fc-center h2 {
                

            }

            #appointmentStatusChart_admin canvas {
                width: 335px !important;

            }

           
            .dash-widget1,
            .dash-widget2 {
                display: flex !important;
                flex-direction: row !important;
                align-items: center !important;
                justify-content: space-between !important;
                text-align: right !important;
                width: 100% !important;
                height: 100% !important;
                min-height: 80px !important;
                padding: 12px 10px !important;
                margin-bottom: 8px !important;
                border-radius: 12px !important;
                box-sizing: border-box !important;
            }

            .dash-widget-bg1,
            .dash-widget-bg2,
            .dash-widget-bg3,
            .dash-widget-bg4 {
                width: 38px !important;
                height: 38px !important;
                line-height: 38px !important;
                font-size: 18px !important;
                border-radius: 50% !important;
                background: #ffffff !important;
                color: #1a1a1a !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                margin: 0 !important;
                float: none !important;
                flex-shrink: 0 !important;
            }

            .dash-widget-info {
                display: flex !important;
                flex-direction: column !important;
                align-items: flex-end !important;
                justify-content: center !important;
                text-align: right !important;
                padding: 0 !important;
                margin: 0 !important;
                flex: 1 1 auto !important;
            }

            .dash-widget-info > span.widget-title1,
            .dash-widget-info > span.widget-title2,
            .dash-widget-info > span.widget-title3,
            .dash-widget-info > span.widget-title4 {
                font-size: 11px !important;
                font-weight: 600 !important;
                line-height: 1.2 !important;
                color: #555555 !important;
                padding: 0 !important;
                margin: 0 0 4px 0 !important;
                display: block !important;
                text-align: right !important;
                word-break: break-word !important;
            }

            .dash-widget-info > h3 {
                font-size: 18px !important;
                font-weight: 700 !important;
                color: #1a1a1a !important;
                line-height: 1 !important;
                margin: 0 !important;
                padding: 0 !important;
                text-align: right !important;
            }

            .about-padding {
                padding: 4px 0 5px 0 !important;
                margin-top: 0px !important;
                margin-left: -5px !important;
                margin-right: -5px !important;
            }

            .content [class*="col-"],
            .boxes {
                padding-left: 5px !important;
                padding-right: 5px !important;
            }

            .icon-style1 {
                font-size: 15px;
                padding: 2px;
            }

            .card .card-header .button {
                margin: 0;
            }




        }

        @media (min-width: 1024px) {

            #appointmentStatusChart_admin canvas {
                width: 478px !important;
            }



            @media (max-width: 767px) {



                a.mobile_btn {

                    padding: 10px 20px !important;

                }
            }

        }


        @media (min-width: 375px) and (max-width: 667px) {
            .fc-right .fc-button-group {
                margin-left: 25px !important;
            }
        }

        @media (min-width: 414px) and (max-width: 896px) {
            .fc-right .fc-button-group {
                margin-left: 60px !important;
            }
        }

        @media (min-width: 412px) and (max-width: 915px) {
            .fc-right .fc-button-group {
                margin-left: 62px !important;
            }
        }

        @media (min-width: 768px) {

            .fc-button {
                padding: 6px 12px !important;
            }


            .fc-next-button,
            .fc-prev-button {

            }

            .fc-next-button {
                margin-left: 8px !important;
              
            }

            .fc-month-button,
            .fc-agendaWeek-button {
                margin-right: 8px !important;
            }



        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/echarts@5.4.2/dist/echarts.min.js"></script>



    <div class="page-wrapper">

        @if (Auth::check() && optional(Auth::user()->role)->name == 'Admin')
            <div class="content">
                <div class="row about-padding">

                    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3 p-1 boxes col-6">
                        <a href="{{ route('followup.index') }}">
                            <div class="dash-widget2">
                                <span class="dash-widget-bg2"><i class="fa fa-stethoscope" aria-hidden="true"></i> </span>
                                <div class="dash-widget-info text-right">
                                    <span class="widget-title2">Today Followup </span>
                                    <h3 class="total-users-count text-dark">0</h3>

                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3 p-1 boxes col-6">
                        <a href="{{ route('appointment.index') }}">
                            <div class="dash-widget1">
                                <span class="dash-widget-bg1"><i class="fa fa-user-md" aria-hidden="true"></i></span>
                                <div class="dash-widget-info text-right">
                                    <span class="widget-title1">Today Appointment</span>
                                    <h3 class="total-doctors-count text-dark">0</h3>
                                </div>
                            </div>
                        </a>
                    </div>


                    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3 p-1 boxes col-6">
                        <a href="{{ route('patients.index') }}">
                            <div class="dash-widget2">
                                <span class="dash-widget-bg4"><i class="fa fa-house-medical" aria-hidden="true"></i></span>
                                <div class="dash-widget-info text-right">
                                    <span class="widget-title2">Today Home Patient </span>
                                    <h3 class="home_patient text-dark">0</h3>

                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3 p-1 boxes col-6">
                        <a href="{{ route('patients.index') }}">
                            <div class="dash-widget1">
                                <span class="dash-widget-bg3"><i class="fa fa-user-md"></i></span>
                                <div class="dash-widget-info text-right">
                                    <span class="widget-title3">Today Cosmetic Patient </span>
                                    <h3 class="cosmatic_patient text-dark">0</h3>

                                </div>
                            </div>
                        </a>
                    </div>

                </div>

                <div class="row about-padding">
                    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3 p-1 boxes col-6">
                        <a href="{{ route('patients.index') }}">
                            <div class="dash-widget1">
                                <span class="dash-widget-bg1"><i class="fa fa-hospital-user" aria-hidden="true"></i></span>
                                <div class="dash-widget-info text-right">
                                    <span class="widget-title1">Today IPD Patient</span>
                                    <h3 class="ipd_patient text-dark">0</h3>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3 p-1 boxes col-6">
                        <a href="{{ route('expense.index') }}">
                            <div class="dash-widget2">
                                <span class="dash-widget-bg2"><i class="fa fa-wallet" aria-hidden="true"></i> </span>
                                <div class="dash-widget-info text-right">
                                    <span class="widget-title2">Today Expense</span>
                                    <h3 class="total-expense text-dark">₹ 0</h3>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3 p-1 boxes col-6">
                        <a href="{{ route('patients.index') }}">
                            <div class="dash-widget1">
                                <span class="dash-widget-bg3"><i class="fa fa-users"></i></span>
                                <div class="dash-widget-info text-right">
                                    <span class="widget-title3">Today Patients</span>
                                    <h3 class="today-patients text-dark">0</h3>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3 p-1 boxes col-6">
                        <a href="#">
                            <div class="dash-widget2">
                                <span class="dash-widget-bg4"><i class="fa fa-birthday-cake" aria-hidden="true"></i></span>
                                <div class="dash-widget-info text-right">
                                    <span class="widget-title2">Today Birthday</span>
                                    <h3 class="today-birthdays text-dark">0</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>


                <div class="row calender-apointment">
                    <div class="col-12 col-md-12 col-lg-6 col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title d-inline-block"><i class="fa fa-stethoscope icon-style1"
                                        aria-hidden="true"></i> Upcoming Appointments</h4>
                                @if (app('hasPermission')(29, 'view'))
                                    <a href="{{ route('appointment.index') }}"
                                        class="btn btn-primary btn-rounded float-right">View all <i
                                            class="fas fa-arrow-right ml-1"></i></a>
                                @endif
                            </div>
                            <div class="card-body " style="height: 430px;overflow-y: scroll;">
                                <div class="table-responsive">
                                    <table class="table custom-table12 tbl1">
                                        <thead style="background-color:rgb(233 152 136);" class="text-center">
                                            <tr>
                                                <th style="border-right: 1px solid #ffffff75;">Patient</th>
                                                <th style="border-right: 1px solid #ffffff75;">Doctor</th>
                                            
                                                <th style="border-right: 1px solid #ffffff75;">Date</th>
                                                {{-- <th style="border-right: 1px solid #ffffff75;">Status</th> --}}
                                            </tr>
                                        </thead>
                                        <tbody class="text-center">
                                        </tbody>
                                    </table>
                                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>




                                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                                    <script>
                                        $(document).ready(function () {
                                            let branchId = localStorage.getItem('selectedBranchId');

                                            $.ajax({
                                                url: "/api/appointments",
                                                type: "GET",
                                                data: {
                                                    branch_id: branchId // ? send branch to API
                                                },
                                                dataType: "json",
                                                success: function (response) {
                                                    // console.log("API Response:", response);

                                                    // ? Use response.data instead of response.appointments
                                                    if (response.appointments && response.appointments.length > 0) {
                                                        let rows = "";
                                                        let appointmentsData = [];

                                                        if (role === 'Admin') {
                                                            appointmentsData = response.appointments.filter(appointment => {
                                                                const today = new Date().toISOString().split('T')[0];
                                                                return appointment.date >= today;
                                                            });
                                                        } else {
                                                            appointmentsData = response.appointments.filter(appointment => {
                                                                const today = new Date().toISOString().split('T')[0];
                                                                return (
                                                                    appointment.user_id == parseInt(userId) &&
                                                                    appointment.date >= today
                                                                );
                                                            }).slice(-5).reverse();
                                                        }

                                                        if (appointmentsData.length > 0) {
                                                            function ucfirst(str) {
                                                                return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
                                                            }

                                                            $.each(appointmentsData, function (index, appointment) {
                                                                // Skip if status is 'completed'
                                                                if (appointment.status?.toLowerCase() === 'completed') return;

                                                                let profileImage = appointment.patient?.profile;

                                                                let statusClass = {
                                                                    'confirmed': 'confirmed',
                                                                    'upcoming': 'upcoming',
                                                                    'cancelled': 'cancelled',
                                                                    'completed': 'completed',
                                                                    'follow-up': 'follow-up'
                                                                }[appointment.status?.toLowerCase()] || 'btn-secondary';

                                                                let statusButton =
                                                                    `<span class="custom-badge btn ${statusClass} btn-rounded">${ucfirst(appointment.status || '')}</span>`;

                                                                rows += `
                                                                            <tr class="clickable-row" style="cursor:pointer" data-id="${appointment.id}">
                                                                                <td class="text-left d-flex align-items-center">
                                                                                    <img src="${profileImage || '{{ asset(env('IMAGE_PATH') . 'admin/assets/img/img1.png') }}'}"
                                                                                            alt="Patient Image" width="50" height="50" class="rounded-circle"
                                                                                            onerror="this.onerror=null;this.src='{{ asset(env('IMAGE_PATH') . 'admin/assets/img/img1.png') }}';">
                                                                                    <span style="padding-left:5px">${ucfirst(appointment.patient?.fullname || 'Unknown')}</span>
                                                                                </td>
                                                                                <td>${ucfirst(appointment.doctor?.fullname || 'Unknown')}</td>

                                                                                <td>${appointment.date || ''}</td>
                                                                                
                                                                            </tr>
                                                                        `;
                                                            });

                                                            if (rows) {
                                                                $(".custom-table12 tbody").html(rows);
                                                            } else {
                                                                $(".custom-table12 tbody").html(
                                                                    "<tr><td colspan='5'>No appointment found</td></tr>"
                                                                );
                                                            }

                                                            $(".custom-table12 tbody").on("click", ".clickable-row", function () {
                                                                let appointmentId = $(this).data("id");
                                                                window.location.href = `/appointment/show/${appointmentId}`;
                                                            });

                                                        } else {
                                                            $(".custom-table12 tbody").html(
                                                                "<tr><td colspan='5'>No appointment found</td></tr>"
                                                            );
                                                        }
                                                    } else {
                                                        $(".custom-table12 tbody").html(
                                                            "<tr><td colspan='5'>No appointment found</td></tr>"
                                                        );
                                                    }
                                                },
                                                error: function (xhr, status, error) {
                                                    console.error("Error fetching data:", error);
                                                    $(".custom-table12 tbody").html(
                                                        "<tr><td colspan='5'>Error loading appointments</td></tr>"
                                                    );
                                                }
                                            });
                                        });
                                    </script>


                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-12 col-lg-6 col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title d-inline-block"><i class="fa fa-stethoscope icon-style1"
                                        aria-hidden="true"></i>Today Followup By Staff</h4>
                                @if (app('hasPermission')(29, 'view'))
                                    <a href="{{ route('followup.index') }}"
                                        class="btn btn-primary btn-rounded float-right">View all <i
                                            class="fas fa-arrow-right ml-1"></i></a>
                                @endif
                            </div>
                            <div class="card-body " style="height: 430px;overflow-y: scroll;">
                                <div class="table-responsive">
                                    <table class="table followup-count tbl1">
                                        <thead style="background-color:rgb(233 152 136);" class="text-center">
                                            <tr>

                                                <th style="border-right: 1px solid #ffffff75;">Doctor</th>
                                                <th>No. Of Followups</th>

                                            </tr>
                                        </thead>
                                        <tbody class="text-center">
                                        </tbody>
                                    </table>

                                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                                    <script>
                                        $(document).ready(function () {
                                            let branchId = localStorage.getItem('selectedBranchId');

                                            $.ajax({
                                                url: "/api/followups/count-by-doctor",
                                                type: "GET",
                                                data: { branch_id: branchId },
                                                dataType: "json",
                                                success: function (res) {
                                                    if (res.status) {
                                                        let rows = '';
                                                        if (res.data.length > 0) {
                                                            $.each(res.data, function (i, doctor) {
                                                                rows += `
                                                                        <tr>
                                                                            <td class="text-left d-flex align-items-center" >
                                                                                <img src="${doctor.profile}" alt="Doctor Image" width="50" height="50" class="rounded-circle" onerror="this.onerror=null;this.src='{{ asset('admin/assets/img/img1.png') }}';">
                                                                                <span style="padding-left:5px">${doctor.doctor_name}</span>
                                                                            </td>
                                                                            <td style="font-weight:500;font-size:18px" >${doctor.followup_count}</td>
                                                                        </tr>
                                                                    `;
                                                            });
                                                        } else {
                                                            rows = "<tr><td colspan='2'>No follow-ups found</td></tr>";
                                                        }
                                                        $(".followup-count tbody").html(rows);
                                                    }
                                                },
                                                error: function (err) {
                                                    console.error(err);
                                                    $(".followup-count tbody").html("<tr><td colspan='2'>Error loading data</td></tr>");
                                                }
                                            });
                                        });

                                    </script>


                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="row">

                    <div class="col-12 col-md-12 col-lg-6 col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title d-inline-block"><i class="fa fa-credit-card icon-style1"></i>Pending
                                    Amounts
                                </h4>
                                @if (app('hasPermission')(28, 'view'))
                                    <a href="{{ route('daily_data.index') }}"
                                        class="btn btn-primary btn-rounded float-right">
                                        View all <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                @endif



                            </div>
                            <div class="card-body" style="height: 445px;overflow-y: scroll;">
                                <div class="table-responsive">
                                    <table class="table custom-table patient-tbl">
                                        <thead style="background-color:#ff8e29;">
                                            <tr>
                                                <!-- <th style="border-right: 1px solid #ffffff75;">Image</th> -->
                                                <th style="border-right: 1px solid #ffffff75;">Patient</th>
                                                <th style="border-right: 1px solid #ffffff75;">Date</th>
                                                <th style="border-right: 1px solid #ffffff75;">Total</th>
                                                <th style="border-right: 1px solid #ffffff75;">Pending</th>

                                            </tr>
                                        </thead>
                                        <tbody class="text-start" id="pendingTableBody">
                                            <!-- Data will be inserted here -->
                                        </tbody>
                                    </table>

                                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                                    <script>
                                        $(document).ready(function () {
                                            const urlParams = new URLSearchParams(window.location.search);
                                            const branchId = urlParams.get('branch_id');
                                            
                                            $.ajax({
                                                url: "api/pending-payments",
                                                type: "GET",
                                                data: { branch_id: branchId },
                                                dataType: "json",
                                                success: function (data) {
                                                    let rows = "";
                                                    if (data.length > 0) {
                                                        data.forEach(function (item) {
                                                            rows += `
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <img src="${item.profile}" alt="Patient" style="width:40px; height:40px; border-radius:50%; margin-right:8px;">
                                                                                                    ${item.patient}
                                                                                                </td>
                                                                                                <td>${item.date}</td>
                                                                                               <td>₹ ${item.total}</td>
                                                    <td>₹ ${item.pending}</td>

                                                                                            </tr>
                                                                                        `;
                                                        });
                                                    } else {
                                                        rows = `<tr><td colspan="4">No pending payments found</td></tr>`;
                                                    }
                                                    $("#pendingTableBody").html(rows);
                                                },
                                                error: function () {
                                                    alert("Error fetching pending payments.");
                                                }
                                            });
                                        });
                                    </script>





                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-12 col-lg-6 col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title d-inline-block"><i class="fa fa-user icon-style1"></i> New Patients
                                </h4>
                                @if (app('hasPermission')(28, 'view'))
                                    <a href="{{ route('patients.index') }}" class="btn btn-primary btn-rounded float-right">
                                        View all <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                @endif



                            </div>
                            <div class="card-body" style="height: 445px;overflow-y: scroll;">
                                <div class="table-responsive">
                                    <table class="table custom-table patient-tbl">
                                        <thead style="background-color:#ff8e29;">
                                            <tr>
                                                <!-- <th style="border-right: 1px solid #ffffff75;">Image</th> -->
                                                <th style="border-right: 1px solid #ffffff75;">Id</th>
                                                <th style="border-right: 1px solid #ffffff75;">Patient</th>
                                                <th style="border-right: 1px solid #ffffff75;">Age</th>
                                                <th style="border-right: 1px solid #ffffff75;">Phone</th>

                                            </tr>
                                        </thead>
                                        <tbody class="text-center" id="patientTableBody">
                                            <!-- Data will be inserted here -->
                                        </tbody>
                                    </table>

                                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                                    <script>
                                        $(document).ready(function () {

                                            let branchId = localStorage.getItem('selectedBranchId');
                                            $.ajax({
                                                url: "{{ url('/api/patient') }}", // Ensure this route exists and returns patient data
                                                type: "GET",
                                                dataType: "json",
                                                data: {
                                                    branch_id: branchId
                                                },
                                                success: function (response) {
                                                    let patientTableBody = $("#patientTableBody");
                                                    patientTableBody.empty(); // Clear existing data

                                                    if (response.patients && response.patients.length > 0) {
                                                        // If the logged-in user's role is Admin, show all patients;
                                                        // otherwise, filter patients by the logged-in user's user_id.
                                                        let userPatients = (role === 'Admin')
                                                            ? response.patients
                                                            : response.patients.filter(patient => patient.user_id == userId);

                                                        // ? Sort by id in descending order
                                                        let sortedPatients = userPatients.sort((a, b) => b.id - a.id);

                                                        // ? Get the first 5 records after sorting
                                                        let latestPatients = sortedPatients.slice(0, 5);

                                                        let defaultImage =
                                                            "{{ asset(env('IMAGE_PATH') . 'admin/assets/img/img1.png') }}";

                                                        if (latestPatients.length > 0) {
                                                            $.each(latestPatients, function (index, patient) {
                                                                // Use patient's profile image or default image
                                                                let imageUrl = patient.profile ? patient
                                                                    .profile : defaultImage;

                                                                function ucfirst(str) {
                                                                    if (!str || typeof str !== "string") {
                                                                        return ""; // fallback if null/undefined
                                                                    }
                                                                    return str.charAt(0).toUpperCase() + str.slice(1);
                                                                }


                                                                // Construct the row
                                                                let row =
                                                                    `
                                                                                                                                                                                                                                            <tr class="clickable-row" style="cursor:pointer" data-id="${patient.id}">
                                                                                                                                                                                                                                            <td>${patient.patient_unique_id}</td>
                                                                                                                                                                                                                                                <td class="text-left">
                                                                                                                                                                                                                                                    <img width="50" height="50" src="${imageUrl}" class="rounded-circle" alt="User Image"
                                                                                                                                                                                                                                                    onerror="this.onerror=null;this.src='${defaultImage}';">
                                                                                                                                                                                                                                                    <span style="padding-left:5px"><h2>${ucfirst(patient.fullname || 'Unknown')}</h2></span>
                                                                                                                                                                                                                                                </td>
                                                                                                                                                                                                                                                <td>${patient.age}</td>
                                                                                                                                                                                                                                                <td>${patient.phone}</td>

                                                                                                                                                                                                                                            </tr>
                                                                                                                                                                                                                                        `;

                                                                patientTableBody.append(row);
                                                            });

                                                            // Make rows clickable
                                                            $(".custom-table tbody").on("click",
                                                                ".clickable-row",
                                                                function () {
                                                                    let patientId = $(this).data("id");
                                                                    window.location.href =
                                                                        `/patient/show/${patientId}`;
                                                                });
                                                        } else {
                                                            patientTableBody.append(
                                                                '<tr><td colspan="5">No patients found.</td></tr>'
                                                            );
                                                        }
                                                    } else {
                                                        patientTableBody.append(
                                                            '<tr><td colspan="5">No patients found.</td></tr>'
                                                        );
                                                    }

                                                },
                                                error: function (xhr, status, error) {
                                                    console.error("Error fetching patients:", error);
                                                    $("#patientTableBody").append(
                                                        '<tr><td colspan="5">Failed to load data.</td></tr>'
                                                    );
                                                }
                                            });
                                        });
                                    </script>



                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if (Auth::check() && optional(Auth::user()->role)->name == 'Doctor')
            <div class="content">

                <div class="row about-padding">

                    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3 p-1 boxes col-6">
                        <a href="{{ route('followup.index') }}">
                            <div class="dash-widget2">
                                <span class="dash-widget-bg2"><i class="fa fa-stethoscope" aria-hidden="true"></i>
                                </span>
                                <div class="dash-widget-info text-right">
                                    <span class="widget-title2">Today Followup </span>
                                    <h3 class=" total-users-count text-dark">0</h3>

                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3 p-1 boxes col-6">
                        <a href="{{ route('appointment.index') }}">
                            <div class="dash-widget1">
                                <span class="dash-widget-bg1"><i class="fa fa-user-md" aria-hidden="true"></i></span>
                                <div class="dash-widget-info text-right">
                                    <span class="widget-title1">Today Appointment</span>
                                    <h3 class=" total-doctors-count text-dark">0</h3>
                                </div>
                            </div>
                        </a>
                    </div>


                    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3 p-1 boxes col-6">
                        <a href="{{ route('appointment.index') }}">
                            <div class="dash-widget2">
                                <span class="dash-widget-bg4"><i class="fa fa-heartbeat" aria-hidden="true"></i></span>
                                <div class="dash-widget-info text-right">
                                    <span class="widget-title2">All Appointment </span>
                                    <h3 class=" total-appointment-count text-dark">0</h3>

                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3 p-1 boxes col-6">
                        <a href="{{ route('patients.index') }}">
                            <div class="dash-widget1">
                                <span class="dash-widget-bg3"><i class="fa fa-user"></i></span>
                                <div class="dash-widget-info text-right">
                                    <span class="widget-title3">All Patients </span>
                                    <h3 class=" total-patient-count text-dark">0</h3>

                                </div>
                            </div>
                        </a>
                    </div>

                </div>

                <div class="row">


                    <div class="col-12 col-md-12 col-lg-12 col-xl-7">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title d-inline-block"><i class="fa fa-stethoscope icon-style1"
                                        aria-hidden="true"></i>
                                    Upcoming Appointments</h4>
                                @if (app('hasPermission')(29, 'view'))
                                    <a href="{{ route('appointment.index') }}"
                                        class="btn btn-primary btn-rounded float-right">View
                                        all <i class="fas fa-arrow-right ml-1"></i></a>
                                @endif


                            </div>
                            <div class="card-body " style=" height: 430px;overflow-y: scroll;">
                                <div class="table-responsive">
                                    <table class="table  custom-table12 tbl1">
                                        <thead style="background-color:rgb(233 152 136);" class="text-center">
                                            <tr>
                                               
                                                <th style="border-right: 1px solid #ffffff75;">Patient </th>

                                                <th style="border-right: 1px solid #ffffff75;">Doctor </th>
                                            
                                                <th style="border-right: 1px solid #ffffff75;"> Date</th>
                                                <th style="border-right: 1px solid #ffffff75;">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-center">
                                        </tbody>
                                    </table>
                                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                                    <script>
                                        $(document).ready(function () {
                                            $.ajax({
                                                url: "/api/appointments",
                                                type: "GET",
                                                dataType: "json",
                                                success: function (response) {
                                                   // console.log("API Response:", response);

                                                    if (response.appointments && response.appointments.length >
                                                        0) {
                                                        let rows = "";
                                                        let userAppointments = response.appointments;
                                                        const today = new Date().toISOString().split('T')[0];

                                                        // Assuming role and userId are available globally
                                                        let filteredAppointments = [];

                                                        if (role === 'Admin') {
                                                            filteredAppointments = userAppointments.filter(
                                                                appointment =>
                                                                    appointment.status.toLowerCase() !==
                                                                    'completed' &&
                                                                    appointment.date >= today
                                                            );
                                                        } else if (role === 'Doctor') {
                                                            filteredAppointments = userAppointments.filter(
                                                                appointment =>
                                                                    appointment.status.toLowerCase() !==
                                                                    'completed' &&
                                                                    appointment.date >= today &&
                                                                    appointment.doctor_id == parseInt(userId)
                                                            );
                                                        } else {
                                                            filteredAppointments = userAppointments.filter(
                                                                appointment =>
                                                                    appointment.status.toLowerCase() !==
                                                                    'completed' &&
                                                                    appointment.date >= today &&
                                                                    appointment.user_id == parseInt(userId)
                                                            );
                                                        }

                                                        let latestAppointments = filteredAppointments.slice(-5)
                                                            .reverse();

                                                        if (latestAppointments.length > 0) {
                                                            function ucfirst(str) {
                                                                return str ? str.charAt(0).toUpperCase() + str
                                                                    .slice(1) : '';
                                                            }

                                                            $.each(latestAppointments, function (index,
                                                                appointment) {
                                                                let profileImage = appointment.patient
                                                                    ?.profile ||
                                                                    "{{ asset(env('IMAGE_PATH') . 'admin/assets/img/img1.png') }}";

                                                                let statusClass = {
                                                                    'confirmed': 'confirmed',
                                                                    'upcoming': 'upcoming',
                                                                    'cancelled': 'cancelled',
                                                                    'completed': 'completed',
                                                                    'follow-up': 'follow-up'
                                                                }[appointment.status
                                                                    .toLowerCase()
                                                                ] || 'btn-secondary';

                                                                let statusButton =
                                                                    `<span class="custom-badge btn ${statusClass} btn-rounded">${ucfirst(appointment.status)}</span>`;

                                                                rows +=
                                                                    `<tr class="clickable-row" style="cursor:pointer" data-id="${appointment.id}">
                                                                        <td class="text-left d-flex align-items-center">
                                                                            <img src="${profileImage}" alt="Patient Image" width="50" height="50" class="rounded-circle" 
                                                                                onerror="this.onerror=null;this.src='{{ asset(env('IMAGE_PATH') . 'admin/assets/img/img1.png') }}';">
                                                                            <span style="padding-left:3px">${ucfirst(appointment.patient.fullname)}</span>
                                                                        </td>
                                                                        <td>${ucfirst(appointment.doctor?.fullname || 'N/A')}</td>

                                                                        <td>${appointment.date}</td>
                                                                        <td>${statusButton}</td>
                                                                    </tr>`;
                                                            });

                                                            $(".custom-table12 tbody").html(rows);

                                                            $(".custom-table12 tbody").on("click",
                                                                ".clickable-row",
                                                                function () {
                                                                    let appointmentId = $(this).data("id");
                                                                    window.location.href =
                                                                        `/appointment/show/${appointmentId}`;
                                                                });
                                                        } else {
                                                            $(".custom-table12 tbody").html(
                                                                "<tr><td colspan='6'>No Appointments Found</td></tr>"
                                                            );
                                                        }
                                                    } else {
                                                        $(".custom-table12 tbody").html(
                                                            "<tr><td colspan='6'>No Appointments Found</td></tr>"
                                                        );
                                                    }
                                                },
                                                error: function (xhr, status, error) {
                                                    console.error("Error fetching data:", error);
                                                }
                                            });
                                        });
                                    </script>





                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-12 col-lg-12 col-xl-5">
                        <div class="card calender-card ">


                            <div id="calendar">
                                <div class="status-container row p-1">
                                    <div class="align-items-center d-flex p-o col-lg-4">
                                        <div class="status-dot confirmed rounded-circle" title="Confirmed"></div>
                                        <div class="px-2">Confirmed</div>
                                    </div>
                                    <div class="align-items-center d-flex p-o col-lg-4">
                                        <div class="status-dot upcoming rounded-circle" title="Upcoming"></div>
                                        <div class="px-2">Upcoming</div>
                                    </div>
                                    <div class="align-items-center d-flex p-o col-lg-4">
                                        <div class="status-dot cancelled rounded-circle" title="Cancelled"></div>
                                        <div class="px-2">Cancelled</div>
                                    </div>
                                </div>
                                <div class=" row p-1">
                                    <div class="align-items-center d-flex p-o col-lg-4">
                                        <div class="status-dot completed rounded-circle" title="Completed"></div>
                                        <div class="px-1">Completed</div>
                                    </div>
                                    <div class="align-items-center d-flex p-o col-lg-4">
                                        <div class="status-dot follow-up rounded-circle" title="Follow-up"></div>
                                        <div class="px-1">Follow-up</div>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 col-md-12 col-lg-6 col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title d-inline-block"><i class="fa fa-credit-card icon-style1"></i>Pending
                                    Amounts
                                </h4>
                                @if (app('hasPermission')(28, 'view'))
                                    <a href="{{ route('daily_data.index') }}"
                                        class="btn btn-primary btn-rounded float-right">
                                        View all <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                @endif



                            </div>
                            <div class="card-body" style="height: 445px;overflow-y: scroll;">
                                <div class="table-responsive">
                                    <table class="table custom-table patient-tbl">
                                        <thead style="background-color:#ff8e29;">
                                            <tr>
                                               
                                                <th style="border-right: 1px solid #ffffff75;">Patient</th>
                                                <th style="border-right: 1px solid #ffffff75;">Date</th>
                                                <th style="border-right: 1px solid #ffffff75;">Total</th>
                                                <th style="border-right: 1px solid #ffffff75;">Pending</th>

                                            </tr>
                                        </thead>
                                        <tbody class="text-start" id="pendingTableBody">
                                            <!-- Data will be inserted here -->
                                        </tbody>
                                    </table>

                                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                                    <script>
                                        $(document).ready(function () {
                                            const urlParams = new URLSearchParams(window.location.search);
                                            const branchId = urlParams.get('branch_id');
                                            
                                            $.ajax({
                                                url: "api/pending-payments",
                                                type: "GET",
                                                data: { branch_id: branchId },
                                                dataType: "json",
                                                success: function (data) {
                                                    let rows = "";
                                                    if (data.length > 0) {
                                                        data.forEach(function (item) {
                                                            rows += `
                                                                                        <tr>
                                                                                            <td>
                                                                                                <img src="${item.profile}" alt="Patient" style="width:40px; height:40px; border-radius:50%; margin-right:8px;">
                                                                                                ${item.patient}
                                                                                            </td>
                                                                                            <td>${item.date}</td>
                                                                                            <td>₹ ${item.total}</td>
                                                    <td>₹ ${item.pending}</td>
                                                                                        </tr>
                                                                                    `;
                                                        });
                                                    } else {
                                                        rows = `<tr><td colspan="4">No pending payments found</td></tr>`;
                                                    }
                                                    $("#pendingTableBody").html(rows);
                                                },
                                                error: function () {
                                                    alert("Error fetching pending payments.");
                                                }
                                            });
                                        });
                                    </script>





                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-12 col-lg-12 col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title d-inline-block"><i class="fa fa-user icon-style1"></i> New Patients
                                </h4>
                                @if (app('hasPermission')(28, 'view'))
                                    <a href="{{ route('patients.index') }}" class="btn btn-primary btn-rounded float-right">
                                        View all <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                @endif



                            </div>
                            <div class="card-body" style="height: 445px;overflow-y: scroll;">
                                <div class="table-responsive">
                                    <table class="table custom-table">
                                        <thead style="background-color:#ff8e29;">
                                            <tr>
                                                <th style="border-right: 1px solid #ffffff75;">Id</th>
                                                <th style="border-right: 1px solid #ffffff75;">Patient</th>
                                                <th style="border-right: 1px solid #ffffff75;">Age</th>
                                                <th style="border-right: 1px solid #ffffff75;">Phone</th>
                                                
                                            </tr>
                                        </thead>
                                        <tbody class="text-center" id="patientTableBody">
                                            <!-- Data will be inserted here -->
                                        </tbody>
                                    </table>

                                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


                                    <script>
                                        $(document).ready(function () {
                                            let branchId = localStorage.getItem('selectedBranchId'); // ? get from localStorage
                                           // console.log("Branch ID from localStorage:", branchId);
                                            $.ajax({
                                                url: "{{ url('/api/patient') }}",
                                                type: "GET",
                                                data: {
                                                    branch_id: branchId, // ? send branch_id
                                                    type: "" // optional: if you want to send "home" or "op", otherwise keep empty
                                                },
                                                dataType: "json",
                                                success: function (response) {
                                                    let patientTableBody = $("#patientTableBody");
                                                    patientTableBody.empty();

                                                 //   console.log("Patients response:", response.patients); // ? debug log

                                                    if (response.patients && response.patients.length > 0) {
                                                        // Already branch filtered by backend
                                                        // Get last 5 patients only
                                                        let latestPatients = response.patients.slice(-5).reverse();

                                                        let defaultImage =
                                                            "{{ asset(env('IMAGE_PATH') . 'admin/assets/img/img1.png') }}";

                                                        $.each(latestPatients, function (index, patient) {
                                                            let imageUrl = patient.profile ? patient.profile : defaultImage;

                                                            function ucfirst(str) {
                                                                return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
                                                            }

                                                            let row = `
                                                                                                                                                                            <tr class="clickable-row" style="cursor:pointer" data-id="${patient.id}">
                                                                                                                                                                            <td>${patient.patient_unique_id}</td>
                                                                                                                                                                                <td class="text-left">
                                                                                                                                                                                    <img width="50" height="50" src="${imageUrl}" class="rounded-circle" alt="User Image"
                                                                                                                                                                                        onerror="this.onerror=null;this.src='${defaultImage}';">
                                                                                                                                                                                    <span>${ucfirst(patient.fullname)}</span>
                                                                                                                                                                                </td>
                                                                                                                                                                                <td>${patient.age}</td>
                                                                                                                                                                                <td>${patient.phone}</td>

                                                                                                                                                                            </tr>
                                                                                                                                                                        `;

                                                            patientTableBody.append(row);
                                                        });

                                                        // ? clickable row redirect
                                                        $(".custom-table tbody").on("click", ".clickable-row", function () {
                                                            let patientId = $(this).data("id");
                                                            window.location.href = `/patient/show/${patientId}`;
                                                        });

                                                    } else {
                                                        patientTableBody.append('<tr><td colspan="5">No patients found.</td></tr>');
                                                    }
                                                },
                                                error: function (xhr, status, error) {
                                                    console.error("Error fetching patients:", error);
                                                    $("#patientTableBody").append('<tr><td colspan="5">Failed to load data.</td></tr>');
                                                }
                                            });
                                        });
                                    </script>


                                </div>
                            </div>
                        </div>
                    </div>





                </div>

            </div>
        @endif



    </div>

     @if (
            isset($appointmentsToday, $followupsToday) &&
            (!$appointmentsToday->isEmpty() || !$followupsToday->isEmpty()) &&
            session('show_today_appointment_modal') === true
        )
        <div class="modal fade" id="todaysAppointmentsModal" tabindex="-1" aria-labelledby="todaysAppointmentsLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header" style="background-color:#f5b6a5;color:black">
                        <h5 class="modal-title" id="todaysAppointmentsLabel">Today's Schedule -
                            {{ \Carbon\Carbon::today()->format('d M Y') }}
                        </h5>
                      
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body" style="max-height: 300px; overflow-y: auto;">
                        <h5 class="mb-3 font-weight-bold">Today's Appointments</h5>
                        @if ($appointmentsToday->isEmpty())
                            <p class="text-muted">No appointments scheduled for today.</p>
                        @else
                            <div class="row">

                                @foreach ($appointmentsToday as $appointment)
                                    <div class="col-md-12 mb-3">

                                        <a href="{{ route('appointment.show', $appointment->id) }}"
                                            class="text-decoration-none text-dark view-appointment" data-id="{{ $appointment->id }}"
                                            style="cursor: pointer;">
                                            <div class="card shadow-sm border rounded-3 m-0">
                                                <div class="card-body py-3">
                                                    <div class="d-flex justify-content-between">
                                                        <div>
                                                            <h5 class="card-title w-100 mb-1" style="display: block !important; white-space: normal !important; line-height: 1.4 !important;">
                                                                Dr. <strong class="fw-bold">
                                                                    {{ $appointment->doctor['fullname'] ?? '-' }}
                                                                </strong> has appointment with <strong>
                                                                    {{ $appointment->patient['fullname'] ?? '-' }}</strong>
                                                                on <strong>
                                                                    {{ \Carbon\Carbon::parse($appointment->date . ' ' . $appointment->duration)->format('d M Y, h:i A') }}</strong>
                                                            </h5>
                                                            <p class="card-text mb-0 w-100" style="display: block !important; white-space: normal !important;">
                                                                Type: <strong> {{ ucfirst($appointment->appoint_type) }}
                                                                </strong> |
                                                                Treatment: <strong>
                                                                    {{ $appointment->treatment['name'] ?? '-' }}
                                                                </strong>
                                                            </p>
                                                        </div>

                                                        <div>
                                                            <span
                                                                class="custom-badge btn btn-rounded float-end {{ $appointment->status ?? '' }}">
                                                                {{ ucfirst($appointment->status) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach


                            </div>
                        @endif

                        <hr />
                        <h5 class="mt-4 mb-3 font-weight-bold">Today's Follow-ups</h5>
                        @if ($followupsToday->isEmpty())
                            <p class="text-muted">No follow-ups scheduled for today.</p>
                        @else
                            <div class="row">
                                @foreach ($followupsToday as $followup)
                                    <div class="col-md-12 mb-3">
                                        <a href="{{ route('followup.show', $followup->id) }}"
                                            class="text-decoration-none text-dark view-followup"
                                            data-id="{{ $followup->id ?? '' }}" style="cursor: pointer;">
                                            <div class="card shadow-sm border rounded-3 m-0 w-100">
    <div class="card-body py-3">
        <div class=" w-100">
            <div style="min-width: 0; width: 100%;">

                <h5 class="card-title mb-1"
                    style="
                        display: block !important;
                        white-space: normal !important;
                        overflow-wrap: break-word !important;
                        word-wrap: break-word !important;
                        width: 100% !important;
                        line-height: 1.4 !important;
                    ">

                    @if(Auth::check() && optional(Auth::user()->role)->name == 'Admin')
                        Dr.
                        <strong class="fw-bold">
                            {{ $followup->doctor['fullname'] ?? '-' }}
                        </strong>
                        has a follow-up with
                    @endif

                    <strong>
                        {{ $followup->patient['fullname'] ?? '-' }}
                    </strong>

                    on

                    <strong>
                        {{ \Carbon\Carbon::parse($followup->date)->format('d M Y') }}
                    </strong>
                </h5>

               <p class="card-text mb-0 w-100" style="display: block !important; white-space: normal !important;">
                    Type:
                    <strong>{{ ucfirst($followup->followup_type) }}</strong>
                    |
                    Treatment:
                    <strong>{{ $followup->treatment['name'] ?? '-' }}</strong>
                </p>

            </div>
        </div>
    </div>
</div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <hr />
                        <h5 class="mt-4 mb-3 font-weight-bold">Expired Treatment Plans</h5>
                        @if ($expiredPlans->isEmpty())
                            <p class="text-muted">No treatment plans expiring today.</p>
                        @else
                            <div class="row">
                                @foreach ($expiredPlans as $plan)
                                    <div class="col-md-12 mb-3">
                                        <a href="{{ route('treatment.show', $plan->id) }}"
                                            class="text-decoration-none text-dark view-plan" data-id="{{ $plan->id }}"
                                            style="cursor: pointer;">
                                            <div class="card shadow-sm border rounded-3 m-0">
                                                <div class="card-body py-3">
                                                    <div class="d-flex justify-content-between">
                                                        <div>
                                                            <h5 class="card-title">
                                                                Patient:
                                                                <strong>{{ $plan->patient->fullname ?? '-' }}</strong> <br>
                                                                Treatment:
                                                                <strong>{{ $plan->treatment->name ?? '-' }}</strong>
                                                            </h5>
                                                        </div>
                                                        <div>
                                                            <span class="badge"
                                                                style="padding: 8px 15px;background-color: #f5b6a5 !important;border-radius: 50px !important;font-size: 12px;">
                                                                Expiring Today
                                                            </span>

                                                        </div>
                                                    </div>
                                                    <p class="card-text mb-0">
                                                        Plan: <strong>{{ ucfirst($plan->plan ?? '-') }}</strong> |
                                                        Remaining Amount: <strong>{{ $plan->remain_amount }}</strong> <br>
                                                        <span class="text-danger">
                                                            ⚠️ This treatment plan will expire today
                                                            ({{ \Carbon\Carbon::parse($plan->payment_date)->format('d M Y') }})
                                                        </span>
                                                    </p>



                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @endif


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger btn-rounded d-flex align-items-center"
                            id="dontShowAgainBtn">
                            Don't show again
                            <span class="spinner-border spinner-border-sm ml-2 d-none" role="status" aria-hidden="true"
                                id="loadingSpinner"></span>
                        </button>
                        <button type="button" class="btn btn-dark btn-rounded" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            $(document).on('click', '.view-appointment', function (e) {
                e.preventDefault(); // Prevent default anchor action
                var appointmentId = $(this).data('id');
                window.location.href = '/appointment/show/' + appointmentId;
            });
            $(document).on('click', '.view-followup', function (e) {
                e.preventDefault();
                var followupId = $(this).data('id');
                window.location.href = '/followup/show/' + followupId;
            });

            $(document).on('click', '.view-plan', function (e) {
                e.preventDefault();
                var planId = $(this).data('id');
                window.location.href = '/treatment_booking/show/' + planId;
            });


            $(document).ready(function () {
                $('#todaysAppointmentsModal').modal('show');
                $('#dontShowAgainBtn').on('click', function () {
                    var $btn = $(this);
                    var $spinner = $('#loadingSpinner');

                    $btn.prop('disabled', true); // disable the button
                    $spinner.removeClass('d-none'); // show the spinner

                    $.ajax({
                        url: '{{ route('hide.today.appointments') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            $('#todaysAppointmentsModal').modal('hide');
                        },
                        complete: function () {
                            $spinner.addClass('d-none'); // hide spinner
                            $btn.prop('disabled', false); // re-enable the button if needed
                        }
                    });
                });
            });
        </script>
    @endif



    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('toggle_btn');
            const sidebar = document.querySelector('.sidebar');

            toggleBtn.addEventListener('click', function () {
                if (sidebar) {
                    sidebar.classList.toggle('mini-sidebar');
                }
            });
        });

        $(document).ready(function () {

            // Fetch Total Users Count
            let branchId = localStorage.getItem('selectedBranchId');


            $(document).ready(function () {
                $.ajax({
                    url: "/api/patient-counts",
                    type: "GET",
                    data: { branch_id: branchId },
                    success: function (res) {
                        if (res.status) {
                            $(".home_patient").text(res.data.home_patient);
                            $(".cosmatic_patient").text(res.data.cosmetic_patient);
                            $(".ipd_patient").text(res.data.ipd_patient);
                        }
                    },
                    error: function (err) {
                        console.error("Error fetching patient counts", err);
                    }
                });
            });


            // followup
            $.ajax({
                url: '/api/total-followup',
                method: 'GET',
                data: {
                    branch_id: branchId
                },
                headers: {
                    "Authorization": "Bearer " + token,
                    "User-ID": userId
                },
                success: function (response) {
                    $('.total-users-count').text(response.total_followup);
                },
                error: function (error) {
                    console.log('Error fetching total users:', error);
                }
            });

            $.ajax({
                url: '/api/all-followup',
                method: 'GET',
                data: {
                    branch_id: branchId
                },
                headers: {
                    "Authorization": "Bearer " + token,
                    "User-ID": userId
                },
                success: function (response) {
                    $('.all-followup-count').text(response.total_followups);
                },
                error: function (error) {
                    console.log('Error fetching total users:', error);
                }
            });





            $.ajax({
                url: '/api/total-patient',
                method: 'GET',
                data: {
                    branch_id: branchId // ? send selected branch
                },
                headers: {
                    "Authorization": "Bearer " + token,
                    "User-ID": userId
                },
                success: function (response) {
                    $('.total-patient-count').text(response.total); // ? use "total"
                },
                error: function (error) {
                    console.log('Error fetching total patients:', error);
                }
            });








            $.ajax({
                url: '/api/total-appointment',
                method: 'GET',
                data: {
                    branch_id: branchId
                }, // ? send branch id
                headers: {
                    "Authorization": "Bearer " + token,
                    "User-ID": userId
                },
                success: function (response) {
                    $('.total-appointment-count').text(response.total_appointment);
                },
                error: function (error) {
                    console.log('Error fetching total appointments:', error);
                }
            });

            // Fetch today's appointment count (branch-wise)
            $.ajax({
                url: '/api/total-today-appointment',
                method: 'GET',
                data: {
                    branch_id: branchId
                }, // ? send branch id
                headers: {
                    "Authorization": "Bearer " + token,
                    "User-ID": userId
                },
                success: function (response) {
                    $('.total-doctors-count').text(response.total_appointments);
                },
                error: function (error) {
                    console.log('Error fetching total doctors:', error);
                }
            });





            $(document).ready(function () {
                let branchId = localStorage.getItem('selectedBranchId');

                $.ajax({
                    url: '/api/today-stats',
                    method: 'GET',
                    data: {
                        branch_id: branchId
                    }, // ? send branch_id
                    success: function (response) {
                        // Format income with ₹ and commas
                        let formattedIncome = new Intl.NumberFormat('en-IN').format(response
                            .today_income);
                        $('.today-income').text('₹ ' + formattedIncome);

                        // Patients
                        $('.today-patients').text(response.today_patients);

                        // Birthdays
                        $('.today-birthdays').text(response.today_birthdays);


                        // Expenses
                        $('.total-expense').text('₹ ' + response.today_expense);
                    }
                });
            });



            $(document).ready(function () {
                $('.today-birthdays').closest('.col-6').on('click', function (e) {
                    e.preventDefault();

                    let branchId = localStorage.getItem('selectedBranchId'); // ? get branch id

                    $.ajax({
                        url: '/api/today-birthday-users',
                        method: 'GET',
                        data: {
                            branch_id: branchId
                        }, // ? send branch_id
                        success: function (response) {
                            if (response.count === 0) {
                                Swal.fire({
                                    icon: 'info',
                                    title: 'No Birthdays Today',
                                    text: 'No patients have a birthday today.',
                                    confirmButtonText: 'OK',
                                    customClass: {
                                        confirmButton: 'custom-ok-btn'
                                    }
                                });
                            } else {
                                let listHtml = '';
                                response.users.forEach(user => {
                                    listHtml += `
                                                                                                                <div style="
                                                                                                                  border: 1px solid #f89884;
                                                                                                                  padding: 8px;
                                                                                                                  border-radius: 5px;
                                                                                                                  margin-bottom: 5px;
                                                                                                                  display: flex;
                                                                                                                  justify-content: space-between;
                                                                                                                  align-items: center;
                                                                                                                  background: #fff;
                                                                                                                ">
                                                                                                                    <span style="font-weight: 500; color: #333;">
                                                                                                                        ${user.fullname}
                                                                                                                    </span>
                                                                                                                    <span style="font-size: 14px; color: #666;">
                                                                                                                        ?? ${user.birth_date}
                                                                                                                    </span>
                                                                                                                </div>
                                                                                                            `;
                                });
                                Swal.fire({
                                    title: "Today's Birthday(s)",
                                    html: listHtml,
                                    showCloseButton: true,
                                    confirmButtonText: 'Wish Them',
                                    width: 500,
                                    customClass: {
                                        confirmButton: 'custom-ok-btn'
                                    }
                                });
                            }
                        },
                        error: function () {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Could not fetch birthday data.'
                            });
                        }
                    });
                });
            });




        });
    </script>

    <style>
        #calendar {
            max-width: 100%;
            margin: 0;
            padding: 14px;
            background-color: #fff;
            border-radius: 20px;
            height: 523px;
        }

        .fc-scroller.fc-day-grid-container {
            height: 305px !important;
        }

        .fc-row.fc-week.fc-widget-content.fc-rigid {
            height: 58px !important;
        }

        hr {
            margin: 0px !important;
        }

        .fc-event {
            border-radius: 50px !important;
            width: 12px !important;
            height: 12px !important;
            padding: 0 !important;
            display: inline-block !important;
            text-indent: -9999px;
            /* hides the text */
            overflow: hidden;
        }

        .custom-ok-btn {
            background-color: #cfece0 !important;
            /* Red */
            color: black !important;
            border-radius: 6px !important;
            padding: 8px 20px !important;
            font-weight: 600 !important;
            border: none !important;
        }

       
           
    </style>

@endsection

