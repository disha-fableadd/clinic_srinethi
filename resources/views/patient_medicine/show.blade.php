@extends('layout.app')

<style>
    .category-title {
        padding-left: 140px !important;
        text-align: center !important;
    }

    .category-button {
        padding-right: 15px !important;
        text-align: center !important;
    }
    .card-footer{
        background-color:#87ceb0 !important;
    }


    @media screen and (max-width:767px) {
        .category-title {
            padding-left: 15px !important;
            text-align: left !important;
        }

        .category-button {
            padding-right: 15px !important;
            text-align: right !important;
        }

        .page-title {
            font-size: 18px !important;
            margin-top: 10px !important;
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
                            <h3 style="float:left" class="text-dark">
                                <i class="fa fa-info-circle icon-style2"></i>
                                <span class="">Patient Medicine Details</span>
                            </h3>

                            <div class="d-flex text-right" style="justify-content: end;">
                                <div class="mr-2 m-b-2">
                                    <a href="javascript:void(0)" class="btn btn-primary btn-rounded btn-hdr download-medicine"
                                        data-id="{{ $patientmedicine_id}}">
                                        <i class="fa fa-download"></i> <span class="hdr-btn-text">PDF</span>
                                    </a>
                                </div>
                                @if (app('hasPermission')(17, 'view'))
                                    <div class="text-right m-b-2">
                                        <a href="{{ route('patient_medicine.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                                            <i class="fa fa-arrow-left"></i> <span class="hdr-btn-text">Back</span></a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="card-body mt-3">





                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="row">
                                        <div class="col-lg-6 col-4 text-center margin-img">
                                            <p class="text-dark">
                                                <!-- <strong><i class="fa fa-image icon-style1 "></i> Profile: </strong><br> -->
                                                <img id="Patient_image" class="userProfile" src="" width="180" height="180"
                                                    alt="Patient Image" style="border-radius:20px">
                                            </p>
                                        </div>
                                        <div class="col-lg-6 col-8">
                                            <p class="text-dark margin-text">
                                                <strong><i class="fa fa-id-badge icon-style1"></i> Patient Name:
                                                </strong>
                                                <span class="Patient_name"></span>
                                            </p>

                                            <hr class="margin-text">

                                            <p class="text-dark margin-text">
                                                <strong><i class="fas fa-medkit icon-style1"></i> Treatment:
                                                </strong>
                                                <span id="treatment"></span>
                                            </p>
                                            <hr class="margin-text">
                                            <p class="text-dark margin-text">
                                                <strong><i class="fas fa-phone icon-style1"></i> Phone: </strong>
                                                <span id="phone"></span>
                                            </p>


                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <p class="text-dark margin-text">
                                        <strong><i class="fas fa-birthday-cake icon-style1"></i> Age:
                                        </strong>
                                        <span id="age"></span>
                                    </p>
                                    <hr class="margin-text">
                                    <p class="text-dark margin-text">
                                        <strong><i class="fas fa-tint icon-style1"></i> Blood Group:
                                        </strong>
                                        <span id="bloodgroup"></span>
                                    </p>
                                    <hr class="margin-text">
                                    <p class="text-dark margin-text">
                                        <strong><i class="fas fa-notes-medical icon-style1"></i> Medical
                                            History:
                                        </strong>
                                        <span id="medical_history"></span>
                                    </p>

                                </div>

                            </div>

                            <div id="medicine_details_container"></div>







                            <div class="button mb-4" style="display: flex; justify-content: end; margin: 0 5px;">
                                @if (app('hasPermission')(17, 'update'))
                                    <a href="" class="btn btn-primary btn-rounded btn-hdr edit-patientmedicine-btn"
                                        style="color:black; margin-right:10px">
                                        <i class="fa fa-pencil-alt"></i> <span class="hdr-btn-text">Edit</span>
                                    </a>
                                @endif
                                @if (app('hasPermission')(17, 'delete'))
                                    <button type="button" class="btn btn-danger btn-rounded btn-hdr delete-patientMedicine"
                                        style="border-radius:50px" data-id="{{ $patientmedicine_id }}">
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

        $(document).on('click', '.download-medicine', function () {
            const id = $(this).data('id');

            $.ajax({
                url: `/api/medicines/${id}/download`,
                type: 'GET',
                xhrFields: {
                    responseType: 'blob' // so file download works
                },
                success: function (data, status, xhr) {
                    // ✅ Create a download link dynamically
                    const blob = new Blob([data], { type: xhr.getResponseHeader('Content-Type') });
                    const link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = `medicine_${id}.pdf`;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                },
                error: function (xhr) {
                    if (xhr.status === 404) {
                        Swal.fire({
                            icon: 'error',
                            title: 'File Not Found',
                            text: 'The requested medicine PDF does not exist.',
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Download Failed',
                            text: 'Something went wrong while downloading the file.',
                        });
                    }
                }
            });
        });
        $(document).ready(function () {
            var patientMedicineId = {{ $patientmedicine_id ?? 'null' }};

            if (patientMedicineId !== 'null') {
                $.ajax({
                    url: "/api/patient-medicines/" + patientMedicineId,
                    method: "GET",
                    success: function (data) {
                        if (data.patient) {
                            const originalPath = data.patient.profile || '/default-avatar.jpg';
                            const fallbackPath = '/public/uploads/patients/' + (data.patient.profile ?
                                data.patient.profile.split('/').pop() : 'default-avatar.jpg');

                            // Set initial image path
                            $('#Patient_image')
                                .attr('src', originalPath)
                                .on('error', function () {
                                    // If image fails to load, set fallback path
                                    $(this).attr('src', fallbackPath);
                                });

                            function ucfirst(str) {
                                return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
                            }

                            $('.Patient_name').text(ucfirst(data.patient.fullname) || "N/A");
                            $('#treatment').text(ucfirst(data.patient.treatment_name) || "N/A");
                            $('#phone').text(data.patient.phone || "N/A");
                            $('#age').text(data.patient.age || "N/A");
                            $('#bloodgroup').text(ucfirst(data.patient.blood_group) || "N/A");
                            $('#medical_history').text(ucfirst(data.patient.medical_history) || "N/A");
                        }










                        $('#medicine_details_container').empty();

                        if (data.medicines && data.medicines.length) {
                            data.medicines.forEach(function (medicine) {
                                let medicineHtml = `
                                                                    <div class="row mb-3 border-top pt-3 mt-3">


                                                                    <div class="col-lg-8">
                                                                        <div class="row">
                                                                            <div class="col-lg-6 col-4 text-center margin-img">

                                                                                    <img src="${medicine.image}" class="userProfile" width="150" height="150" alt="Medicine Image" style="border-radius:20px">
                                                                                </p>
                                                                            </div>
                                                                            <div class="col-lg-6 col-8">
                                                                                <p class="margin-text"><strong>Medicine: </strong> ${ucfirst(medicine.name)}</p>
                                                                                <hr class="margin-text">
                                                                                <p class="margin-text"><strong>Code: </strong> ${medicine.pivot_code || 'N/A'}</p>
                                                                                <hr class="margin-text">
                                                                                <p class="margin-text"><strong>Value: </strong> ${medicine.pivot_value || 'N/A'}</p>
                                                                                <hr class="margin-text">
                                                                                <p class="margin-text"><strong>Value Type: </strong> ${medicine.pivot_value_type || 'N/A'}</p>
                                                                                <hr class="margin-text">
                                                                                    <p class="margin-text"><strong>Quantity: </strong> ${medicine.quantity}</p>
                                                                                <hr class="margin-text">
                                                                               
                                                                               
                                                                                <p class="margin-text"><strong>Price: </strong> ${medicine.unit}</p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-4">
                                                                            <p class="margin-text"><strong>Status: </strong> ${ucfirst(medicine.status)}</p>
                                                                        <hr class="margin-text">
                                                                        <p class="margin-text"><strong>Description: </strong> ${ucfirst(medicine.description)}</p>
                                                                        <hr class="margin-text">
                                                                        <p class="margin-text"><strong>Mfg Date: </strong> ${medicine.manufacture_date}</p>
                                                                        <hr class="margin-text">
                                                                        <p class="margin-text"><strong>Expiry Date: </strong> ${medicine.expiry_date}</p>
                                                                         <hr class="margin-text">
                                                                          <p class="margin-text"><strong>Category: </strong> ${ucfirst(medicine.category.name)}</p>
                                                                           <hr class="margin-text">


                                                                    </div>
                                                                    </div>
                                                                `;
                                $('#medicine_details_container').append(medicineHtml);
                            });
                        } else {
                            $('#medicine_details_container').append("<p>No medicines available.</p>");
                        }

                        $(".edit-patientmedicine-btn").attr("href", "/patientmedicine/edit/" +
                            patientMedicineId);
                    },
                    error: function (xhr, status, error) {
                        alert("An error occurred while fetching data: " + error);
                    }
                });
            }


            $(document).on('click', '.delete-patientMedicine', function () {
                var patientmedicineId = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to delete Patientmedicine!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/api/patient-medicines/' + patientmedicineId,
                            type: 'DELETE',
                            // //headers: { "Authorization": "Bearer " + token },
                            success: function (response) {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'Patientmedicine deleted successfully!',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.href =
                                        "{{ route('patient_medicine.index') }}";

                                });
                            },
                            error: function (xhr) {
                                Swal.fire('Error', xhr.responseJSON?.message ||
                                    'Failed to delete Patientmedicine. Please try again.',
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
            /* background-color: white; */
            color: white;
            padding: 5px;
            font-size: 20px;
            border-radius: 50%;
        }
    </style>
@endsection