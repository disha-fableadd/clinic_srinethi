@extends('layout.app')
<style>
    .nav-tabs .nav-link:focus,
    .nav-tabs .nav-link:hover {
        background-color: transparent !important;
        border-color: #dee2e6 #dee2e6 #fff !important;
        color: black;
    }

    .nav-tabs .nav-link:focus,
    .nav-tabs .nav-link {
        /* background-color:; */
        border-color: transparent;
        color: rgb(207 236 224);
        ;
    }

    .form-control {
        border-color: rgb(207 236 224);
        !important;
    }

    .title {
        margin-top: 5px;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 24px;
    }

    .switch input {
        display: none;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 24px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked+.slider {
        background-color: #28a745;
    }

    input:checked+.slider:before {
        transform: translateX(26px);
    }

    div:where(.swal2-container) button:where(.swal2-styled):where(.swal2-confirm) {
        border-radius: 50px !important;
        background: initial;
        background-color: #f89884 !important;
        box-shadow: var(--swal2-confirm-button-box-shadow);
        color: var(--swal2-confirm-button-color);
        font-size: 1em;
        width: 200px !important;
    }
</style>
<!-- Select2 CSS -->
<style>
    .form-group label {
        display: block;
        width: 100%;
        margin-bottom: 5px;
        font-weight: 600;
    }

    /* Make the Select2 dropdown 100% width */
    .select2-container {
        width: 100% !important;
    }

    /* Apply styling to Select2 multiple selection */
    .select2-container--default .select2-selection--multiple {
        border-radius: 50px !important;
        border: 1px solid rgb(207 236 224) !important;
        min-height: 40px !important;
        padding: 5px;
        font-size: 14px;
        background-color: #fff;
        width: 100%;
        transition: border-color 0.3s ease-in-out;
    }

    /* Ensure the Select2 input field appears full-width */
    .select2-container--default .select2-selection--single {
        width: 100% !important;
    }

    /* Focus effect */
    .select2-container--default .select2-selection--multiple:focus,
    .select2-container--default .select2-selection--single:focus {
        border-color: rgb(207 236 224) !important;
        box-shadow: 0 0 5px rgb(207 236 224) !important;
        outline: none;
    }

    /* Style selected tags */
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        background-color: rgb(207 236 224) !important;
        color: black !important;
        border-radius: 20px;
        /* padding: 2px 10px !important; */
        border: none;
        font-size: 14px;
        font-weight: 500;
        text-align: center;
        max-width: fit-content;
    }


    /* Style dropdown items */
    .select2-container--default .select2-results__option {
        padding: 10px;
        font-size: 14px;
    }

    /* Highlight hovered item */
    .select2-container--default .select2-results__option--highlighted {
        background-color: rgb(207 236 224) !important;
        color: black !important;
    }
</style>

