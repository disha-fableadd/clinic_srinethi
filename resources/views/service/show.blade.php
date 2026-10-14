@extends('layout.app')

<style>
    .card-footer{
        background-color:#87ceb0 !important;
    }
    @media screen and (max-width:767px) {

        .page-title {
            font-size: 20px !important;

        }
    }
</style>


@section('content')
    <div class="page-wrapper">
        <div class="content" style="height:100vh">
            <div class="row" style="padding-top:15px">



            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-footer text-right">

                            <h3 style="float:left" class="text-dark"><i class="fa fa-info-circle icon-style2"></i> <span
                                    class="service_name"></span> Details
                            </h3>
                            @if (app('hasPermission')(8, 'view'))
                                <div class="text-right m-b-2">
                                    <a href="{{ route('service.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                                        <i class="fa fa-arrow-left"></i> <span class="hdr-btn-text">Back</span>
                                    </a>
                                </div>
                            @endif

                        </div>

                        <div class="card-body mt-3">


                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="row">
                                        <div class="col-lg-6 col-4 margin-img">
                                            <p class="text-dark">
                                                <img id="Patient_image" class="userProfile" src="" width="180"
                                                    height="180" alt="Patient Image" style="border-radius:20px">
                                            </p>
                                        </div>
                                        <div class="col-lg-6 col-8">
                                            <p class="text-dark margin-text">
                                                <strong><i class="fas fa-user icon-style1"></i> Patient Name:</strong>
                                                <span class="Patient_name"></span>
                                            </p>
                                            <hr class="margin-text">

                                            <p class="text-dark margin-text">
                                                <strong><i class="fas fa-building icon-style1"></i> Department:</strong>
                                                <span id="depaartment"></span>
                                            </p>
                                            <hr class="margin-text">

                                            <p class="text-dark margin-text">
                                                <strong><i class="fas fa-stethoscope icon-style1"></i> Service:</strong>
                                                <span id="service"></span>
                                            </p>
                                            <hr class="margin-text">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <p class="text-dark margin-text">
                                        <strong><i class="fas fa-vial icon-style1"></i> Test Code:</strong>
                                        <span id="test_code"></span>
                                    </p>
                                    <hr class="margin-text">

                                    <p class="text-dark margin-text">
                                        <strong>₹ Cost:</strong>
                                        <span id="cost"></span>
                                    </p>
                                    <hr class="margin-text">
                                </div>
                            </div>

                            <div class="button mb-4" style="display: flex; justify-content: end; margin: 0 5px;">
                                @if (app('hasPermission')(8, 'update'))
                                    <a href="#" class="btn btn-primary btn-rounded btn-hdr edit-service-btn"
                                        style="color:black; margin-right:10px">
                                        <i class="fa fa-pencil-alt"></i> <span class="hdr-btn-text">Edit</span>
                                    </a>
                                @endif
                                @if (app('hasPermission')(8, 'delete'))
                                    <button type="button" class="btn btn-danger btn-rounded btn-hdr delete-service"
                                        data-id="{{ $service_id }}">
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
        $(document).ready(function() {
            var serviceId = "{{ $service_id }}";

            $.ajax({
                url: '/api/services/' + serviceId,
                type: 'GET',
                dataType: 'json',
                headers: {
                    "Authorization": "Bearer " + token
                },
                success: function(service) {
                    function ucfirst(str) {
                        return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
                    }

                    // Patient name
                    $('.Patient_name').text(ucfirst(service.patient?.fullname || 'N/A'));

                    // Patient image (if available)
                    $('#Patient_image').attr('src', service.patient?.profile || '/default/patient.png');

                    // Department
                    $('#depaartment').text(ucfirst(service.department || 'N/A'));

                    // Service Details based on department
                    if (service.department === "Pathology" && service.pathelogy_service) {
                        $('#service').text(ucfirst(service.pathelogy_service.test_name));
                        $('#test_code').text(service.pathelogy_service.test_code);
                        $('#sample_type').text(service.pathelogy_service.sample_type);
                        $('#normal_range').text(service.pathelogy_service.normal_range);
                        $('#cost').text(service.pathelogy_service.cost);
                    } else if (service.department === "Radiology" && service.radiology_service) {
                        $('#service').text(ucfirst(service.radiology_service.test_name));
                        $('#test_code').text(service.radiology_service.test_code);
                        $('#sample_type').text(service.radiology_service.sample_type);
                        $('#normal_range').text(service.radiology_service.normal_range);
                        $('#cost').text(service.radiology_service.cost);
                    } else {
                        $('#service').text('N/A');
                        $('#test_code').text('N/A');
                        $('#sample_type').text('N/A');
                        $('#normal_range').text('N/A');
                        $('#cost').text('N/A');
                    }
                },
                error: function() {
                    alert('Failed to fetch service details.');
                }
            });
        });


        $(document).on('click', '.delete-service', function() {
            var serviceId = $(this).data('id');
            console.log("Deleting Service ID:", serviceId);

            if (!serviceId) {
                Swal.fire('Error', 'Service ID is missing!', 'error');
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
                        url: '/api/services/' + serviceId,
                        type: 'DELETE',
                        success: function(response) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Service deleted successfully!',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = "{{ route('service.index') }}";
                            });
                        },
                        error: function() {
                            Swal.fire('Error', 'Failed to delete service.', 'error');
                        }
                    });
                }
            });
        });


        $(document).ready(function() {
            var pathParts = window.location.pathname.split('/');
            var serviceId = pathParts[pathParts.length - 1];

            console.log("Extracted Service ID from URL:", serviceId);

            if (!serviceId || isNaN(serviceId)) {
                alert("Error: Service ID is missing or invalid.");
                return;
            }


            $.ajax({
                url: "/api/services/" + serviceId,
                type: "GET",
                success: function(service) {

                    $("#name").val(service.name);
                    $("#description").val(service.description);
                    $("#type").val(service.type);
                    $("#price").val(service.price);
                    $("input[name='status'][value='" + service.status + "']").prop("checked", true);
                    $(".edit-service-btn").attr("href", "/service/edit/" + service.id);
                },
                error: function() {
                    alert("Failed to fetch service details.");
                }
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
    </style>
@endsection

