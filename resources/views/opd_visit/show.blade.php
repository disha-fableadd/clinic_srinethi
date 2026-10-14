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
                <div class="col-md-12  col-sm-12 col-lg-12">
                    <div class="card">
                        <div class="card-footer text-right">
                            <h3 style="float:left" class="text-dark">
                                <i class="fa fa-stethoscope icon-style2"></i>
                                <span class="service_name"></span> OPD Details
                            </h3>


                            <div class="d-flex text-right" style="justify-content: end;">

                                <div class="mr-2 m-b-2">
                                    <a href="javascript:void(0)" class="btn btn-primary btn-rounded btn-hdr download-opd"
                                        data-id="{{ $id}}">
                                        <i class="fa fa-download"></i> <span class="hdr-btn-text">PDF</span>
                                    </a>
                                </div>


                                @if (app('hasPermission')(23, 'view'))
                                    <div class="text-right m-b-2">
                                        <a href="{{ route('opd_visit.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                                            <i class="fa fa-arrow-left"></i> <span class="hdr-btn-text">Back</span>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="card-body mt-3">
                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="row">
                                        <div class="col-lg-6 col-4 text-center margin-img">
                                            <p class="text-dark margin-text">
                                                <img id="Patient_image" class="userProfile" src="" width="200" height="200"
                                                    alt="Patient Image" style="border-radius:20px"
                                                    onerror="this.onerror=null;this.src='{{ asset(env('IMAGE_PATH') . 'admin/assets/img/img1.png') }}';">
                                            </p>
                                        </div>
                                        <div class="col-lg-6 col-8">

                                            <p class="text-dark margin-text">
                                                <strong><i class="fas fa-user-injured icon-style1"></i> Patient Name:
                                                </strong>
                                                <span id="patient_name"></span>
                                            </p>
                                            <hr class="margin-text">

                                            <p class="text-dark margin-text">
                                                <strong><i class="fas fa-user-md icon-style1"></i> Doctor Name: </strong>
                                                <span id="doctor_name"></span>
                                            </p>
                                            <hr class="margin-text">

                                            <p class="text-dark margin-text">
                                                <strong><i class="fas fa-calendar-day icon-style1"></i> Visit Date:
                                                </strong>
                                                <span id="visit_date"></span>
                                            </p>
                                            <hr class="margin-text">

                                            <p class="text-dark margin-text">
                                                <strong><i class="fas fa-stethoscope icon-style1"></i> Chief Complaint:
                                                </strong>
                                                <span id="chief_complaint"></span>
                                            </p>


                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <p class="text-dark margin-text">
                                        <strong><i class="fas fa-notes-medical icon-style1"></i> Diagnosis: </strong>
                                        <span id="diagnosis"></span>
                                    </p>
                                    <hr class="margin-text">
                                    <p class="text-dark margin-text">
                                        <strong>₹ Consultation Fees:
                                        </strong>
                                        <span id="consultation_fees"></span>
                                    </p>
                                    <hr class="margin-text">
                                    <p class="text-dark margin-text">
                                        <strong><i class="fas fa-prescription icon-style1"></i> Prescription: </strong>
                                        <span id="prescription"></span>
                                    </p>
                                    <hr class="margin-text">
                                    <p class="text-dark margin-text">
                                        <strong><i class="fas fa-tint icon-style1"></i> Status: </strong>
                                        <span id="status"></span>
                                    </p>
                                </div>


                            </div>
                            <div class="button mb-4" style="display: flex; justify-content: end; margin: 0 5px;">
                                @if (app('hasPermission')(23, 'update'))
                                    <a href="#" class="btn btn-primary btn-rounded btn-hdr edit-opd-btn"
                                        style="color:black; margin-right:10px">
                                        <i class="fa fa-pencil-alt"></i> <span class="hdr-btn-text">Edit</span>
                                    </a>
                                @endif
                                @if (app('hasPermission')(23, 'delete'))
                                    <button type="button" class="btn btn-danger btn-rounded btn-hdr delete-opd" data-id="{{ $id }}">
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
        $(document).ready(function () {
            const visitId = "{{ $id }}"; // passed from blade

            $.ajax({
                url: `/api/opd/edit-show/${visitId}`,
                method: 'GET',
                success: function (response) {
                    if (response.status) {
                        const data = response.data;

                        $('#patient_name').text(data.patient?.fullname || 'N/A');
                        $('#doctor_name').text(data.doctor?.fullname || 'N/A');
                        $('#visit_date').text(new Date(data.visit_date).toLocaleDateString());
                        $('#consultation_fees').text(data.consultation_fees ?? '0');
                        $('#chief_complaint').text(data.chief_complaint || 'N/A');
                        $('#diagnosis').text(data.diagnosis || 'N/A');

                        // Prescription is an array
                        if (Array.isArray(JSON.parse(data.prescription))) {
                            let prescriptionHtml = JSON.parse(data.prescription).join(', ');
                            $('#prescription').text(prescriptionHtml);
                        } else {
                            $('#prescription').text('N/A');
                        }

                        $('#status').text(data.status || 'N/A');

                        $(".edit-opd-btn").attr("href", "/opd/edit/" + data.id);

                        // 👇 SET patient image
                        const imagePath = data.patient?.profile ?
                            ` ${data.patient.profile}` :
                            '';

                        $('#Patient_image').attr('src', imagePath);
                    } else {
                        $('#errorMessage').text('Visit not found.').show();
                    }
                },
                error: function () {
                    $('#errorMessage').text('Something went wrong.').show();
                }
            });

              $(document).on('click', '.download-opd', function () {
                const id = $(this).data('id');
                window.location.href = `/api/opds/${id}/download`;
            });


            $(document).on('click', '.delete-opd', function () {
                var opdId = $(this).data('id');

                if (!opdId) {
                    Swal.fire('Error', 'opdId ID is missing!', 'error');
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
                            url: '/api/delete-opd-visit/' + opdId,
                            type: 'DELETE',
                            success: function (response) {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'OPD deleted successfully!',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.href =
                                        "{{ route('opd_visit.index') }}";
                                });
                            },
                            error: function (xhr) {
                                Swal.fire('Error', 'Failed to delete opd.', 'error');
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
    </style>
@endsection
