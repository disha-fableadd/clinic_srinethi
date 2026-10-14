@extends('layout.app')

<style>
    @media screen and (max-width:767px) {

        .padding-all {
            padding-left: 5px !important;
        }

    }
</style>

@section('content')
    <div class="page-wrapper">
        <div class="content" style="height:100vh">
            <div class="row" style="padding-top: 15px;">
                <div class="col-sm-6 col-8">
                    <h4 class="page-title">
                        <i class="fa fa-user"></i> Profile Details
                    </h4>
                </div>
                <div class="col-sm-6 col-4 text-right">
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-rounded btn-hdr">
                        <i class="fa fa-arrow-left"></i> <span class="hdr-btn-text">Back</span>
                    </a>
                </div>
            </div>

            @php
                $userRole = optional(Auth::user()->role)->name;
                use App\Models\Setting;

                $clinicLogo =
                    Setting::where('key', 'clinic_logo')->value('value') ??
                    env('IMAGE_PATH') . 'admin/assets/img/cliniclogo.png';
                $clinicName = Setting::where('key', 'clinic_name')->value('value') ?? 'Clinic';
                $email = Setting::where('key', 'clinic_email')->value('value') ?? 'Email';
                $phone = Setting::where('key', 'clinic_phone')->value('value') ?? 'Phone';
                $address = Setting::where('key', 'clinic_address')->value('value') ?? 'Address';
                $city = Setting::where('key', 'clinic_city')->value('value') ?? 'City';
                $state = Setting::where('key', 'clinic_state')->value('value') ?? 'State';
                $tax = Setting::where('key', 'tax_name')->value('value') ?? 'Tax';
                $type = Setting::where('key', 'tax_type')->value('value') ?? 'Type';
                $value = Setting::where('key', 'value')->value('value') ?? '0';
            @endphp

            @if ($userRole !== 'Patient')
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-footer text-right" style="background-color:#87ceb0">
                                <h3 style="float:left" class="text-dark">
                                    <i class="fa fa-info-circle icon-style2"></i>
                                    <span class="user-name"></span>'s Profile
                                </h3>
                                <a href="#" id="edit-profile-btn" class="btn btn-primary btn-rounded btn-hdr">
                                    <i class="fa fa-pencil-alt"></i> <span class="hdr-btn-text">Edit</span>
                                </a>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-8">
                                        <div class="row">
                                              <div
                                        class="col-lg-6 col-4 margin-img " style="display: flex;align-items: center;justify-content: center;">
                                        <img class="avatar user-image userProfile"
                                            src="{{ Auth::user()->profile ? asset(Auth::user()->profile) : asset('/admin/assets/img/img1.png') }}"
                                            alt="Profile Image"
                                            style="width: 150px; height: 150px; border-radius: 50%; border: 3px solid #4caf50; object-fit: cover;">
                                    </div>


                                            <div class="col-lg-6 col-8">

                                                {{-- <p class="text-dark"><strong>Plan:</strong>
                                                 <span>
                                                        @if (Auth::check() && optional(Auth::user()->role)->name == 'Admin')

                                                            <a href="" style="padding-right:10px">
                                                                <button class="btn custom-btn" style="background-color: #87ceb0;font-weight: 500;">
                                                                    <i class="fa fa-crown"></i>
                                                                    {{ Str::title(App\Models\Plan::where('user_id', Auth::id())->first()?->plan_title ?? 'Free ') }}
                                                                    Plan
                                                                </button>
                                                            </a>

                                                        @endif
                                                    </span></p> --}}

                                                <p class="text-dark"><strong>Plan:</strong>
                                                    <span>
                                                        @if (Auth::check())
                                                            @php
                                                                $currentUser = Auth::user();
                                                                $planId = $currentUser->plan_id;
                                                                if (!$planId && $currentUser->created_by) {
                                                                    $creator = \App\Models\User::find($currentUser->created_by);
                                                                    $planId = $creator ? $creator->plan_id : null;
                                                                }
                                                                $planName = $planId ? \App\Models\Plan::find($planId)?->name : 'Free';
                                                            @endphp
                                                            <a href="" style="padding-right:10px">
                                                                <button class="btn custom-btn"
                                                                    style="background-color: #87ceb0;font-weight: 500;">
                                                                    <i class="fa fa-crown"></i>
                                                                    {{ Str::title($planName) }}
                                                                    
                                                                </button>
                                                            </a>
                                                        @endif
                                                    </span>
                                                </p>
  
                                                <hr>
                                                </p>

                                                <p class="text-dark"><strong>Role:</strong> <span id="role-select"></span>
                                                </p>
                                                <hr>
                                                <p class="text-dark"><strong>Email:</strong> <span id="user-email"></span>
                                                </p>
                                                <hr>
                                                <p class="text-dark"><strong>Phone:</strong> <span id="user-phone"></span>
                                                </p>
                                                <hr>
                                                <p class="text-dark"><strong>Birthday:</strong> <span id="user-dob"></span>
                                                </p>
                                                <hr>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <p class="text-dark"><strong>Address:</strong> <span id="user-address"></span></p>
                                        <hr>
                                        <p class="text-dark"><strong>Gender:</strong> <span id="user-gender"></span></p>
                                        <hr>
                                        <p class="text-dark"><strong>City:</strong> <span id="city"></span></p>
                                        <hr>
                                        <p class="text-dark"><strong>State:</strong> <span id="state"></span></p>
                                        <hr>
                                        <p class="text-dark"><strong>Shift:</strong> <span id="shift"></span></p>
                                        <hr>
                                    </div>
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
                                    <i class="fa fa-info-circle icon-style2 text-white"></i> Clinic Settings
                                </h3>

                            </div>

                            <div class="card-body mt-3">
                                <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active text-dark" id="clinic-tab" data-toggle="tab"
                                            href="#clinicDetails" role="tab">Clinic Details</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-dark" id="password-tab" data-toggle="tab"
                                            href="#changePassword" role="tab">Change Password</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-dark" id="tax-tab" data-toggle="tab" href="#taxDetails"
                                            role="tab">Tax Details</a>
                                    </li>
                                </ul>



                                <div class="tab-content mt-3" id="settingsTabsContent">
                                    <!-- Clinic Details Tab -->
                                    <div class="tab-pane fade show active " id="clinicDetails" role="tabpanel">
                                        <div class="row">
                                            <div class="col-12 col-md-4">
                                                <div class="form-group">
                                                    <label for="clinic-name" class="text-dark">Clinic/Hospital Name:</label>
                                                    <input type="text" id="clinic-name" name="clinic_name"
                                                        class="form-control text-dark" value="{{ $clinicName }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <div class="form-group">
                                                    <label class="text-dark"> Email</label>
                                                    <input type="email" name="clinic_email" class="form-control"
                                                        value="{{ $email }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <div class="form-group">
                                                    <label class="text-dark"> Phone</label>
                                                    <input type="text" name="clinic_phone" class="form-control"
                                                        value="{{ $phone }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-md-4">
                                                <div class="form-group">
                                                    <label class="text-dark"> Address</label>
                                                    <input type="text" name="clinic_address" class="form-control"
                                                        value="{{ $address }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <div class="form-group">
                                                    <label class="text-dark"> City</label>
                                                    <input type="text" name="clinic_city" class="form-control"
                                                        value="{{ $city }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <div class="form-group">
                                                    <label class="text-dark"> State</label>
                                                    <input type="text" name="clinic_state" class="form-control"
                                                        value="{{ $state }}" readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group mb-0">
                                            <label for="clinic-logo" class="text-dark">Clinic/Hospital Logo:</label>
                                            <br>
                                            <img src="{{ asset(env('IMAGE_PATH') . $clinicLogo) }}" id="clinic-logo"
                                                alt="Clinic Logo" class="img-fluid" style="max-height: 100px;">
                                        </div>

                                        <!-- Update Button -->
                                        <button type="button" class="btn btn-primary" data-toggle="modal"
                                            style="float:right" data-target="#updateModal">
                                            Update Details
                                        </button>

                                    </div>

                                    <!-- Update Modal -->
                                    <div class="modal fade" id="updateModal" tabindex="-1" role="dialog"
                                        aria-labelledby="updateModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header" style="background-color: #CFECE0; color:black">
                                                    <h5 class="modal-title" id="updateModalLabel" style="font-size:18px">
                                                        Update
                                                        Clinic Details</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <form id="updateClinicForm" method="POST" action=""
                                                    enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label for="update-clinic-name"
                                                                        class="text-dark">Clinic
                                                                        Name:</label>
                                                                    <input type="text" id="update-clinic-name"
                                                                        name="clinic_name" class="form-control"
                                                                        value="{{ $clinicName }}" required>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label class="text-dark">Clinic Email</label>
                                                                    <input type="email" name="clinic_email"
                                                                        class="form-control" value="{{ $email }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label class="text-dark">Clinic Phone</label>
                                                                    <input type="text" name="clinic_phone"
                                                                        class="form-control" value="{{ $phone }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label class="text-dark">Clinic Address</label>
                                                                    <input type="text" name="clinic_address"
                                                                        class="form-control" value="{{ $address }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label class="text-dark">Clinic City</label>
                                                                    <input type="text" name="clinic_city"
                                                                        class="form-control" value="{{ $city }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label class="text-dark">Clinic State</label>
                                                                    <input type="text" name="clinic_state"
                                                                        class="form-control" value="{{ $state }}">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="update-clinic-logo" class="text-dark">Clinic
                                                                Logo:</label>
                                                            <div>
                                                                <!-- Show the old logo if it exists -->
                                                                @if (Auth::user()->clinic_logo)
                                                                    <img src="{{ asset(env('IMAGE_PATH') . Auth::user()->clinic_logo ?? env('IMAGE_PATH') . 'admin/assets/img/cliniclogo.png') }}"
                                                                        id="current-clinic-logo" alt="Logo"
                                                                        class="img-fluid"
                                                                        style="max-height: 100px; margin-bottom: 10px;">
                                                                @else
                                                                    <p>No logo uploaded</p>
                                                                @endif
                                                            </div>
                                                            <!-- Preview area for the uploaded image -->
                                                            <div id="logo-preview-container"
                                                                style="margin-top: 10px; display: none;">
                                                                <img id="logo-preview" src="" alt="Logo Preview"
                                                                    class="img-fluid" style="max-height: 100px;">
                                                            </div>

                                                            <!-- Input for uploading the logo -->
                                                            <input type="file" id="update-clinic-logo"
                                                                name="clinic_logo" class="form-control" accept="image/*"
                                                                onchange="previewImage(event)">

                                                            <script>
                                                                // Function to show the image preview and hide the old image
                                                                function previewImage(event) {
                                                                    // Hide the old logo if it exists
                                                                    var oldLogo = document.getElementById('current-clinic-logo');
                                                                    if (oldLogo) {
                                                                        oldLogo.style.display = 'none'; // Hide the old logo
                                                                    }

                                                                    // Show the preview container
                                                                    var previewContainer = document.getElementById('logo-preview-container');
                                                                    previewContainer.style.display = 'block';

                                                                    var reader = new FileReader();
                                                                    reader.onload = function() {
                                                                        var output = document.getElementById('logo-preview');
                                                                        output.src = reader.result; // Set the preview image to the selected file
                                                                    };
                                                                    reader.readAsDataURL(event.target.files[0]);
                                                                }
                                                            </script>
                                                        </div>
                                                    </div>

                                                    <div id="logosuccessMessage" class="alert alert-success"
                                                        style="display:none;">
                                                    </div>
                                                    <div id="logoerrorMessage" class="alert alert-danger"
                                                        style="display:none;">
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal" style="border-radius:50px">Close</button>
                                                        <button type="submit" class="btn btn-primary">Update</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Add Modal -->
                                    <div class="modal fade" id="addClinicInfoModal" tabindex="-1" role="dialog"
                                        aria-labelledby="addClinicInfoModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <form id="addClinicInfoForm" method="POST"
                                                action="{{ route('clinic.settings.update') }}">
                                                @csrf
                                                <div class="modal-content">
                                                    <div class="modal-header" style="background-color:#CFECE0">
                                                        <h5 class="modal-title text-dark" id="addClinicInfoModalLabel">Add
                                                            Clinic Information</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span>&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label class="text-dark">Clinic Email</label>
                                                            <input type="email" name="clinic_email"
                                                                class="form-control">
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="text-dark">Clinic Phone</label>
                                                            <input type="text" name="clinic_phone"
                                                                class="form-control">
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="text-dark">Clinic Address</label>
                                                            <input type="text" name="clinic_address"
                                                                class="form-control">
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="text-dark">Clinic City</label>
                                                            <input type="text" name="clinic_city"
                                                                class="form-control">
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="text-dark">Clinic State</label>
                                                            <input type="text" name="clinic_state"
                                                                class="form-control">
                                                        </div>

                                                        <div id="clinicInfoMessage" class="alert"
                                                            style="display: none; margin-top: 10px;"></div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-primary">Save Info</button>
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal" style="border-radius:50px">Close</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <script>
                                        $(document).ready(function() {
                                            $('#addClinicInfoForm').on('submit', function(e) {
                                                e.preventDefault();

                                                var form = $(this);
                                                var url = form.attr('action');
                                                var formData = form.serialize();

                                                $.ajax({
                                                    type: "POST",
                                                    url: url,
                                                    data: formData,
                                                    success: function(response) {
                                                        showAlert('Clinic information added successfully.', 'success');
                                                        setTimeout(function() {
                                                            $('#addClinicInfoModal').modal('hide');
                                                            window.location.href = "/profile";
                                                        }, 1500);
                                                    },
                                                    error: function(xhr) {
                                                        let err = xhr.responseJSON?.message || 'Something went wrong.';
                                                        showAlert(err, 'danger');
                                                    }
                                                });
                                            });

                                            function showAlert(message, type) {
                                                let alertBox = $('#clinicInfoMessage');
                                                alertBox.removeClass().addClass(`alert alert-${type}`).text(message).fadeIn();
                                                if (type === 'success') {
                                                    setTimeout(function() {
                                                        alertBox.fadeOut();
                                                    }, 1500);
                                                }
                                            }
                                        });
                                    </script>

                                    <!-- Change Password Tab -->
                                    <div class="tab-pane fade" id="changePassword" role="tabpanel">
                                        <form id="change-password-form">
                                            <div class="form-group">
                                                <label for="current-password" class="text-dark">Current Password:</label>
                                                <input type="password" id="current-password" name="current_password"
                                                    class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="new-password" class="text-dark">New Password:</label>
                                                <input type="password" id="new-password" name="new_password"
                                                    class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="confirm-password" class="text-dark">Confirm New
                                                    Password:</label>
                                                <input type="password" id="confirm-password"
                                                    name="new_password_confirmation" class="form-control" required>
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-rounded btn-hdr">
                                                <i class="fa fa-save"></i> Update Password
                                            </button>
                                            <br><br>
                                            <div id="messageContainer" class="alert d-none" role="alert"></div>
                                        </form>
                                    </div>

                                    <!-- Tax Details Tab -->
                                    <div class="tab-pane fade" id="taxDetails" role="tabpanel">


                                        <div class="form-group">
                                            <label class="text-dark">Tax Name:</label>
                                            <input type="text" name="tax_name" class="form-control text-dark"
                                                value="{{ ucfirst($tax) }}">
                                        </div>

                                        @php
                                            $formattedType =
                                                $type === 'fixed_amount'
                                                    ? 'Fixed Amount'
                                                    : ($type === 'percentage'
                                                        ? 'Percentage'
                                                        : ucfirst($type));
                                        @endphp

                                        <div class="form-group">
                                            <label class="text-dark">Tax Type:</label>
                                            <input type="text" name="tax_type" class="form-control"
                                                value="{{ $formattedType }}">
                                        </div>



                                        <div class="form-group">
                                            <label class="text-dark">Value :</label>
                                            <input type="number" name="value" class="form-control"
                                                value="{{ $value }}">
                                        </div>

                                        <div class="row" style="display: flex;justify-content: end;margin-right:5px">
                                            <button type="button" class="btn btn-primary" data-toggle="modal"
                                                data-target="#addTaxInfoModal">
                                                Update TaxInfo
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Add Tax Info Modal -->
                                    <div class="modal fade" id="addTaxInfoModal" tabindex="-1" role="dialog"
                                        aria-labelledby="addTaxInfoModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <form id="addTaxInfoForm" method="POST"
                                                action="{{ route('clinic.settings.update') }}">
                                                @csrf
                                                <div class="modal-content">
                                                    <div class="modal-header" style="background-color:#CFECE0">
                                                        <h5 class="modal-title text-dark" id="addTaxInfoModalLabel">Update
                                                            Tax
                                                            Information</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span>&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label class="text-dark">Tax Name</label>
                                                            <input type="text" name="tax_name" class="form-control"
                                                                value="{{ ucfirst($tax) }}" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="text-dark">Tax Type</label>
                                                            <select name="tax_type" class="form-control" required>
                                                                <option value="">Select Tax Type</option>
                                                                <option value="percentage"
                                                                    {{ $type == 'percentage' ? 'selected' : '' }}>
                                                                    Percentage</option>
                                                                <option value="fixed_amount"
                                                                    {{ $type == 'fixed_amount' ? 'selected' : '' }}>Fixed
                                                                    Amount</option>
                                                            </select>
                                                        </div>

                                                        <div class="form-group">
                                                            <label class="text-dark">Value</label>
                                                            <input type="text" name="value" class="form-control"
                                                                value="{{ $value }}" required>
                                                        </div>
                                                        <div id="taxInfoMessage" class="alert"
                                                            style="display: none; margin-top: 10px;"></div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-primary">Update
                                                            Info</button>
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal" style="border-radius:50px">Close</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <script>
                                        $(document).ready(function() {
                                            $('#addTaxInfoForm').on('submit', function(e) {
                                                e.preventDefault();

                                                var form = $(this);
                                                var url = form.attr('action');
                                                var formData = form.serialize();

                                                $.ajax({
                                                    type: "POST",
                                                    url: url,
                                                    data: formData,
                                                    success: function(response) {
                                                        showTaxAlert('Tax information added successfully.', 'success');
                                                        setTimeout(function() {
                                                            $('#addTaxInfoModal').modal('hide');
                                                            window.location.reload();
                                                        }, 1500);
                                                    },
                                                    error: function(xhr) {
                                                        let err = xhr.responseJSON?.message || 'Something went wrong.';
                                                        showTaxAlert(err, 'danger');
                                                    }
                                                });
                                            });

                                            function showTaxAlert(message, type) {
                                                let alertBox = $('#taxInfoMessage');
                                                alertBox.removeClass().addClass(`alert alert-${type}`).text(message).fadeIn();
                                                if (type === 'success') {
                                                    setTimeout(function() {
                                                        alertBox.fadeOut();
                                                    }, 1500);
                                                }
                                            }
                                        });
                                    </script>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif


            <!-- patient profile -->
            @if ($userRole == 'Patient')
                <div class="row mt-3">
                    <div class="col-md-12  col-sm-12 col-lg-12">
                        <div class="card">
                            <div class="card-footer text-right" style="background-color:#87ceb0">
                                <h3 style="float:left" class="text-dark">
                                    <i class="fa fa-info-circle icon-style2 text-white"></i>
                                    <span class="Patient_name"></span> ' s Details
                                </h3>
                                <a href="#" id="edit-profile-btn" class="btn btn-primary btn-rounded btn-hdr">
                                    <i class="fa fa-pencil-alt"></i> <span class="hdr-btn-text">Edit</span>
                                </a>
                            </div>
                            <div class="card-body mt-3">
                                <div class="row">
                                    <div class="col-lg-8">
                                        <div class="row">
                                            <div class="col-lg-6 col-4 margin-img" style="display: flex;align-items: center;justify-content: center;">
                                                <p class="text-dark margin-text">

                                                    <img id="Patient_image" class="userProfile" src=""
                                                        width="200" height="200" alt="Patient Image"
                                                        style="border-radius:20px"
                                                        onerror="this.onerror=null;this.src='{{ asset(env('IMAGE_PATH') . 'admin/assets/img/img1.png') }}';">
                                                </p>
                                                <p class="text-dark margin-text">
                                                    <strong><i class="fas fa-medkit icon-style1"></i> Symptoms: </strong>
                                                    <span id="symptoms"></span>
                                                <div id="symptom_images_container" class="mt-2"
                                                    style="display: flex; flex-wrap: wrap; gap: 10px;">
                                                    {{-- Symptom images will be injected here dynamically using JavaScript
                                                    --}}
                                                </div>
                                                </p>
                                            </div>

                                            <div class="col-lg-6 col-4 margin-img d-flex align-items-center justify-content-center">
    <img
        class="avatar user-image userProfile"
        src="{{ Auth::user()->profile ? asset(Auth::user()->profile) : asset('/admin/assets/img/img1.png') }}"
        alt="Profile Image"
        style="width: 150px; height: 150px; border-radius: 50%; border: 3px solid #4caf50; object-fit: cover;"
    >
    <div>
         <p class="text-dark margin-text">
                                                    <strong><i class="fas fa-medkit icon-style1"></i> Symptoms: </strong>
                                                    <span id="symptoms"></span>
                                                <div id="symptom_images_container" class="mt-2"
                                                    style="display: flex; flex-wrap: wrap; gap: 10px;">
                                                    {{-- Symptom images will be injected here dynamically using JavaScript
                                                    --}}
                                                </div>
                                                </p>
    </div>
