@extends('layout.app')

<style>
    .select2-container--default .select2-selection--multiple {
        box-shadow: none;
        font-size: 14px;
        min-height: 40px !important;
        border-radius: 50px !important;
        padding: 0.469rem 0.75rem;
        border-color: rgb(207, 236, 224) !important;
    }

    .select2-container--default .select2-selection--multiple {
        padding: 4px 8px;
        text-align: left;
        min-height: 38px;
    }

    .select2-selection__rendered {
        text-align: left !important;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        background-color: rgb(207, 236, 224) !important;
        color: black !important;
        border-radius: 20px !important;
        border: none;
        font-size: 14px;
        font-weight: normal !important;
        text-align: center;
        max-width: fit-content;
    }

    .expense-title {
        padding-left: 145px !important;
        text-align: center !important;
    }

    .expense-button {
        padding-right: 8px !important;
        text-align: center !important;
    }
      .form-container {
        width: 60% !important;
    }

    @media screen and (max-width:768px) {
        .expense-title {
            padding-left: 15px !important;
            text-align: left !important;
        }

        .expense-button {
            padding-right: 15px !important;
            text-align: right !important;
        }
    }

    @media screen and (max-width:767px) {
        .expense-title {
            padding-left: 15px !important;
            text-align: left !important;
        }

        .expense-button {
            padding-right: 15px !important;
            text-align: right !important;
        }

        /* .expense-form {
            height: 720px !important;
        } */
    }
</style>

