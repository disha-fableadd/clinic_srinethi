@extends('layout.app')

<style>
    textarea {
        resize: both;
        /* allow manual resize on desktop */
        overflow: auto;
        /* show scroll if needed */
        min-height: 60px;
        /* minimum height */
    }

    .nav-link {
        display: block;
        padding: .5rem .5rem !important;
    }


    .upcoming {
        background-color: #007bff;
        /* Blue */
        color: #fff;
    }

    button.btn.btn-danger.btn-sm.remove-homeadvice {
        margin-bottom: 62px !important;
        margin-right: -22px !important;
        border-radius: 11px !important;
    }

    button.btn.btn-primary.btn-sm.addRow {
        margin-left: -32px !important;
    }

    .col-md-5.assign-template {
        padding-left: 29px;
    }

    .confirmed {
        background-color: #005c6b;
        /* Cyan */
        color: #fff;
    }

    hr {
        margin-top: .5rem !important;
        margin-bottom: .5rem !important;

    }


    ul.dropdown-menu.dropdown-menu-end.show {
        border-radius: 26px;
        box-shadow: 0px 2px 12px rgb(2 2 2 / 35%);
    }

    .completed {
        background-color: #28a745;
        /* Green */
        color: #fff;
    }

    .col-md-5.assign-template {
        padding-left: 29px;
    }

    .col-md-5.d-flex.align-items-end {
        padding-left: 20px;
    }

    button.btn.btn-danger.btn-sm.remove-assessment {
        margin-bottom: 62px !important;
        margin-right: -22px !important;
        border-radius: 11px !important;
    }

    button.btn.btn-primary.btn-sm.addRow {
        margin-left: -32px !important;
    }

    button.btn-close.custom-close {
        background-color: #f89884;
        border: none;
        padding: 3px 6px;
        border-radius: 6px;
    }

    .cancelled {
        background-color: #dc3545;
        /* Red */
        color: #fff;
    }

    .follow-up {
        background-color: #ffc107;
        /* Yellow */
        color: #000;
    }

    #symptom_section .symptom-remark {
        display: flex;
        align-items: center;
    }



    .upcoming:hover,
    .confirmed:hover,
    .completed:hover,
    .cancelled:hover {
        color: #ffffff;
    }



    .modal-header {
        display: -ms-flexbox;
        display: flex;
        -ms-flex-align: start;
        align-items: flex-start;
        -ms-flex-pack: justify;
        justify-content: space-between;
        padding: 1rem 1rem;
        border-bottom: 1px solid #e9ecef;
        border-top-left-radius: .3rem;
        border-top-right-radius: .3rem;
        background: #cfece0;
        color: #161515;
    }

    .note-item {
        background-color: #f8f9fa;
        border-left: 4px solid #17a2b8;
        padding: 6px 18px;
        margin-bottom: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    .speech-note {
        border-left-color: #28a745;
    }

    .audio-note {
        border-left-color: #28a745;
    }

    .note-item p {
        margin: 5px 0;
        color: #333;
        font-size: 14px;
        line-height: 1.5;
    }



    audio {
        margin-top: 5px;
        width: 100%;
    }

    /* Keep your existing CSS for note-item, icons, etc. */
    .margin-text mt-1 mb-1 {
        margin-bottom: 5px;
    }

    .icon-style1 {
        background-color: white;
        font-size: 17px !important;
        color: rgb(157 195 179);
        padding: 10px;
        border-radius: 50%;
    }

    #symptom_images_container img {
        width: 40px;
        height: 40px;
        border-radius: 5px;
    }

    /* Buttons alignment */
    .d-flex.justify-content-end {
        gap: 10px;
    }

    .card-body {
        flex: 1 1 auto;
        padding: 0 20px;
        height: auto;
    }

    .card-body-upper {
        flex: 1 1 auto;
        padding: 0 20px;
        height: auto;
    }

    .col-12.d-flex.justify-content-end {
        margin-bottom: 12px;
    }

    .select2-container--default .select2-selection--multiple {
        box-shadow: none;
        font-size: 14px;
        min-height: 40px !important;
        border-radius: 50px !important;
        padding: 0.469rem 0.75rem;
        border-color: rgb(207, 236, 224) !important;
    }

    .select2-container--default .select2-selection--multiple {
        padding: 4px 8px;
        text-align: left;
        min-height: 38px;
    }

    .select2-selection__rendered {
        text-align: left !important;
    }


    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        background-color: rgb(207, 236, 224) !important;
        color: black !important;
        border-radius: 20px !important;
        border: none;
        font-size: 14px;
        font-weight: normal !important;
        text-align: center;
        max-width: fit-content;
    }

    .select2-container--default.select2-container--focus .select2-selection--multiple,
    .select2-container--default .select2-selection--multiple {
        min-height: 50px;
        /* Minimum height */
        max-height: 150px;
        /* Maximum height */
        overflow-y: auto;
        /* Scroll when too many items */
        padding: 5px 10px;
        border-radius: 10px;
        display: flex;
        flex-wrap: wrap;
        /* Important: allow items to go to next line */
        align-items: center;
    }

    .select2-selection__choice {
        margin: 2px 5px;
        padding: 3px 8px;
        font-size: 14px;
        border-radius: 12px;
        display: flex;
        align-items: center;
    }

    .card-footer {
        background-color: #87ceb0 !important;
    }


    @media screen and (max-width:767px) {
        .page-title {
            font-size: 20px !important;
            margin-bottom: 0 !important;
            margin-top: 8px;
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

        button#viewReferralBtn {
            font-size: 12px;
        }


    }

    @media (min-width: 768px) and (max-width: 1024px) {
        button#viewReferralBtn {
            font-size: 11px;
        }

    }

    h5 {
        font-size: 1rem !important;
    }

    @media only screen and (max-width: 767.98px) {
        h5 {
            font-size: 1rem !important;
        }
    }
</style>

@php
    use App\Models\Setting;
    $razorpayEnabled = Setting::getValue('razorpay_status') === 'on';
