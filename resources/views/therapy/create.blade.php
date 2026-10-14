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




    .therapy-title {
        padding-left: 145px !important;
        text-align: center !important;
    }

    .therapy-button {
        padding-right: 8px !important;
        text-align: center !important;
    }

    .form-container {
        width: 60% !important;
        padding-bottom: 60px !important;
    }


    @media screen and (max-width:768px) {
        .therapy-title {
            padding-left: 15px !important;
            text-align: left !important;
        }

        .therapy-button {
            padding-right: 15px !important;
            text-align: right !important;
        }

    }

    @media screen and (max-width:767px) {
        .therapy-title {
            padding-left: 15px !important;
            text-align: left !important;
        }

        .therapy-button {
            padding-right: 15px !important;
            text-align: right !important;
        }

        .therapy-form {
            height: 720px !important;
        }

    }
</style>

@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="row" style="padding-top:15px">
                <div class=" col-6">
                    <h4 class="page-title  therapy-title">Add Therapy</h4>
                </div>
                @if (app('hasPermission')(26, 'view'))
                    <div class=" col-6 m-b-2 eye-btn therapy-button">
                        <a href="{{ route('therapy.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                            <i class="fa fa-arrow-left m-r-5 icon3  "></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    </div>
                @endif
            </div>




            <div class="row ">
                <div class="col-12">
                    <form id="therapyForm" method="POST" class="form-container all-form therapy-form">

                        @csrf
                        <input type="hidden" name="branch_id" id="branch_id">
                        <div class="row">

                            <!-- Test Name -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-vial icon-style"></i> Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" placeholder="Therapy name"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-sliders-h icon-style"></i> Duration (minutes) <span
                                            class="text-danger">*</span></label>
                                    <input type="number" name="duration_minutes" class="form-control" placeholder="e.g. 30"
                                        required>
                                </div>
                            </div>

                            <!-- Test Code -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label><i class="fas fa-barcode icon-style"></i> Description <span
                                            class="text-danger">*</span></label>
                                    <textarea name="description" class="form-control" rows="3" placeholder="Describe the therapy"
                                        style="border-radius: 20px;"></textarea>
                                </div>
                            </div>



                            <!-- Normal Range -->


                            <!-- Cost -->
                            <div class="col-md-6">
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
                                        <option value="">Select GST Option</option>
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
                                    <label><i class="fas fa-flask icon-style"></i> Status <span
                                            class="text-danger">*</span></label>
                                    <select name="status" class="form-control select2" required>
                                        <option value="">Select status</option>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>

                                    </select>
                                </div>
                            </div>


                        </div>
                        <div id="therapysuccessMessage" class="alert alert-success" style="display:none;"></div>
                        {{-- <div id="therapyerrorMessage" class="alert alert-danger" style="display:none;"></div> --}}
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





    <script src="{{ asset(env('IMAGE_PATH') . 'admin/assets/js/therapy-create.js') }}"></script>
@endsection