</div>

                                            <div class="col-lg-6 col-8">
                                                <div class="col-md-12 col-lg-12 col-sm-12">
                                                    <div id="output">
                                                    </div>
                                                    <hr class="margin-text">
                                                </div>
                                                <p class="text-dark margin-text">
                                                    <strong><i class="fa fa-id-badge icon-style1"></i> Patient Name:
                                                    </strong>
                                                    <span class="Patient_name"></span>
                                                </p>

                                                <hr class="margin-text">

                                                <p class="text-dark margin-text">
                                                    <strong><i class="fas fa-medkit icon-style1"></i> Treatment: </strong>
                                                    <span id="treatment"></span>
                                                </p>
                                                <hr class="margin-text">

                                                <p class="text-dark margin-text">
                                                    <strong><i class="fas fa-map-marker-alt icon-style1"></i> Address:
                                                    </strong>
                                                    <span id="address"></span>
                                                </p>
                                                <hr class="margin-text">

                                                <p class="text-dark margin-text">
                                                    <strong><i class="fas fa-city icon-style1"></i> City:
                                                    </strong>
                                                    <span id="city"></span>
                                                </p>
                                                <hr class="margin-text">
                                                <p class="text-dark margin-text">
                                                    <strong><i class="fas fa-phone icon-style1"></i> Phone: </strong>
                                                    <span id="phone"></span>
                                                </p>
                                                <hr class="margin-text">

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <p class="text-dark margin-text">
                                            <strong><i class="fas fa-envelope icon-style1"></i> Email: </strong>
                                            <span id="email"></span>
                                        </p>
                                        <hr class="margin-text">
                                        <p class="text-dark margin-text">
                                            <strong><i class="fas fa-birthday-cake icon-style1"></i> Age: </strong>
                                            <span id="age"></span>
                                        </p>
                                        <hr class="margin-text">
                                        <p class="text-dark margin-text">
                                            <strong><i class="fas fa-tint icon-style1"></i> Blood Group: </strong>
                                            <span id="bloodgroup"></span>
                                        </p>
                                        <hr class="margin-text">
                                        <p class="text-dark margin-text">
                                            <strong><i class="fas fa-notes-medical icon-style1"></i> Medical History:
                                            </strong>
                                            <span id="medical_history"></span>
                                        </p>
                                        <hr class="margin-text">

                                        <p class="text-dark margin-text">
                                            <strong><i class="fas fa-flag icon-style1"></i> State:
                                            </strong>
                                            <span id="state"></span>
                                        </p>
                                        <hr class="margin-text">
                                    </div>


                                </div>
                                <div class="button mb-4" style="display: flex; justify-content: end; margin: 0 5px;">
                                    @if (app('hasPermission')(5, 'update'))
                                        <a href="#" class="btn btn-primary btn-rounded btn-hdr edit-patient-btn"
                                            style="color:black; margin-right:10px">
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

                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-footer text-right" style="background-color:#87ceb0">
                                <h3 style="float:left" class="text-dark">
                                    <i class="fa fa-info-circle icon-style2 text-white"></i>
                                    <span class="Patient_name"></span>'s Details
                                </h3>
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

                                </ul>

                                <div class="tab-content mt-3" id="patientTabsContent">
                                    <!-- Follow-up Records Tab -->
                                    <div class="tab-pane fade show active" id="followup" role="tabpanel">
                                        <div class="table-responsive-wrapper">
                                            <table class="table table-bordered" id="followupTable">
                                                <thead>
                                                    <tr>
                                                        <th>Doctor </th>
                                                        <th>Treatment</th>
                                                        <th>Date</th>
                                                        <th>Type</th>
                                                        <th>Comments</th>


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
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>

    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css">
    <script src=" https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#settingsTabs a').on('click', function(e) {
                e.preventDefault();
                $(this).tab('show');
            });
        });
        $(document).ready(function() {

            // Fetch user profile
            $.ajax({
                url: '/api/profile',
                method: 'GET',
                headers: {
                    "Authorization": "Bearer " + token
                },
                success: function(response) {
                    if (response.user) {
                        $('.user-name').text(response.user.fullname || 'N/A');
                        $('#role-select').text(response.user.role ? response.user.role.name : 'N/A');
                        $('#user-name-detail').text(response.user.username || 'N/A');
                        $('#user-email').text(response.user.email || 'N/A');
                        $('#user-phone').text(response.user.phone || 'N/A');
                        $('.user-image').attr('src', response.user.profile || '/default-profile.png');

                        // Check if userDetails exists
                        if (response.userDetails) {
                            $('#user-dob').text(response.userDetails.birth_date || 'N/A');
                            $('#user-address').text(response.userDetails.address || 'N/A');
                            $('#user-gender').text(response.userDetails.gender || 'N/A');
                            $('#city').text(response.userDetails.city || 'N/A');
                            $('#state').text(response.userDetails.state || 'N/A');
                            $('#shift').text(response.userDetails.shift || 'N/A');
                            $('#salary').text(response.userDetails.salary || 'N/A');
                        } else {
                            console.log('User details not found');
                        }

                        $("#edit-profile-btn").click(function() {
                            window.location.href = "/profile/edit";
                        });
                    } else {
                        console.log('User not found');
                    }
                },
                error: function(error) {
                    console.log('Error fetching profile:', error);
                    if (error.status === 401) {

                        window.location.href = "{{ route('login') }}";
                    }
                }
            });



            // Change Password Form Submission


            $('#change-password-form').submit(function(e) {
                e.preventDefault();

                let currentPassword = $('#current-password').val();
                let newPassword = $('#new-password').val();
                let confirmPassword = $('#confirm-password').val();


                if (newPassword !== confirmPassword) {
                    $('#logoerrorMessage').text('New password and Confirm password do not match.').show();
                    setTimeout(() => {
                        $('#errorMessage').fadeOut();
                    }, 3000);
                    return;
                }

                $.ajax({
                    url: '/api/change-password',
                    method: 'POST',
                    headers: {
                        "Authorization": "Bearer " + token
                        // 'Accept': 'application/json'
                    },
                    data: {
                        current_password: currentPassword,
                        new_password: newPassword,
                        new_password_confirmation: confirmPassword
                    },
                    success: function() {
                        $('#logosuccessMessage').text('Password updated successfully.').show();
                        $('#logoerrorMessage').hide();
                        $('#change-password-form')[0].reset();

                        setTimeout(() => {
                            $('#successMessage').fadeOut();
                        }, 3000);
                    },
                    error: function(xhr) {
                        let errorMessage = 'Failed to update password. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        $('#logoerrorMessage').text(errorMessage).show();
                        $('#logosuccessMessage').hide();

                        setTimeout(() => {
                            $('#errorMessage').fadeOut();
                        }, 3000);
                    }
                });
            });

        });
        $(document).ready(function() {
            $('#updateClinicForm').on('submit', function(e) {
                e.preventDefault(); // Prevent the default form submission

                // Clear previous messages
                $('#logoerrorMessage').hide().text('');
                $('#logosuccessMessage').hide().text('');

                // Get input values
                var clinicName = $('#update-clinic-name').val();
                var logoInput = $('#update-clinic-logo')[0].files[0];

                let errorMessage = '';

                // Validate clinic name
                if (clinicName.length > 255) {
                    errorMessage += 'Clinic name must not exceed 255 characters.<br>';
                }

                // Validate clinic logo if selected
                if (logoInput) {
                    var allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg'];
                    if (!allowedTypes.includes(logoInput.type)) {
                        errorMessage += 'Logo must be an image (jpg, jpeg, png, gif, webp).<br>';
                    }
                    if (logoInput.size > 2 * 1024 * 1024) {
                        errorMessage += 'Logo size must not exceed 2MB.<br>';
                    }
                }

                if (errorMessage) {
                    $('#logoerrorMessage').html(errorMessage).show();
                    return;
                }

                // Create FormData and proceed with AJAX
                var formData = new FormData(this);

                $.ajax({
                    url: '/api/update-clinic-details',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response) {
                            $('#logosuccessMessage').text(response.message).show();
                            setTimeout(function() {
                                $('#updateModal').modal('hide');
                                location.reload();
                            }, 2000);
                        } else {
                            $('#logoerrorMessage').text('Unexpected error occurred.').show();
                        }
                    },
                    error: function(xhr, status, error) {
                        let msg = 'An error occurred. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        $('#logoerrorMessage').html(msg).show();
                    }
                });
            });
        });



        $(document).ready(function() {
            $('#change-password-form').off('submit').on('submit', function(e) {
                e.preventDefault();

                let currentPassword = $('#current-password').val();
                let newPassword = $('#new-password').val();
                let confirmPassword = $('#confirm-password').val();


                if (newPassword !== confirmPassword) {
                    showMessage('danger', 'New password and Confirm password do not match.');
                    return;
                }

                $.ajax({
                    url: '/api/change-password',
                    method: 'POST',
                    headers: {
                        "Authorization": "Bearer " + token
                    },
                    data: {
                        current_password: currentPassword,
                        new_password: newPassword,
                        new_password_confirmation: confirmPassword
                    },
                    success: function() {
                        showMessage('success', 'Password updated successfully.');
                        $('#change-password-form')[0].reset();
                    },
                    error: function(xhr) {
                        let errorMessage = 'Failed to update password. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        showMessage('danger', errorMessage);
                    }
                });
            });

            function showMessage(type, message) {
                let messageContainer = $('#messageContainer');
                messageContainer.removeClass('d-none alert-success alert-danger')
                    .addClass(`alert-${type}`)
                    .text(message)
                    .fadeIn();

                setTimeout(() => {
                    messageContainer.fadeOut();
                }, 3000);
            }
        });
    </script>


    <!-- patient details -->


    @php
        $patientId =
            Auth::check() && Auth::user()->role->name === 'Patient'
                ? \App\Models\Patients::where('login_patient_id', Auth::id())->value('id')
                : null;
    @endphp

    <script>
        $(document).ready(function() {
            // const speakButton = document.getElementById("speakButton");
            const outputTextarea = document.getElementById("output");
            const LANG = "en-US";

            // Text-to-Speech (TTS)
            const speakButton = document.getElementById("speakButton");
            if (speakButton) {
                speakButton.addEventListener("click", (e) => {
                    e.preventDefault();
                    const text = document.getElementById("output").textContent.trim();
                    if (text === "") {
                        Swal.fire({
                            icon: "warning",
                            title: "No note added!",
                            text: "You haven't added anything to speak.",
                        });
                        return;
                    }
                    const speech = new SpeechSynthesisUtterance(text);
                    speech.lang = LANG;
                    window.speechSynthesis.speak(speech);
                });
            }

        });

        $(document).ready(function() {

            var patientId = "{{ $patientId }}";


            $.ajax({
                url: '/api/patient/' + patientId,
                type: 'GET',
                dataType: 'json',
                headers: {
                    "Authorization": "Bearer " + token
                },
                success: function(data) {
                    // Populate patient details
                    // $('.note').text(data.patient.note ? data.patient.note : "N/A");

                    const notes = data.patient.note;

                    let noteHtml = '';

                    if (Array.isArray(notes) && notes.length > 0) {
                        notes.forEach(note => {
                            if (note.type === 'speech') {
                                noteHtml += `<div class="note-item speech-note">
                                                                                                            <p><i class="fas fa-comment icon-style1"></i>Note</p>
                                                                                                            <p> ${note.content ? note.content.charAt(0).toUpperCase() + note.content.slice(1) : 'N/A'}</p>
                                                                                                        </div>`;
                            } else if (note.type === 'audio') {
                                noteHtml += `<div class="note-item audio-note">
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


                    // Show symptoms (as list or joined string)
                    let symptoms = data.symptoms;

                    if (typeof symptoms === 'object' && symptoms !== null) {
                        // Get the values from the symptoms object and join them as a comma-separated list
                        let symptomNames = Object.values(symptoms).map(symptom => symptom.charAt(0)
                            .toUpperCase() + symptom.slice(1)).join(', ');
                        $('#symptoms').text(symptomNames);
                    } else {
                        // If symptoms is not an object, display 'N/A'
                        $('#symptoms').text(symptoms ? symptoms.charAt(0).toUpperCase() + symptoms
                            .slice(1) : 'N/A');
                    }

                    // Show symptom images
                    if (data.patient.symptom_images && data.patient.symptom_images.length > 0) {
                        const container = $('#symptom_images_container');
                        container.empty();
                        let img = "{{ asset(env('IMAGE_PATH') ?? '/admin/assets/img/img1.png') }}";
                        console.log(img, 'img');

                        data.patient.symptom_images.forEach(function(imagePath) {
                            // const fullUrl = img + imagePath;
                            const fullUrl =
                                `${img.replace(/\/$/, '')}/${imagePath.replace(/^\//, '')}`;
                            container.append(`
                                                <img src="${fullUrl}" alt="Symptom Image" width="50" height="50" style="border-radius:10px; object-fit:cover;">
                                            `);
                        });
                    }

                    $('.Patient_name').text(data.patient.fullname ? data.patient.fullname.charAt(0)
                        .toUpperCase() + data.patient.fullname.slice(1) : "N/A");
                    $('#email').text(data.patient.email ? data.patient.email : "N/A");
                    $('#treatment').text(data.patient.treatment_name ? data.patient.treatment_name
                        .charAt(0).toUpperCase() + data.patient.treatment_name.slice(1) : "N/A");
                    $('#phone').text(data.patient.phone ? data.patient.phone : "N/A");
                    $('#address').text(data.patient.address ? data.patient.address.charAt(0)
                        .toUpperCase() + data.patient.address.slice(1) : "N/A");
                    $('#city').text(data.patient.city ? data.patient.city.charAt(0).toUpperCase() + data
                        .patient.city.slice(1) : "N/A");
                    $('#state').text(data.patient.state ? data.patient.state.charAt(0).toUpperCase() +
                        data.patient.state.slice(1) : "N/A");
                    $('#age').text(data.patient.age ? data.patient.age : "N/A");
                    $('#bloodgroup').text(data.patient.blood_group ? data.patient.blood_group : "N/A");
                    $('#medical_history').text(data.patient.medical_history ? data.patient
                        .medical_history.charAt(0).toUpperCase() + data.patient.medical_history
                        .slice(1) : "N/A");
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
                                                                                                                                    <td>${followup.treatment ? ucfirst(followup.treatment.name) : "N/A"}</td>
                                                                                                                                    <td>${followup.date || "N/A"}</td>
                                                                                                                                    <td>${followup.followup_type || "N/A"}</td>
                                                                                                                                    <td>
                                                                                                                                        ${followup.followup_update
                                    ? followup.followup_update.charAt(0).toUpperCase() + followup.followup_update.slice(1)
                                    : "No comments"}
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
                                'completed': 'completed', // ? corrected
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
                            "destroy": true // ? Important: destroy previous DataTable instance if any
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
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
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
                                    location
                                .reload(); // Reload the page to update the list
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

        });
    </script>

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

        .experience-list {
            list-style-type: none;
            padding-left: 0;
        }

        .experience-list li {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            position: relative;
        }

        .experience-user .before-circle {
            width: 12px;
            /* Increased size of the dot */
            height: 12px;
            /* Increased size of the dot */
            background-color: #4caf50;
            border-radius: 50%;
            position: absolute;
            left: 0;
            /* No margin to the left */
            top: 50%;
            /* Align the dot vertically */
            transform: translateY(-50%);
            /* Ensure it's centered vertically */
        }

        .experience-content {
            flex: 1;
            padding-left: 20px;
            /* Space for the line to go through the dot */
            position: relative;
        }

        .experience-content::before {
            content: "";
            position: absolute;
            left: -5px;
            /* Align with the dot */
            top: 50%;
            width: 1px;
            /* Line thickness */
            height: 100%;
            /* Full height of the item */
            background-color: #4caf50;
            /* Green line color */
            transform: translateY(-50%);
            /* Center the line vertically */
        }

        .timeline-content a {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }

        .timeline-content .time {
            display: block;
            font-size: 14px;
            color: #888;
            margin-top: 5px;
        }

        .card-body {
            padding: 20px;
        }
    </style>

@endsection
