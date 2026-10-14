@extends('layout.app')


<style>
    .sms-title {
        /* text-align: center; */
        margin-left: -179px;
    }

    .sms-float {
        text-align: center;
        padding-left: 83px !important;
    }


    @media screen and (max-width: 767px) {
        .sms-title {

            margin-left: 0 !important;
        }

        .sms-float {
            text-align: right;
            padding-left: 0px !important;
        }

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
        <div class="content" style="height:100vh;">
            <div class="row" style="padding-top:15px">
                <div class="col-sm-6 col-6">
                    <h4 class="page-title template-create" style="text-align:center;padding-left: 230px;">Create Template
                    </h4>
                </div>

                <div class="col-sm-6 col-6 sms-float  m-b-2">
                    <a href="{{ route('sms.template') }}" class="btn btn-primary btn-rounded btn-hdr sms-title" style="">
                        <i class="fa fa-arrow-left m-r-5 icon3"></i> <span class="hdr-btn-text">Back</span>
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="offset-lg-2">
                    <form class="form-container" style="width:60% ;padding-bottom: 60px;" id="createServiceForm">
                        <div class="form-group">
                            <label><i class="fas fa-sms icon-style"></i> Template </label>
                            <input class="form-control" type="text" name="name" id="name" required>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-align-left icon-style"></i> Content</label>
                            <textarea cols="30" rows="4" class="form-control" name="content" id="description"
                                style="border-radius:10px" required></textarea>
                        </div>

                            <input type="hidden" name="branch_id" id="branch_id">
                        <div class="form-group">
                            <label class="display-block"><i class="fas fa-check-circle icon-style"></i> <span class="hdr-btn-text">Status</span></label>
                            <div class="form-control">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="active" value="active"
                                        checked>
                                    <label class="form-check-label" for="active">
                                        Active
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="inactive"
                                        value="inactive">
                                    <label class="form-check-label" for="inactive">
                                        Inactive
                                    </label>
                                </div>

                            </div>
                        </div>

                        <div id="smssuccessMessage" class="alert alert-success" style="display:none;"></div>
                        <div id="smserrorMessage" class="alert alert-danger" style="display:none;"></div>
                        <div class="m-t-20 text-center">
                            <button type="submit" class="btn btn-primary submit-btn">Create Template</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
    let storedBranchId = localStorage.getItem('selectedBranchId');
    if (storedBranchId) {
        document.getElementById('branch_id').value = storedBranchId;
    }
});
        $(document).ready(function () {

            $('#createServiceForm').on('submit', function (e) {
                e.preventDefault();
              
                $.ajax({
                    type: 'POST',
                    url: '/api/sms-template',
                    data: {
                        name: $('#name').val(),
                        content: $('#description').val(),
                        status: $('input[name="status"]:checked').val(),
                         branch_id: $('#branch_id').val()
                    },
                    headers: { "Authorization": "Bearer " + token },
                    success: function (response) {
                        $('#smssuccessMessage').text(response.success).show();
                        $('#smserrorMessage').hide();
                        $('#createServiceForm')[0].reset();
                        setTimeout(function () {
                            window.location.href = "{{ route('sms.template') }}";
                        }, 1500);
                    },
                    error: function (xhr) {
                        $('#smserrorMessage').text('An error occurred. Please try again.').show();
                        $('#smssuccessMessage').hide();
                    }
                });
            });
        });
    </script>












@endsection