@section('content')
    <div class="page-wrapper">
        <div class="content" style="height:100vh">
            <div class="row" style="padding-top:15px">

            </div>

            <div class="row mt-2">
                <div class="col-md-12">

                    <div class="card">

                        <div class="card-header" style="background-color:#f89884;">
                            <h3 class="card-title d-inline-block text-white title"><i class="fas fa-sms px-2"
                                    style="font-size:20px"></i>Settings</h3>

                        </div>

                        <ul class="nav nav-tabs mt-5 mb-3" id="smsTabs" role="tablist" style="padding: 0 20px;">

                            <li class="nav-item">
                                <a class="nav-link active" id="credentials-tab" data-toggle="tab" href="#credentials"
                                    role="tab" aria-controls="credentials" aria-selected="false"
                                    style="text-decoration:none;color:black !important;">
                                    <i class="fas fa-cogs px-2" style="font-size:20px;"></i> SMS Credentials
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="whatsapp-tab" data-toggle="tab" href="#whatsapp" role="tab"
                                    aria-controls="whatsapp" aria-selected="false"
                                    style="text-decoration:none;color:black !important;">
                                    <i class="fab fa-whatsapp px-2" style="font-size:20px;color:black;"></i>WhatsApp
                                    Credentials
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="smtp-credentials-tab" data-toggle="tab" href="#smtp-credentials"
                                    role="tab" aria-controls="smtp-credentials" aria-selected="false"
                                    style="text-decoration:none;color:black !important;">
                                    <i class="fas fa-key px-2" style="font-size:20px"></i> SMTP Credentials
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="firebase-credentials-tab" data-toggle="tab"
                                    href="#firebase-credentials" role="tab" aria-controls="firebase-credentials"
                                    aria-selected="false" style="text-decoration:none;color:black !important;">
                                    <i class="fas fa-fire px-2" style="font-size:20px;"></i> Firebase Credentials
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="razorpay-credentials-tab" data-toggle="tab"
                                    href="#razorpay-credentials" role="tab" aria-controls="razorpay-credentials"
                                    aria-selected="false" style="text-decoration:none;color:black !important;">
                                    <i class="fas fa-credit-card px-2" style="font-size:20px;"></i>
                                    Razorpay Credentials
                                </a>
                            </li>

                        </ul>



                        <div class="card-body">
                            <div class="tab-content" id="smsTabsContent">
                                <!-- Template Tab -->

                                <!-- Credentials Tab -->
                                <div class="tab-pane fade show active" id="credentials" role="tabpanel"
                                    aria-labelledby="credentials-tab">
                                    <div class="row mt-3">
                                        <div class="col-12 col-md-6">
                                            <div class="form-group mb-0">
                                                <label for="smsMethod">Select SMS Method</label>
                                                <select class="form-control" id="smsMethod" name="smsMethod">
                                                    <option value="">-- Select Method --</option>
                                                    <option value="twilio" selected>Twilio</option>
                                                    <option value="2factor">2 Factor</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>

                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h4 class="mb-0" style="font-size: 1.1rem;">API Credentials</h4>
                                                <div class="d-flex align-items-center">
                                                    <small id="smsStatusText" class="text-muted mr-2 mb-0">Loading...</small>
                                                    <label class="switch mb-0">
                                                        <input type="checkbox" id="smsStatus" checked>
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-1">
                                        <div class="col-md-12">
                                            <hr class="mt-2">
                                            <form class="mt-4" id="twilioform">

                                                <!-- Twilio Credentials -->
                                                <div id="twilio-credentials">
                                                    <div class="row">
                                                        <div class="col-xl-6 col-md-12 col-sm-12">
                                                            <div class="form-group">
                                                                <label for="twiliosid">Twilio SID</label>
                                                                <input type="text" class="form-control" id="twiliosid"
                                                                    name="twilio_sid">
                                                            </div>
                                                        </div>
                                                        <div class="col-xl-6 col-md-12 col-sm-12">
                                                            <div class="form-group">
                                                                <label for="twilioauthtoken">Twilio Auth Token</label>
                                                                <input type="text" class="form-control" id="twilioauthtoken"
                                                                    name="twilio_token">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-xl-6 col-md-12 col-sm-12">
                                                            <div class="form-group">
                                                                <label for="twiliophonenumber">Twilio Phone Number</label>
                                                                <input type="text" class="form-control"
                                                                    id="twiliophonenumber" name="twilio_number">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- 2 Factor Credentials -->
                                                <div id="factor-credentials" style="display: none;">
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <div class="form-group">
                                                                <label for="factorApiKey">2 Factor API Key</label>
                                                                <input type="text" class="form-control" id="factorApiKey"
                                                                    name="two_factor_api_key">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="text-center mt-4">
                                                    <button type="submit" class="btn btn-primary">Save Settings</button>
                                                </div>
                                            </form>
                                            <div id="templatesuccessMessage" class="alert alert-success"
                                                style="display:none;">
                                            </div>
                                            <div id="templateerrorMessage" class="alert alert-danger" style="display:none;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- WhatsApp Credentials Tab -->
                                <div class="tab-pane fade" id="whatsapp" role="tabpanel" aria-labelledby="whatsapp-tab">




                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h4 class="mb-0" style="font-size: 1.1rem;">WhatsApp API Credentials</h4>
                                                <div class="d-flex align-items-center">
                                                    <small id="whatsappStatusText" class="text-muted mr-2 mb-0">OFF</small>
                                                    <label class="switch mb-0">
                                                        <input type="checkbox" id="whatsappStatus">
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>

                                    <div class="row mt-3">
                                        <div class="col-md-12">

                                            <form id="whatsappForm" class="mt-4">

                                                <div class="row">
                                                    <div class="col-xl-6 col-md-12 col-sm-12">
                                                        <div class="form-group">
                                                            <label for="whatsappToken">WhatsApp Token</label>
                                                            <input type="text" class="form-control" id="whatsappToken"
                                                                name="whatsapp_token" placeholder="Enter WhatsApp Token">
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-6 col-md-12 col-sm-12">
                                                        <div class="form-group">
                                                            <label for="whatsappPhoneId">WhatsApp Phone Number ID</label>
                                                            <input type="text" class="form-control" id="whatsappPhoneId"
                                                                name="whatsapp_phone_number_id"
                                                                placeholder="Enter Phone Number ID">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row mt-2">
                                                    <div class="col-xl-6 col-md-12 col-sm-12">
                                                        <div class="form-group">
                                                            <label for="whatsappApiUrl">WhatsApp API URL</label>
                                                            <input type="text" class="form-control" id="whatsappApiUrl"
                                                                name="whatsapp_api_url"
                                                                value="https://graph.facebook.com/v20.0">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="text-center mt-4">
                                                    <button type="submit" class="btn btn-primary">Save
                                                        Settings</button>
                                                </div>
                                            </form>

                                            <div id="whatsappsSuccessMessage" class="alert alert-success"
                                                style="display:none;"></div>
                                            <div id="whatsappsErrorMessage" class="alert alert-danger"
                                                style="display:none;"></div>
                                        </div>
                                    </div>
                                </div>

                                {{-- smtp Credentials Tab --}}


                                <div class="tab-pane fade" id="smtp-credentials" role="tabpanel"
                                    aria-labelledby="smtp-credentials-tab">
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h4 class="mb-0" style="font-size: 1.1rem;">SMTP Configuration</h4>
                                                <div class="d-flex align-items-center">
                                                    <small id="smtpStatusText" class="text-muted mr-2 mb-0">OFF</small>
                                                    <label class="switch mb-0">
                                                        <input type="checkbox" id="smtpStatus">
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>

                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <form class="mt-2" id="emailForm">
                                                <div id="email-credentials">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="mailMailer">Mail Mailer</label>
                                                                <input type="text" class="form-control" id="mailMailer"
                                                                    name="mail_mailer">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="mailHost">Mail Host</label>
                                                                <input type="text" class="form-control" id="mailHost"
                                                                    name="mail_host">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="mailPort">Mail Port</label>
                                                                <input type="number" class="form-control" id="mailPort"
                                                                    name="mail_port">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="mailUsername">Mail Username</label>
                                                                <input type="email" class="form-control" id="mailUsername"
                                                                    name="mail_username">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="mailPassword">Mail Password</label>
                                                                <div class="input-group">
                                                                    <input type="password" class="form-control"
                                                                        id="mailPassword" name="mail_password" style="border-top-left-radius: 50px; border-bottom-left-radius: 50px;">
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text" id="toggleMailPassword" style="cursor: pointer; border-color: rgb(207 236 224); border-top-right-radius: 50px; border-bottom-right-radius: 50px;">
                                                                            <i class="fas fa-eye"></i>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="mailEncryption">Mail Encryption</label>
                                                                <input type="text" class="form-control" id="mailEncryption"
                                                                    name="mail_encryption">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="mailFromAddress">Mail From Address</label>
                                                                <input type="email" class="form-control"
                                                                    id="mailFromAddress" name="mail_from_address">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="mailFromName">Mail From Name</label>
                                                                <input type="text" class="form-control" id="mailFromName"
                                                                    name="mail_from_name">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="text-center mt-4">
                                                        <button type="submit" class="btn btn-primary">Save
                                                            Settings</button>
                                                    </div>
                                                </div>
                                            </form>

                                            <div id="successMessage" class="alert alert-success" style="display:none;">
                                            </div>
                                            <div id="errorMessage" class="alert alert-danger" style="display:none;">
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                {{-- firebase Credentials Tab --}}



                                <div class="tab-pane fade" id="firebase-credentials" role="tabpanel"
                                    aria-labelledby="firebase-credentials-tab">
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h4 class="mb-0" style="font-size: 1.1rem;">Firebase Configuration</h4>
                                                <div class="d-flex align-items-center">
                                                    <small id="firebaseStatusText" class="text-muted mr-2 mb-0">OFF</small>
                                                    <label class="switch mb-0">
                                                        <input type="checkbox" id="firebaseStatus">
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>

                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <form class="mt-2" id="firebaseForm">
                                                <div id="firebase-credentials">
                                                    <div class="row">
                                                        <div class="col-md-6 col-lg-6 col-sm-6">
                                                            <div class="form-group">
                                                                <label for="firebaseApiKey">API Key</label>
                                                                <input type="text" class="form-control" id="firebaseApiKey"
                                                                    name="firebase_api_key">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-lg-6 col-sm-6">
                                                            <div class="form-group">
                                                                <label for="firebasejson">Firebase JSON</label>
                                                                <input type="file" class="form-control" id="firebasejson"
                                                                    name="firebase_json">
                                                                <div id="firebase_json_link" class="mt-2" style="display:none;">
                                                                    <a href="/api/download-firebase-json" class="text-primary"><i class="fas fa-download"></i> <span id="firebase_json_name"></span></a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="text-center mt-4">
                                                        <button type="submit" class="btn btn-primary">Save
                                                            Settings</button>
                                                    </div>
                                                </div>
                                            </form>

                                            <div id="firebaseSuccessMessage" class="alert alert-success"
                                                style="display:none;"></div>
                                            <div id="firebaseErrorMessage" class="alert alert-danger" style="display:none;">
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                {{-- razorpay Credentials Tab --}}

                                <div class="tab-pane fade" id="razorpay-credentials" role="tabpanel"
                                    aria-labelledby="razorpay-credentials-tab">

                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h4 class="mb-0" style="font-size: 1.1rem;">Razorpay Configuration</h4>
                                                <div class="d-flex align-items-center">
                                                    <small id="razorpayStatusText" class="text-muted mr-2 mb-0">OFF</small>
                                                    <label class="switch mb-0">
                                                        <input type="checkbox" id="razorpayStatus">
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                             <!-- <div class="text-center ">
                                                <strong id="razorpayConnectionText" >Not Connected </strong>
                                            </div> -->
                                            <form class="mt-2" id="razorpayForm">
                                                <div class="row">
                                                    <div class="col-md-6 col-lg-6 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="razorpayKey">Razorpay Key ID</label>
                                                            <input type="text" class="form-control" id="razorpayKey"
                                                                name="razorpay_key">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6 col-lg-6 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="razorpaySecret">Razorpay Secret Key</label>
                                                            <input type="text" class="form-control" id="razorpaySecret"
                                                                name="razorpay_secret">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="text-center mt-4">
                                                    <button type="submit" class="btn btn-primary">
                                                        Save Settings
                                                    </button>
                                                </div>
                                            </form>
                                            

                                            <div id="razorpaySuccessMessage" class="alert alert-success"
                                                style="display:none;"></div>
                                            <div id="razorpayErrorMessage" class="alert alert-danger" style="display:none;">
                                            </div>
                                        </div>
                                    </div>

                                </div>




                            </div>
                        </div>

                    </div>
                </div>
            </div>


            <div class="modal fade" id="sendSmsModal" tabindex="-1" role="dialog" aria-labelledby="sendSmsModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color:#f89884;">
                            <h5 class="modal-title text-white" id="sendSmsModalLabel"><i class="fa fa-paper-plane"></i> <span class="hdr-btn-text">Send</span>
                                SMS</h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="sendSmsForm">
                                <div class="form-group">
                                    <label for="selectUser">Select User</label>
                                    <select class="form-control" id="selectUser" name="selectUser[]" multiple>
                                        <!-- Options will be populated dynamically -->
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="selectTemplate">Select Template</label>
                                    <select class="form-control" id="selectTemplate" name="selectTemplate">
                                        <!-- Options will be populated dynamically -->
                                    </select>
                                </div>

                                <!-- Display Selected Users -->
                                <div class="form-group">
                                    <label>Selected Template Details:</label>
                                    <ul id="selectedTemplateDetails" class="list-group">
                                        <!-- Selected template details will appear here -->
                                    </ul>
                                </div>

                            </form>


                        </div>
                        <div class="modal-footer">

                            <button type="button" class="btn btn-primary" onclick="sendSms()">Send SMS</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css">
    <script src=" https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>


    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Firebase SDK -->
    <script src="https://www.gstatic.com/firebasejs/9.6.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.0/firebase-database-compat.js"></script>



    <script>
        $(document).ready(function () {
            // ✅ Load saved status from backend on page load
            $.get('/api/get-service-status', function (res) {
                let smsStatus = res.sms_status === 'on';
                let whatsappStatus = res.whatsapp_status === 'on';
                let smtpStatus = res.smtp_status === 'on';
                let firebaseStatus = res.firebase_status === 'on';
                let razorpayStatus = res.razorpay_status === 'on';


                $('#smsStatus').prop('checked', smsStatus);
                $('#whatsappStatus').prop('checked', whatsappStatus);
                $('#smtpStatus').prop('checked', smtpStatus);
                $('#firebaseStatus').prop('checked', firebaseStatus);
                $('#razorpayStatus').prop('checked', razorpayStatus);

                $('#smsStatusText').text(smsStatus ? 'ON' : 'OFF');
                $('#whatsappStatusText').text(whatsappStatus ? 'ON' : 'OFF');
                $('#smtpStatusText').text(smtpStatus ? 'ON' : 'OFF');
                $('#firebaseStatusText').text(firebaseStatus ? 'ON' : 'OFF');
                $('#razorpayStatusText').text(razorpayStatus ? 'ON' : 'OFF');


            });

            // ✅ Toggle SMS
            $('#smsStatus').on('change', function (e) {
                e.preventDefault();

                let isChecked = $(this).is(':checked');
                let status = isChecked ? 'on' : 'off';

                $('#smsStatusText').text(status.toUpperCase());

                $.post('/api/save-service-status', {
                    type: 'sms',
                    status: status
                })
                    .done(function () {
                        Swal.fire({
                            icon: status === 'on' ? 'success' : 'warning',
                            title: 'SMS Service',
                            text: "SMS Service is now " + status.toUpperCase(),
                            confirmButtonText: 'OK',
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload(); // 🔄 reload after OK
                            }
                        });
                    })
                    .fail(function (xhr) {
                        // revert toggle on error
                        $('#smsStatus').prop('checked', !isChecked);
                        $('#smsStatusText').text(isChecked ? 'OFF' : 'ON');

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Something went wrong',
                            confirmButtonText: 'OK'
                        });
                    });
            });


            // ✅ Toggle WhatsApp
            $('#whatsappStatus').on('change', function (e) {
                e.preventDefault();

                let isChecked = $(this).is(':checked');
                let status = isChecked ? 'on' : 'off';

                $('#whatsappStatusText').text(status.toUpperCase());

                $.post('/api/save-service-status', {
                    type: 'whatsapp',
                    status: status
                })
                    .done(function () {
                        Swal.fire({
                            icon: status === 'on' ? 'success' : 'warning',
                            title: 'WhatsApp Service',
                            text: "WhatsApp Service is now " + status.toUpperCase(),
                            confirmButtonText: 'OK',
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload(); // 🔄 reload after OK click
                            }
                        });
                    })
                    .fail(function (xhr) {
                        // revert toggle if API fails
                        $('#whatsappStatus').prop('checked', !isChecked);
                        $('#whatsappStatusText').text(isChecked ? 'OFF' : 'ON');

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Something went wrong',
                            confirmButtonText: 'OK'
                        });
                    });
            });

            // ✅ Toggle SMTP
            $('#smtpStatus').on('change', function (e) {
                e.preventDefault();

                console.log('SMTP toggle changed');

                let isChecked = $(this).is(':checked');
                let status = isChecked ? 'on' : 'off';

                $('#smtpStatusText').text(status.toUpperCase());

                $.post('/api/save-service-status', {
                    type: 'smtp',
                    status: status
                })
                    .done(function () {
                        console.log('SMTP POST success');

                        Swal.fire({
                            icon: status === 'on' ? 'success' : 'warning',
                            title: 'SMTP Service',
                            text: "SMTP Service is now " + status.toUpperCase(),
                            confirmButtonText: 'OK',
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload(); // 🔄 reload only after OK
                            }
                        });
                    })
                    .fail(function (xhr) {
                        console.log('SMTP POST failed');

                        $('#smtpStatus').prop('checked', !isChecked);
                        $('#smtpStatusText').text(isChecked ? 'OFF' : 'ON');

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Something went wrong',
                            confirmButtonText: 'OK'
                        });
                    });
            });


            $('#firebaseStatus').on('change', function (e) {
                e.preventDefault();

                let isChecked = $(this).is(':checked');
                let status = isChecked ? 'on' : 'off';

                $('#firebaseStatusText').text(status.toUpperCase());

                $.post('/api/save-service-status', {
                    type: 'firebase',
                    status: status
                })
                    .done(function () {
                        Swal.fire({
                            icon: status === 'on' ? 'success' : 'warning',
                            title: 'Firebase Service',
                            text: "Firebase Service is now " + status.toUpperCase(),
                            confirmButtonText: 'OK',
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload(); // 🔄 reload after OK
                            }
                        });
                    })
                    .fail(function (xhr) {
                        // revert toggle on error
                        $('#firebaseStatus').prop('checked', !isChecked);
                        $('#firebaseStatusText').text(isChecked ? 'OFF' : 'ON');

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Something went wrong',
                            confirmButtonText: 'OK'
                        });
                    });
            });



            $('#razorpayStatus').on('change', function (e) {
                e.preventDefault();

                let isChecked = $(this).is(':checked');
                let status = isChecked ? 'on' : 'off';

                $('#razorpayStatusText').text(status.toUpperCase());

                $.post('/api/save-service-status', {
                    type: 'razorpay',
                    status: status
                })
                    .done(function () {
                        Swal.fire({
                            icon: status === 'on' ? 'success' : 'warning',
                            title: 'Razorpay Service',
                            text: "Razorpay is now " + status.toUpperCase(),
                            confirmButtonText: 'OK',
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload(); // 🔄 reload ONLY after OK click
                            }
                        });
                    })
                    .fail(function (xhr) {
                        $('#razorpayStatus').prop('checked', false);
                        $('#razorpayStatusText').text('OFF');

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Something went wrong',
                            confirmButtonText: 'OK'
                        });
                    });
            });




        });




        $(document).on('click', '#exportButton', function () {
            let branchId = localStorage.getItem('selectedBranchId');

            if (!branchId) {
                alert("Branch ID not found in localStorage!");
                return;
            }

            window.location.href = "{{ route('smstemplate.export') }}" + "?branch_id=" + branchId;
        });

        $(document).ready(function () {
            // Function to toggle inputs based on selected method
            function toggleInputs() {
                var smsMethod = $('#smsMethod').val();

                if (smsMethod === 'twilio') {
                    $('#twilio-credentials').show();
                    $('#factor-credentials').hide();
                } else if (smsMethod === '2factor') {
                    $('#twilio-credentials').hide();
                    $('#factor-credentials').show();
                } else {
                    $('#twilio-credentials').hide();
                    $('#factor-credentials').hide();
                }
            }

            // Trigger change event on page load to set the initial state
            $('#smsMethod').on('change', toggleInputs);
            toggleInputs(); // Call once to initialize
        });



        $(document).ready(function () {
            $('#smsMethod').on('change', function () {
                var smsMethod = $(this).val();

                if (smsMethod) {
                    $.ajax({
                        url: '/api/get-sms-settings',
                        method: 'GET',
                        data: {
                            sms_method: smsMethod
                        },
                        success: function (response) {
                            // If the method is Twilio, fill the form with the Twilio credentials
                            if (smsMethod === 'twilio' && response.data) {
                                $('#twiliosid').val(response.data.twilio_sid);
                                $('#twilioauthtoken').val(response.data.twilio_token);
                                $('#twiliophonenumber').val(response.data.twilio_number);

                                // Clear 2factor input
                                $('#factorApiKey').val('');
                            } else if (smsMethod === '2factor' && response.data) {
                                // If the method is 2factor, fill the form with 2factor API Key
                                $('#factorApiKey').val(response.data.two_factor_api_key);

                                // Clear Twilio inputs
                                $('#twiliosid').val('');
                                $('#twilioauthtoken').val('');
                                $('#twiliophonenumber').val('');
                            }
                        },
                        error: function () {
                            alert('Failed to load settings');
                        }
                    });
                } else {
                    // Clear all fields if no method is selected
                    $('#twiliosid, #twilioauthtoken, #twiliophonenumber, #factorApiKey').val('');
                }
            });

            // Initialize the form with default settings for Twilio (if any)
            $('#smsMethod').trigger('change');

        });

        $('#whatsapp-tab').on('shown.bs.tab', function () {
            $.ajax({
                url: '/api/get-whatsapp-settings',
                method: 'GET',
                success: function (response) {
                    if (response.data) {
                        $('#whatsappToken').val(response.data.whatsapp_token || '');
                        $('#whatsappPhoneId').val(response.data.whatsapp_phone_number_id || '');
                        $('#whatsappApiUrl').val(response.data.whatsapp_api_url ||
                            'https://graph.facebook.com/v20.0');
                    }
                },
                error: function () {
                    $('#whatsappsErrorMessage').text('Failed to load WhatsApp settings').show();
                }
            });
        });

        $('#whatsappForm').on('submit', function (e) {
            e.preventDefault();

            var formData = {
                'whatsapp_token': $('#whatsappToken').val(),
                'whatsapp_phone_number_id': $('#whatsappPhoneId').val(),
                'whatsapp_api_url': $('#whatsappApiUrl').val(),
            };

            $.ajax({
                url: '/api/save-settings',
                type: 'POST',
                data: JSON.stringify(formData),
                contentType: 'application/json',
                success: function (response) {
                    $('#whatsappsSuccessMessage')
                        .text(response.message || 'WhatsApp settings saved successfully!')
                        .show();
                    setTimeout(function () {
                        $('#whatsappsSuccessMessage').fadeOut();
                    }, 1500);
                },
                error: function () {
                    $('#whatsappsErrorMessage')
                        .text('Failed to save WhatsApp settings')
                        .show();
                }
            });
        });


        $('#twilioform').on('submit', function (e) {
            e.preventDefault();

            var smsMethod = $('#smsMethod').val();
            var twilioSid = $('#twiliosid').val();
            var twilioAuthToken = $('#twilioauthtoken').val();
            var twilioPhoneNumber = $('#twiliophonenumber').val();
            var factorApiKey = $('#factorApiKey').val();

            // Prepare data to store in key-value format
            var formData = {
                'sms_method': smsMethod,
                'twilio_sid': twilioSid,
                'twilio_token': twilioAuthToken,
                'twilio_number': twilioPhoneNumber,
                'two_factor_api_key': factorApiKey,

            };


            $.ajax({
                url: '/api/save-settings',
                type: 'POST',
                data: JSON.stringify(formData),
                contentType: 'application/json',
                success: function (response) {
                    // alert('Settings saved successfully!');
                    $('#templatesuccessMessage').text(response.message ||
                        'Settings saved successfully!').show();
                    setTimeout(function () {
                        $('#templatesuccessMessage').fadeOut();
                    }, 1000);
                },
                error: function (xhr) {
                    alert('Failed to save settings');
                }
            });
        });

        $('#emailForm').on('submit', function (e) {
            e.preventDefault();



            var mailHost = $('#mailHost').val();
            var mailMailer = $('#mailMailer').val();
            var mailPort = $('#mailPort').val();
            var mailUsername = $('#mailUsername').val();
            var mailPassword = $('#mailPassword').val();
            var mailEncryption = $('#mailEncryption').val();
            var mailFromAddress = $('#mailFromAddress').val();
            var mailFromName = $('#mailFromName').val();

            // Prepare data to store in key-value format
            var formData = {

                // Email configuration data
                'mail_host': mailHost,
                'mail_mailer': mailMailer,
                'mail_port': mailPort,
                'mail_username': mailUsername,
                'mail_password': mailPassword,
                'mail_encryption': mailEncryption,
                'mail_from_address': mailFromAddress,
                'mail_from_name': mailFromName
            };


            $.ajax({
                url: '/api/save-settings',
                type: 'POST',
                data: JSON.stringify(formData),
                contentType: 'application/json',
                success: function (response) {

                    $('#successMessage').text(response.message || 'Settings saved successfully!')
                        .show();
                    setTimeout(function () {
                        $('#successMessage').fadeOut();
                    }, 1000);
                },
                error: function (xhr) {

                    alert('Failed to save settings');
                }
            });

        });
        $.ajax({
            url: '/api/get-sms-settings',
            method: 'GET',
            success: function (response) {
                if (response.data) {
                    $('#mailMailer').val(response.data.mail_mailer);
                    $('#mailHost').val(response.data.mail_host);
                    $('#mailPort').val(response.data.mail_port);
                    $('#mailUsername').val(response.data.mail_username);
                    $('#mailPassword').val(response.data.mail_password);
                    $('#mailEncryption').val(response.data.mail_encryption);
                    $('#mailFromAddress').val(response.data.mail_from_address);
                    $('#mailFromName').val(response.data.mail_from_name);
                }
            },
            error: function () {
                alert('Failed to load email settings');
            }
        });




        $('#firebaseForm').on('submit', function (e) {
            e.preventDefault();

            let formData = new FormData();
            let firebaseJson = $('#firebasejson')[0].files[0];

            if (firebaseJson) {
                formData.append('firebase_json', firebaseJson);
            }

            $.ajax({
                url: '/api/save-settings',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    $('#firebaseSuccessMessage')
                        .text(response.message)
                        .show();
                    setTimeout(() => $('#firebaseSuccessMessage').fadeOut(), 2000);
                    
                    // Refresh the download link
                    if (firebaseJson) {
                        $('#firebase_json_name').text(firebaseJson.name);
                        $('#firebase_json_link').show();
                    }
                },
                error: function () {
                    $('#firebaseErrorMessage')
                        .text('Failed to upload Firebase JSON.')
                        .show();
                }
            });
        });
        $(document).ready(function () {
            $.ajax({
                url: '/api/get-firebase-settings',
                type: 'GET',
                success: function (response) {
                    if (response.success && response.data) {
                        // Show file name next to input
                        if (response.data.firebase_json_name) {
                            $('#firebase_json_name').text(response.data.firebase_json_name);
                            $('#firebase_json_link').show();
                        }
                    }
                },
                error: function () {
                    $('#firebaseErrorMessage')
                        .text('Failed to load Firebase settings.')
                        .show();
                }
            });
        });





        $('#razorpayForm').on('submit', function (e) {
            e.preventDefault();

            let formData = new FormData(this);

            $.ajax({
                url: '/api/save-razorpay-settings',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,

                success: function (response) {
                    $('#razorpaySuccessMessage')
                        .removeClass('text-danger')
                        .addClass('text-success')
                        .html('✅ ' + response.message)
                        .show();

                    $('#razorpayErrorMessage').hide();
                },

                error: function (xhr) {
                    let res = xhr.responseJSON;

                    $('#razorpayErrorMessage')
                        .removeClass('text-success')
                        .addClass('text-danger')
                        .html(
                            '❌ ' + (res?.message || 'Connection failed') +
                            '<br><small>' + (res?.error || '') + '</small>'
                        )
                        .show();

                    $('#razorpaySuccessMessage').hide();
                }
            });
        });

        $(document).ready(function () {
            $.ajax({
                url: '/api/get-razorpay-settings',
                type: 'GET',
                success: function (response) {
                    if (response.success && response.data?.razorpay_key) {
                        $('#razorpayKey').val(response.data.razorpay_key);
                        $('#razorpaySecret').val(response.data.razorpay_secret);

                        $('#razorpayConnectionText').text('Connected')
                          .addClass('text-success');
                    } else {
                        $('#razorpayConnectionText').text('Not Connected')
                          .addClass('text-danger');
                    }
                },
                error: function () {
                    $('#razorpayConnectionText').text('Not Connected');
                }
            });
        });






        $(document).ready(function () {
            let branchId = localStorage.getItem('selectedBranchId');
            // Fetch data from API
            function fetchTemplates() {
                $.ajax({
                    type: 'GET',
                    url: '/api/sms-template',
                    data: {
                        branch_id: branchId
                    },
                    success: function (response) {
                        var table = $('#smstbl').DataTable();
                        if ($.fn.DataTable.isDataTable("#smstbl")) {
                            table.destroy();
                        }

                        $('#inventoryBody').empty(); // Clear existing data
                        let srNo = 1;
                        response.forEach(template => {
                            let statusClass = template.status === 'active' ? 'btn-primary' :
                                'btn-danger';
                            let statusText = template.status === 'active' ? 'Active' :
                                'Inactive';
                            let extraStyles = 'border-radius: 20px; padding:1px 15px;';

                            $('#inventoryBody').append(
                                `
                                                                                                                                                                                                    <tr>

                                                                                                                                                                                                        <td>${template.name}</td>
                                                                                                                                                                                                        <td>${template.content}</td>
                                                                                                                                                                                                        <td>
                                                                                                                                                                                                            <button class="btn ${statusClass}" style="${extraStyles}">${statusText}</button>
                                                                                                                                                                                                        </td>
                                                                                                                                                                                                        <td>
                                                                                                                                                                                                            <div class="icon" style="cursor:pointer">
                                                                                                                                                                                                                <i class="fa fa-pencil m-r-5 icon1 edit-user"  data-id="${template.id}" ></i>
                                                                                                                                                                                                                <i class="fa fa-trash-o m-r-5 icon2 delete-user" data-id="${template.id}" ></i>
                                                                                                                                                                                                            </div>
                                                                                                                                                                                                        </td>
                                                                                                                                                                                                    </tr>
                                                                                                                                                                                            `
                            );
                        });
                        $('#smstbl').DataTable({
                            "paging": true,
                            "searching": true,
                            "ordering": true,
                            "destroy": true
                        });
                    },
                    error: function (xhr) {
                        console.error('Error fetching data:', xhr);
                    }
                });
            }

            // Call the function to fetch data on page load
            fetchTemplates();
        });


        $(document).on('click', '.edit-user', function () {
            var templateId = $(this).data('id');
            window.location.href = '/sms-template/edit/' + templateId;
        });

        $(document).on('click', '.delete-user', function () {
            var templateId = $(this).data('id');

            if (!templateId) {
                Swal.fire('Error', 'Template ID is missing!', 'error');
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/api/sms-template/' + templateId,
                        type: 'DELETE',
                        success: function (response) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Template deleted successfully!',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                                fetchTemplates();
                            });
                        },
                        error: function (xhr) {
                            Swal.fire('Error', 'Failed to delete template.', 'error');
                        }
                    });
                }
            });
        });





        $(document).ready(function () {
            // When the modal is opened
            $('#sendSmsModal').on('show.bs.modal', function () {
                // Initialize Select2 for the first time when the modal opens
                $('#selectUser').select2({
                    placeholder: "Select users",
                    allowClear: true
                });

                // Fetch users
                $.ajax({
                    url: '/api/users', // Your API endpoint for users
                    method: 'GET',
                    success: function (response) {
                        var users = response
                            .data; // Assuming the response structure is { data: [...] }
                        var userOptions = '<option value="">-- Select User --</option>';
                        $.each(users, function (index, user) {
                            userOptions +=
                                `<option value="${user.id}">${user.fullname}</option>`;
                        });
                        $('#selectUser').html(userOptions); // Populate the user dropdown
                    },
                    error: function () {
                        alert('Failed to fetch users');
                    }
                });

                // Fetch templates
                $.ajax({
                    url: '/api/sms-templates', // Your API endpoint for templates
                    method: 'GET',
                    success: function (response) {
                        var templates = response
                            .data; // Assuming the response structure is { data: [...] }
                        var templateOptions = '<option value="">-- Select Template --</option>';
                        $.each(templates, function (index, template) {
                            templateOptions +=
                                `<option value="${template.id}">${template.name}</option>`;
                        });
                        $('#selectTemplate').html(
                            templateOptions); // Populate the template dropdown

                        // Initialize Select2 plugin
                        $('#selectTemplate').select2({
                            placeholder: "-- Select Template --",
                            allowClear: true,
                            width: '100%',
                            minimumResultsForSearch: 0,
                            dropdownParent: $('#sendSmsModal')
                        });

                        // Set search input placeholder after opening dropdown
                        $('#selectTemplate').on('select2:open', function () {
                            $('.select2-search__field').attr('placeholder',
                                'Search templates...');
                        });
                    },
                    error: function () {
                        alert('Failed to fetch templates');
                    }
                });

                // Update selected users list when the selection changes
                // $('#selectUser').on('change', function () {
                //     var selectedUsers = $(this).val(); // Get selected user IDs
                //     var selectedTemplateDetails = $('#selectedTemplateDetails');
                //     selectedTemplateDetails.empty(); // Clear the list before updating

                //     $.each(selectedUsers, function (index, userId) {
                //         // You might want to fetch the user names dynamically to display here, based on IDs
                //         selectedTemplateDetails.append('<li class="list-group-item" data-user-id="' + userId + '">' + userId + '</li>');
                //     });
                // });

                $('#selectTemplate').on('change', function () {
                    var selectedTemplateId = $(this).val(); // Get selected template ID
                    var selectedTemplateList = $('#selectedTemplateDetails');
                    selectedTemplateList.empty(); // Clear previous selection

                    if (selectedTemplateId) {
                        // 🔹 Fetch template details by ID via API
                        $.ajax({
                            url: '/api/sms-templates/' +
                                selectedTemplateId, // endpoint to fetch single template
                            method: 'GET',
                            success: function (response) {
                                var template = response.data;

                                // Display template details
                                var detailsHtml = `

                                                                                                                                                    <li class="list-group-item"><strong>Name:</strong> ${template.name}</li>
                                                                                                                                                    <li class="list-group-item"><strong>Message:</strong> ${template.content || 'N/A'}</li>
                                                                                                                                                    <li class="list-group-item"><strong>Status:</strong> ${template.status || 'N/A'}</li>
                                                                                                                                                `;

                                selectedTemplateList.html(detailsHtml);
                            },
                            error: function () {
                                selectedTemplateList.html(
                                    '<li class="list-group-item text-danger">Failed to load template details</li>'
                                );
                            }
                        });
                    }
                });

            });
        });







        function sendSms() {
            // Get selected user IDs
            var selectedUsers = $('#selectUser').val();
            var selectedTemplate = $('#selectTemplate').val();
            var selectedService = "twilio"; // Default value
            var senderId = "{{ auth()->user()->id }}"; // Logged-in user ID

            // Validate
            if (!selectedUsers || selectedUsers.length === 0 || !selectedTemplate) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Validation Error',
                    text: 'Please select at least one user and a template.'
                });
                return;
            }

            // AJAX call
            $.ajax({
                url: "/api/send-sms",
                type: 'POST',
                data: {
                    user_ids: selectedUsers, // Send as an array
                    template_id: selectedTemplate,
                    sender_id: senderId,
                    service: selectedService,
                },
                success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message || 'SMS sent successfully!',
                    }).then(() => {
                        $('#sendSmsForm')[0].reset();
                        $('#selectUser').val(null).trigger('change');
                        $('#selectTemplate').val(null).trigger('change');
                        $('#selectedTemplateDetails').empty();
                        location.reload();
                        // Close the modal
                        $('#sendSmsModal').modal('hide');
                    });
                },
                error: function (xhr) {
                    let message = 'Failed to send SMS. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: message,
                    });
                }
            });



            // get Credential
            $(document).ready(function () {
                // Fetch email settings when the page is loaded
                $.ajax({
                    url: '/api/get-sms-settings',
                    method: 'GET',
                    success: function (response) {
                        if (response.data) {
                            // Fill the email form with the received data
                            $('#mailMailer').val(response.data.mail_mailer);
                            $('#mailHost').val(response.data.mail_host);
                            $('#mailPort').val(response.data.mail_port);
                            $('#mailUsername').val(response.data.mail_username);
                            $('#mailPassword').val(response.data.mail_password);
                            $('#mailEncryption').val(response.data.mail_encryption);
                            $('#mailFromAddress').val(response.data.mail_from_address);
                            $('#mailFromName').val(response.data.mail_from_name);
                        }
                    },
                    error: function () {
                        alert('Failed to load email settings');
                    }
                });
            });



        }
        $(document).on('click', '#toggleMailPassword', function () {
            let passwordField = $('#mailPassword');
            let icon = $(this).find('i');
            
            if (passwordField.attr('type') === 'password') {
                passwordField.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                passwordField.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

    </script>
@endsection