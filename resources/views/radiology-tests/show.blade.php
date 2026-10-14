@extends('layout.app')
<style>
    .container {
        width: 60%;
        margin: auto;
    }

    .header1 {
        background: #cfece0;
        color: black;
        padding: 10px 15px;
        font-size: 18px;
        font-weight: bold;
        margin-top: 50px;
        border-radius: 15px;
    }

    .card-body {
        height: auto;
        padding: 20px !important;
    }

    .card {
        margin: 0;
    }

    .info-label {
        font-weight: bold;
        color: #333;
    }

    .info-value {
        color: #555;
    }
    .card-footer{
        background-color:#87ceb0 !important;
    }
</style>
@section('content')
    <div class="page-wrapper">
        <div class="content" style="height:100vh">
            <div class="row mt-3">
                <div class="col-sm-8 col-8">
                    <h4 class="page-title" style="text-align:left;">
                        Radiology Test Details
                    </h4>
                </div>
                @if(app('hasPermission')(21, 'view'))
                    <div class="col-sm-4 col-4 text-right m-b-2">
                        <a href="{{ route('radiology-tests.index') }}" class="btn btn-primary btn-rounded btn-hdr">
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
                                <span>Test Information</span>
                            </h3>
                        </div>


                        <div class="card-body mt-3">





                            <div class="row">


                                <div class="col-lg-12 ">

                                    <p class="text-dark margin-text">
                                        <strong><i class="fas fa-user-injured icon-style1"></i> Test Name: </strong>
                                        <span id="testName"></span>
                                    </p>
                                    <hr class="margin-text">

                                    <p class="text-dark margin-text">
                                        <strong><i class="fas fa-user-md icon-style1"></i> Test Code: </strong>
                                        <span id="testCode"></span>
                                    </p>
                                    <hr class="margin-text">

                                    <p class="text-dark margin-text">
                                        <strong><i class="fas fa-notes-medical icon-style1"></i> Body Part: </strong>
                                        <span id="bodyPart"></span>
                                    </p>
                                    <hr class="margin-text">

                                    <p class="text-dark margin-text">
                                        <strong><i class="fas fa-calendar-alt icon-style1"></i> Cost: </strong>
                                        <span id="cost"></span>
                                    </p>
                                    <hr class="margin-text">

                                    <div id="gst_details" style="display: none;">
                                        <p class="text-dark margin-text">
                                            <strong><i class="fas fa-percent icon-style1"></i> GST Option: </strong>
                                            <span id="gstOption"></span>
                                        </p>
                                        <hr class="margin-text">

                                        <p class="text-dark margin-text" id="product_gst_row" style="display: none;">
                                            <strong><i class="fas fa-percentage icon-style1"></i> Product GST: </strong>
                                            <span id="productGst"></span>
                                        </p>
                                        <hr class="margin-text" id="product_gst_hr" style="display: none;">
                                    </div>

                                    <p class="text-dark margin-text">
                                        <strong><i class="fas fa-check-circle icon-style1"></i> Report Format:</strong>
                                        <span id="reportFormat"></span>
                                    </p>
                                </div>

                            </div>
                            <div class="button mb-4" style="display: flex; justify-content: end; margin: 0 5px;">
                                @if(app('hasPermission')(21, 'update'))
                                    <a href="#" class="btn btn-primary btn-rounded btn-hdr edit-test-btn"
                                        style="color:black; margin-right:10px">
                                        <i class="fa fa-pencil-alt"></i> <span class="hdr-btn-text">Edit</span>
                                    </a>
                                @endif
                                @if(app('hasPermission')(21, 'delete'))
                                    <button type="button" class="btn btn-danger btn-rounded btn-hdr delete-test">
                                        <i class="fa fa-trash"></i> <span class="hdr-btn-text">Delete</span>
                                    </button>

                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function getIdFromUrl() {
            const segments = window.location.pathname.split('/');
            return segments.pop() || segments.pop();
        }
        const formatLabels = {
            'dcm': 'DICOM (.dcm)',
            'jpg': 'JPEG (.jpg)',
            'jpeg': 'JPEG (.jpeg)',
            'png': 'PNG (.png)',
            'tiff': 'TIFF (.tiff)',
            'bmp': 'Bitmap (.bmp)',
            'pdf': 'PDF (.pdf)',
            'doc': 'Word (.doc)',
            'docx': 'Word (.docx)',
            'webp': 'WEBP (.webp)',
            'avi': 'Video (.avi)',
            'mp4': 'Video (.mp4)',
            'zip': 'ZIP (.zip)'
        };

        function renderTestData(test) {
            $('#testName').text(test.test_name || 'N/A');
            $('#testCode').text(test.test_code || 'N/A');
            $('#bodyPart').text(test.body_part || 'N/A');
            $('#cost').text(test.cost !== null && test.cost !== undefined && test.cost !== '' ? test.cost : 'N/A');

            // Handle GST Fields
            if (test.gst_option) {
                $('#gst_details').show();
                // Normalize for display check
                let normalizedGstOption = test.gst_option.toLowerCase().indexOf('with') !== -1 && test.gst_option.toLowerCase().indexOf('without') === -1 ? 'With GST' : 'Without GST';
                $('#gstOption').text(normalizedGstOption);

                if (normalizedGstOption === 'With GST' && test.product_gst) {
                    $('#product_gst_row').show();
                    $('#product_gst_hr').show();
                    // If it's an array join it, if string just show it
                    let gstText = Array.isArray(test.product_gst) ? test.product_gst.join(', ') : test.product_gst;
                    $('#productGst').text(gstText);
                } else {
                    $('#product_gst_row').hide();
                    $('#product_gst_hr').hide();
                }
            } else {
                $('#gst_details').hide();
            }

            $(".edit-test-btn").attr("href", "/radiology-tests/edit/" + test.id);
            const formatted = formatLabels[test.report_format] || 'N/A';
            $('#reportFormat').text(formatted);
        }

        function showNA() {
            $('#testName').text('N/A');
            $('#testCode').text('N/A');
            $('#bodyPart').text('N/A');
            $('#cost').text('N/A');
            $('#gst_details').hide();
            $('#reportFormat').text('N/A');
        }

        $(document).ready(function () {
            const id = getIdFromUrl();
            $.ajax({
                url: `/api/radiology-tests/${id}`,
                method: 'GET',
                dataType: 'json',
                success: function (data) {
                    if (!data || $.isEmptyObject(data)) {
                        showNA();
                    } else {
                        renderTestData(data);
                    }
                },
                error: function () {
                    showNA();
                }
            });
        });
        $(document).on('click', '.delete-test', function () {
            var testId = getIdFromUrl(); // get ID from URL

            if (!testId) {
                Swal.fire('Error', 'Test ID is missing!', 'error');
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: "You want to delete this radiology test?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/api/radiology-tests/' + testId,
                        type: 'DELETE',
                        success: function (response) {
                            Swal.fire('Deleted!', 'Radiology test deleted successfully!', 'success')
                                .then(() => {
                                    window.location.href = '/radiology-tests';
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
    </script>

@endsection