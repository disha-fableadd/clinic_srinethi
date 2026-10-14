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

    #editBranchForm {
        width: 60%;
    }

    #branchSuccessMessage {
        display: none;
    }

    #branchErrorMessage {
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
                <h4 class="page-title branch-title">Edit Branch</h4>
            </div>
            <div class="col-6 m-b-2 eye-btn branch-button">
                <a href="{{ route('branch.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                    <i class="fa fa-arrow-left m-r-5 icon3"></i> <span class="hdr-btn-text">Back</span>
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <form id="editBranchForm" class="form-container all-form branch-form">
                    @csrf
                    <input type="hidden" id="branch_id" value="{{ $id }}">

                    <div class="row">
                        <!-- Branch Name -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><i class="fas fa-code-branch icon-style"></i> Branch Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><i class="fas fa-envelope icon-style"></i> Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><i class="fas fa-map-marked-alt icon-style"></i> Address <span class="text-danger">*</span></label>
                                <textarea name="address" class="form-control" rows="2" required></textarea>
                            </div>
                        </div>

                        <!-- City -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-city icon-style"></i> City <span class="text-danger">*</span></label>
                                <input type="text" name="city" class="form-control" required>
                            </div>
                        </div>

                        <!-- State -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-flag icon-style"></i> State <span class="text-danger">*</span></label>
                                <input type="text" name="state" class="form-control" required>
                            </div>
                        </div>

                        <!-- Country -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><i class="fas fa-globe icon-style"></i> Country <span class="text-danger">*</span></label>
                                <input type="text" name="country" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div id="branchSuccessMessage" class="alert alert-success"></div>
                    <div id="branchErrorMessage" class="alert alert-danger"></div>

                    <button type="submit" class="btn btn-primary submit-btn d-block m-auto">
                        Update
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
<script>
$(document).ready(function () {
    // Ensure we always target the correct branch ID (from the URL / route)
    const branchId = @json($id);

    if (!branchId) {
        $('#branchErrorMessage').text("Invalid branch ID.").fadeIn();
        return;
    }

    // Load existing data
    $.ajax({
        url: "/api/branches/" + branchId,
        method: "GET",
        success: function(branch) {
            $("input[name='name']").val(branch.name);
            $("input[name='email']").val(branch.email);
            $("textarea[name='address']").val(branch.address);
            $("input[name='city']").val(branch.city);
            $("input[name='state']").val(branch.state);
            $("input[name='country']").val(branch.country);
        },
        error: function() {
            $('#branchErrorMessage').text("Branch not found!").fadeIn();
        }
    });

    // Setup validation
    $("#editBranchForm").validate({
        rules: {
            name: {
                required: true,
                minlength: 3
            },
            email: {
                required: true,
                email: true
            },
            address: {
                required: true,
                minlength: 10
            },
            city: {
                required: true,
                minlength: 2
            },
            state: {
                required: true,
                minlength: 2
            },
            country: {
                required: true,
                minlength: 2
            }
        },
        messages: {
            name: {
                required: "Please enter the branch name",
                minlength: "Branch name must be at least 3 characters"
            },
            email: {
                required: "Please enter an email",
                email: "Please enter a valid email"
            },
            address: {
                required: "Please enter the address",
                minlength: "Address must be at least 10 characters"
            },
            city: {
                required: "Please enter the city",
                minlength: "City must be at least 2 characters"
            },
            state: {
                required: "Please enter the state",
                minlength: "State must be at least 2 characters"
            },
            country: {
                required: "Please enter the country",
                minlength: "Country must be at least 2 characters"
            }
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
        },
        submitHandler: function(form) {
            let formData = {
                name: $("input[name='name']").val(),
                email: $("input[name='email']").val(),
                address: $("textarea[name='address']").val(),
                city: $("input[name='city']").val(),
                state: $("input[name='state']").val(),
                country: $("input[name='country']").val(),
            };

            $.ajax({
                url: "/api/branches/" + branchId,
                method: "PUT",
                data: JSON.stringify(formData),
                contentType: "application/json",
                headers: {
                    "Accept": "application/json"
                },
                success: function(response) {
                    $('#branchSuccessMessage').text("Branch updated successfully.").fadeIn();
                    $('#branchErrorMessage').hide();

                    setTimeout(() => {
                        window.location.href = "{{ route('branch.index') }}";
                    }, 1500);
                },
                error: function(xhr) {
                    let errorMsg = "Update failed.";
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMsg = "";
                        $.each(xhr.responseJSON.errors, function(key, value) {
                            errorMsg += value[0] + "<br>";
                        });
                    }
                    $('#branchErrorMessage').html(errorMsg).fadeIn();
                    $('#branchSuccessMessage').hide();
                }
            });
        }
    });
});
</script>
@endsection




















{{-- @extends('layout.app')

@section('content')
<div class="container">
    <h2>Edit Branch</h2>

    <form id="editBranchForm">
        <input type="hidden" id="branch_id" value="{{ $id }}">

        <div class="form-group">
            <label>Branch Name</label>
            <input type="text" class="form-control" id="name" name="name">
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" class="form-control" id="email" name="email">
        </div>

        <div class="form-group">
            <label>Address</label>
            <textarea class="form-control" id="address" name="address"></textarea>
        </div>

        <div class="form-group">
            <label>City</label>
            <input type="text" class="form-control" id="city" name="city">
        </div>

        <div class="form-group">
            <label>State</label>
            <input type="text" class="form-control" id="state" name="state">
        </div>

        <div class="form-group">
            <label>Country</label>
            <input type="text" class="form-control" id="country" name="country">
        </div>

        <button type="submit" class="btn btn-primary mt-3">Update</button>
        <a href="{{ url()->previous() }}" class="btn btn-secondary mt-3">Cancel</a>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    var branchId = $("#branch_id").val();

    // Load branch data
    $.ajax({
        url: "/api/branches/" + branchId,
        method: "GET",
        success: function(branch) {
            $("#name").val(branch.name);
            $("#email").val(branch.email);
            $("#address").val(branch.address);
            $("#city").val(branch.city);
            $("#state").val(branch.state);
            $("#country").val(branch.country);
        },
        error: function() {
            alert("Branch not found!");
        }
    });

    // Submit update
    $("#editBranchForm").on("submit", function(e) {
        e.preventDefault();

        $.ajax({
            url: "/api/branches/" + branchId,
            method: "PUT",
            data: {
                name: $("#name").val(),
                email: $("#email").val(),
                address: $("#address").val(),
                city: $("#city").val(),
                state: $("#state").val(),
                country: $("#country").val(),
            },
            success: function(response) {
                alert(response.message);
                window.location.href = "/branch/" + branchId; // redirect to view page
            },
            error: function(xhr) {
                alert("Update failed: " + xhr.responseJSON.message);
            }
        });
    });
});
</script>
@endsection --}}
