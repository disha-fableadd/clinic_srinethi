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
        /* padding: 2px 10px !important; */
        border: none;
        font-size: 14px;
        font-weight: normal !important;
        text-align: center;
        max-width: fit-content;
    }




    .daily_data-title {
        padding-left: 145px !important;
        text-align: center !important;
    }

    .daily_data-button {
        padding-right: 8px !important;
        text-align: center !important;
    }

    @media screen and (max-width:768px) {
        .daily_data-title {
            padding-left: 15px !important;
            text-align: left !important;
        }

        .daily_data-button {
            padding-right: 15px !important;
            text-align: right !important;
        }

    }

    @media screen and (max-width:767px) {
        .daily_data-title {
            padding-left: 15px !important;
            text-align: left !important;
        }

        .daily_data-button {
            padding-right: 15px !important;
            text-align: right !important;
        }

        .daily_data-form {
            /* height: 820px !important; */
        }

    }
</style>
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="row" style="padding-top:15px">
                <div class=" col-6">
                    <h4 class="page-title  daily_data-title">Add Daily Register</h4>
                </div>
                @if (app('hasPermission')(26, 'view'))
                    <div class=" col-6 m-b-2 eye-btn daily_data-button">
                        <a href="{{ route('daily_data.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                            <i class="fa fa-arrow-left m-r-5 icon3  "></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    </div>
                @endif
            </div>

            <div class="row ">
                <div class="col-12">
                    <form id="daily_dataForm" method="POST" class="form-container all-form daily_data-form"
                        style="width: 60%;">

                        @csrf
                        <input type="hidden" name="branch_id" id="branch_id">

                        <div class="row">

                            <!-- Patient Name -->
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <div class="d-flex align-items-center justify-content-between">


                                        <label class="mr-2"><i class="fas fa-medkit icon-style"></i> Patient <span
                                                class="text-danger">*</span></label>

                                        @if (app('hasPermission')(5, 'create'))
                                            <a href="{{ route('patients.create') }}" target="_blank"
                                                class="btn btn-primary btn-sm">
                                                <i class="fas fa-plus"></i> <span class="hdr-btn-text">Add</span> 
                                            </a>
                                        @endif

                                    </div>
                                    <select class="form-control select2" name="patient_id" id="patientDropdown" required>
                                        <option value="">Select Patient</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">

                                    <div class="d-flex align-items-center justify-content-between">


                                        <label class="mr-2"><i class="fas fa-medkit icon-style"></i> Treatment <span
                                                class="text-danger">*</span></label>

                                        @if (app('hasPermission')(7, 'create'))
                                            <a href="{{ route('treatment.create') }}" target="_blank"
                                                class="btn btn-primary btn-sm">
                                                <i class="fas fa-plus"></i> <span class="hdr-btn-text">Add</span> 
                                            </a>
                                        @endif

                                    </div>
                                    <select class="form-control select2" name="treatment_id" id="treatmentDropdown"
                                        required>
                                        <option value="">Select Treatment</option>
                                    </select>

                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="mr-2"><i class="fas fa-comment-alt icon-style"></i> Comment</label>
                                    <textarea name="comment" id="comment" class="form-control" rows="3" placeholder="Enter any comments or notes..."
                                        style="border-radius:10px"></textarea>
                                </div>
                            </div>

                            <!-- Date -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-calendar-day icon-style"></i> Date <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="date" class="form-control" id="todayOnlyDate" required>
                                </div>
                            </div>

                            <script>
                                const today = new Date().toISOString().split('T')[0];
                                $('#todayOnlyDate').attr('min', today).attr('max', today).val(today);
                            </script>
                            <!-- By -->
                            <div class="col-md-6">


                                <div class="form-group">

                                    <div class="d-flex align-items-center justify-content-between">


                                        <label class="mr-2"><i class="fas fa-medkit icon-style"></i> Collected By <span
                                                class="text-danger">*</span></label>
                                    </div>
                                    <select class="form-control select2" name="staff_id" id="userdropdown" required>
                                        <option value="">Select Staff</option>
                                    </select>

                                </div>



                            </div>


                            <!-- Payment -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>₹ Payment <span
                                            class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="payment" id="payment" class="form-control"
                                        placeholder="Enter total payment" required>
                                </div>
                            </div>

                            <!-- Payment Mode -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-money-bill icon-style"></i> Payment Mode <span
                                            class="text-danger">*</span></label>
                                    <select name="payment_mode" class="form-control select2" id="payment_mode" required>
                                        <option value="">Select Payment Mode</option>
                                        <option value="cash">Cash</option>
                                        <option value="online">Online</option>
                                        <option value="cash+online">Cash + Online</option>
                                    </select>
                                </div>
                            </div>


                            <!-- Paid Type -->
                            <div class="col-md-4 d-none" id="amount_paid_type">
                                <div class="form-group">
                                    <label><i class="fas fa-hand-holding-usd icon-style"></i> Paid Type <span
                                            class="text-danger">*</span></label>
                                    <select name="paid_type" class="form-control select2" id="paid_type" required>
                                        <option value="">Select Paid Type</option>
                                        <option value="fully">Fully </option>
                                        <option value="partial">Partial </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 d-none" id="cash_amount_div">
                                <div class="form-group">
                                    <label><i class="fas fa-coins icon-style"></i> Cash Amount</label>
                                    <input type="number" step="0.01" name="cash_amount" id="cash_amount"
                                        class="form-control" placeholder="Enter cash amount">
                                    <span id="cash_error" class="text-danger small"></span>
                                </div>
                            </div>

                            <!-- Online Amount -->
                            <div class="col-md-4 d-none" id="online_amount_div">
                                <div class="form-group">
                                    <label><i class="fas fa-credit-card icon-style"></i> Online Amount</label>
                                    <input type="number" step="0.01" name="online_amount" id="online_amount"
                                        class="form-control" placeholder="Enter online amount">
                                    <span id="online_error" class="text-danger small"></span>
                                </div>
                            </div>


                            <!-- Amount Paid (show if partial) -->
                            <div class="col-md-4 d-none" id="amount_paid_div">
                                <div class="form-group">
                                    <label><i class="fas fa-coins icon-style"></i> Amount Paid <span
                                            class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="amount_paid" id="amount_paid"
                                        class="form-control" placeholder="Enter paid amount">
                                </div>
                            </div>

                            <!-- Pending Amount (auto calculated) -->
                            <div class="col-md-4 d-none" id="pending_amount_div">
                                <div class="form-group">
                                    <label><i class="fas fa-hourglass-half icon-style"></i> Pending Amount</label>
                                    <input type="number" step="0.01" name="pending_amount" id="pending_amount"
                                        class="form-control" readonly>
                                </div>
                            </div>

                        </div>





                        <div id="daily_datasuccessMessage" class="alert alert-success" style="display:none;"></div>
                        {{-- <div id="daily_dataerrorMessage" class="alert alert-danger" style="display:none;"></div> --}}
                        <button type="submit" class="btn btn-primary submit-btn d-block m-auto"
                            style="padding:8px 50px; border-radius:50px; ">
                            Submit
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>



    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let storedBranchId = localStorage.getItem('selectedBranchId');
            if (storedBranchId) {
                document.getElementById('branch_id').value = storedBranchId;
            }
        });


        $(document).ready(function() {

            // Load treatment price on selection
            $(document).on('change', '#treatmentDropdown', function() {
                let price = $(this).find(':selected').data('price');
                $('#payment').val(price ? price : '');
                // Reset payment fields when treatment changes
                $('#amount_paid, #pending_amount, #cash_amount, #online_amount').val('');
                $('#amount_paid_div, #pending_amount_div, #cash_amount_div, #online_amount_div').addClass(
                    'd-none');
                $('#cash_error').text('');
            });

            // Handle payment mode selection
            $('#payment_mode').on('change', function() {
                let mode = $(this).val();
                $('#amount_paid_type').removeClass('d-none');

                // Hide all payment fields initially
                $('#amount_paid_div, #pending_amount_div, #cash_amount_div, #online_amount_div').addClass(
                    'd-none');
                $('#cash_error').text('');
                $('#cash_amount, #online_amount, #amount_paid, #pending_amount').val('');
            });

            // Handle paid type (full / partial)
            $('#paid_type').on('change', function() {
                const type = $(this).val();
                const mode = $('#payment_mode').val();
                const total = parseFloat($('#payment').val()) || 0;

                // Reset
                $('#amount_paid_div, #pending_amount_div, #cash_amount_div, #online_amount_div').addClass(
                    'd-none');
                $('#cash_error').text('');
                $('#cash_amount, #online_amount, #amount_paid, #pending_amount').val('');

                if (mode === 'cash+online') {
                    $('#cash_amount_div, #online_amount_div').removeClass('d-none');
                    if (type === 'partial') {
                        $('#pending_amount_div').removeClass('d-none');
                        $('#cash_amount').val(0);
                        $('#online_amount').val(0);
                        $('#pending_amount').val(total.toFixed(2));
                    } else { // full
                        $('#cash_amount').val(total);
                        $('#online_amount').val(0);
                        $('#pending_amount').val(0);
                    }
                } else if (mode === 'cash' || mode === 'online') {
                    $('#amount_paid_div').removeClass('d-none');
                    if (type === 'partial') {
                        $('#pending_amount_div').removeClass('d-none');
                        $('#amount_paid').val(0);
                        $('#pending_amount').val(total.toFixed(2));
                    } else { // full
                        $('#amount_paid').val(total);
                        $('#pending_amount').val(0);
                    }
                }
            });

            // Cash + Online validation & pending calculation
            $('#cash_amount, #online_amount').on('input', function() {
                const total = parseFloat($('#payment').val()) || 0;
                const cash = parseFloat($('#cash_amount').val()) || 0;
                const online = parseFloat($('#online_amount').val()) || 0;
                const sum = cash + online;
                const paidType = $('#paid_type').val();

                if ($('#payment_mode').val() === 'cash+online') {
                    if (paidType === 'fully') {
                        if (sum !== total) {
                            $('#cash_error').text(`⚠ Cash + Online must equal payable amount (${total})`);
                        } else {
                            $('#cash_error').text('');
                        }
                    } else if (paidType === 'partial') {
                        $('#pending_amount').val(Math.max(total - sum, 0).toFixed(2));
                        $('#cash_error').text('');
                    }
                }
            });

            // Auto calculate pending for single cash/online payments
            $('#amount_paid').on('input', function() {
                const total = parseFloat($('#payment').val()) || 0;
                const paid = parseFloat($('#amount_paid').val()) || 0;
                $('#pending_amount').val(Math.max(total - paid, 0).toFixed(2));
            });



            let branchId = localStorage.getItem('selectedBranchId');
            $('#branch_id').val(branchId);

            $.ajax({
                url: "/api/patientss",
                type: "GET",
                data: {
                    branch_id: branchId
                },
                dataType: "json",
                xhrFields: {
                    withCredentials: true // ✅ send session cookie for Sanctum
                },
                success: function(response) {
                    let patientDropdown = $('#patientDropdown');

                    if (patientDropdown.length === 0) {
                        console.error("Dropdown not found! Check your HTML.");
                        return;
                    }

                    patientDropdown.empty().append('<option value="">Select Patient</option>');

                    if (!response.patients || response.patients.length === 0) {
                        console.warn("No patients found for this branch.");
                        return;
                    }

                    $.each(response.patients, function(index, patient) {
                        const capitalizedName = patient.fullname.charAt(0).toUpperCase() +
                            patient.fullname.slice(1);

                        patientDropdown.append(
                            `<option value="${patient.id}" data-treatment-id="${patient.treatment_id}">
                    ${capitalizedName}
                </option>`
                        );
                    });

                    patientDropdown.select2({
                        placeholder: "Select Patient",
                        allowClear: true,
                        width: '100%'
                    });

                    patientDropdown.on('select2:open', function() {
                        $('.select2-search__field').attr('placeholder', 'Search Patient');
                    });
                },
                error: function(xhr) {
                    console.error("API Error:", xhr.status, xhr.responseText);
                }
            });


           $.ajax({
    url: "/api/treatments",
    type: "GET",
    data: {
        branch_id: branchId
    },
    xhrFields: {
        withCredentials: true // send session cookie for Sanctum
    },
    success: function (data) {
        let treatmentDropdown = $('select[name="treatment_id"]');

        if (!treatmentDropdown.length) return;

        treatmentDropdown.empty().append('<option value="">Select Treatment</option>');

        let treatmentsList = data.treatments || [];

        $.each(treatmentsList, function (key, treatment) {
            const name = treatment.name.charAt(0).toUpperCase() + treatment.name.slice(1);

            // 👇 store price and doctor_id as data attributes
            treatmentDropdown.append(
                `<option value="${treatment.id}" data-price="${treatment.price}" data-doctor-id="${treatment.doctor_id}">
                    ${name}
                </option>`
            );
        });

        // Initialize Select2
        treatmentDropdown.select2({
            placeholder: "Select Treatment",
            allowClear: true,
            width: '100%'
        });

        // Search placeholder inside dropdown
        treatmentDropdown.on('select2:open', function () {
            $('.select2-search__field').attr('placeholder', 'Search Treatment');
        });

        // 👇 When treatment selected, auto-fill price in payment input
        treatmentDropdown.on('change', function () {
            let selectedOption = $(this).find('option:selected');
            let price = selectedOption.data('price');

            if (price) {
                $('#payment').val(price); // auto fill
            } else {
                $('#payment').val(''); // clear if no selection
            }
        });
    },
    error: function (xhr) {
        console.log("API Error:", xhr.status, xhr.responseText);
    }
});


            // --- AJAX: Staff ---
            $.ajax({
                url: "/api/userss",
                type: "GET",
                data: {
                    branch_id: branchId
                },
                success: function(data) {
                    let userdropdown = $('select[name="staff_id"]');
                    if (!userdropdown.length) return;

                    userdropdown.empty().append('<option value="">Select Staff</option>');

                    $.each(data.users || [], function(_, user) {
                        // Capitalize first letter of each word
                        const capitalizedName = user.fullname.replace(/\b\w/g, function(char) {
                            return char.toUpperCase();
                        });

                        userdropdown.append(
                            `<option value="${user.id}">${capitalizedName}</option>`
                        );
                    });

                    userdropdown.select2({
                            placeholder: "Select staff",
                            allowClear: true,
                            width: '100%'
                        })
                        .on('select2:open', function() {
                            $('.select2-search__field').attr('placeholder', 'Search staff');
                        });
                }
            });

           



            $('.select2').select2();

            // --- Form validation & submission ---
            var form = $('#daily_dataForm');
            form.validate({
                rules: {
                    patient_id: "required",
                    treatment_id: "required",
                    date: "required",
                    amount: {
                        required: true,
                        number: true,
                        min: 0
                    },
                    status: "required",
                    staff_id: "required"
                },
                messages: {
                    patient_id: "Please select a patient",
                    treatment_id: "Please select a treatment",
                    date: "Please select a date",
                    amount: {
                        required: "Please enter the amount",
                        number: "Amount must be a number",
                        min: "Amount must be at least 0"
                    },
                    status: "Please select a status",
                    staff_id: "Please select staff"
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
                   patient_id: $('#patientDropdown').val(),
                    treatment_id: $("select[name='treatment_id']").val(),
                    date: $("input[name='date']").val(),
                    staff_id: $("select[name='staff_id']").val(),
                    payment: $("#payment").val(),
                    // payment_mode: $("#payment_mode").val(),
                    payment_mode: $("#payment_mode").val().toLowerCase(),
                    cash_amount: $("#cash_amount").val() || 0,
                    online_amount: $("#online_amount").val() || 0,
                    paid_type: $("#paid_type").val(),
                    amount_paid: $("#amount_paid").val() || 0,
                    pending_amount: $("#pending_amount").val() || 0,
                    comment: $("#comment").val(),
                    branch_id: $("#branch_id").val()
                };

                $.ajax({
                    url: '/api/daily-data',
                    type: 'POST',
                    data: JSON.stringify(formData),
                    contentType: 'application/json',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#daily_datasuccessMessage').text('Record added successfully.')
                            .fadeIn();
                        $('#daily_dataerrorMessage').hide();
                        $('#daily_dataForm')[0].reset();
                        setTimeout(() => {
                            window.location.href = "{{ route('daily_data.index') }}";
                        }, 1500);
                    },
                    error: function(xhr) {
                        let errorMsg = 'Something went wrong.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            errorMsg = '';
                            $.each(xhr.responseJSON.errors, function(_, value) {
                                errorMsg += value[0] + '<br>';
                            });
                        }
                        $('#daily_dataerrorMessage').html(errorMsg).fadeIn();
                        $('#daily_datasuccessMessage').hide();
                    }
                });
            });

        });
    </script>
@endsection

