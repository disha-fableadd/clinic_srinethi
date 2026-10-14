@php
    use App\Models\Appointments;
    $appointments = Appointments::find($appointment_id);
@endphp

<!-- Reschedule Appointment Modal -->
<div class="modal fade" id="rescheduleModal_{{ $appointment_id }}" tabindex="-1" aria-labelledby="rescheduleModalLabel"
    aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#cfece0;">
                <h5 class="modal-title" id="rescheduleModalLabel">Reschedule Appointment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="rescheduleForm_{{ $appointment_id }}">
                    @csrf
                    <input type="hidden" name="branch_id" id="branch_id">
                    <input type="hidden" name="appointment_id" id="rescheduleAppointmentId"
                        value="{{ $appointment_id }}">

                    <div class="form-group">
                        <label for="rescheduleDate">Date</label>
                        <input type="date" class="form-control" name="date" id="rescheduleDate"
                            value="{{ $appointments->date ?? '' }}">
                        <div class="invalid-feedback">Please select a valid date.</div>
                    </div>

                    <div class="form-group">
                        <label for="rescheduleTime">Time</label>
                        <input type="time" class="form-control" name="time" id="rescheduleTime"
                            value="{{ $appointments->duration ?? '' }}">
                        <div class="invalid-feedback">Please select a valid time.</div>
                    </div>

                    <div class="form-group">
                        <label for="rescheduleComment">Comment</label>
                        <textarea class="form-control" name="comment" id="rescheduleComment" rows="1"></textarea>
                        <div class="invalid-feedback">Comment is required.</div>
                    </div>

                    <div class="form-group">
                        <label for="rescheduleStatus">Status</label>
                        <select class="form-control" name="status" id="rescheduleStatus">
                            <option value="" disabled {{ empty($appointments->status) ? 'selected' : '' }}>Select Status
                            </option>
                            <option value="upcoming" {{ $appointments->status == 'upcoming' ? 'selected' : '' }}>Upcoming
                            </option>
                            <option value="confirmed" {{ $appointments->status == 'confirmed' ? 'selected' : '' }}>
                                Confirmed</option>
                            <option value="completed" {{ $appointments->status == 'completed' ? 'selected' : '' }}>
                                Completed</option>
                            <option value="cancelled" {{ $appointments->status == 'cancelled' ? 'selected' : '' }}>
                                Cancelled</option>
                            <option value="follow-up" {{ $appointments->status == 'follow-up' ? 'selected' : '' }}>
                                Follow-Up</option>

                            <!-- <option value="" selected disabled>Select Status</option>
                            <option value="upcoming">Upcoming</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="follow-up">Follow-Up</option> -->
                        </select>
                        <div class="invalid-feedback">Please select a status.</div>
                    </div>

                    <div id="reschedulesuccessMessage" class="alert alert-success success-message"
                        style="display:none;"></div>
                    <div id="reschedulerrorMessage" class="alert alert-danger error-message" style="display:none;">
                    </div>

                    <button type="submit" class="btn btn-primary float-right">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
    let storedBranchId = localStorage.getItem('selectedBranchId');
    if (storedBranchId) {
        document.getElementById('branch_id').value = storedBranchId;
    }
});
    // $(document).on("click", ".reschedule-appointment", async function () {
    //     let appointmentId = $(this).data("id");
    //     var form = $("#rescheduleForm_" + appointmentId);

    //     if (form.length) {
    //         form[0].reset();
    //     }
    //     const today = new Date().toISOString().split("T")[0];
    //     form.find("input[name='date']").attr("min", today);

    //     $("#rescheduleModal_" + appointmentId).modal("show");
    // });

    $(document).on("click", ".reschedule-appointment", function () {
    let appointmentId = $(this).data("id");
    let form = $("#rescheduleForm_" + appointmentId);

    if (form.length) {
        form[0].reset();
    }

    const today = new Date().toISOString().split("T")[0];
    form.find("input[name='date']").attr("min", today);

    // ✅ Set branch_id AFTER reset
    let storedBranchId = localStorage.getItem('selectedBranchId');
    if (storedBranchId) {
        form.find("input[name='branch_id']").val(storedBranchId);
    }

    $("#rescheduleModal_" + appointmentId).modal("show");
});


    $(document).ready(function () {
        $(document).off("submit", "form[id^='rescheduleForm_']").on("submit", "form[id^='rescheduleForm_']", async function (e) {
            e.preventDefault();
            // console.log('Submit clicked');

            let formElement = $(this); // Reference the current form
            let isValid = true;

            // Remove previous error messages
            formElement.find(".invalid-feedback").remove();
            formElement.find(".form-control").removeClass("is-invalid");

            // Get values from form inputs
            let date = formElement.find("input[name='date']").val()?.trim();
            let time = formElement.find("input[name='time']").val()?.trim();
            let comment = formElement.find("textarea[name='comment']").val()?.trim();
            let status = formElement.find("select[name='status']").val();
            let appointmentId = formElement.find("input[name='appointment_id']").val();
            let csrfToken = formElement.find("input[name='_token']").val(); // Correctly get CSRF token from form

            if (!date) {
                isValid = false;
                formElement.find("input[name='date']").addClass("is-invalid").after('<div class="invalid-feedback">Date is required.</div>');
            }

            if (!time) {
                isValid = false;
                formElement.find("input[name='time']").addClass("is-invalid").after('<div class="invalid-feedback">Time is required.</div>');
            }

            // if (!comment) {
            //     isValid = false;
            //     formElement.find("textarea[name='comment']").addClass("is-invalid").after('<div class="invalid-feedback">Comment is required.</div>');
            // }

            if (!status) {
                isValid = false;
                formElement.find("select[name='status']").addClass("is-invalid").after('<div class="invalid-feedback">Please select a status.</div>');
            }

            if (isValid) {
                let formData = formElement.serialize(); // Automatically includes _token
                // console.log("Form Data:", formData);

                let submitBtn = formElement.find("button[type='submit']");
                submitBtn.prop("disabled", true);

                $.ajax({
                    url: "/api/appointmentHistory/store",
                    type: "POST",
                    headers: {
                        "Authorization": "Bearer " + token,  // your auth token
                        "Accept": "application/json"
                    },
                    
                    data: formData,
                    success: function (response, textStatus, xhr) {
                        console.log("API Response:", response, xhr.status);

                        if (xhr.status === 200 || xhr.status === 201) {
                            formElement.find("input, textarea, select").val(""); // Reset fields

                            // $("#successMessage").text(response.message || "Follow-up created successfully").show();
                            formElement.find(".success-message")
                                .text(response.message || "Appointment saved successfully")
                                .show();

                            setTimeout(function () {
                                // $("#successMessage").hide();
                                // $(".modal").modal("hide");
                                formElement.find(".success-message").hide();
                                formElement.closest(".modal").modal("hide");
                                window.location.reload(); // Reload the page to reflect changes
                            }, 1500);
                        } else {
                            // $("#errorMessage").text(response.message || "An error occurred").show();
                            formElement.find(".error-message")
                                .text(response.message || "An error occurred")
                                .show();
                        }
                        console.log("succ Response:", xhr.responseJSON);
                    },
                    error: function (xhr) {
                        console.log("Error Response:", xhr.responseJSON);
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            // showValidationErrors(xhr.responseJSON.errors);
                            $.each(errors, function (field, messages) {
                                let inputField = $("[name='" + field + "']");
                                inputField.addClass("is-invalid");
                                inputField.after('<span class="invalid-feedback">' + messages[0] + "</span>");
                            });
                        } else {
                            let message = "An unexpected error occurred";

                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            } else if (xhr.status === 422) {
                                message = "Validation error occurred";
                            }

                            formElement.find(".error-message").text(message).show();
                        }
                    },
                    complete: function () {
                        submitBtn.prop("disabled", false); // Re-enable the button
                    }
                });
            }
        });

    });
</script>