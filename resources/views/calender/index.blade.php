@extends('layout.app')
<style>
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

    .bold {
        font-weight: 500;
        font-size: 17px;
    }

    .fc-event {
        border-radius: 50px !important;
        width: 15px !important;
        height: 15px !important;
        padding: 0 !important;
        display: inline-block !important;
        text-indent: -9999px;
        /* hides the text */
        overflow: hidden;

    }


    @media screen and (max-width: 767px) {
        .page-title {
            font-size: 19px !important;
            padding-left: 10px !important;
            text-align: left !important;
            padding-top: 18px !important;
        }

        .filter-event-state {
            padding-top: 10px !important;
        }

        .fc-toolbar {
            /* display: flex !important; */
            align-items: center;
            justify-content: space-between;
            /* or center/left based on your need */
            flex-wrap: nowrap;
            gap: 10px;
        }

        .fc-toolbar>div {
            margin: 0 !important;
        }

        .fc .fc-toolbar>*>* {
            float: left;
            /* margin-left: 0 !important; */
        }

        .fc-button {
            padding: 6px 10px !important;
        }



        .fc-left,
        .fc-right {
            /* display: flex; */
            align-items: center;
            gap: 10px;
        }

        .fc-center h2 {
            margin: 0;
            /* margin-left: 10px !important ; */

        }

        .fc-center {
            text-align: center;
            margin: 0px auto;

            justify-content: center !important;
            /* padding-top: 12px !important; */
        }

        .fc-next-button,
        .fc-today-button {
            margin-left: 5px !important;
            /* margin-top: 2px !important; */
        }

        .fc-next-button,
        .fc-prev-button {

            margin-top: 3px !important;
        }


        .fc-month-button,
        .fc-agendaWeek-button {
            margin-right: 5px !important;
        }

        .today-appointment {
            padding-left: 0 !important;
        }


    }




    @media (min-width: 360px) and (max-width: 667px) {
        .fc-toolbar {

            align-items: center;
            justify-content: space-between;

            flex-wrap: nowrap;
            gap: 10px;
        }

        .fc-toolbar>div {
            margin: 0 !important;
        }

        .fc .fc-toolbar>*>* {
            float: left;

        }

        .fc-button {
            padding: 6px 6px !important;
        }



        .fc-left,
        .fc-right {

            align-items: center;
            gap: 10px;
        }

        .fc-center h2 {
            margin: 0;


        }

        .fc-center {
            text-align: center;
            margin: 0px auto;

            justify-content: center !important;

        }

        .fc-next-button,
        .fc-today-button {
            margin-left: 3px !important;

        }

        .fc-next-button,
        .fc-prev-button {

            margin-top: 3px !important;
        }


        .fc-month-button,
        .fc-agendaWeek-button {
            margin-right: 3px !important;
        }

        .today-appointment {
            padding-left: 0 !important;
        }

        .fc-right .fc-button-group {
            margin-left: 3px !important;
        }


    }


    @media (min-width: 768px) {



        .fc-button {
            padding: 6px 12px !important;
        }


        .fc-next-button,
        .fc-prev-button {

            margin-top: 4px !important;
        }

        .fc-next-button {
            margin-left: 8px !important;
            /* margin-top: 2px !important; */
        }

        .fc-month-button,
        .fc-agendaWeek-button {
            margin-right: 8px !important;
        }



    }
</style>
@section('content')
    <div class="page-wrapper">
        <div class="content" style="height:100vh">
            <div class="row" style="padding-top:15px">
                <div class="col-sm-4 col-4">
                    <h4 class="page-title" style="text-align:left;">Calendar</h4>
                </div>
                <div class="col-8">
                    <div class="row">
                        <div class="col-sm-6 col-6 filter-event-state">
                            <div class="form-group">
                                <select class="form-control" id="filter-event-state">
                                    <!-- Options loaded dynamically from API -->
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-6 today-appointment">
                            <div class="form-group">
                                <!-- <select class="form-control" id="filter-event-type">
                                                                                            <option value="All">All Appointments</option>
                                                                                            <option value="Today">Today’s Appointments</option>
                                                                                            <option value="Upcoming">Upcoming Appointments</option>
                                                                                        </select> -->
                                <div id="filter-event-type">

                                    <button class="btn btn-primary"><i class="fa fa-calendar px-2"
                                            style="font-size:15px"></i>
                                        Today's Appointment</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>


            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box mb-0">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="d-flex flex-wrap justify-content-center m-auto pb-4 py-1 status-container">
                                    <div class="align-items-center d-flex p-o">
                                        <div class="status-dot confirmed rounded-circle" title="Confirmed"></div>
                                        <div class="px-3 bold">Confirmed</div>
                                    </div>
                                    <div class="align-items-center d-flex p-o">
                                        <div class="status-dot upcoming rounded-circle" title="Upcoming"></div>
                                        <div class="px-3 bold">Upcoming</div>
                                    </div>
                                    <div class="align-items-center d-flex p-o">
                                        <div class="status-dot cancelled rounded-circle" title="Cancelled"></div>
                                        <div class="px-3 bold">Cancelled</div>
                                    </div>
                                    <div class="align-items-center d-flex p-o">
                                        <div class="status-dot completed rounded-circle" title="Completed"></div>
                                        <div class="px-3 bold">Completed</div>
                                    </div>
                                    <div class="align-items-center d-flex p-o">
                                        <div class="status-dot follow-up rounded-circle" title="Follow-up"></div>
                                        <div class="px-3 bold">Follow-up</div>
                                    </div>

                                </div>
                                <div id="mycalendar"></div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>

        <script>


            $(document).ready(function () {
                $.ajax({
                    url: "/api/calendar-statuses",
                    method: "GET",
                    success: function (data) {
                        var $statusDropdown = $("#filter-event-state");
                        $statusDropdown.empty();
                        $.each(data, function (index, status) {
                            $statusDropdown.append('<option value="' + status + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</option>');
                        });
                    },
                    error: function () {
                        console.log("Error fetching statuses.");
                    }
                });
            });

        </script>




@endsection