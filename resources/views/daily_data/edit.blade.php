@extends('layout.app')

<style>
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
        /* padding: 2px 10px !important; */
        border: none;
        font-size: 14px;
        font-weight: normal !important;
        text-align: center;
        max-width: fit-content;
    }




    .daily_data-title {
        padding-left: 145px !important;
        text-align: center !important;
    }

    .daily_data-button {
        padding-right: 8px !important;
        text-align: center !important;
    }

    @media screen and (max-width:768px) {
        .daily_data-title {
            padding-left: 15px !important;
            text-align: left !important;
        }

        .daily_data-button {
            padding-right: 15px !important;
            text-align: right !important;
        }

    }

    @media screen and (max-width:767px) {
        .daily_data-title {
            padding-left: 15px !important;
            text-align: left !important;
        }

        .daily_data-button {
            padding-right: 15px !important;
            text-align: right !important;
        }

        .daily_data-form {
            height: 720px !important;
        }

    }
</style>

@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="row" style="padding-top:15px">
                <div class=" col-6">
                    <h4 class="page-title  daily_data-title">Edit Daily Register</h4>
                </div>
                @if (app('hasPermission')(30, 'view'))
                    <div class=" col-6 m-b-2 eye-btn daily_data-button">
                        <a href="{{ route('daily_data.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                            <i class="fa fa-arrow-left m-r-5 icon3  "></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    </div>
                @endif
            </div>




            <div class="row mt-4">
                <div class="col-12">
                    <form id="editdaily_dataForm" method="POST" class="form-container all-form daily_data-form"
                        style="width: 60%;">

                        @csrf

                        <div class="row">

                            <!-- Patient Name -->
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <div class="d-flex align-items-center justify-content-between">


                                        <label class="mr-2"><i class="fas fa-medkit icon-style"></i> Patient <span
                                                class="text-danger">*</span></label>

                                        @if (app('hasPermission')(30, 'create'))
                                            <a href="{{ route('patients.create') }}" target="_blank"
                                                class="btn btn-primary btn-sm">
                                                <i class="fas fa-plus"></i> <span class="hdr-btn-text">Add</span>
                                            </a>
                                        @endif

                                    </div>
                                    <select class="form-control select2" name="patient_id" id="patientDropdown" required>
                                        <option value="">Select Patient</option>
                                    </select>


                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">

                                    <div class="d-flex align-items-center justify-content-between">


                                        <label class="mr-2"><i class="fas fa-medkit icon-style"></i> Treatment <span
                                                class="text-danger">*</span></label>

                                        @if (app('hasPermission')(7, 'create'))
                                            <a href="{{ route('treatment.create') }}" target="_blank"
                                                class="btn btn-primary btn-sm">
                                                <i class="fas fa-plus"></i> <span class="hdr-btn-text">Add</span>
                                            </a>
                                        @endif

                                    </div>
                                    <select class="form-control select2" name="treatment_id" id="treatmentDropdown"
                                        required>
                                        <option value="">Select Treatment</option>
                                    </select>

                                </div>
                            </div>



                            <!-- Date -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-calendar-day icon-style"></i> Date <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="date" class="form-control" required>
                                </div>
                            </div>



                            <!-- Status -->
                            <div class="col-md-6 d-none" id="pending_amount_div">
                                <div class="form-group">
                                    <label><i class="fas fa-hourglass-half icon-style"></i> Pending Amount</label>
                                    <input type="number" step="0.01" name="pending_amount" id="pending_amount"
                                        class="form-control" readonly>
                                </div>
                            </div>

                            <!-- By -->
                            <div class="col-md-6">
                                <div class="form-group">

                                    <div class="d-flex align-items-center justify-content-between">


                                        <label class="mr-2"><i class="fas fa-medkit icon-style"></i> Collect By <span
                                                class="text-danger">*</span></label>
                                    </div>
                                    <select class="form-control select2" name="collect_by_id" id="userdropdown" required>
                                        <option value="">Select Staff</option>
                                    </select>


                                </div>



                            </div>
                        </div>
                        <div id="editdaily_datasuccessMessage" class="alert alert-success" style="display:none;"></div>
                        {{-- <div id="editdaily_dataerrorMessage" class="alert alert-danger" style="display:none;"></div> --}}
                        <button type="submit" class="btn btn-primary submit-btn d-block m-auto">
                            Submit
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>



    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
   


