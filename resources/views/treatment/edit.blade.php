@extends('layout.app')



<style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice__display {
        cursor: default;
        padding-left: 12px !important;
        padding-right: 5px;
    }

    .treatment-button {
        padding-right: 65px !important;
        text-align: center !important;
    }

    .treatment-title {
        padding-left: 95px !important;
        text-align: center !important;
    }

    .form-container {
        width: 60% !important;
        padding-bottom: 60px !important;
    }

    /* GST select2 styling to match reference UI */
    .gst-select2+.select2-container .select2-selection--multiple,
    .gst-select2+.select2-container .select2-selection--single {
        border: 1px solid #cfe6d9 !important;
        border-radius: 22px !important;
        min-height: 42px !important;
        padding: 4px 10px !important;
        background: #fff !important;
        box-shadow: none !important;
    }

    .gst-select2+.select2-container .select2-selection__rendered {
        line-height: 30px !important;
    }

    .gst-select2+.select2-container .select2-selection__choice {
        background: #d9efe6 !important;
        border: 1px solid #c6e3d6 !important;
        border-radius: 16px !important;
        padding: 1px 8px !important;
        color: #1f4b3e !important;
        font-size: 13px !important;
        margin: 4px 6px 0 0 !important;
    }

    .gst-select2+.select2-container .select2-selection__choice__remove {
        color: #1f4b3e !important;
        margin-right: 6px !important;
    }

    .gst-select2+.select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 26px !important;
    }

    .gst-select2+.select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #d9efe6 !important;
        color: #1f4b3e !important;
    }

    /* Fix Select2 width when element was initially hidden */
    #product_gst_div .select2-container,
    #product_gst_div .select2-selection,
    #gst_option+.select2-container,
    #product_gst+.select2-container {
        width: 100% !important;
    }

    /* Prevent selected GST chips overlapping */
    .gst-select2+.select2-container .select2-selection--multiple .select2-selection__rendered {
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 6px !important;
        padding: 0 !important;
        line-height: normal !important;
    }

    .gst-select2+.select2-container .select2-selection--multiple .select2-selection__choice {
        margin: 0 !important;
    }

    .gst-select2+.select2-container .select2-selection--multiple {
        height: auto !important;
    }

    /* GST select2 styling to match reference UI */
    .gst-select2+.select2-container .select2-selection--multiple,
    .gst-select2+.select2-container .select2-selection--single {
        border: 1px solid #cfe6d9 !important;
        border-radius: 22px !important;
        min-height: 42px !important;
        padding: 4px 10px !important;
        background: #fff !important;
        box-shadow: none !important;
    }

    .gst-select2+.select2-container .select2-selection__rendered {
        line-height: 30px !important;
    }

    .gst-select2+.select2-container .select2-selection__choice {
        background: #d9efe6 !important;
        border: 1px solid #c6e3d6 !important;
        border-radius: 16px !important;
        padding: 1px 8px !important;
        color: #1f4b3e !important;
        font-size: 13px !important;
        margin-top: 6px !important;
    }

    .gst-select2+.select2-container .select2-selection__choice__remove {
        color: #1f4b3e !important;
        margin-right: 6px !important;
    }

    .gst-select2+.select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 26px !important;
    }

    .gst-select2+.select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #d9efe6 !important;
        color: #1f4b3e !important;
    }


    @media screen and (max-width: 767px) {


        .page-title {
            font-size: 19px !important;
        }

        .treatment-button {
            padding-right: 15px !important;
            text-align: right !important;
        }

        .treatment-title {
            padding-left: 0px !important;
            text-align: left !important;
        }



    }
</style>

