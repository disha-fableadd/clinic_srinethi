@extends('layout.app')


@php
    use App\Models\Setting;

    $currentProjectTypeId = (int) Setting::getValue('project_type_id', 1);
@endphp

<style>
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
          max-height: 200px;              /* limit max height */
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

        a.btn.btn-primary.btn-sm,
        a.btn.btn-primary.btn-rounded.addsymp {
            font-size: 10px;
            padding: 4px 7px;
        }

        .patient-form {
            height: 1100px !important;
        }

        a#openReferralModal {
            width: 112px;
            font-size: 9px;
        }

        .d-flex.align-items-center.justify-content-between.symplabel {
            font-size: 11px;
        }

        .form-group.symimg {
            font-size: 11px;
        }
    }

    @media (min-width: 360px) and (max-width: 740px) {
        a#openReferralModal {
            width: 104px;
            font-size: 8px;
            padding: 2px 6px !important;
            white-space: nowrap;
            margin-left: 1px;
        }

        a.btn.btn-primary.btn-sm,
        a.btn.btn-primary.btn-rounded.addsymp {
            display: flex;
            align-items: center;
        }
    }

    @media (min-width: 344px) and (max-width: 882px) {
        a#openReferralModal {
            width: 83px;
            font-size: 6px;
        }
    }
