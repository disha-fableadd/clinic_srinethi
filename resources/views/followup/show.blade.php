@extends('layout.app')


<style>
    @media screen and (max-width:767px) {

        .page-title {
            font-size: 20px !important;
        }

    }

    /* Title styling */
    .card-footer h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        text-align: center;
    }

    /* Buttons container */
    .footer-buttons {
        display: flex;
        justify-content: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    /* Button base */
    .footer-buttons .btn {
        font-size: 14px;
        padding: 6px 14px;
        min-width: 100px;
        text-align: center;
    }

    .card-footer{
        background-color:#87ceb0 !important;
    }

    @media (max-width: 375px) {
        .footer-buttons {
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .footer-buttons .btn {
            width: 80%;
            max-width: 220px;
        }

        .card-footer h3 {
            font-size: 14px;
            margin-bottom: 10px;
        }
    }
</style>

@section('content')
    <div class="page-wrapper">
        <div class="content" style="height:100vh">


            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-footer text-right">
                            <h3 style="float:left" class="text-dark">
                                <i class="fa fa-info-circle icon-style2"></i>
                                <span class="">Followup Details</span>
                            </h3>
                            <div class="d-flex text-right" style="justify-content: end;">

                                <div class="mr-2 m-b-2">
                                    <a href="javascript:void(0)" class="btn btn-primary btn-rounded btn-hdr download-followup"
                                        data-id="{{ $followup_id }}">
                                        <i class="fa fa-download"></i> <span class="hdr-btn-text">PDF</span>
                                    </a>
                                </div>


                                @if (app('hasPermission')(2, 'view'))
                                    <div class=" text-right m-b-2">
                                        <a href="{{ route('followup.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                                            <i class="fa fa-arrow-left m-r-5 icon3"></i> <span class="hdr-btn-text">Back</span>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="card-body mt-3">

                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="row">
                                        <div class="col-lg-6 col-4 margin-img text-center">
                                            <p class="text-dark">
                                                <!-- <strong><i class="fa fa-image icon-style1 "></i> Patient:
                                                                </strong><br> -->
                                                <img id="file" class="userProfile" src="" width="200"
                                                    height="200" alt="report Image" style="border-radius:20px"
                                                    onerror="this.onerror=null;this.src='{{ asset(env('IMAGE_PATH') . 'admin/assets/img/img1.png') }}';">
                                            </p>
                                        </div>
                                        <div class="col-lg-6 col-8">
                                            <p class="text-dark margin-text">
                                                <strong><i class="fas fa-user-injured icon-style1"></i> Patient:
                                                </strong>
                                                <span id="patient"></span>
                                            </p>

                                            <hr class="margin-text">
                                            <p class="text-dark margin-text">
                                                <strong><i class="fas fa-user-md icon-style1"></i> Doctor: </strong>
                                                <span id="doctor"></span>
                                            </p>

                                            @php
                                                $currentProjectTypeId = (int) \App\Models\Setting::getValue('project_type_id', 1);
                                            @endphp
                                            @if($currentProjectTypeId !== 3)
                                            <hr class="margin-text">
                                            <p class="text-dark margin-text">
                                                <strong><i class="fas fa-stethoscope icon-style1"></i> Treatment:
                                                </strong>
                                                <span id="treatment"></span>
                                            </p>
                                            <hr class="margin-text">
                                            @endif

                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">

                                    <p class="text-dark margin-text">
                                        <strong><i class="fas fa-clock icon-style1"></i> Followup Date:
                                        </strong>
                                        <span id="date"></span>
                                    </p>
                                    <hr class="margin-text">
                                    @if($currentProjectTypeId !== 3)
                                    <p class="text-dark margin-text">
                                        <strong><i class="fas fa-pencil-alt icon-style1"></i>Followup Update:
                                        </strong>
                                        <span id="update"></span>
                                    </p>
                                    <hr class="margin-text">
                                    <p class="text-dark margin-text">
                                        <strong><i class="fas fa-pencil-alt icon-style1"></i>Followup Type:
                                        </strong>
                                        <span id="type"></span>
                                    </p>
                                    <hr class="margin-text">
                                    @endif
                                </div>
                            </div>





                            <div class="button mb-4" style="display: flex; justify-content: end; margin: 0 5px;">
                                @if (app('hasPermission')(2, 'update'))
                                    <a href="#" class="btn btn-primary btn-rounded btn-hdr edit-followup-btn"
                                        style="color:black; margin-right:10px">
                                        <i class="fa fa-pencil-alt"></i> <span class="hdr-btn-text">Edit</span>
                                    </a>
                                @endif
                                @if (app('hasPermission')(2, 'delete'))
                                    <button type="button" class="btn btn-danger btn-rounded btn-hdr delete-report"
                                        data-id="{{ $followup_id }}">
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



        <script>
            $(document).ready(function() {


                let followupId = "{{ $followup_id }}";




                $.ajax({
                    url: "/api/followup/" + followupId,
                    method: "GET",
                    dataType: "json",
                    headers: {
                        "Authorization": "Bearer " + token
                    },
                    success: function(response) {
                        if (response.success) {
                            var followup = response.data;

                            // Check if the profile exists and is not null or undefined
                            let imageUrl = followup.patient.profile

                            // Set the image source
                            function ucfirst(str) {
                                return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
                            }

                            $("#file").attr("src", imageUrl).on("error", function() {
                                // If image fails to load, set default image
                                $(this).attr("src",
                                    "{{ asset(env('IMAGE_PATH') . 'admin/assets/img/img1.png') }}"
                                    );
                            });

                            $("#patient").text(ucfirst(followup.patient ? followup.patient.fullname : '--'));
                            $("#doctor").text(ucfirst(followup.doctor ? followup.doctor.fullname : '--'));
                            $("#treatment").text(ucfirst(followup.treatment ? followup.treatment.name : '--'));
                            $("#date").text(followup.date ?? '--');
                            $("#update").text(ucfirst(followup.followup_update ?? '--'));
                            $("#type").text(ucfirst(followup.followup_type ?? '--'));


                            $(".edit-followup-btn").attr("href", "/followup/edit/" + followup.id);
                        } else {
                            alert("Failed to fetch followup details");
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        alert("Error fetching followup details");
                    }
                });

                $(document).on('click', '.download-followup', function() {
                    const followupId = $(this).data('id');
                    window.location.href = `/api/followups/${followupId}/download`;
                });
                // Delete functionality
                $(".delete-report").on("click", function() {
                    var followupId = $(this).data('id');

                    if (!followupId) {
                        Swal.fire('Error', 'followup ID not found!', 'error');
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
                                url: '/api/followup/' + followupId,
                                method: 'DELETE',
                                success: function(response) {
                                    Swal.fire({
                                        title: 'Deleted!',
                                        text: 'followup deleted successfully!',
                                        icon: 'success',
                                        timer: 1500,
                                        showConfirmButton: false
                                    }).then(() => {
                                        $('button[data-id="' + followupId + '"]')
                                            .closest('tr').remove();
                                        setTimeout(function() {
                                            window.location.href =
                                                "{{ route('followup.index') }}";
                                        }, 1500);
                                    });
                                },
                                error: function() {
                                    Swal.fire('Error', 'Error deleting followup.', 'error');
                                }
                            });
                        }
                    });
                });

            });
        </script>














        <style>
            /* .card-footer {
                                            border-top-left-radius: 20px;
                                            border-top-right-radius: 20px;
                                        } */

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
