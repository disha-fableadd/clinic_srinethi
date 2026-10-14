@extends('layout.app')

@php
    use App\Models\Setting;

    $currentProjectTypeId = (int) Setting::getValue('project_type_id', 1);
@endphp


<style>
    .form-control.is-invalid,
    .was-validated .form-control:invalid {
        border: 1px solid #cfece0 !important;
    }

    .icon-style {
        margin-right: 5px;
        color: #007bff;
    }

    #convert_text {
        height: 120px;
        resize: none;
    }

    #click_to_convert {
        display: flex;
        align-items: center;
        gap: 5px;
        padding: 8px 12px;
        border-radius: 10px;
        margin-bottom: 10px;
    }

    .custom-close {
        background-color: #f5b6a5 !important;
        opacity: 1;
        border: 1px solid #f5b6a5;
        border-radius: 5px;
        padding: 3px 6px;
    }

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
        height: auto !important;
        max-height: 200px;
        /* limit max height */
        overflow-y: auto;
    }

    /* Allow selected items to wrap to next line */
    /* .select2-container--default .select2-selection--multiple .select2-selection__rendered {
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 4px;
      
    } */

    /* Optional: add scrollbar if too many items */
    .select2-container--default .select2-selection--multiple {
        max-height: 200px;
        /* limit max height */
        overflow-y: auto;
        /* scroll if exceed max height */
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


    .image-preview-container {
        position: relative;
        display: inline-block;
    }

    .preview-img {
        height: 30px;
        width: auto;
        border-radius: 8px;
        margin-right: 5px;
        margin-left: 7px;
    }

    .remove-btn {
        position: absolute;
        top: -6px;
        right: -6px;
        background-color: red;
        color: white;
        border: none;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        font-size: 12px;
        cursor: pointer;
        line-height: 18px;
        text-align: center;
    }

    .patient-title {
        padding-left: 70px !important;
        text-align: center !important;
    }

    .patient-button {
        padding-right: 60px !important;
        text-align: center !important;
    }

    .custom-close {
        background-color: #f5b6a5 !important;
        opacity: 1;
        border: 1px solid #f5b6a5;
        border-radius: 5px;
        padding: 3px 6px;
    }

    .addtreat {
        margin-bottom: 8px;
    }


    @media screen and (max-width:768px) {
        .patient-title {
            padding-left: 15px !important;
            text-align: left !important;
        }

        .patient-button {
            padding-right: 15px !important;
            text-align: right !important;
        }

    }

    @media screen and (max-width:767px) {
        .patient-title {
            padding-left: 15px !important;
            text-align: left !important;
        }

        .patient-button {
            padding-right: 15px !important;
            text-align: right !important;
        }

        #multiStepForm {
            width: 100% !important;
            height: 65rem;
        }

        .patient-form {
            height: auto !important;
        }

        /* new */

        a.btn.btn-primary.btn-sm,
        a.btn.btn-primary.btn-rounded.addsymp {
            font-size: 10px;
            padding: 4px 7px;
        }



        .d-flex.align-items-center.justify-content-between.symplabel {
            font-size: 11px;
        }

        .form-group.symimg {
            font-size: 11px;
        }
    }

    /* referel button */
    @media (min-width: 360px) and (max-width: 740px) {

        a.btn.btn-primary.btn-sm,
        a.btn.btn-primary.btn-rounded.addsymp {
            display: flex;
            align-items: center;
        }

    }

    @media (min-width: 344px) and (max-width: 882px) {}