</style>
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="row" style="padding-top:15px">
                <div class="col-6">
                    <h4 class="page-title patient-title">Edit Patient</h4>
                </div>
                @if (app('hasPermission')(5, 'view'))
                    <div class="col-6 patient-button m-b-2">
                        <a href="{{ route('patients.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                            <i class="fa fa-arrow-left m-r-5 icon3"></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    </div>
                @endif
            </div>
            <div class="row">
                <div class="col-12">
                    <form class="form-container patient-form all-form" id="multiStepForm" method="POST"
                        action="javascript:void(0);" style="width:60%; padding-bottom: 75px;" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="patientId" value="{{ $patient_id ?? '' }}">
                        <div class="form-step edit-patient-form" id="step-1">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-user icon-style"></i> Full Name <span
                                                class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="fullname"
                                            placeholder="Enter Full Name" required>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-phone icon-style"></i> Phone <span
                                                class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="phone" placeholder="Enter Phone"maxlength="10"
                                            required>
                                    </div>
                                </div>
                                   <div class="col-12 col-md-4" id="email_container" style="{{ $currentProjectTypeId === 3 ? 'display: none;' : '' }}">
                                    <div class="form-group">
                                        <label><i class="fas fa-envelope icon-style"></i> Email</label>
                                        <input class="form-control" type="email" name="email" placeholder="Enter Email">
                                    </div>
                                </div>
                                <div id="birthdate_container" class="{{ $currentProjectTypeId === 3 ? 'col-6 col-md-6' : 'col-6 col-md-4' }}">
                                    <div class="form-group">
                                        <label><i class="fas fa-cake-candles icon-style"></i> Birthdate</label>
                                        <input type="date" class="form-control" name="birthdate" id="birth_date">
                                    </div>
                                </div>
                                  <div id="age_container" class="{{ $currentProjectTypeId === 3 ? 'col-6 col-md-6' : 'col-6 col-md-4' }}">
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
                                            <label class="mr-2"><i class="fas fa-medkit icon-style"></i> Diagnosis </label>
                                            @if (app('hasPermission')(35, 'create'))
                                                <a href="{{ route('diagnosis.create') }}" target="_blank"
                                                    class="btn btn-primary btn-sm">
                                                    <i class="fas fa-plus"></i> <span class="hdr-btn-text">Add</span>
                                                </a>
                                            @endif
                                        </div>
                                        <select class="form-control select2" name="diagnosis_id[]" id="diagnosisDropdown"
                                            multiple  style="min-height: 100px; max-height: 200px; overflow-y: auto;">
                                            <!-- Options will be loaded dynamically -->
                                        </select>
                                        <small class="text-muted">You can select multiple diagnoses</small>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <label><i class="fas fa-notes-medical icon-style"></i> Symptoms</label>
                                        </div>
                                        <select name="symptoms[]" class="form-control" id="symptomDropdown" multiple
                                            style="min-height: 100px; max-height: 200px; overflow-y: auto;"></select>
                                        <small class="text-muted">You can select multiple symptoms</small>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <i class="fas fa-notes-medical icon-style"></i>
                                        <span>Symptoms Input (Day-1)</span>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <div class="d-flex gap-3">
                                                <div>
                                                    <input type="radio" name="symptom_option" value="image"
                                                        onchange="toggleSymptomInput()"
                                                        {{ old('symptom_option', !empty($symptom_images) ? 'image' : '') == 'image' ? 'checked' : '' }}>
                                                    <span>Upload Image</span>
                                                </div>
                                                <div>
                                                    <input type="radio" name="symptom_option" value="text"
                                                        style="margin-left:10px" onchange="toggleSymptomInput()"
                                                        {{ old('symptom_option', !empty($symptom_remarks) ? 'text' : '') == 'text' ? 'checked' : '' }}>
                                                    <span>Remarks</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div id="symptomImageBox"
                                                style="{{ old('symptom_option', !empty($symptom_images) ? 'image' : '') == 'image' ? '' : 'display:none;' }}">
                                                <input type="file" id="symptomImagesInput" class="form-control" name="symptom_images[]"
                                                    multiple accept="image/*" onchange="previewSymptomImages(this)">
                                                <small class="text-muted">You can select multiple images</small>
                                                <div id="symptomImagePreview" class="mt-3 d-flex flex-wrap gap-2">
                                                    @if (!empty($symptom_images))
                                                        @foreach ($symptom_images as $img)
                                                            <img src="{{ asset('uploads/symptoms/' . $img) }}"
                                                                style="width:100px; height:100px; object-fit:cover; border:1px solid #ccc; border-radius:5px;">
                                                        @endforeach
                                                    @endif
                                                </div>
                                                <div id="existing_symptom_images" class="d-flex flex-wrap gap-2"></div>
                                            </div>
                                            <div id="symptomTextBox"
                                                style="{{ old('symptom_option', !empty($symptom_remarks) ? 'text' : '') == 'text' ? '' : 'display:none;' }}">
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
                                    }
                                </script>
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
                                                class="btn btn-primary btn-rounded btn-hdr d-flex align-items-center justify-content-center"
                                                id="openReferralModal" data-bs-toggle="modal"
                                                data-bs-target="#addReferralModal"
                                                style="padding:8px 12px !important; white-space: nowrap; margin-left: 15px;">
                                                <i class="fa fa-plus m-0"></i>
                                                <span style="margin-left: 6px;" class="d-none d-md-inline">Edit Referral Details</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div id="referralSummary" class="mt-2 text-muted"
                                    style="border: 1px solid #cfece0; padding: 10px 25px; margin-left: 20px; color: black !important; display: none;">
                                </div>
                                <div id="sourceerrorMessage" class="text-danger"
                                    style="margin-left: 20px;width: 100% !important;font-size: 12px;"></div>

                            </div>
                            <button type="button" class="btn btn-primary next-btn mt-3"
                                style="margin-left:5px; padding:8px 50px; float:right;">Next</button>
                        </div>
                        <!-- Step 2: Additional Information -->
                        <div class="form-step patient-form2" id="step-2" style="display:none;">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label><i class="fas fa-image icon-style"></i> Profile Image</label>
                                        <div>
                                            <img id="profilePreview"
                                                src="{{ asset($patient->profile ?? 'admin/assets/img/img1.png') }}"
                                                alt="Profile Image" width="100" height="100"
                                                style="border-radius: 10px; object-fit: cover; border: 1px solid #ccc;">
                                        </div>
                                        <input type="file" class="form-control mt-2" name="profile"
                                            id="profileInput">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label><i class="fas fa-map-marker-alt icon-style"></i> Address</label>
                                        <input type="text" class="form-control" name="address"
                                            placeholder="Enter Address">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-6">
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
                                <div class="col-12 col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-flag icon-style"></i> State</label>
                                        <select class="form-control select2" id="select-state" name="state">
                                            <option value="">Select State</option>
                                            <option value="Gujarat">Gujarat</option>
                                            <option value="Maharashtra">Maharashtra</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-6">
                                                <label><i class="fas fa-sticky-note icon-style"></i> Note Type</label>
                                                <select class="form-control global-note-type select2" id="select-note"
                                                    name="note_type">
                                                    <option value=""></option>
                                                    <option value="speech">Speech-to-Text Note</option>
                                                    <option value="audio">Audio Note</option>
                                                </select>
                                            </div>
                                            <div class="col-6">
                                                <label><i class="fas fa-clock icon-style"></i> Duration</label>
                                                <select class="form-control select2" id="select-time"
                                                    name="note_interval">
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
                                <div class="col-12" id="notes-container"></div>
                                <div class="col-12 d-flex justify-content-end mb-3">
                                    <button id="addNoteButton" class="btn btn-primary mt-2">
                                        <i class="fas fa-plus"></i> Add Note
                                    </button>
                                </div>
                            </div>
                            <div id="editpatientsuccessMessage" class="alert alert-success" style="display:none;"></div>
                            <button type="button" class="btn btn-danger prev-btn mt-3"
                                style="padding:8px 50px; border-radius:50px; float:left;">Previous</button>
                            <button type="submit" class="btn btn-primary mt-3"
                                style="padding:8px 50px; border-radius:50px; float:right;">Submit</button>
                        </div>
                        <!-- Referral Modal -->
                        <div class="modal fade" id="addReferralModal" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header" style="background-color: #CFECE0; color:black">
                                        <h5 class="modal-title">Add Referral Details</h5>
                                        <button type="button" class="btn-close custom-close" data-bs-dismiss="modal"
                                            aria-label="Close"><i class="fas fa-times"></i></button>
                                    </div>
                                    <div class="modal-body" id="referralFields"></div>
                                    <div id="referralError" class="text-danger mb-3"
                                        style="display:none; margin-left:20px !important;"></div>
                                    <div class="modal-footer">
                                        <button type="button" id="saveReferralDetails" class="btn btn-primary">Save
                                            Details</button>
                                        <button type="button" class="btn btn-danger"
                                            style="border-radius:50px !important" data-bs-dismiss="modal">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const birthInput = document.getElementById("birth_date");
            const ageInput = document.getElementById("age");
            const today = new Date();
            const yesterday = new Date(today);
            yesterday.setDate(today.getDate() - 1);
            const maxDate = yesterday.toISOString().split("T")[0];
            birthInput.setAttribute("max", maxDate);
            birthInput.addEventListener("change", function() {
                const birthDate = new Date(this.value);
                if (!isNaN(birthDate)) {
                    let age = today.getFullYear() - birthDate.getFullYear();
                    const m = today.getMonth() - birthDate.getMonth();
                    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                        age--;
                    }
                    ageInput.value = age;
                } else {
                    ageInput.value = "";
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#symptomDropdown').select2({
                placeholder: "Select Symptoms",
                width: '100%'
            });
        });

        function loadSymptoms(symptomIds) {
            let branchId = localStorage.getItem('selectedBranchId');
            $.ajax({
                url: "{{ route('symptoms.list') }}",
                type: "GET",
                data: {
                    branch_id: branchId
                },
                success: function(data) {
                    let symptomsList = Array.isArray(data) ? data : (data.symptoms || []);
                    let symptomDropdown = $('#symptomDropdown');
                    symptomDropdown.empty().append('<option value="">Select Symptom</option>');
                    $.each(symptomsList, function(index, symptom) {
                        const name = symptom.name.charAt(0).toUpperCase() + symptom.name.slice(1);
                        symptomDropdown.append(`<option value="${symptom.id}">${name}</option>`);
                    });
                    if (symptomIds && symptomIds.length > 0) {
                        symptomDropdown.val(symptomIds).trigger('change');
                    }
                },
                error: function(xhr) {
                    console.error("Failed to load symptoms:", xhr.status, xhr.responseText);
                }
            });
        }
        loadSymptoms();
    </script>
    <script>
        let removedSymptomImages = [];
        let selectedFiles = [];

        function renderExistingImages(images) {
            const imagePreviewContainer = $('#existing_symptom_images');
            imagePreviewContainer.empty();
            images.forEach((image, index) => {
                const isLocal = window.location.hostname === 'localhost' || window.location.hostname ===
                    '127.0.0.1';
                const fullImagePath = window.location.origin + (isLocal ? '' : '/public') + image;
                imagePreviewContainer.append(`
                    <div class="symptom-image-wrapper" style="position:relative; margin:5px;">
                        <img src="${fullImagePath}" class="preview-img" style="object-fit:cover; border-radius:10px; width:80px; height:80px;" />
                        <button type="button" class="remove-db-btn" data-path="${image}" style="position:absolute; top:-5px; right:-5px;">&times;</button>
                    </div>
                `);
            });
        }
        $('#existing_symptom_images').on('click', '.remove-db-btn', function() {
            const imagePath = $(this).data('path');
            removedSymptomImages.push(imagePath);
            $(this).closest('.symptom-image-wrapper').remove();
        });
        const imageInput = document.getElementById('symptomImagesInput');
        const previewContainer = document.getElementById('symptomImagePreview');
        imageInput.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            selectedFiles = [...files];
            previewContainer.innerHTML = '';
            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.createElement('div');
                    preview.className = 'image-preview-container';
                    preview.style.position = 'relative';
                    preview.style.margin = '5px';
                    preview.innerHTML = `
                        <img src="${e.target.result}" class="preview-img" style="object-fit:cover; border-radius:10px; width:80px; height:80px;" />
                        <button type="button" class="remove-new-btn" data-index="${index}" style="position:absolute; top:-5px; right:-5px;">&times;</button>
                    `;
                    previewContainer.appendChild(preview);
                };
                reader.readAsDataURL(file);
            });
        });
        previewContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-new-btn')) {
                const indexToRemove = parseInt(e.target.getAttribute('data-index'));
                selectedFiles.splice(indexToRemove, 1);
                const dataTransfer = new DataTransfer();
                selectedFiles.forEach(file => dataTransfer.items.add(file));
                imageInput.files = dataTransfer.files;
                imageInput.dispatchEvent(new Event('change'));
            }
        });
    </script>
    <script>
        $('#profileInput').on('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#profilePreview').attr('src', e.target.result);
                };
                reader.readAsDataURL(file);
            }
        });
        $('#typedropdown').select2({
            placeholder: "Select Patient type",
            width: '100%'
        });
        $('#select-city').select2({
            placeholder: "Select City",
            width: '100%'
        });
        $('#select-state').select2({
            placeholder: "Select State",
            width: '100%'
        });
        $('#select-note').select2({
            placeholder: "Select Note type",
            width: '100%'
        });
        $('#select-time').select2({
            placeholder: "Select Duration",
            width: '100%'
        });
        $('#select-blood').select2({
            placeholder: "Select Referral Source",
            width: '100%'
        });
    </script>
    <script>
        $(document).ready(function() {
            var form = $("#multiStepForm");
            form.validate({
                rules: {
                    fullname: "required",
                    phone: {
                        required: true,
                        number: true,
                        minlength: 10,
                        maxlength: 15
                    },
                    patient_type: {
                        required: true
                    },
                    age: {
                        required: true,
                        number: true,
                        min: 0
                    }
                },
                messages: {
                    fullname: "Please enter your full name",
                    phone: "Please enter a valid phone number",
                    patient_type: "Please select patient type",
                    age: {
                        required: "Please enter your age",
                        number: "Age must be a number",
                        min: "Age cannot be negative"
                    }
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

            function validateStep(step) {
                var valid = true;
                step.find('input, select, textarea').each(function() {
                    if (!$(this).valid()) {
                        valid = false;
                    }
                });
                return valid;
            }
            $('.next-btn').click(function() {
                var currentStep = $('#step-1');
                if (validateStep(currentStep)) {
                    currentStep.hide();
                    $('#step-2').show();
                }
            });
            $('.prev-btn').click(function() {
                var currentStep = $('#step-2');
                currentStep.hide();
                $('#step-1').show();
            });
            const patientId = $("#patientId").val();
            const token = localStorage.getItem("token");
            let removedSymptomImages = [];
            $('#diagnosisDropdown').select2({
                placeholder: "Select Diagnosis",
                allowClear: true,
                width: '100%'
            });
            $.ajax({
                url: `/api/patient/${patientId}`,
                type: "GET",
                dataType: "json",
                headers: {
                    "Authorization": "Bearer " + token
                },
                success: function(response) {
                    if (!response || !response.patient) return;
                    const patient = response.patient;
                    $("input[name='fullname']").val(patient.fullname);
                    $("input[name='phone']").val(patient.phone);
                       $("input[name='email']").val(patient.email);
                    $("input[name='birthdate']").val(patient.birthdate);
                    $("input[name='age']").val(patient.age);
                    $("select[name='patient_type']").val(patient.patient_type).trigger('change');
                    $("input[name='address']").val(patient.address);
                    $("select[name='city']").val(patient.city).trigger('change');
                    $("select[name='state']").val(patient.state).trigger('change');
                    $('#profilePreview').attr('src', patient.profile ||
                        '{{ asset('admin/assets/img/img1.png') }}');
                    if (patient.symptom_images && patient.symptom_images.length > 0) {
                        $("input[name='symptom_option'][value='image']").prop("checked", true);
                        $("#symptomImageBox").show();
                        $("#symptomTextBox").hide();
                        const imagePreviewContainer = $('#existing_symptom_images');
                        imagePreviewContainer.empty();
                        patient.symptom_images.forEach((image, index) => {
                            const isLocal = window.location.hostname === 'localhost' || window
                                .location.hostname === '127.0.0.1';
                            const fullImagePath = window.location.origin + (isLocal ? '/' :
                                '/public/') + image;
                            imagePreviewContainer.append(`
                                <div class="symptom-image-wrapper" style="position:relative; margin: 5px;">
                                    <img src="${fullImagePath}" class="preview-img" style="object-fit:cover; border-radius:10px; width:50px; height:50px;" />
                                    <button class="remove-btn" data-path="${image}" data-index="${index}" style="position:absolute; top:-5px; right:-5px;">&times;</button>
                                </div>
                            `);
                        });
                    } else if (patient.symptom_remarks && patient.symptom_remarks.trim() !== "") {
                        $("input[name='symptom_option'][value='text']").prop("checked", true);
                        $("#symptomTextBox").show();
                        $("#symptomImageBox").hide();
                        $("textarea[name='symptom_remarks']").val(patient.symptom_remarks);
                    } else {
                        $("input[name='symptom_option']").prop("checked", false);
                        $("#symptomImageBox").hide();
                        $("#symptomTextBox").hide();
                    }
                    $('#existing_symptom_images').on('click', '.remove-btn', function() {
                        removedSymptomImages.push($(this).data('path'));
                        $(this).closest('.symptom-image-wrapper').remove();
                    });
                   // Load selected symptoms
                    const selectedSymptomIds = patient.symptoms || [];
                    loadSymptoms(selectedSymptomIds);

                    // Load diagnoses
                    const selectedDiagnosisIds = response.diagnosis_ids || [];
                    loadDiagnoses(selectedDiagnosisIds);

                    // Notes
                    $('#noteType').val(patient.note_type || 'speech').trigger('change');
                    $('#noteInterval').val(patient.note_interval || '5000').trigger('change');

                    if (patient.note && patient.note.length > 0) {
                        patient.note.forEach(note => addNoteSection(note.type, note.content));
                    } else {
                        addNoteSection($('#noteType').val());
                    }
                },
                error: function(xhr) {
                    console.error("Error fetching patient data:", xhr);
                    alert("Failed to fetch patient data.");
                }
            });

            function loadDiagnoses(selectedIds = []) {
                const branchId = localStorage.getItem('selectedBranchId');
                $.ajax({
                    url: "/api/diagnoses",
                    type: "GET",
                    data: {
                        branch_id: branchId
                    },
                    success: function(response) {
                        const diagnosisDropdown = $('#diagnosisDropdown');
                        diagnosisDropdown.empty();
                        (response.diagnoses || []).forEach(diagnosis => {
                            const selected = selectedIds.map(String).includes(String(diagnosis
                                .id)) ? 'selected' : '';
                            diagnosisDropdown.append(
                                `<option value="${diagnosis.id}" ${selected}>${diagnosis.name}</option>`
                            );
                        });
                        diagnosisDropdown.trigger('change');
                    },
                    error: function(xhr) {
                        console.error("Failed to load diagnoses:", xhr);
                    }
                });
            }
            $('#addNoteButton').click(function(e) {
                e.preventDefault();
                addNoteSection($('#noteType').val());
            });
            form.on('submit', function(e) {
                e.preventDefault();
                if (form.valid()) {
                    let formData = new FormData(this);
                    formData.append('_method', 'PUT');
                    formData.append('removed_symptom_images', JSON.stringify(removedSymptomImages));
                    const referralSource = $("input[name='referral_source']").val();
                    if (referralSource) {
                        formData.append('referral_source', referralSource);
                    }
                    $('input[name^="source_details["]').each(function() {
                        const key = $(this).attr('name').replace('source_details[', '').replace(']',
                            '');
                        const value = $(this).val();
                        formData.append(`source_details[${key}]`, value);
                    });
                    $.ajax({
                        url: `/api/patient/${patientId}`,
                        type: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        headers: {
                            "Authorization": "Bearer " + token
                        },
                        success: function(response) {
                            $("#editpatientsuccessMessage").text(response.message ||
                                'Patient updated successfully').show();
                            setTimeout(() => {
                                window.location.href = "{{ route('patients.index') }}";
                            }, 1500);
                        },
                        error: function(xhr) {
                            let errorMessage = '';
                            if (xhr.status === 422) {
                                const errors = xhr.responseJSON.errors;
                                $.each(errors, function(key, messages) {
                                    errorMessage += messages[0] + '<br>';
                                });
                            }
                            $('#editpatienterrorMessage').html(errorMessage).show();
                        }
                    });
                }
            });
            let endTimeInterval = 1000;

            function addNoteSection(type, content = '') {
                let html = '';
                if (type === 'speech') {
                    html = `
                        <div class="note-section mt-3">
                            <div class="form-group">
                                <div class="d-flex justify-content-end gap-2 mt-2">
                                    <button type="button" class="btn btn-danger rounded-pill removeButton">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <button type="button" class="btn btn-primary mx-2 startButton">
                                        <i class="fas fa-microphone"></i>
                                    </button>
                                    <button type="button" class="btn btn-primary speakButton">
                                        <i class="fas fa-volume-up"></i>
                                    </button>
                                </div>
                                <textarea class="form-control note-textarea mt-2" name="speech_note[]" placeholder="Speak or type here" style="border-radius:10px">${content}</textarea>
                            </div>
                        </div>
                    `;
                } else {
                    const audioPlayer = content ?
                        `<div class="audio-player-container"><audio controls><source src="${content}" type="audio/wav"></audio></div>` :
                        '<div class="audio-player-container"><audio controls style="display:none;"></audio></div>';
                    html = `
                        <div class="note-section mt-3">
                            <div class="form-group">
                                <div class="d-none d-lg-flex align-items-center justify-content-between">
                                    ${audioPlayer}
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-danger rounded-pill mr-2 removeButton">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <button type="button" class="btn btn-primary recordButton">
                                            <i class="fas fa-microphone"></i> Record
                                        </button>
                                        <button type="button" class="btn btn-success rounded-pill stopButton" style="display:none;">
                                            <i class="fas fa-stop"></i> Stop
                                        </button>
                                    </div>
                                </div>
                                <div class="d-lg-none">
                                    ${audioPlayer}
                                    <div class="d-flex justify-content-end gap-2 mt-2">
                                        <button type="button" class="btn btn-danger rounded-pill removeButton mr-2">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <button type="button" class="btn btn-primary recordButton">
                                            <i class="fas fa-microphone"></i> Record
                                        </button>
                                        <button type="button" class="btn btn-success rounded-pill stopButton" style="display:none;">
                                            <i class="fas fa-stop"></i> Stop
                                        </button>
                                    </div>
                                </div>
                                <input type="hidden" class="audio-data" name="audio_note[]" value="${content || ''}">
                            </div>
                        </div>
                    `;
                }
                $('#notes-container').append(html);
                initializeNoteSection($('#notes-container .note-section').last(), type);
            }
            $('.global-note-type').change(function() {
                $('#notes-container').html('');
                $('#addNoteButton').click();
            });

            function initializeNoteSection(section, type) {
                if (type === 'speech') {
                    const startButton = section.find('.startButton');
                    const speakButton = section.find('.speakButton');
                    const outputTextarea = section.find('.note-textarea');
                    const removeButton = section.find('.removeButton');
                    const recognition = new(window.SpeechRecognition || window.webkitSpeechRecognition)();
                    recognition.lang = "en-US";
                    recognition.continuous = true;
                    recognition.interimResults = true;
                    let speechTimer;
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
                        clearTimeout(speechTimer);
                    };
                    recognition.onerror = (event) => {
                        alert(`Speech Recognition Error: ${event.error}`);
                        clearTimeout(speechTimer);
                    };
                    startButton.click((e) => {
                        e.preventDefault();
                        try {
                            recognition.start();
                            const duration = parseInt($('#noteInterval').val()) + endTimeInterval;
                            speechTimer = setTimeout(() => {
                                recognition.stop();
                            }, duration);
                        } catch (error) {
                            alert(`Error starting speech recognition: ${error.message}`);
                        }
                    });
                    speakButton.click((e) => {
                        e.preventDefault();
                        const utterance = new SpeechSynthesisUtterance(outputTextarea.val());
                        window.speechSynthesis.speak(utterance);
                        const duration = parseInt($('#noteInterval').val()) + endTimeInterval;
                        setTimeout(() => {
                            window.speechSynthesis.cancel();
                        }, duration);
                    });
                    removeButton.click((e) => {
                        e.preventDefault();
                        clearTimeout(speechTimer);
                        section.remove();
                    });
                } else {
                    let mediaRecorder;
                    let audioChunks = [];
                    const recordButton = section.find('.recordButton');
                    const stopButton = section.find('.stopButton');
                    const audioPlayer = section.find('audio');
                    const audioData = section.find('.audio-data');
                    const removeButton = section.find('.removeButton');
                    recordButton.click(async () => {
                        try {
                            const stream = await navigator.mediaDevices.getUserMedia({
                                audio: true
                            });
                            mediaRecorder = new MediaRecorder(stream);
                            audioChunks = [];
                            mediaRecorder.ondataavailable = (e) => audioChunks.push(e.data);
                            mediaRecorder.onstop = () => {
                                const audioBlob = new Blob(audioChunks, {
                                    type: 'audio/wav'
                                });
                                const audioUrl = URL.createObjectURL(audioBlob);
                                audioPlayer.attr('src', audioUrl).show();
                                const reader = new FileReader();
                                reader.onloadend = () => {
                                    audioData.val(reader.result);
                                };
                                reader.readAsDataURL(audioBlob);
                            };
                            mediaRecorder.start();
                            recordButton.hide();
                            stopButton.show();
                            const duration = parseInt($('#noteInterval').val()) + endTimeInterval;
                            setTimeout(() => {
                                if (mediaRecorder.state === "recording") {
                                    mediaRecorder.stop();
                                    stream.getTracks().forEach(track => track.stop());
                                    recordButton.show();
                                    stopButton.hide();
                                }
                            }, duration);
                        } catch (err) {
                            console.error("Error recording audio:", err);
                            alert("Error accessing microphone");
                        }
                    });
                    stopButton.click(() => {
                        if (mediaRecorder && mediaRecorder.state === "recording") {
                            mediaRecorder.stop();
                            mediaRecorder.stream.getTracks().forEach(track => track.stop());
                            recordButton.show();
                            stopButton.hide();
                        }
                    });
                    removeButton.click(() => {
                        section.remove();
                    });
                }
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            let patientReferralData = {};

            // Initialize Select2 for Referral Source
            $('#select-blood').select2({
                placeholder: "Select Referral Source",
                width: '100%',
                templateResult: formatReferralSource,
                templateSelection: formatReferralSourceSelection
            });

            function formatReferralSource(referral) {
                return referral.text;
            }

            function formatReferralSourceSelection(referral) {
                return referral.text;
            }

            // Open modal & load fields
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
                    <div class="text-center my-2"><strong>OR</strong></div>
                    <div class="mb-3">
                        <label>Enter Doctor Name (if not listed)</label>
                        <input type="text" class="form-control" id="referralDoctorText" name="referral_name_text" placeholder="Type doctor name" value="${patientReferralData.doctor_name || ''}">
                    </div>
                `;
                        $('#referralFields').html(fieldsHtml);
                        loadReferralDoctors(() => {
                            if (patientReferralData.referral_name) {
                                $('#referralDoctorSelect').val(patientReferralData.referral_name)
                                    .trigger('change');
                            }
                        });
                        break;

                    case "Website":
                        fieldsHtml = `<div class="mb-3">
                                <label>Website Name</label>
                                <input type="text" class="form-control" name="website_name" value="${patientReferralData.website_name || ''}">
                              </div>`;
                        $('#referralFields').html(fieldsHtml);
                        break;

                    case "Advertisement":
                        fieldsHtml = `<div class="mb-3">
                                <label>Advertisement Name</label>
                                <input type="text" class="form-control" name="advertisement_name" value="${patientReferralData.advertisement_name || ''}">
                              </div>
                              <div class="mb-3">
                                <label>Description</label>
                                <textarea class="form-control" name="description">${patientReferralData.description || ''}</textarea>
                              </div>`;
                        $('#referralFields').html(fieldsHtml);
                        break;

                    case "Patients":
                        fieldsHtml = `<div class="mb-3">
                                <label>Patient Name</label>
                                <input type="text" class="form-control" name="patient_name" value="${patientReferralData.patient_name || ''}">
                              </div>`;
                        $('#referralFields').html(fieldsHtml);
                        break;

                    case "Insurance":
                        fieldsHtml = `<div class="mb-3">
                                <label>Referral Name</label>
                                <input type="text" class="form-control" name="referral_name" value="${patientReferralData.referral_name || ''}">
                              </div>
                              <div class="mb-3">
                                <label>Mobile</label>
                                <input type="number" class="form-control" name="mobile" value="${patientReferralData.mobile || ''}">
                              </div>
                              <div class="mb-3">
                                <label>Email</label>
                                <input type="email" class="form-control" name="email" value="${patientReferralData.email || ''}">
                              </div>`;
                        $('#referralFields').html(fieldsHtml);
                        break;

                    case "Others":
                        fieldsHtml = `<div class="mb-3">
                                <label>Other's Name</label>
                                <input type="text" class="form-control" name="other_name" value="${patientReferralData.other_name || ''}">
                              </div>`;
                        $('#referralFields').html(fieldsHtml);
                        break;

                    default:
                        fieldsHtml = ``;
                }

                if (selected) $("#addReferralModal").modal("show");
            });

            // Load referral doctors
            function loadReferralDoctors(callback) {
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
                            select.append(
                                `<option value="${doctor.id}" data-specialization="${doctor.specialization}" data-phone="${doctor.phone}" data-email="${doctor.email}">${doctor.doctor_name}</option>`
                            );
                        });

                        select.select2({
                            width: '100%',
                            placeholder: 'Select Doctor'
                        });
                        if (callback) callback();
                    },
                    error: function(err) {
                        console.error('Failed to load referral doctors', err);
                    }
                });
            }

            // Save referral details
            $("#saveReferralDetails").on("click", function() {
                let selectedSource = $("#select-blood").val();
                let sourceDetails = {};
                let $errorBox = $("#referralError");
                $errorBox.hide().html("");
                let allValid = true;

                $("#referralFields").find("input, textarea, select").each(function() {
                    let name = $(this).attr("name");
                    let value = $(this).val().trim();
                    if (!value && selectedSource !== "Doctors") {
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

                // Doctors logic
                if (selectedSource === "Doctors") {
                    let doctorId = $("#referralDoctorSelect").val();
                    let doctorText = $("#referralDoctorText").val().trim();

                    if (!doctorId && !doctorText) {
                        $("#sourceerrorMessage").text("⚠ Please select a doctor or enter a name").show();
                        return;
                    }

                    $(".hidden-referral-input").remove();
                    $("#multiStepForm").append(
                        `<input type="hidden" class="hidden-referral-input" name="referral_source" value="${selectedSource}">`
                        );

                    if (doctorId) {
                        const selectedOption = $("#referralDoctorSelect option:selected");
                        $("#multiStepForm").append(
                            `<input type="hidden" class="hidden-referral-input" name="source_details[doctor_id]" value="${doctorId}">`
                            );

                        let summaryHtml = `
                    <strong>Doctor: ${selectedOption.text()}</strong>
                    <ul>
                        <li>Specialization: ${selectedOption.data("specialization") || '-'}</li>
                        <li>Phone: ${selectedOption.data("phone") || '-'}</li>
                        <li>Email: ${selectedOption.data("email") || '-'}</li>
                    </ul>`;
                        $("#referralSummary").html(summaryHtml).show();
                        patientReferralData = {
                            referral_name: selectedOption.text(), // Use doctor name here
                            doctor_name: selectedOption.text(),
                            specialization: selectedOption.data("specialization"),
                            phone: selectedOption.data("phone"),
                            email: selectedOption.data("email")
                        };

                    } else {
                        // New doctor → create via API
                        const token = localStorage.getItem('token');
                        const branchId = localStorage.getItem('selectedBranchId');
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
                                const doctor = res.data;
                                $("#multiStepForm").append(
                                    `<input type="hidden" class="hidden-referral-input" name="source_details[doctor_id]" value="${doctor.id}">`
                                    );
                                let summaryHtml =
                                    `<strong>Doctor: ${doctor.doctor_name}</strong>`;
                                $("#referralSummary").html(summaryHtml).show();
                                patientReferralData = doctor;
                            }
                        });
                    }
                    $("#addReferralModal").modal("hide");
                    return;
                }

                // Non-doctor sources
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
                patientReferralData = sourceDetails;
                $("#addReferralModal").modal("hide");
            });


            // Populate on page load
            const patientId = $("#patientId").val();
            const token = localStorage.getItem("token");

            $.ajax({
                url: `/api/patient/${patientId}`,
                type: "GET",
                headers: {
                    "Authorization": "Bearer " + token
                },
                success: function(response) {
                    if (!response || !response.patient) return;
                    const patient = response.patient;
                    // patientReferralData = patient.source_details || {};
                    try {
    patientReferralData = typeof patient.source_details === 'string'
        ? JSON.parse(patient.source_details)
        : (patient.source_details || {});
} catch (e) {
    patientReferralData = {};
}


                    if (patient.referral_source) {
                        $("#select-blood").val(patient.referral_source).trigger("change");

                        if (patient.referral_source === "Doctors" && patient.source_details.doctor_id) {
                            // Fetch doctor details by ID
                            $.ajax({
                                url: "/api/referal_doctors/" + patient.source_details.doctor_id,
                                type: "GET",
                                headers: {
                                    "Authorization": "Bearer " + token
                                },
                                success: function(res) {
                                    const doctor = res.data ||
                                    res; // make sure your API returns doctor in res.data
                                    let summaryHtml = `
                            <strong>Doctor: ${doctor.doctor_name}</strong>
                            <ul>
                                <li>Specialization: ${doctor.specialization || '-'}</li>
                                <li>Phone: ${doctor.phone || '-'}</li>
                                <li>Email: ${doctor.email || '-'}</li>
                            </ul>`;
                                    $("#referralSummary").html(summaryHtml).show();

                                    // Update local data for modal re-open
                                    patientReferralData = {
                                        doctor_id: doctor.id,
                                        doctor_name: doctor.doctor_name,
                                        specialization: doctor.specialization,
                                        phone: doctor.phone,
                                        email: doctor.email
                                    };
                                }
                            });
                        } else {
                            // Non-doctor sources
                            let summaryHtml = `<strong>${patient.referral_source}</strong><ul>`;
                            $.each(patient.source_details, function(key, value) {
                                summaryHtml +=
                                    `<li>${key.replace(/_/g, ' ')}: ${value || '-'}</li>`;
                            });
                            summaryHtml += `</ul>`;
                            $("#referralSummary").html(summaryHtml).show();
                        }
                    }
                }
            });

        });
    </script>


@endsection
