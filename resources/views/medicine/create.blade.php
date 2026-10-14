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
        padding-left: 70px !important;
        text-align: center !important;
    }

    .medicine-button {
        padding-right: 60px !important;
        text-align: center !important;
    }
    .form-container {
        width: 60% !important;
        padding-bottom: 60px !important;
    }

    /* GST select2 styling – slim pills like reference UI */
    .gst-select2 + .select2-container .select2-selection--multiple,
    .gst-select2 + .select2-container .select2-selection--single {
        border: 1px solid #cfe6d9 !important;
        border-radius: 22px !important;
   
        padding: 2px 10px !important;
        background: #fff !important;
        box-shadow: none !important;
        display: flex !important;
        align-items: center !important;
    }
    .gst-select2 + .select2-container .select2-selection__rendered {
        display: flex !important;
        flex-wrap: wrap !important;
        align-items: center !important;
        gap: 4px !important;
        padding: 0 !important;
        line-height: 1.2 !important;
    }
    .gst-select2 + .select2-container .select2-selection__choice {
        background: #d9efe6 !important;
        border: 1px solid #c6e3d6 !important;
        border-radius: 14px !important;
        padding: 0 8px !important;
        color: #1f4b3e !important;
        font-size: 12px !important;
        margin-top: 0 !important;
        height: 22px !important;
        display: inline-flex !important;
        align-items: center !important;
    }
    .gst-select2 + .select2-container .select2-selection__choice__remove {
        color: #1f4b3e !important;
        margin-right: 4px !important;
        font-size: 12px !important;
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
        .medi-form {
    height: 830px!important;
}
        

    }
</style>


@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="row" style="padding-top:15px">
                <div class=" col-6">
                    <h4 class="page-title medicine-title ">Add Medicine</h4>
                </div>
                @if (app('hasPermission')(4, 'view'))
                    <div class=" col-6 medicine-button  m-b-2">
                        <a href="{{ route('medicine.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                            <i class="fa fa-arrow-left m-r-5"></i> <span class="hdr-btn-text">Back</span></a>
                    </div>
                @endif
            </div>

            <div class="row">
                <div class="col-12">
                    <form class="form-container medi-form" id="medicineForm" method="POST" action="javascript:void(0);"
                        enctype="multipart/form-data" >
                        @csrf
                        <input type="hidden" name="branch_id" id="branch_id">

                        <!-- Step 1 -->
                        <div class="form-step" id="step-1">
                            <div class="row">


                                <div class="col-sm-12">
                                    <div class="form-group d-flex align-items-center justify-content-between">
                                        <label class="mb-0">
                                            <i class="fas fa-boxes icon-style"></i> Category <span
                                                class="text-danger">*</span>
                                        </label>
                                        @if (Auth::check() && optional(Auth::user()->role)->name == 'Admin')
                                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                                                data-target="#addCategoryModal">
                                                <i class="fas fa-plus"></i> <span class="hdr-btn-text">Add</span>
                                            </button>
                                        @endif
                                    </div>
                                    <div class="input-group">
                                        <select class="form-control select2" name="category_id" id="categoryDropdown"
                                            required>
                                            <option value="">Select Category</option>
                                        </select>
                                    </div>
                                </div>


                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label><i class="fas fa-pills icon-style mt-3"></i> Medicine Name <span
                                                class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="name" required>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label><i class="fas fa-align-left icon-style"></i> Description</label>
                                        <textarea class="form-control" rows="3" name="description" style="border-radius:10px"></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label><i class="fas fa-image icon-style"></i> Upload Image</label>
                                        <div class="profile-upload">

                                            <div class="upload-input">
                                                <input type="file" class="form-control" id="imageInput" name="image"
                                                    accept="image/*">
                                                <div id="imageError" class="text-danger mt-2" style="font-size: 0.9rem;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
                                        <input class="form-control" type="number" name="quantity" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>₹ Price <span
                                                class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="unit" required>
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

                                        <input type="date" class="form-control " id="manufacture_date"
                                            name="manufacture_date" required>

                                    </div>
                                </div>
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-calendar-times icon-style"></i> Expiry Date <span
                                                class="text-danger">*</span></label>

                                        <input type="date" class="form-control " id="expiry_date" name="expiry_date"
                                            required>

                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-barcode icon-style"></i> Batch Number <span
                                                class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="batch_no" required>
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
                                    window.onload = function() {
                                        const today = new Date();

                                        // Format as yyyy-mm-dd
                                        const yyyy = today.getFullYear();
                                        const mm = String(today.getMonth() + 1).padStart(2, '0');
                                        const dd = String(today.getDate()).padStart(2, '0');
                                        const todayStr = `${yyyy}-${mm}-${dd}`;

                                        // Set max date for manufacture_date to today
                                        document.getElementById('manufacture_date').setAttribute('max', todayStr);

                                        // Set min date for expiry_date to today
                                        document.getElementById('expiry_date').setAttribute('min', todayStr);
                                    };
                                </script>

                            </div>

                            <div id="medicinesuccessMessage" class="alert alert-success" style="display:none;"></div>
                            {{-- <div id="medicineerrorMessage" class="alert alert-danger" style="display:none;"></div> --}}

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



                    <!-- Add Category Modal -->
                    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header" style="background-color: #CFECE0; color:black">
                                    <h5 class="modal-title" id="addCategoryModalLabel" style="font-size:18px">Add New
                                        Category</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form id="addCategoryForm" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="branch_id" id="branch_id">

                                        <div class="form-group">
                                            <label>Category Name</label>
                                            <input type="text" class="form-control" name="name" required>
                                        </div>

                                        <div class="form-group">
                                            <label>Description</label>
                                            <textarea class="form-control" name="description"></textarea>
                                        </div>

                                        <div id="addCategoryError" class="alert alert-danger" style="display:none;">
                                        </div>
                                        <div id="addCategorySuccess" class="alert alert-success" style="display:none;">
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal"
                                                style="border-radius:50px">Close</button>
                                            <button type="submit" class="btn btn-primary">Save Category</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>



                </div>
            </div>
        </div>
    </div>

    <!-- Include jQuery Validation Plugin -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // Define nextStep and prevStep globally
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

        document.addEventListener('DOMContentLoaded', function() {
            let storedBranchId = localStorage.getItem('selectedBranchId');
            if (storedBranchId) {
                document.getElementById('branch_id').value = storedBranchId;
            }

            // ... rest of your existing code ...
            $(document).ready(function() {
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

                const today = new Date().toISOString().split("T")[0];
                $("input[name='expiry_date']").attr("min", today);
                $('#imageInput').on('change', function() {
                    var file = this.files[0];
                    var errorMessage = '';
                    var allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp',
                        'image/jpg'
                    ];
                    if (file) {
                        if (!allowedTypes.includes(file.type)) {
                            errorMessage += 'Image must be jpg, jpeg, png, gif, or webp.<br>';
                        }
                        if (file.size > 2 * 1024 * 1024) {
                            errorMessage += 'Image size must not exceed 2MB.<br>';
                        }
                    }
                    $('#imageError').html(errorMessage);
                });

                // Initialize form validation
                $('#medicineForm').validate({
                    ignore: ":hidden:not(.datetimepicker)",
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
                        image: {
                            required: true,
                            extension: "jpg|jpeg|png|gif|webp"
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
                        status: {
                            required: true
                        }
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
                        image: {
                            required: "Please select the medicine image",
                            extension: "Only image files (jpg, jpeg, png, gif, webp) are allowed."
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
                        status: {
                            required: "Please select a status"
                        }
                    },
                    errorElement: 'span',
                    errorPlacement: function(error, element) {
                        error.addClass('invalid-feedback');
                        if (element.is('select') && element.closest('.input-group').length) {
                            element.closest('.input-group').after(error);
                        } else if (element.attr('name') === 'image') {
                            $('#imageError').html(error);
                        } else {
                            element.closest('.form-group').append(error);
                        }
                    },
                    highlight: function(element, errorClass, validClass) {
                        $(element).addClass('is-invalid');
                    },
                    unhighlight: function(element, errorClass, validClass) {
                        $(element).removeClass('is-invalid');
                    }
                });

                fetchCategories();
                fetchMedicineUnits();
                fetchTaxRates();

                $('#gst_option').on('change', function() {
                    if ($(this).val() === 'With GST') {
                        $('#product_gst_div').show();
                        $('#product_gst').attr('required', true);
                    } else {
                        $('#product_gst_div').hide();
                        $('#product_gst').attr('required', false).val(null).trigger('change');
                    }
                });

                function fetchMedicineUnits() {
                    let branchId = localStorage.getItem('selectedBranchId');
                    $.ajax({
                        url: '/api/medicine-units',
                        type: 'GET',
                        data: {
                            branch_id: branchId
                        },
                        headers: {
                            "Authorization": "Bearer " + localStorage.getItem("authToken")
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

              

                function fetchCategories() {
                    let branchId = localStorage.getItem('selectedBranchId'); // 👈 branch from localStorage

                    $.ajax({
                        url: '/api/categories',
                        type: 'GET',
                        data: {
                            branch_id: branchId
                        }, // 👈 send to backend
                        headers: {
                            "Authorization": "Bearer " + localStorage.getItem("authToken")
                        },
                        success: function(response) {
                            let categoryDropdown = $('#categoryDropdown');
                            categoryDropdown.empty().append(
                                '<option value="">Select Category</option>');

                            if (!response || response.length === 0) {
                                categoryDropdown.append(
                                    '<option value="">No categories found</option>');
                            } else {
                                $.each(response, function(index, category) {
                                    let formattedName = category.name.charAt(0)
                                        .toUpperCase() + category.name.slice(1);
                                    categoryDropdown.append(
                                        `<option value="${category.id}">${formattedName}</option>`
                                    );
                                });
                            }

                            // ✅ Initialize Select2 after data is loaded
                            categoryDropdown.select2({
                                placeholder: "Select Category",
                                allowClear: true,
                                width: '100%'
                            });

                            // ✅ Set search input placeholder
                            categoryDropdown.on('select2:open', function() {
                                $('.select2-search__field').attr('placeholder',
                                    'Search Category');
                            });
                        },
                        error: function(xhr) {
                            console.error("API Error:", xhr.status, xhr.responseText);
                            alert('Failed to load categories.');
                        }
                    });
                }


                $('#medicineForm').on('submit', function(e) {
                    e.preventDefault();
                    if ($('#medicineForm').valid()) {
                        var formData = new FormData(this);
                        let storedBranchId = localStorage.getItem('selectedBranchId');
                        let url = "{{ url('api/medicines') }}";
                        if (storedBranchId) {
                            url += `?branch_id=${storedBranchId}`;
                        }
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            headers: {
                                "Authorization": "Bearer " + localStorage.getItem(
                                    "authToken")
                            },
                            success: function(response) {
                                $('#medicinesuccessMessage').text(response.message ||
                                    'Medicine created successfully').show();
                                $('#medicineForm')[0].reset();
                                if (response.token) {
                                    localStorage.setItem("authToken", response.token);
                                }
                                setTimeout(function() {
                                    window.location.href =
                                        "{{ route('medicine.index') }}";
                                }, 1500);
                            },
                            error: function(xhr) {
                                var errorMessage = '';
                                if (xhr.status === 422) {
                                    var errors = xhr.responseJSON.errors;
                                    $.each(errors, function(key, messages) {
                                        errorMessage += messages[0] + '<br>';
                                    });
                                }
                                $('#medicineerrorMessage').html(errorMessage).show();
                            }
                        });
                    }
                });
            });

          

            $(document).ready(function() {
                $('#addCategoryForm').on('submit', function(e) {
                    e.preventDefault();

                    let formData = new FormData(this);

                    // ✅ Ensure branch_id is added from localStorage
                    formData.set('branch_id', localStorage.getItem('selectedBranchId'));

                    $.ajax({
                        url: "{{ url('api/category') }}",
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            "Authorization": "Bearer " + localStorage.getItem("authToken")
                        },
                        success: function(response) {
                            $('#addCategorySuccess').text(response.message ||
                                'Category added successfully').show();
                            $('#addCategoryError').hide();

                            // ✅ Add new category to dropdown
                            $('#categoryDropdown').append(
                                `<option value="${response.category.id}" selected>${response.category.name}</option>`
                            ).trigger('change'); // refresh Select2 if used

                            setTimeout(() => {
                                $('#addCategoryModal').modal('hide');
                                $('#addCategoryForm')[0].reset();
                                $('#addCategorySuccess').hide();
                            }, 1000);
                        },
                        error: function(xhr) {
                            let errorMessage = 'Error adding category';
                            if (xhr.status === 422) {
                                errorMessage = Object.values(xhr.responseJSON.errors)
                                    .join('<br>');
                            }
                            $('#addCategoryError').html(errorMessage).show();
                            $('#addCategorySuccess').hide();
                        }
                    });
                });
            });


        });
    </script>
@endsection

