@extends('layout.app')

<style>
    .service-title {
        text-align: center !important;
        padding-left: 75px !important;
    }
    .supplier-btn{
         text-align: center;
        padding-right: 7px !important;
    }
    .form-container {
        width: 60% !important;
        padding-bottom: 60px !important;
    }


     @media screen and (max-width: 767px) {
        .page-title {
            font-size: 19px !important;
            padding-left: 10px !important;
              padding-top: 6px !important;
             text-align: left !important;
        }
         .supplier-btn{
         text-align: right;
        padding-right: 15px !important;
    }
    }

</style>
@section('content')

    <div class="page-wrapper">
        <div class="content">
            <div class="row" style="padding-top:15px">
                <div class="col-6">
                    <h4 class="page-title service-title ">Edit Supplier</h4>
                </div>
                @if(app('hasPermission')(12, 'view'))
                    <div class="col-6 supplier-btn" >
                        <a href="{{ route('supplier.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                            <i class="fa fa-arrow-left m-r-5 icon3  "></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    </div>
                @endif
            </div>
            <div class="row">
                <div class="col-12">
                    <form class="form-container" id="editSupplierForm" method="" action="">
                        @csrf
                        <!-- Step 1 -->
                        <div class="form-step" id="step-1">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label><i class="fa fa-hospital-o icon-style"></i> Company Name <span
                                                        class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="company_name" name="name">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label><i class="fas fa-user icon-style"></i> Contact_Person <span
                                                        class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="contact_person" name="contact_person">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label><i class="fas fa-phone icon-style"></i> Phone <span
                                                        class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="phone" name="phone">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label><i class="fas fa-envelope icon-style"></i> Email <span
                                                        class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="email" name="email">
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label><i class="fas fa-map-marker-alt icon-style"></i> Address <span
                                                        class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="address" name="address">
                                    </div>
                                </div>
                            </div>

                            <div id="editsuppliersuccessMessage" class="alert alert-success" style="display:none;"></div>
                            <div id="editsuppliererrorMessage" class="alert alert-danger" style="display:none;"></div>

                            <div class="m-t-20 text-center">
                                <button class="btn btn-primary submit-btn"> Update Supplier Details</button>
                            </div>
                        </div>


                    </form>


                </div>
            </div>
        </div>
    </div>






    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>



    <script>var supplierId = "{{ $supplier_id }}";</script>
    <script src="{{ asset(env('IMAGE_PATH').'admin/assets/js/supplier-edit.js') }}"></script>

@endsection