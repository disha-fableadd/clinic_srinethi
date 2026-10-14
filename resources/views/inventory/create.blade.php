@extends('layout.app')

<style>
    .inventory-btn {
        text-align: center;
        padding-right: 55px;
    }
    .form-container {
        width: 60% !important;
        padding-bottom: 60px !important;
    }


    @media screen and (max-width: 767px) {


        .page-title {
            font-size: 19px !important;
            padding-top: 6px !important;
        }

        .inventory-btn {
            text-align: right !important;
            padding-right: 15px !important;
        }



    }
</style>
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="row" style="padding-top:15px">
                <div class="col-6">
                    <h4 class="page-title text-center inventory-title" style="padding-left: 90px;">Add Inventory</h4>
                </div>
                @if (app('hasPermission')(11, 'view'))
                    <div class="col-6 inventory-btn  m-b-2">
                        <a href="{{ route('inventory.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                            <i class="fa fa-arrow-left m-r-5 icon3  "></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    </div>
                @endif
            </div>
            <div class="row">
                <div class="col-12">
                    <form class="form-container" id="multiStepForm" method="POST" action=""
                        >
                        @csrf
                        <input type="hidden" name="branch_id" id="branch_id">
                        <!-- Step 1 -->
                        <div class="form-step" id="step-1">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label><i class="fas fa-cogs icon-style"></i> Item Name</label>
                                        <input type="text" class="form-control" name="item_name">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label><i class="fas fa-box icon-style"></i> Quantity</label>
                                        <input type="number" class="form-control" name="quantity">
                                    </div>
                                </div>
                            </div>




                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <label class="mr-2"><i class="fas fa-medkit icon-style"></i> Supplier <span
                                                    class="text-danger">*</span></label>
                                            @if (app('hasPermission')(12, 'create'))
                                                <a href="{{ route('supplier.create') }}" target="_blank"
                                                    class="btn btn-primary btn-sm">
                                                    <i class="fas fa-plus"></i> Add Supplier
                                                </a>
                                            @endif

                                        </div>
                                        <select class="form-control select2" name="supplier_id">
                                            <option value="">Select Supplier</option>
                                        </select>

                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label><i class="fas fa-calendar-alt icon-style"></i> Purchase Date</label>
                                        <input type="date" class="form-control" name="purchase_date" id="purchase_date">
                                    </div>
                                </div>
                            </div>

                            <script>
                                document.addEventListener("DOMContentLoaded", function() {
                                    const purchaseDateInput = document.getElementById("purchase_date");

                                    const today = new Date().toISOString().split("T")[0];

                                    // Set max date for purchase date (can't select future date)
                                    purchaseDateInput.setAttribute("max", today);
                                });
                            </script>


                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label><i class="fas fa-toggle-on icon-style"></i> <span class="hdr-btn-text">Status</span></label>
                                        <select class="form-control select2" id="status" name="status">
                                            <option value="">Select</option>
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                            <option value="expired">Expired</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div id="inventorysuccessMessage" class="alert alert-success" style="display:none;"></div>
                            {{-- <div id="inventoryerrorMessage" class="alert alert-danger" style="display:none;"></div> --}}

                            <div class="m-t-20 text-center">
                                <button class="btn btn-primary submit-btn"> Create Inventory</button>
                            </div>
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
    <script>
        document.addEventListener('DOMContentLoaded', function () {
    let storedBranchId = localStorage.getItem('selectedBranchId');
    if (storedBranchId) {
        document.getElementById('branch_id').value = storedBranchId;
    }
});
        $('#status').select2({
            placeholder: "Select Status",
            width: '100%'
        });
        $('#status').on('select2:open', function() {
            $('.select2-search__field').attr('placeholder', 'Search Status');
        });

        $(document).ready(function() {




            $.ajax({
                url: "/api/supplierss",
                type: "GET",
                dataType: "json",
                headers: {
                    "Authorization": "Bearer " + token
                }, // Ensure authentication
                success: function(response) {
                    console.log("API Response:", response);

                    let supplierDropdown = $('select[name="supplier_id"]');

                    if (supplierDropdown.length === 0) {
                        console.error("Dropdown not found! Check your HTML.");
                        return;
                    }

                    supplierDropdown.empty().append('<option value="">Select Supplier</option>');

                    let suppliersList = response.suppliers || [];

                    if (suppliersList.length === 0) {
                        console.warn("No suppliers found for this user.");
                        return;
                    }

                    // Populate dropdown with only the logged-in user's suppliers
                    $.each(suppliersList, function(index, supplier) {
                        let name = supplier.name;
                        let formattedName = name.charAt(0).toUpperCase() + name.slice(1);

                        supplierDropdown.append(
                            `<option value="${supplier.id}">${formattedName}</option>`
                        );
                    });


                    // Initialize Select2 plugin
                    supplierDropdown.select2({
                        placeholder: "Select Supplier",
                        allowClear: true,
                        width: '100%'
                    });

                    // Set search input placeholder after opening dropdown
                    supplierDropdown.on('select2:open', function() {
                        $('.select2-search__field').attr('placeholder', 'Search Supplier');
                    });
                },
                error: function(xhr) {
                    console.error("API Error:", xhr.status, xhr.responseText);
                }
            });



            // Form Validation
            $("#multiStepForm").validate({
                rules: {
                    item_name: {
                        required: true,
                        minlength: 3
                    },
                    quantity: {
                        required: true,
                        number: true,
                        min: 1
                    },
                    supplier_id: {
                        required: true
                    },
                    purchase_date: {
                        required: true,
                        date: true
                    },
                    status: {
                        required: true
                    }
                },
                messages: {
                    item_name: {
                        required: "Please enter the item name",
                        minlength: "Item name must be at least 3 characters"
                    },
                    quantity: {
                        required: "Please enter the quantity",
                        number: "Quantity must be a number",
                        min: "Quantity must be at least 1"
                    },
                    supplier_id: {
                        required: "Please select a supplier"
                    },
                    purchase_date: {
                        required: "Please select a purchase date",
                        date: "Please enter a valid date"
                    },
                    status: {
                        required: "Please select a status"
                    }
                },
                errorElement: 'span',

                errorPlacement: function(error, element) {
                    error.addClass('text-danger');
                    element.closest('.form-group').append(error);
                },
                submitHandler: function(form) {
                    // Perform the AJAX form submission if validation passes
                    let formData = {
                        item_name: $('input[name="item_name"]').val(),
                        quantity: $('input[name="quantity"]').val(),
                        supplier_id: $('select[name="supplier_id"]').val(),
                        purchase_date: $('input[name="purchase_date"]').val(),
                        status: $('select[name="status"]').val(),
                         branch_id: $('#branch_id').val()
                    };

                    $.ajax({
                        url: "{{ url('/api/inventoryy') }}",
                        type: "POST",
                        data: JSON.stringify(formData),
                        contentType: "application/json",
                        headers: {
                            "Authorization": "Bearer " + token
                        },
                        success: function(response) {
                            $('#inventorysuccessMessage').text(response.message ||
                                'Inventory created successfully').show();
                            $('#multiStepForm')[0].reset();
                            setTimeout(function() {
                                window.location.href =
                                    "{{ route('inventory.index') }}";
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
                            $('#inventoryerrorMessage').html(errorMessage).show();
                        }
                    });
                }
            });
        });
    </script>
@endsection
