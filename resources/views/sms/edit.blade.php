@extends('layout.app')
<style>
        .sms-template-btn{
            text-align: center !important;
        }

    @media screen and (max-width: 767px) {
        .sms-template-btn{
            text-align: right !important;
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
                <div class="col-sm-6 col-8">
                    <h4 class="page-title" style="text-align:center;padding-left: 230px;">Edit Template</h4>
                </div>

                <div class="col-sm-6 col-4  sms-template-btn  m-b-2">
                    <a href="{{ route('sms.template') }}" class="btn btn-primary btn-rounded btn-hdr " style="margin-left: -179px;">
                        <i class="fa fa-eye m-r-5 icon3"></i>
                        Sms Template
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="offset-lg-2">
                    <form class="form-container" style="width:60% ;padding-bottom: 60px;" id="updateTemplateForm">
                        <div class="form-group">
                            <label><i class="fas fa-sms icon-style"></i> Template Name</label>
                            <input class="form-control" type="text" name="name" id="name" required>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-align-left icon-style"></i> Content</label>
                            <textarea cols="30" rows="4" class="form-control" name="content" id="description"
                                style="border-radius:10px" required></textarea>
                        </div>


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

                        <div id="editsmssuccessMessage" class="alert alert-success" style="display:none;"></div>
                        <div id="editsmserrorMessage" class="alert alert-danger" style="display:none;"></div>
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
        $(document).ready(function () {
            // Get the template ID from the URL or pass it from the controller
            var templateId = "{{ request()->route('id') }}";

            // Fetch existing template details
            $.ajax({
                url: `/api/sms-template/${templateId}`,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    $('#name').val(data.name);
                    $('#description').val(data.content);
                    if (data.status === 'active') {
                        $('#active').prop('checked', true);
                    } else {
                        $('#inactive').prop('checked', true);
                    }
                },
                error: function () {
                    $('#editsmserrorMessage').text('Failed to fetch template details').show();
                }
            });

            // Handle form submission for update
            $('#updateTemplateForm').on('submit', function (e) {
                e.preventDefault();

                var formData = {
                    name: $('#name').val(),
                    content: $('#description').val(),
                    status: $('input[name="status"]:checked').val(),
                };

                $.ajax({
                    url: `/api/sms-template/${templateId}`,
                    type: 'PUT',
                    data: JSON.stringify(formData),
                    contentType: 'application/json',
                    success: function (response) {
                        $('#editsmssuccessMessage').text(response.success).show();
                        $('#editsmserrorMessage').hide();
                        setTimeout(function () {
                            window.location.href = "{{ route('sms.template') }}";
                        }, 1500);
                    },
                    error: function (xhr) {
                        var errorMsg = xhr.responseJSON.error || 'Failed to update template';
                        $('#editsmserrorMessage').text(errorMsg).show();
                        $('#editsmssuccessMessage').hide();
                    }
                });
            });
        });
    </script>

@endsection