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
        <div class="content" style="height:100vh;">
            <div class="row" style="padding-top:15px">
                <div class=" col-6">
                    <h4 class="page-title">Create Template</h4>
                </div>
                @if(app('hasPermission')(16, 'view'))
                    <div class=" col-6 text-right m-b-2 eye-btn ">
                        <a href="{{ route('email.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                            <i class="fa fa-eye m-r-5 icon3"></i>
                            Email Template
                        </a>
                    </div>
                @endif
            </div>



            <h2 class="text-center mt-5">All Email Templates</h2>
            <div class="owl-carousel owl-theme" id="templateSlider">
                <!-- Templates will be dynamically added here -->
            </div>





            <div class="row">
                <div class="offset-lg-2">
                    <form class="form-container" id="createServiceForm">
                        @csrf
                        <input type="hidden" name="branch_id" id="branch_id">
                        <input type="hidden" name="template_id" id="template_id">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label><i class="fas fa-envelope icon-style"></i> Template Name</label>
                                    <input class="form-control" type="text" name="name" id="name" required>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label class="display-block"><i class="fas fa-toggle-on icon-style"></i> <span class="hdr-btn-text">Status</span></label>
                                    <div class="form-control">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="status" id="active"
                                                value="active" checked>
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
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <label><i class="fas fa-image icon-style"></i> Img1</label>
                                <input type="file" class="form-control" name="img1" id="img1">
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <label><i class="fas fa-image icon-style"></i> Img2</label>
                                <input type="file" class="form-control" name="img2" id="img2">
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <label><i class="fas fa-image icon-style"></i> Img3</label>
                                <input type="file" class="form-control" name="img3" id="img3">
                            </div>
                        </div>
                        <div id="emailsuccessMessage" class="alert alert-success" style="display:none;"></div>
                        <div id="emailerrorMessage" class="alert alert-danger" style="display:none;"></div>
                        <div class="m-t-20 text-center">
                            <button type="submit" class="btn btn-primary submit-btn">Create Template</button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">

    <!-- jQuery and Owl Carousel JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
    let storedBranchId = localStorage.getItem('selectedBranchId');
    if (storedBranchId) {
        document.getElementById('branch_id').value = storedBranchId;
    }
});
        $(document).ready(function () {
            // Initialize Owl Carousel
            $('#templateSlider').owlCarousel({
                loop: true,
                margin: 10,
                nav: true,
                dots: true,
                items: 1,
                responsive: {
                    0: { items: 1 },
                    600: { items: 1 },
                    1000: { items: 1 }
                }
            });

            let selectedTemplateId = null; // Variable to store selected template ID

            // Fetch Templates via API
            $.ajax({
                url: '/api/default-email-templates',
                type: 'GET',
                headers: { "Authorization": "Bearer " +token},
                success: function (templates) {
                    templates.forEach(function (template) {
                        $('#templateSlider').trigger('add.owl.carousel', [`<div class="item template-item">
                                <div class="template-content" data-template-id="${template.id}">
                                    ${template.content}
                                </div>
                            </div>`]).trigger('refresh.owl.carousel');
                    });
                },
                error: function () {
                    console.log('Failed to fetch templates.');
                }
            });

            // Add click event to select template
            $(document).on('click', '.template-item .template-content', function () {
                $('.template-content').removeClass('selected-template'); // Remove previous selection
                $(this).addClass('selected-template'); // Add green border to selected template
                selectedTemplateId = $(this).data('template-id'); // Store selected template ID
                $("#template_id").val(selectedTemplateId); // Update hidden input field
            });

            // Handle form submission
            $("#createServiceForm").on("submit", function (e) {
                e.preventDefault();

                let formData = new FormData(this);
              

                if (!userId) {
                    $("#errorMessage").html("User is not logged in").show();
                    return;
                }
                if (!selectedTemplateId) {
                    $("#errorMessage").html("Please select a template before submitting.").show();
                    return;
                }

                formData.append("user_id", userId); // Append user_id to the form data
                formData.append("template_id", selectedTemplateId); // Append selected template_id

                $.ajax({
                    url: "/api/email-templates",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: { Authorization: "Bearer " + token },
                    success: function (response) {
                        $("#emailsuccessMessage").text(response.message).show();
                      
                        $("#createServiceForm")[0].reset();
                        selectedTemplateId = null; // Reset selected template
                        $(".template-content").removeClass("selected-template"); // Remove highlight

                        setTimeout(function () {
                            window.location.href = "{{ route('email.index') }}"; // Redirect to email index page
                        }, 1500); // Redirect after 1.5 seconds
                    },

                    error: function (xhr) {
                        let errors = xhr.responseJSON.errors;
                        let errorMessage = Object.values(errors).map(e => e[0]).join('<br>');
                        $("#emailerrorMessage").html(errorMessage).show();
                       
                    }
                });
            });
        });

    </script>


    <style>
        .selected-template {
            border: 1px solid green;
            padding: 0;
            border-radius: 10px;
        }
    </style>
@endsection