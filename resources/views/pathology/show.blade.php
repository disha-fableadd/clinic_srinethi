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
    .card-footer{
        background-color:#87ceb0 !important;
    }
</style>

@section('content')
    <div class="page-wrapper">
        <div class="content" style="height:100vh">
            <div class="row" style="padding-top:15px">
                <div class=" col-8">
                    <h4 class="page-title"><i class="fa fa-flask"></i> Pathology Test Details</h4>
                </div>
                @if(app('hasPermission')(19, 'view'))
                    <div class=" col-4 text-right m-b-2">
                        <a href="{{ route('pathology.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                            <i class="fa fa-arrow-left"></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    </div>
                @endif
            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-footer text-right">
                            <h3 class="text-dark" style="float:left"><i class="fa fa-info-circle icon-style2"></i> <span
                                    class="test_name"></span> Details</h3>
                        </div>

                        <div class="card-body mt-3">
                            <p><strong><i class="fa fa-vial icon-style1"></i> Test Name: </strong> <span
                                    class="test_name"></span></p>
                            <hr>
                            <p><strong><i class="fa fa-barcode icon-style1"></i> Test Code: </strong> <span
                                    class="test_code"></span></p>
                            <hr>
                            <p><strong><i class="fa fa-sliders-h icon-style1"></i> Normal Range: </strong> <span
                                    class="normal_range"></span></p>
                            <hr>
                            <p><strong>₹ Cost: </strong> <span
                                    class="cost"></span></p>
                            <hr>
                            <p><strong><i class="fas fa-percent icon-style1"></i> GST Option: </strong> <span
                                    class="gst_option"></span></p>
                            <hr id="product_gst_hr" style="display: none;">
                            <p id="product_gst_p" style="display: none;"><strong><i class="fas fa-file-invoice-dollar icon-style1"></i> Product GST: </strong> <span
                                    class="product_gst"></span></p>
                            <hr id="product_gst_hr_after" style="display: none;">
                            <p><strong><i class="fa fa-flask icon-style1"></i> Sample Type: </strong> <span
                                    class="sample_type"></span></p>
                            <hr>
                            <p><strong><i class="fa fa-file icon-style1"></i> Report Format: </strong> <span
                                    class="report_format"></span></p>

                            <div class="button mt-4 mb-4" style="display: flex; justify-content: end;">
                                @if(app('hasPermission')(19, 'update'))
                                    <a href="#" class="btn btn-primary btn-rounded btn-hdr edit-pathology-btn"
                                        style="margin-right:10px; color:black">
                                        <i class="fa fa-pencil-alt"></i> <span class="hdr-btn-text">Edit</span>
                                    </a>
                                @endif
                                @if(app('hasPermission')(19, 'delete'))
                                    <button type="button" class="btn btn-danger btn-rounded btn-hdr delete-pathology"
                                        data-id="{{ $pathology_id }}">
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

    <script>
        $(document).ready(function () {
            var pathologyId = "{{ $pathology_id }}";

            $.ajax({
                url: '/api/pathology-tests/' + pathologyId,
                type: 'GET',
                success: function (response) {
                    $('.test_name').text(response.test_name);
                    $('.test_code').text(response.test_code);
                    $('.sample_type').text(response.sample_type);
                    $('.normal_range').text(response.normal_range);
                    $('.cost').text(response.cost);
                    $('.gst_option').text(response.gst_option || 'Without GST');
                    if (response.gst_option === 'With GST') {
                        $('.product_gst').text(response.product_gst);
                        $('#product_gst_p, #product_gst_hr, #product_gst_hr_after').show();
                    } else {
                        $('#product_gst_p, #product_gst_hr, #product_gst_hr_after').hide();
                    }
                    $('.report_format').text(response.report_format);

                    $('.edit-pathology-btn').attr('href', '/pathology/edit/' + response.id);
                },
                error: function () {
                    alert('Failed to fetch pathology test details.');
                }
            });

            $(document).on('click', '.delete-pathology', function () {
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This action cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/api/pathology-tests/' + id,
                            type: 'DELETE',
                            success: function (response) {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'Test deleted successfully!',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.href = "{{ route('pathology.index') }}";
                                });
                            },
                            error: function (xhr) {
                                let errorMsg = 'Failed to delete test.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMsg = xhr.responseJSON.message;
                                }
                                Swal.fire('Error', errorMsg, 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