@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="row" style="padding-top:15px">
                <div class="col-sm-6 col-6">
                    <h4 class="page-title treatment-title">Edit Treatment</h4>
                </div>
                @if (app('hasPermission')(7, 'view'))
                    <div class="col-sm-6 col-6  treatment-button m-b-2">
                        <a href="{{ route('treatment.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                            <i class="fa fa-arrow-left m-r-5 icon3"></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    </div>
                @endif
            </div>

            <div class="row">
                <div class="col-12">
                    <form id="editTreatmentForm" class="form-container">
                        @csrf

                        <div class="form-group">
                            <label><i class="fas fa-cogs icon-style"></i> Treatment Name</label>
                            <input class="form-control" type="text" id="name" name="name" required>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-user-md icon-style"></i> Doctor Name</label>
                            <select class="form-control select2 doctorSelect" id="doctorSelect" name="doctor_id"
                                id="doctorSelect" required>
                                <option value="">Select Doctor</option>
                            </select>
                        </div>


                        <div class="form-group">
                            <label><i class="fas fa-clipboard-list icon-style"></i> Description</label>
                            <textarea cols="7" rows="4" class="form-control" id="description" name="description"
                                style="border-radius:10px"></textarea>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-cogs icon-style"></i> Price</label>
                            <input class="form-control" type="number" id="price" name="price" required>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label><i class="fas fa-percent icon-style"></i> GST Option <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control select2 gst-select2" name="gst_option" id="gst_option"
                                        required>
                                        <option value="Without GST">Without GST</option>
                                        <option value="With GST">With GST</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6" id="product_gst_div" style="display: none;">
                                <div class="form-group">
                                    <label><i class="fas fa-file-invoice-dollar icon-style"></i> Product GST <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control select2 gst-select2" name="product_gst[]" id="product_gst"
                                        multiple>
                                        <option value="">Select GST</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div id="editsuccesstreat" class="alert alert-success" style="display:none;"></div>
                        <div id="editerrortreat" class="alert alert-danger" style="display:none;"></div>

                        <div class="m-t-20 text-center">
                            <button type="submit" class="btn btn-primary submit-btn">Update Treatment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function() {


            $('#editTreatmentForm').validate({
                rules: {
                    name: {
                        required: true,
                        minlength: 3
                    },
                    doctor_id: {
                        required: true
                    },
                    price: {
                        required: true
                    },
                    gst_option: {
                        required: true
                    },
                    'product_gst[]': {
                        required: function() {
                            return $('#gst_option').val() === 'With GST';
                        }
                    }
                },
                messages: {
                    name: {
                        required: "Please enter the treatment name",
                        minlength: "Treatment name must be at least 3 characters long"
                    },
                    doctor_id: {
                        required: "Please select a doctor"
                    },
                    price: {
                        required: "Please enter price"
                    },
                    gst_option: {
                        required: "Please select GST option"
                    },
                    'product_gst[]': {
                        required: "Please select at least one GST"
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

            $('#gst_option').select2({
                placeholder: "Select GST Option",
                width: '100%'
            });

            function ensureProductGstSelect2() {
                const $el = $('#product_gst');
                if (!$el.length) return;
                if ($el.hasClass('select2-hidden-accessible')) {
                    $el.select2('destroy');
                }
                $el.select2({
                    placeholder: "Select GST",
                    allowClear: true,
                    closeOnSelect: false,
                    width: '100%'
                });
            }

            $('#gst_option').on('change', function() {
                if ($(this).val() === 'With GST') {
                    $('#product_gst_div').show();
                    ensureProductGstSelect2(); // fix width when showing from hidden
                } else {
                    $('#product_gst_div').hide();
                    $('#product_gst').val(null).trigger('change');
                }
            });

            function fetchTaxRates(callback) {
                let branchId = localStorage.getItem('selectedBranchId');
                $.ajax({
                    url: '/api/tax-rates',
                    type: 'GET',
                    data: {
                        branch_id: branchId
                    },
                    headers: {
                        "Authorization": "Bearer " + token
                    },
                    success: function(data) {
                        let gstDropdown = $('#product_gst');
                        gstDropdown.empty().append('<option value="">Select GST</option>');

                        let taxRates = Array.isArray(data) ? data : (data.data || []);
                        window.taxRatesList = taxRates; // Store for mapping
                        $.each(taxRates, function(index, tax) {
                            if (tax.status === 'active') {
                                gstDropdown.append(
                                    `<option value="${tax.id}">${tax.tax_name} (${tax.tax_rate}%)</option>`
                                    );
                            }
                        });

                        ensureProductGstSelect2();

                        if (callback) callback();
                    },
                    error: function(xhr) {
                        console.error("Error loading tax rates:", xhr.responseText);
                    }
                });
            }

            $(document).ready(function() {
                // Get treatmentId from Blade or some other dynamic source
                let treatmentId = "{{ $treatment_id }}"


                if (!treatmentId || treatmentId === "") {
                    console.warn("No treatment ID provided.");
                    return;
                }

                fetchTaxRates(function() {
                    fetchTreatmentDetails(treatmentId);
                });

                function fetchTreatmentDetails(treatmentId) {
                    $.ajax({
                        url: `/api/treatments/${treatmentId}`,
                        type: 'GET',
                        headers: {
                            "Authorization": "Bearer " + token
                        },
                        success: function(data) {
                            if (data && data.name) {
                                $("#name").val(data.name);
                                $("#price").val(data.price);
                                $("#description").val(data.description);
                                $("#gst_option").val(data.gst_option || 'Without GST').trigger(
                                    'change');

                                if (data.gst_option === 'With GST' && data.product_gst) {
                                    let selectedGst = data.product_gst;
                                    if (typeof selectedGst === 'string') {
                                        try {
                                            selectedGst = JSON.parse(selectedGst);
                                        } catch (e) {}
                                    }
                                    if (Array.isArray(selectedGst)) {
                                        let ids = [];
                                        $.each(selectedGst, function(i, item) {
                                            let match = (window.taxRatesList || [])
                                                .find(t =>
                                                    t.tax_name === item.tax_name &&
                                                    parseFloat(t.tax_rate) ===
                                                    parseFloat(item.tax_rate)
                                                );
                                            if (match) ids.push(String(match.id));
                                        });
                                        $('#product_gst').val(ids).trigger('change');
                                    }
                                }

                                loadDoctors(data.doctor_id);
                            } else {
                                console.warn("Treatment data not found.");
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching treatment:", status, error);
                            console.log("Response:", xhr.responseText);
                        }
                    });
                }

                function loadDoctors(selectedDoctorId) {
                    let branchId = localStorage.getItem("selectedBranchId");
                    $.ajax({
                        url: "/api/doctors",
                        type: "GET",
                        dataType: "json",
                        data: {
                            branch_id: branchId // ✅ send branch_id
                        },
                        headers: {
                            "Authorization": "Bearer " + token
                        },
                        success: function(response) {
                            let doctorDropdown = $('.doctorSelect');

                            if (doctorDropdown.length === 0) {
                                console.error("Dropdown not found! Check your HTML.");
                                return;
                            }

                            doctorDropdown.empty().append(
                                '<option value="">Select Doctor</option>');

                            let doctorsList = response.doctors || [];

                            if (doctorsList.length === 0) {
                                console.warn("No doctors found.");
                                return;
                            }

                            $.each(doctorsList, function(index, doctor) {
                                // Capitalize first letter only
                                const capitalizedName = doctor.fullname.charAt(0)
                                    .toUpperCase() + doctor
                                    .fullname.slice(1);

                                doctorDropdown.append(
                                    `<option value="${doctor.id}">${capitalizedName}</option>`
                                );
                            });

                            // ✅ Initialize Select2 plugin
                            doctorDropdown.select2({
                                placeholder: "Select Doctor",
                                allowClear: true,
                                width: '100%'
                            });

                            // ✅ Set the selected doctor AFTER initializing Select2
                            if (selectedDoctorId) {
                                doctorDropdown.val(selectedDoctorId).trigger('change');
                            }

                            // Optional: Set search input placeholder
                            doctorDropdown.on('select2:open', function() {
                                $('.select2-search__field').attr('placeholder',
                                    'Search Doctor');
                            });
                        },
                        error: function(xhr) {
                            console.error("API Error:", xhr.status, xhr.responseText);
                        }
                    });
                }

            });


            // // Handle Update Submission
            $('#editTreatmentForm').submit(function(e) {
                let treatmentId = "{{ $treatment_id }}"
                e.preventDefault();
                let formData = {
                    name: $("#name").val(),
                    price: $("#price").val(),
                    doctor_id: $("#doctorSelect").val(),
                    description: $("#description").val(),
                    gst_option: $("#gst_option").val(),
                    product_gst: $("#product_gst").val()
                };

                $.ajax({
                    url: `/api/treatments/${treatmentId}`,
                    type: 'PUT',
                    contentType: 'application/json',
                    headers: {
                        "Authorization": "Bearer " + token
                    },
                    data: JSON.stringify(formData),
                    success: function(response) {
                        $("#editsuccesstreat").text("treatment updated successfully!").fadeIn()
                            .delay(700).fadeOut();
                        setTimeout(function() {
                            window.location.href = "{{ route('treatment.index') }}";
                        }, 2000);
                    },
                    error: function(xhr) {
                        var errorMessage = '';
                        if (xhr.status === 422) { // Validation error
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, messages) {
                                errorMessage += messages[0] + '<br>';
                            });
                        }
                        $('#editerrortreat').html(errorMessage).show();
                    }

                });
            });
        });
    </script>
@endsection
