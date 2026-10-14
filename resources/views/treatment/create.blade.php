@extends('layout.app')

<style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice__display {
        cursor: default;
        padding-left: 12px !important;
        padding-right: 5px;
    }

    .treatment-title {
        padding-left: 95px !important;
        text-align: center !important;
    }

    .treatment-button {
        padding-right: 65px !important;
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



    @media screen and (max-width: 767px) {
        .treatment-title {
            padding-left: 0px !important;
            text-align: left !important;

        }

        .page-title {
            font-size: 19px !important;
        }

        .treatment-button {
            padding-right: 15px !important;
            text-align: right !important;
        }


    }
</style>

@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="row" style="padding-top:15px">
                <div class="col-sm-6 col-6">
                    <h4 class="page-title treatment-title">Add Treatment</h4>
                </div>
                @if (app('hasPermission')(7, 'view'))
                    <div class="col-sm-6 col-6 treatment-button m-b-2">
                        <a href="{{ route('treatment.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                            <i class="fa fa-arrow-left m-r-5 icon3"></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    </div>
                @endif
            </div>

            <div class="row">
                <div class="col-12">
                    <form id="createTreatmentForm" class="form-container">
                        @csrf
                        <input type="hidden" name="branch_id" id="branch_id">

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label><i class="fas fa-cogs icon-style"></i> Treatment Name</label>
                                    <input class="form-control" type="text" name="name" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <label class="mr-2"><i class="fas fa-medkit icon-style"></i> Doctor <span
                                                class="text-danger">*</span></label>
                                        @if (app('hasPermission')(3, 'create'))
                                            <a href="{{ route('user.create') }}" target="_blank"
                                                class="btn btn-primary btn-sm">
                                                <i class="fas fa-plus"></i> Add Doctor
                                            </a>
                                        @endif
                                    </div>
                                    <select class="form-control select2 doctorSelect" name="doctor_id" id="doctorSelect"
                                        required>
                                        <option value="">Select Doctor</option>
                                    </select>
                                </div>
                            </div>
                        </div>



                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label><i class="fas fa-money-bill-wave icon-style"></i> Price <span
                                            class="text-danger">*</span></label>
                                    <input class="form-control" type="number" name="price" id="price" required>
                                </div>
                            </div>
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

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label><i class="fas fa-clipboard-list icon-style"></i> Description <span
                                            class="text-danger">*</span></label>
                                    <textarea cols="7" rows="4" class="form-control" name="description" style="border-radius:10px"></textarea>
                                </div>
                            </div>

                        </div>
                        <div id="treatmentsuccessMessage" class="alert alert-success" style="display:none;"></div>
                        <div id="treatmenterrorMessage" class="alert alert-danger" style="display:none;"></div>

                        <div class="m-t-20 text-center">
                            <button type="submit" class="btn btn-primary submit-btn">Create Treatment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Include jQuery Validation Plugin -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>

    {{-- <script src="{{ asset('admin/js/treatment-create.js') }}"></script> --}}
    <script src="{{ asset(env('IMAGE_PATH') . 'admin/assets/js/treatment-create.js') }}"></script>
@endsection
