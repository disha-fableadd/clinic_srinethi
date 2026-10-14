@php
use App\Models\Patients;
use App\Models\User;

$user = Auth::user();
$patients = Patients::where('id', $patient_id)->get();
$treatments = DB::table('treatments')
//->join('user', 'user.id', '=', 'treatments.doctor_id')
//->select('treatments.*', 'user.fullname as doctor_name')
->get();


//$user = auth()->user(); // Get logged-in user

$doctors = User::whereHas('role', function ($query) {
$query->where('name', 'Doctor');
})
->where('created_by', $user->id) // Filter by created_by
->select('id', 'fullname', 'profile', 'created_by')
->get();

if ($user->role->name === 'Receptionist') {
$doctors = User::whereHas('role', function ($query) {
$query->where('name', 'Doctor');
})->select('id', 'fullname', 'profile', 'created_by')->get();
}

//dd($patients);
//print_r($doctor_id, $appointment_id);
@endphp

<!-- Follow-up Modal -->
<div class="modal fade" id="followupModal_{{ $appointment_id }}" tabindex="-1" aria-labelledby="followupModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#cfece0;">
                <h5 class="modal-title" id="followupModalLabel">Follow-up Appointment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="" id="followupForm_{{ $appointment_id }}" method="POST">
                    <!-- form-container all-form follow-form -->
                    @csrf

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label><i class="fas fa-user-md icon-style"></i> Follow-up Type</label>
                                @php
                                $module_type = $module_type ?? ''; // Ensure $module_type is set

                                $followupType = match ($module_type) {
                                'appointment' => 'Appointment related',
                                'medicine' => 'Medicine related',
                                'discharge' => 'Discharge related',
                                'report' => 'Report related',
                                default => ''
                                };
                                @endphp
                                <select class="form-control select2" name="followup_type" id="followupType_{{ $appointment_id }}" {{ $followupType ? 'disabled' : '' }}>
                                    <option value="" disabled {{ !$followupType ? 'selected' : '' }}>Select Type</option>
                                    <option value="Regular followup" {{ $followupType === 'Regular followup' ? 'selected' : '' }}>Regular follow-up</option>
                                    <option value="Appointment related" {{ $followupType === 'Appointment related' ? 'selected' : '' }}>Appointment related</option>
                                    <option value="Medicine related" {{ $followupType === 'Medicine related' ? 'selected' : '' }}>Medicine related</option>
                                    <option value="Discharge related" {{ $followupType === 'Discharge related' ? 'selected' : '' }}>Discharge related</option>
                                    <option value="Report related" {{ $followupType === 'Report related' ? 'selected' : '' }}>Report related</option>
                                    <option value="other" {{ $followupType === 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                <input type="hidden" name="followup_type" value="{{ $followupType }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-user-injured icon-style"></i> Patient Name <span class="text-danger">*</span></label>

                                <select class="form-control select2" name="patient_id" id="followupPatientSelect_{{ $appointment_id }}">
                                    <option value="">Select Patient</option>
                                    @if (isset($patients) && $patients->count() > 0)
                                    @foreach ($patients as $patient)
                                    <option value="{{ $patient->id ?? '' }}" data-treatment-id="{{ $patient->treatment_id ?? '' }}" {{ $loop->iteration == 1 ? 'selected' : '' }}>{{ $patient->fullname ?? '--' }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    <!-- </div> -->

                    <!-- <div class="row"> -->
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-stethoscope icon-style"></i> Treatment <span class="text-danger">*</span></label>
                                <select class="form-control select2" name="treatment_id" id="followupTreatmentSelect_{{ $appointment_id }}">
                                    <option value="">Select Treatment</option>
                                    @if (isset($treatments) && $treatments->count() > 0)
                                    @foreach ($treatments as $treatment)
                                    <option value="{{ $treatment->id ?? '' }}" data-doctor-id="{{ $treatment->doctor_id ?? '' }}" {{ isset($treatment_id) && $treatment_id == $treatment->id ? 'selected' : '' }}>{{ $treatment->name ?? '--' }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-user-md icon-style"></i> Doctor <span class="text-danger">*</span></label>
                                <select class="form-control select2" name="doctor_id" id="followupDoctorSelect_{{ $appointment_id }}">
                                    <option value="">Select Doctor</option>
                                    @if (isset($doctors) && $doctors->count() > 0)
                                    @foreach ($doctors as $doctor)
                                    <option value="{{ $doctor->id ?? '' }}" {{ isset($doctor_id) && $doctor_id == $doctor->id ? 'selected' : '' }}>{{ $doctor->fullname ?? '--' }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    <!-- </div> -->

                    <!-- <div class="row"> -->
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-clock icon-style"></i> Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="date" r>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label><i class="fas fa-pencil-alt icon-style"></i> Follow-up Comments</label>
                                <textarea class="form-control" name="followup_update" rows="1" ></textarea>
                                <!-- style="border-radius:10px" -->
                            </div>
                        </div>
                    </div>

                    <div id="successMessage" class="alert alert-success success-message" style="display:none;"></div>
                    <div id="errorMessage" class="alert alert-danger error-message" style="display:none;"></div>

                    <button type="submit" class="btn btn-primary" style="padding:8px 50px;border-radius:50px; float:right">
                        Submit
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        const modal = $("#followupModal_{{ $appointment_id }}");
        const form = $("#followupForm_{{ $appointment_id }}");
        const patientSelect = modal.find("select[name='patient_id']");
        const treatmentSelect = modal.find("select[name='treatment_id']");
        const doctorSelect = modal.find("select[name='doctor_id']");
        const followupTypeSelect = modal.find("select[name='followup_type']");
        const appointmentDoctorId = "{{ $doctor_id ?? '' }}";

        // Initialize Select2 (if available)
        if ($.fn.select2) {
            followupTypeSelect.select2({
                placeholder: "Select Type",
                width: '100%',
                dropdownParent: modal
            });

            followupTypeSelect.on('select2:open', function() {
                $('.select2-search__field').attr('placeholder', 'Search Type');
            });

            patientSelect.select2({
                placeholder: "Select Patient",
                width: '100%',
                dropdownParent: modal
            });

            patientSelect.on('select2:open', function() {
                $('.select2-search__field').attr('placeholder', 'Search Patient');
            });

            treatmentSelect.select2({
                placeholder: "Select Treatment",
                width: '100%',
                dropdownParent: modal
            });

            treatmentSelect.on('select2:open', function() {
                $('.select2-search__field').attr('placeholder', 'Search Treatment');
            });

            doctorSelect.select2({
                placeholder: "Select Doctor",
                width: '100%',
                dropdownParent: modal
            });

            doctorSelect.on('select2:open', function() {
                $('.select2-search__field').attr('placeholder', 'Search Doctor');
            });
        }

        // Sync Doctor when treatment changes
        treatmentSelect.on("change", function () {
            let selectedDoctorId = $(this).find("option:selected").data("doctor-id");

            if (selectedDoctorId) {
                doctorSelect.val(selectedDoctorId).trigger('change');
            } else {
                doctorSelect.val("").trigger('change'); // Reset if no doctor found
            }
        });

        // When modal opens, make sure the doctor dropdown matches the current values
        modal.on('shown.bs.modal', function () {
            if (appointmentDoctorId) {
                doctorSelect.val(appointmentDoctorId).trigger('change');
            }

            // If doctor is not yet set, sync from the selected treatment
            if (!doctorSelect.val()) {
                treatmentSelect.trigger('change');
            }
        });


        $(document).on("click", ".followup-appointment", async function() {
            let appointmentId = $(this).data("id");
            var form = $("#followupForm_" + appointmentId);

            if (form.length) {
                form[0].reset();
            }
            const today = new Date().toISOString().split("T")[0];
            form.find("input[name='date']").attr("min", today);
            
            $("#followupModal_" + appointmentId).modal("show");
        });

        $(document).ready(function() {
            $(document).off("submit", "form[id^='followupForm_']").on("submit", "form[id^='followupForm_']", async function(e) {
                e.preventDefault(); // Prevent form submission

                let formElement = $(this); // Get the current form
                let isValid = true; // Assume the form is valid

                // Clear previous errors
                formElement.find(".invalid-feedback").remove();
                formElement.find(".form-control").removeClass("is-invalid");

                // Get form field values
                let patientId = formElement.find("select[name='patient_id']").val();
                let treatmentId = formElement.find("select[name='treatment_id']").val();
                let doctorId = formElement.find("select[name='doctor_id']").val();
                let date = formElement.find("input[name='date']").val();
                let csrfToken = formElement.find("input[name='_token']").val(); // Get CSRF token from form

                // Validation Checks
                if (!patientId) {
                    isValid = false;
                    formElement.find("select[name='patient_id']").addClass("is-invalid")
                        .after('<div class="invalid-feedback">Please select a patient</div>');
                }

                if (!treatmentId) {
                    isValid = false;
                    formElement.find("select[name='treatment_id']").addClass("is-invalid")
                        .after('<div class="invalid-feedback">Please select a treatment</div>');
                }

                if (!doctorId) {
                    isValid = false;
                    formElement.find("select[name='doctor_id']").addClass("is-invalid")
                        .after('<div class="invalid-feedback">Please select a doctor</div>');
                }

                if (!date) {
                    isValid = false;
                    formElement.find("input[name='date']").addClass("is-invalid")
                        .after('<div class="invalid-feedback">Please enter the date</div>');
                }

                if (isValid) {
                    let formData = formElement.serialize(); // Serialize form data

                    let submitBtn = formElement.find("button[type='submit']");
                    submitBtn.prop("disabled", true);

                    $.ajax({
                        url: "/api/followup/createFollowup",
                        type: "POST",
                        data: formData,
                        success: function(response, textStatus, xhr) {
                            if (xhr.status === 200 || xhr.status === 201) {
                                formElement[0].reset(); // Reset the form fields
                               // console.log(response);
                                // $("#successMessage").text(response?.message || "Follow-up created successfully").show();
                                formElement.find(".success-message")
                                .text(response?.message || "Follow-up created successfully")
                                .show();
                                setTimeout(function() {
                                    formElement.find(".success-message").hide();
                                    formElement.closest(".modal").modal("hide");
                                    // $("#successMessage").hide();
                                }, 1500);
                                // $("#followupModal").modal("hide");
                            } else {
                                // $("#errorMessage").text(response?.message || "An error occurred").show();
                                formElement.find(".error-message")
                                .text(response?.message || "An error occurred")
                                .show();
                            }
                        },
                        error: function(xhr) {
                            let message = "An unexpected error occurred";

                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                // showValidationErrors(xhr.responseJSON.errors); // Call reusable function
                                $.each(errors, function(field, messages) {
                                    let inputField = $("[name='" + field + "']");
                                    inputField.addClass("is-invalid");
                                    inputField.after('<span class="invalid-feedback">' + messages[0] + "</span>");
                                });
                            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                                $("#errorMessage").text(xhr.responseJSON.message).show();
                            } else if (xhr.status === 422) {
                                message = "Validation error occurred";
                                formElement.find(".error-message").text(message).show();
                                // $("#errorMessage").text("Validation error occurred").show();
                            } else {
                                // $("#errorMessage").text("An unexpected error occurred").show();
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
    });

  
</script>