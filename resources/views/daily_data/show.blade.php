@extends('layout.app')

<style>
    @media screen and (max-width:767px) {
        .page-title {
            font-size: 20px !important;
        }
    }

    .icon-style1 {
        background-color: white;
        color: #9dc3b3;
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

@section('content')
    <div class="page-wrapper">
        <div class="content" style="height:100vh">
            <div class="row" style="padding-top:15px">
                <div class=" col-8">
                    <h4 class="page-title"><i class="fa fa-user-injured"></i>Patient Details</h4>
                </div>
                @if(app('hasPermission')(26, 'view'))
                    <div class="text-right m-b-2">
                        <a href="{{ route('daily_data.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                            <i class="fa fa-arrow-left"></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    </div>
                @endif
            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-footer text-right" style="background-color:#87ceb0">
                            <h3 class="text-dark" style="float:left">
                                <i class="fa fa-info-circle icon-style2"></i> <span class="patient_name"></span> Details
                            </h3>
                        </div>

                        <div class="card-body mt-3">
                            <div class="row">
                                <div class="col-lg-6 col-sm-12">


                                    <p><strong><i class="fa fa-user icon-style1"></i> Patient Name: </strong> <span
                                            class="patient_name"></span></p>
                                    <hr>
                                    <p><strong><i class="fa fa-phone icon-style1"></i> Contact: </strong> <span
                                            class="contact"></span></p>
                                    <hr>
                                    <p><strong><i class="fa fa-hourglass-start icon-style1"></i> Age: </strong> <span
                                            class="age"></span></p>
                                    <hr>

                                    <p><strong><i class="fa fa-calendar icon-style1"></i> Date: </strong> <span
                                            class="date"></span>
                                    </p>
                                    <hr>
                                </div>
                                <div class="col-lg-6 col-sm-12">


                                    <p><strong>₹ Amount: </strong> <span
                                            class="amount"></span></p>
                                    <hr>
                                    <p><strong><i class="fa fa-toggle-on icon-style1"></i> Status: </strong> <span
                                            class="payment-status"></span></p>
                                    <hr>
                                    <p><strong><i class="fa fa-user-nurse icon-style1"></i> Collected By: </strong> <span
                                            class="by"></span></p>
                                </div>

                            </div>
                            <div class="button mt-4 mb-4" style="display: flex; justify-content: end;">
                                @if(app('hasPermission')(19, 'update'))
                                    <a href="#" class="btn btn-primary btn-rounded btn-hdr edit-daily_data-btn"
                                        style="margin-right:10px; color:black">
                                        <i class="fa fa-pencil-alt"></i> <span class="hdr-btn-text">Edit</span>
                                    </a>
                                @endif
                                @if(app('hasPermission')(19, 'delete'))
                                    <button type="button" class="btn btn-danger btn-rounded btn-hdr delete-daily_data"
                                        data-id="{{ $daily_data_id }}">
                                        <i class="fa fa-trash"></i> <span class="hdr-btn-text">Delete</span>
                                    </button>
                                @endif
                                <button type="button" class="btn btn-primary btn-rounded btn-hdr generate-link-btn"
                                    data-id="{{ $daily_data_id }}" data-pending="{{ $pending_amount ?? 0 }}"
                                    style="margin-left:10px">
                                    <i class="fa fa-link"></i> Generate Payment Link
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div id="successMessage" class="alert alert-success" style="display:none;"></div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            var daily_dataId = "{{ $daily_data_id }}";

            $.ajax({
                url: '/api/daily-data/' + daily_dataId,
                type: 'GET',
                success: function (response) {
                    $('.patient_name').text(response.patient_name);
                    $('.contact').text(response.contact);
                    $('.age').text(response.age);
                    $('.date').text(response.date);
                    $('.amount').text("₹ " + parseFloat(response.amount).toFixed(2));
                    $('.payment-status').text(response.status);
                    $('.by').text(response.by);

                    $('.edit-daily_data-btn').attr('href', '/daily_data/edit/' + response.id);
                },
                error: function () {
                    alert('Failed to fetch daily data details.');
                }
            });

            $(document).on('click', '.delete-daily_data', function () {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "Delete this daily data?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/api/daily-data/${id}`,
                            type: 'DELETE',
                            success: function (res) {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'Daily data deleted successfully!',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.href =
                                        "{{ route('daily_data.index') }}";
                                });
                            },
                            error: function (xhr) {
                                let errorMsg = 'Failed to delete daily data.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMsg = xhr.responseJSON.message;
                                }
                                Swal.fire('Error', errorMsg, 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