</style>
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="row" style="padding-top:15px">
                <div class=" col-6">
                    <h4 class="page-title  patient-title">Add Patient</h4>
                </div>
                @if (app('hasPermission')(5, 'view'))
                    <div class=" col-6 m-b-2 eye-btn patient-button">
                        <a href="{{ route('patients.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                            <i class="fa fa-arrow-left m-r-5 icon3  "></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    </div>
                @endif
            </div>
            <div class="row">
                <div class="col-12">

                    <form class="form-container  all-form" id="multiStepForm" method="POST" action=""
                        style="width:60% ;padding-bottom: 75px;">

                        @csrf
                        <input type="hidden" name="branch_id" id="branch_id">
                        <!-- Step 1: Basic Information -->
                        <div class="form-step patient-form " id="step-1">
                            <div class="row">

                                <div class="col-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-user icon-style"></i> Full Name <span
                                                class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="fullname"
                                            placeholder="Enter Full Name" required>
                                    </div>
                                </div>
                                <div class="col-6 ">
                                    <div class="form-group">
                                        <label><i class="fas fa-phone icon-style"></i> Phone <span
                                                class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="phone"
                                            placeholder="Enter Phone"maxlength="10" required>
                                    </div>
                                </div>

                                @if ($currentProjectTypeId !== 3)
                                    <div class="col-12 col-md-4">
                                        <div class="form-group">
                                            <label><i class="fas fa-envelope icon-style"></i> Email </label>
                                            <input class="form-control" type="email" name="email"
                                                placeholder="Enter Email">
                                        </div>
                                    </div>
                                @endif

                                <div class="{{ $currentProjectTypeId === 3 ? 'col-6 col-md-6' : 'col-6 col-md-4' }}">
                                    <div class="form-group">
                                        <label><i class="fas fa-cake-candles icon-style"></i> Birthdate</label>
                                        <input type="date" class="form-control" name="birthdate" id="birth_date">
                                    </div>
                                </div>

                                <div class="{{ $currentProjectTypeId === 3 ? 'col-6 col-md-6' : 'col-6 col-md-4' }}">
                                    <div class="form-group">
                                        <label><i class="fas fa-birthday-cake icon-style"></i> Age <span
                                                class="text-danger">*</span></label>
                                        <input class="form-control" type="number" name="age" id="age"
                                            placeholder="Enter Age">
                                    </div>
                                </div>





                                <div class="col-12 col-md-12">
                                    <div class="form-group">
                                        <label><i class="fas fa-user icon-style"></i> Patient Type <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control select2" name="patient_type" id="typedropdown" required>
                                            <option value="">Select Patient Type</option>
                                            <option value="All">All</option>
                                            <option value="OPD">OPD Patient</option>
                                            <option value="IPD">IPD Patient</option>
                                            <option value="Home">Home Patient</option>
                                            <option value="Cosmetic">Cosmetic Patient</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <label class="mr-2">
                                                <i class="fas fa-medkit icon-style"></i> Diagnosis <span
                                                    class="text-danger">*</span>
                                            </label>
                                            @if (app('hasPermission')(7, 'create'))
                                                <a href="{{ route('diagnosis.create') }}" target="_blank"
                                                    class="btn btn-primary btn-sm">
                                                    <i class="fas fa-plus"></i> <span class="hdr-btn-text">Add</span>
                                                </a>
                                            @endif
                                        </div>
                                        <select class="form-control select2" name="diagnosis_id[]" id="diagnosisDropdown"
                                            multiple style="min-height: 100px; max-height: 200px; overflow-y: auto;">
                                            <option value="" class="text-dark">Select Diagnosis</option>
                                        </select>
                                        <small class="text-muted">You can select multiple diagnoses</small>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <label><i class="fas fa-notes-medical icon-style"></i> Symptoms</label>
                                            <div class="m-b-2 eye-btn">
                                                <a href="#" class="btn btn-primary btn-rounded btn-hdr" data-bs-toggle="modal"
                                                    data-bs-target="#addSymptomModal" style="padding:4px 8px !important;">
                                                    <i class="fa fa-plus m-r-2 icon3"></i> <span class="hdr-btn-text">Add</span>
                                                </a>
                                            </div>
                                        </div>
                                        <select name="symptoms[]" class="form-control select2" id="symptomDropdown"
                                            multiple style="min-height: 100px; max-height: 200px; overflow-y: auto;">

                                        </select>
                                        <small class="text-muted">You can select multiple symptoms</small>
                                    </div>
                                </div>



                                <div class="col-12">
                                    <!-- Title -->
                                    <div class="mb-3">
                                        <i class="fas fa-notes-medical icon-style"></i>
                                        <span>Symptoms Input (Day-1)</span>
                                    </div>

                                    <div class="row">
                                        <!-- Left column: Radio buttons -->
                                        <div class="col-12 col-md-6">
                                            <div class="d-flex gap-3 margin right:22px">
                                                <div>
                                                    <input type="radio" name="symptom_option" value="image"
                                                        onchange="toggleSymptomInput()"
                                                        {{ old('symptom_option', !empty($symptom_images) ? 'image' : '') == 'image' ? 'checked' : '' }}>
                                                    <span>Upload Image</span>
                                                </div>
                                                <div>
                                                    <input type="radio" name="symptom_option" value="text"
                                                        style="margin-left:10px" onchange="toggleSymptomInput()"
                                                        {{ old('symptom_option', !empty($symptom_remarks) ? 'text' : 'text') == 'text' ? 'checked' : '' }}>
                                                    <span>Remarks</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Right column: Input field -->
                                        <div class="col-12 col-md-6">
                                            <!-- Image upload -->
                                            <div id="symptomImageBox"
                                                style="{{ old('symptom_option', !empty($symptom_images) ? 'image' : '') == 'image' ? '' : 'display:none;' }}">
                                                <input type="file" class="form-control" name="symptom_images[]"
                                                    multiple accept="image/*" onchange="previewSymptomImages(this)">
                                                <small class="text-muted">You can select multiple images</small>

                                                <div id="symptomImageBox"
                                                    style="{{ old('symptom_option', !empty($symptom_images) ? 'image' : '') == 'image' ? '' : 'display:none;' }}">
                                                    <input type="file" class="form-control" name="symptom_images[]"
                                                        id="symptomImagesInput" multiple accept="image/*"
                                                        onchange="previewSymptomImages(this)">
                                                    <small class="text-muted">You can select multiple images</small>
                                                </div>
                                                <div id="existing_symptom_images" class="d-flex flex-wrap gap-2"></div>
                                                <!-- New uploaded images -->
                                                <div id="symptomImagePreview" class="d-flex flex-wrap gap-2"></div>

                                            </div>

                                            <!-- Textarea for remarks -->
                                            <div id="symptomTextBox"
                                                style="{{ old('symptom_option', !empty($symptom_remarks) ? 'text' : 'text') == 'text' ? '' : 'display:none;' }}">
                                                <textarea class="form-control" name="symptom_remarks" rows="1" placeholder="Enter remarks...">{{ old('symptom_remarks', $symptom_remarks ?? '') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <script>
                                    function toggleSymptomInput() {
                                        const option = document.querySelector('input[name="symptom_option"]:checked').value;
                                        document.getElementById('symptomImageBox').style.display = (option === 'image') ? 'block' : 'none';
                                        document.getElementById('symptomTextBox').style.display = (option === 'text') ? 'block' : 'none';
                                    } // Preview selected images function previewSymptomImages(input) { const previewContainer = document.getElementById('symptomImagePreview'); previewContainer.innerHTML = ''; // Clear previous previews const files = input.files; if (files) { Array.from(files).forEach(file => { if (file.type.startsWith('image/')) { const reader = new FileReader(); reader.onload = function(e) { const img = document.createElement('img'); img.src = e.target.result; img.style.width = '100px'; img.style.height = '100px'; img.style.objectFit = 'cover'; img.style.border = '1px solid #ccc'; img.style.borderRadius = '5px'; previewContainer.appendChild(img); } reader.readAsDataURL(file); } }); } }
                                </script>





                                <!-- Select + Button -->
                                <div class="col-12 col-md-12">
                                    <div class="form-group">
                                        <label><i class="fas fa-share-alt icon-style"></i> Referral Source</label>
                                        <div class="d-flex align-items-center">
                                            <select class="form-control select2 me-2" id="select-blood"
                                                name="referral_source" style="flex: 1;">
                                                <option value=""></option>
                                                <option value="Doctors">Doctors</option>
                                                <option value="Website">Website</option>
                                                <option value="Advertisement">Advertisement</option>
                                                <option value="Patients">Patients</option>
                                                <option value="Insurance">Insurance</option>
                                                <option value="Others">Others</option>
                                            </select>
                                            <a href="#"
                                                class="btn btn-primary btn-sm btn-rounded d-flex align-items-center justify-content-center"
                                                id="openReferralModal" data-bs-toggle="modal"
                                                data-bs-target="#addReferralModal"
                                                style="padding:8px 12px !important; white-space: nowrap; margin-left: 15px;">
                                                <i class="fa fa-plus m-0"></i>
                                                <span style="margin-left: 6px;" class="d-none d-md-inline">Add Referral Details</span>
                                            </a>

                                        </div>
                                    </div>
                                </div>



                                <div id="referralSummary" class="mt-2 text-muted"
                                    style="border: 1px solid #cfece0; padding: 10px 25px; margin-left: 20px; color: black !important; display: none;">
                                </div>

                                <div id="sourceerrorMessage" class="text-danger"
                                    style="margin-left: 20px;width: 100% !important;font-size: 12px;"></div>


                                <!-- Modal -->
                                <div class="modal fade" id="addReferralModal" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header" style="background-color: #CFECE0; color:black">
                                                <h5 class="modal-title">Add Referral Details</h5>
                                                <button type="button" class="btn-close custom-close"
                                                    data-bs-dismiss="modal" aria-label="Close"><i
                                                        class="fas fa-times"></i></button>
                                            </div>
                                            <div class="modal-body" id="referralFields">
                                                <!-- Dynamic Fields Will Appear Here -->
                                            </div>
                                            <div id="referralError" class="text-danger mb-3"
                                                style="display:none;margin-left:20px !important;"></div>

                                            <div class="modal-footer">
                                                <button type="button" id="saveReferralDetails"
                                                    class="btn btn-primary">Save
                                                    Details</button>
                                                <button type="button" class="btn btn-danger"
                                                    style="border-radius:50px !important"
                                                    data-bs-dismiss="modal">Cancel</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>






                            <button type="button" class="btn btn-primary next-btn"
                                style="margin-left:5px;padding:8px 50px;float:right;">
                                Next
                            </button>

                        </div>


                        <!-- Step 2: Additional Information -->
                        <div class="form-step patient-form2" id="step-2" style="display:none;">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label><i class="fas fa-image icon-style"></i> Profile Image</label>
                                        <input type="file" class="form-control" name="profile">
                                    </div>
                                </div>
                                <div class="col-12">

                                    <div class="form-group">
                                        <label><i class="fas fa-map-marker-alt icon-style"></i> Address</label>
                                        <input type="text" class="form-control" name="address"
                                            placeholder="Enter Address">
                                    </div>
                                </div>
                                <div class="col-6 col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-city icon-style"></i> City</label>
                                        <select class="form-control select2" id="select-city" name="city">
                                            <option value="">Select City</option>
                                            <option value="Surat">Surat</option>
                                            <option value="Ahmedabad">Ahmedabad</option>
                                            <option value="Mumbai">Mumbai</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-flag icon-style"></i> State</label>
                                        <select class="form-control select2" id="select-state" name="state">
                                            <option value="">Select State</option>
                                            <option value="Gujarat">Gujarat</option>
                                            <option value="Maharashtra">Maharashtra</option>
                                        </select>
                                    </div>
                                </div>




                                <!-- Note Type Selector -->
                                <div class="col-12">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-6">
                                                <label><i class="fas fa-sticky-note icon-style"></i> Note Type</label>
                                                <select class="form-control global-note-type select2" id="select-note"
                                                    name="note_type" style="margin-bottom: 20px;">
                                                    <option value=""></option>
                                                    <option value="speech">Speech-to-Text Note</option>
                                                    <option value="audio">Audio Note</option>
                                                </select>
                                            </div>
                                            <div class="col-6">
                                                <label><i class="fas fa-clock icon-style"></i> Duration</label>
                                                <select class="form-control duration-select select2" id="select-time"
                                                    name="note_interval" style="margin-bottom: 20px;">
                                                    <option value=""></option>
                                                    <option value="5000">5 seconds (test)</option>
                                                    <option value="10000">10 seconds (test)</option>
                                                    <option value="300000">5 minutes</option>
                                                    <option value="600000">10 minutes</option>
                                                    <option value="900000">15 minutes</option>
                                                </select>
                                            </div>
                                        </div>

                                    </div>



                                </div>

                                <div class="col-12" id="notes-container">
                                    <div class="note-section">
                                        <!-- Speech-to-Text Section (shown by default) -->
                                        <div class="speech-section">
                                            <div class="form-group">
                                                <div class="d-flex justify-content-end gap-2 mt-2">
                                                    <button class="btn btn-danger rounded-pill removeButton">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    <button class="btn btn-primary mx-2 startButton">
                                                        <i class="fas fa-microphone"></i>
                                                    </button>
                                                    <button class="btn btn-primary speakButton">
                                                        <i class="fas fa-volume-up"></i>
                                                    </button>
                                                </div>
                                                <textarea class="form-control note-textarea mt-2" name="speech_note[]" placeholder="Speak or type here"
                                                    style="border-radius:10px"></textarea>
                                            </div>
                                        </div>

                                        <!-- Audio Note Section (hidden by default) -->
                                        <div class="audio-section" style="display:none;">
                                            <div class="form-group">
                                                <div class="d-flex justify-content-end gap-2 mt-2">
                                                    <button class="btn btn-danger rounded-pill mr-2 removeButton">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    <button class="btn btn-primary recordButton">
                                                        <i class="fas fa-microphone"></i> Record
                                                    </button>
                                                    <button class="btn btn-success rounded-pill stopButton"
                                                        style="display:none;">
                                                        <i class="fas fa-stop"></i> Stop
                                                    </button>


                                                </div>
                                                <audio class="audio-player" controls
                                                    style="width:100%;margin-top:10px;display:none;"></audio>
                                                <input type="hidden" class="audio-data" name="audio_note[]">
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-12 d-flex justify-content-end mb-3">
                                    <button id="addNoteButton" class="btn btn-primary mt-2">
                                        <i class="fas fa-plus"></i> Add Note
                                    </button>
                                </div>



                            </div>

                            <div id="patientsuccessMessage" class="alert alert-success" style="display:none;"></div>
                            <button type="button" class="btn btn-danger prev-btn mt-3 mb-3"
                                style="padding:8px 50px;border-radius:50px; float:left">
                                Previous
                            </button>

                            <button type="submit" class="btn btn-primary mt-3 mb-3"
                                style="padding:8px 50px;border-radius:50px; float:right">
                                Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="addSymptomModal" tabindex="-1" aria-labelledby="addSymptomModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form id="symptomForm">
                @csrf
                <div class="modal-content">
                    <!-- hidden input for branch -->
                    <input type="hidden" name="branch_id" id="branch_id">

                    <div class="modal-header" style="background-color: #CFECE0; color:black">
                        <h5 class="modal-title" id="addSymptomModalLabel">Add Symptom</h5>
                        <button type="button" class="btn-close custom-close" data-bs-dismiss="modal"
                            aria-label="Close"><i class="fas fa-times"></i></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="symptomName" class="form-label">Symptom Name</label>
                            <input type="text" class="form-control" id="symptomName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="symptomDetails" class="form-label">Details</label>
                            <textarea class="form-control" id="symptomDetails" name="details" rows="3" style="border-radius:10px"></textarea>
                        </div>

                        <div id="globalSuccess" class="alert alert-success mt-4" style="display:none;"></div>
                        <div id="globalError" class="alert alert-danger mt-4" style="display:none;"></div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save Symptom</button>
                    </div>
                </div>
            </form>
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




    {{-- age --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const birthInput = document.getElementById("birth_date");
            const ageInput = document.getElementById("age");

            // ✅ Restrict max date to yesterday
            const today = new Date();
            const yesterday = new Date(today);
            yesterday.setDate(today.getDate() - 1);
            const maxDate = yesterday.toISOString().split("T")[0];
            birthInput.setAttribute("max", maxDate);

            // ✅ Calculate age automatically
            birthInput.addEventListener("change", function() {
                if (this.value) {
                    const birthDate = new Date(this.value);
                    const today = new Date();

                    let age = today.getFullYear() - birthDate.getFullYear();
                    const monthDiff = today.getMonth() - birthDate.getMonth();
                    const dayDiff = today.getDate() - birthDate.getDate();

                    // If birthday hasn't occurred yet this year, subtract 1
                    if (monthDiff < 0 || (monthDiff === 0 && dayDiff < 0)) {
                        age--;
                    }

                    ageInput.value = age >= 0 ? age : "";
                } else {
                    ageInput.value = "";
                }
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ✅ Set branch_id into hidden input from localStorage
            let storedBranchId = localStorage.getItem('selectedBranchId');
            if (storedBranchId) {
                document.getElementById('branch_id').value = storedBranchId;
            }

            // ✅ Init Select2 for Patient type
            $('#typedropdown').select2({
                placeholder: "Select Patient type",
                width: '100%'
            }).on('select2:open', function() {
                $('.select2-search__field').attr('placeholder', 'Search Patient type');
            });

            // ✅ Init Select2 for Symptoms
            $('#symptomDropdown').select2({
                placeholder: "Select Symptoms",
                width: '100%'
            }).on('select2:open', function() {
                $('.select2-search__field').attr('placeholder', 'Search Symptoms');
            });

            // ✅ Load Symptoms Function (branch wise)
            function loadSymptoms() {
                let branchId = localStorage.getItem('selectedBranchId');

                $.ajax({
                    url: "{{ route('symptoms.list') }}",
                    type: "GET",
                    data: {
                        branch_id: branchId
                    },
                    success: function(data) {
                        // console.log("API Response:", data);

                        let symptomsList = Array.isArray(data) ? data : (data.symptoms || []);

                        let symptomDropdown = $('#symptomDropdown');
                        symptomDropdown.empty().append('<option value="">Select Symptom</option>');

                        $.each(symptomsList, function(index, symptom) {
                            const name = symptom.name.charAt(0).toUpperCase() + symptom.name
                                .slice(1);
                            symptomDropdown.append(
                                `<option value="${symptom.id}">${name}</option>`);
                        });

                        symptomDropdown.trigger('change');
                    },
                    error: function(xhr) {
                        console.error("Failed to load symptoms:", xhr.status, xhr.responseText);
                    }
                });
            }

            // ✅ Load symptoms initially
            loadSymptoms();



            $('#symptomForm').on('submit', function(e) {
                e.preventDefault();

                let branchId = localStorage.getItem('selectedBranchId');
                $('#globalSuccess,#globalError').hide();

                $.ajax({
                    url: "{{ route('symptoms.store') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}", // ✅ include CSRF token
                        name: $('#symptomName').val(),
                        details: $('#symptomDetails').val(),
                        branch_id: branchId
                    },
                    success: function(response) {
                        $('#globalSuccess').text(response.message).fadeIn();

                        setTimeout(() => {
                            $('#addSymptomModal').modal('hide');
                            $('#symptomForm')[0].reset();
                            $('#globalSuccess').fadeOut();
                            loadSymptoms(); // reload dropdown list
                        }, 1500);
                    },
                    error: function(xhr) {
                        let errorText = xhr.responseJSON?.message || 'Something went wrong!';
                        $('#globalError').text(errorText).fadeIn();
                        setTimeout(() => $('#globalError').fadeOut(), 3000);
                    }
                });
            });

        });
    </script>






    <!-- img -->

    <script>
        const imageInput = document.getElementById('symptomImagesInput');
        const previewContainer = document.getElementById('symptomImagePreview');
        let selectedFiles = [];

        imageInput.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);

            // Store files locally
            selectedFiles = [...files];

            previewContainer.innerHTML = ''; // Clear previous previews

            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.createElement('div');
                    preview.className = 'image-preview-container';

                    preview.innerHTML = `
                                                                                    <img src="${e.target.result}" class="preview-img" alt="Symptom Image">
                                                                                    <button class="remove-btn" data-index="${index}">&times;</button>
                                                                                `;

                    previewContainer.appendChild(preview);
                };
                reader.readAsDataURL(file);
            });
        });

        // Remove image preview
        previewContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-btn')) {
                const indexToRemove = parseInt(e.target.getAttribute('data-index'));
                selectedFiles.splice(indexToRemove, 1);

                // Create a new FileList
                const dataTransfer = new DataTransfer();
                selectedFiles.forEach(file => dataTransfer.items.add(file));
                imageInput.files = dataTransfer.files;

                // Trigger change to refresh previews
                imageInput.dispatchEvent(new Event('change'));
            }
        });
    </script>


    <!-- button note speech -->

    <script>
        $(document).ready(function() {
            let speechTimer = null;
            let ttsTimer = null;
            let endTimeInterval = 1000; // default end time interval

            // Global note type change handler
            $('.global-note-type').change(function() {
                $('#notes-container').html('');
                $('#addNoteButton').click();
            });

            $('#addNoteButton').click(function(e) {
                e.preventDefault();
                const currentType = $('.global-note-type').val();
                let html = '';

                if (currentType === 'speech') {
                    html = `
                                                                    <div class="note-section">
                                                                        <div class="form-group speech-section">
                                                                            <div class="d-flex justify-content-end gap-2 mt-2">
                                                                                <button class="btn btn-danger rounded-pill removeButton">
                                                                                    <i class="fas fa-trash"></i>
                                                                                </button>
                                                                                <button class="btn btn-primary mx-2 startButton">
                                                                                    <i class="fas fa-microphone"></i>
                                                                                </button>
                                                                                <button class="btn btn-primary speakButton">
                                                                                    <i class="fas fa-volume-up"></i>
                                                                                </button>
                                                                            </div>
                                                                            <textarea class="form-control mt-2 note-textarea" name="speech_note[]" 
                                                                                    placeholder="Speak or type here" style="border-radius:10px"></textarea>
                                                                        </div>
                                                                    </div>
                                                                `;
                } else {
                    html = `
                                                                        <div class="note-section">
                                                                            <div class="form-group audio-section">
                                                                                <div class="d-flex justify-content-end gap-2 mt-2">
                                                                                    <button class="btn btn-danger rounded-pill removeButton mr-2">
                                                                                        <i class="fas fa-trash"></i>
                                                                                    </button>
                                                                                    <button class="btn btn-primary recordButton">
                                                                                        <i class="fas fa-microphone"></i> Record
                                                                                    </button>
                                                                                    <button class="btn btn-success rounded-pill stopButton" style="display:none;">
                                                                                        <i class="fas fa-stop"></i> Stop
                                                                                    </button>                                
                                                                                </div>
                                                                                <audio class="audio-player" controls style="width:100%;margin-top:10px;display:none;"></audio>
                                                                                <input type="hidden" class="audio-data" name="audio_note[]">
                                                                            </div>
                                                                        </div>
                                                                    `;
                }



                const $newNote = $(html);
                $('#notes-container').append($newNote);
                initializeNoteSection($newNote);
            });

            initializeNoteSection($('.note-section:first'));

            function initializeNoteSection(section) {
                const startButton = section.find('.startButton');
                const speakButton = section.find('.speakButton');
                const recordButton = section.find('.recordButton');
                const stopButton = section.find('.stopButton');
                const playButton = section.find('.playButton');
                const outputTextarea = section.find('.note-textarea');
                const audioPlayer = section.find('.audio-player');
                const audioData = section.find('.audio-data');
                const removeButton = section.find('.removeButton');

                removeButton.off('click').click(function(e) {
                    e.preventDefault();
                    if ($('.note-section').length > 1) {
                        $(this).closest('.note-section').remove();
                    } else {
                        alert("You need at least one note section.");
                    }
                });

                const recognition = new(window.SpeechRecognition || window.webkitSpeechRecognition)();

                if (recognition) {
                    recognition.lang = "";
                    recognition.continuous = false;
                    recognition.interimResults = true;

                    recognition.onstart = () => {
                        startButton.html('<i class="fas fa-microphone fa-beat"></i>');
                        outputTextarea.val("");
                    };

                    recognition.onresult = (event) => {
                        let interimTranscript = '';
                        let finalTranscript = '';

                        for (let i = event.resultIndex; i < event.results.length; i++) {
                            const transcript = event.results[i][0].transcript;
                            if (event.results[i].isFinal) {
                                finalTranscript += transcript;
                            } else {
                                interimTranscript += transcript;
                            }
                        }

                        outputTextarea.val(finalTranscript + interimTranscript);
                    };

                    recognition.onend = () => {
                        startButton.html('<i class="fas fa-microphone"></i>');
                        clearTimeout(speechTimer); // Clear any leftover timer
                    };

                    recognition.onerror = (event) => {
                        alert(`Speech Recognition Error: ${event.error}`);
                        clearTimeout(speechTimer);
                    };

                    startButton.off('click').click((e) => {
                        e.preventDefault();
                        try {
                            recognition.start();

                            const duration = parseInt($('.duration-select').val()) + endTimeInterval;
                            speechTimer = setTimeout(() => {
                                recognition.stop();
                            }, duration);

                        } catch (error) {
                            alert(`Error starting speech recognition: ${error.message}`);
                        }
                    });
                }

                speakButton.off('click').click((e) => {
                    e.preventDefault();
                    const text = outputTextarea.val().trim();
                    if (text === "") {
                        alert("Please enter text to speak.");
                        return;
                    }

                    const speech = new SpeechSynthesisUtterance(text);
                    const voices = window.speechSynthesis.getVoices();

                    const detectedLang = detectLanguage(text);
                    speech.lang = detectedLang;
                    const voice = voices.find(v => v.lang.startsWith(detectedLang));
                    if (voice) speech.voice = voice;

                    window.speechSynthesis.cancel(); // Stop any ongoing speech
                    window.speechSynthesis.speak(speech);

                    const duration = parseInt($('.duration-select').val()) + endTimeInterval;
                    clearTimeout(ttsTimer);
                    ttsTimer = setTimeout(() => {
                        window.speechSynthesis.cancel();
                    }, duration);
                });

                let mediaRecorder;
                let audioChunks = [];

                recordButton.off('click').click(async (e) => {
                    e.preventDefault();
                    try {
                        const stream = await navigator.mediaDevices.getUserMedia({
                            audio: true
                        });
                        mediaRecorder = new MediaRecorder(stream);

                        mediaRecorder.ondataavailable = (e) => {
                            audioChunks.push(e.data);
                        };

                        mediaRecorder.onstop = () => {
                            const audioBlob = new Blob(audioChunks, {
                                type: 'audio/wav'
                            });
                            const audioUrl = URL.createObjectURL(audioBlob);
                            audioPlayer.attr('src', audioUrl).show();

                            const reader = new FileReader();
                            reader.readAsDataURL(audioBlob);
                            reader.onloadend = () => {
                                audioData.val(reader.result);
                            };

                            playButton.show();
                            recordButton.show();
                            stopButton.hide();
                        };

                        audioChunks = [];
                        mediaRecorder.start();
                        recordButton.hide();
                        stopButton.show();

                        const duration = parseInt($('.duration-select').val()) + endTimeInterval;
                        setTimeout(() => {
                            if (mediaRecorder && mediaRecorder.state === "recording") {
                                mediaRecorder.stop();
                                stream.getTracks().forEach(track => track.stop());
                            }
                        }, duration);

                    } catch (err) {
                        alert(`Error accessing microphone: ${err}`);
                    }
                });

                stopButton.off('click').click((e) => {
                    e.preventDefault();
                    if (mediaRecorder) {
                        mediaRecorder.stop();
                        mediaRecorder.stream.getTracks().forEach(track => track.stop());
                    }
                });

                playButton.off('click').click((e) => {
                    e.preventDefault();
                    audioPlayer[0].play();
                });
            }

            function detectLanguage(text) {
                if (/[\u0900-\u097F]/.test(text)) return "hi-IN";
                if (/[\u0A80-\u0AFF]/.test(text)) return "gu-IN";
                return "en-US";
            }
        });
    </script>
    <script>
        document.getElementById('multiStepForm').addEventListener('submit', function(e) {
            let branchId = localStorage.getItem('selectedBranchId');
            if (branchId) {
                document.getElementById('branch_id').value = branchId;
            } else {
                // optional: alert if no branch is selected
                alert('Please select a branch before submitting.');
                e.preventDefault();
            }
        });

        $('#select-city').select2({
            placeholder: "Select City",
            width: '100%'
        });
        $('#select-city').on('select2:open', function() {
            $('.select2-search__field').attr('placeholder', 'Search City');
        });

        $('#select-state').select2({
            placeholder: "Select State",
            width: '100%'
        });
        $('#select-state').on('select2:open', function() {
            $('.select2-search__field').attr('placeholder', 'Search State');
        });

        $('#select-note').select2({
            placeholder: "Select Note type",
            width: '100%'
        });
        $('#select-note').on('select2:open', function() {
            $('.select2-search__field').attr('placeholder', 'Search Note type');
        });

        $('#select-time').select2({
            placeholder: "Select Duration",
            width: '100%'
        });
        $('#select-time').on('select2:open', function() {
            $('.select2-search__field').attr('placeholder', 'Search Duration');
        });


        $('#select-blood').select2({
            placeholder: "Select Referral Source",
            width: '100%'
        });
        $('#select-blood').on('select2:open', function() {
            $('.select2-search__field').attr('placeholder', 'Search Referral Source');
        });



        function formatPatientId(fullId) {
            if (!fullId) return 'N/A';
            // Extract the part after "PAT-"
            const parts = fullId.split('-');
            if (parts.length < 2) return fullId;
            // Take the first 4 characters after "PAT-"
            const shortId = parts[1].substring(0, 4);
            return `Pt${shortId}`;
        }

        // <!-- DETAILS -->

        $(document).ready(function() {

            // Open modal & load correct fields
            $("#openReferralModal").on("click", function(e) {
                e.preventDefault();

                let selected = $("#select-blood").val();
                let fieldsHtml = "";

                switch (selected) {


                    case "Doctors":
                        fieldsHtml = `
                    <div class="mb-3">
                        <label>Referral Doctor (Select from list)</label>
                        <select class="form-control" id="referralDoctorSelect" name="referral_name">
                            <option value="">Select Doctor</option>
                        </select>
                    </div>

                    <div class="text-center my-2">
                        <strong>OR</strong>
                    </div>

                    <div class="mb-3">
                        <label>Enter Doctor Name (if not listed)</label>
                        <input type="text" class="form-control" id="referralDoctorText" name="referral_name_text" placeholder="Type doctor name">
                    </div>
                `;
                        $('#referralContainer').html(fieldsHtml);
                        loadReferralDoctors();
                        break;


                    case "Website":
                        fieldsHtml = `
                                                        <div class="mb-3">
                                                            <label>Website Name</label>
                                                            <input type="text" class="form-control" name="website_name">
                                                        </div>`;
                        break;

                    case "Advertisement":
                        fieldsHtml = `
                                                        <div class="mb-3">
                                                            <label>Advertisement Name</label>
                                                            <input type="text" class="form-control" name="advertisement_name">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label>Description</label>
                                                            <textarea class="form-control" name="description"></textarea>
                                                        </div>`;
                        break;

                    case "Patients":
                        fieldsHtml = `
                                                        <div class="mb-3">
                                                            <label>Patient Name</label>
                                                            <input type="text" class="form-control" name="patient_name">
                                                        </div>`;
                        break;

                    case "Insurance":
                        fieldsHtml = `
                                                        <div class="mb-3">
                                                            <label>Referral Name</label>
                                                            <input type="text" class="form-control" name="referral_name">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label>Mobile</label>
                                                            <input type="number" class="form-control" name="mobile">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label>Email</label>
                                                            <input type="email" class="form-control" name="email">
                                                        </div>`;
                        break;

                    case "Others":
                        fieldsHtml = `
                                                        <div class="mb-3">
                                                            <label>Other's Name</label>
                                                            <input type="text" class="form-control" name="other_name">
                                                        </div>`;
                        break;

                    default:
                        fieldsHtml = ``;
                }

                $("#referralFields").html(fieldsHtml);

                if (selected) {
                    $("#addReferralModal").modal("show");
                }
            });


            function loadReferralDoctors() {
                const token = localStorage.getItem('token');
                const branchId = localStorage.getItem('selectedBranchId');

                $.ajax({
                    url: '/api/referal_doctors',
                    type: 'GET',
                    headers: {
                        "Authorization": "Bearer " + token
                    },
                    data: {
                        branch_id: branchId
                    },
                    success: function(res) {
                        const select = $('#referralDoctorSelect');
                        select.empty();
                        select.append('<option value="">Select Doctor</option>');

                        res.data.forEach(function(doctor) {
                            // Use doctor.id as value
                            select.append(
                                `<option value="${doctor.id}">${doctor.doctor_name}</option>`
                            );
                        });

                        // Initialize Select2
                        select.select2({
                            width: '100%',
                            placeholder: 'Select Doctor'
                        });
                    },
                    error: function(err) {
                        console.error('Failed to load referral doctors', err);
                    }
                });
            }


            $("#referralDoctorSelect").on("change", function() {
                let doctorId = $(this).val();
                if (doctorId) {
                    $.ajax({
                        url: "/api/referal_doctors/" + doctorId,
                        type: "GET",
                        success: function(doctor) {
                            let detailsHtml = `
                        <div class="mt-2">
                            <strong>Doctor Details:</strong>
                            <ul>
                                <li>Specialization: ${doctor.specialist || '-'}</li>
                                <li>Phone: ${doctor.phone_number || '-'}</li>
                                <li>Email: ${doctor.email || '-'}</li>
                            </ul>
                        </div>
                    `;
                            $("#referralDoctorSelect").after(detailsHtml);
                        },
                        error: function(err) {
                            console.error("Failed to load doctor details", err);
                        }
                    });
                }
            });

            // Save details from modal into hidden form inputs
            $("#saveReferralDetails").on("click", function() {
                let selectedSource = $("#select-blood").val();
                let sourceDetails = {};
                let $errorBox = $("#referralError");
                $errorBox.hide().html("");
                let allValid = true;

                if (selectedSource === "Doctors") {
                    let doctorId = $("#referralDoctorSelect").val();
                    let doctorText = $("#referralDoctorText").val().trim();
                    const token = localStorage.getItem('token');
                    const branchId = localStorage.getItem('selectedBranchId');

                    if (!doctorId && !doctorText) {
                        $("#sourceerrorMessage").text("⚠ Please select a doctor or enter a name").show();
                        return;
                    }

                    // remove old hidden
                    $(".hidden-referral-input").remove();

                    if (doctorId) {
                        // existing doctor
                        $("#multiStepForm").append(
                            `<input type="hidden" class="hidden-referral-input" name="referral_source" value="${selectedSource}">`
                        );
                        $("#multiStepForm").append(
                            `<input type="hidden" class="hidden-referral-input" name="source_details[doctor_id]" value="${doctorId}">`
                        );

                        $.ajax({
                            url: "/api/referal_doctors/" + doctorId,
                            type: "GET",
                            success: function(doctor) {
                                console.log("Doctor details:", doctor);
                                let summaryHtml =
                                    `<strong>Doctor: ${doctor.doctor_name}</strong><ul>`;
                                summaryHtml +=
                                    `<li><strong>Specialization:</strong> ${doctor.specialization ? doctor.specialization: '-'}</li>`;
                                summaryHtml +=
                                    `<li><strong>Phone:</strong> ${doctor.phone ? doctor.phone: '-'}</li>`;
                                summaryHtml +=
                                    `<li><strong>Email:</strong> ${doctor.email ? doctor.email : '-'}</li>`;
                                summaryHtml += `</ul>`;
                                $("#referralSummary").html(summaryHtml).show();
                                $("#sourceerrorMessage").hide();
                                $("#addReferralModal").modal("hide");
                            }
                        });
                    } else {
                        // new doctor -> create via API
                        $.ajax({
                            url: "/api/referal_doctors",
                            type: "POST",
                            headers: {
                                "Authorization": "Bearer " + token
                            },
                            data: {
                                doctor_name: doctorText,
                                branch_id: branchId
                            },
                            success: function(res) {
                                let doctor = res.data;
                                $(".hidden-referral-input").remove();
                                $("#multiStepForm").append(
                                    `<input type="hidden" class="hidden-referral-input" name="referral_source" value="${selectedSource}">`
                                );
                                $("#multiStepForm").append(
                                    `<input type="hidden" class="hidden-referral-input" name="source_details[doctor_id]" value="${doctor.id}">`
                                );

                                let summaryHtml =
                                    `<strong>Doctor: ${doctor.doctor_name}</strong>`;
                                $("#referralSummary").html(summaryHtml).show();
                                $("#sourceerrorMessage").hide();
                                $("#addReferralModal").modal("hide");
                            },
                            error: function() {
                                $("#sourceerrorMessage").text("⚠ Failed to save doctor").show();
                            }
                        });
                    }

                    return;
                }

                // Non-doctor sources (your existing validation)
                $("#referralFields").find("input, textarea, select").each(function() {
                    let name = $(this).attr("name");
                    let value = $(this).val().trim();

                    if (!value) {
                        $(this).addClass("is-invalid");
                        allValid = false;
                    } else {
                        $(this).removeClass("is-invalid");
                    }
                    sourceDetails[name] = value;
                });

                if (!allValid) {
                    $errorBox.html("⚠ Please fill in all referral details.").fadeIn();
                    return;
                }

                $(".hidden-referral-input").remove();

                $("#multiStepForm").append(
                    `<input type="hidden" class="hidden-referral-input" name="referral_source" value="${selectedSource}">`
                );

                $.each(sourceDetails, function(key, value) {
                    $("#multiStepForm").append(
                        `<input type="hidden" class="hidden-referral-input" name="source_details[${key}]" value="${value}">`
                    );
                });

                let summaryHtml = `<strong>${selectedSource}</strong><ul>`;
                $.each(sourceDetails, function(key, value) {
                    summaryHtml += `<li>${key.replace(/_/g, ' ')}: ${value || '-'}</li>`;
                });
                summaryHtml += `</ul>`;
                $("#referralSummary").html(summaryHtml).show();
                $("#sourceerrorMessage").hide();
                $("#addReferralModal").modal("hide");
            });

        });





        $(document).ready(function() {




            var form = $("#multiStepForm");
            form.validate({
                // Validation rules for the entire form
                rules: {

                    fullname: "required",

                    phone: {
                        required: true,
                        number: true,
                        minlength: 10,
                        maxlength: 15
                    },
                    age: {
                        required: true,
                        number: true,
                        min: 0
                    },
                    patient_type: {
                        required: true,
                    },

                },
                messages: {

                    fullname: "Please enter your full name",
                    phone: "Please enter a valid phone number",
                    age: {
                        required: "Please enter your age",
                        number: "Age must be a number",
                        min: "Age cannot be negative"
                    },
                    patient_type: {
                        required: "Please select patient type",
                    },

                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });
            // Function to validate the current step
            function validateStep(step) {
                var valid = true;

                // First validate normal inputs
                step.find('input, select, textarea').each(function() {
                    if (!$(this).valid()) {
                        valid = false;
                    }
                });



                return valid;
            }

            // Next step button click event
            $('.next-btn').click(function() {
                var currentStep = $('#step-1');
                let valid = true;

                // Run normal validation
                if (!validateStep(currentStep)) {
                    valid = false;
                }


                if (valid) {
                    currentStep.hide();
                    $('#step-2').show();
                }
            });


            // Previous step button click event
            $('.prev-btn').click(function() {
                var currentStep = $('#step-2');
                currentStep.hide();
                $('#step-1').show();
            });


            form.on('submit', function(e) {
                console.log(userId);

                e.preventDefault();
                if (form.valid()) {
                    // Get submit button and disable it
                    var submitBtn = $('button[type="submit"]');
                    var originalText = submitBtn.text();

                    // Disable button and show loading state
                    submitBtn.prop('disabled', true)
                        .text('Submitting...')
                        .addClass('btn-secondary')
                        .removeClass('btn-primary');

                    var formData = new FormData(this);

                    var branchId = localStorage.getItem('selectedBranchId');
                    if (!branchId) {
                        alert('Please select a branch before submitting.');
                        // Re-enable button
                        submitBtn.prop('disabled', false)
                            .text(originalText)
                            .addClass('btn-primary')
                            .removeClass('btn-secondary');
                        return;
                    }
                    formData.set('branch_id', branchId);




                    var sourceDetails = {};
                    $('[name^="source_details"]').each(function() {
                        var key = $(this).attr('name').replace('source_details[', '')
                            .replace(']', '');
                        var value = $(this).val();
                        formData.append(`source_details[${key}]`, value);
                    });


                    $.ajax({
                        url: "{{ url('api/patient') }}",
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            "Authorization": "Bearer " + token
                        },
                        success: function(response) {
                            // Keep button disabled on success since we're redirecting
                            $('#patientsuccessMessage').text(response.message ||
                                'Patient created successfully').show();
                            $('#multiStepForm')[0].reset();
                            setTimeout(function() {
                                window.location.href =
                                    "{{ route('patients.index') }}";
                            }, 1500);
                        },
                        error: function(xhr) {
                            // Re-enable button on error
                            submitBtn.prop('disabled', false)
                                .text(originalText)
                                .addClass('btn-primary')
                                .removeClass('btn-secondary');

                            var errorMessage = '';
                            if (xhr.status === 422) { // Validation error
                                var errors = xhr.responseJSON.errors;
                                $.each(errors, function(key, messages) {
                                    errorMessage += messages[0] + '<br>';
                                });
                            }
                            $('#patienterrorMessage').html(errorMessage).show();
                        }
                    });
                }
            });


            // Fetch treatments
            fetchDiagnoses();




            function fetchDiagnoses() {
                let token = localStorage.getItem("token");
                let branchId = localStorage.getItem('selectedBranchId');

                let diagnosisDropdown = $('#diagnosisDropdown');

                diagnosisDropdown.select2({
                    placeholder: "Select Diagnoses",
                    allowClear: true,
                    width: '100%'
                });

                // Set search placeholder
                diagnosisDropdown.on('select2:open', function() {
                    $('.select2-search__field').attr('placeholder', 'Search diagnoses');
                });

                $.ajax({
                    url: "/api/diagnoses",
                    type: "GET",
                    data: {
                        branch_id: branchId
                    },
                    headers: {
                        "Authorization": "Bearer " + token
                    },
                    success: function(response) {
                        diagnosisDropdown.empty();

                        if (response.diagnoses && response.diagnoses.length > 0) {
                            $.each(response.diagnoses, function(i, diagnosis) {
                                let name = diagnosis.name.charAt(0).toUpperCase() + diagnosis
                                    .name.slice(1);
                                diagnosisDropdown.append(
                                    `<option value="${diagnosis.id}">${name}</option>`
                                );
                            });
                        }

                        diagnosisDropdown.trigger('change');
                    },
                    error: function(xhr) {
                        console.error("Unable to fetch diagnoses:", xhr.responseText);
                    }
                });
            }



            $(document).ready(function() {
                fetchDiagnoses();
            });





            // Fetch Doctors for Dropdown

            $.ajax({
                url: "{{ url('/api/doctors') }}",
                type: "GET",
                headers: {
                    "Authorization": "Bearer " + token
                },
                success: function(data) {
                    let doctorDropdown = $('#doctorSelect');
                    doctorDropdown.empty();
                    doctorDropdown.append('<option value="">Select doctor</option>');

                    let doctorsList = data.doctors || data;
                    $.each(doctorsList, function(key, doctor) {
                        let name = doctor.fullname;
                        let formattedName = name.charAt(0).toUpperCase() + name
                            .slice(1);

                        doctorDropdown.append(
                            '<option value="' + doctor.id + '">' +
                            formattedName + '</option>'
                        );
                    });

                },
                error: function(xhr) {
                    console.log("API Error:", xhr.status, xhr.responseText);
                }
            });

        });
    </script>
@endsection
