@extends('layout.app')

<style>
    .service-title {
        text-align: center;
        /* padding-left: 17px; */
    }
    .card-footer{
        background-color:#87ceb0 !important;
    }

     @media screen and (max-width: 767px) {
        .page-title {
            font-size: 19px !important;
            padding-left: 10px !important;
             text-align: left !important;
             padding-top: 6px !important;
        }
    }

</style>

@section('content')
    <div class="page-wrapper">
        <div class="content" style="height:100vh">
            <div class="row" style="padding-top:15px">
                <div class="col-sm-7 col-8">
                    <h4 class="page-title service-title " >
                        <i class="fa fa-cogs"></i> Supplier Details
                    </h4>
                </div>
                @if(app('hasPermission')(12, 'view'))
                    <div class="col-sm-5 col-4 text-right m-b-2">
                        <a href="{{ route('supplier.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                            <i class="fa fa-arrow-left"></i> <span class="hdr-btn-text">Back</span>
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
                                <span class="company_name"></span> Details
                            </h3>
                        </div>

                        <div class="card-body mt-3">
                            <p class="text-dark">
                                <strong><i class="fa fa-id-badge icon-style1"></i> Company Name: </strong>
                                <span class="company_name"></span>
                            </p>
                            <hr>

                            <p class="text-dark">
                                <strong><i class="fa fa-user icon-style1"></i> Contact Person: </strong>
                                <span id="contact_person"></span>
                            </p>
                            <hr>

                            <p class="text-dark">
                                <strong><i class="fa fa-phone icon-style1"></i> Phone: </strong>
                                <span id="supplier_phone"></span>
                            </p>
                            <hr>

                            <p class="text-dark">
                                <strong><i class="fa fa-envelope icon-style1"></i> Email: </strong>
                                <span id="supplier_email"></span>
                            </p>
                            <hr>

                            <p class="text-dark">
                                <strong><i class="fa fa-map-marker-alt icon-style1"></i> Address: </strong>
                                <span id="supplier_address"></span>
                            </p>
                            <hr>

                            <p class="text-dark">
                                <strong><i class="fa fa-calendar-plus icon-style1"></i> Created At: </strong>
                                <span id="supplier_created_at"></span>
                            </p>

                            <div class="button mb-4" style="display: flex; justify-content: end; margin: 0 5px;">
                                @if(app('hasPermission')(12, 'update'))
                                    <a href="#" class="btn btn-primary btn-rounded btn-hdr edit-supplier-btn"
                                        style="color:black; margin-right:10px">
                                        <i class="fa fa-pencil-alt"></i> <span class="hdr-btn-text">Edit</span>
                                    </a>
                                @endif
                                @if(app('hasPermission')(12, 'delete'))
                                    <button type="button" class="btn btn-danger btn-rounded btn-hdr delete-supplier"
                                        data-id="{{ $supplier_id }}">
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

    <script>var supplierId = "{{ $supplier_id }}";</script>
    <script src="{{ asset(env('IMAGE_PATH').'admin/assets/js/supplier-show.js') }}"></script>

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