@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="row" style="padding-top:15px">
                <div class="col-6">
                    <h4 class="page-title expense-title">Add Expense</h4>
                </div>
                @if (app('hasPermission')(33, 'view'))
                    <div class="col-6 m-b-2 eye-btn expense-button">
                        <a href="{{ route('expense.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                            <i class="fa fa-arrow-left m-r-5 icon3"></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    </div>
                @endif
            </div>

            <div class="row">
                <div class="col-12">
                    <form id="expenseForm" class="form-container all-form expense-form" >
                        @csrf
                        <input type="hidden" name="branch_id" id="branch_id">
                        <div class="row">
                            <!-- User ID -->
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <label class="mr-2"><i class="fas fa-medkit icon-style"></i> Staff <span
                                                class="text-danger">*</span></label>
                                        @if (app('hasPermission')(3, 'create'))
                                            <a href="{{ route('user.create') }}" target="_blank"
                                                class="btn btn-primary btn-sm">
                                                <i class="fas fa-plus"></i> <span class="hdr-btn-text">Add</span>
                                            </a>
                                        @endif

                                    </div>
                                    <!-- <select class="form-control select2 doctorSelect" name="user_id" id="doctorSelect"
                                                                    required>
                                                                    <option value="">Select Doctor</option>
                                                                </select> -->
                                    <select class="form-control select2 doctorSelect" name="user_id" id="doctorSelect"
                                        required>
                                        {{-- <option value="">Select Staff</option> --}}
                                    </select>



                                </div>
                            </div>

                            <!-- Date & Time -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label><i class="fas fa-calendar-alt icon-style"></i> Date & Time <span
                                            class="text-danger">*</span></label>
                                    <input type="datetime-local" name="date_time" class="form-control" id="dateTimeInput"
                                        required>
                                </div>
                            </div>

                            <script>
                                // Get current date & time in format YYYY-MM-DDTHH:MM
                                const now = new Date();
                                const year = now.getFullYear();
                                const month = String(now.getMonth() + 1).padStart(2, '0');
                                const day = String(now.getDate()).padStart(2, '0');
                                const hours = String(now.getHours()).padStart(2, '0');
                                const minutes = String(now.getMinutes()).padStart(2, '0');

                                const maxDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;

                                // Set the max attribute
                                document.getElementById('dateTimeInput').setAttribute('max', maxDateTime);
                            </script>


                            <!-- Amount -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>₹ Amount <span
                                            class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="amount" class="form-control" id="amount"
                                        placeholder="e.g. 1000" required>
                                </div>
                            </div>

                            <!-- Service -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-concierge-bell icon-style"></i> Service <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="service" class="form-control"
                                        placeholder="Service description" required>
                                </div>
                            </div>

                            @php
                                $currentProjectTypeId = (int) \App\Models\Setting::getValue('project_type_id', 1);
                            @endphp
                            @if($currentProjectTypeId !== 3)
                            <!-- Comment -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label><i class="fas fa-comment icon-style"></i> Comment <span
                                                        class="text-danger">*</span></label>
                                    <textarea name="comment" class="form-control" rows="3" placeholder="Optional comment"
                                        style="border-radius: 20px;"></textarea>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div id="expenseSuccessMessage" class="alert alert-success" style="display:none;"></div>
                        {{-- <div id="expenseErrorMessage" class="alert alert-danger" style="display:none;"></div> --}}

                        <button type="submit" class="btn btn-primary submit-btn d-block m-auto"
                            style="padding:8px 50px; border-radius:50px;">
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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let storedBranchId = localStorage.getItem('selectedBranchId');
            if (storedBranchId) {
                document.getElementById('branch_id').value = storedBranchId;
            }
        });
       


        let branchId = localStorage.getItem('selectedBranchId'); // fetch branch from localStorage

        $.ajax({
            url: "/api/userss",
            type: "GET",
            data: {
                branch_id: branchId
            }, // ✅ send branch id
            dataType: "json",
            success: function(data) {
                let userdropdown = $('select[name="user_id"]');

                if (userdropdown.length === 0) {
                    console.log("Dropdown not found! Check your HTML.");
                    return;
                }

                userdropdown.empty().append('<option value="">Select Staff</option>');

                let staffs = data.users || [];

                if (staffs.length === 0) {
                    console.warn("No staff found for this branch.");
                }

                $.each(staffs, function(key, user) {
                    const capitalizedName = user.fullname.charAt(0).toUpperCase() + user.fullname.slice(
                        1);
                    userdropdown.append(`<option value="${user.id}">${capitalizedName}</option>`);
                });

                // Initialize Select2 plugin
                if (!userdropdown.hasClass('select2-hidden-accessible')) {
                    userdropdown.select2({
                        placeholder: "Select Staff",
                        allowClear: true,
                        width: '100%'
                    });
                }

                // Search placeholder in select2
                userdropdown.on('select2:open', function() {
                    $('.select2-search__field').attr('placeholder', 'Search Staff');
                });
            },
            error: function(xhr) {
                console.log("API Error:", xhr.status, xhr.responseText);
            }
        });



        $(document).ready(function() {
            $('.select2').select2();

            var form = $('#expenseForm');

            form.validate({
                rules: {
                    user_id: "required",
                    date_time: "required",
                    amount: {
                        required: true,
                        number: true,
                        min: 0
                    },
                    service: "required",
                    @if($currentProjectTypeId !== 3)
                    comment: "required"
                    @endif
                },
                messages: {
                    user_id: "Please enter the staff/user ID",
                    date_time: "Please select date and time",
                    amount: {
                        required: "Please enter the amount",
                        number: "Please enter a valid number",
                        min: "Amount cannot be negative"
                    },
                    service: "Please enter the service",
                    @if($currentProjectTypeId !== 3)
                    comment: "Please enter the comment",
                    @endif
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
                    user_id: $("select[name='user_id']").val(),
                    date_time: $("input[name='date_time']").val(),
                    amount: $("#amount").val(),
                    service: $("input[name='service']").val(),
                    comment: $("textarea[name='comment']").val(),
                    branch_id: $("#branch_id").val(),
                };

                $.ajax({
                    url: '/api/expenses',
                    type: 'POST',
                    data: JSON.stringify(formData),
                    contentType: 'application/json',
                    headers: {
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        $('#expenseSuccessMessage').text('Expense added successfully.')
                            .fadeIn();
                        $('#expenseErrorMessage').hide();
                        $('#expenseForm')[0].reset();
                        $('.select2').val(null).trigger('change');

                        setTimeout(() => {
                            window.location.href = "{{ route('expense.index') }}";
                        }, 1500);
                    },
                    error: function(xhr) {
                        let errorMsg = 'Something went wrong.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            errorMsg = '';
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                errorMsg += value[0] + '<br>';
                            });
                        }
                        $('#expenseErrorMessage').html(errorMsg).fadeIn();
                        $('#expenseSuccessMessage').hide();
                    }
                });
            });
        });
    </script>
@endsection