<script>
$(document).ready(function() {

    let branchId = localStorage.getItem('selectedBranchId');
    console.log("Selected Branch ID:", branchId); // debug

    function loadPatients() {
        return $.ajax({
            url: "/api/patientss",
            type: "GET",
            dataType: "json",
            data: { branch_id: branchId },
            success: function(response) {
                let patientDropdown = $('#patientDropdown');
                patientDropdown.empty().append('<option value="">Select Patient</option>');
                $.each(response.patients || [], function(_, patient) {
                    const capitalizedName = patient.fullname.replace(/\b\w/g, c => c.toUpperCase());
                    patientDropdown.append(`<option value="${patient.id}" data-treatment-id="${patient.treatment_id}">${capitalizedName}</option>`);
                });
                patientDropdown.select2({ placeholder: "Select Patient", allowClear: true, width: '100%' });
                patientDropdown.on('select2:open', function() {
                    $('.select2-search__field').attr('placeholder', 'Search Patient');
                });
            },
            error: function(xhr) {
                console.error("Patients API Error:", xhr.status, xhr.responseText);
            }
        });
    }

    function loadTreatments() {
        return $.ajax({
            url: "/api/treatments",
            type: "GET",
            data: { branch_id: branchId },
            success: function(data) {
                let treatmentDropdown = $('select[name="treatment_id"]');
                treatmentDropdown.empty().append('<option value="">Select Treatment</option>');
                $.each(data.treatments || [], function(_, treatment) {
                    const capitalizedName = treatment.name.replace(/\b\w/g, c => c.toUpperCase());
                    treatmentDropdown.append(`<option value="${treatment.id}" data-price="${treatment.price}" data-doctor-id="${treatment.doctor_id}">${capitalizedName}</option>`);
                });
                treatmentDropdown.select2({ placeholder: "Select Treatment", allowClear: true, width: '100%' });
                treatmentDropdown.on('select2:open', function() {
                    $('.select2-search__field').attr('placeholder', 'Search Treatment');
                });
            },
            error: function(xhr) {
                console.error("Treatments API Error:", xhr.status, xhr.responseText);
            }
        });
    }

   function loadUsers() {
        return $.ajax({
             url: "/api/userss",
           type: "GET",
            data: { branch_id: branchId },
          success: function(data) {
               // console.log("Users API Response:", data); // debug
                let userDropdown = $('select[name="collect_by_id"]');
              userDropdown.empty().append('<option value="">Select Staff</option>');
                $.each(data.users || [], function(_, user) {
                  const capitalizedName = user.fullname.replace(/\b\w/g, c => c.toUpperCase());
                   userDropdown.append(`<option value="${user.id}">${capitalizedName}</option>`);
                });
              userDropdown.select2({ placeholder: "Select Staff", allowClear: true, width: '100%' });
                userDropdown.on('select2:open', function() {
                     $('.select2-search__field').attr('placeholder', 'Search Staff');
                });
          },
            error: function(xhr) {
                console.error("Users API Error:", xhr.status, xhr.responseText);
            }
        });
    }


     $.ajax({
                url: "/api/userss",
                type: "GET",
                success: function(data) {
                    let userdropdown = $('select[name="collect_by_id"]');
 
 
                    if (userdropdown.length === 0) {
                        console.log("Dropdown not found! Check your HTML.");
                        return;
                    }
 
                    userdropdown.empty();
                    userdropdown.append('<option value="">Select Staff</option>');
 
                    let staffs = data.users || [];
 
                    $.each(data.users || [], function(_, user) {
                        // Capitalize first letter of each word
                        const capitalizedName = user.fullname.replace(/\b\w/g, function(char) {
                            return char.toUpperCase();
                        });
 
                        userdropdown.append(
                            `<option value="${user.id}">${capitalizedName}</option>`
                        );
                    });
 
                    // Initialize Select2 plugin
                    userdropdown.select2({
                        placeholder: "Select staff",
                        allowClear: true,
                        width: '100%'
                    });
 
                    // Set search input placeholder after opening dropdown
                    userdropdown.on('select2:open', function() {
                        $('.select2-search__field').attr('placeholder', 'Search staff');
                    });
                },
                error: function(xhr) {
                    console.log("API Error:", xhr.status, xhr.responseText);
                }
            });
 

    var form = $('#editdaily_dataForm');
    form.validate({
        rules: {
            patient_id: "required",
            treatment_id: "required",
            date: "required",
            amount: { required: true, number: true, min: 0 },
            status: "required",
            collect_by_id: "required"
        }
    });

    const dataId = "{{ $daily_data_id }}";

    if (dataId) {
        // Edit mode: load all dropdowns first
        $.when(loadPatients(), loadTreatments(), loadUsers()).done(function() {
            $.ajax({
                url: `/api/daily-data/${dataId}`,
                type: 'GET',
                success: function(response) {
                    let dailyData = response.data;
                    $('#patientDropdown').val(dailyData.patient_id).trigger('change');
                    $('select[name="treatment_id"]').val(dailyData.treatment_id).trigger('change');
                    $('input[name="date"]').val(dailyData.date);
                    $('select[name="collect_by_id"]').val(dailyData.collect_by_id).trigger('change');
                },
                error: function() {
                    $('#editdaily_dataerrorMessage').text("Failed to load daily data.").fadeIn();
                }
            });
        });
    } else {
        // Create mode
        loadPatients();
        loadTreatments();
        loadUsers();
    }

    $('#editdaily_dataForm').on('submit', function(e) {
        e.preventDefault();
        const formData = {
            patient_id: $('#patientDropdown').val(),
            treatment_id: $('select[name="treatment_id"]').val(),
            date: $('input[name="date"]').val(),
            amount: $('input[name="amount"]').val(),
            status: $('select[name="status"]').val(),
            collect_by_id: $('select[name="collect_by_id"]').val(),
            branch_id: branchId
        };

        $.ajax({
            url: `/api/daily-data/${dataId}`,
            type: 'PUT',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            success: function(response) {
                $('#editdaily_datasuccessMessage').text(response.message).fadeIn();
                setTimeout(() => window.location.href = "{{ route('daily_data.index') }}", 1500);
            },
            error: function(xhr) {
                let errorMsg = 'Something went wrong.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMsg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                }
                $('#editdaily_dataerrorMessage').html(errorMsg).fadeIn();
            }
        });
    });

});
</script>


@endsection
