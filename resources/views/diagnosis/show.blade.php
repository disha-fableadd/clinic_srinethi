@extends('layout.app')

<link rel="stylesheet" href="{{ asset(env('IMAGE_PATH').'admin/assets/css/diagnosis-show.css') }}">

@section('content')

    <div class="page-wrapper">
        <div class="content">

            <div class="diagnosis-card mt-2">
                <div class="card-footer text-right">
                    <h3 class="text-dark">
                        <i class="fa fa-info-circle icon-style2 text-white"></i>
                        Diagnosis Details
                    </h3>
                    <div class="d-flex text-right">
                        {{-- <div class="mr-2 m-b-2">
                            <a href="javascript:void(0)" class="btn btn-primary btn-rounded btn-hdr download-diagnosis"
                                data-id="{{ $diagnosis_id }}">
                                <i class="fa fa-download"></i> <span class="hdr-btn-text">PDF</span>
                            </a>
                        </div> --}}

                        <div class="m-b-2">
                            <a href="{{ route('diagnosis.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                                <i class="fa fa-arrow-left"></i> <span class="hdr-btn-text">Back</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="diagnosis-body">
                    <div class="diagnosis-field">
                        <i class="fa fa-user-circle"></i>
                        <div class="diagnosis-label">Diagnosis Name:</div>
                        <div class="diagnosis-value" id="diagnosis_name">--</div>
                    </div>


                    <div class="diagnosis-field">
                        <i class="fa fa-align-left"></i>
                        <div class="diagnosis-label">Description:</div>
                        <div class="diagnosis-value" id="diagnosis_description">--</div>
                    </div>
                </div>

                <div class="action-buttons">
                    <a href="{{ route('diagnosis.edit', $diagnosis_id) }}" class="btn btn-primary">
                        <i class="fa fa-edit"></i> <span class="hdr-btn-text">Edit</span>
                    </a>
                    <button class="btn btn-danger delete-diagnosis" data-id="{{ $diagnosis_id }}" style="border-radius:50px">
                        <i class="fa fa-trash"></i> <span class="hdr-btn-text">Delete</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    

    <script>
        $(document).ready(function() {
            let token = localStorage.getItem('token');
            let diagnosisId = "{{ $diagnosis_id }}";

            // 🔹 Fetch Diagnosis Details
            $.ajax({
                url: '/api/diagnosis/' + diagnosisId,
                type: 'GET',
                headers: {
                    "Authorization": "Bearer " + token
                },
                success: function(response) {
                   // console.log(response); // check in console

                    let diagnosis = response.diagnosis; // ✅ correct

                    $('#diagnosis_name').text(diagnosis?.name ?? '--');
                    $('#diagnosis_description').text(diagnosis?.description ?? '--');
                },
                error: function(xhr) {
                    console.error("Failed to fetch diagnosis:", xhr.responseText);
                }
            });

            // 🔹 Delete Diagnosis
            $(document).on('click', '.delete-diagnosis', function() {
                let id = $(this).data('id');

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
                            url: '/api/diagnosis/' + diagnosisId,
                            type: 'DELETE',
                            headers: {
                                "Authorization": "Bearer " + token
                            },
                            success: function(response) {
                                Swal.fire(
                                    'Deleted!',
                                    'Diagnosis has been deleted.',
                                    'success'
                                ).then(() => {
                                    window.location.href = "{{ route('diagnosis.index') }}";
                                });
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Failed!',
                                    'Failed to delete diagnosis.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
