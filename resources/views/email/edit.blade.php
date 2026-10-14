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
                    <h4 class="page-title">Edit Template</h4>
                </div>
                @if(app('hasPermission')(16, 'view'))
                    <div class="col-6 text-right m-b-2">
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


                        <input type="hidden" name="template_id" id="template_id" value="{{ $emailtemplate_id }}">

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
                        <div id="editemailsuccessMessage" class="alert alert-success" style="display:none;"></div>
                        <div id="editemailerrorMessage" class="alert alert-danger" style="display:none;"></div>
                        <div class="m-t-20 text-center">
                            <button type="submit" class="btn btn-primary submit-btn">Update Template</button>
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
        let selectedTemplateId;
        $(document).ready(function () {
            // let templateId = $('#template_id').val();

            let emailtemplateId = "{{ $emailtemplate_id }}";
            console.log("Template ID from input field:", emailtemplateId);

            if (!emailtemplateId) {
                console.error("Template ID is missing or empty.");
            }

            if (emailtemplateId) {

                $.ajax({
                    url: `/api/email-templates/${emailtemplateId}`,
                    type: 'GET',
                    headers: { "Authorization": "Bearer " + token },
                    success: function (data) {
                       // console.log("API response:", data); // Debugging API response
                        selectedTemplateId = data.template_id
                        if (data) {
                            $('#template_id').val(data.id);
                            $('#name').val(data.name);
                            $('input[name="status"][value="' + data.status + '"]').prop('checked', true);

                            if (data.img1) {
                                let img1Path = `/uploads/images/${data.img1.replace('uploads/images/', '')}`;
                                $('#img1').after(`<img src="${img1Path}" width="100" class="mt-2">`);
                            }
                            if (data.img2) {
                                let img2Path = `/uploads/images/${data.img2.replace('uploads/images/', '')}`;
                                $('#img2').after(`<img src="${img2Path}" width="100" class="mt-2">`);
                            }
                            if (data.img3) {
                                let img3Path = `/uploads/images/${data.img3.replace('uploads/images/', '')}`;
                                $('#img3').after(`<img src="${img3Path}" width="100" class="mt-2">`);
                            }


                        } else {
                            console.warn("No data received from API.");
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("API request failed:", error);
                        console.error("Response Text:", xhr.responseText);
                        alert('Failed to load template details.');
                    }
                });
            }



            $('#createServiceForm').on('submit', function (e) {
                e.preventDefault();

                var formData = new FormData(this);
                let templateId = $('#template_id').val();

                if (!templateId) {
                    alert("No template selected for update.");
                    return;
                }

                // Append method override for Laravel
                formData.append('_method', 'PUT');

                // Ensure name and status fields are present
                formData.append('name', $('#name').val());
                formData.append('status', $('input[name="status"]:checked').val());

                let url = `/api/email-templates/${emailtemplateId}`;

                $.ajax({
                    url: url,
                    type: 'POST',  // Laravel doesn't handle PUT with FormData well, so use POST
                    headers: { "Authorization": "Bearer " + token },
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        console.log("Form submission success:", response);
                        $('#editemailsuccessMessage').text('Template updated successfully!').show();
                        setTimeout(function () {
                            window.location.href = "{{ route('email.index') }}";
                        }, 1500);
                    },
                    error: function (xhr, status, error) {
                        console.error("Form submission failed:", error);
                        console.error("Response Text:", xhr.responseText);
                        $('#editemailerrorMessage').text('Something went wrong.').show();
                    }
                });
            });

        });

        $(document).ready(function () {
            // let selectedTemplateId = 0
            let startPosition = 0;
            let templatesArray = [];

            // Initialize Owl Carousel (empty for now)
            let owl = $('#templateSlider').owlCarousel({
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

            // Fetch Templates via API
            $.ajax({
                url: '/api/default-email-templates',
                type: 'GET',
                headers: { "Authorization": "Bearer " + token},
                success: function (templates) {
                    templatesArray = templates;

                    // Destroy existing carousel before adding new items
                    $('#templateSlider').trigger('destroy.owl.carousel').html('');

                    templates.forEach(function (template, index) {
                        let isSelected = template.id == selectedTemplateId ? 'selected-template' : '';

                        // Add new template items to the carousel
                        $('#templateSlider').append(`
                                    <div class="item template-item">
                                        <div class="template-content  ${isSelected}" data-template-id="${template.id}">
                                            ${template.content}
                                        </div>
                                    </div>
                                `);

                        // Find the index of the stored template ID
                        if (template.id == selectedTemplateId) {
                            startPosition = index;
                        }
                    });

                    // Reinitialize the carousel with new items
                    $('#templateSlider').owlCarousel({
                        loop: true,
                        margin: 10,
                        nav: true,
                        dots: true,
                        items: 1
                    });

                    // Move carousel to stored template ID's position
                    $('#templateSlider').trigger('to.owl.carousel', [startPosition, 300, true]);

                },
                error: function () {
                    console.log('Failed to fetch templates.');
                }
            });

            // Click event to select a template
            $(document).on('click', '.template-item .template-content', function () {
                $('.template-content').removeClass('selected-template');
                $(this).addClass('selected-template');
                selectedTemplateId = $(this).data('template-id');
                $("#template_id").val(selectedTemplateId);
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