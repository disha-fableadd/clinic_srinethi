$(document).ready(function () {
    const apiToken = (typeof token !== "undefined" && token) ? token : localStorage.getItem("token");
    const branchId = localStorage.getItem("selectedBranchId");
    const bookingId = window.bookingId || $(".page-wrapper").data("treatment-booking-id");

    $(".select2").select2({ width: "100%" });

    if (!bookingId) {
        $("#bookingError").text("Booking ID is missing.").show();
        return;
    }

    if (!apiToken) {
        $("#bookingError").text("Authentication token is missing. Please log in again.").show();
        return;
    }

    function authorizedGet(url, data = {}) {
        return $.ajax({
            url,
            type: "GET",
            data,
            headers: {
                "Authorization": "Bearer " + apiToken
            }
        });
    }

    function normalizeMachineIds(value) {
        if (!value) return [];
        if (Array.isArray(value)) {
            return value.map(String);
        }
        if (typeof value === "string") {
            try {
                const parsed = JSON.parse(value);
                if (Array.isArray(parsed)) {
                    return parsed.map(String);
                }
            } catch (e) {
                return value.split(",").map(item => item.trim()).filter(Boolean);
            }
        }
        return [String(value)];
    }

    function loadPatients() {
        return authorizedGet("/api/patientss", { branch_id: branchId }).then(function (res) {
            const dropdown = $("#patientDropdown").empty().append('<option value="">Select Patient</option>');
            (res.patients || []).forEach(function (patient) {
                dropdown.append(`<option value="${patient.id}">${patient.fullname}</option>`);
            });
        });
    }

    function loadTreatments() {
        return authorizedGet("/api/treatments", { branch_id: branchId }).then(function (res) {
            const dropdown = $("#treatmentDropdown").empty().append('<option value="">Select Treatment</option>');
            const treatments = Array.isArray(res) ? res : (res.treatments || []);

            treatments.forEach(function (treatment) {
                dropdown.append(`<option value="${treatment.id}">${treatment.name}</option>`);
            });
        });
    }

    function loadMachines() {
        return authorizedGet("/api/machines", { branch_id: branchId }).then(function (res) {
            const dropdown = $("#machineDropdown").empty();
            (res || []).forEach(function (machine) {
                dropdown.append(`<option value="${machine.id}">${machine.name}</option>`);
            });
        });
    }

    function loadBooking() {
        return authorizedGet(`/api/treatment_booking/${bookingId}`);
    }

    $.when(loadPatients(), loadTreatments(), loadMachines(), loadBooking())
        .done(function (_patientsRes, _treatmentsRes, _machinesRes, bookingRes) {
            const response = bookingRes[0] || {};
            const booking = response.booking;

            if (!booking) {
                $("#bookingError").text("Booking details not found.").show();
                return;
            }

            $("#patientDropdown").val(String(booking.patient_id)).trigger("change");
            $("#treatmentDropdown").val(String(booking.treatment_id)).trigger("change");
            $("#machineDropdown").val(normalizeMachineIds(booking.machine_id)).trigger("change");
            $("#todayOnlyDate").val(booking.payment_date);
            $("#plan").val(booking.plan).trigger("change");
            $("#edittreatmentBookingForm").data("id", bookingId);
            $("#bookingError").hide();
        })
        .fail(function (xhr) {
            console.error("Failed to load treatment booking edit data:", xhr?.status, xhr?.responseText);
            const errorMessage = xhr?.responseJSON?.message || "Failed to load booking data.";
            $("#bookingError").text(errorMessage).show();
        });

    $("#edittreatmentBookingForm").on("submit", function (e) {
        e.preventDefault();

        $.ajax({
            url: "/api/treatment_booking/" + bookingId,
            type: "PUT",
            data: $(this).serialize(),
            headers: {
                "Authorization": "Bearer " + apiToken
            },
            success: function (response) {
                if (response.status) {
                    $("#bookingSuccess").text(response.message).show();
                    $("#bookingError").hide();
                    setTimeout(function () {
                        window.location.href = window.treatmentBookingIndexRoute;
                    }, 1500);
                } else {
                    $("#bookingError").text(response.message || "Failed to update booking.").show();
                    $("#bookingSuccess").hide();
                }
            },
            error: function (xhr) {
                let errorMsg = xhr.responseJSON?.message || "Something went wrong.";

                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    errorMsg = Object.values(xhr.responseJSON.errors).flat().join(" ");
                }

                $("#bookingError").text(errorMsg).show();
                $("#bookingSuccess").hide();
            }
        });
    });
});
