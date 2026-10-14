$(document).ready(function () {

    /* ----------------------------------------------------
       1️⃣  SET TODAY DATE
    ---------------------------------------------------- */
    const today = new Date().toISOString().split('T')[0];
    $('#todayOnlyDate').attr('min', today).attr('max', today).val(today);


    /* ----------------------------------------------------
       2️⃣  TREATMENT PRICE AUTO-FILL + PAYABLE AMOUNT
    ---------------------------------------------------- */
    let payableAmount = 0;

    $('#treatmentDropdown').on('change', function () {
        let selected = $(this).find(':selected');
        let price = selected.data('price') || 0;

        payableAmount = parseFloat(price);
        $('#amount').val(price);

        $("#cash_amount, #online_amount").val("");
        $("#cashError, #onlineError").text("");
    });


    /* ----------------------------------------------------
       3️⃣  PAYMENT TYPE BEHAVIOR
    ---------------------------------------------------- */
    $('#payment_mode').on('change', function () {
        let mode = $(this).val();

        $('#paid_type').val('');
        $('#paid_amount_div, #remain_amount_div, #cash_amount_div, #online_amount_div')
            .addClass('d-none');

        $('#paid_amount,#remain_amount,#cash_amount,#online_amount').val('');

        if (mode === 'cash' || mode === 'online' || mode === 'cash+online') {
            $('#paid_amount_type').removeClass('d-none');
        } else {
            $('#paid_amount_type').addClass('d-none');
        }
    });

    $('#paid_type').on('change', function () {
        let type = $(this).val();
        let mode = $('#payment_mode').val();

        $('#paid_amount_div, #remain_amount_div, #cash_amount_div, #online_amount_div')
            .addClass('d-none');

        if (mode === 'cash+online') {
            if (type === 'fully') {
                $('#cash_amount_div, #online_amount_div').removeClass('d-none');
            } else if (type === 'partial') {
                $('#cash_amount_div, #online_amount_div, #remain_amount_div').removeClass('d-none');
            }
        } else {
            if (type === 'partial') {
                $('#paid_amount_div, #remain_amount_div').removeClass('d-none');
            }
        }
    });


    /* ----------------------------------------------------
       4️⃣  CASH + ONLINE ERROR VALIDATION (FULL PAYMENT)
    ---------------------------------------------------- */
    function validateAmounts() {
        let cash = parseFloat($("#cash_amount").val()) || 0;
        let online = parseFloat($("#online_amount").val()) || 0;
        let total = cash + online;
        let paidType = $("#paid_type").val();

        $("#cashError, #onlineError").addClass("d-none").text("");

        if (paidType === "fully") {
            if ((cash > 0 || online > 0) && total !== payableAmount) {
                if ($("#cash_amount").is(":focus")) {
                    $("#cashError").text(`⚠️ Cash + Online must equal payable amount (${payableAmount})`)
                        .removeClass("d-none");
                }
                if ($("#online_amount").is(":focus")) {
                    $("#onlineError").text(`⚠️ Cash + Online must equal payable amount (${payableAmount})`)
                        .removeClass("d-none");
                }
            }
        }
    }

    $("#cash_amount, #online_amount").on("input", validateAmounts);


    /* ----------------------------------------------------
       5️⃣  AUTO CALCULATE REMAIN AMOUNT
    ---------------------------------------------------- */
    $('#cash_amount, #online_amount, #amount').on('input', function () {
        const total = parseFloat($('#amount').val()) || 0;
        const cash = parseFloat($('#cash_amount').val()) || 0;
        const online = parseFloat($('#online_amount').val()) || 0;

        $('#remain_amount').val(Math.max(total - (cash + online), 0).toFixed(2));
    });

    $('#paid_amount, #amount').on('input', function () {
        const total = parseFloat($('#amount').val()) || 0;
        const paid = parseFloat($('#paid_amount').val()) || 0;

        $('#remain_amount').val(Math.max(total - paid, 0).toFixed(2));
    });


    /* ----------------------------------------------------
       6️⃣  SELECT2 INITIALIZATION
    ---------------------------------------------------- */
    $('.select2').select2({ width: '100%' });


    /* ----------------------------------------------------
       7️⃣  LOAD TREATMENTS
    ---------------------------------------------------- */
    let branchId = localStorage.getItem('selectedBranchId');

    $.ajax({
        url: "/api/treatments",
        type: "GET",
        data: { branch_id: branchId },
        xhrFields: { withCredentials: true },
        success: function (data) {
            let dropdown = $('select[name="treatment_id"]');

            dropdown.empty().append('<option value="">Select Treatment</option>');

            $.each(data.treatments || [], function (key, t) {
                dropdown.append(`
                    <option value="${t.id}" 
                        data-doctor-id="${t.doctor_id}" 
                        data-price="${t.price}">
                        ${t.name.charAt(0).toUpperCase() + t.name.slice(1)}
                    </option>
                `);
            });

            dropdown.select2({
                placeholder: "Select Treatment",
                allowClear: true,
                width: '100%'
            });
        }
    });


    /* ----------------------------------------------------
       8️⃣  LOAD MACHINES + ADD NEW MACHINE
    ---------------------------------------------------- */
    function loadMachines() {
        $.ajax({
            url: "/api/machines",
            type: "GET",
            data: { branch_id: branchId },
            xhrFields: { withCredentials: true },
            success: function (data) {
                let dropdown = $('#machineDropdown');
                dropdown.empty().append('<option value="">Select Machines</option>');

                $.each(data, function (index, m) {
                    dropdown.append(`
                        <option value="${m.id}" data-price="${m.price}">
                            ${m.name.charAt(0).toUpperCase() + m.name.slice(1)}
                        </option>
                    `);
                });

                dropdown.select2({
                    placeholder: "Select Machines",
                    allowClear: true,
                    width: '100%'
                });
            }
        });
    }

    loadMachines();

    $('#machineForm').on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            url: "/machines",
            method: "POST",
            data: {
                name: $('#machineName').val(),
                description: $('#machinedescription').val(),
                price: $('#price').val(),
                branch_id: branchId,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                $('#machineSuccess').text(res.message).fadeIn();
                setTimeout(() => {
                    $('#addMachineModal').modal('hide');
                    $('#machineForm')[0].reset();
                    loadMachines();
                }, 1200);
            }
        });
    });


    /* ----------------------------------------------------
       9️⃣  LOAD PATIENTS
    ---------------------------------------------------- */
    $.ajax({
        url: "/api/patientss",
        type: "GET",
        data: { branch_id: branchId },
        success: function (res) {
            let dropdown = $('#patientDropdown');
            dropdown.empty().append('<option value="">Select Patient</option>');

            $.each(res.patients || [], function (i, p) {
                dropdown.append(`<option value="${p.id}">${p.fullname}</option>`);
            });

            dropdown.select2({
                placeholder: "Select Patient",
                allowClear: true,
                width: '100%'
            });
        }
    });


    /* ----------------------------------------------------
       🔟 FORM VALIDATION + SUBMISSION
    ---------------------------------------------------- */
    $("#treatmentBookingForm").validate({
        rules: {
            patient_id: { required: true },
            treatment_id: { required: true },
            payment_date: { required: true },
            plan: { required: true },
            amount: { required: true, number: true, min: 1 },
            payment_mode: { required: true },
            paid_type: {
                required: function () {
                    return $("#payment_mode").val() !== "";
                }
            }
        },

        submitHandler: function (form) {

            let total = parseFloat($("#amount").val());
            let cash = parseFloat($("#cash_amount").val()) || 0;
            let online = parseFloat($("#online_amount").val()) || 0;
            let paymentMode = $("#payment_mode").val();
            let paidType = $("#paid_type").val();

            if (paymentMode === "cash+online" && paidType === "fully") {
                if ((cash + online) !== total) {
                    $('#bookingError').text(
                        `⚠️ Cash + Online must equal payable amount (${total})`
                    ).show();
                    return false;
                }
            }

            let formData = {
                patient_id: $("#patientDropdown").val(),
                treatment_id: $("#treatmentDropdown").val(),
                machine_id: $("#machineDropdown").val(),
                plan: $("#plan").val(),
                amount: $("#amount").val(),
                payment_mode: paymentMode,
                payment_date: $("input[name='payment_date']").val(),
                paid_type: paidType,
                paid_amount: $("#paid_amount").val() || null,
                remain_amount: $("#remain_amount").val() || null,
                cash: cash,
                online: online,
                branch_id: $("#branch_id").val()
            };

            $.ajax({
                url: "/api/treatment_booking",
                type: "POST",
                data: JSON.stringify(formData),
                contentType: "application/json",
                success: function (res) {
                    $('#bookingSuccess').text(res.message).show();
                    form.reset();
                    setTimeout(() => {
                        window.location.href = "/treatment_booking";
                    }, 1500);
                }
            });
        }
    });

});
