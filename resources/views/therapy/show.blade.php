@extends('layout.app')

<style>
    @media screen and (max-width:767px) {
        .page-title {
            font-size: 20px !important;
        }
    }

    .icon-style1 {
        background-color: white;
        color: #9dc3b3;
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

    .card-footer {
        background-color: #87ceb0 !important;
    }
</style>

@section('content')
    <div class="page-wrapper">
        <div class="content" style="height:100vh">
            <div class="row" style="padding-top:15px">
                <div class=" col-8">
                    <h4 class="page-title"><i class="fa fa-flask"></i> Terapy Details</h4>
                </div>
                @if (app('hasPermission')(26, 'view'))
                    <div class=" col-4 text-right m-b-2">
                        <a href="{{ route('therapy.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                            <i class="fa fa-arrow-left"></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    </div>
                @endif
            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-footer text-right">
                            <h3 class="text-dark" style="float:left">
                                <i class="fa fa-info-circle icon-style2"></i> <span class="therapy_name"></span> Details
                            </h3>
                        </div>

                        <div class="card-body mt-3">
                            <p><strong><i class="fa fa-notes-medical icon-style1"></i> Name: </strong> <span
                                    class="therapy_name"></span></p>
                            <hr>
                            <p><strong><i class="fa fa-align-left icon-style1"></i> Description: </strong> <span
                                    class="description"></span></p>
                            <hr>
                            <p><strong><i class="fa fa-clock icon-style1"></i> Duration (mins): </strong> <span
                                    class="duration_minutes"></span></p>
                            <hr>
                            <p><strong>₹ Cost: </strong> <span
                                    class="cost"></span></p>
                            <hr>
                            <p><strong><i class="fa fa-percent icon-style1"></i> GST Option: </strong> <span
                                    class="gst_option"></span></p>
                            <hr id="product_gst_hr" style="display: none;">
                            <p id="product_gst_p" style="display: none;"><strong><i
                                        class="fa fa-file-invoice-dollar icon-style1"></i> Product GST: </strong> <span
                                    class="product_gst"></span></p>
                            <hr>
                            <p><strong><i class="fa fa-toggle-on icon-style1"></i> Status: </strong> <span
                                    class="therapy_status"></span></p>

                            <div class="button mt-4 mb-4" style="display: flex; justify-content: end;">
                                @if (app('hasPermission')(19, 'update'))
                                    <a href="#" class="btn btn-primary btn-rounded btn-hdr edit-therapy-btn"
                                        style="margin-right:10px; color:black">
                                        <i class="fa fa-pencil-alt"></i> <span class="hdr-btn-text">Edit</span>
                                    </a>
                                @endif
                                @if (app('hasPermission')(19, 'delete'))
                                    <button type="button" class="btn btn-danger btn-rounded btn-hdr delete-therapy"
                                        data-id="{{ $therapy_id }}">
                                        <i class="fa fa-trash"></i> <span class="hdr-btn-text">Delete</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div id="successMessage" class="alert alert-success" style="display:none;"></div>
        </div>
    </div>

    <script src="{{ asset(env('IMAGE_PATH') . 'admin/assets/js/therapy-show.js') }}"></script>
@endsection

