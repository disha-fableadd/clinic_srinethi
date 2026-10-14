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
        color: black !impor        border: 1px solid #aaa !important;
           border: none;
        font-size: 14px;
        font-weight: normal !important;
        text-align: center;
        max-width: fit-content;

    }

    .radiology-btn {
        text-align: left !important;
    }

    .page-title {
        text-align: center !important;
        padding-left: 58px !important;
    }
    .form-container {
        width: 70% !important;
        padding-bottom: 60px !important;
    }


    @media screen and (max-width:767px) {
        .page-title {
            font-size: 20px !important;
            margin-top: 7px !important;
            padding-left: 0px !important;
        }

        .radiology-btn {
            text-align: right !important;
            padding-right: 15px !important;
        }
    }

    .radiology-btn {
        text-align: center;
    }

    .is-invalid {
        border: 2px solid #e74c3c !important;
        background-color: #fff6f6;
    }

    .error-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #e74c3c;
        font-size: 22px;
        pointer-events: none;
    }

    .input-wrapper {
        position: relative;
    }

    .invalid-feedback {
        display: block;
        color: #e74c3c;
        font-size: 0.95em;
        margin-top: 2px;
    }
</style>

@section('content')
    <div class="page-wrapper">
        <div class="content" style="height:100vh;">
            <div class="row" style="padding-top:15px">
                <div class="col-sm-8 col-6">
                    <h4 class="page-title" style="">Add Radiology Test</h4>
                </div>
                @if (app('hasPermission')(21, 'view'))
                    <div class="col-sm-4 col-6 radiology-btn">
                        <a href="{{ route('radiology-tests.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                            <i class="fa fa-arrow-left m-r-5"></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    </div>
                @endif
            </div>
            <div class="row">
                <div class="offset-lg-2">
                    <form class="form-container" id="radiology-form" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="branch_id" id="branch_id">
                        <div class="form-group">
                            <label><i class="fas fa-vial icon-style"></i> Test Name <span
                                                        class="text-danger">*</span></label>
                            <div class="input-wrapper">
                                <input class="form-control @error('test_name') is-invalid @enderror" type="text"
                                    name="test_name" value="{{ old('test_name') }}">
                            </div>
                            @error('test_name')
                                <span class="invalid-feedback"
                                    style="color:#e74c3c;font-size:0.95em;">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label><i class="fas fa-barcode icon-style"></i> Test Code <span
                                                        class="text-danger">*</span></label>
                                    <div class="input-wrapper">
                                        <input class="form-control @error('test_code') is-invalid @enderror" type="text"
                                            name="test_code" value="{{ old('test_code') }}">
                                    </div>
                                    @error('test_code')
                                        <span class="invalid-feedback"
                                            style="color:#e74c3c;font-size:0.95em;">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label><i class="fas fa-user-md icon-style"></i> Body Part <span
                                                        class="text-danger">*</span></label>
                                    <div class="input-wrapper">
                                        <input class="form-control @error('body_part') is-invalid @enderror" type="text"
                                            name="body_part" value="{{ old('body_part') }}">
                                    </div>
                                    @error('body_part')
                                        <span class="invalid-feedback"
                                            style="color:#e74c3c;font-size:0.95em;">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label><i class="fas fa-file-alt icon-style"></i> Report Format File Type</label>
                                    <select class="form-control select2 @error('report_format') is-invalid @enderror"
                                        name="report_format" id="report_format">
                                        <option value="">Select Format</option>
                                        <option value="dcm">DICOM (.dcm)</option>
                                        <option value="jpg">JPEG (.jpg)</option>
                                        <option value="png">PNG (.png)</option>
                                        <option value="tiff">TIFF (.tiff)</option>
                                        <option value="bmp">Bitmap (.bmp)</option>
                                        <option value="pdf">PDF (.pdf)</option>
                                        <option value="avi">Video (.avi)</option>
                                        <option value="mp4">Video (.mp4)</option>
                                        <option value="zip">ZIP (.zip)</option>
                                    </select>
                                    @error('report_format')
                                        <span class="invalid-feedback"
                                            style="color:#e74c3c;font-size:0.95em;">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label><i class="fas fa-dollar-sign icon-style"></i> Cost <span
                                                        class="text-danger">*</span></label>
                                    <div class="input-wrapper">
                                        <input class="form-control @error('cost') is-invalid @enderror" type="number"
                                            name="cost" value="{{ old('cost') }}" step="0.01">
                                    </div>
                                    @error('cost')
                                        <span class="invalid-feedback"
                                            style="color:#e74c3c;font-size:0.95em;">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label><i class="fas fa-percent icon-style"></i> GST Option</label>
                                    <select class="form-control select2" name="gst_option" id="gst_option">
                                        <option value="">Select GST Option</option>
                                        <option value="Without GST">Without GST</option>
                                        <option value="With GST">With GST</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6" id="product_gst_wrapper" style="display:none;">
                                <div class="form-group">
                                    <label><i class="fas fa-percentage icon-style"></i> Product GST</label>
                                    <select class="form-control select2" name="product_gst[]" id="product_gst" multiple>
                                        <option value="">Select GST</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                       


                        <!-- Alerts -->
                        <div id="radiotestsuccessMessage" class="alert alert-success" style="display:none;"></div>
                        <div id="radiotesterrorMessage" class="alert alert-danger" style="display:none;"></div>

                        <div class="m-t-20 text-center">
                            <button type="submit" class="btn btn-primary submit-btn">Create Radiology Test</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include in your <head> section -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Include before </body> -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Set branch_id from localStorage
            let branchId = localStorage.getItem('selectedBranchId');
            if (branchId) {
                $('#branch_id').val(branchId);
            }

            $('#report_format').select2({
                placeholder: 'Select Format',
                width: '100%'
            });

            $('#gst_option').select2({
                placeholder: 'Select GST Option',
                width: '100%'
            });

            $('#product_gst').select2({
                placeholder: 'Select GST',
                width: '100%'
            });

            // Toggle Product GST field visibility
            $('#gst_option').on('change', function() {
                if ($(this).val() === 'With GST') {
                    $('#product_gst_wrapper').show();
                    fetchTaxes();
                } else {
                    $('#product_gst_wrapper').hide();
                    $('#product_gst').val(null).trigger('change');
                }
            });

            function fetchTaxes() {
                let branchId = localStorage.getItem('selectedBranchId');
                $.ajax({
                    url: '/api/tax-rates',
                    type: 'GET',
                    data: {
                        branch_id: branchId
                    },
                    success: function(response) {
                        let gstSelect = $('#product_gst');
                        gstSelect.empty();
                        $.each(response, function(index, tax) {
                            if (tax.status === 'active') {
                                let taxValue = `${tax.tax_name} (${tax.tax_rate}%)`;
                                gstSelect.append(`<option value="${taxValue}">${taxValue}</option>`);
                            }
                        });
                        gstSelect.trigger('change');
                    },
                    error: function(xhr) {
                        console.error('Error fetching taxes:', xhr);
                    }
                });
            }

            $('#radiology-form').on('submit', function(e) {
                e.preventDefault();

                // Clear previous errors
                $('.invalid-feedback').hide();
                $('.is-invalid').removeClass('is-invalid');
                $('#radiotestsuccessMessage').hide().text('');
                $('#radiotesterrorMessage').hide().text('');

                let formData = {
                    branch_id: $('#branch_id').val(),
                    test_name: $('input[name="test_name"]').val(),
                    test_code: $('input[name="test_code"]').val(),
                    body_part: $('input[name="body_part"]').val(),
                    report_format: $('#report_format').val(),
                    cost: $('input[name="cost"]').val(),
                    gst_option: $('#gst_option').val(),
                    product_gst: $('#product_gst').val()
                };

                $.ajax({
                    url: '/api/radiology-tests',
                    type: 'POST',
                    data: JSON.stringify(formData),
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#radiotestsuccessMessage').text(response.message).show();
                            setTimeout(function() {
                                window.location.href = '/radiology-tests';
                            }, 2000);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, messages) {
                                let input = $('[name="' + key + '"]');
                                input.addClass('is-invalid');
                                // Find or create error message container
                                let errorSpan = input.closest('.form-group').find('.invalid-feedback');
                                if (errorSpan.length > 0) {
                                    errorSpan.text(messages[0]).show();
                                } else {
                                    input.after('<span class="invalid-feedback" style="color:#e74c3c;font-size:0.95em;display:block;">' + messages[0] + '</span>');
                                }
                            });
                        } else {
                            $('#radiotesterrorMessage').text('Something went wrong. Please try again.').show();
                        }
                    }
                });
            });
        });
    </script>
    @endsection
