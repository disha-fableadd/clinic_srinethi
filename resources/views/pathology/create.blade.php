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

    .select2-container--default .select2-selection--single {
        border-radius: 50px !important;
        height: 40px !important;
        border-color: rgb(207, 236, 224) !important;
        padding-top: 4px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        text-align: left !important;
        padding-left: 12px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px !important;
        top: 1px !important;
        right: 10px !important;
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




    .pathology-title {
        padding-left: 145px !important;
        text-align: center !important;
    }

    .pathology-button {
        padding-right: 8px !important;
        text-align: center !important;
    }
    .form-container {
        width: 60% !important;
        padding-bottom: 60px !important;
    }


    @media screen and (max-width:768px) {
        .pathology-title {
            padding-left: 15px !important;
            text-align: left !important;
        }

        .pathology-button {
            padding-right: 15px !important;
            text-align: right !important;
        }

    }

    @media screen and (max-width:767px) {
        .pathology-title {
            padding-left: 15px !important;
            text-align: left !important;
        }

        .pathology-button {
            padding-right: 15px !important;
            text-align: right !important;
        }

        .pathology-form {
            height: 720px !important;
        }

    }
</style>

@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="row" style="padding-top:15px">
                <div class=" col-6">
                    <h4 class="page-title  pathology-title">Add Pathology Test</h4>
                </div>
                @if (app('hasPermission')(19, 'view'))
                    <div class=" col-6 m-b-2 eye-btn pathology-button">
                        <a href="{{ route('pathology.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                            <i class="fa fa-arrow-left m-r-5 icon3  "></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    </div>
                @endif
            </div>




            <div class="row mt-4">
                <div class="col-12">
                    <form id="pathologyForm" method="POST" class="form-container all-form pathology-form">

                        @csrf

                        <div class="row">
                            <input type="hidden" name="branch_id" id="branch_id">
                            <!-- Test Name -->
                            <div class="col-6">
                                <div class="form-group">
                                    <label><i class="fas fa-vial icon-style"></i> Test Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="test_name" class="form-control" placeholder="e.g. CBC"
                                        required>
                                </div>
                            </div>

                            <!-- Test Code -->
                            <div class="col-6">
                                <div class="form-group">
                                    <label><i class="fas fa-barcode icon-style"></i> Test Code <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="test_code" class="form-control" placeholder="e.g. CBC-101"
                                        required>
                                </div>
                            </div>



                            <!-- Normal Range -->
                            <div class="col-6">
                                <div class="form-group">
                                    <label><i class="fas fa-sliders-h icon-style"></i> Normal Range <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="normal_range" class="form-control"
                                        placeholder="e.g. 4.5 - 11.0" required>
                                </div>
                            </div>

                            <!-- Cost -->
                            <div class="col-6">
                                <div class="form-group">
                                    <label>₹ Cost <span
                                            class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="cost" class="form-control"
                                        placeholder="e.g. 250" required>
                                </div>
                            </div>

                            <!-- GST Option -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-percent icon-style"></i> GST Option <span
                                            class="text-danger">*</span></label>
                                    <select name="gst_option" id="gst_option" class="form-control select2" required>
                                        <option value="Without GST">Without GST</option>
                                        <option value="With GST">With GST</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Product GST -->
                            <div class="col-md-6" id="product_gst_div" style="display: none;">
                                <div class="form-group">
                                    <label><i class="fas fa-file-invoice-dollar icon-style"></i> Product GST <span
                                            class="text-danger">*</span></label>
                                    <select name="product_gst[]" id="product_gst" class="form-control select2" multiple>
                                        <option value="">Select GST</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Sample Type -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-flask icon-style"></i> Sample Type <span
                                            class="text-danger">*</span></label>
                                    <select name="sample_type" class="form-control select2" required>
                                        <option value="">Select Sample Type</option>
                                        <option value="Blood">Blood</option>
                                        <option value="Urine">Urine</option>
                                        <option value="Saliva">Saliva</option>
                                        <option value="Stool">Stool</option>
                                        <option value="Tissue">Tissue</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Report Format -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-file-pdf icon-style"></i> Report Format</label>
                                    <select name="report_format" class="form-control select2">
                                        <option value="PDF" selected>PDF</option>
                                        <option value="Excel">Excel</option>
                                        <option value="Image">Image</option>
                                    </select>
                                </div>
                            </div>

                        </div>
                        <div id="pathologysuccessMessage" class="alert alert-success" style="display:none;"></div>
                        {{-- <div id="pathologyerrorMessage" class="alert alert-danger" style="display:none;"></div> --}}
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
            // Initialize Select2
            $('.select2').select2({
                width: '100%'
            });

            fetchTaxRates();

            $('#gst_option').on('change', function() {
                if ($(this).val() === 'With GST') {
                    $('#product_gst_div').show();
                    $('#product_gst').attr('required', true);
                } else {
                    $('#product_gst_div').hide();
                    $('#product_gst').attr('required', false).val(null).trigger('change');
                }
            });

            function fetchTaxRates() {
                let branchId = localStorage.getItem('selectedBranchId');
                $.ajax({
                    url: '/api/tax-rates',
                    type: 'GET',
                    data: {
                        branch_id: branchId
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    success: function(data) {
                        let gstDropdown = $('#product_gst');
                        gstDropdown.empty().append('<option value="">Select GST</option>');
                        data.forEach(function(tax) {
                            if (tax.status === 'active') {
                                gstDropdown.append(
                                    `<option value="${tax.tax_name} (${tax.tax_rate}%)">${tax.tax_name} (${tax.tax_rate}%)</option>`
                                );
                            }
                        });
                    },
                    error: function(xhr) {
                        console.error("Error loading tax rates:", xhr.responseText);
                    }
                });
            }

            var form = $('#pathologyForm');

            form.validate({
                rules: {
                    test_name: "required",
                    test_code: "required",
                    sample_type: "required",
                    normal_range: "required",
                    cost: {
                        required: true,
                        number: true,
                        min: 0
                    },
                    gst_option: "required",
                    "product_gst[]": {
                        required: function() {
                            return $("#gst_option").val() === "With GST";
                        }
                    },
                    report_format: "required"
                },
                messages: {
                    test_name: "Please enter the test name",
                    test_code: "Please enter the test code",
                    sample_type: "Please select a sample type",
                    normal_range: "Please enter the normal range",
                    cost: {
                        required: "Please enter the cost",
                        number: "Please enter a valid number",
                        min: "Cost cannot be negative"
                    },
                    gst_option: "Please select a GST option",
                    "product_gst[]": "Please select a product GST",
                    report_format: "Please select a report format"
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

            form.on('submit', function(e) {
                e.preventDefault();

                if (!form.valid()) {
                    return;
                }

                // Clear previous messages
                $('#pathologysuccessMessage').hide().text('');
                $('#pathologyerrorMessage').hide().text('');

                let formData = {
                    test_name: $("input[name='test_name']").val(),
                    test_code: $("input[name='test_code']").val(),
                    sample_type: $("select[name='sample_type']").val(),
                    normal_range: $("input[name='normal_range']").val(),
                    cost: $("input[name='cost']").val(),
                    gst_option: $("select[name='gst_option']").val(),
                    product_gst: $("#product_gst").val(),
                    report_format: $("select[name='report_format']").val(),
                    branch_id: $("input[name='branch_id']").val(),
                };

                $.ajax({
                    url: '/api/pathology-tests',
                    type: 'POST',
                    data: JSON.stringify(formData),
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        $('#pathologysuccessMessage').text(response.message).fadeIn();

                        // Reset the form
                        form[0].reset();
                        $('.select2').val(null).trigger('change');

                        setTimeout(function() {
                            window.location.href = "{{ route('pathology.index') }}";
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
                        $('#pathologyerrorMessage').html(errorMsg).fadeIn();

                        setTimeout(() => $('#pathologyerrorMessage').fadeOut(), 5000);
                    }
                });
            });
        });
    </script>
@endsection

