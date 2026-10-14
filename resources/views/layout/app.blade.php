<!DOCTYPE html>

<html lang="en">





<!-- index22:59-->



<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
<meta name="robots" content="noindex, nofollow"> 
 
<!-- <link rel="shortcut icon" type="image/x-icon" href="{{asset(env('IMAGE_PATH').'admin/assets/img/favicon.ico')}}"> -->

    <link rel="shortcut icon" type="image/x-icon"
        href="{{asset(env('IMAGE_PATH') . 'admin/assets/img/logo.png')}}">

    <!-- Add to homescreen configuration -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Fablead-HMS">
    <link rel="apple-touch-icon" href="{{asset(env('IMAGE_PATH') . 'admin/assets/img/logo.png')}}">

    <title>Fablead-HMS</title>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="{{asset(env('IMAGE_PATH') . 'admin/assets/css/bootstrap.min.css')}}">

    <link rel="stylesheet" type="text/css"
        href="{{asset(env('IMAGE_PATH') . 'admin/assets/css/font-awesome.min.css')}}">

    <link rel="stylesheet" type="text/css"
        href="{{asset(env('IMAGE_PATH') . 'admin/assets/css/fullcalendar.min.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset(env('IMAGE_PATH') . 'admin/assets/css/select2.min.css')}}">

    <!-- <link rel="stylesheet" type="text/css" href="{{asset(env('IMAGE_PATH').'admin/assets/css/bootstrap-datetimepicker.min.css')}}"> -->

    <link rel="stylesheet" type="text/css" href="{{asset(env('IMAGE_PATH') . 'admin/assets/css/style.css')}}">

    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">

    <script src=" https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="{{ asset(env('IMAGE_PATH') . 'admin/assets/js/select2.min.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css">

    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<link rel="stylesheet" type="text/css" href="{{asset(env('IMAGE_PATH') . 'admin/assets/css/common.css')}}">

<link rel="stylesheet" type="text/css" href="{{asset(env('IMAGE_PATH') . 'admin/assets/css/app.css')}}">


<style>
        .select2-container--default .select2-selection--single.is-invalid {
            border: 2px solid #e74c3c !important;
            background-color: #fff6f6 !important;
        }


        .form-control.is-invalid,
        .was-validated .form-control:invalid {
            border: 2px solid #e74c3c !important;
        }

        /* searchbar inside dropdown */
        .select2-container .select2-selection--single {
            height: 40px !important;
            border: 1px solid rgb(207 236 224) !important;
            border-radius: 25px !important;
            padding: 6px 12px;
            font-size: 14px;
            color: black !important;
            background-color: #fff;
            box-shadow: none;
        }

        .select2-dropdown {
            background-color: white;
            border: 1px solid rgb(207 236 224) !important;
            border-radius: 4px;
            box-sizing: border-box;
            display: block;
            position: absolute;
            left: -100000px;
            width: 100%;
            z-index: 1051;
            margin-top: 6px !important;
            border-radius: 15px !important;
            /* border-top: 0 !important; */
        }

        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background-color: rgb(207 236 224) !important;
            color: black !important;
            border-radius: 15px !important;
            margin: 2px !important;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid rgb(207 236 224) !important;
            border-radius: 25px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 26px;
            color: #6c757d;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px !important;
            right: 10px;
        }

        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #495057 !important;
        }

        .select2-search__field {
            padding-left: 10px !important;
        }

        .select2-container--default .select2-selection--multiple .select2-search__field {
            display: inline-block !important;
            width: 100% !important;
            min-height: 35px;
            padding: 6px 8px;
            margin: 4px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            background-color: white;
        }




        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background-color: rgb(207, 236, 224) !important;
            color: black !important;
            border-radius: 20px !important;
            /* padding: 2px 10px !important; */
            border: none;
            font-size: 14px;
            font-weight: normal !important;
            text-align: center;
            max-width: fit-content;
        }

        .select2-container--default .select2-results__option--selected {
            background-color: rgb(207 236 224) !important;
            border-radius: 15px !important;
            margin: 2px !important;
            color: black !important;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: rgb(207 236 224) !important;
            border-radius: 15px !important;
            margin: 2px !important;
            color: black !important;
        }









        .table th {

            border-top: 0 !important;

        }

        .table {
            color: #000 !important;
            border-collapse: separate !important;
            border-spacing: 0 !important;
            border: 1px solid #dee2e6 !important;
            border-radius: 20px 20px 0 0 !important;
            /* overflow: hidden !important; */
        }

        div.dt-container.dt-empty-footer tbody>tr:last-child>* {
            border-bottom: 1px solid transparent !important;
        }

        .all {

            display: flex;

            justify-content: space-between;

            align-items: center;



        }



        .fc-toolbar.fc-header-toolbar {

            margin-bottom: 0 !important;

        }









        table.dataTable thead>tr>th.dt-ordering-asc span.dt-column-order:before,

        table.dataTable thead>tr>th.dt-ordering-desc span.dt-column-order:after,

        table.dataTable thead>tr>td.dt-ordering-asc span.dt-column-order:before,

        table.dataTable thead>tr>td.dt-ordering-desc span.dt-column-order:after {

            opacity: 6 !important;

        }



        table.dataTable th.dt-type-numeric,

        table.dataTable th.dt-type-date,

        table.dataTable td.dt-type-numeric,

        table.dataTable td.dt-type-date {

            text-align: left !important;

        }







        .chart-container {

            width: 512px;

            height: 430px;

            /* Adjust as needed */

        }



        .icon-style {

            background-color: white;



            color: rgb(157 195 179);

            padding: 10px;

            border-radius: 50%;



        }



        .icon-style1 {

            background-color: white;

            font-size: 25px;

            color: rgb(157 195 179);

            padding: 10px;

            border-radius: 50%;



        }





        .form-container {

            width: 100%;



            padding: 30px;

            background-color: #ffffff;

            border-radius: 10px;

            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);

            margin: 20px auto;

        }



        .offset-lg-2 {

            margin: auto;

            width: 80%;

        }



        .icon {

            font-size: 15px;

            display: flex;



        }



        .icon .icon1 {

            padding: 8px;

            background-color: #f89884;

            color: white;

            border-radius: 10px;



        }



        .icon .icon2 {

            padding: 8px;

            background-color: #f89884;

            color: white;

            border-radius: 10px;

        }



        .icon .icon3 {

            padding: 8px;

            background-color: #f89884;

            color: white;

            border-radius: 10px;

        }



        .icon .icon4 {

            padding: 8px;

            background-color: #f89884;

            color: white;

            border-radius: 10px;

        }


        table.dataTable>thead>tr>th,
        {
        padding: 10px;
        border-bottom: 1px solid #f89884 !important;



        }

        .table thead th {
            /* border-bottom: 1px solid #f89884 !important; */
            /* border-top-right-radius: 20px !important; */


        }

        /* .dt-orderable-asc {
            border-top-left-radius: 20px !important;
        } */

        .table thead tr {

            background-color: #f89884;

            color: white;

            text-align: center;

            border-top-right-radius: 20px !important;

            border-top-left-radius: 20px !important;

        }



        .table thead th {

            text-align: center;

            border-right: 1px solid #ffffff75;

        }



        .page-title {

            color: #565656;

            font-size: 25px;

            font-weight: normal;

            margin-bottom: 10px;

            text-align: left;

            padding-left: 12px;

        }





        @media (max-width: 767px) {




            /* a.mobile_btn {

                padding: 20px 20px ;

            }  */



            .userProfile {
                width: 100px !important;
                height: 100px !important;
            }

            .margin-text {
                margin: 5px !important;
            }

            .margin-img {
                margin: auto 0;
            }

            .icon-style1 {


                font-size: 15px;

            }








            div.dt-container div.dt-layout-row:not(.dt-layout-table) {
                display: flex !important;

                align-items: stretch;
                gap: 10px;
                /* Optional spacing */
            }

            .dt-search label {
                display: none !important;
            }

            .dt-length label {
                display: none !important;
            }

            /* Style the search input placeholder */
            input[type="search"]::placeholder {
                color: #999;
                font-size: 14px;
                text-align: center;
                /* Optional */
            }

            input[type="search"] {
                width: 100% !important;
                font-size: 14px;
                padding: 8px 12px;
                border-radius: 10px;
                border: 1px solid #f4a48b;
            }






            div.dt-container select.dt-input {
                padding: 9px !important;
            }

            .header .wrap {

                display: none;

            }

            div.dt-container .dt-input {

                border-radius: 50px !important;

            }


            /* 
            .page-title {

                padding: 0 !important;

            } */



            .eye-btn {

                /* padding: 0 !important; */

                margin: 0 !important;



            }



            .smssend {

                margin-left: 0 !important;

            }



            .prv-btn {

                padding: 10px 33px !important;

            }



            .all-form {

                width: 100% !important;

            }



            .form-container {

                width: 100% !important;
                padding: 10px !important;

            }



            .table-responsive {

                overflow-x: auto !important;

                -webkit-overflow-scrolling: touch !important;

                /* For smooth scrolling on mobile */

            }



            /* .patient-form {

                height: 760px;

            } */

            .edit-patient-form {
                height: 800px !important;
            }

            .patient-form2 {

                height: 755px;

            }

            .page-title {
                font-size: 14px !important;
                padding-left: 10px !important;
                text-align: left !important;
                padding-top: 5px !important;
            }


            .appo-form {

                height: 580px;

            }



            .follow-form {

                height: 735px;

            }

            .medi-form {
                height: 505px;
            }

            .view-service {
                margin-left: 0 !important;
            }

            .offset-lg-2 {
                width: 100%;
                padding: 10px;
            }

            .view-discharge {
                padding-right: 15px !important;
                padding-left: 15px !important;
            }

            .discharge-form1 {
                height: 800px;
            }

            .discharge-form2 {
                height: 690px;
            }

            .medicine-form {

                height: 510px;

            }


            .report-form {

                height: 600px;

            }



            .buttons {

                display: flex;

            }



            .emailtemp {

                margin-right: 5px;

            }



            /* .offset-lg-2{

         width: 0;

        } */



            .nav-tabs {

                flex-direction: column;

                /* Stack items vertically */

                display: flex;

                padding: 0;

            }



            .nav-tabs .nav-item {

                width: 100%;

                /* Full width */

                text-align: center;

            }



            .nav-tabs .nav-link {

                display: block;

                width: 100%;

                text-align: center;

                padding: 10px 15px;

                border: 1px solid #ddd;

                /* Optional: Add border to separate tabs */

                border-radius: 5px;

                margin-bottom: 5px;

                /* Space between tabs */

            }

            #multiStepForm {

                width: 100% !important;

            }




        }

        @media (min-width: 768px) and (max-width: 1366px) {

            #multiStepForm,
            #appointmentForm,
            #editTreatmentForm,
            #createTreatmentForm,
            #followupForm,
            #medicineForm,
            #categoryForm,
            #editCategoryForm,
            #createmedicineForm,
            #createServiceForm,
            #editServiceForm,
            #dischargeform,
            #editSupplierForm,
            #createSupplierForm,
            #medicalReportForm,
            #editProfileForm {

                width: 100% !important;

            }

            .discharge-title {
                text-align: left !important;
                /* padding-left: 180px; */
            }

            .calender-apointment,
            .about-padding {
                padding-top: 9px !important;
            }

            .report-title {
                text-align: left !important;
                /* padding-left: 160px !important; */
            }

            .report-btn {
                padding-right: 15px !important;
                text-align: right !important;
            }

            .sms-float {
                text-align: right !important;
            }

            .template-create {
                text-align: left !important;
                padding-left: 15px !important;
            }

            .service-title {
                text-align: left !important;
                padding-left: 15px !important;
            }

            .supplier-btn {
                text-align: right;
                padding-right: 15px !important;
            }

            .inventory-btn {
                text-align: right !important;
                padding-right: 15px !important;
            }

            .inventory-title {
                text-align: left !important;
                padding-left: 15px !important;
            }

            .discharge-btn {
                padding-right: 15px !important;
                text-align: right !important;
            }

            .service-text {
                padding-left: 0 !important;
                text-align: left !important;

            }

            .service-btn {
                text-align: right !important;
                padding-right: 15px !important;

            }

            .pm-title {
                padding-left: 15px !important;
                text-align: left !important;
            }

            .pm-button {
                padding-right: 15px !important;
                text-align: right !important;
            }

            .offset-lg-2 {
                width: 100%;
                padding: 10px;
            }

            .category-title {
                padding-left: 15px !important;
                text-align: left !important;
            }

            .category-button {
                padding-right: 15px !important;
                text-align: right !important;
            }

            .staff-title {
                padding-left: 0px !important;
                text-align: left !important;
            }

            .staff-button {
                padding-right: 15px !important;
                text-align: right !important;
            }

            .patient-title {
                padding-left: 15px !important;
                text-align: left !important;
            }

            .patient-button {
                padding-right: 15px !important;
                text-align: right !important;
            }

            .appointment-title {
                padding-left: 15px !important;
                text-align: left !important;
            }

            .appointment-button {
                padding-right: 15px !important;
                text-align: right !important;
            }

            .treatment-title {
                padding-left: 0px !important;
                text-align: left !important;

            }

            .treatment-button {
                padding-right: 15px !important;
                text-align: right !important;
            }

            .followup-title {
                padding-left: 15px !important;
                text-align: left !important;
            }

            .followup-button {
                padding-right: 15px !important;
                text-align: right !important;
            }

            .medicine-title {
                padding-left: 15px !important;
                text-align: left !important;
            }

            .medicine-button {
                padding-right: 15px !important;
                text-align: right !important;
            }
                    .row:has(> [class*="col-"] > .page-title) .eye-btn,
        .row:has(> [class*="col-"] > .page-title) .patient-button,
        .row:has(> [class*="col-"] > .page-title) [class*="col-"]:last-child {
            display: flex !important;
            align-items: center !important;
            justify-content: flex-end !important;
            margin: 0 !important;
        }

        }

        /* =========================================================
           Header Button Class: full on desktop, icon-only on mobile
           ========================================================= */
        .btn-hdr {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 5px !important;
            float: none !important;
            margin: 0 !important;
            border-radius: 50px !important;
        }

        .btn-hdr .hdr-btn-text {
            display: inline !important;
        }

        @media only screen and (max-width: 767.98px) {
            .btn-hdr .hdr-btn-text {
                display: none !important;
            }

            .btn-hdr {
                min-width: 32px !important;
                height: 32px !important;
                padding: 6px 8px !important;
                gap: 0 !important;
            }

            .btn-hdr i {
                font-size: 14px !important;
                margin: 0 !important;
            }
        }

        /* =========================================================
           Mobile View Padding, Dashboard & Modal Fixes (<= 767.98px)
           ========================================================= */
        @media only screen and (max-width: 767.98px) {
            .page-wrapper > .content,
            .content {
                padding: 10px 8px calc(88px + env(safe-area-inset-bottom)) 8px !important;
                height: auto !important;
                min-height: calc(100vh - 60px) !important;
            }

            /* Global minimal padding/margin for Cards, Rows, Forms on mobile */
            .card {
                margin-bottom: 12px !important;
            }
            
            .card-body {
                padding: 12px !important;
            }
            
            .form-group {
                margin-bottom: 12px !important;
            }

            .row {
                margin-left: -8px !important;
                margin-right: -8px !important;
            }
            
            .row > [class*="col-"] {
                padding-left: 3px !important;
                padding-right: 3px !important;
            }

            .row.calender-apointment {
                margin-left: -5px !important;
                margin-right: -5px !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            /* Dashboard Stat Widgets Mobile Sizing */
            .row.about-padding {
                display: flex !important;
                flex-wrap: wrap !important;
                /* margin-top: 2px !important; */
                margin-left: -5px !important;
                margin-right: -5px !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            .row.about-padding > [class*="col-"],
            .row.about-padding .boxes,
            .boxes {
                padding-left: 5px !important;
                padding-right: 5px !important;
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

            .card {
                margin-bottom: 15px !important;
                border-radius: 16px !important;
            }

            .card .card-header {
                padding: 12px 14px !important;
                border-top-left-radius: 16px !important;
                border-top-right-radius: 16px !important;
            }

            .card-table .card-body,
            .card:has(.table-responsive) .card-body,
            .card:has(table) .card-body,
            .card-body.p-0 {
                padding: 0 10px 12px 10px !important;
            }

            /* Form Container Mobile Width & Padding */
            .content form.form-container,
            .content .form-container,
            form.form-container,
            .all-form {
                width: 100% !important;
                max-width: 100% !important;
                padding: 16px 14px 25px 14px !important;
                margin: 10px auto !important;
                border-radius: 14px !important;
                box-sizing: border-box !important;
            }

            .table-responsive {
                padding: 0 !important;
                margin-bottom: 0 !important;
                border: 0 !important;
            }

            .table.custom-table,
            table.dataTable,
            .table {
                width: 100% !important;
                margin-bottom: 0 !important;
            }

            .table.custom-table > tbody > tr > td,
            .table.custom-table > tbody > tr > th,
            .table.custom-table > tfoot > tr > td,
            .table.custom-table > tfoot > tr > th,
            .table.custom-table > thead > tr > td,
            .table.custom-table > thead > tr > th,
            .table td,
            .table th,
            table.dataTable th,
            table.dataTable td {
                padding: 9px 7px !important;
            }

            .table.custom-table > tbody > tr > td:first-child,
            .table.custom-table > thead > tr > th:first-child,
            .table td:first-child,
            .table th:first-child,
            table.dataTable th:first-child,
            table.dataTable td:first-child {
                padding-left: 6px !important;
            }

            .table.custom-table > tbody > tr > td:last-child,
            .table.custom-table > thead > tr > th:last-child,
            .table td:last-child,
            .table th:last-child,
            table.dataTable th:last-child,
            table.dataTable td:last-child {
                padding-right: 6px !important;
            }

            div.dataTables_wrapper {
                padding: 0 !important;
            }

            div.dataTables_wrapper .row {
                margin-left: 0 !important;
                margin-right: 0 !important;
            }

            div.dataTables_wrapper [class*="col-"] {
                padding-left: 4px !important;
                padding-right: 4px !important;
            }

            div.dataTables_wrapper div.dataTables_length,
            div.dataTables_wrapper div.dataTables_filter {
                margin-bottom: 8px !important;
            }

            div.dataTables_wrapper div.dataTables_filter input {
                margin-left: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }

            /* Card Header Mobile Centering */
            .card .card-header,
            .card-header {
                padding: 10px 12px !important;
                min-height: 48px !important;
                gap: 6px !important;
                display: flex !important;
                align-items: center !important;
                flex-wrap: wrap !important;
            }

            .card .card-header .card-title,
            .card-header .card-title {
                font-size: 15px !important;
                line-height: 1 !important;
                margin: 0 !important;
                flex: 1 1 auto !important;
                display: inline-flex !important;
                align-items: center !important;
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
                min-width: 0 !important;
            }

            .card .card-header .card-title i,
            .card-header .card-title i {
                font-size: 16px !important;
                margin-right: 6px !important;
                flex-shrink: 0 !important;
                display: inline-flex !important;
                align-items: center !important;
            }

            /* Button group wrapper inside card header */
            .card .card-header > .card-title ~ *,
            .card-header > .card-title ~ * {
                flex-shrink: 0 !important;
            }

            .card .card-header .btn,
            .card-header .btn,
            .card .card-header button,
            .card-header button,
            .card .card-header a.btn,
            .card-header a.btn {
                height: 32px !important;
                padding: 6px 10px !important;
                font-size: 0 !important;
                /* Hide text, keep icon */
                line-height: 1 !important;
                margin: 0 !important;
                float: none !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                flex-shrink: 0 !important;
                white-space: nowrap !important;
                border-radius: 50px !important;
                min-width: 32px !important;
            }

            /* Restore icon size inside card-header buttons */
            .card .card-header .btn i,
            .card-header .btn i,
            .card .card-header button i,
            .card-header button i,
            .card .card-header a.btn i,
            .card-header a.btn i {
                font-size: 14px !important;
                margin: 0 !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
            }

            /* =========================================================
               Today's Schedule Modal Mobile View Fixes
               ========================================================= */
            #todaysAppointmentsModal .modal-dialog {
                margin: 12px 10px !important;
                max-width: calc(100% - 20px) !important;
            }

            #todaysAppointmentsModal .modal-header {
                padding: 10px 14px !important;
            }

            #todaysAppointmentsModal .modal-title {
                font-size: 14.5px !important;
            }

            #todaysAppointmentsModal .modal-body {
                padding: 10px 12px !important;
                max-height: 360px !important;
            }

            #todaysAppointmentsModal .card,
            #todaysAppointmentsModal .today-schedule-card {
                border: 1px solid #e2e8f0 !important;
                border-radius: 12px !important;
                margin-bottom: 8px !important;
                box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04) !important;
            }

            #todaysAppointmentsModal .card-body {
                padding: 10px 10px !important;
            }

            #todaysAppointmentsModal .card-title,
            #todaysAppointmentsModal .card .card-title {
                font-size: 12.5px !important;
                line-height: 1.4 !important;
                margin-bottom: 4px !important;
                text-align: left !important;
                font-weight: 500 !important;
                color: #2d3748 !important;
                display: block !important;
                white-space: normal !important;
                word-break: break-word !important;
            }

            #todaysAppointmentsModal .card-title strong,
            #todaysAppointmentsModal .card .card-title strong {
                font-weight: 700 !important;
                color: #1a202c !important;
            }

            #todaysAppointmentsModal .card-text,
            #todaysAppointmentsModal .card .card-text {
                font-size: 11px !important;
                line-height: 1.35 !important;
                text-align: left !important;
                color: #718096 !important;
                word-break: break-word !important;
            }

            #todaysAppointmentsModal .card-text strong,
            #todaysAppointmentsModal .card .card-text strong {
                color: #4a5568 !important;
                font-weight: 600 !important;
            }

            #todaysAppointmentsModal .schedule-info,
            #todaysAppointmentsModal .card .d-flex > div:first-child {
                flex: 1 1 auto !important;
                min-width: 0 !important;
                text-align: left !important;
                padding-right: 6px !important;
            }

            #todaysAppointmentsModal .schedule-badge-wrap,
            #todaysAppointmentsModal .card .d-flex > div:last-child {
                flex-shrink: 0 !important;
                margin-left: auto !important;
                display: flex !important;
                align-items: flex-start !important;
            }

            #todaysAppointmentsModal .custom-badge {
                font-size: 10.5px !important;
                padding: 2px 8px !important;
                border-radius: 20px !important;
                line-height: 1.2 !important;
                height: auto !important;
                white-space: nowrap !important;
            }

            #todaysAppointmentsModal .expiring-badge,
            #todaysAppointmentsModal .badge {
                font-size: 10px !important;
                padding: 3px 8px !important;
                border-radius: 20px !important;
                white-space: nowrap !important;
            }

            #todaysAppointmentsModal .modal-footer {
                padding: 8px 12px !important;
            }

            #todaysAppointmentsModal .modal-footer .btn {
                font-size: 12px !important;
                padding: 5px 12px !important;
            }

            /* =========================================================
               Action Buttons on View/Show & Detail Pages (Mobile)
               ========================================================= */
            .content .button,
            .card-body > .button,
            .card-body .button,
            .action-buttons,
            .button.mb-4,
            .button.mt-4.mb-4 {
                margin-top: 20px !important;
                margin-bottom: calc(25px + env(safe-area-inset-bottom)) !important;
                display: flex !important;
                flex-direction: row !important;
                justify-content: center !important;
                align-items: center !important;
                flex-wrap: wrap !important;
                gap: 12px !important;
                width: 100% !important;
                padding: 0 10px !important;
                box-sizing: border-box !important;
            }

            .content .button .btn,
            .card-body > .button .btn,
            .card-body .button .btn,
            .action-buttons .btn,
            .button.mb-4 .btn,
            .button.mt-4.mb-4 .btn {
                margin: 0 !important;
                min-width: 110px !important;
                height: 38px !important;
                padding: 8px 20px !important;
                font-size: 13.5px !important;
                font-weight: 600 !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                gap: 6px !important;
                border-radius: 50px !important;
            }
        }

        /* =========================================================
           Page Title & Header Button Perfect Vertical Centering
           ========================================================= */
        .row:has(> [class*="col-"] > .page-title) {
            display: flex !important;
            align-items: center !important;
        }

       
        .row:has(> [class*="col-"] > .page-title) .page-title {
            margin: 0 !important;
            
            padding-top: 0 !important;
            padding-bottom: 0 !important;
        }

        /* .row:has(> [class*="col-"] > .page-title) .eye-btn,
        .row:has(> [class*="col-"] > .page-title) .patient-button,
        .row:has(> [class*="col-"] > .page-title) [class*="col-"]:last-child {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin: 0 !important;
        } */

        /* =========================================================
           Card Header Perfect Vertical Centering (All Pages)
           ========================================================= */
        .card .card-header,
        .card-header {
            display: flex !important;
            align-items: center !important;
            justify-content: flex-start !important;
            flex-wrap: wrap !important;
            gap: 8px !important;
            min-height: 52px !important;
        }

        .card .card-header .card-title,
        .card-header .card-title {
            margin: 0 !important;
            margin-right: auto !important;
            padding: 0 !important;
            display: inline-flex !important;
            align-items: center !important;
            line-height: 1 !important;
            vertical-align: middle !important;
        }

        .card .card-header .card-title i,
        .card-header .card-title i {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin-right: 8px !important;
            padding: 0 !important;
            line-height: 1 !important;
            vertical-align: middle !important;
        }

        .card .card-header .btn,
        .card-header .btn,
        .card .card-header button,
        .card-header button,
        .card .card-header a.btn,
        .card-header a.btn {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 5px !important;
            margin: 0 !important;
            float: none !important;
            height: 34px !important;
            padding: 6px 14px !important;
            font-size: 13px !important;
            line-height: 1 !important;
            vertical-align: middle !important;
            border-radius: 50px !important;
        }

        .card .card-header .btn i,
        .card-header .btn i,
        .card .card-header button i,
        .card-header button i {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            line-height: 1 !important;
            margin: 0 !important;
            padding: 0 !important;
            font-size: 12px !important;
        }

        /* =========================================================
           Today's Schedule Modal Card Styling (All Devices)
           ========================================================= */
        #todaysAppointmentsModal .modal-content {
            border-radius: 16px !important;
            overflow: hidden !important;
            border: none !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
        }

        #todaysAppointmentsModal .modal-header {
            background-color: #f5b6a5 !important;
            color: #1a1a1a !important;
            padding: 12px 18px !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05) !important;
        }

        #todaysAppointmentsModal .modal-title {
            font-size: 16px !important;
            font-weight: 700 !important;
            margin: 0 !important;
        }

        #todaysAppointmentsModal .modal-body {
            padding: 14px 16px !important;
            max-height: 420px !important;
            overflow-y: auto !important;
        }

        #todaysAppointmentsModal .modal-body h5.font-weight-bold {
            font-size: 14px !important;
            font-weight: 700 !important;
            color: #2c3e50 !important;
            margin-bottom: 10px !important;
        }

        #todaysAppointmentsModal .card,
        #todaysAppointmentsModal .today-schedule-card {
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px !important;
            background: #ffffff !important;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04) !important;
            margin-bottom: 10px !important;
            transition: all 0.2s ease !important;
        }

        #todaysAppointmentsModal .card:hover,
        #todaysAppointmentsModal .today-schedule-card:hover {
            border-color: #cbd5e1 !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
        }

        #todaysAppointmentsModal .card-body {
            padding: 12px 14px !important;
        }

        #todaysAppointmentsModal .schedule-info,
        #todaysAppointmentsModal .card .d-flex > div:first-child {
            flex: 1 1 auto !important;
            min-width: 0 !important;
            text-align: left !important;
            padding-right: 8px !important;
        }

        #todaysAppointmentsModal .card-title,
        #todaysAppointmentsModal .card .card-title {
            font-size: 13.5px !important;
            line-height: 1.45 !important;
            color: #334155 !important;
            text-align: left !important;
            margin-bottom: 5px !important;
            font-weight: 500 !important;
            display: block !important;
            white-space: normal !important;
            word-break: break-word !important;
        }

        #todaysAppointmentsModal .card-title strong,
        #todaysAppointmentsModal .card .card-title strong {
            font-weight: 700 !important;
            color: #0f172a !important;
        }

        #todaysAppointmentsModal .card-text,
        #todaysAppointmentsModal .card .card-text {
            font-size: 12px !important;
            line-height: 1.4 !important;
            color: #64748b !important;
            text-align: left !important;
            word-break: break-word !important;
        }

        #todaysAppointmentsModal .card-text strong,
        #todaysAppointmentsModal .card .card-text strong {
            color: #334155 !important;
            font-weight: 600 !important;
        }

        #todaysAppointmentsModal .schedule-badge-wrap,
        #todaysAppointmentsModal .card .d-flex > div:last-child {
            flex-shrink: 0 !important;
            margin-left: auto !important;
            display: flex !important;
            align-items: flex-start !important;
        }

        #todaysAppointmentsModal .custom-badge {
            font-size: 11px !important;
            font-weight: 600 !important;
            padding: 3px 9px !important;
            border-radius: 20px !important;
            white-space: nowrap !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            line-height: 1.2 !important;
            text-transform: capitalize !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
        }

        #todaysAppointmentsModal .expiring-badge,
        #todaysAppointmentsModal .badge {
            font-size: 11px !important;
            font-weight: 600 !important;
            padding: 4px 10px !important;
            border-radius: 20px !important;
            white-space: nowrap !important;
            background-color: #f5b6a5 !important;
            color: #000 !important;
        }

        /* Dashboard Stat Widgets Equal Height (Desktop) */
        @media only screen and (min-width: 768px) {
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

            /* Action Buttons on View/Show & Detail Pages (Desktop) */
            .content .button,
            .card-body > .button,
            .card-body .button,
            .action-buttons {
                margin-top: 20px !important;
                margin-bottom: 24px !important;
                display: flex !important;
                justify-content: flex-end !important;
                align-items: center !important;
                gap: 12px !important;
            }

            .content .button .btn,
            .card-body > .button .btn,
            .card-body .button .btn,
            .action-buttons .btn {
                min-width: 100px !important;
                height: 38px !important;
                padding: 8px 18px !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                gap: 6px !important;
                border-radius: 50px !important;
            }
        }

        /* Compress margins and paddings for Add/Edit forms only on mobile */
        @media (max-width: 767px) {
            .content > .row {
                margin-top: 0 ;
                padding-top: 0 !important;
            }
            
.mobile-margin-box{
    margin-top:20px !important;
}
            .content .card-body:has(form), 
            .content .form-container {
                padding-top: 10px !important;
                padding-bottom: 10px !important;
                margin-top: 0 !important;
            }
            .content form .form-group {
                margin-top: 2px !important;
                margin-bottom: 8px !important;
            }
            .content form .row {
                margin-top: 0 !important;
                margin-bottom: 0 !important;
            }
            .content .page-title {
                margin-top: 0px !important;
                margin-bottom: 0px !important;
            }
            .page-wrapper > .content {
                padding-top: 10px !important;
            }
        }
    </style>

</head>



<body>

    <div class="main-wrapper">

        @include('layout.header')

        @include('layout.sidebar')



        @yield('content')


        @yield('scripts')


        <script>
    function setBranch(branchId, branchName) {
    localStorage.setItem("selectedBranchId", branchId);
    localStorage.setItem("selectedBranchName", branchName);

    // Redirect to dashboard with branch_id
    window.location.href = `/dashboard?branch_id=${branchId}`;
}



        </script>

        @include('layout.footer')