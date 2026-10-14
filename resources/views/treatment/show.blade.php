@extends('layout.app')
<style>
    .treatment-button {
        padding-right: 0 !important;
        text-align: right !important;
    }
    .card-footer{
        background-color:#87ceb0 !important;
    }

    @media screen and (max-width: 767px) {


        .page-title {
            font-size: 19px !important;
        }

        .treatment-button {
            padding-right: 0 !important;
            text-align: center !important;
        }



    }
</style>
@section('content')
    <div class="page-wrapper" data-treatment-id="{{ $treatment_id }}">
        <div class="content" style="height:100vh">
            <div class="row " style="padding-top:15px">
                <div class="col-sm-6 col-8">
                    <h4 class="page-title" style="text-align:left;">
                        <i class="fa fa-cogs"></i> Treatments Details
                    </h4>
                </div>
                @if(app('hasPermission')(7, 'view'))
                    <div class="col-sm-6 col-4 treatment-button m-b-2">
                        <a href="{{ route('treatment.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                            <i class="fa fa-arrow-left m-r-5 icon3"></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    </div>
                @endif
            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-footer text-right">
                            <h3 style="float:left" class="text-dark">
                                <i class="fa fa-info-circle icon-style2"></i>
                                <span class="treatment_name"></span> Details
                            </h3>
                        </div>

                        <div class="card-body mt-3">
                            <p class="text-dark">
                                <strong><i class="fa fa-id-badge icon-style1"></i> Treatment Name: </strong>
                                <span class="treatment_name"></span>
                            </p>
                            <hr>

                            <p class="text-dark">
                                <strong><i class="fa fa-user icon-style1"></i>Doctor Name: </strong>
                                <span id="doctor_name"></span>
                            </p>
                            <hr>
                           
                            <p class="text-dark">
                                <strong><i class="fa fa-percent icon-style1"></i> GST Option: </strong>
                                <span id="gst_option"></span>
                            </p>
                            <hr>

                            <div id="product_gst_div" style="display: none;">
                                <p class="text-dark">
                                    <strong><i class="fa fa-file-invoice-dollar icon-style1"></i> Product GST: </strong>
                                    <span id="product_gst"></span>
                                </p>
                                <hr>
                                 <p class="text-dark">
                                <strong><i class="fas fa-clipboard-list icon-style1"></i> Price: </strong>
                                <span id="price"></span>
                            </p>
                            <hr>

                            </div>

                            <p class="text-dark">
                                <strong><i class="fas fa-clipboard-list icon-style1"></i> Description: </strong>
                                <span id="description"></span>
                            </p>
                            <hr>



                            <p class="text-dark">
                                <strong><i class="fa fa-calendar-plus icon-style1"></i> Created At: </strong>
                                <span id="treatment_created_at"></span>
                            </p>

                            <div class="button mb-4" style="display: flex; justify-content: end; margin: 0 5px;">
                                @if(app('hasPermission')(31, 'update'))
                                    <a href="#" class="btn btn-primary btn-rounded btn-hdr edit-treatment-btn"
                                        style="color:black; margin-right:10px">
                                        <i class="fa fa-pencil-alt"></i> <span class="hdr-btn-text">Edit</span>
                                    </a>
                                @endif
                                @if(app('hasPermission')(31, 'delete'))
                                    <button type="button" class="btn btn-danger btn-rounded btn-hdr delete-treatment"
                                        data-id="{{ $treatment_id }}">
                                        <i class="fa fa-trash"></i> <span class="hdr-btn-text">Delete</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="successMessage" class="alert alert-success" style="display:none;"></div>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
        </div>
    </div>

    <script src="{{ asset(env('IMAGE_PATH').'admin/assets/js/treatment-show.js') }}"></script>

    <style>
        .icon-style1 {
            background-color: white;
            color: rgb(157 195 179);
            padding: 5px;
            font-size: 20px;
            border-radius: 50%;
        }

        .icon-style2 {
            color: white;
            padding: 5px;
            font-size: 20px;
            border-radius: 50%;
        }
    </style>
@endsection
