@extends('layout.app')

<style>
    /* Common Select2 Styling */
    .select2-container--default .select2-selection--single,
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
    }

    .select2-selection__rendered {
        text-align: left !important;
    }

    /* Page Titles & Buttons */
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

    /* Responsive */
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
        .expense-form {
            height: auto !important;
        }
    }
</style>

@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="row" style="padding-top:15px">
                <div class="col-6">
                    <h4 class="page-title expense-title">Edit Expense</h4>
                </div>
                <div class="col-6 m-b-2 expense-button">
                    <a href="{{ route('expense.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                        <i class="fa fa-arrow-left m-r-5"></i> <span class="hdr-btn-text">Back</span>
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <form id="editExpenseForm" class="form-container all-form expense-form">
                        @csrf

                        <!-- Staff -->
                        <div class="form-group">
                            <label><i class="fas fa-user icon-style"></i> Staff <span class="text-danger">*</span></label>
                            <!-- <select class="form-control select2 doctorSelect" id="userSelect" name="user_id" required>
                                                                <option value="">Select Staff</option>
                                                            </select> -->
                            <select class="form-control select2 doctorSelect" name="user_id" id="userSelect" required>
                                <option value="">Select Staff</option>
                            </select>

                        </div>
                        <!-- Date & Time -->
                        <div class="form-group">
                            <label><i class="fas fa-calendar-alt icon-style"></i> Date & Time <span
                                    class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="date_time" name="date_time" required>
                        </div>

                        <script>
                            const dateInput = document.getElementById('date_time');

                            // Get current date & time in YYYY-MM-DDTHH:MM format
                            const now = new Date();
                            const year = now.getFullYear();
                            const month = String(now.getMonth() + 1).padStart(2, '0');
                            const day = String(now.getDate()).padStart(2, '0');
                            const hours = String(now.getHours()).padStart(2, '0');
                            const minutes = String(now.getMinutes()).padStart(2, '0');
                            const maxDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;

                            // Set the max attribute
                            dateInput.setAttribute('max', maxDateTime);

                            // If editing, preserve the existing value (must be in same format)
                            const existingValue = dateInput.value;
                            if (existingValue && existingValue > maxDateTime) {
                                dateInput.value = maxDateTime; // optional: reset to max if existing value is future
                            }
                        </script>


                        <!-- Amount -->
                        <div class="form-group">
                            <label><i class="fas fa-dollar-sign icon-style"></i> Amount <span
                                    class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" id="amount" name="amount"
                                required>
                        </div>

                        <!-- Service -->
                        <div class="form-group">
                            <label><i class="fas fa-briefcase icon-style"></i> Service <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="service" name="service" required>
                        </div>

                        @php
                            $currentProjectTypeId = (int) \App\Models\Setting::getValue('project_type_id', 1);
                        @endphp
                        @if($currentProjectTypeId !== 3)
                        <!-- Comment -->
                        <div class="form-group">
                            <label><i class="fas fa-comment icon-style"></i> Comment <span
                                                        class="text-danger">*</span></label>
                            <textarea class="form-control" id="comment" name="comment" rows="3" style="border-radius:20px;"></textarea>
                        </div>
                        @endif

                        <div id="editSuccessExpense" class="alert alert-success" style="display:none;"></div>
                        {{-- <div id="editErrorExpense" class="alert alert-danger" style="display:none;"></div> --}}

                        <button type="submit" class="btn btn-primary submit-btn d-block m-auto mt-3">
                            Update Expense
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>

    <script>
        $(document).ready(function() {
            const expenseId = "{{ $expense_id }}";

            function loadUsers(selectedUserId = null) {
                let branchId = localStorage.getItem("selectedBranchId");
                $.ajax({
                    url: "/api/userss",
                    type: "GET",
                    data: {
                        branch_id: branchId // ✅ send branch id
                    },

                    success: function(data) {
                        let userdropdown = $('select[name="user_id"]');

                        if (userdropdown.length === 0) {
                            console.log("Dropdown not found! Check your HTML.");
                            return;
                        }

                        userdropdown.empty();
                        userdropdown.append('<option value="">Select Staff</option>');

                        let staffs = data.users || [];

                        $.each(staffs, function(key, user) {
                            // Capitalize first letter only
                            const capitalizedName = user.fullname.charAt(0).toUpperCase() + user
                                .fullname.slice(
                                    1);

                            userdropdown.append(
                                `<option value="${user.id}">${capitalizedName}</option>`
                            );
                        });

                        // Initialize Select2 plugin
                        userdropdown.select2({
                            placeholder: "Select staff",
                            allowClear: true,
                            width: '100%'
                        });

                        // Set search input placeholder after opening dropdown
                        userdropdown.on('select2:open', function() {
                            $('.select2-search__field').attr('placeholder', 'Search staff');
                        });

                        // ✅ Set the stored user as selected
                        if (selectedUserId) {
                            userdropdown.val(selectedUserId).trigger('change');
                        }
                    },
                    error: function(xhr) {
                        console.log("API Error:", xhr.status, xhr.responseText);
                    }
                });
            }


            function fetchExpenseDetails() {
                $.ajax({
                    url: `/api/expenses/${expenseId}`,
                    type: 'GET',
                    success: function(res) {
                        const exp = res.data;
                        $("#date_time").val(exp.date_time.replace(' ', 'T'));
                        $("#amount").val(exp.amount);
                        $("#service").val(exp.service);
                        $("#comment").val(exp.comment);
                        loadUsers(exp.user_id);
                    }
                });
            }

            if (expenseId) fetchExpenseDetails();

            // Validation
            $('#editExpenseForm').validate({
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
                    user_id: "Please select a staff",
                    date_time: "Please select date & time",
                    amount: {
                        required: "Please enter the amount",
                        number: "Please enter a valid number",
                        min: "Amount cannot be negative"
                    },
                    service: "Please enter service name",
                    @if($currentProjectTypeId !== 3)
                    comment: "Please enter comment"
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
                },
                submitHandler: function(form) {
                    const formData = {
                        user_id: $("#userSelect").val(),
                        date_time: $("#date_time").val(),
                        amount: $("#amount").val(),
                        service: $("#service").val(),
                        comment: $("#comment").val()
                    };

                    $.ajax({
                        url: `/api/expenses/${expenseId}`,
                        type: 'PUT',
                        contentType: 'application/json',
                        data: JSON.stringify(formData),
                        success: function() {
                            $("#editSuccessExpense").text("Expense updated successfully!")
                                .fadeIn().delay(1000).fadeOut();
                            setTimeout(() => window.location.href =
                                "{{ route('expense.index') }}", 1500);
                        },
                        error: function(xhr) {
                            let errorMessage = '';
                            if (xhr.status === 422) {
                                $.each(xhr.responseJSON.errors, (key, msgs) => {
                                    errorMessage += msgs[0] + '<br>';
                                });
                            }
                            $('#editErrorExpense').html(errorMessage).fadeIn().delay(5000)
                                .fadeOut();
                        }
                    });
                }
            });

        });
    </script>
@endsection