@endphp
@section('content')
    <div class="page-wrapper">
        <div class="content">



            <div class="row mt-3">
                <div class="col-md-12  col-sm-12 col-lg-12">
                    <div class="card">
                        <div class="card-footer text-right">
                            <h3 style="float:left" class="text-dark mb-0">
                                <i class="fa fa-info-circle icon-style2 text-white"></i>
                                <span class="Patient_name"></span> ' s Details
                            </h3>


                            <div class="d-flex text-right" style="justify-content: end;">

                                <div class="mr-2 m-b-2">
                                    <a href="javascript:void(0)"
                                        class="btn btn-primary btn-rounded btn-hdr download-patient-history"
                                        data-id="{{ $patient_id }}">
                                        <i class="fa fa-file-pdf"></i> <span class="hdr-btn-text">PDF</span>
                                    </a>
                                </div>


                                @if (app('hasPermission')(5, 'view'))
                                    <div class="text-right m-b-2">
                                        <a href="{{ route('patients.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                                            <i class="fa fa-arrow-left"></i> <span class="hdr-btn-text">Back</span>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="card-body-upper mt-3">
                            <div class="row">
                                <!-- Patient Image and Symptoms Column -->
                                <div class="col-lg-3 text-center">
                                    <p class="text-dark mb-0">
                                        <img id="Patient_image" class="userProfile mb-3" src="" width="200"
                                            height="200" alt="Patient Image" style="border-radius:20px">
                                    </p>
                                    <!-- Note Section Below Symptoms -->
                                    <div class="col-md-12 col-lg-12 col-sm-12">
                                        <div id="output">
                                        </div>
                                    </div>

                                </div>

                                <!-- Patient Info in Three Columns -->
                                <div class="col-lg-9">
                                    <div class="row">
                                        <!-- Column 1 -->
                                        <div class="col-md-4">
                                            <p class="text-dark mb-0 ">
                                                <strong><i class="fa fa-id-badge icon-style1"></i> Patient Name: </strong>
                                                <span class="Patient_name"></span>
                                            </p>
                                            <hr class="margin-text mt-1 mb-1">
                                            @php
                                                $currentProjectTypeId = (int) \App\Models\Setting::getValue('project_type_id', 1);
                                            @endphp
                                            @if($currentProjectTypeId !== 3)
                                            <p class="text-dark mb-0 ">
                                                <strong><i class="fas fa-envelope icon-style1"></i> Email: </strong>
                                                <span id="email"></span>
                                            </p>
                                            <hr class="margin-text mt-1 mb-1">
                                            @endif
                                            <p class="text-dark mb-0 ">
                                                <strong><i class="fas fa-medkit icon-style1"></i> Diagnosis: </strong>
                                                <span id="diagnosis"></span>
                                            </p>
                                            <hr class="margin-text mt-1 mb-1">

                                            @if (Auth::user()->role_id == '2' || Auth::user()->role_id == '1')
                                                <p class="text-dark mb-0">
                                                    <strong><i class="fas fa-map-marker-alt icon-style1"></i> Address:
                                                    </strong>
                                                    <span id="address"></span>
                                                </p>

                                                <hr class="margin-text mt-1 mb-1">
                                            @endif
                                            <p class="text-dark mb-0 ">
                                                <strong><i class="fas fa-medkit icon-style1"></i> Symptoms: </strong>
                                                <span id="symptoms"></span>

                                                {{--
                                                <hr class="margin-text mt-1 mb-1"> --}}
                                        </div>
                                        <!-- Column 2 -->
                                        <div class="col-md-4">
                                            <p class="text-dark mb-0">
                                                <strong><i class="fa fa-id-badge icon-style1"></i> Patient type: </strong>
                                                <span id="type"></span>
                                            </p>
                                            @if (Auth::user()->role_id == '2' || Auth::user()->role_id == '1')
                                                <hr class="margin-text mt-1 mb-1">
                                                <p class="text-dark mb-0">
                                                    <strong><i class="fas fa-phone icon-style1"></i> Phone: </strong>
                                                    <span id="phone"></span>
                                                </p>
                                            @endif
                                            <hr class="margin-text mt-1 mb-1">
                                            <p class="text-dark mb-0">
                                                <strong><i class="fas fa-city icon-style1"></i> City: </strong>
                                                <span id="city"></span>
                                            </p>
                                            <hr class="margin-text mt-1 mb-1">

                                            <p class="text-dark mb-0">
                                                <strong><i class="fa-solid fa-image icon-style1"></i>Symptom
                                                    Remark/Image:</strong>
                                            <div id="symptom_section"
                                                style="margin-top:5px; margin-right:10px; display:flex; flex-wrap:wrap; gap:10px;">
                                            </div>
                                            </p>

                                        </div>
                                        <!-- Column 3 -->
                                        <div class="col-md-4">
                                            <p class="text-dark mb-0">
                                                <strong><i class="fas fa-birthday-cake icon-style1"></i> Birthdate:
                                                </strong>
                                                <span id="birthdate"></span>
                                            </p>
                                            <hr class="margin-text mt-1 mb-1">
                                            <p class="text-dark mb-0">
                                                <strong><i class="fas fa-birthday-cake icon-style1"></i> Age: </strong>
                                                <span id="age"></span>
                                            </p>
                                            <hr class="margin-text mt-1 mb-1">
                                            <p class="text-dark mb-0">
                                                <strong><i class="fas fa-flag icon-style1"></i> State: </strong>
                                                <span id="state"></span>
                                            </p>
                                            <hr class="margin-text mt-1 mb-1">
                                            <p class="text-dark mb-0">
                                                <strong><i class="fas fa-share-alt icon-style1"></i> Referral Source:
                                                </strong>
                                                <span id="referral_source"></span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="col-12 d-flex justify-content-end">
                                <button type="button" id="viewReferralBtn" class="btn btn-primary btn-rounded btn-hdr me-2">
                                    <i class="fas fa-eye"></i> View Referral Source
                                </button>

                                @if (app('hasPermission')(5, 'update'))
                                    <a href="#" class="btn btn-primary btn-rounded btn-hdr me-2 edit-patient-btn"
                                        data-id="{{ $patient_id }}" style="color:black;">
                                        <i class="fa fa-pencil-alt"></i> <span class="hdr-btn-text">Edit</span>
                                    </a>
                                @endif

                                @if (app('hasPermission')(5, 'delete'))
                                    <button type="button" class="btn btn-danger btn-rounded btn-hdr delete-patient"
                                        data-id="{{ $patient_id }}">
                                        <i class="fa fa-trash"></i> <span class="hdr-btn-text">Delete</span>
                                    </button>
                                @endif
                            </div>

                        </div>

                    </div>
                </div>
            </div>

            <div class="modal fade" id="viewReferralModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #CFECE0; color:black">
                            <h5 class="modal-title">Referral Details</h5>
                        </div>
                        <div class="modal-body" id="viewReferralBody">
                            <!-- Referral details will load here -->
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal"
                                style="border-radius:50px !important">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- model --}}


            <!-- Assessment Modal -->
            <div class="modal fade" id="assessmentModal" tabindex="-1" aria-labelledby="assessmentModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="assessmentModalLabel">Assessment</h5>
                            <button type="button" class="btn-close custom-close" data-bs-dismiss="modal"
                                aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modal-body">


                            <form id="assessmentForm">
                                <input type="hidden" id="assessment_patient_id" name="patient_id">
                                <!-- patient fullname field -->
                                <div class="mb-3">
                                    <label for="assessment_patient_name" class="form-label">Patient Name</label>
                                    <input type="text" class="form-control" id="assessment_patient_name"
                                        name="patient_name" readonly>
                                </div>


                                <div class="row">
                                    <!-- Assign Template -->
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="mr-2">
                                                <i class="fas fa-medkit icon-style"></i> Assign Template
                                                {{-- <span class="text-danger">*</span> --}}
                                            </label>
                                            <select class="form-control select2" name="assessment_id"
                                                id="assessmentDropdown" required>
                                                <option value="" class="text-dark">Select Assessment</option>
                                            </select>
                                            {{-- <div class="invalid-feedback">
                                                Please select an Assessment template.
                                            </div> --}}
                                        </div>
                                    </div>


                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <i class="fas fa-align-left icon-style"></i>
                                            <label>Description</label>
                                            <textarea class="form-control assessment-description mt-1" name="description" placeholder="Enter description"
                                                rows="5" style="border-radius: 10px"></textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-12 col-12 mb-2 text-center ">
                                        <button type="button" class="btn btn-primary btn-sm addRow">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>


                                </div>
                                <div id="assessmentContainer"></div>


                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                                <div id="assessmentSuccessMessage" class="alert alert-success" style="display:none;">
                                </div>
                                <div id="assessmentErrorMessage" class="alert alert-danger" style="display:none;"></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Diet-chart Modal -->
            <div class="modal fade" id="dietChartModal" tabindex="-1" aria-labelledby="dietChartModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title" id="dietChartModalLabel">
                                <i class="fa-solid fa-bowl-food me-2"></i> Diet Chart
                            </h5>
                            <button type="button" class="btn-close custom-close" data-bs-dismiss="modal"
                                aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <div class="modal-body">
                            <form id="dietChartForm" enctype="multipart/form-data">
                                <input type="hidden" id="dietchart_patient_id" name="patient_id">

                                <!-- Patient Name -->
                                <div class="mb-3">
                                    <label for="dietchart_patient_name" class="form-label">Patient Name</label>
                                    <input type="text" class="form-control" id="dietchart_patient_name"
                                        name="patient_name" readonly>
                                </div>

                                <div class="row">
                                    <!-- Assign Template -->
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="mr-2">
                                                <i class="fa-solid fa-list-alt me-1"></i> Assign Diet Template
                                            </label>
                                            <select class="form-control select2" name="diet_template_id"
                                                id="dietChartDropdown">
                                                <option value="">Select Diet Template</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <i class="fas fa-align-left icon-style"></i>
                                            <label>Description</label>
                                            <textarea class="form-control diet-description mt-1" name="description" placeholder="Enter description"
                                                rows="5" style="border-radius: 10px"></textarea>
                                        </div>
                                    </div>

                                    <!-- Add Row Button -->
                                    <div class="col-md-12 col-12 mb-2 text-center">
                                        <button type="button" class="btn btn-primary btn-sm addRow">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Dynamic Rows -->
                                <div id="dietChartContainer"></div>

                                <!-- Modal Footer -->
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>

                                <!-- Success & Error Messages -->
                                <div id="dietChartSuccessMessage" class="alert alert-success" style="display:none;">
                                </div>
                                <div id="dietChartErrorMessage" class="alert alert-danger" style="display:none;"></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Home Advice Modal -->
            <div class="modal fade" id="homeadviceModal" tabindex="-1" aria-labelledby="homeadviceModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="homeadviceModalLabel">Homeadvice</h5>
                            <button type="button" class="btn-close custom-close" data-bs-dismiss="modal"
                                aria-label="Close"><i class="fas fa-times"></i></button>
                        </div>
                        <div class="modal-body">

                            <form id="homeadviceForm">
                                <input type="hidden" id="homeadvice_patient_id" name="patient_id">

                                <div class="mb-3">
                                    <label for="patient_name" class="form-label">Patient Name</label>
                                    <input type="text" class="form-control Patient_name" id="patient_name"
                                        name="patient_name" readonly>
                                </div>





                                <div class="row">
                                    <!-- Assign Template -->
                                    <div class="col-md-12 assignhome">
                                        <div class="form-group">
                                            <label class="mr-2"><i class="fas fa-medkit icon-style"></i> Assign Template
                                            </label>
                                            <select class="form-control select2" name="homeadvice_id"
                                                id="homeadviceDropdown" required>
                                                <option value="" class="text-dark">Select Homeadvice</option>
                                            </select>
                                            <div class="invalid-feedback">
                                                Please select a Home Advice template.
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <div class="col-md-12 assignhome">
                                        <div class="form-group">
                                            <label>
                                                <i class="fas fa-align-left icon-style"></i> Description
                                            </label>
                                            <textarea id="mainHomeadviceDescription" class="form-control homeadvice-description" name="description[]"
                                                placeholder="Enter description" rows="5" style="border-radius: 10px"></textarea>
                                        </div>

                                    </div>
                                    <div class="col-md-12 col-12 mb-2  text-center ">
                                        <button type="button" class="btn btn-primary btn-sm addRowhome">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>


                                </div>
                                <div id="homeAdviceContainer"></div>




                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                                <!-- ✅ Success/Error messages go here -->
                                <div id="homeadviceSuccessMessage" class="alert alert-success" style="display:none;">
                                </div>
                                <div id="homeAdviceErrorMessage" class="alert alert-danger" style="display:none;"></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>




            {{-- appointment --}}
            <div class="modal fade" id="appointmentsModal" tabindex="-1" aria-labelledby="appointmentsModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="appointmentsModalLabel">Appointments</h5>
                            <button type="button" class="btn-close custom-close" data-bs-dismiss="modal"
                                aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="appointmentsForm">
                                <input type="hidden" id="appointment_patient_id" name="patient_id">
                                <input type="hidden" id="branch_id" name="branch_id">

                                <!-- Patient fullname field -->
                                <div class="mb-3">
                                    <label for="appointment_patient_name" class="form-label">Patient Name</label>
                                    <input type="text" class="form-control" id="appointment_patient_name"
                                        name="patient_name" readonly>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">

                                            <div class="d-flex align-items-center justify-content-between">
                                                <label class="mr-2"><i class="fas fa-medkit icon-style"></i> Treatment
                                                    <span class="text-danger">*</span></label>
                                            </div>
                                            <select class="form-control select2" name="treatment_id"
                                                id="treatmentDropdown">
                                            </select>

                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <label class="mr-2"><i class="fas fa-medkit icon-style"></i> Doctor
                                                    <span class="text-danger">*</span></label>
                                            </div>
                                            <select class="form-control select2 doctorSelect" name="doctor_id"
                                                id="doctorSelect">
                                            </select>

                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name="appoint_type" value="in-person">
                                <input type="hidden" name="status" value="confirmed">


                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label><i class="fas fa-clock icon-style"></i> Date <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" class="form-control timepicker" name="date"
                                                id="dateField" required>
                                        </div>
                                        <input type="hidden" name="appoint_type" value="in-person">
                                        <input type="hidden" name="status" value="confirmed">
                                    </div>

                                    <script>
                                        const dateField = document.getElementById('dateField');
                                        if (dateField) {
                                            const today = new Date().toISOString().split('T')[0];
                                            dateField.setAttribute('min', today);
                                        }
                                    </script>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label><i class="fas fa-hourglass-half icon-style"></i> Time <span
                                                    class="text-danger">*</span></label>
                                            <input type="time" class="form-control" name="duration" required>
                                        </div>

                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>

                                <div id="appointmentSuccessMessage" class="alert alert-success" style="display:none;">
                                </div>
                                <div id="appointmentErrorMessage" class="alert alert-danger" style="display:none;"></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>


            {{-- Report --}}

            <div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel"
                aria-hidden="true">
                <div class="modal-dialog "> <!-- large for form -->
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="reportModalLabel">
                                <i class="fas fa-file-medical me-2"></i> Add Medical Report
                            </h5>
                            <button type="button" class="btn-close custom-close" data-bs-dismiss="modal"
                                aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <div class="modal-body">
                            <form id="medicalReportFormModal" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="branch_id" id="branch_id_modal">

                                <!-- Patient Name -->
                                <div class="mb-3">
                                    <label for="patientDropdownModal" class="form-label">
                                        <i class="fas fa-user icon-style"></i> Patient Name <span
                                            class="text-danger">*</span>
                                    </label>
                                    <select class="form-control select2" name="patient_id" id="patientDropdownModal"
                                        readonly>
                                        <option value="">Select Patient</option>
                                    </select>
                                </div>



                                <!-- Report Type + Date -->
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="report_type_modal" class="form-label">
                                            <i class="fas fa-file-medical icon-style"></i> Report Type
                                        </label>
                                        <input type="text" class="form-control" name="report_type"
                                            id="report_type_modal" placeholder="Eg: Scan, X-Ray, Blood">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="date_modal" class="form-label">
                                            <i class="fas fa-calendar-alt icon-style"></i> Date
                                        </label>
                                        <input type="date" class="form-control" name="date" id="date_modal"
                                            value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}"
                                            max="{{ date('Y-m-d') }}">
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="mb-3">
                                    <label for="description_modal" class="form-label">
                                        <i class="fas fa-align-left icon-style"></i> Description <span
                                            class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control" rows="3" name="description" id="description_modal" style="border-radius:10px"
                                        required></textarea>
                                </div>

                                <!-- File Upload -->
                                <div class="mb-3">
                                    <label for="file_path_modal" class="form-label">
                                        <i class="fas fa-file-upload icon-style"></i> Upload Report <span
                                            class="text-danger">*</span>
                                    </label>
                                    <input type="file" class="form-control" name="file_path" id="file_path_modal"
                                        required>
                                </div>

                                <!-- Alerts -->
                                <div id="medicalsuccessMessageModal" class="alert alert-success" style="display:none;">
                                </div>
                                <div id="medicalerrorMessageModal" class="alert alert-danger" style="display:none;">
                                </div>
                            </form>
                        </div>

                        <div class="modal-footer">
                            {{-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            --}}
                            <button type="submit" form="medicalReportFormModal" class="btn btn-primary">Save</button>
                        </div>
                    </div>
                </div>
            </div>




            {{-- followup --}}
            <div class="modal fade" id="followupModal" tabindex="-1" aria-labelledby="followupModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="followupModalLabel">Followup</h5>
                            <button type="button" class="btn-close custom-close" data-bs-dismiss="modal"
                                aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="followupForm">
                                <input type="hidden" id="followup_patient_id" name="patient_id">
                                <input type="hidden" id="branch_id" name="branch_id">


                                <!-- Patient fullname field -->
                                <div class="mb-3">
                                    <label for="followup_patient_name" class="form-label">Patient Name</label>
                                    <input type="text" class="form-control" id="followup_patient_name"
                                        name="patient_name" readonly>
                                </div>


                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <label class="mr-2"><i class="fas fa-medkit icon-style"></i> Doctor
                                                    <span class="text-danger">*</span></label>
                                            </div>
                                            <select class="form-control select2 doctorSelect" name="doctor_id"
                                                id="doctorSelect1">
                                                {{-- <option value="">Select Doctor</option> --}}
                                            </select>

                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label><i class="fas fa-clock icon-style"></i> Date <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="date" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>

                                <div id="followupSuccessMessage" class="alert alert-success" style="display:none;">
                                </div>
                                <div id="followupErrorMessage" class="alert alert-danger" style="display:none;"></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Treatment Booking Modal -->
            <div class="modal fade" id="treatmentBookingModal" tabindex="-1"
                aria-labelledby="treatmentBookingModalLabel" aria-hidden="true">
                <div class="modal-dialog "> <!-- larger for more fields -->
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="treatmentBookingModalLabel">Treatment Booking</h5>
                            <button type="button" class="btn-close custom-close" data-bs-dismiss="modal"
                                aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="treatmentBookingForm" method="POST">
                                @csrf
                                <input type="hidden" name="branch_id" id="branch_id">
                                <div class="row">
                                    {{-- Patient --}}
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label><i class="fas fa-user icon-style"></i> Patient <span
                                                    class="text-danger">*</span></label>
                                            @if (app('hasPermission')(5, 'create'))
                                                <a href="{{ route('patients.create') }}" target="_blank"
                                                    class="btn btn-primary btn-sm float-right">
                                                    <i class="fas fa-plus"></i>
                                                </a>
                                            @endif
                                            <select class="form-control select2" name="patient_id" id="patientDropdown"
                                                required>
                                                <option value="">Select Patient</option>
                                            </select>
                                        </div>
                                    </div>


                                    {{-- Treatment --}}
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label><i class="fas fa-medkit icon-style"></i> Treatment <span
                                                    class="text-danger">*</span></label>
                                            @if (app('hasPermission')(7, 'create'))
                                                <a href="{{ route('treatment.create') }}" target="_blank"
                                                    class="btn btn-primary btn-sm float-right">
                                                    <i class="fas fa-plus"></i>
                                                </a>
                                            @endif
                                            <select class="form-control select2" name="treatment_id"
                                                id="treatmentDropdownforbooking" required>
                                                <option value="">Select Treatment</option>
                                            </select>

                                        </div>

                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label><i class="fa fa-cogs icon-style"></i> Machine <span
                                                    class="text-danger">*</span></label>
                                            @if (app('hasPermission')(7, 'create'))
                                                <button type="button" class="btn btn-primary btn-sm float-right"
                                                    data-bs-toggle="modal" data-bs-target="#addMachineModal">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            @endif
                                            <select id="machineDropdown" name="machine_id[]" class="form-control select2"
                                                multiple style="min-height: 100px; max-height: 200px; overflow-y: auto;">
                                                <option value="">Select Machines</option>
                                            </select>


                                        </div>
                                        <!-- <div id="treatmentMachines" class="mt-1 mb-1" style="font-size:16px"></div> -->
                                    </div>
                                </div>

                                {{-- Date & Plan --}}
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label><i class="fas fa-calendar-day icon-style"></i> Date <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" name="payment_date" class="form-control"
                                                id="todayOnlyDate" required>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label><i class="fas fa-calendar-day icon-style"></i> Plan <span
                                                    class="text-danger">*</span></label>
                                            <select name="plan" class="form-control select2" id="plan" required>
                                                <option value="">Select Plan</option>
                                                <option value="daily">Daily</option>
                                                <option value="weekly">Weekly</option>
                                                <option value="monthly">Monthly</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                {{-- Payment & Mode --}}
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>₹ Payment <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" step="0.01" name="amount" id="amount"
                                                class="form-control" placeholder="Enter total payment" required>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label><i class="fas fa-money-bill icon-style"></i>Mode <span
                                                    class="text-danger">*</span></label>
                                            <select name="payment_mode" class="form-control select2" id="payment_mode"
                                                required>
                                                <option value="">Select Payment Mode</option>
                                                <option value="cash">Cash</option>
                                                <option value="online">Online</option>
                                                <option value="cash+online">Cash+Online</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                {{-- Paid Type / Split Amount --}}
                                <div class="row">
                                    <div class="col-md-4 col-sm-6">
                                        <div class="form-group d-none" id="paid_amount_type">
                                            <label><i class="fas fa-hand-holding-usd icon-style"></i> Paid Type <span
                                                    class="text-danger">*</span></label>
                                            <select name="paid_type" class="form-control select2" id="paid_type">
                                                <option value="">Select Paid Type</option>
                                                <option value="fully">Fully</option>
                                                <option value="partial">Partial</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group d-none col-md-4 col-sm-6" id="cash_amount_div">
                                        <label>₹ Cash <span
                                                class="text-danger">*</span></label>
                                        <input type="number" step="0.01" name="cash" id="cash_amount"
                                            class="form-control" placeholder="Enter cash amount">
                                        <small id="cashError" class="text-danger d-none"></small>
                                    </div>

                                    <div class="form-group d-none col-md-4 col-sm-6" id="online_amount_div">
                                        <label><i class="fas fa-credit-card icon-style"></i> Online <span
                                                class="text-danger">*</span></label>
                                        <input type="number" step="0.01" name="online" id="online_amount"
                                            class="form-control" placeholder="Enter online amount">
                                        <small id="onlineError" class="text-danger d-none"></small>
                                    </div>

                                    <div class="col-md-4 col-sm-6">
                                        <div class="form-group d-none" id="paid_amount_div">
                                            <label><i class="fas fa-coins icon-style"></i> Amount Paid </label>
                                            <input type="number" step="0.01" name="paid_amount" id="paid_amount"
                                                class="form-control" placeholder="Enter paid amount">
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6 mx-auto">
                                        <div class="form-group d-none" id="remain_amount_div">
                                            <label><i class="fas fa-hourglass-half icon-style"></i> <span class="hdr-btn-text">Pending</span></label>
                                            <input type="number" step="0.01" name="remain_amount" id="remain_amount"
                                                class="form-control" readonly>
                                        </div>
                                    </div>
                                </div>

                                <div id="bookingSuccess" class="alert alert-success" style="display:none;"></div>
                                <div id="bookingError" class="alert alert-danger" style="display:none;"></div>

                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>






            <!-- tabs -->

            <div class="row">
                <div class="col-md-12">
                    <div class="card">


                        <div class="card-footer d-flex justify-content-between align-items-center"
                            style="background-color:#87ceb0">
                            <h3 class="text-dark mb-0">
                                <i class="fa fa-info-circle icon-style2 text-white"></i>
                                <span class="Patient_name"></span>'s Details
                            </h3>

                            <!-- 3-dot dropdown -->
                            <div class="dropdown ">
                                <button class="btn btn-link text-dark p-0" type="button" id="dropdownMenuButton"
                                    data-bs-toggle="dropdown" aria-expanded="false" style="font-size:1.5rem !important">
                                    <i class="fa fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                                    <li>
                                        <a class="dropdown-item followup-link" href="javascript:void(0);"
                                            data-patient-id="{{ $patient_id }}">
                                            <i class="fas fa-calendar-plus me-2"></i>
                                            <span class="font-bold">Add Followup</span>
                                        </a>
                                    </li>
                                    <hr>
                                    <li>
                                        <a class="dropdown-item appointments-link" href="javascript:void(0);"
                                            data-patient-id="{{ $patient_id }}">
                                            <i class="fas fa-calendar-check me-2"></i>
                                            <span class="font-bold">Add Appointments</span>
                                        </a>
                                    </li>
                                    <hr>
                                    <li>
                                        <a class="dropdown-item treatment-link" href="javascript:void(0);"
                                            data-patient-id="{{ $patient_id }}"
                                            data-patient-name="{{ $patient->fullname ?? '' }}">
                                            <i class="fas fa-pills me-2"></i>
                                            <span class="font-bold">Add Treatment</span>
                                        </a>
                                    </li>

                                    <hr>
                                    <li>
                                        <a class="dropdown-item open-diagnosis-modal" href="javascript:void(0);"
                                            data-patient-id="{{ $patient_id }}">
                                            <i class="fas fa-diagnoses me-2"></i>
                                            <span class="font-bold">Update Diagnosis</span>
                                        </a>
                                    </li>
                                    <hr>
                                    <li>
                                        <a class="dropdown-item open-symptoms-modal" href="javascript:void(0);"
                                            data-patient-id="{{ $patient_id }}">
                                            <i class="fas fa-notes-medical me-2"></i>
                                            <span class="font-bold">Update Symptoms</span>
                                        </a>
                                    </li>
                                    {{-- Hidden Generate Payment Link per requirement --}}
                                    {{--
                                    @if ($razorpayEnabled)
                                        <hr>
                                        <li>
                                            <a class="dropdown-item generate-payment-link" href="javascript:void(0);"
                                                data-patient-id="{{ $patient_id }}"
                                                data-patient-name="{{ $patient->fullname ?? '' }}">
                                                <i class="fas fa-link me-2"></i>
                                                <span class="font-bold">Generate Payment Link</span>
                                            </a>
                                        </li>
                                    @endif
                                    --}}

                                </ul>
                            </div>
                        </div>




                        <div class="card-body mt-3">
                            <ul class="nav nav-tabs" id="patientTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="followup-tab" data-toggle="tab" href="#followup"
                                        role="tab">Followup History</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="appointments-tab" data-toggle="tab" href="#appointments"
                                        role="tab">Appointments History</a>
                                </li>
                                {{-- Hidden Payment History tab per requirement --}}
                                {{--
                                <li class="nav-item">
                                    <a class="nav-link" id="payments-tab" data-toggle="tab" href="#payments"
                                        role="tab">Payment History</a>
                                </li>
                                --}}
                                <li class="nav-item">
                                    <a class="nav-link" id="treatment-tab" data-toggle="tab" href="#treatment-record"
                                        role="tab">Treatment History</a>
                                </li>


                            </ul>

                            <div class="tab-content mt-3" id="patientTabsContent">
                                <!-- Follow-up Records Tab -->
                                <div class="tab-pane fade show active" id="followup" role="tabpanel">
                                    <div class="table-responsive-wrapper">
                                        <table class="table table-bordered" id="followupTable">
                                            <thead>
                                                <tr>
                                                    <th>Doctor </th>
                                                    <th>Date</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="followupRecords">
                                                <!-- Follow-up records will be loaded here -->
                                            </tbody>
                                        </table>
                                    </div>
                                    <div id="no-followup-message" class="alert alert-info text-center"
                                        style="display: none;">
                                        No followup history available.
                                    </div>
                                </div>

                                <!-- Appointments Tab -->
                                <div class="tab-pane fade" id="appointments" role="tabpanel">
                                    <div class="table-responsive-wrapper">
                                        <table class="table table-bordered" id="appointmentTable">
                                            <thead>
                                                <tr>
                                                    <th>Doctor </th>
                                                    <th>Treatment</th>
                                                    <th>Date</th>
                                                    <th>Appointment Type</th>
                                                    <th>Status</th>
                                                    <th>Action</th>


                                                </tr>
                                            </thead>
                                            <tbody id="appointmentRecords">

                                            </tbody>
                                        </table>
                                    </div>
                                    <div id="no-appointment-message" class="alert alert-info text-center"
                                        style="display: none;">
                                        No appointment history available.
                                    </div>
                                </div>

                                {{--
                                <!-- Payment History Tab -->
                                <div class="tab-pane fade" id="payments" role="tabpanel">
                                    <div class="table-responsive-wrapper">
                                        <table class="table table-bordered" id="paymentTable">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Amount</th>
                                                    <th>Paid Amount</th>
                                                    <th>Remaining</th>
                                                    <th>Collected By</th>
                                                    <th>Action</th>

                                                </tr>
                                            </thead>
                                            <tbody id="paymentRecords">
                                                <!-- Payment history will load here -->
                                            </tbody>
                                        </table>
                                    </div>
                                    <div id="no-payment-message" class="alert alert-info text-center"
                                        style="display: none;">
                                        No payment history available.
                                    </div>
                                </div>
                                --}}

                                <!-- Report History Tab -->
                                <div class="tab-pane fade" id="reports" role="tabpanel">
                                    <div class="table-responsive-wrapper">
                                        <table class="table table-bordered" id="reportTable">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Report Type</th>
                                                    <th>Description</th>
                                                    <th>File</th>
                                                    <th>Action</th>

                                                </tr>
                                            </thead>
                                            <tbody id="reportRecords">
                                                <!-- Report records will be loaded here -->
                                            </tbody>
                                        </table>
                                    </div>
                                    <div id="no-report-message" class="alert alert-info text-center"
                                        style="display: none;">
                                        No report history available.
                                    </div>
                                </div>

                                <!-- Treatment History Tab -->
                                <div class="tab-pane fade" id="treatment-record" role="tabpanel">
                                    <div class="table-responsive-wrapper">
                                        <table class="table table-bordered" id="treatmentTable">
                                            <thead>
                                                <tr>
                                                    <th>Doctor</th>
                                                    <th>Treatment</th>

                                                    <th>Machine</th>
                                                    <th>Plan</th>
                                                    <th>Payment Date</th>
                                                    <th>Status</th>
                                                    <th>Action</th>

                                                </tr>
                                            </thead>
                                            <tbody id="treatmentRecords">
                                                <!-- Treatment records will load here -->
                                            </tbody>
                                        </table>
                                    </div>
                                    <div id="no-treatment-message" class="alert alert-info text-center"
                                        style="display: none;">
                                        No treatment history available.
                                    </div>
                                </div>

                                {{-- assessment --}}
                                <div class="tab-pane fade" id="assessment-record" role="tabpanel">
                                    <div class="table-responsive-wrapper">
                                        <table class="table table-bordered" id="assessmentTable">
                                            <thead>
                                                <tr>

                                                    <th>Template Name</th>
                                                    <th>Description</th>
                                                    <th>Action</th>



                                                </tr>
                                            </thead>
                                            <tbody id="assessmentRecords">
                                                <!-- Assessment records will load here -->
                                            </tbody>
                                        </table>
                                    </div>
                                    <div id="no-assessment-message" class="alert alert-info text-center"
                                        style="display: none;">
                                        No Assessment history available.
                                    </div>
                                </div>
                                {{-- diet-chart --}}
                                <div class="tab-pane fade" id="diet-chart-record" role="tabpanel">
                                    <div class="table-responsive-wrapper">
                                        <table class="table table-bordered" id="dietTable">
                                            <thead>
                                                <tr>
                                                    <th>Template Name</th>
                                                    <th>Description</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="dietRecords">
                                                <!-- Diet Chart records will load here -->
                                            </tbody>
                                        </table>
                                    </div>
                                    <div id="no-dietChart-message" class="alert alert-info text-center"
                                        style="display: none;">
                                        No Diet Chart history available.
                                    </div>
                                </div>

                                {{-- homeadvice --}}
                                <div class="tab-pane fade" id="homeadvice-record" role="tabpanel">
                                    <div class="table-responsive-wrapper">
                                        <table class="table table-bordered" id="homeadviceTable">
                                            <thead>
                                                <tr>

                                                    <th>Template Name</th>
                                                    <th>Description</th>
                                                    <th>Action</th>

                                                </tr>
                                            </thead>
                                            <tbody id="homeadviceRecords">
                                                <!-- homeadvice records will load here -->
                                            </tbody>
                                        </table>
                                    </div>
                                    <div id="no-homeadvice-message" class="alert alert-info text-center"
                                        style="display: none;">
                                        No Homeadvice history available.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="addMachineModal" tabindex="-1" aria-labelledby="addMachineModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <form id="machineForm">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header" style="background-color: #CFECE0; color:black">
                                <h5 class="modal-title" id="addMachineModalLabel">Add Machine</h5>
                                <button type="button" class="btn-close custom-close" data-bs-dismiss="modal"
                                    aria-label="Close">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="machineName" class="form-label">Title</label>
                                    <input type="text" class="form-control" id="machineName" name="name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="machinedescription" class="form-label">Description</label>
                                    <textarea class="form-control" id="machinedescription" name="description" rows="3"
                                        style="border-radius:10px"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="machineName" class="form-label">Price</label>
                                    <input type="number" class="form-control" id="price" name="price" required>
                                </div>
                                <div id="machineSuccess" class="alert alert-success" style="display:none;"></div>
                                <div id="machineError" class="alert alert-danger" style="display:none;"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Save Machine</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Symptoms Modal -->
            <div class="modal fade" id="symptomsModal" tabindex="-1" aria-labelledby="symptomsModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="symptomsModalLabel">Update Symptoms</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                style="background-color:#cfece0">X</button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="patientId">

                            <div class="mb-3">
                                <label>Patient Name</label>
                                <input type="text" id="patientNamesymptoms" class="form-control" readonly>
                            </div>

                            <div class="mb-3">
                                <label>Symptoms</label>
                                <select name="symptoms[]" class="form-control select2" id="symptomDropdown" multiple
                                    style="min-height: 100px; max-height: 200px; overflow-y: auto;"></select>
                            </div>

                            <div class="mb-3">
                                <label>Input Type</label>
                                <div class="d-flex gap-3">
                                    <div>
                                        <input type="radio" name="symptom_option" value="image"
                                            onchange="toggleSymptomInput()">
                                        <span>Upload Image</span>
                                    </div>
                                    <div>
                                        <input type="radio" name="symptom_option" value="text"
                                            onchange="toggleSymptomInput()" checked>
                                        <span>Remarks</span>
                                    </div>
                                </div>
                            </div>

                            <div id="symptomImageBox" style="display:none;">
                                <input type="file" class="form-control" name="symptom_images[]" multiple
                                    accept="image/*" onchange="previewSymptomImages(this)">
                                <div id="existing_symptom_images" class="d-flex flex-wrap gap-2 mt-2"></div>
                                <div id="symptomImagePreview" class="d-flex flex-wrap gap-2 mt-2"></div>
                            </div>

                            <div id="symptomTextBox">
                                <textarea id="symptomRemarks" class="form-control" rows="3" placeholder="Enter remarks..."
                                    style="border-radius:10px !important;"></textarea>
                            </div>
                        </div>
                        <div id="editpatientsuccessMessage" class="alert alert-success text-center"
                            style="display:none; border-radius:10px;">
                        </div>

                        <div class="modal-footer">
                            <button type="button" id="updateSymptomsBtn" class="btn btn-primary">Update
                                Symptoms</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Diagnosis Modal -->
            <div class="modal fade" id="diagnosisModal" tabindex="-1" aria-labelledby="diagnosisModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="diagnosisModalLabel">Update Diagnosis</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                style="background-color:#cfece0">X</button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="patientId">

                            <div class="mb-3">
                                <label>Patient Name</label>
                                <input type="text" id="patientName" class="form-control" readonly>
                            </div>

                            <div class="mb-3">
                                <label>Diagnosis</label>
                                <select id="diagnosisDropdown" class="form-control select2" name="diagnosis_id[]"
                                    multiple>
                                    <option value="">Select Diagnosis</option>
                                </select>
                            </div>
                        </div>
                        <div id="editdignosissuccessMessage" class="alert alert-success text-center"
                            style="display:none; border-radius:10px;">
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="updateDiagnosisBtn" class="btn btn-primary">Update
                                Diagnosis</button>
                        </div>
                    </div>
                </div>
            </div>

            <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css">
            <script src=" https://code.jquery.com/jquery-3.7.1.js"></script>
            <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <!-- Select2 CSS -->
            <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
            <!-- Select2 JS -->
            <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>

            <script>
                let branchId = localStorage.getItem('selectedBranchId');
            </script>


            <!-- razorpay -->

            <script>
                $(document).on('click', '.generate-payment-link', function() {
                    let patientId = $(this).data('patient-id');

                    $('#razorpay').modal('show');

                    if (!patientsLoaded) {
                        loadPatientsrazorpay(patientId);
                        patientsLoaded = true; // ✅ prevent double API call
                    } else {
                        // just change selection without reloading dropdown
                        $('#razorpay_patient_id').val(patientId).trigger('change');
                    }
                });
            </script>


            {{-- treatment --}}
            <script>
                $(document).ready(function() {
                    $('#treatmentDropdownforbooking').on('change', function() {
                        // Get selected option
                        let selectedOption = $(this).find('option:selected');

                        // Get price from data attribute
                        let price = selectedOption.data('price') || 0;

                        // Set the payment field
                        $('#payment').val(price);
                    });
                });
                $(document).on('click', '.treatment-link', function() {
                    // Get patient data from link
                    let patientId = $(this).data('patient-id');
                    let patientName = $(this).data('patient-name');


                    let patientDropdown = $('#treatmentBookingModal #patientDropdown');

                    // Set the value
                    if (patientDropdown.find("option[value='" + patientId + "']").length) {
                        patientDropdown.val(patientId).trigger('change');
                    } else {
                        patientDropdown.append(
                            `<option value="${patientId}" selected>${patientName}</option>`
                        ).trigger('change');
                    }

                    // Disable the dropdown to make it fixed
                    patientDropdown.prop('disabled', true);

                    // If you want to keep the styling of Select2 after disabling
                    patientDropdown.select2({
                        width: '100%',
                        disabled: true
                    });



                    // if you have readonly patient name field, set it
                    $('#treatmentBookingModal #appointment_patient_name').val(patientName);

                    // Show modal
                    $('#treatmentBookingModal').modal('show');
                });


                $(document).ready(function() {
                    let payableAmount = 0; // initially 0, update dynamically

                    function validateAmounts() {
                        let cash = parseFloat($("#cash_amount").val()) || 0;
                        let online = parseFloat($("#online_amount").val()) || 0;
                        let total = cash + online;
                        let paidType = $("#paid_type").val(); // get selected paid type

                        // clear previous errors
                        $("#cashError").text("").addClass("d-none");
                        $("#onlineError").text("").addClass("d-none");

                        // ✅ Only validate when paid type is "fully"
                        if (paidType === "fully") {
                            if (cash > 0 || online > 0) {
                                if (total !== payableAmount) {
                                    if ($("#cash_amount").is(":focus")) {
                                        $("#cashError")
                                            .text(`⚠️ Cash + Online must equal payable amount (${payableAmount})`)
                                            .removeClass("d-none");
                                    }
                                    if ($("#online_amount").is(":focus")) {
                                        $("#onlineError")
                                            .text(`⚠️ Cash + Online must equal payable amount (${payableAmount})`)
                                            .removeClass("d-none");
                                    }
                                }
                            }
                        }
                    }


                    // Event: Cash / Online input
                    $("#cash_amount, #online_amount").on("input", function() {
                        validateAmounts();
                    });


                    // When treatment changes, update payableAmount dynamically
                    $('#treatmentDropdownforbooking').on('change', function() {
                        let price = $(this).find(':selected').data('price') || 0;
                        payableAmount = parseFloat(price); // update dynamic payableAmount
                        $('#amount').val(price); // show in amount input (optional)

                        // reset entered cash/online amounts
                        $("#cash_amount, #online_amount").val("");
                        $("#cashError, #onlineError").text("");
                    });
                });


                $(document).ready(function() {
                    // Set date input to today only
                    const today = new Date().toISOString().split('T')[0];
                    $('#todayOnlyDate').attr('min', today).attr('max', today).val(today);

                    // Handle payment mode change
                    $('#payment_mode').on('change', function() {
                        let mode = $(this).val();
                        $('#paid_type').val('');
                        $('#paid_amount_div, #remain_amount_div, #cash_amount_div, #online_amount_div').addClass(
                            'd-none');
                        $('#paid_amount, #remain_amount, #cash_amount, #online_amount').val('');

                        if (mode === 'cash' || mode === 'online' || mode === 'cash+online') {
                            $('#paid_amount_type').removeClass('d-none');
                        } else {
                            $('#paid_amount_type').addClass('d-none');
                        }
                    });

                    // Handle paid type change
                    $('#paid_type').on('change', function() {
                        let type = $(this).val();
                        let mode = $('#payment_mode').val();

                        $('#paid_amount_div, #remain_amount_div, #cash_amount_div, #online_amount_div').addClass(
                            'd-none');

                        if (mode === 'cash+online') {
                            if (type === 'fully') {
                                $('#cash_amount_div, #online_amount_div').removeClass('d-none');
                            } else if (type === 'partial') {
                                $('#cash_amount_div, #online_amount_div, #remain_amount_div').removeClass('d-none');
                            }
                        } else {
                            if (type === 'partial') {
                                $('#paid_amount_div, #remain_amount_div').removeClass('d-none');
                            }
                        }
                    });

                    // Auto-calc pending for cash+online
                    $('#cash_amount, #online_amount, #amount').on('input', function() {
                        const total = parseFloat($('#amount').val()) || 0;
                        const cash = parseFloat($('#cash_amount').val()) || 0;
                        const online = parseFloat($('#online_amount').val()) || 0;
                        const paid = cash + online;
                        $('#remain_amount').val((total - paid >= 0 ? (total - paid).toFixed(2) : 0));
                    });

                    // Auto-calc pending for simple partial
                    $('#paid_amount, #amount').on('input', function() {
                        const total = parseFloat($('#amount').val()) || 0;
                        const paid = parseFloat($('#paid_amount').val()) || 0;
                        $('#remain_amount').val((total - paid >= 0 ? (total - paid).toFixed(2) : 0));
                    });

                    // Initialize Select2 for all select inputs
                    $('.select2').select2({
                        width: '100%'
                    });

                    let branchId = localStorage.getItem('selectedBranchId');

                    $.ajax({
                        url: "/api/treatments",
                        type: "GET",
                        data: {
                            branch_id: branchId
                        },
                        xhrFields: {
                            withCredentials: true // send session cookie for Sanctum
                        },
                        success: function(data) {
                            let treatmentDropdown = $('select[name="treatment_id"]');

                            if (!treatmentDropdown.length) return;

                            treatmentDropdown.empty().append('<option value="">Select Treatment</option>');

                            let treatmentsList = data.treatments || [];

                            $.each(treatmentsList, function(key, treatment) {
                                // Split the comma-separated string into an array
                                let machines = treatment.machines ? treatment.machines.split(',') : [];

                                const name = treatment.name.charAt(0).toUpperCase() + treatment.name
                                    .slice(1);
                                treatmentDropdown.append(
                                    `<option
                                                                            value="${treatment.id}"
                                                                            data-doctor-id="${treatment.doctor_id}"
                                                                            data-price="${treatment.price}"
                                                                            data-machine="${treatment.machine || ''}"  
                                                                        >${name}</option>`
                                );

                            });

                            treatmentDropdown.select2({
                                placeholder: "Select Treatment",
                                allowClear: true,
                                width: '100%'
                            });

                            treatmentDropdown.on('select2:open', function() {
                                $('.select2-search__field').attr('placeholder', 'Search Treatment');
                            });
                        },
                        error: function(xhr) {
                            console.log("API Error:", xhr.status, xhr.responseText);
                        }
                    });
                    $('#treatmentDropdownforbooking').on('change', function() {
                        const selectedOption = $(this).find(':selected');
                        let machines = selectedOption.data('machine'); // string

                        // Split by comma to get array
                        machines = machines ? machines.split(',') : [];

                        if (machines.length) {
                            $('#treatmentMachines').html('<strong>Machine:</strong> ' + machines.join(', '));
                        } else {
                            $('#treatmentMachines').html('No machines assigned.');
                        }
                    });




                    $.ajax({
                        url: "/api/patientss",
                        type: "GET",
                        data: {
                            branch_id: branchId
                        },
                        dataType: "json",
                        xhrFields: {
                            withCredentials: true // ✅ send session cookie for Sanctum
                        },
                        success: function(response) {
                            let patientDropdown = $('#patientDropdown');

                            if (patientDropdown.length === 0) {
                                console.error("Dropdown not found! Check your HTML.");
                                return;
                            }

                            patientDropdown.empty().append('<option value="">Select Patient</option>');

                            if (!response.patients || response.patients.length === 0) {
                                console.warn("No patients found for this branch.");
                                return;
                            }

                            $.each(response.patients, function(index, patient) {
                                const capitalizedName = patient.fullname.charAt(0).toUpperCase() +
                                    patient.fullname.slice(1);

                                patientDropdown.append(
                                    `<option value="${patient.id}" data-treatment-id="${patient.treatment_id}">
                                                                                                                                                                                                                        ${capitalizedName}
                                                                                                                                                                                                                    </option>`
                                );
                            });

                            patientDropdown.select2({
                                placeholder: "Select Patient",
                                allowClear: true,
                                width: '100%'
                            });

                            patientDropdown.on('select2:open', function() {
                                $('.select2-search__field').attr('placeholder', 'Search Patient');
                            });
                        },
                        error: function(xhr) {
                            console.error("API Error:", xhr.status, xhr.responseText);
                        }
                    });



                    // When treatment changes, set payment field
                    $('#treatmentDropdownforbooking').on('change', function() {
                        let price = $(this).find(':selected').data('price') || '';
                        $('#amount').val(price);
                    });
                    document.addEventListener('DOMContentLoaded', function() {
                        let storedBranchId = localStorage.getItem('selectedBranchId');
                        if (storedBranchId) {
                            document.getElementById('branch_id').value = storedBranchId;
                        }
                    });


                    function loadMachines() {
                        $.ajax({
                            url: "/api/machines",
                            type: "GET",
                            data: {
                                branch_id: branchId
                            },
                            xhrFields: {
                                withCredentials: true
                            },
                            success: function(data) {
                                let machineDropdown = $('#machineDropdown');
                                machineDropdown.empty().append('<option value="">Select Machine</option>');

                                if (!data || data.length === 0) {
                                    console.warn("No machines found for this branch.");
                                    return;
                                }

                                $.each(data, function(index, machine) {
                                    const name = machine.name.charAt(0).toUpperCase() + machine.name
                                        .slice(1);
                                    machineDropdown.append(
                                        `<option value="${machine.id}" data-price="${machine.price}">
                                                                                                                            ${name} 
                                                                                                                        </option>`
                                    );
                                });

                                machineDropdown.select2({
                                    placeholder: "Select Machine",
                                    allowClear: true,
                                    width: '100%'
                                });
                            },
                            error: function(xhr) {
                                console.error("API Error:", xhr.status, xhr.responseText);
                            }
                        });
                    }

                    // ✅ Save new machine
                    $('#machineForm').on('submit', function(e) {
                        e.preventDefault();
                        $('#machineSuccess,#machineError').hide();

                        $.ajax({
                            url: "{{ route('machines.store') }}",
                            method: "POST",
                            data: {
                                name: $('#machineName').val(),
                                description: $('#machinedescription').val(),
                                price: $('#price').val(),
                                branch_id: branchId,
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                $('#machineSuccess').text(response.message).fadeIn();
                                setTimeout(() => {
                                    $('#addMachineModal').modal('hide');
                                    $('#machineForm')[0].reset();
                                    loadMachines(); // refresh dropdown after add
                                }, 1200);
                            },
                            error: function(xhr) {
                                $('#machineError').text(xhr.responseJSON?.message ||
                                    'Something went wrong!').fadeIn();
                                setTimeout(() => $('#machineError').fadeOut(), 3000);
                            }
                        });
                    });

                    // Load machines on page ready
                    $(document).ready(function() {
                        loadMachines();
                    });


                    // Validate and submit the form
                    $("#treatmentBookingForm").validate({
                        rules: {
                            patient_id: {
                                required: true
                            },
                            treatment_id: {
                                required: true
                            },
                            payment_date: {
                                required: true,
                                date: true
                            },
                            plan: {
                                required: true
                            },
                            amount: {
                                required: true,
                                number: true,
                                min: 1
                            },
                            payment_mode: {
                                required: true
                            },
                            paid_type: {
                                required: function() {
                                    return $("#payment_mode").val() !== "";
                                }
                            },
                            paid_amount: {
                                required: function() {
                                    return $("#paid_type").val() === "partial" && $("#payment_mode").val() !==
                                        "cash+online";
                                },
                                number: true,
                                min: 1
                            }
                        },
                        messages: {
                            patient_id: " Please select a patient",
                            treatment_id: " Please select a treatment",
                            payment_date: " Please choose a valid date",
                            plan: " Please select a plan",
                            amount: {
                                required: " Please enter payment amount",
                                number: " Amount must be numeric",
                                min: " Amount must be greater than 0"
                            },
                            payment_mode: " Please select a payment mode",

                        },
                        errorElement: 'span',
                        errorPlacement: function(error, element) {
                            error.addClass('invalid-feedback'); // bootstrap error text
                            element.closest('.form-group').append(error);
                        },
                        highlight: function(element) {
                            $(element).addClass('is-invalid'); // red border
                        },
                        unhighlight: function(element) {
                            $(element).removeClass('is-invalid'); // remove red border
                        },

                        submitHandler: function(form) {
                            let total = parseFloat($("#amount").val()) || 0;
                            let paymentMode = $("#payment_mode").val();
                            let paidType = $("#paid_type").val();
                            let cash = parseFloat($("#cash_amount").val()) || 0;
                            let online = parseFloat($("#online_amount").val()) || 0;

                            // 🚨 Validation for Cash+Online fully
                            if (paymentMode === "cash+online" && paidType === "fully") {
                                if ((cash + online) !== total) {
                                    $('#bookingError').text(
                                        `⚠️ Cash + Online must equal payable amount (${total})`).show();
                                    $('#bookingSuccess').hide();
                                    return false; // stop submission
                                }
                            }
                            // Get selected value even if the select is disabled
                            let selectedTreatment = $('#treatmentDropdownforbooking').val();

                            if (!selectedTreatment) {
                                alert("Please select a treatment");
                                return false;
                            }


                            let formData = {
                                patient_id: $("#patientDropdown").val(),
                                treatment_id: selectedTreatment,
                                machine_id: $("#machineDropdown").val(),
                                plan: $("#plan").val(),
                                amount: $("#amount").val(),
                                payment_mode: paymentMode,
                                payment_date: $("input[name='payment_date']").val(),
                                paid_type: paidType,
                                paid_amount: $("#paid_amount").val() || null,
                                remain_amount: $("#remain_amount").val() || null,
                                cash: $("#cash_amount").val() || null,
                                online: $("#online_amount").val() || null,
                                branch_id: $("#branch_id").val()
                            };

                            $.ajax({
                                url: "/api/treatment_booking",
                                type: "POST",
                                data: JSON.stringify(formData),
                                contentType: "application/json",
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(res) {
                                    $('#bookingSuccess').text(res.message ||
                                        'Booking created successfully').show();
                                    $('#bookingError').hide();
                                    form.reset();
                                    setTimeout(function() {
                                        location.reload();
                                    }, 1500);
                                },
                                error: function(xhr) {
                                    $('#bookingError').text(xhr.responseJSON?.message ||
                                        'Error occurred').show();
                                    $('#bookingSuccess').hide();
                                }
                            });
                        }

                    });
                });
            </script>

            <!-- medical report -->
            <script>
                $(document).ready(function() {
                    let branchId = localStorage.getItem('selectedBranchId');
                    $("#branch_id_modal").val(branchId);

                    // Fetch patients once (on page load)
                    $.ajax({
                        url: "/api/patientss",
                        type: "GET",
                        data: {
                            branch_id: branchId
                        },
                        dataType: "json",
                        success: function(response) {
                            let patientDropdown = $('#patientDropdownModal');
                            patientDropdown.empty().append('<option value="">Select Patient</option>');

                            if (response.patients && response.patients.length > 0) {
                                $.each(response.patients, function(index, patient) {
                                    const capitalizedName = patient.fullname.charAt(0).toUpperCase() +
                                        patient.fullname.slice(1);
                                    patientDropdown.append(
                                        `<option value="${patient.id}" data-treatment-id="${patient.treatment_id}">
                                                                                                                                                                                                                                                                ${capitalizedName}
                                                                                                                                                                                                                                                            </option>`
                                    );
                                });

                                patientDropdown.select2({
                                    placeholder: "Select Patient",
                                    allowClear: true,
                                    dropdownParent: $("#reportModal"), // ✅ fix select2 inside modal
                                    width: '100%'
                                });
                            }
                        }
                    });

                    // When clicking "Add Report" in dropdown
                    $(document).on("click", ".report-link", function() {
                        let patientId = $(this).data("patient-id");
                        let patientName = $(this).data("patient-name");

                        // Pre-fill patient in dropdown
                        if (patientId) {
                            let patientDropdown = $("#patientDropdownModal");
                            if (patientDropdown.find("option[value='" + patientId + "']").length === 0) {
                                patientDropdown.append(`<option value="${patientId}">${patientName}</option>`);
                            }
                            patientDropdown.val(patientId).trigger("change");
                        }

                        // Show modal
                        $("#reportModal").modal("show");
                    });
                    $("#medicalReportFormModal").validate({
                        rules: {
                            patient_id: {
                                required: true
                            },
                            report_file: {
                                required: true
                            }
                        },
                        messages: {
                            patient_id: {
                                required: "Please select a patient"
                            },
                            report_file: {
                                required: "Please upload a report file"
                            }
                        },
                        errorElement: "span",
                        errorClass: "text-danger",
                        highlight: function(element) {
                            $(element).addClass("is-invalid");
                        },
                        unhighlight: function(element) {
                            $(element).removeClass("is-invalid");
                        }
                    });

                    // Handle form submit
                    $("#medicalReportFormModal").on("submit", function(e) {
                        e.preventDefault();
                        let form = $(this);
                        let formData = new FormData(this);
                        if (form.valid()) {
                            let formData = new FormData(this);

                            $.ajax({
                                url: "{{ url('/api/medical-reports') }}",
                                type: "POST",
                                data: formData,
                                contentType: false,
                                processData: false,
                                headers: {
                                    "Authorization": "Bearer " + token
                                },
                                success: function(response) {
                                    $('#medicalsuccessMessageModal').text(response.message ||
                                        'Report created successfully').show();
                                    $('#medicalReportFormModal')[0].reset();

                                    setTimeout(function() {
                                        $("#reportModal").modal("hide");
                                        location.reload();
                                    }, 1500);
                                },
                                error: function(xhr) {
                                    var errorMessage = '';
                                    if (xhr.status === 422) {
                                        var errors = xhr.responseJSON.errors;
                                        $.each(errors, function(key, messages) {
                                            errorMessage += messages[0] + '<br>';
                                        });
                                    }
                                    $('#medicalerrorMessageModal').html(errorMessage).show();
                                }
                            });
                        }
                    });
                });
            </script>


            {{-- symptomps diagnosis --}}
            <script>
                $('#symptomDropdown').select2({
                    placeholder: "Select Symptoms",
                    width: '100%'
                }).on('select2:open', function() {
                    $('.select2-search__field').attr('placeholder', 'Search Symptoms');
                });

                // Open Symptoms modal
                $(document).on('click', '.open-symptoms-modal', function(e) {
                    e.preventDefault();
                    let patientId = $(this).data('patient-id');
                    openSymptomsModal(patientId);
                });

                function openSymptomsModal(patientId) {
                    let token = localStorage.getItem("token");
                    $('#patientId').val(patientId);

                    // Fetch patient details
                    $.ajax({
                        url: `/api/patient/${patientId}`,
                        type: 'GET',
                        headers: {
                            "Authorization": "Bearer " + token
                        },
                        success: function(res) {
                            console.log(res); // Check API structure

                            // Access nested patient object
                            let patient = res.patient || {};
                            let fullname = patient.fullname || '';
                            $('#patientNamesymptoms').val(fullname);

                            // Load symptoms from symptom_ids
                            let symptomIds = [];
                            if (res.symptom_ids) {
                                symptomIds = res.symptom_ids;
                            } else if (res.patient && res.patient.symptoms) {
                                try {
                                    symptomIds = JSON.parse(res.patient.symptoms);
                                } catch (e) {
                                    symptomIds = [];
                                }
                            }
                            console.log("Symptom IDs extracted:", symptomIds);
                            loadSymptoms(symptomIds);




                            // Handle symptom input type (text / image)
                            if (res.symptom_images && res.symptom_images.length > 0) {
                                $('input[name="symptom_option"][value="image"]').prop('checked', true);
                                toggleSymptomInput();
                                displayExistingSymptomImages(res.symptom_images); // pass relative paths
                            } else {
                                $('input[name="symptom_option"][value="text"]').prop('checked', true);
                                toggleSymptomInput();
                                $('#symptomRemarks').val(res.symptom_remarks || '');
                            }

                            $('#symptomsModal').modal('show');
                        },
                        error: function(err) {
                            console.error("Unable to fetch patient:", err);
                        }
                    });
                }


                // Load Symptoms
                function loadSymptoms(symptomIds) {
                    let branchId = localStorage.getItem('selectedBranchId');
                    console.log("Branch ID from localStorage:", branchId);
                    $.ajax({
                        url: "{{ route('symptoms.list') }}",
                        type: "GET",
                        data: {
                            branch_id: branchId
                        },
                        success: function(data) {
                            console.log("API Response:", data);
                            let symptomsList = Array.isArray(data) ? data : (data.symptoms || []);
                            let symptomDropdown = $('#symptomDropdown');
                            symptomDropdown.empty().append('<option value="">Select Symptom</option>');
                            $.each(symptomsList, function(index, symptom) {
                                const name = symptom.name.charAt(0).toUpperCase() + symptom.name.slice(1);
                                symptomDropdown.append(
                                    `<option value="${symptom.id}">${name}</option>`
                                );
                            });
                            // Select the patient's symptoms in the dropdown
                            if (symptomIds && symptomIds.length > 0) {
                                symptomDropdown.val(symptomIds).trigger('change');
                            }
                        },
                        error: function(xhr) {
                            console.error("Failed to load symptoms:", xhr.status, xhr.responseText);
                        }
                    });
                }



                // Toggle input type
                function toggleSymptomInput() {
                    const option = document.querySelector('input[name="symptom_option"]:checked').value;
                    document.getElementById('symptomImageBox').style.display = (option === 'image') ? 'block' : 'none';
                    document.getElementById('symptomTextBox').style.display = (option === 'text') ? 'block' : 'none';
                }

                let removedImages = []; // track existing images removed

                // Preview uploaded images
                function previewSymptomImages(input) {
                    const previewContainer = document.getElementById('symptomImagePreview');
                    previewContainer.innerHTML = '';
                    const files = input.files;

                    if (files) {
                        Array.from(files).forEach((file, index) => {
                            if (file.type.startsWith('image/')) {
                                const reader = new FileReader();
                                reader.onload = function(e) {
                                    const wrapper = document.createElement('div');
                                    wrapper.style.position = 'relative';
                                    wrapper.style.display = 'inline-block';

                                    const img = document.createElement('img');
                                    img.src = e.target.result;
                                    img.style.width = '40px';
                                    img.style.height = '40px';
                                    img.style.objectFit = 'cover';
                                    img.style.border = '1px solid #ccc';
                                    img.style.borderRadius = '5px';
                                    img.style.marginRight = '10px';

                                    // Cancel button
                                    const cancelBtn = document.createElement('span');
                                    cancelBtn.innerHTML = '&times;';
                                    cancelBtn.style.position = 'absolute';
                                    cancelBtn.style.top = '-8px';
                                    cancelBtn.style.right = '2px';
                                    cancelBtn.style.cursor = 'pointer';
                                    cancelBtn.style.background = '#f00';
                                    cancelBtn.style.color = '#fff';
                                    cancelBtn.style.fontSize = '12px';
                                    cancelBtn.style.fontWeight = 'bold';
                                    cancelBtn.style.borderRadius = '50%';
                                    cancelBtn.style.padding = '2px 5px';

                                    cancelBtn.onclick = function() {
                                        wrapper.remove();
                                        // remove file from input
                                        const dt = new DataTransfer();
                                        Array.from(input.files).forEach((f, i) => {
                                            if (i !== index) dt.items.add(f);
                                        });
                                        input.files = dt.files;
                                    };

                                    wrapper.appendChild(img);
                                    wrapper.appendChild(cancelBtn);
                                    previewContainer.appendChild(wrapper);
                                };
                                reader.readAsDataURL(file);
                            }
                        });
                    }
                }

                // Show existing symptom images
                function displayExistingSymptomImages(images) {
                    const container = document.getElementById('existing_symptom_images');
                    container.innerHTML = '';

                    images.forEach((image, index) => {
                        // Check if running local or production
                        const isLocal = window.location.hostname === 'localhost' || window.location.hostname ===
                            '127.0.0.1';
                        const fullImagePath = window.location.origin + (isLocal ? '' : '/public') + image;

                        // Wrapper
                        const wrapper = document.createElement('div');
                        wrapper.className = 'symptom-image-wrapper';
                        wrapper.style.position = 'relative';
                        wrapper.style.display = 'inline-block';
                        wrapper.style.margin = '5px';

                        // Image
                        const imgEl = document.createElement('img');
                        imgEl.src = fullImagePath;
                        imgEl.style.objectFit = 'cover';
                        imgEl.style.borderRadius = '5px';
                        imgEl.style.width = '40px';
                        imgEl.style.height = '40px';
                        imgEl.style.marginRight = '10px';




                        // Remove button
                        const removeBtn = document.createElement('button');
                        removeBtn.type = 'button';
                        removeBtn.innerHTML = '&times;';
                        removeBtn.className = 'remove-db-btn';
                        removeBtn.dataset.path = image; // store relative path for backend
                        removeBtn.style.position = 'absolute';
                        removeBtn.style.top = '-5px';
                        removeBtn.style.right = '-5px';
                        removeBtn.style.cursor = 'pointer';
                        removeBtn.style.background = '#f00';
                        removeBtn.style.color = '#fff';
                        removeBtn.style.border = 'none';
                        removeBtn.style.fontSize = '14px';
                        removeBtn.style.fontWeight = 'bold';
                        removeBtn.style.borderRadius = '50%';
                        removeBtn.style.width = '20px';
                        removeBtn.style.height = '20px';
                        removeBtn.style.lineHeight = '16px';
                        removeBtn.style.textAlign = 'center';
                        removeBtn.style.padding = '0';

                        removeBtn.onclick = function() {
                            wrapper.remove();
                            removedImages.push(image); // track removed (relative path)
                            document.getElementById('removed_images').value = JSON.stringify(removedImages);
                        };

                        wrapper.appendChild(imgEl);
                        wrapper.appendChild(removeBtn);
                        container.appendChild(wrapper);
                    });
                }




                $('#updateSymptomsBtn').on('click', function() {
                    let patientId = $('#patientId').val();
                    let token = localStorage.getItem("token");

                    let symptoms = $('#symptomDropdown').val() || [];
                    let symptomOption = $('input[name="symptom_option"]:checked').val();
                    let remarks = $('#symptomRemarks').val();
                    let files = $('#symptomImageBox input[type="file"]')[0].files;

                    let formData = new FormData();
                    formData.append('symptoms', JSON.stringify(symptoms));
                    formData.append('symptom_option', symptomOption);
                    formData.append('symptom_remarks', remarks);

                    if (files.length > 0) {
                        Array.from(files).forEach((file, index) => {
                            formData.append('symptom_images[]', file);
                        });
                    }

                    $.ajax({
                        url: `/api/patients/${patientId}/update-symptoms`,
                        type: 'POST',
                        headers: {
                            "Authorization": "Bearer " + token
                        },
                        processData: false,
                        contentType: false,
                        data: formData,
                        success: function(res) {
                            $("#editpatientsuccessMessage")
                                .text(res.message || 'Patient updated successfully')
                                .fadeIn();



                            setTimeout(() => {
                                $('#symptomsModal').modal('hide');
                                location.reload();
                            }, 1500);
                        },
                        error: function(err) {
                            console.error("Update failed:", err.responseJSON || err);
                        }
                    });
                });














                // Open modal when clicking Diagnosis link
                $(document).on('click', '.open-diagnosis-modal', function(e) {
                    e.preventDefault();
                    let patientId = $(this).data('patient-id');
                    openDiagnosisModal(patientId);
                });

                function openDiagnosisModal(patientId) {
                    let token = localStorage.getItem("token");

                    // Store patientId for later update
                    $('#patientId').val(patientId);

                    // Fetch patient details
                    $.ajax({
                        url: `/api/patient/${patientId}`,
                        type: 'GET',
                        headers: {
                            "Authorization": "Bearer " + token
                        },
                        success: function(res) {
                            $('#patientName').val(res.patient.fullname);

                            // Fetch diagnoses and pre-select old diagnosis
                            fetchDiagnoses(res.patient.diagnosis_id);
                        },
                        error: function(err) {
                            console.error("Unable to fetch patient:", err);
                        }
                    });

                    // Show modal
                    $('#diagnosisModal').modal('show');
                }

                function fetchDiagnoses(selectedId = null) {
                    let token = localStorage.getItem("token");
                    let branchId = localStorage.getItem('selectedBranchId');
                    let diagnosisDropdown = $('#diagnosisDropdown');

                    $.ajax({
                        url: "/api/diagnoses",
                        type: "GET",
                        data: {
                            branch_id: branchId
                        },
                        headers: {
                            "Authorization": "Bearer " + token
                        },
                        success: function(response) {
                            diagnosisDropdown.empty().append('<option value="">Select Diagnosis</option>');

                            if (response.diagnoses && response.diagnoses.length > 0) {
                                $.each(response.diagnoses, function(i, diagnosis) {
                                    let name = diagnosis.name.charAt(0).toUpperCase() + diagnosis.name.slice(1);
                                    let selected = (diagnosis.id == selectedId) ? 'selected' : '';
                                    diagnosisDropdown.append(
                                        `<option value="${diagnosis.id}" ${selected}>${name}</option>`);
                                });
                            }

                            // Initialize or refresh Select2
                            if ($.fn.select2 && !diagnosisDropdown.hasClass('select2-hidden-accessible')) {
                                diagnosisDropdown.select2({
                                    placeholder: "Select diagnosis",
                                    allowClear: true,
                                    width: '100%',
                                    multiple: true
                                });
                            } else {
                                diagnosisDropdown.trigger('change');
                            }
                        },
                        error: function(xhr) {
                            console.error("Unable to fetch diagnoses:", xhr.responseText);
                        }
                    });
                }

                // Update Diagnosis
                $('#updateDiagnosisBtn').on('click', function() {
                    let patientId = $('#patientId').val();
                    let diagnosisId = $('#diagnosisDropdown').val();
                    let token = localStorage.getItem("token");

                    if (!diagnosisId) {
                        alert("Please select a diagnosis");
                        return;
                    }

                    $.ajax({
                        url: `/api/patients/${patientId}/update-diagnosis`,
                        type: 'POST',
                        headers: {
                            "Authorization": "Bearer " + token,
                            "Content-Type": "application/json"
                        },
                        data: JSON.stringify({
                            diagnosis_id: diagnosisId
                        }),
                        success: function(res) {
                            $("#editdignosissuccessMessage")
                                .text(res.message || 'Patient updated successfully')
                                .fadeIn();



                            setTimeout(() => {
                                $('#diagnosisModal').modal('hide');
                                location.reload();
                            }, 1500);
                        },
                        error: function(err) {
                            console.error("Update failed:", err.responseJSON || err);
                            alert(err.responseJSON?.message || "Update failed");
                        }
                    });

                });
            </script>



            {{-- followup --}}
            <script>
                $(document).ready(function() {
                    let branchId = localStorage.getItem("selectedBranchId");
                    let token = @json(session('access_token'));

                    // Initialize select2 with modal parent
                    $('#select-type, #select-status, #treatmentDropdown, #doctorSelect, #doctorSelect1, #treatmentDropdown1,#select-followup')
                        .select2({
                            dropdownParent: $('#followupModal'),
                            width: '100%'
                        });

                    // Open modal on link click
                    $(document).on("click", ".followup-link", function() {
                        var patientId = $(this).data("patient-id");

                        // Fetch patient details
                        $.ajax({
                            url: "/api/patient/" + patientId,
                            method: "GET",
                            success: function(response) {
                                var patient = response.patient;

                                // Fill modal inputs
                                $("#followup_patient_id").val(patient.id);
                                $("#followup_patient_name").val(patient.fullname);

                                // Show modal
                                $("#followupModal").modal("show");

                                // Load doctors when modal opens
                                loadDoctors(branchId, token);
                            },
                            error: function() {
                                alert("Failed to fetch patient details!");
                            }
                        });
                    });

                    // 🔹 Fetch Doctors
                    function loadDoctors(branchId, token) {
                        $.ajax({
                            url: "/api/doctors",
                            type: "GET",
                            data: {
                                branch_id: branchId
                            },
                            dataType: "json",
                            headers: {
                                "Authorization": "Bearer " + token
                            },
                            success: function(response) {
                                let doctorDropdown = $("#doctorSelect1");
                                doctorDropdown.empty().append('<option value="">Select Doctor</option>');

                                let doctorsList = response.doctors || [];
                                $.each(doctorsList, function(index, doctor) {
                                    const name = doctor.fullname.charAt(0).toUpperCase() + doctor
                                        .fullname.slice(1);
                                    doctorDropdown.append(
                                        `<option value="${doctor.id}">${name}</option>`);
                                });
                            },
                            error: function(xhr) {
                                console.log("Doctor API Error:", xhr.status, xhr.responseText);
                            }
                        });
                    }

                    // 🔹 Handle form submission
                    $('#followupForm').on('submit', function(e) {
                        e.preventDefault();

                        var branchId = localStorage.getItem('selectedBranchId');
                        if (!branchId) {
                            alert("Branch ID not found in localStorage");
                            return;
                        }

                        // ✅ Set hidden input value
                        $("#branch_id").val(branchId);
                        let formData = $(this).serializeArray();
                        formData.push({
                            name: "branch_id",
                            value: branchId
                        });

                        $.ajax({
                            url: "/api/followup",
                            type: "POST",
                            data: formData,
                            headers: {
                                "Authorization": "Bearer " + token
                            },
                            success: function(response) {
                                $('#followupSuccessMessage').text(response.message ||
                                    'Followup created successfully').show();
                                $('#followupForm')[0].reset();

                                setTimeout(function() {
                                    $("#followupModal").modal("hide");
                                    location.reload();
                                }, 1500);
                            },
                            error: function(xhr) {
                                $('#followupErrorMessage')
                                    .text(xhr.responseJSON?.message || "Something went wrong")
                                    .show();
                                setTimeout(function() {
                                    $('#followupErrorMessage').fadeOut();
                                }, 3000);
                            }
                        });
                    });
                });
            </script>

            {{-- appointment --}}
            <script>
                $(document).ready(function() {
                    let branchId = localStorage.getItem("selectedBranchId");
                    let token = @json(session('access_token'));

                    // Initialize select2 with modal parent
                    $('#select-type, #select-status, #treatmentDropdown, #doctorSelect').select2({
                        dropdownParent: $('#appointmentsModal'),
                        width: '100%'
                    });

                    // Open modal on link click
                    $(document).on("click", ".appointments-link", function() {
                        var patientId = $(this).data("patient-id");

                        // Fetch patient details
                        $.ajax({
                            url: "/api/patient/" + patientId,
                            method: "GET",
                            success: function(response) {
                                var patient = response.patient;

                                // Fill modal inputs
                                $("#appointment_patient_id").val(patient.id);
                                $("#appointment_patient_name").val(patient.fullname);

                                // Show modal
                                $("#appointmentsModal").modal("show");

                                // Load treatments when modal opens
                                loadTreatments(branchId);
                                loadDoctors(branchId, token);
                            },
                            error: function() {
                                alert("Failed to fetch patient details!");
                            }
                        });
                    });

                    // 🔹 Fetch Treatments
                    function loadTreatments(branchId) {
                        $.ajax({
                            url: "/api/treatments",
                            type: "GET",
                            data: {
                                branch_id: branchId
                            },
                            xhrFields: {
                                withCredentials: true
                            },
                            success: function(data) {
                                let treatmentDropdown = $("#treatmentDropdown");
                                treatmentDropdown.empty().append('<option value="">Select Treatment</option>');

                                let treatmentsList = data.treatments || [];
                                $.each(treatmentsList, function(key, treatment) {
                                    const name = treatment.name.charAt(0).toUpperCase() + treatment.name
                                        .slice(1);
                                    treatmentDropdown.append(
                                        `<option value="${treatment.id}" data-doctor-id="${treatment.doctor_id}">${name}</option>`
                                    );
                                });

                                treatmentDropdown.off("change").on("change", function() {
                                    let selectedTreatment = $(this).find("option:selected");
                                    let doctorId = selectedTreatment.data("doctor-id");

                                    if (doctorId) {
                                        $("#doctorSelect").val(doctorId).trigger("change");
                                    } else {
                                        $("#doctorSelect").val("").trigger("change");
                                    }
                                });
                            },
                            error: function(xhr) {
                                console.log("Treatment API Error:", xhr.status, xhr.responseText);
                            }
                        });
                    }

                    // 🔹 Fetch Doctors
                    function loadDoctors(branchId, token) {
                        $.ajax({
                            url: "/api/doctors",
                            type: "GET",
                            data: {
                                branch_id: branchId
                            },
                            dataType: "json",
                            headers: {
                                "Authorization": "Bearer " + token
                            },
                            success: function(response) {
                                let doctorDropdown = $("#doctorSelect");
                                doctorDropdown.empty().append('<option value="">Select Doctor</option>');

                                let doctorsList = response.doctors || [];
                                $.each(doctorsList, function(index, doctor) {
                                    const name = doctor.fullname.charAt(0).toUpperCase() + doctor
                                        .fullname.slice(1);
                                    doctorDropdown.append(
                                        `<option value="${doctor.id}">${name}</option>`);
                                });
                            },
                            error: function(xhr) {
                                console.log("Doctor API Error:", xhr.status, xhr.responseText);
                            }
                        });
                    }

                    // 🔹 Handle form submission
                    $(document).on("submit", "#appointmentsForm", function(e) {
                        e.preventDefault();

                        // put branchId into hidden field
                        $("#branch_id").val(branchId);

                        let formData = $(this).serialize();

                        $.ajax({
                            url: "/api/appointments",
                            method: "POST",
                            data: formData,
                            headers: {
                                "Authorization": "Bearer " + token
                            },
                            success: function(response) {
                                $('#appointmentSuccessMessage')
                                    .text(response.message || 'Appointment created successfully')
                                    .show();

                                // Reset the form
                                $('#appointmentsForm')[0].reset();

                                // Hide modal after 1.5 seconds
                                setTimeout(function() {
                                    $("#appointmentsModal").modal("hide"); // ✅ correct modal ID
                                    location.reload();
                                }, 1500);
                            },
                            error: function(xhr) {
                                $('#appointmentErrorMessage')
                                    .text(xhr.responseJSON?.message || "Something went wrong")
                                    .show();

                                // Auto-hide error after 3s (optional)
                                setTimeout(function() {
                                    $('#appointmentErrorMessage').fadeOut();
                                }, 3000);
                            }
                        });
                    });
                });
            </script>

            {{-- text area --}}

            {{-- assessment --}}
            <script>
                // Fetch options from the original dropdown, including description
                function getAssessmentOptions() {
                    let options = '';
                    $('#assessmentDropdown option').each(function() {
                        options +=
                            `<option value="${$(this).val()}" data-description="${$(this).data('description') || ''}" ${$(this).prop('selected') ? 'selected' : ''}>${$(this).text()}</option>`;
                    });
                    return options;
                }

                // Add new row
                $(document).on("click", ".addRow", function() {
                    let newRow = `
                                                            <div class="assessment-item row mb-2">
                                                                <div class="col-md-12 col-12 assign-template">
                                                                    <div class="form-group">
                                                                        <label class="mr-2">
                                                                            <i class="fas fa-medkit icon-style"></i> Assign Template
                                                                        </label>
                                                                        <select class="form-control select2 assessment-template" name="assessment_id[]">
                                                                            ${getAssessmentOptions()}
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12 col-12 ">
                                                                    <div class="form-group">
                                                                        <i class="fas fa-align-left icon-style"></i>
                                                                        <label>Description</label>
                                                                        <textarea class="form-control assessment-description mt-1" name="description[]"  rows="5" placeholder="Enter description" style="border-radius: 10px"></textarea>
                                                                    </div>
                                                                </div>
                                                                    <div class="col-md-12 col-12 text-center ">
                                                                        <button type="button" class="btn btn-danger btn-sm remove-assessment">
                                                                            <i class="fas fa-trash"></i>
                                                                        </button>
                                                                    </div>


                                                                </div>`;
                    $("#assessmentContainer").append(newRow);
                    $(".select2").select2(); // Re-init select2
                });

                // Remove row
                $(document).on("click", ".remove-assessment", function() {
                    $(this).closest(".assessment-item").remove();
                });

                // Auto-fill description when dropdown changes
                $(document).on("change", ".assessment-template, #assessmentDropdown", function() {
                    var description = $(this).find('option:selected').data('description') || '';
                    if ($(this).attr('id') === 'assessmentDropdown') {
                        $(this).closest('.row').find('.assessment-description').first().val(description);
                    } else {
                        $(this).closest('.assessment-item').find('.assessment-description').val(description);
                    }
                });

                // Fetch patient details and templates
                $(document).on("click", ".assessment-link", function() {
                    var patientId = $(this).data("patient-id");
                    $.ajax({
                        url: "/api/patient/" + patientId,
                        method: "GET",
                        success: function(response) {
                            var patient = response.patient;
                            $("#assessment_patient_id").val(patient.id);
                            $("#assessment_patient_name").val(patient.fullname);

                            // Fetch templates
                            $.ajax({
                                url: "/api/assessment-templates",
                                method: "GET",
                                success: function(data) {
                                    var dropdown = $("#assessmentDropdown");
                                    dropdown.empty();
                                    dropdown.append('<option value="">Select Assessment</option>');
                                    $.each(data, function(index, template) {
                                        dropdown.append(
                                            `<option value="${template.id}" data-description="${template.description || ''}">${template.name}</option>`
                                        );
                                    });
                                    dropdown.select2({
                                        dropdownParent: $('#assessmentModal'),
                                        placeholder: "Select Assessment",
                                        allowClear: true,
                                        width: '100%'
                                    });
                                },
                                error: function() {
                                    alert("Error loading templates");
                                }
                            });

                            $("#assessmentModal").modal("show");
                        },
                        error: function() {
                            alert("Could not fetch patient details");
                        }
                    });
                });

                $(document).on("submit", "#assessmentForm", function(e) {
                    e.preventDefault();
                    var branchId = localStorage.getItem('selectedBranchId');
                    if (!branchId) {
                        alert("Branch ID not found in localStorage");
                        return;
                    }

                    var patientId = $("#assessment_patient_id").val();
                    if (!patientId) {
                        alert("Patient ID is missing!");
                        return;
                    }

                    var assessmentIds = [$("#assessmentDropdown").val()];
                    var descriptions = [$(".assessment-description").first().val()];

                    $(".assessment-item").each(function() {
                        assessmentIds.push($(this).find(".assessment-template").val());
                        descriptions.push($(this).find(".assessment-description").val());
                    });

                    assessmentIds = assessmentIds.filter(id => id);
                    descriptions = descriptions.filter(desc => desc !== undefined);

                    if (assessmentIds.length === 0) {
                        alert("Please select at least one assessment template!");
                        return;
                    }

                    var formData = {
                        branch_id: branchId,
                        patient_id: patientId,
                        assessment_id: assessmentIds,
                        description: descriptions
                    };

                    $.ajax({
                        url: "/api/patient-assign-assessment",
                        method: "POST",
                        data: formData,
                        success: function(response) {
                            $('#assessmentSuccessMessage').text(response.message ||
                                'Assessment assigned successfully').show();
                            $('#assessmentForm')[0].reset();
                            setTimeout(function() {
                                $("#assessmentModal").modal("hide");
                                location.reload();
                            }, 2000);
                        },
                        error: function(xhr) {
                            console.log(xhr.responseJSON);
                            alert("Error: " + (xhr.responseJSON?.message || "Something went wrong"));
                        }
                    });
                });

                // Reset select2 when modal closes
                $('#assessmentModal').on('hidden.bs.modal', function() {
                    $("#assessmentDropdown").val(null).trigger('change');
                });
            </script>


            {{-- Diet-Chart --}}
            <script>
                // Get all diet template options for cloning
                function getDietTemplateOptions() {
                    let options = '';
                    $('#dietChartDropdown option').each(function() {
                        options +=
                            `<option value="${$(this).val()}" data-description="${$(this).data('description') || ''}" ${$(this).prop('selected') ? 'selected' : ''}>${$(this).text()}</option>`;
                    });
                    return options;
                }

                // Add new diet chart row
                $(document).on("click", ".addRow", function() {
                    let newRow = `
            <div class="diet-item row mb-2">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>
                            <i class="fa-solid fa-list-alt me-1"></i> Assign Diet Template
                        </label>
                        <select class="form-control select2 diet-template" name="diet_template_id[]">
                            ${getDietTemplateOptions()}
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <i class="fas fa-align-left icon-style"></i>
                        <label>Description</label>
                        <textarea class="form-control diet-description mt-1" name="description[]" rows="5" placeholder="Enter description" style="border-radius: 10px"></textarea>
                    </div>
                </div>
                <div class="col-md-12 text-center mb-2">
                    <button type="button" class="btn btn-danger btn-sm remove-diet">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            

            </div>
        `;
                    $("#dietChartContainer").append(newRow);
                    $(".select2").select2(); // Re-initialize select2
                });

                // Remove diet row
                $(document).on("click", ".remove-diet", function() {
                    $(this).closest(".diet-item").remove();
                });

                // Auto-fill description when selecting a template
                $(document).on("change", ".diet-template, #dietChartDropdown", function() {
                    var description = $(this).find('option:selected').data('description') || '';
                    if ($(this).attr('id') === 'dietChartDropdown') {
                        $(this).closest('.row').find('.diet-description').first().val(description);
                    } else {
                        $(this).closest('.diet-item').find('.diet-description').val(description);
                    }
                });

                // When clicking the "Diet Chart" button/link
                $(document).on("click", ".diet-chart-link", function() {
                    var patientId = $(this).data("patient-id");

                    // Fetch patient details
                    $.ajax({
                        url: "/api/patient/" + patientId,
                        method: "GET",
                        success: function(response) {
                            var patient = response.patient;
                            $("#dietchart_patient_id").val(patient.id);
                            $("#dietchart_patient_name").val(patient.fullname);

                            // Fetch diet templates
                            $.ajax({
                                // url: "/api/diet-templates",
                                url: "/api/patient-assign-dietchart", // ✅ Correct URL
                                method: "GET",
                                success: function(data) {
                                    var dropdown = $("#dietChartDropdown");
                                    dropdown.empty();
                                    dropdown.append(
                                        '<option value="">Select Diet Template</option>');
                                    $.each(data, function(index, template) {
                                        dropdown.append(
                                            `<option value="${template.id}" data-description="${template.description || ''}">${template.name}</option>`
                                        );
                                    });
                                    dropdown.select2({
                                        dropdownParent: $('#dietChartModal'),
                                        placeholder: "Select Diet Template",
                                        allowClear: true,
                                        width: '100%'
                                    });
                                },
                                error: function() {
                                    alert("Error loading diet templates");
                                }
                            });

                            $("#dietChartModal").modal("show");
                        },
                        error: function() {
                            alert("Could not fetch patient details");
                        }
                    });
                });

                // Submit diet chart form
                $(document).on("submit", "#dietChartForm", function(e) {
                    e.preventDefault();

                    var branchId = localStorage.getItem('selectedBranchId');
                    if (!branchId) {
                        alert("Branch ID not found in localStorage");
                        return;
                    }

                    var patientId = $("#dietchart_patient_id").val();
                    if (!patientId) {
                        alert("Patient ID is missing!");
                        return;
                    }

                    // Collect data
                    var templateIds = [$("#dietChartDropdown").val()];
                    var descriptions = [$(".diet-description").first().val()];

                    $(".diet-item").each(function() {
                        templateIds.push($(this).find(".diet-template").val());
                        descriptions.push($(this).find(".diet-description").val());
                    });

                    templateIds = templateIds.filter(id => id);
                    descriptions = descriptions.filter(desc => desc !== undefined);

                    if (templateIds.length === 0) {
                        alert("Please select at least one diet template!");
                        return;
                    }

                    var formData = {
                        branch_id: branchId,
                        patient_id: patientId,
                        diet_template_id: templateIds,
                        description: descriptions
                    };

                    $.ajax({
                        url: "/api/patient-assign-dietchart",
                        method: "POST",
                        data: formData,
                        success: function(response) {
                            $('#dietChartSuccessMessage').text(response.message ||
                                'Diet chart assigned successfully').show();
                            $('#dietChartForm')[0].reset();
                            setTimeout(function() {
                                $("#dietChartModal").modal("hide");
                                location.reload();
                            }, 2000);
                        },
                        error: function(xhr) {
                            console.log(xhr.responseJSON);
                            alert("Error: " + (xhr.responseJSON?.message || "Something went wrong"));
                        }
                    });
                });

                // Reset select2 when modal closes
                $('#dietChartModal').on('hidden.bs.modal', function() {
                    $("#dietChartDropdown").val(null).trigger('change');
                });
            </script>


            {{-- homeadvice --}}
            <script>
                // 🔹 Generate dropdown options dynamically (including description)
                function gethomeadviceOptions() {
                    let options = '';
                    $('#homeadviceDropdown option').each(function() {
                        options +=
                            `<option value="${$(this).val()}" 
                                                                     data-description="${$(this).data('description') || ''}" 
                                                                     ${$(this).prop('selected') ? 'selected' : ''}>${$(this).text()}</option>`;
                    });
                    return options;
                }

                // 🔹 Add new row on + button click
                $(document).on("click", ".addRowhome", function() {
                    let newRow = `
                                                    <div class="homeadvice-item row mb-2">
                                                        <div class="col-md-12 assign-template">
                                                            <div class="form-group">
                                                                <label class="mr-2">
                                                                    <i class="fas fa-medkit icon-style"></i> Assign Template
                                                                </label>
                                                                <select class="form-control select2 homeadvice-template" name="homeadvice_id[]">
                                                                    ${gethomeadviceOptions()}
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12 ">
                                                            <div class="form-group">
                                                                <label class="mr-2">
                                                                    <i class="fas fa-align-left icon-style"></i> Description
                                                                </label>
                                                                <textarea class="form-control homeadvice-description" name="description[]"
                                                                          placeholder="Enter description" rows="5" style="border-radius: 10px"></textarea>
                                                            </div>
                                                              </div>
                                                            <div class="col-md-12 col-12 mb-2 text-center ">
                                                                <button type="button" class="btn btn-danger btn-sm remove-homeadvice">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>


                                                        </div>
                                                    `;

                    let $newRow = $(newRow);
                    $("#homeAdviceContainer").append($newRow);

                    // Initialize Select2 for new row
                    $newRow.find(".select2").select2({
                        dropdownParent: $('#homeadviceModal'),
                        width: '100%'
                    });
                });

                // 🔹 Remove row on trash button click
                $(document).on("click", ".remove-homeadvice", function() {
                    $(this).closest(".homeadvice-item").remove();
                });

                // 🔹 Open modal + load patient + home advices
                $(document).on("click", ".homeadvice-link", function() {
                    var patientId = $(this).data("patient-id");

                    $.ajax({
                        url: "/api/patient/" + patientId,
                        method: "GET",
                        success: function(response) {
                            var patient = response.patient;

                            $("#homeadvice_patient_id").val(patient.id);
                            $("#patient_name").val(patient.fullname);

                            // Fetch home advice templates
                            $.ajax({
                                url: "/api/homeadvices",
                                method: "GET",
                                success: function(data) {
                                    var dropdown = $("#homeadviceDropdown");
                                    dropdown.empty();
                                    dropdown.append('<option value="">Select Home Advice</option>');

                                    $.each(data, function(index, advice) {
                                        dropdown.append(
                                            `<option value="${advice.id}" 
                                                                                      data-description="${advice.description || ''}">
                                                                                ${advice.template_name}
                                                                             </option>`
                                        );
                                    });

                                    dropdown.select2({
                                        dropdownParent: $('#homeadviceModal'),
                                        placeholder: "Select Home Advice",
                                        allowClear: true,
                                        width: '100%'
                                    });
                                },
                                error: function() {
                                    alert("Error loading home advices");
                                }
                            });

                            $("#homeadviceModal").modal("show");
                        },
                        error: function() {
                            alert("Could not fetch patient details");
                        }
                    });
                });

                // 🔹 Auto-fill description when selecting advice
                $(document).on("change", "#homeadviceDropdown", function() {
                    let description = $(this).find(":selected").data("description") || "";
                    $(".homeadvice-description").first().val(description);
                });

                $(document).on("change", ".homeadvice-template", function() {
                    let description = $(this).find(":selected").data("description") || "";
                    $(this).closest(".homeadvice-item").find(".homeadvice-description").val(description);
                });

                // 🔹 Submit form
                $(document).on("submit", "#homeadviceForm", function(e) {
                    e.preventDefault();
                    var branchId = localStorage.getItem('selectedBranchId');
                    if (!branchId) {
                        alert("Branch ID not found in localStorage");
                        return;
                    }

                    var patientId = $("#homeadvice_patient_id").val();
                    if (!patientId) {
                        alert("Patient ID is missing!");
                        return;
                    }

                    var homeadviceIds = [];
                    var descriptions = [];

                    // Main row
                    homeadviceIds.push($("#homeadviceDropdown").val());
                    descriptions.push($(".homeadvice-description").first().val());

                    // Dynamic rows
                    $(".homeadvice-item").each(function() {
                        homeadviceIds.push($(this).find(".homeadvice-template").val());
                        descriptions.push($(this).find(".homeadvice-description").val());
                    });

                    // Clean empty selections
                    homeadviceIds = homeadviceIds.filter(id => id);
                    descriptions = descriptions.filter(desc => desc !== undefined);

                    if (homeadviceIds.length === 0) {
                        alert("Please select at least one home advice template!");
                        return;
                    }

                    var formData = {
                        branch_id: branchId,
                        patient_id: patientId,
                        homeadvice_id: homeadviceIds,
                        description: descriptions
                    };

                    $.ajax({
                        url: "/api/patient-assign-homeadvice",
                        method: "POST",
                        data: formData,
                        success: function(response) {
                            $('#homeadviceSuccessMessage').text(response.message ||
                                'Home advice assigned successfully').show();
                            $('#homeadviceForm')[0].reset();
                            setTimeout(function() {
                                $("#homeadviceModal").modal("hide");
                                location.reload();
                            }, 1000);
                        },
                        error: function(xhr) {
                            console.log(xhr.responseJSON);
                            alert("Error: " + (xhr.responseJSON?.message || "Something went wrong"));
                        }
                    });
                });

                // 🔹 Reset select2 on modal close
                $('#homeadviceModal').on('hidden.bs.modal', function() {
                    $("#homeadviceDropdown").val(null).trigger('change');
                });
            </script>






            <!-- patient -->
            <script>
                $(document).ready(function() {

                    $("#viewReferralBtn").on("click", function() {
                        let patientId = $("#referral_source").data("id");

                        if (!patientId) {
                            $("#viewReferralBody").html("<p class='text-danger'>No referral details found.</p>");
                            $("#viewReferralModal").modal("show");
                            return;
                        }

                        $.ajax({
                            url: '/api/patient/' + patientId,
                            method: "GET",
                            success: function(response) {
                                let patient = response.patient || {};
                                let source = patient.referral_source;
                                let details = {};

                                // Parse stored JSON details if exists
                                if (patient.source_details) {
                                    if (typeof patient.source_details === "string") {
                                        try {
                                            details = JSON.parse(patient.source_details);
                                        } catch (e) {
                                            console.error("Invalid JSON in source_details:", e);
                                        }
                                    } else {
                                        details = patient.source_details;
                                    }
                                }

                                let html = "";
                                if (source === "Doctors") {
                                    let doctorId = details
                                        .referral_name; // ⚠️ make sure you stored doctor_id here

                                    if (!doctorId) {
                                        html =
                                            `<p class="text-danger">No doctor ID stored in referral details.</p>`;
                                    } else {
                                        $.ajax({
                                            url: "/api/referal_doctors/" + doctorId,
                                            method: "GET",
                                            async: false, // force to finish before showing modal
                                            success: function(doctor) {
                                                html =
                                                    `
                                                                                                                                                                                                                                                                <p><strong>Name:</strong> ${doctor.doctor_name || ''}</p>
                                                                                                                                                                                                                                                                <p><strong>Specialist:</strong> ${doctor.specialization || ''}</p>
                                                                                                                                                                                                                                                                <p><strong>Mobile:</strong> ${doctor.phone || ''}</p>
                                                                                                                                                                                                                                                                <p><strong>Email:</strong> ${doctor.email || ''}</p>
                                                                                                                                                                                                                                                            `;
                                            },
                                            error: function() {
                                                html =
                                                    `<p class="text-danger">Error fetching doctor details.</p>`;
                                            }
                                        });
                                    }
                                } else if (source === "Website") {
                                    html =
                                        `<p><strong>Website Name:</strong> ${details.website_name || ''}</p>`;
                                } else if (source === "Advertisement") {
                                    html =
                                        `
                                                                                                                                                                                                                                                                                                                                                        <p><strong>Name:</strong> ${details.advertisement_name || ''}</p>
                                                                                                                                                                                                                                                                                                                                                        <p><strong>Description:</strong> ${details.description || ''}</p>
                                                                                                                                                                                                                                                                                                                                                    `;
                                } else if (source === "Patients") {
                                    html =
                                        `<p><strong>Patient Name:</strong> ${details.patient_name || ''}</p>`;
                                } else if (source === "Insurance") {
                                    html =
                                        `
                                                                                                                                                                                                                                                                                                                                                        <p><strong>Name:</strong> ${details.referral_name || ''}</p>
                                                                                                                                                                                                                                                                                                                                                        <p><strong>Mobile:</strong> ${details.mobile || ''}</p>
                                                                                                                                                                                                                                                                                                                                                        <p><strong>Email:</strong> ${details.email || ''}</p>
                                                                                                                                                                                                                                                                                                                                                    `;
                                } else if (source === "Others") {
                                    html =
                                        `<p><strong>Other's Name:</strong> ${details.other_name || ''}</p>`;
                                } else {
                                    html = `<p class="text-danger">Details not available.</p>`;
                                }

                                $("#viewReferralBody").html(html);
                                $("#viewReferralModal").modal("show");
                            },
                            error: function() {
                                $("#viewReferralBody").html(
                                    "<p class='text-danger'>Error loading details.</p>");
                                $("#viewReferralModal").modal("show");
                            }
                        });
                    });



                    const speakButton = document.getElementById("speakButton");
                    const outputTextarea = document.getElementById("output");
                    const LANG = "en-US";

                    // Text-to-Speech (TTS)
                    $("#speakButton").on("click", function(e) {
                        e.preventDefault();
                        const text = $("#output").text().trim();
                        if (!text) {
                            Swal.fire({
                                icon: "warning",
                                title: "No note added!",
                                text: "You haven't added anything to speak.",
                            });
                            return;
                        }
                        const speech = new SpeechSynthesisUtterance(text);
                        speech.lang = "en-US";
                        window.speechSynthesis.speak(speech);
                    });
                    $.ajax({
                        url: "/api/doctors",
                        type: "GET",
                        data: {
                            branch_id: branchId
                        }, // branch filter
                        dataType: "json",
                        headers: {
                            "Authorization": "Bearer " + token
                        },
                        success: function(response) {
                            let doctorDropdown = $('.doctorSelect');
                            doctorDropdown.empty().append('<option value="">Select Doctor</option>');

                            let doctorsList = response.doctors || [];
                            $.each(doctorsList, function(index, doctor) {
                                const capitalizedName = doctor.fullname.charAt(0).toUpperCase() + doctor
                                    .fullname.slice(1);
                                doctorDropdown.append(
                                    `<option value="${doctor.id}">${capitalizedName}</option>`);
                            });

                            if (!doctorDropdown.hasClass('select2-hidden-accessible')) {
                                doctorDropdown.select2({
                                    placeholder: "Select Doctor",
                                    allowClear: true,
                                    width: '100%'
                                });
                            }

                            doctorDropdown.on('select2:open', function() {
                                $('.select2-search__field').attr('placeholder', 'Search Doctor');
                            });
                        },
                        error: function(xhr) {
                            console.error("API Error:", xhr.status, xhr.responseText);
                        }
                    });

                });


                $(document).ready(function() {

                    var patientId = "{{ $patient_id }}";

                    $.ajax({
                        url: '/api/patient/' + patientId,
                        type: 'GET',
                        dataType: 'json',
                        headers: {
                            "Authorization": "Bearer " + token
                        },
                        success: function(data) {
                            // Populate patient details

                            const notes = data.patient.note;
                            let noteHtml = '';

                            if (Array.isArray(notes) && notes.length > 0) {
                                notes.forEach(note => {
                                    if (note.type === 'speech') {
                                        noteHtml +=
                                            `<div class="note-item speech-note">
                                                                                                                                                                                                                                                                                                                                                                                                                                                    <p><i class="fas fa-comment icon-style1"></i>Note</p>
                                                                                                                                                                                                                                                                                                                                                                                                                                                    <p> ${note.content ? note.content.charAt(0).toUpperCase() + note.content.slice(1) : 'N/A'}</p>
                                                                                                                                                                                                                                                                                                                                                                                                                                                </div>`;
                                    } else if (note.type === 'audio') {
                                        noteHtml +=
                                            `<div class="note-item audio-note">
                                                                                                                                                                                                                                                                                                                                                                                                                                                    <p><i class="fas fa-comment icon-style1"></i>Audio Note</p>
                                                                                                                                                                                                                                                                                                                                                                                                                                                        <audio controls>
                                                                                                                                                                                                                                                                                                                                                                                                                                                            <source src="${note.content}" type="audio/mpeg">
                                                                                                                                                                                                                                                                                                                                                                                                                                                            Your browser does not support the audio element.
                                                                                                                                                                                                                                                                                                                                                                                                                                                        </audio>
                                                                                                                                                                                                                                                                                                                                                                                                                                                </div>`;
                                    }
                                });
                            } else {
                                noteHtml = `<p>No notes available.</p>`;
                            }

                            $('#output').html(noteHtml);



                            let symptoms = data.symptoms;
                            let symptomText = 'N/A'; // Default value

                            if (symptoms) {
                                if (typeof symptoms === 'object' && symptoms !== null && Object.keys(symptoms)
                                    .length > 0) {
                                    // If symptoms is a non-empty object, join the values
                                    symptomText = Object.values(symptoms)
                                        .map(symptom => symptom ? symptom.charAt(0).toUpperCase() + symptom
                                            .slice(1) : '')
                                        .filter(Boolean) // Remove empty strings
                                        .join(', ');
                                    symptomText = symptomText ||
                                        'N/A'; // Fallback to 'N/A' if all values are empty
                                } else if (typeof symptoms === 'string' && symptoms.trim() !== '') {
                                    // If symptoms is a non-empty string
                                    symptomText = symptoms.charAt(0).toUpperCase() + symptoms.slice(1);
                                }
                            }

                            $('#symptoms').text(symptomText);



                            const symptomRemark = data.patient.symptom_remarks;
                            const symptomImages = data.patient.symptom_images;

                            const section = $('#symptom_section');
                            section.empty();

                            const container = $(
                                '<div class="d-flex align-items-start flex-wrap gap-3"></div>'
                            ); // Flex container

                            // ✅ Render remark if exists
                            if (symptomRemark && symptomRemark.trim() !== "") {
                                const remarkEl = $(
                                    `<div class="symptom-remark" style="min-width:150px;">
                                                                                                                                                                                                                                                                                                                    <strong>Remarks:</strong> ${symptomRemark}
                                                                                                                                                                                                                                                                                                                </div>`
                                );
                                container.append(remarkEl);
                            }

                            // ✅ Render images if exist
                            if (symptomImages && symptomImages.length > 0) {
                                let imgBase = "{{ asset(env('IMAGE_PATH') ?? '/admin/assets/img/img1.png') }}";
                                const imagesContainer = $('<div class="d-flex flex-wrap gap-2"></div>');
                                symptomImages.forEach(function(imagePath) {
                                    const fullUrl =
                                        `${imgBase.replace(/\/$/, '')}/${imagePath.replace(/^\//, '')}`;
                                    imagesContainer.append(
                                        `
                                                                                                                                                                                                                                                                                                                        <img src="${fullUrl}" alt="Symptom Image"
                                                                                                                                                                                                                                                                                                                             width="45" height="45"
                                                                                                                                                                                                                                                                                                                             style="border-radius:10px;margin-right:10px; object-fit:cover;">
                                                                                                                                                                                                                                                                                                                    `
                                    );
                                });
                                container.append(imagesContainer);
                            }

                            // ✅ If neither exists, show N/A
                            if ((!symptomRemark || symptomRemark.trim() === "") && (!symptomImages ||
                                    symptomImages.length === 0)) {
                                container.append('<span>N/A</span>');
                            }

                            section.append(container);

                            function ucfirst(str) {
                                return str ? str.charAt(0).toUpperCase() + str.slice(1) : "N/A";
                            }

                            $('.Patient_name').text(data.patient.fullname ? data.patient.fullname.charAt(0)
                                .toUpperCase() + data.patient.fullname.slice(1) : "N/A");
                            $('#email').text(data.patient.email ? data.patient.email : "N/A");
                            $('#diagnosis').text(
                                Array.isArray(data.patient.diagnosis_names) && data.patient.diagnosis_names
                                .length > 0 ?
                                data.patient.diagnosis_names.join(', ') :
                                "N/A"
                            );


                            $('#phone').text(data.patient.phone ? data.patient.phone : "N/A");
                            $('#birthdate').text(data.patient.birthdate ? data.patient.birthdate : "N/A");
                            $('#type').text(data.patient.patient_type ? ucfirst(data.patient.patient_type) :
                                "N/A");

                            // Inside your AJAX success callback
                            $('#referral_source')
                                .text(data.patient.referral_source ? data.patient.referral_source : "N/A")
                                .attr("data-id", data.patient.id); // Set the patient ID for the referral button

                            $('#address').text(data.patient.address ? data.patient.address.charAt(0)
                                .toUpperCase() +
                                data.patient.address.slice(1) : "N/A");
                            $('#city').text(data.patient.city ? data.patient.city.charAt(0).toUpperCase() + data
                                .patient.city.slice(1) : "N/A");
                            $('#state').text(data.patient.state ? data.patient.state.charAt(0).toUpperCase() +
                                data
                                .patient.state.slice(1) : "N/A");
                            $('#age').text(data.patient.age ? data.patient.age : "N/A");
                            $('#bloodgroup').text(data.patient.blood_group ? data.patient.blood_group : "N/A");
                            $('#medical_history').text(data.patient.medical_history ? data.patient
                                .medical_history
                                .charAt(0).toUpperCase() + data.patient.medical_history.slice(1) : "N/A");
                            $(".edit-patient-btn").attr("href", "/patient/edit/" + patientId);

                            if (data.patient.profile) {
                                $('#Patient_image').attr('src', data.patient.profile);
                            }



                            var followupHtml = "";
                            if (data.followups.length > 0) {
                                data.followups.forEach(function(followup) {
                                    followupHtml +=
                                        `
                                                                                                                                                                                                                                                                <tr>
                                                                                                                                                                                                                                                                    <td>
                                                                                                                                                                                                                                                                        ${followup.followup_doctor
                                            ? `<img src="${followup.followup_doctor.profile}" alt="Doctor Image" style="width:30px; height:30px; border-radius:50%; object-fit:cover; margin-right:5px;">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ${followup.followup_doctor.fullname.charAt(0).toUpperCase() + followup.followup_doctor.fullname.slice(1)}`
                                            : "N/A"}
                                                                                                                                                                                                                                                                    </td>
                                                                                                                                                                                                                                                                    <td>${followup.date || "N/A"}</td>
                                                                                                                                                                                                    <td>
                                                                                                                                                                                                        <div class="icon" style="cursor:pointer">

                                                                                                                                                                                                            @if (app('hasPermission')(2, 'update')) 
                                                                                                                                                                                                                <i class="fa fa-pencil m-r-5 icon1 edit-followup" data-id="${followup.id ?? ''}" title="Edit"></i> 
                                                                                                                                                                                                            @endif
                                                                                                                                                                                                            @if (app('hasPermission')(2, 'delete')) 
                                                                                                                                                                                                                <i class="fa fa-trash-o m-r-5 icon2 delete-followup" data-id="${followup.id ?? ''}" title="Delete"></i> 
                                                                                                                                                                                                            @endif

                                                                                                                                                                                                        </div>
                                                                                                                                                                                                    </td>
                                                                                                                                                                                                                                                                </tr>`;

                                    function ucfirst(str) {
                                        return str.charAt(0).toUpperCase() + str.slice(1);
                                    }

                                });

                                document.getElementById("followupRecords").innerHTML = followupHtml;
                                document.getElementById("followupTable").style.display = "table";
                                document.getElementById("no-followup-message").style.display = "none";

                                $('#followupTable').DataTable({
                                    "responsive": true,
                                    "paging": true,
                                    "searching": true,
                                    "ordering": true
                                });

                            } else {
                                document.getElementById("followupTable").style.display = "none";
                                document.getElementById("no-followup-message").style.display = "block";
                            }







                            //appointment

                            var appointmentHtml = "";
                            var appointments = data.appointments;

                            // Destroy existing DataTable if initialized
                            if ($.fn.DataTable.isDataTable("#appointmentTable")) {
                                $("#appointmentTable").DataTable().clear().destroy();
                            }

                            if (appointments.length > 0) {
                                appointments.forEach(function(appointment) {
                                    let appointmentStatus = appointment.status ? appointment.status
                                        .toLowerCase() : 'unknown';

                                    let statusClass = {
                                        'upcoming': 'upcoming',
                                        'confirmed': 'confirmed',
                                        'completed': 'completed', // ✅ corrected
                                        'cancelled': 'cancelled',
                                        'follow-up': 'follow-up'
                                    } [appointmentStatus] || 'btn-secondary';

                                    let statusButton =
                                        `<span class="custom-badge btn ${statusClass} btn-rounded">
                                                                                                                                                                                                                                                                    ${appointmentStatus.charAt(0).toUpperCase() + appointmentStatus.slice(1)}
                                                                                                                                                                                                                                                                </span>`;

                                    appointmentHtml +=
                                        `
                                                                                                                                                                                                                                                                <tr>
                                                                                                                                                                                                                                                                    <td>
                                                                                                                                                                                                                                                                        ${appointment.appointment_doctor
                                            ? `<img src="${appointment.appointment_doctor.profile}" alt="Doctor Image" style="width:30px; height:30px; border-radius:50%; object-fit:cover; margin-right:5px;">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    ${ucfirst(appointment.appointment_doctor.fullname)}`
                                            : "N/A"}
                                                                                                                                                                                                                                                                    </td>
                                                                                                                                                                                                                                                                    <td>${appointment.treatment ? ucfirst(appointment.treatment.name) : "N/A"}</td>
                                                                                                                                                                                                                                                                    <td>${appointment.date ? ucfirst(appointment.date) : "N/A"}</td>
                                                                                                                                                                                                                                                                    <td>${appointment.appoint_type ? ucfirst(appointment.appoint_type) : "N/A"}</td>
                                                                                                                                                                                                                                                                    <td>${statusButton}</td>
                                                                                                                                                                                                                                                                 <td>
                                                                                                                                                                                                        <div class="icon" style="cursor:pointer">

                                                                                                                                                                                                            @if (app('hasPermission')(6, 'update')) 
                                                                                                                                                                                                                <i class="fa fa-pencil m-r-5 icon1 edit-appointment" data-id="${appointment.id ?? ''}" title="Edit"></i> 
                                                                                                                                                                                                            @endif
                                                                                                                                                                                                             @if (app('hasPermission')(6, 'delete'))
                                                                                                                                                                                                                <i class="fa fa-trash-o m-r-5 icon2 delete-appointment" style="cursor:pointer" data-id="${appointment.id}"  title="Delete "></i>
                                                                                                                                                                                                            @endif

                                                                                                                                                                                                        </div>
                                                                                                                                                                                                    </td>
                                                                                                                                                                                                                                                                    </tr>`;

                                    function ucfirst(str) {
                                        return str.charAt(0).toUpperCase() + str.slice(1);
                                    }
                                });

                                $("#appointmentRecords").html(appointmentHtml);
                                $("#appointmentTable").show();
                                $("#no-appointment-message").hide();

                                // Re-initialize DataTable
                                $('#appointmentTable').DataTable({
                                    "responsive": true,
                                    "paging": true,
                                    "searching": true,
                                    "ordering": true,
                                    "destroy": true // ✅ Important: destroy previous DataTable instance if any
                                });
                            } else {
                                $("#appointmentRecords").empty();
                                $("#appointmentTable").hide();
                                $("#no-appointment-message").show();
                            }


                        },
                        error: function() {
                            alert('Failed to fetch patient details.');
                        }
                    });


                    $(document).on('click', '.download-patient-history', function() {
                        const patientId = $(this).data('id');

                        // Redirect to your backend route that calls patientHistoryPdf
                        window.location.href = `/api/patients/${patientId}/history-pdf`;
                    });


                    $(document).on('click', '.edit-patient-btn', function(e) {
                        e.preventDefault();
                        var patientId = $(this).data('id');
                        window.location.href = '/patient/edit/' + patientId;
                    });

                    $(document).on('click', '.delete-patient', function() {
                        var patientId = $(this).data('id');
                        if (!patientId) {
                            Swal.fire('Error', 'Patient ID not found!', 'error');
                            return;
                        }
                        Swal.fire({
                            title: 'Are you sure?',
                            text: "You won't be able to revert this!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#cfece0',
                            cancelButtonColor: '#f89884',
                            confirmButtonText: 'Yes, delete it!',
                            cancelButtonText: 'Cancel',
                            customClass: {
                                confirmButton: 'swal-confirm-btn',
                                cancelButton: 'swal-cancel-btn'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $.ajax({
                                    url: '/api/patient/' + patientId,
                                    type: 'DELETE',
                                    success: function(response) {
                                        Swal.fire({
                                            title: 'Deleted!',
                                            text: 'Patient deleted successfully!',
                                            icon: 'success',
                                            timer: 1500,
                                            showConfirmButton: false
                                        }).then(() => {
                                            window.location.href = '/patients';
                                        });
                                    },
                                    error: function(xhr) {
                                        Swal.fire('Error', xhr.responseJSON?.message ||
                                            'Failed to delete patient. Please try again.',
                                            'error');
                                    }
                                });
                            }
                        });
                    });


                    // payment
                    $('#payments-tab').on('shown.bs.tab', function() {
                        if ($('#paymentRecords').children().length === 0) {
                            $.ajax({
                                url: '/api/daily-data/payment-history/' + patientId,
                                type: 'GET',
                                dataType: 'json',
                                success: function(data) {
                                    let recordsHtml = '';
                                    if (data.history && data.history.length > 0) {
                                        data.history.forEach(function(payment) {
                                            recordsHtml +=
                                                `
                                                                                                                                                                                                                <tr>
                                                                                                                                                                                                                    <td>
                                                                                                                                                                                                                    ${payment.created_at ? new Date(payment.created_at).toLocaleDateString('en-US', {
                                                    year: 'numeric',
                                                    month: 'short',
                                                    day: 'numeric'
                                                }) : 'N/A'}
                                                                                                                                                                                                                            </td>
                                                                                                                                                                                                                            <td>${payment.amount || '0'}</td>
                                                                                                                                                                                                                            <td>${payment.paid_amount || '0'}</td>
                                                                                                                                                                                                                            <td>${payment.remain_amount || '0'}</td>
                                                                                                                                                                                                                            <td>${payment.collected_by || 'N/A'}</td>
                                                                                                                                                                                                                              <td>
                                                                                                                                                                                                                                <div class="icon">
                                                                                                                                                                                                                                    @if (app('hasPermission')(30, 'update'))
                                                                                                                                                                                                                                        <i class="fa fa-pencil m-r-5 icon1 edit-daily_data" style="cursor:pointer" data-id="${payment.id}"  title="Edit Daily register"></i>
                                                                                                                                                                                                                                    @endif
                                                                                                                                                                                                                                     @if (app('hasPermission')(30, 'delete'))
                                                                                                                                                                                                                                        <i class="fa fa-trash-o m-r-5 icon2 delete-daily_data" style="cursor:pointer" data-id="${payment.id}"  title="Delete Daily register"></i>
                                                                                                                                                                                                                                    @endif
                                                                                                                                                                                                                                       </div>
                                                                                                                                                                                                                            </td>

                                                                                                                                                                                                                                </tr>
                                                                                                                                                                                                                            `;
                                        });

                                        $('#paymentRecords').html(recordsHtml);
                                        $('#paymentTable').show();
                                        $('#no-payment-message').hide();

                                        if (!$.fn.DataTable.isDataTable("#paymentTable")) {
                                            $('#paymentTable').DataTable({
                                                responsive: true,
                                                paging: true,
                                                searching: true,
                                                ordering: true
                                            });
                                        }
                                    } else {
                                        $('#paymentRecords').empty();
                                        $('#paymentTable').hide();
                                        $('#no-payment-message').show();
                                    }
                                },
                                error: function() {
                                    $('#paymentRecords').empty();
                                    $('#paymentTable').hide();
                                    $('#no-payment-message').text('Failed to load payment history.')
                                        .show();
                                }
                            });
                        }
                    });



                    $('#record-tab').on('shown.bs.tab', function() {
                        if ($('#reportRecords').children().length === 0) {
                            // Assuming patientId is already defined
                            $.ajax({
                                url: `/api/patient/${patientId}/medical-reports`, // Create this route
                                type: "GET",
                                dataType: "json",
                                success: function(response) {
                                    const reportRecords = $("#reportRecords");
                                    reportRecords.empty();

                                    if (response.length > 0) {
                                        response.forEach(report => {
                                            const row =
                                                `
                                                                                                                                                                                                                <tr>
                                                                                                                                                                                                                    <td>${report.date ? report.date.split('T')[0] : 'N/A'}</td>
                                                                                                                                                                                                                    <td>${report.report_type || 'N/A'}</td>
                                                                                                                                                                                                                    <td>${report.description || 'N/A'}</td>
                                                                                                                                                                                                                    <td>
                                                                                                                                                                                                                        <a href="${report.file_path}" target="_blank" class="btn btn-primary btn-sm">
                                                                                                                                                                                                                            <i class="fas fa-file-alt"></i>
                                                                                                                                                                                                                        </a>
                                                                                                                                                                                                                    </td>
                                                                                                                                                                                                                    <td>
                                                                                                                                                                                            <div class="icon" style="cursor:pointer">

                                                                                                                                                                                                @if (app('hasPermission')(13, 'update')) 
                                                                                                                                                                                                    <i class="fa fa-pencil m-r-5 icon1 edit-report" data-id="${report.id}" title="Edit"></i> 
                                                                                                                                                                                                @endif
                                                                                                                                                                                                  @if (app('hasPermission')(13, 'delete')) 
                                                                                                                                                                                                    <i class="fa fa-trash-o m-r-5 icon2 delete-report" data-id="${report.id}" title="Delete"></i> 
                                                                                                                                                                                                @endif

                                                                                                                                                                                            </div>
                                                                                                                                                                                        </td>
                                                                                                                                                                                                                </tr>`;
                                            reportRecords.append(row);
                                        });
                                        $("#reportTable").show();
                                        $("#no-report-message").hide();

                                        // Initialize DataTable
                                        if ($.fn.DataTable.isDataTable("#reportTable")) {
                                            $('#reportTable').DataTable().destroy();
                                        }
                                        $('#reportTable').DataTable({
                                            responsive: true,
                                            paging: true,
                                            searching: true,
                                            ordering: true
                                        });
                                    } else {
                                        $("#reportTable").hide();
                                        $("#no-report-message").show();
                                    }


                                },
                                error: function(err) {
                                    console.error("Failed to fetch report history", err);
                                }
                            });
                        }
                    });


                    $('#treatment-tab').on('shown.bs.tab', function() {

                        if ($('#treatmentRecords').children().length === 0) {
                            $.ajax({
                                url: `/api/patient/treatment-bookings/${patientId}`,
                                type: "GET",
                                dataType: "json",
                                success: function(response) {
                                    const treatmentRecords = $("#treatmentRecords");
                                    treatmentRecords.empty();
                                    if (response.length > 0) {
                                        response.forEach(record => {
                                            const row =
                                                `
                                                                                <tr>
                                                                                    <td>${record.patient_name || 'N/A'}</td>
                                                                                    <td>${record.treatment_name || 'N/A'}</td>
                                                                                    <td>${record.machine_names && record.machine_names.length ? record.machine_names.join(', ') : 'N/A'}</td>
                                                                                    <td>${record.plan || 'N/A'}</td>
                                                                                    <td>${record.payment_date ? record.payment_date.split('T')[0] : 'N/A'}</td>
                                                                                    <td>${record.status || 'N/A'}</td>
                                                                                    <td>
                                                                                        <div class="icon" style="cursor:pointer">
                                                                                            @if (app('hasPermission')(7, 'update')) 
                                                                                                <i class="fa fa-pencil m-r-5 icon1 edit-treatmentbooking" data-id="${record.id}" title="Edit"></i> 
                                                                                            @endif
                                                                                            @if (app('hasPermission')(7, 'delete')) 
                                                                                                <i class="fa fa-trash-o m-r-5 icon2 delete-treatment-booking" data-id="${record.id}" title="delete"></i> 
                                                                                            @endif
                                                                                        </div>
                                                                                    </td>
                                                                                </tr>`;
                                            treatmentRecords.append(row);
                                        });
                                        $("#treatmentTable").show();
                                        $("#no-treatment-message").hide();

                                        if ($.fn.DataTable.isDataTable("#treatmentTable")) {
                                            $('#treatmentTable').DataTable().destroy();
                                        }
                                        $('#treatmentTable').DataTable({
                                            responsive: true,
                                            paging: true,
                                            searching: true,
                                            ordering: true
                                        });
                                    } else {
                                        $("#treatmentTable").hide();
                                        $("#no-treatment-message").show();
                                    }

                                },
                                error: function(err) {
                                    console.error("Failed to fetch treatment history", err);
                                }
                            });
                        }
                    });


                    $('#assessment-tab').on('shown.bs.tab', function() {
                        if ($('#assessmentRecords').children().length === 0) {
                            $.ajax({
                                url: `/api/patient/${patientId}/assessments`,
                                type: "GET",
                                dataType: "json",
                                success: function(response) {
                                    const assessmentRecords = $("#assessmentRecords");
                                    assessmentRecords.empty();
                                    if (response.assessments && response.assessments.length > 0) {
                                        console.log(response.assessments);

                                        response.assessments.forEach(record => {
                                            record.templates.forEach((template, index) => {
                                                const description = (typeof record
                                                        .description === 'object' &&
                                                        record.description[index]) ?
                                                    record.description[index] : 'N/A';
                                                const row = `
                                                                                                                <tr>

                                                                                                                    <td>${template.name ? capitalize(template.name) : 'N/A'}</td>
                                                                                                                    <td>${description ? capitalize(description) : 'N/A'}</td>
                                                                                                                    <td>
                                                                                                                        <div class="icon" style="cursor:pointer">
                                                                                                                            <i class="fa fa-trash-o m-r-5 icon2 delete-patientassesment" data-id="${record.id ?? ''}" title="Delete"></i>
                                                                                                                        </div>
                                                                                                                    </td>
                                                                                                                </tr>`;
                                                assessmentRecords.append(row);
                                            });
                                        });
                                        $("#assessmentTable").show();
                                        $("#no-assessment-message").hide();
                                        if ($.fn.DataTable.isDataTable("#assessmentTable")) {
                                            $('#assessmentTable').DataTable().destroy();
                                        }
                                        $('#assessmentTable').DataTable({
                                            responsive: true,
                                            paging: true,
                                            searching: true,
                                            ordering: true
                                        });
                                    } else {
                                        $("#assessmentTable").hide();
                                        $("#no-assessment-message").show();
                                    }
                                },
                                error: function(err) {
                                    console.error("Failed to fetch assessment history", err);
                                }
                            });
                        }
                    });





                    function capitalize(str) {
                        if (typeof str !== "string" || !str.length) return "";
                        return str.charAt(0).toUpperCase() + str.slice(1);
                    }
                    //Diet-Chart

                    $('#diet-chart-tab').on('shown.bs.tab', function() {
                        if ($('#dietRecords').children().length === 0) {
                            $.ajax({
                                url: `/api/patient/${patientId}/diet-charts`,
                                type: "GET",
                                dataType: "json",
                                success: function(response) {
                                    console.log("API Response:", response);

                                    const dietRecords = $("#dietRecords");
                                    dietRecords.empty();

                                    if (response.dietCharts && response.dietCharts.length > 0) {
                                        response.dietCharts.forEach(record => {
                                            record.templates.forEach(template => {
                                                const description = Array.isArray(
                                                        template.description) &&
                                                    template.description.length > 0 ?
                                                    template.description[0] :
                                                    'N/A';

                                                const row = `
                                <tr>
                                    <td>${template.name ? capitalize(template.name) : 'N/A'}</td>
                                    <td>${description ? capitalize(description) : 'N/A'}</td>
                                    <td>
                                        <div class="icon" style="cursor:pointer">
                                             <i class="fa fa-download m-r-5 icon2 download-assessment" data-id="${record.diet_template_id ?? ''}" title="Download PDF"></i>
                                             <i class="fa fa-trash-o m-r-5 icon2 delete-patientdiet" data-id="${record.id}" title="Delete"></i>
                                        </div>
                                        
                                     

                                        
                                    </td>
                                </tr>`;
                                                dietRecords.append(row);
                                            });
                                        });

                                        $("#dietTable").show();
                                        $("#no-dietChart-message").hide();

                                        if ($.fn.DataTable.isDataTable("#dietTable")) {
                                            $('#dietTable').DataTable().destroy();
                                        }
                                        $('#dietTable').DataTable({
                                            responsive: true,
                                            paging: true,
                                            searching: true,
                                            ordering: true
                                        });
                                    } else {
                                        $("#dietTable").hide();
                                        $("#no-dietChart-message").show();
                                    }
                                },
                                error: function(err) {
                                    console.error("Failed to fetch diet charts", err);
                                }
                            });
                        }
                        // $(document).on('click', '.download-diet', function() {
                        //     const dietId = $(this).data('id');
                        //     window.location.href =
                        //         `/api/patient/${patientId}/diet-charts/${dietId}/download`;
                        // });
                        // Diet Chart PDF download
                        $(document).off('click', '.download-assessment').on('click', '.download-assessment',
                            function() {
                                const dietId = $(this).data('id');


                                const url = `/api/dietchart/download/${dietId}`;

                                window.open(url, '_blank');
                            });




                    });

                    // homeavice


                    $('#homeadvice-tab').on('shown.bs.tab', function() {
                        if ($('#homeadviceRecords').children().length === 0) {
                            $.ajax({
                                url: `/api/patient/${patientId}/homeadvices`,
                                type: "GET",
                                dataType: "json",
                                success: function(response) {
                                    const homeadviceRecords = $("#homeadviceRecords");
                                    homeadviceRecords.empty();
                                    if (response.homeadvices && response.homeadvices.length > 0) {
                                        response.homeadvices.forEach(record => {
                                            record.templates.forEach((template, index) => {
                                                const description = (typeof record
                                                        .description === 'object' &&
                                                        record.description[index]) ?
                                                    record.description[index] : 'N/A';
                                                const row = `
                                                                                                            <tr>

                                                                                                                <td>${template.template_name ? capitalize(template.template_name) : 'N/A'}</td>
                                                                                                                <td>${description ? capitalize(description) : 'N/A'}</td>
                                                                                                                <td>
                                                                                                                    <div class="icon" style="cursor:pointer">
                                                                                                                        <i class="fa fa-trash-o m-r-5 icon2 delete-patienthome" data-id="${record.id ?? ''}" title="Delete"></i>
                                                                                                                    </div>
                                                                                                                </td>
                                                                                                            </tr>`;
                                                homeadviceRecords.append(row);
                                            });
                                        });
                                        $("#homeadviceTable").show();
                                        $("#no-homeadvice-message").hide();
                                        // Reset datatable if already initialized
                                        if ($.fn.DataTable.isDataTable("#homeadviceTable")) {
                                            $('#homeadviceTable').DataTable().destroy();
                                        }
                                        $('#homeadviceTable').DataTable({
                                            responsive: true,
                                            paging: true,
                                            searching: true,
                                            ordering: true
                                        });
                                    } else {
                                        $("#homeadviceTable").hide();
                                        $("#no-homeadvice-message").show();
                                    }
                                },
                                error: function(err) {
                                    console.error("Failed to fetch homeadvice history", err);
                                }
                            });
                        }
                    });



                });



                $(document).on('click', '.edit-followup', function() {
                    var followupId = $(this).data('id');
                    window.location.href = '/followup/edit/' + followupId;
                });
                $(document).on('click', '.edit-appointment', function() {
                    var appointmentId = $(this).data('id');
                    window.location.href = '/appointment/edit/' + appointmentId;
                });

                $(document).on('click', '.edit-daily_data', function() {
                    let id = $(this).data('id');
                    window.location.href = `/daily_data/edit/${id}`;
                });


                // Edit Report
                $(document).on("click", ".edit-report", function() {
                    let reportId = $(this).data("id");
                    window.location.href = '/report/edit/' + reportId;
                });



                $(document).on('click', '.edit-treatmentbooking', function() {
                    var treatmentId = $(this).data('id');
                    window.location.href = '/treatment_booking/edit/' + treatmentId;
                });
            </script>


            <!-- delete assesment -->
            <script>
                $(document).on('click', '.delete-patientassesment', function() {
                    var patientassesment = $(this).data('id');
                    if (!patientassesment) {
                        Swal.fire('Error', 'patient assesment not found!', 'error');
                        return;
                    }

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#cfece0',
                        cancelButtonColor: '#f89884',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel',
                        customClass: {
                            confirmButton: 'swal-confirm-btn',
                            cancelButton: 'swal-cancel-btn'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '/api/patient-assessments/' + patientassesment,
                                type: 'DELETE',
                                success: function(response) {
                                    Swal.fire({
                                        title: 'Deleted!',
                                        text: 'The assessment has been deleted.',
                                        icon: 'success',
                                        timer: 1500,
                                        showConfirmButton: false
                                    }).then(() => {
                                        location.reload();
                                    });
                                },
                                error: function(xhr) {
                                    console.log(xhr.responseText);
                                    Swal.fire('Error', 'Failed to delete the appointment.',
                                        'error');
                                }
                            });
                        }
                    });
                });

                // delete diet-chart

                $(document).on('click', '.delete-patientdiet', function() {
                    var dietId = $(this).data('id'); // Get diet chart ID
                    if (!dietId) {
                        Swal.fire('Error', 'Diet chart not found!', 'error');
                        return;
                    }

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#cfece0',
                        cancelButtonColor: '#f89884',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel',
                        customClass: {
                            confirmButton: 'swal-confirm-btn',
                            cancelButton: 'swal-cancel-btn'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '/api/patient-assign-dietchart/' + dietId, // <-- Correct URL
                                type: 'DELETE',
                                success: function(response) {
                                    Swal.fire({
                                        title: 'Deleted!',
                                        text: response.message,
                                        icon: 'success',
                                        timer: 1500,
                                        showConfirmButton: false
                                    }).then(() => {
                                        // Remove the deleted row from the table without reloading
                                        $(`.delete-patientdiet[data-id="${dietId}"]`).closest(
                                            'tr').remove();

                                        // If table is empty, show the "no diet chart" message
                                        if ($('#dietRecords').children().length === 0) {
                                            $("#dietTable").hide();
                                            $("#no-dietChart-message").show();
                                        }
                                    });
                                },
                                error: function(xhr) {
                                    console.log(xhr.responseText);
                                    Swal.fire('Error', 'Failed to delete the diet chart.', 'error');
                                }
                            });
                        }
                    });
                });

                // <!-- delete homeadvice -->

                $(document).on('click', '.delete-patienthome', function() {
                    var patienthome = $(this).data('id');
                    if (!patienthome) {
                        Swal.fire('Error', 'patient homeadvice not found!', 'error');
                        return;
                    }

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#cfece0',
                        cancelButtonColor: '#f89884',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel',
                        customClass: {
                            confirmButton: 'swal-confirm-btn',
                            cancelButton: 'swal-cancel-btn'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '/api/patient-patienthome/' + patienthome,
                                type: 'DELETE',
                                success: function(response) {
                                    Swal.fire({
                                        title: 'Deleted!',
                                        text: 'The Home advice has been deleted.',
                                        icon: 'success',
                                        timer: 1500,
                                        showConfirmButton: false
                                    }).then(() => {
                                        location.reload();
                                    });
                                },
                                error: function(xhr) {
                                    console.log(xhr.responseText);
                                    Swal.fire('Error', 'Failed to delete the appointment.',
                                        'error');
                                }
                            });
                        }
                    });
                });


                // delete treatment

                $(document).on('click', '.delete-treatment-booking', function() {
                    var treatmentId = $(this).data('id');
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You want to delete treatment booking!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#cfece0',
                        cancelButtonColor: '#f89884',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel',
                        customClass: {
                            confirmButton: 'swal-confirm-btn',
                            cancelButton: 'swal-cancel-btn'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '/api/treatment_booking/' + treatmentId,
                                type: 'DELETE',
                                headers: {
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                },

                                success: function(response) {
                                    Swal.fire({
                                        title: 'Deleted!',
                                        text: 'Treatment Booking has been deleted.',
                                        icon: 'success',
                                        timer: 1500,
                                        showConfirmButton: false
                                    }).then(() => {
                                        location.reload();
                                    });
                                },
                                error: function(xhr) {
                                    Swal.fire('Error', xhr.responseJSON?.message ||
                                        'Failed to delete treatment. Please try again.',
                                        'error');
                                }
                            });
                        }
                    });
                });


                // delete report

                $(document).on("click", ".delete-report", function() {
                    let reportId = $(this).data("id");

                    if (!reportId) {
                        Swal.fire('Error', 'Report ID not found!', 'error');
                        return;
                    }

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This action cannot be undone!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#cfece0', // background for Yes
                        cancelButtonColor: '#f89884', // background for Cancel
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel',
                        customClass: {
                            confirmButton: 'swal-confirm-btn', // ✅ custom class
                            cancelButton: 'swal-cancel-btn'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "{{ url('/api/medical-reports') }}/" + reportId,
                                type: "DELETE",
                                success: function() {
                                    Swal.fire({
                                        title: 'Deleted!',
                                        text: 'Report deleted successfully!',
                                        icon: 'success',
                                        timer: 1500,
                                        showConfirmButton: false
                                    }).then(() => {
                                        location.reload();
                                    });
                                },
                                error: function(xhr) {
                                    Swal.fire('Error',
                                        'An error occurred while deleting the report.',
                                        'error');
                                    console.error(xhr.responseText);
                                }
                            });
                        }
                    });
                });
                // Delete payment
                $(document).on('click', '.delete-daily_data', function() {
                    let id = $(this).data('id');

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "Delete this daily entry?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel',
                        customClass: {
                            popup: 'custom-swal-popup',
                            title: 'custom-swal-title',
                            htmlContainer: 'custom-swal-text',
                            confirmButton: 'swal-confirm-btn',
                            cancelButton: 'swal-cancel-btn'
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: `/api/daily-data/${id}`,
                                type: 'DELETE',
                                success: function(res) {
                                    Swal.fire({
                                        title: 'Deleted!',
                                        text: 'Payment report deleted successfully!',
                                        icon: 'success',
                                        timer: 1500,
                                        showConfirmButton: false
                                    }).then(() => {
                                        location.reload();
                                    });
                                },
                                error: function(xhr) {
                                    Swal.fire('Error', 'Failed to delete entry.', 'error');
                                }
                            });
                        }
                    });
                });

                //appointment
                $(document).on('click', '.delete-appointment', function() {
                    var appointmentId = $(this).data('id');
                    if (!appointmentId) {
                        Swal.fire('Error', 'Appointment ID not found!', 'error');
                        return;
                    }

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#cfece0', // background for Yes
                        cancelButtonColor: '#f89884', // background for Cancel
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel',
                        customClass: {
                            confirmButton: 'swal-confirm-btn', // ✅ custom class
                            cancelButton: 'swal-cancel-btn'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '/api/appointments/' + appointmentId,
                                type: 'DELETE',
                                success: function(response) {
                                    Swal.fire({
                                        title: 'Deleted!',
                                        text: 'The appointment has been deleted.',
                                        icon: 'success',
                                        timer: 1500,
                                        showConfirmButton: false
                                    }).then(() => {
                                        location.reload();
                                    });
                                },
                                error: function(xhr) {
                                    console.log(xhr.responseText);
                                    Swal.fire('Error', 'Failed to delete the appointment.',
                                        'error');
                                }
                            });
                        }
                    });
                });
                //followup
                $(document).on('click', '.delete-followup', function() {
                    var followupId = $(this).data('id');
                    if (!followupId) {
                        Swal.fire('Error', 'Followup ID not found!', 'error');
                        return;
                    }

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#cfece0',
                        cancelButtonColor: '#f89884',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel',
                        customClass: {
                            confirmButton: 'swal-confirm-btn',
                            cancelButton: 'swal-cancel-btn'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '/api/followup/' + followupId,
                                type: 'DELETE',
                                success: function(response) {
                                    Swal.fire({
                                        title: 'Deleted!',
                                        text: 'The Followup has been deleted.',
                                        icon: 'success',
                                        timer: 1500,
                                        showConfirmButton: false
                                    }).then(() => {
                                        location.reload();
                                    });
                                },
                                error: function(xhr) {
                                    console.log(xhr.responseText);
                                    Swal.fire('Error', 'Failed to delete the appointment.',
                                        'error');
                                }
                            });
                        }
                    });
                });
            </script>
        @endsection
        ``

