@extends('layout.app')

<link rel="stylesheet" href="{{ asset(env('IMAGE_PATH').'admin/assets/css/diagnosis-create.css') }}">

@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="row">
                <div class="col-6">
                    <h4 class="page-title diagnosis-title">Add Diagnosis</h4>
                </div>
                @if (app('hasPermission')(25, 'view'))
                    <div class="col-6 m-b-2 diagnosis-btn">
                        <a href="{{ route('diagnosis.index') }}" class="btn btn-primary btn-rounded btn-hdr  ">
                            <i class="fa fa-arrow-left m-r-5 icon3"></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    </div>
                @endif
            </div>
            <div class="row">
                <div class="col-12">
                    <form class="form-container" id="diagnosisForm" method="POST" action="">
                        @csrf
                        <div class="form-step diagnosis-form1" id="step-1">
                            <input type="hidden" name="branch_id" id="branch_id">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label><i class="fas fa-file-medical icon-style"></i> Diagnosis Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name"
                                            placeholder="Enter diagnosis name" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label><i class="fas fa-notes-medical icon-style"></i> Description </label>
                                        <textarea class="form-control" name="description" rows="2" placeholder="Enter description" ></textarea>
                                    </div>
                                </div>
                            </div>
                            <div id="diagnosisSuccessMessage" class="alert alert-success"></div>
                            <div id="diagnosisErrorMessage" class="alert alert-danger"></div>
                            <button type="submit" class="btn btn-primary d-block m-auto">
                                Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <style>
        .invalid-feedback {
            display: block;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 80%;
            color: #dc3545;
        }
        .is-invalid {
            border-color: #dc3545 !important;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let storedBranchId = localStorage.getItem('selectedBranchId');
            if (storedBranchId) {
                document.getElementById('branch_id').value = storedBranchId;
            }
        })
        $(document).ready(function() {
            let token = localStorage.getItem('token');
            // Initialize form validation
            var form = $("#diagnosisForm");
            form.validate({
                rules: {
                    name: "required",
                   
                },
                messages: {
                    name: "Please enter a diagnosis name",
                   
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

            // Handle form submission
            form.on('submit', function(e) {
                e.preventDefault();
                if (form.valid()) {
                    let formData = new FormData(this);
                    $.ajax({
                        url: "{{ url('/api/diagnosis') }}",
                        type: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        headers: {
                            "Authorization": "Bearer " + token
                        },
                        success: function(response) {
                            $('#diagnosisSuccessMessage').text(response.message ||
                                'Diagnosis created successfully').show();
                            $('#diagnosisForm')[0].reset();
                            setTimeout(function() {
                                window.location.href = "{{ route('diagnosis.index') }}";
                            }, 1500);
                        },
                        error: function(xhr) {
                            var errorMessage = '';
                            if (xhr.status === 422) {
                                var errors = xhr.responseJSON.errors;
                                $.each(errors, function(key, messages) {
                                    errorMessage += messages[0] + '<br>';
                                });
                            } else {
                                errorMessage = "Something went wrong.";
                            }
                            $('#diagnosisErrorMessage').html(errorMessage).show();
                        }
                    });
                }
            });
        });
    </script>
@endsection
