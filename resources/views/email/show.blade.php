@extends('layout.app')
<style>
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
                <div class=" col-6">
                    <h4 class="page-title " style="text-align:left;">View Template</h4>
                </div>

                <div class=" col-6 text-right m-b-2">
                    <a href="{{ route('email.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                        <i class="fa fa-eye m-r-5 icon3"></i> Email Template
                    </a>
                </div>

            </div>

            <div class="row mt-5">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-footer text-right" style="background-color:#87ceb0">
                            <h3 style="float:left" class="text-dark">
                                <i class="fa fa-info-circle icon-style2"></i>
                                <span class="">Email Template Details</span>
                            </h3>
                        </div>

                        <div class="card-body mt-3">
                            <div class="row">
                                <div class="col-12">
                                    <p class="text-dark">
                                        <strong><i class="fa fa-file-alt icon-style1"></i> Template Name: </strong>
                                        <span id="template"></span>
                                    </p>
                                    <hr>
                                    <p class="text-dark">
                                        <strong><i class="fa fa-toggle-on icon-style1"></i> Template Status: </strong>
                                        <span id="status"></span>
                                    </p>
                                    <hr>
                                    <p class="text-dark">
                                        <strong><i class="fa fa-file-image icon-style1"></i> Template: </strong><br>
                                    <div id="default_email_template"
                                        style="border: 1px solid #ddd; padding: 10px; border-radius: 10px;"></div>
                                    </p>



                                </div>
                            </div>
                            <div class="button mb-4" style="display: flex; justify-content: end; margin: 0 5px;">
                                <a href="#" class="btn btn-primary btn-rounded btn-hdr edit-user-btn"
                                    style="color:black; margin-right:10px">
                                    <i class="fa fa-pencil-alt"></i> Edit Report
                                </a>
                                <button type="button" class="btn btn-danger btn-rounded btn-hdr delete-user"
                                    data-id="${template.id}">
                                    <i class="fa fa-trash"></i> Delete Report
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div id="successMessage" class="alert alert-success" style="display:none;"></div>


        </div>
    </div>




    <script>

        // fetch template

        $(document).ready(function () {
            var templateId = "{{ $emailtemplate_id }}"; // Get ID from route parameter

            // Fetch template data using API
            $.ajax({
                url: "/api/email-templates/" + templateId,
                type: "GET",
                success: function (response) {
                    if (response) {
                        $("#template").text(response.name);
                        $("#status").text(response.status);
                        // 'content' => $template->defaultTemplate ? $template->defaultTemplate->content : null,


                        if (response.content) {
                            $("#default_email_template").html(response.content);
                        } else {
                            $("#default_email_template").html("<p>No template content available.</p>");
                        }


                        $(".edit-user-btn").attr("href", "/email-template/edit/" + templateId);

                    }
                },
                error: function () {
                    Swal.fire("Error", "Failed to fetch template details.", "error");
                }
            });
        });


        $(document).on('click', '.delete-user', function () {
            var templateId = $(this).data('id');

            if (!templateId) {
                Swal.fire('Error', 'Template ID is missing!', 'error');
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/api/email-templates/' + templateId,
                        type: 'DELETE',
                        success: function (response) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Template deleted successfully!',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                                fetchTemplates();
                            });
                        },
                        error: function (xhr) {
                            Swal.fire('Error', 'Failed to delete template.', 'error');
                        }
                    });
                }
            });
        });




    </script>











    <style>
        /* .card-footer {
                                        border-top-left-radius: 20px;
                                        border-top-right-radius: 20px;
                                    } */

        .icon-style1 {
            background-color: white;
            color: rgb(157 195 179);
            padding: 5px;
            font-size: 20px;
            border-radius: 50%;
        }

        .icon-style2 {
            /* background-color: white; */
            color: white;
            padding: 5px;
            font-size: 20px;
            border-radius: 50%;
        }
    </style>
@endsection