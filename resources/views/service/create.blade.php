@extends('layout.app')

<style>
    .service-btn {
        /* padding-right: 143px !important; */
        text-align: left !important;
    }

    .page-title {

        text-align: center !important;
        padding-left: 0 !important;
    }
    .form-container {
        width: 60% !important;
        padding-bottom: 60px !important;
    }


    @media screen and (max-width:767px) {

        .page-title {
            font-size: 20px !important;
            margin-top: 7px !important;
            padding-left: 0px !important;

        }

        .service-btn {
            text-align: right !important;
            padding-right: 15px !important;

        }
    }

    .service-btn {
        text-align: center;

    }
</style>

@section('content')
    <div class="page-wrapper">
        <div class="content" style="height:100vh;">
            <div class="row" style="padding-top:15px">
                <div class="col-sm-8 col-6">
                    <h4 class="page-title service-text" style="">Add Service</h4>
                </div>
                @if (app('hasPermission')(8, 'view'))
                    <div class="col-sm-4 col-6 service-btn" style=" ">
                        <a href="{{ route('service.index') }}" class="btn btn-primary btn-rounded btn-hdr view-service">
                            <i class="fa fa-arrow-left m-r-5 icon3"></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    </div>
                @endif
            </div>
            <div class="row">
                <div class="offset-lg-2">
                   


                    <form class="form-container" id="createServiceForm">
                        <input type="hidden" name="branch_id" id="branch_id">
                        <!-- Select Patient -->
                        <div class="form-group">
                            <label><i class="fas fa-user icon-style"></i> Select Patient <span
                                                        class="text-danger">*</span></label>
                            <select class="form-control select2" name="patient_id" id="patientDropdown" required>
                                <option value="">Select Patient</option>

                            </select>
                        </div>

                        <!-- Select Department -->
                        <div class="form-group">
                            <label><i class="fas fa-building icon-style"></i> Department <span
                                                        class="text-danger">*</span></label>
                            <select class="form-control" name="department" id="department" required>
                                <option value="">Select Department</option>
                                <option value="Pathology">Pathology</option>
                                <option value="Radiology">Radiology</option>
                            </select>
                        </div>

                        <!-- Select Service -->
                        <div class="form-group">
                            <label><i class="fas fa-cogs icon-style"></i> Select Service <span
                                                        class="text-danger">*</span></label>
                            <select class="form-control select2" name="service_id" id="service_id" required>
                                <option value="">Select Service</option>
                            </select>
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label><i class="fas fa-align-left icon-style"></i> Description <span
                                                        class="text-danger">*</span></label>
                            <textarea cols="30" rows="4" class="form-control" name="description" id="description"
                                style="border-radius:10px" required></textarea>
                        </div>



                        <div id="servicesuccessMessage" class="alert alert-success" style="display:none;"></div>
                        <div id="serviceerrorMessage" class="alert alert-danger" style="display:none;"></div>

                        <div class="m-t-20 text-center">
                            <button type="submit" class="btn btn-primary submit-btn">Create Service</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- Include jQuery Validation Plugin -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>

    <script>
        $(document).ready(function() {
          $('#department').on('change', function () {
    let department = $(this).val();
    let branchId = localStorage.getItem('selectedBranchId'); // ✅ define here

    if (!department || !branchId) return;

    $.ajax({
        url: "/api/get-services-by-department", // or "/get-services-by-department" if in web.php
        type: "GET",
        data: {
            department: department,
            branch_id: branchId
        },
        success: function (services) {
            let serviceDropdown = $('#service_id');
            serviceDropdown.empty().append('<option value="">Select Service</option>');

            if (services.length === 0) {
                serviceDropdown.append('<option value="">No services found</option>');
                return;
            }

            $.each(services, function (index, service) {
                serviceDropdown.append(
                    `<option value="${service.id}">${service.test_name}</option>`
                );
            });

            serviceDropdown.trigger('change'); // refresh select2
        },
        error: function (xhr) {
            console.error("Error fetching services:", xhr.responseText);
        }
    });
});

        });
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let storedBranchId = localStorage.getItem('selectedBranchId');
            if (storedBranchId) {
                document.getElementById('branch_id').value = storedBranchId;
            }
        });
        $('#patientDropdown').select2({
            placeholder: "Select Type",
            width: '100%'
        });
        $('#patientDropdown').on('select2:open', function() {
            $('.select2-search__field').attr('placeholder', 'Search Type');
        });

        $('#service_id').select2({
            placeholder: "Select Type",
            width: '100%'
        });
        $('#service_id').on('select2:open', function() {
            $('.select2-search__field').attr('placeholder', 'Search Type');
        });

        $('#department').select2({
            placeholder: "Select Type",
            width: '100%'
        });
        $('#department').on('select2:open', function() {
            $('.select2-search__field').attr('placeholder', 'Search Type');
        });



        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('toggle_btn');
            const sidebar = document.querySelector('.sidebar');

            toggleBtn.addEventListener('click', function() {
                if (sidebar) {
                    sidebar.classList.toggle('mini-sidebar');
                }
            });
        });


        $(document).ready(function() {

          

            let branchId = localStorage.getItem('selectedBranchId');


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



            // Initialize form validation
            $('#createServiceForm').validate({
                rules: {
                    patient_id: {
                        required: true
                    },
                    department: {
                        required: true
                    },
                    service_id: {
                        required: true
                    },
                    description: {
                        required: true,
                        minlength: 10
                    }
                },
                messages: {
                    patient_id: {
                        required: "Please select a patient"
                    },
                    department: {
                        required: "Please select a department"
                    },
                    service_id: {
                        required: "Please select a service"
                    },
                    description: {
                        required: "Please enter a description",
                        minlength: "Description must be at least 10 characters long"
                    }
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });




            $('#createServiceForm').on('submit', function(e) {
                e.preventDefault();

                if (!$(this).valid()) return;

                const formData = {
                     branch_id: $('#branch_id').val(),
                    patient_id: $('#patientDropdown').val(),
                    department: $('#department').val(),
                    service_id: $('#service_id').val(),
                    description: $('#description').val(),
                };

                $.ajax({
                    url: '/api/services',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#servicesuccessMessage').text(response.message).show();
                        $('#serviceerrorMessage').hide();
                        $('#createServiceForm')[0].reset();
                        setTimeout(function() {
                            window.location.href = "{{ route('service.index') }}";
                        }, 2000);
                    },
                    error: function(xhr) {
                        let msg = 'An error occurred';
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            msg = Object.values(errors).map(e => e.join(', ')).join('<br>');
                        }
                        $('#serviceerrorMessage').html(msg).show();
                        $('#servicesuccessMessage').hide();
                    }
                });
            });

        });
    </script>
@endsection
