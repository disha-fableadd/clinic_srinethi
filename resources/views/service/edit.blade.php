@extends('layout.app')
<style>
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

    .form-container {
        width: 60% !important;
        padding-bottom: 60px !important;
    }
</style>

@section('content')
    <div class="page-wrapper">
        <div class="content" style="height:100vh;">
            <div class="row" style="padding-top:15px">
                <div class="col-sm-6 col-6">
                    <h4 class="page-title service-text" style="text-align:center;padding-left: 170px;">Edit Service</h4>
                </div>
                @if (app('hasPermission')(8, 'view'))
                    <div class="col-sm-6 col-6 service-btn" style=" padding-right: 143px ;">
                        <a href="{{ route('service.index') }}" class="btn btn-primary btn-rounded btn-hdr view-service ">
                            <i class="fa fa-arrow-left m-r-5 icon3"></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    </div>
                @endif
            </div>
            <div class="row mt-2">
                <div class="offset-lg-2">
                    <form class="form-container" id="editServiceForm">

                        <!-- Select Patient -->
                        <div class="form-group">
                            <label><i class="fas fa-user icon-style"></i> Select Patient <span
                                                        class="text-danger">*</span></label>
                            <select class="form-control select2" name="patient_id" id="edit_patientDropdown" required>
                                <option value="">Select Patient</option>
                            </select>
                        </div>

                        <!-- Department -->
                        <div class="form-group">
                            <label><i class="fas fa-building icon-style"></i> Department <span
                                                        class="text-danger">*</span></label>
                            <select class="form-control" name="department" id="edit_department" required>
                                <option value="">Select Department</option>
                                <option value="Pathology">Pathology</option>
                                <option value="Radiology">Radiology</option>
                            </select>
                        </div>
                        <input type="hidden" name="service_id" id="service_id" value="">

                        <!-- Service -->
                        <div class="form-group">
                            <label><i class="fas fa-cogs icon-style"></i> Select Service <span
                                                        class="text-danger">*</span></label>
                            <select class="form-control select2" name="service_id" id="edit_service_id" required>
                                <option value="">Select Service</option>
                            </select>
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label><i class="fas fa-align-left icon-style"></i> Description <span
                                                        class="text-danger">*</span></label>
                            <textarea cols="30" rows="4" class="form-control" name="description" id="edit_description"
                                style="border-radius:10px" required></textarea>
                        </div>

                        <input type="hidden" id="edit_id" name="id">

                        <div id="editserviceSuccessMessage" class="alert alert-success" style="display:none;"></div>
                        <div id="editserviceErrorMessage" class="alert alert-danger" style="display:none;"></div>

                        <div class="m-t-20 text-center">
                            <button type="submit" class="btn btn-primary submit-btn">Update Service</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            let branchId = localStorage.getItem("selectedBranchId");
            let serviceId = "{{ $service_id }}";
            let token = localStorage.getItem("token");

            function initSelect2(selector, placeholder) {
                $(selector).select2({
                    placeholder: placeholder,
                    allowClear: true,
                    width: '100%'
                }).on('select2:open', function() {
                    $('.select2-search__field').attr('placeholder', placeholder.replace("Select",
                    "Search"));
                });
            }

            initSelect2('#edit_patientDropdown', "Select Patient");
            initSelect2('#edit_department', "Select Department");
            initSelect2('#edit_service_id', "Select Service");

            // Load patients by branch
            $.ajax({
                url: "/api/patientss",
                type: "GET",
                dataType: "json",
                data: {
                    branch_id: branchId
                },
                success: function(response) {
                    let patientDropdown = $('#edit_patientDropdown');
                    patientDropdown.empty().append('<option value="">Select Patient</option>');

                    if (response.patients && response.patients.length > 0) {
                        $.each(response.patients, function(index, patient) {
                            const capitalizedName = patient.fullname.charAt(0).toUpperCase() +
                                patient.fullname.slice(1);
                            patientDropdown.append(
                                `<option value="${patient.id}" data-treatment-id="${patient.treatment_id}">${capitalizedName}</option>`
                                );
                        });
                    }
                },
                error: function(xhr) {
                    console.error("API Error:", xhr.status, xhr.responseText);
                }
            });

            // Department change → load services
            $('#edit_department').change(function() {
                var department = $(this).val();
                var selectedServiceId = $("#service_id").val() || "";

                $('#edit_service_id').empty().append('<option value="">Loading...</option>');

                if (department !== '') {
                    $.ajax({
                        url: '/api/get-services-by-department',
                        method: 'GET',
                        data: {
                            department: department,
                            branch_id: branchId
                        },
                        success: function(response) {
                            $('#edit_service_id').empty().append(
                                '<option value="">Select Service</option>');
                            if (response.length > 0) {
                                $.each(response, function(key, service) {
                                    let isSelected = (service.id == selectedServiceId) ?
                                        'selected' : '';
                                    $('#edit_service_id').append(
                                        `<option value="${service.id}" ${isSelected}>${service.test_name}</option>`
                                        );
                                });
                                $('#edit_service_id').val(selectedServiceId).trigger('change');
                            } else {
                                $('#edit_service_id').append(
                                    '<option value="">No services found</option>');
                            }
                        },
                        error: function() {
                            $('#edit_service_id').empty().append(
                                '<option value="">Error fetching services</option>');
                        }
                    });
                } else {
                    $('#edit_service_id').empty().append(
                        '<option value="">Select Department First</option>');
                }
            });

            // Form validation
            $('#editServiceForm').validate({
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
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                }
            });

            // Load service details
            if (serviceId && !isNaN(serviceId)) {
                $.ajax({
                    url: "/api/services/" + serviceId,
                    type: "GET",
                    headers: {
                        "Authorization": "Bearer " + token
                    },
                    success: function(service) {
                        $('#edit_patientDropdown').val(service.patient_id).trigger('change');
                        $('#edit_description').val(service.description);
                        $('#edit_department').val(service.department).trigger('change');

                        let selectedServiceId = "";
                        if (service.department === "Pathology" && service.pathelogy_service) {
                            selectedServiceId = String(service.pathelogy_service.id);
                        } else if (service.department === "Radiology" && service.radiology_service) {
                            selectedServiceId = String(service.radiology_service.id);
                        }
                        $("#service_id").val(selectedServiceId);
                    },
                    error: function() {
                        alert("Failed to fetch service details.");
                    }
                });
            } else {
                alert("Error: Service ID is missing or invalid.");
            }

            // Handle form submission
            $("#editServiceForm").submit(function(event) {
                event.preventDefault();
                if ($('#editServiceForm').valid()) {
                    let serviceData = {
                        branch_id: branchId,
                        patient_id: $("#edit_patientDropdown").val(),
                        department: $("#edit_department").val(),
                        service_id: $("#edit_service_id").val(),
                        description: $("#edit_description").val(),
                    };

                    $.ajax({
                        url: "/api/services/" + serviceId,
                        type: "PUT",
                        data: JSON.stringify(serviceData),
                        contentType: "application/json",
                        success: function() {
                            $("#editserviceSuccessMessage").text(
                                    "Service updated successfully!")
                                .fadeIn().delay(3000).fadeOut();
                            setTimeout(function() {
                                window.location.href = "{{ route('service.index') }}";
                            }, 2000);
                        },
                        error: function(xhr) {
                            var errorMessage = '';
                            if (xhr.status === 422 && xhr.responseJSON.errors) {
                                $.each(xhr.responseJSON.errors, function(key, messages) {
                                    errorMessage += messages[0] + '<br>';
                                });
                            } else {
                                errorMessage = "An unexpected error occurred.";
                            }
                            $('#editserviceErrorMessage').html(errorMessage).show();
                        }
                    });
                }
            });
        });
    </script>
@endsection
