@extends('layout.app')

<link rel="stylesheet" href="{{ asset(env('IMAGE_PATH').'admin/assets/css/assign-therapy-show.css') }}">

@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="row top-padding">


            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-footer text-right">

                            <h3 class="text-dark float-left"><i class="fa fa-info-circle icon-style2"></i> <span
                                    class="patient-name"></span>'s <span class="therapy-name"></span> Details</h3>


                            <div class="d-flex text-right justify-end">

                                <div class="mr-2 m-b-2">
                                    <a href="javascript:void(0)" class="btn btn-primary btn-rounded btn-hdr download-assigned-therapy"
                                        data-id="{{ $assign_therapy_id}}">
                                        <i class="fa fa-download"></i> <span class="hdr-btn-text">PDF</span>
                                    </a>
                                </div>


                                @if (app('hasPermission')(27, 'view'))
                                    <div class="text-right m-b-2">
                                        <a href="{{ route('assign-therapy.index') }}" class="btn btn-primary btn-rounded btn-hdr">
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
                                            <p class="text-dark">
                                                <img id="file" src="" class="userProfile" width="200" height="200"
                                                    alt="Patient Image"
                                                    onerror="this.onerror=null;this.src='{{ asset(env('IMAGE_PATH') . 'admin/assets/img/img1.png') }}';">
                                            </p>
                                        </div>
                                        <div class="col-lg-6 col-8">
                                            <p><strong><i class="fa fa-user icon-style1"></i> Patient: </strong>
                                                <span class="patient-name"></span>
                                            </p>
                                            <hr class="margin-text">
                                            <p><strong><i class="fa fa-user-md icon-style1"></i> Therapy Name: </strong>
                                                <span class="therapy-name"></span>
                                            </p>
                                            <hr class="margin-text">
                                            <p><strong><i class="fa fa-user-md icon-style1"></i> Doctor: </strong>
                                                <span class="doctor-name"></span>
                                            </p>
                                            <hr class="margin-text">
                                            <p><strong><i class="fa fa-notes-medical icon-style1"></i> Type: </strong>
                                                <span class="assign-type"></span>
                                            </p>
                                            <hr class="margin-text">

                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <p><strong><i class="fa fa-calendar icon-style1"></i> Start Date: </strong>
                                        <span class="start-date"></span>
                                    </p>
                                    <hr class="margin-text">
                                    <p><strong><i class="fa fa-calendar-alt icon-style1"></i> End Date: </strong>
                                        <span class="end-date"></span>
                                    </p>
                                    <hr class="margin-text">

                                    <p><strong><i class="fa fa-notes-medical icon-style1"></i> Status: </strong>
                                        <span class="assign-status"></span>
                                    </p>
                                    <hr class="margin-text">
                                </div>
                            </div>

                            <div class="button mt-4 mb-4">
                                @if (app('hasPermission')(20, 'update'))
                                    <a href="#" class="btn btn-primary btn-rounded btn-hdr edit-report-btn">
                                        <i class="fa fa-pencil-alt"></i> <span class="hdr-btn-text">Edit</span>
                                    </a>
                                @endif
                                @if (app('hasPermission')(20, 'delete'))
                                    <button type="button" class="btn btn-danger btn-rounded btn-hdr delete-report"
                                        data-id="{{ $assign_therapy_id }}">
                                        <i class="fa fa-trash"></i> <span class="hdr-btn-text">Delete</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="successMessage" class="alert alert-success"></div>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            var therapyId = "{{ $assign_therapy_id }}";

            $.ajax({
                url: '/api/assigned-therapies/' + therapyId,
                type: 'GET',
                success: function (data) {
                    $('#file').attr('src', data.patient?.profile ?? '');
                    $('.patient-name').text(data.patient?.fullname ?? 'N/A');
                    $('.doctor-name').text(data.doctor?.fullname ?? 'N/A');
                    $('.therapy-name').text(data.therapy?.name ?? 'N/A');
                    $('.start-date').text(data.start_date ?? 'N/A');
                    $('.end-date').text(data.end_date ?? 'N/A');
                    $('.assign-status').text(data.status ?? 'N/A');
                    $('.assign-type').text(data.type ?? 'N/A');

                    $('.edit-report-btn').attr('href', '/assign-therapy/edit/' + data.id);
                },
                error: function () {
                    alert('Failed to fetch assigned therapy details.');
                }
            });

              $(document).on('click', '.download-assigned-therapy', function () {
                const id = $(this).data('id');
                window.location.href = `/api/assigned-therapies/${id}/download`;
            });

            $(document).on('click', '.delete-report', function () {
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to undo this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/api/assigned-therapies/${id}`,
                            type: 'DELETE',
                            success: function (response) {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'Therapy deleted successfully!',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.href =
                                        "{{ route('assign-therapy.index') }}";
                                });
                            },
                            error: function () {
                                Swal.fire('Error', 'Failed to delete the therapy.',
                                    'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection