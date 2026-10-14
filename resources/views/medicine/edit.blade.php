@extends('layout.app')

<style>
        .select2-container--default .select2-selection--multiple .select2-selection__choice__display {
    cursor: default;
    padding-left: 12px !important;
    padding-right: 5px;
}

.select2-container--default .select2-selection--multiple .select2-search__field {
   
    min-height: 27px !important;
   
}
    .medicine-title {
        padding-left: 140px !important;
        text-align: center !important;
    }

    .medicine-button {
        padding-right: 150px !important;
        text-align: center !important;
    }
    .form-container {
        width: 60% !important;
        padding-bottom: 60px !important;
    }



    /* GST select2 styling to match reference UI */
    .gst-select2 + .select2-container .select2-selection--multiple,
    .gst-select2 + .select2-container .select2-selection--single {
        border: 1px solid #cfe6d9 !important;
        border-radius: 22px !important;
        padding: 2px 10px !important;
        background: #fff !important;
        box-shadow: none !important;
        min-height: 38px !important;
        display: flex;
        align-items: center;
    }
    .gst-select2 + .select2-container .select2-selection__rendered {
        line-height: normal !important;
        display: flex !important;
        align-items: center !important;
        flex-wrap: wrap;
        padding: 0 !important;
    }
    .gst-select2 + .select2-container .select2-selection__choice {
        background: #d9efe6 !important;
        border: 1px solid #c6e3d6 !important;
        border-radius: 16px !important;
        padding: 0px 8px !important;
        color: #1f4b3e !important;
        font-size: 12px !important;
        margin-top: 2px !important;
        margin-bottom: 2px !important;
        display: flex;
        align-items: center;
    }
    .gst-select2 + .select2-container .select2-selection__choice__remove {
        color: #1f4b3e !important;
        margin-right: 6px !important;
    }
    .gst-select2 + .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 26px !important;
    }
    .gst-select2 + .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #d9efe6 !important;
        color: #1f4b3e !important;
    }

    @media screen and (max-width:767px) {
        .medicine-title {
            padding-left: 15px !important;
            text-align: left !important;
        }

        .medicine-button {
            padding-right: 15px !important;
            text-align: right !important;
        }

        .page-title {
            font-size: 20px;
        }

        .form-container #step-1 {
            height: 650px;
        }

        .form-container #step-2 {
            height: 470px !important;
        }

    }
</style>
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="row" style="padding-top:15px">
                <div class=" col-6">
                    <h4 class="page-title medicine-title ">Edit Medicine</h4>
                </div>
                @if (app('hasPermission')(4, 'view'))
                    <div class="col-6 medicine-button  medicine-title  m-b-2 ">
                        <a href="{{ route('medicine.index') }}" class="btn btn-primary  btn-rounded">
                            <i class="fa fa-arrow-left m-r-5 icon3  "></i> <span class="hdr-btn-text">Back</span></a>
                    </div>
                @endif
            </div>
            <div class="row">
                <div class="col-12">
                    <form class="form-container" id="medicineForm" method="POST" action="javascript:void(0);"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="medicineId">
                        <!-- Step 1 -->
                        <div class="form-step" id="step-1">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label><i class="fas fa-boxes icon-style"></i> Category <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control" name="category_id" id="categoryDropdown"
                                            data-selected="{{ old('category_id', $category->category_id ?? '') }}" required>
                                            <option value="">Select Category</option>
                                        </select>

                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label><i class="fas fa-pills icon-style"></i> Medicine Name <span
                                                class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="name" id="name" required>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label><i class="fas fa-align-left icon-style"></i> Description</label>
                                        <textarea class="form-control" rows="3" name="description" id="description" style="border-radius:10px"></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div id="imagePreviewContainer">
                                            <img id="imagePreview" src="" alt="Medicine Image"
                                                style="max-width: 200px; display: none;">
                                        </div>

                                        <label><i class="fas fa-image icon-style"></i> Upload Image</label>
                                        <div class="profile-upload">
                                            <div class="upload-input">
                                                <input type="file" id="imageInput" name="image" accept="image/*">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <script>
                                    const imageInput = document.getElementById('imageInput');
                                    const imagePreview = document.getElementById('imagePreview');

                                    imageInput.addEventListener('change', function() {
                                        const file = this.files[0];
                                        if (file) {
                                            const reader = new FileReader();

                                            reader.onload = function(e) {
                                                imagePreview.src = e.target.result;
                                                imagePreview.style.display = 'block'; // Show the image
                                            };

                                            reader.readAsDataURL(file);
                                        } else {
                                            imagePreview.src = '';
                                            imagePreview.style.display = 'none'; // Hide if no image selected
                                        }
                                    });
                                </script>

                            </div>
                            <button type="button" class="btn btn-primary"
                                style="margin-left:5px;padding:8px 50px;float:right;" onclick="nextStep()">
                                Next
                            </button>
                        </div>

                        <!-- Step 2 -->
                        <div class="form-step" id="step-2" style="display: none;">
                            <div class="row">
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-sort-numeric-up icon-style"></i> Quantity <span
                                                class="text-danger">*</span></label>
                                        <input class="form-control" type="number" name="quantity" id="quantity" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-weight icon-style"></i> Price <span
                                                class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="unit" id="unit" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-tag icon-style"></i> Medicine Unit <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control select2" name="medicine_unit" id="medicine_unit_dropdown" required>
                                            <option value="">Select Unit</option>
                                        </select>
                                    </div>
                                </div>
                               
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-calendar-alt icon-style"></i> Manufacturing Date <span
                                                class="text-danger">*</span></label>
                                        <div class="">
                                            <input type="date" class="form-control " name="manufacture_date"
                                                id="manufacture_date" required>
                                        </div>
                                    </div>
                                </div>
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-calendar-times icon-style"></i> Expiry Date <span
                                                class="text-danger">*</span></label>
                                        <div class="">
                                            <input type="date" class="form-control " name="expiry_date"
                                                id="expiry_date" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-barcode icon-style"></i> Batch Number <span
                                                class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="batch_no" id="batch_no" required>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-percent icon-style"></i> GST Option <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control select2 gst-select2" name="gst_option" id="gst_option" required>
                                            <option value="Without GST">Without GST</option>
                                            <option value="With GST">With GST</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-6" id="product_gst_div" style="display: none;">
                                    <div class="form-group">
                                        <label><i class="fas fa-file-invoice-dollar icon-style"></i> Product GST <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control select2 gst-select2" name="product_gst[]" id="product_gst" multiple>
                                            <option value="">Select GST</option>
                                        </select>
                                    </div>
                                </div>
                               
                                <script>
                                    document.addEventListener("DOMContentLoaded", function() {
                                        const manufactureDateInput = document.getElementById('manufacture_date');
                                        const expiryDateInput = document.getElementById('expiry_date');

                                        // Get today's date in YYYY-MM-DD format
                                        const today = new Date().toISOString().split('T')[0];

                                        // Set max date for manufacture date to today (can't choose future)
                                        manufactureDateInput.setAttribute('max', today);

                                        // Set min date for expiry date to today (can't choose past)
                                        expiryDateInput.setAttribute('min', today);
                                    });
                                </script>



                            </div>
                            <div id="editmedicinesuccessMessage" class="alert alert-success" style="display:none;"></div>
                            {{-- <div id="editmedicineerrorMessage" class="alert alert-danger" style="display:none;"></div> --}}
                            <button type="button" class="btn btn-danger"
                                style="padding:8px 50px;border-radius:50px; float:left" onclick="prevStep()">
                                Previous
                            </button>

                            <button type="submit" class="btn btn-primary"
                                style="padding:8px 50px;border-radius:50px; float:right">
                                Submit
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>

    <script>
      

        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('toggle_btn');
            toggleBtn.addEventListener('click', function() {
                document.body.classList.toggle('mini-sidebar');
            });
        });

        function nextStep() {
            var validator = $("#medicineForm").validate();
            if (validator.element("#categoryDropdown") && validator.element("[name='name']")) {
                document.getElementById('step-1').style.display = 'none';
                document.getElementById('step-2').style.display = 'block';
            }
        }

        function prevStep() {
            document.getElementById('step-2').style.display = 'none';
            document.getElementById('step-1').style.display = 'block';
        }

        $(document).ready(function() {
            const token = localStorage.getItem('authToken');
            $('#gst_option').select2({
                placeholder: "Select GST Option",
                width: '100%'
            });

            $('#product_gst').select2({
                placeholder: "Select GST",
                allowClear: true,
                closeOnSelect: false,
                width: '100%'
            });

            // Initialize form validation
            $('#medicineForm').validate({
                ignore: ":hidden:not(.datetimepicker)", // Ignore hidden fields except datetimepicker
                rules: {
                    category_id: {
                        required: true
                    },
                    name: {
                        required: true,
                        minlength: 3
                    },
                    description: {
                        required: true,
                        minlength: 3
                    },
                    unit: {
                        required: true
                    },
                    medicine_unit: {
                        required: true
                    },
                    quantity: {
                        required: true,
                        digits: true
                    },
                    manufacture_date: {
                        required: true
                    },
                    batch_no: {
                        required: true
                    },
                    expiry_date: {
                        required: true
                    },
                    // status: {
                    //     required: true
                    // }
                },
                messages: {
                    category_id: {
                        required: "Please select a category"
                    },
                    name: {
                        required: "Please enter the medicine name",
                        minlength: "Medicine name must be at least 3 characters long"
                    },
                    description: {
                        required: "Please enter the medicine description",
                        minlength: "Medicine description must be at least 3 characters long"
                    },
                    unit: {
                        required: "Please enter the price"
                    },
                    medicine_unit: {
                        required: "Please select a unit"
                    },
                    quantity: {
                        required: "Please enter the quantity",
                        digits: "Quantity must be a number"
                    },
                    manufacture_date: {
                        required: "Please enter the manufacture date"
                    },
                    batch_no: {
                        required: "Please enter the batch number"
                    },
                    expiry_date: {
                        required: "Please enter the expiry date"
                    },
                    // status: {
                    //     required: "Please select a status"
                    // }
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });

            $('#gst_option').on('change', function() {
                if ($(this).val() === 'With GST') {
                    $('#product_gst_div').show();
                    $('#product_gst').attr('required', true);
                } else {
                    $('#product_gst_div').hide();
                    $('#product_gst').attr('required', false).val('').trigger('change');
                }
            });

            function fetchMedicineUnits() {
                let branchId = localStorage.getItem('selectedBranchId');
                return $.ajax({
                    url: '/api/medicine-units',
                    type: 'GET',
                    data: {
                        branch_id: branchId
                    },
                    headers: {
                        "Authorization": "Bearer " + token
                    },
                    success: function(response) {
                        let unitDropdown = $('#medicine_unit_dropdown');
                        unitDropdown.empty().append('<option value="">Select Unit</option>');
                        if (response.status && response.data) {
                            response.data.forEach(function(unit) {
                                unitDropdown.append(
                                    `<option value="${unit.unit}">${unit.unit}</option>`
                                );
                            });
                        }
                        unitDropdown.select2({
                            placeholder: "Select Unit",
                            allowClear: true,
                            width: '100%'
                        });
                    },
                    error: function(xhr) {
                        console.error("Error loading medicine units:", xhr.responseText);
                    }
                });
            }

            function fetchTaxRates(callback) {
                let branchId = localStorage.getItem('selectedBranchId');
                $.ajax({
                    url: '/api/tax-rates',
                    type: 'GET',
                    data: { branch_id: branchId },
                    headers: { "Authorization": "Bearer " + token },
                    success: function(data) {
                        let gstDropdown = $('#product_gst');
                        gstDropdown.empty().append('<option value="">Select GST</option>');
                        
                        let taxRates = Array.isArray(data) ? data : (data.data || []);
                        window.taxRatesList = taxRates; // Store for mapping
                        $.each(taxRates, function(index, tax) {
                            if (tax.status === 'active') {
                                gstDropdown.append(`<option value="${tax.id}">${tax.tax_name} (${tax.tax_rate}%)</option>`);
                            }
                        });

                        ensureProductGstSelect2();

                        if (callback) callback();
                    },
                    error: function(xhr) {
                        console.error("Error loading tax rates:", xhr.responseText);
                    }
                });
            }




            let pathParts = window.location.pathname.split('/');
            let medicineId = pathParts[pathParts.length - 1];
            $("#medicineId").val(medicineId);

            // Fetch categories and tax rates first, then medicine details
            $.when(fetchCategories(), fetchTaxRates(), fetchMedicineUnits()).done(function() {
                fetchMedicineDetails(medicineId);
            });

           
            function fetchCategories() {
                let branchId = localStorage.getItem('selectedBranchId'); // ✅ from localStorage

                return $.ajax({
                    url: '/api/categories',
                    type: 'GET',
                    headers: {
                        "Authorization": "Bearer " + token
                    },
                    data: {
                        branch_id: branchId
                    }, // ✅ send branch_id
                    success: function(response) {
                        var categoryDropdown = $('select[name="category_id"]');

                        if (categoryDropdown.length === 0) {
                            console.error("Dropdown not found! Check your HTML.");
                            return;
                        }

                        categoryDropdown.empty().append('<option value="">Select Category</option>');

                        if (!response || response.length === 0) {
                            console.warn("No categories found for this branch.");
                            return;
                        }

                        // Store temporarily in case needed later
                        window.categoryList = response;

                        // Populate dropdown
                        $.each(response, function(index, category) {
                            let name = category.name;
                            let formattedName = name.charAt(0).toUpperCase() + name.slice(1);

                            categoryDropdown.append(
                                `<option value="${category.id}">${formattedName}</option>`
                            );
                        });

                        // Initialize Select2
                        categoryDropdown.select2({
                            placeholder: "Select Category",
                            allowClear: true,
                            width: '100%'
                        });

                        // Set placeholder for search field
                        categoryDropdown.on('select2:open', function() {
                            $('.select2-search__field').attr('placeholder', 'Search Category');
                        });
                    },
                    error: function(xhr) {
                        console.error("API Error:", xhr.status, xhr.responseText);
                        alert('Failed to load categories.');
                    }
                });
            }


            function fetchMedicineDetails(medicineId) {
                $.ajax({
                    url: '/api/medicines/' + medicineId,
                    type: 'GET',
                    headers: {
                        "Authorization": "Bearer " + token
                    },
                    success: function(data) {
                        let medicine = data.medicine;

                        $('#name').val(medicine.name);
                        $('#description').val(medicine.description);
                        $('#unit').val(medicine.unit);
                        $('#medicine_unit_dropdown').val(medicine.medicine_unit).trigger('change');
                        $('#quantity').val(medicine.quantity);
                        $('#manufacture_date').val(medicine.manufacture_date);
                        $('#batch_no').val(medicine.batch_no);
                        $('#gst_option').val(medicine.gst_option || 'Without GST').trigger('change');
                        let selectedGst = medicine.product_gst;
                        if (typeof selectedGst === 'string') {
                            try {
                                selectedGst = JSON.parse(selectedGst);
                            } catch (e) {
                                selectedGst = selectedGst.split(',').map(v => v.trim());
                            }
                        }
                        if (selectedGst && Array.isArray(selectedGst)) {
                            let mappedGstIds = [];
                            $.each(selectedGst, function(i, item) {
                                if (typeof item === 'object' && item !== null) {
                                    // Try to find matching ID from window.taxRates
                                    let match = (window.taxRates || []).find(t => 
                                        t.tax_name === item.tax_name && 
                                        parseFloat(t.tax_rate) === parseFloat(item.tax_rate)
                                    );
                                    if (match) mappedGstIds.push(String(match.id));
                                } else {
                                    mappedGstIds.push(String(item));
                                }
                            });
                            $('#product_gst').val(mappedGstIds).trigger('change');
                        } else if (selectedGst) {
                            $('#product_gst').val(String(selectedGst)).trigger('change');
                        }
                        $('#expiry_date').val(medicine.expiry_date);

                        // ✅ Now that categories are loaded and select2 initialized, set selected value
                        $('#categoryDropdown').val(medicine.category_id).trigger('change');

                        // Optional: preview image
                        if (medicine.image) {
                            $('#imagePreview')
                                .attr('src', medicine.image)
                                .attr('onerror',
                                    "this.onerror=null;this.src='{{ asset('admin/assets/img/default.webp') }}';"
                                )
                                .show();
                            $('#imagePreviewContainer').show();
                        } else {
                            $('#imagePreview').attr('src',
                                "{{ asset('admin/assets/img/default.webp') }}").show();
                            $('#imagePreviewContainer').show();
                        }
                    },
                    error: function() {
                        alert('Failed to fetch medicine details.');
                    }
                });
            }





            $("#medicineForm").submit(function(e) {
                e.preventDefault();

                if ($('#medicineForm').valid()) {

                    let manufactureDate = new Date($('#manufacture_date').val());
                    let expiryDate = new Date($('#expiry_date').val());

                  

                    var formData = new FormData(this);
                    formData.append('_method', 'PUT');

                    $.ajax({
                        url: '/api/medicines/' + medicineId,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            $('#editmedicinesuccessMessage').text(response.message ||
                                'Medicine updated successfully').show();
                            $('#medicineForm')[0].reset();
                            setTimeout(function() {
                                window.location.href = "{{ route('medicine.index') }}";
                            }, 1500);
                        },
                        error: function(xhr) {
                            var errorMessage = '';
                            if (xhr.status === 422) { // Validation error
                                var errors = xhr.responseJSON.errors;
                                $.each(errors, function(key, messages) {
                                    errorMessage += messages[0] + '<br>';
                                });
                            }
                            $('#editmedicineerrorMessage').html(errorMessage).show();
                        }
                    });
                }
            });
        });
    </script>
@endsection
