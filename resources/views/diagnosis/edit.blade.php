@extends('layout.app')

<link rel="stylesheet" href="{{ asset(env('IMAGE_PATH').'admin/assets/css/diagnosis-edit.css') }}">

<style>
    .is-invalid {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }
    #nameError {
        font-size: 80%;
        margin-top: 5px;
        display: block;
    }
</style>

@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="row">
                <div class="col-6">
                    <h4 class="page-title diagnosis-title ">Edit Diagnosis </h4>
                </div>
                @if (app('hasPermission')(25, 'view'))
                    <div class="col-6 diagnosis-btn m-b-2 eye-btn">
                        <a href="{{ route('diagnosis.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                            <i class="fa fa-arrow-left m-r-5 icon3"></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    </div>
                @endif
            </div>

            <div class="row">
                <div class="col-12">
                    <form class="form-container" id="diagnosisForm" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Hidden fields --}}
                        <input type="hidden" id="diagnosis_id" value="{{ $id }}">
                        <input type="hidden" name="branch_id" id="branch_id">

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>
                                        <i class="fas fa-file-medical icon-style"></i> Diagnosis Name
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" name="name" id="name"
                                        placeholder="Enter diagnosis name" >
                                    <span id="nameError" class="text-danger" style="display: none;">Please enter a diagnosis name</span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>
                                        <i class="fas fa-notes-medical icon-style"></i> Description
                                    </label>
                                    <textarea class="form-control" name="description" id="description" rows="2" placeholder="Enter description"
                                        ></textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Messages --}}
                        <div id="diagnosisSuccessMessage" class="alert alert-success"></div>
                        <div id="diagnosisErrorMessage" class="alert alert-danger"></div>

                        <button type="submit" class="btn btn-primary d-block m-auto">
                            Update
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- jQuery --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            let token = localStorage.getItem('token');
            let diagnosisId = $("#diagnosis_id").val();

            // 🔹 Set branch_id from localStorage
            let storedBranchId = localStorage.getItem('selectedBranchId');
            if (storedBranchId) {
                $('#branch_id').val(storedBranchId);
            }

            // 🔹 Fetch existing diagnosis details
            $.ajax({
                url: "/api/diagnosis/" + diagnosisId,
                type: "GET",
                headers: {
                    "Authorization": "Bearer " + token
                },
                success: function(response) {
                    if (response.status && response.diagnosis) {
                        $("#name").val(response.diagnosis.name);
                        $("#description").val(response.diagnosis.description);
                    } else {
                        $("#diagnosisErrorMessage").text("Diagnosis not found").show();
                    }
                },
                error: function(xhr) {
                    $("#diagnosisErrorMessage").text("Unable to fetch diagnosis data").show();
                    console.error(xhr.responseText);
                }
            });

            // 🔹 Update form submit
            $("#diagnosisForm").on("submit", function(e) {
                e.preventDefault();

                let name = $("#name").val().trim();
                if (name === "") {
                    $("#nameError").show();
                    $("#name").addClass("is-invalid");
                    return;
                } else {
                    $("#nameError").hide();
                    $("#name").removeClass("is-invalid");
                }

                let formData = {
                    name: name,
                    description: $("#description").val(),
                    branch_id: $("#branch_id").val()
                };

                $.ajax({
                    url: "/api/diagnosis/" + diagnosisId,
                    type: "PUT",
                    data: formData,
                    headers: {
                        "Authorization": "Bearer " + token
                    },
                    success: function(response) {
                        if (response.status) {
                            $('#diagnosisSuccessMessage').text(response.message).show();
                            $('#diagnosisErrorMessage').hide();

                            setTimeout(function() {
                                window.location.href = "{{ route('diagnosis.index') }}";
                            }, 1500);
                        } else {
                            $('#diagnosisErrorMessage').text("Update failed").show();
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = '';
                        if (xhr.status === 422 && xhr.responseJSON.errors) {
                            $.each(xhr.responseJSON.errors, function(key, messages) {
                                errorMessage += messages[0] + '<br>';
                            });
                        } else {
                            errorMessage = "Something went wrong.";
                        }
                        $('#diagnosisErrorMessage').html(errorMessage).show();
                    }
                });
            });
        });
    </script>
@endsection
