@extends('layout.app')
<style>
    .error {
        color: red;
        font-size: 14px;
        margin-top: 5px;
    }

    .is-invalid {
        border-color: red;
    }

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
                    <h4 class="page-title text-center inventory-title" style="padding-left: 90px;">Edit Inventory</h4>
                </div>
                @if (app('hasPermission')(11, 'view'))
                    <div class="col-6 inventory-btn ">
                        <a href="{{ route('inventory.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                            <i class="fa fa-arrow-left m-r-5 icon3"></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    </div>
                @endif
            </div>
            <div class="row">
                <div class="col-12">
                    <form class="form-container" id="multiStepForm" method="POST" action="">
                        @csrf
                        <input type="hidden" id="inventoryId">

                        <div class="form-step" id="step-1">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label><i class="fas fa-cogs icon-style"></i> Item Name</label>
                                        <input type="text" class="form-control" name="item_name" id="item_name">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label><i class="fas fa-box icon-style"></i> Quantity</label>
                                        <input type="number" class="form-control" name="quantity" id="quantity">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label><i class="fas fa-truck icon-style"></i> Supplier Name</label>
                                        <select class="form-control select2" name="supplier_id" id="supplier_id">
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

                                    const today = new Date();
                                    const formattedToday = today.toISOString().split("T")[0];

                                    // Set max date for purchase date (can't select future date)
                                    if (purchaseDateInput) {
                                        purchaseDateInput.setAttribute("max", formattedToday);
                                    }
                                });
                            </script>


                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label><i class="fas fa-toggle-on icon-style"></i> <span class="hdr-btn-text">Status</span></label>
                                        <select class="form-control select2" name="status" id="status">
                                            <option value="">Select</option>
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                            <option value="expired">Expired</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div id="editinventorysuccessMessage" class="alert alert-success" style="display:none;"></div>
                            {{-- <div id="editinventoryerrorMessage" class="alert alert-danger" style="display:none;"></div> --}}

                            <div class="m-t-20 text-center">
                                <button class="btn btn-primary submit-btn" type="submit"> Update Inventory</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery Validation Script -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $('#status').select2({
            placeholder: "Select Status",
            width: '100%'
        });
        $('#status').on('select2:open', function() {
            $('.select2-search__field').attr('placeholder', 'Search Status');
        });

        $(document).ready(function() {

            let inventoryId = getInventoryIdFromURL();

            if (inventoryId) {
                fetchSuppliers().then(() => {
                    fetchInventoryDetails(inventoryId);
                });
            }

            function fetchSuppliers() {
                return $.ajax({
                    url: "/api/supplierss",
                    type: "GET",
                    dataType: "json",
                    headers: {
                        "Authorization": "Bearer " + token
                    },
                    success: function(response) {
                        let supplierDropdown = $('#supplier_id');
                        supplierDropdown.empty().append('<option value="">Select Supplier</option>');

                        $.each(response.suppliers, function(index, supplier) {
                            let name = supplier.name;
                            let formattedName = name.charAt(0).toUpperCase() + name.slice(1);

                            supplierDropdown.append(
                                `<option value="${supplier.id}">${formattedName}</option>`
                            );
                        });


                        supplierDropdown.select2({
                            placeholder: "Select Supplier",
                            allowClear: true,
                            width: '100%',
                            tags: true,

                        });

                        supplierDropdown.on('select2:open', function() {
                            $('.select2-search__field').attr('placeholder', 'Search Supplier');
                        });
                    },
                    error: function(xhr) {
                        console.error("API Error:", xhr.status, xhr.responseText);
                    }
                });
            }

            function fetchInventoryDetails(id) {
                $.ajax({
                    url: "{{ url('/api/inventoryy') }}/" + id,
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        console.log("Inventory:", response);

                        if (response) {
                            $('#inventoryId').val(response.id);
                            $('#item_name').val(response.item_name);
                            $('#quantity').val(response.quantity);
                            $('#purchase_date').val(response.purchase_date);
                            $('#status').val(response.status).trigger('change');

                            // ✅ Now that Select2 is ready, set the supplier_id
                            $('#supplier_id').val(response.supplier_id).trigger('change');
                        }
                    },
                    error: function(xhr) {
                        console.log("Error fetching inventory details:", xhr.responseText);
                    }
                });
            }



            // jQuery Validation
            $('#multiStepForm').validate({
                rules: {
                    item_name: {
                        required: true,
                        minlength: 2
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
                        minlength: "Item name must be at least 2 characters"
                    },
                    quantity: {
                        required: "Please enter the quantity",
                        number: "Please enter a valid number",
                        min: "Quantity must be greater than zero"
                    },
                    supplier_id: {
                        required: "Please select a supplier"
                    },
                    purchase_date: {
                        required: "Please select a purchase date",
                        date: "Please enter a valid date"
                    },
                    status: {
                        required: "Please select the status"
                    }
                },
                submitHandler: function(form) {
                    let inventoryId = $('#inventoryId').val();
                    let supplierId = $('#supplier_id option:selected').val();
                    let formData = {
                        item_name: $('#item_name').val(),
                        quantity: $('#quantity').val(),
                        supplier_id: supplierId,
                        purchase_date: $('#purchase_date').val(),
                        status: $('#status').val(),
                    };

                    $.ajax({
                        url: "{{ url('/api/inventoryy') }}/" + inventoryId,
                        type: "PUT",
                        data: JSON.stringify(formData),
                        contentType: "application/json",
                        dataType: "json",
                        headers: {
                            "Authorization": "Bearer " + token
                        },
                        success: function(response) {
                            $('#editinventorysuccessMessage').text(
                                'Inventory updated successfully!').show();
                            $('#errorMessage').hide();
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
                            $('#editinventoryerrorMessage').html(errorMessage).show();
                        }
                    });
                }
            });

            function getInventoryIdFromURL() {
                let url = window.location.href;
                let id = url.substring(url.lastIndexOf('/') + 1);
                return isNaN(id) ? null : id;
            }
        });
    </script>
@endsection
