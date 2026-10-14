@extends('layout.app')

<style>
    .branch-title {
        padding-left: 145px !important;
        text-align: center !important;
    }

    .branch-button {
        padding-right: 8px !important;
        text-align: center !important;
    }

    @media screen and (max-width:768px) {
        .branch-title {
            padding-left: 15px !important;
            text-align: left !important;
        }

        .branch-button {
            padding-right: 15px !important;
            text-align: right !important;
        }
    }

    @media screen and (max-width:767px) {
        .branch-title {
            padding-left: 15px !important;
            text-align: left !important;
        }

        .branch-button {
            padding-right: 15px !important;
            text-align: right !important;
        }

        .branch-form {
            height: 720px !important;
        }
    }

    .top-padding {
        padding-top: 15px;
    }

    #branchForm {
        width: 60%;
    }

    textarea {
        border-radius: 10px;
    }

    #branchSuccessMessage {
        display: none;
    }

    .submit-btn {
        padding: 8px 50px;
        border-radius: 50px;
    }
</style>

@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="row top-padding">
                <div class="col-6">
                    <h4 class="page-title branch-title">Add Branch</h4>
                </div>
                @if (app('hasPermission')(33, 'view'))
                    <div class="col-6 m-b-2 eye-btn branch-button">
                        <a href="{{ route('branch.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                            <i class="fa fa-arrow-left m-r-5 icon3"></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    </div>
                @endif
            </div>

            <div class="row">
                <div class="col-12">
                    <form id="branchForm" class="form-container all-form branch-form">
                        @csrf
                        <div class="row">
                            <!-- Branch Name -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label><i class="fas fa-code-branch icon-style"></i> Branch Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" placeholder="Enter branch name"
                                        required>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label><i class="fas fa-envelope icon-style"></i> Email <span
                                            class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control"
                                        placeholder="Enter branch email" required>
                                </div>
                            </div>

                            <!-- Address -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label><i class="fas fa-map-marked-alt icon-style"></i> Address <span
                                            class="text-danger">*</span></label>
                                    <textarea name="address" class="form-control" rows="2" placeholder="Enter full address"
                                        required></textarea>
                                </div>
                            </div>

                            <!-- City -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-city icon-style"></i> City <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="city" class="form-control"
                                        placeholder="Enter city" required>
                                </div>
                            </div>

                            <!-- State -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-flag icon-style"></i> State <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="state" class="form-control"
                                        placeholder="Enter state" required>
                                </div>
                            </div>

                            <!-- Country -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label><i class="fas fa-globe icon-style"></i> Country <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="country" class="form-control"
                                        placeholder="Enter country" required>
                                </div>
                            </div>
                        </div>

                        <div id="branchSuccessMessage" class="alert alert-success"></div>
                        {{-- <div id="branchErrorMessage" class="alert alert-danger" style="display:none;"></div> --}}

                        <button type="submit" class="btn btn-primary submit-btn d-block m-auto">
                            Submit
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            var form = $('#branchForm');

            form.validate({
                rules: {
                    name: "required",
                    email: {
                        required: true,
                        email: true
                    },
                    address: "required",
                    city: "required",
                    state: "required",
                    country: "required",
                },
                messages: {
                    name: "Please enter the branch name",
                    email: {
                        required: "Please enter an email",
                        email: "Please enter a valid email"
                    },
                    address: "Please enter the address",
                    city: "Please enter the city",
                    state: "Please enter the state",
                    country: "Please enter the country",
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                }
            });

            form.on('submit', function(e) {
                e.preventDefault();

                let formData = {
                    name: $("input[name='name']").val(),
                    email: $("input[name='email']").val(),
                    address: $("textarea[name='address']").val(),
                    city: $("input[name='city']").val(),
                    state: $("input[name='state']").val(),
                    country: $("input[name='country']").val(),
                };

                $.ajax({
                    url: '/api/branches',
                    type: 'POST',
                    data: JSON.stringify(formData),
                    contentType: 'application/json',
                    headers: {
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        $('#branchSuccessMessage').text('Branch added successfully.').fadeIn();
                        $('#branchErrorMessage').hide();
                        $('#branchForm')[0].reset();

                        setTimeout(() => {
                            window.location.href = "{{ route('branch.index') }}";
                        }, 1500);
                    },
                    error: function(xhr) {
                        let errorMsg = 'Something went wrong.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            errorMsg = '';
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                errorMsg += value[0] + '<br>';
                            });
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        
                        $('#branchErrorMessage').html(errorMsg).fadeIn();
                        $('#branchSuccessMessage').hide();

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                html: errorMsg,
                            });
                        }
                    }
                });
            });
        });
    </script>
@endsection
