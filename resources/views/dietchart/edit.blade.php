@extends('layout.app')
<link rel="stylesheet" href="{{ asset(env('IMAGE_PATH').'admin/assets/css/dietchart-edit.css') }}"> 

@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="row mx-auto align-items-center" style="max-width: 700px; padding-top:15px">
                <div class="col-sm-6 col-6">
                    <h4 class="page-title m-0">Edit Diet Chart</h4>
                </div>
                <div class="col-sm-6 col-6 text-right m-b-2">
                    <a href="{{ route('dietchart.index') }}" class="btn btn-primary btn-rounded btn-hdr" style="float: right;margin-left:0 !important">
                        <i class="fa fa-arrow-left m-r-5 icon3"></i> <span class="hdr-btn-text">Back</span>
                    </a>
                </div>
            </div>

            <div class="assessment-entry rounded p-3 mb-4 position-relative">
                <div class="row">
                    <div class="col-12">
                        <form id="editDietForm" class="form-container mx-auto" style="max-width: 700px; padding-bottom: 60px;"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="container mt-4">

                                {{-- Diet Chart Name --}}
                                <div class="form-group">
                                    <label><i class="fas fa-file-signature"></i> Template Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control">
                                </div>

                                {{-- Dynamic Rows for Title, Description, Time --}}
                                <div id="dynamicFormContainer"></div>

                                {{-- Success / Error Messages --}}
                                <div id="editDietSuccessMessage" class="alert alert-success" style="display:none;"></div>
                                <div id="editDietErrorMessage" class="alert alert-danger" style="display:none;"></div>

                                {{-- Submit Button --}}
                                <div class="m-t-20 text-center">
                                    <button type="submit" class="btn btn-primary submit-btn">Update Diet Chart</button>
                                </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <script>
        $(document).ready(function() {
            let dietId = {{ $id }};
            let token = localStorage.getItem("token");

            // Fetch existing diet chart data
            $.ajax({
                url: `/api/dietchart/${dietId}`,
                method: 'GET',
                headers: {
                    "Authorization": "Bearer " + token
                },
                success: function(chart) {
                    $('input[name="name"]').val(chart.name);
                    $('#dynamicFormContainer').empty();

                    let titles = chart.title || [];
                    let descriptions = chart.description || [];
                    let times = chart.time || [];
                    let images = chart.image || [];

                    if (titles.length > 0) {
                        for (let i = 0; i < titles.length; i++) {
                            appendRow(titles[i], descriptions[i], times[i], images[i] || '');
                        }
                    } else {
                        appendRow();
                    }
                },
                error: function() {
                    alert('Failed to load diet chart data.');
                }
            });

            function appendRow(title = '', description = '', time = '', imagePath = '') {
                let uniqueId = 'preview-' + Math.random().toString(36).substr(2, 9);
                let imagePreview = imagePath ?
                    `<img id="${uniqueId}" src="${imagePath}" class="img-thumbnail mb-2" width="50"/>` :
                    `<img id="${uniqueId}" src="https://via.placeholder.com/80" class="img-thumbnail mb-2"/>`;

                let row = `
    <div class="row g-3 align-items-center mb-3 template-row">
        <div class="col-md-6 col-12">
            <label>Title <span class="text-danger">*</span></label>
            <input type="text" name="title[]" class="form-control" value="${title}">
        </div>
        <div class="col-md-6 col-12">
            <label>Description <span class="text-danger">*</span></label>
            <input type="text" name="description[]" class="form-control" value="${description}">
        </div>
        <div class="col-md-6 col-12">
            <label>Time <span class="text-danger">*</span></label>
            <input type="text" name="time[]" class="form-control" value="${time}">
        </div>
        <div class="col-md-6 col-12">
            <label class="mt-5">Image</label>
            <input type="file" name="image[]" class="form-control image-input" data-preview-id="${uniqueId}" accept="image/*">
            ${imagePreview}
           
        </div>
        <div class="col-6">
            <button type="button" class="btn btn-primary btn-sm addRow mr-1" title="Add"><i class="fas fa-plus"></i></button>
            <button type="button" class="btn btn-danger btn-sm removeRow" title="Remove"><i class="fas fa-minus"></i></button>
        </div>
    </div>`;

                $('#dynamicFormContainer').append(row);
            }


            // Add / Remove row
            $(document).on('click', '.addRow', function() {
                appendRow();
            });
            $(document).on('click', '.removeRow', function() {
                if ($('.template-row').length > 1) $(this).closest('.template-row').remove();
                else alert("At least one row is required.");
            });

            // Image preview
            $(document).on('change', '.image-input', function(event) {
                let previewId = $(this).data('preview-id');
                let file = event.target.files[0];
                if (file) {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        $('#' + previewId).attr('src', e.target.result);
                    }
                    reader.readAsDataURL(file);
                }
            });

            // Submit updated data
            $('#editDietForm').submit(function(e) {
                e.preventDefault();
                let formData = new FormData(this);

                $.ajax({
                    url: `/api/dietchart/${dietId}`,
                    method: 'POST',
                    headers: {
                        "Authorization": "Bearer " + token,
                        "X-HTTP-Method-Override": "PUT"
                    },
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        $('#editDietSuccessMessage').text(res.message).show();
                        $('#editDietErrorMessage').hide();
                        window.location.href = '/dietchart';
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON.errors || {};
                        let msg = '';
                        $.each(errors, function(key, arr) {
                            msg += arr.join(', ') + '<br>';
                        });
                        $('#editDietErrorMessage').html(msg).show();
                        $('#editDietSuccessMessage').hide();
                    }
                });
            });
        });
    </script>
@endsection
