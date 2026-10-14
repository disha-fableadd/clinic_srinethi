@extends('layout.app')

<style>
    .discharge-title {
        text-align: center;
    }
    .card-footer{
        background-color:#87ceb0 !important;
    }

    @media screen and (max-width: 767px) {
        .page-title {
            font-size: 19px !important;
            padding-left: 10px !important;
            text-align: left !important;
            padding-top: 6px !important;
        }
    }
</style>

@section('content')
    <div class="page-wrapper">
        <div class="content" style="height:100vh">
            <div class="row" style="padding-top:15px">
                {{-- <div class="col-sm-6 col-8">
                    <h4 class="page-title discharge-title">IPD Admission Details</h4>
                </div> --}}
                {{--  --}}
            </div>

            <div class="row mt-3">
                <div class="col-md-12  col-sm-12 col-lg-12">
                    <div class="card">
                        {{-- <div class="card-footer text-right">
                            <h3 style="float:left" class="text-dark">
                                <i class="fa fa-info-circle icon-style2"></i>
                                <span>IPD Details</span>
                            </h3>
                        </div> --}}
                        <div class="card-footer text-right" style="background-color:#87ceb0">
                            <h3 style="float:left" class="text-dark">
                                <i class="fa fa-info-circle icon-style2"></i>
                                <span>IPD Details</span>
                            </h3>


                            <div class="d-flex text-right" style="justify-content: end;">




                                @if(app('hasPermission')(25, 'view'))
                    <div class="col-sm-6 col-4 text-right m-b-2">
                        <a href="{{ route('ipd_admit.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                            <i class="fa fa-arrow-left m-r-5 icon3"></i> <span class="hdr-btn-text">Back</span></a>
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
                                                <img id="file" src="" class="userProfile" width="200" height="200"
                                                    alt="Patient Image" style="border-radius:20px"
                                                    onerror="this.onerror=null;this.src='{{ asset(env('IMAGE_PATH') . 'admin/assets/img/img1.png') }}';">
                                            </p>
                                        </div>
                                        <div class="col-lg-6 col-8">
                                            <p class="text-dark margin-text">
                                                <strong><i class="fas fa-user-injured icon-style1"></i> Patient:</strong>
                                                <span id="patient"></span>
                                            </p>
                                            <hr class="margin-text">
                                            <p class="text-dark margin-text">
                                                <strong><i class="fas fa-user-md icon-style1"></i> Doctor:</strong>
                                                <span id="doctor"></span>
                                            </p>
                                            <hr class="margin-text">
                                            <p class="text-dark margin-text">
                                                <strong><i class="fas fa-stethoscope icon-style1"></i> Treatment:</strong>
                                                <span id="treatment"></span>
                                            </p>
                                            <hr class="margin-text">

                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <p class="text-dark margin-text">
                                        <strong><i class="fas fa-clock icon-style1"></i> Admit Date:</strong>
                                        <span id="admit"></span>
                                    </p>
                                    <hr class="margin-text">
                                    <p class="text-dark margin-text">
                                        <strong><i class="fas fa-hotel icon-style1"></i> Room Number:</strong>
                                        <span id="room"></span>
                                    </p>
                                    <hr class="margin-text">
                                    <p class="text-dark margin-text">
                                        <strong><i class="fas fa-bed icon-style1"></i> Bed Number:</strong>
                                        <span id="bed"></span>
                                    </p>
                                     <hr class="margin-text">
                                </div>
                            </div>

                            <div class="button mb-4" style="display: flex; justify-content: end; margin: 0 5px;">
                                @if(app('hasPermission')(25, 'update'))
                                    <a href="#" class="btn btn-primary btn-rounded btn-hdr edit-ipd-btn"
                                        style="color:black; margin-right:10px">
                                        <i class="fa fa-pencil-alt"></i> <span class="hdr-btn-text">Edit</span>
                                    </a>
                                @endif
                                 @if(app('hasPermission')(25, 'delete'))
                                    <button type="button" class="btn btn-danger btn-rounded btn-hdr delete-IPD" data-id="{{ $ipd_admit_id }}">
                                        <i class="fa fa-trash"></i> <span class="hdr-btn-text">Delete</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="successMessage" class="alert alert-success" style="display:none;"></div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
        </div>
    </div>

    <script>
        $(document).ready(function () {
            let ipdId = "{{ $ipd_admit_id }}";

            $.ajax({
                url: "/api/ipd-admissions/" + ipdId,
                method: "GET",
                headers: { "Authorization": "Bearer " + token },
                success: function (ipd) {
                    function ucfirst(str) {
                        return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
                    }

                    $("#file").attr("src", ipd.patient?.profile || '');
                    $("#patient").text(ucfirst(ipd.patient?.fullname));
                    $("#doctor").text(ucfirst(ipd.doctor?.fullname));
                    $("#treatment").text(ucfirst(ipd.treatment?.name));
                    $("#admit").text(ipd.admission_date);
                    $("#room").text(ipd.room_number);
                    $("#bed").text(ipd.bed_number);

                    $(".edit-ipd-btn").attr("href", "/ipd_admit/edit/" + ipd.id);
                },
                error: function (xhr) {
                    console.error("Failed to fetch IPD details", xhr);
                    alert("Error fetching IPD admission details.");
                }
            });
        });
    
    
           $(document).on('click', '.delete-IPD', function () {
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This action cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/api/ipd-admissions/' + id,
                            type: 'DELETE',
                            success: function (response) {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'Test deleted successfully!',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.href = "{{ route('ipd_admit.index') }}";
                                });
                            },
                            error: function () {
                                Swal.fire('Error', 'Failed to delete the test.', 'error');
                            }
                        });
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