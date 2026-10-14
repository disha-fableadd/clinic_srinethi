@extends('layout.app')

<style>
    .card-footer{
        background-color:#87ceb0 !important;
    }
     @media screen and (max-width: 767px) {


        .page-title {
            font-size: 18px !important;
            margin-top: 10px !important;
        }

       



    }
</style>

@section('content')
    <div class="page-wrapper">
        <div class="content" style="height:100vh">
            <div class="row" style="padding-top:15px">
                <div class="col-sm-8 col-8">
                    <h4 class="page-title" style="text-align:left;">
                        <i class="fa fa-box"></i> Inventory Details
                    </h4>
                </div>
                @if(app('hasPermission')(11, 'view'))
                    <div class="col-sm-4 col-4 text-right m-b-2">
                        <a href="{{ route('inventory.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                            <i class="fa fa-arrow-left"></i> <span class="hdr-btn-text">Back</span> 
                        </a>
                    </div>
                @endif
            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-footer text-right">
                            <h3 style="float:left" class="text-dark">
                                <i class="fa fa-info-circle icon-style2"></i> Inventory Details
                            </h3>
                        </div>

                        <div class="card-body mt-3">
                            <p class="text-dark"><strong><i class="fa fa-box-open icon-style1"></i> Item Name: </strong>
                                <span class="inventory_name"></span>
                            </p>

                            <hr>
                            <p class="text-dark"><strong><i class="fa fa-layer-group icon-style1"></i> Quantity: </strong>
                                <span id="inventory_quantity"></span>
                            </p>
                            <hr>
                            <p class="text-dark"><strong><i class="fa fa-industry icon-style1"></i> Supplier: </strong>
                                <span id="supplier_name"></span>
                            </p>
                            <hr>
                            <p class="text-dark"><strong><i class="fa fa-calendar-plus icon-style1"></i> Purchase Date:
                                </strong>
                                <span id="purchase_date"></span>
                            </p>
                            <hr>
                            <p class="text-dark"><strong><i class="fa fa-tag icon-style1"></i> Status: </strong>
                                <span id="inventory_status"></span>
                            </p>

                            <div class="button mb-4" style="display: flex; justify-content: end; margin: 0 5px;">
                                @if(app('hasPermission')(11, 'update'))
                                    <a href="#" class="btn btn-primary btn-rounded btn-hdr edit-inventory-btn"
                                        style="color:black; margin-right:10px">
                                        <i class="fa fa-pencil-alt"></i> <span class="hdr-btn-text">Edit</span> 
                                    </a>
                                @endif

                                @if(app('hasPermission')(11, 'delete'))
                                    <button type="button" class="btn btn-danger btn-rounded btn-hdr delete-inventory"
                                        data-id="{{ $inventory_id }}">
                                        <i class="fa fa-trash"></i> <span class="hdr-btn-text">Delete</span> 
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="successMessage" class="alert alert-success" style="display:none;"></div>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
        </div>
    </div>

    <script>

        $(document).ready(function () {
            var inventoryId = "{{ $inventory_id }}";

            $.ajax({
                url: '/api/inventoryy/' + inventoryId,
                type: 'GET',
                dataType: 'json',
                headers: { "Authorization": "Bearer " + token },
                success: function (inventory) {
                    function ucfirst(str) {
                        return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
                    }

                    $('.inventory_name').text(ucfirst(inventory.item_name));
                    $('#inventory_quantity').text(inventory.quantity);
                    $('#supplier_name').text(ucfirst(inventory.supplier.name));
                    $('#purchase_date').text(inventory.purchase_date);
                    $('#inventory_status').text(ucfirst(inventory.status));


                    $(".edit-inventory-btn").attr("href", "/inventory/edit/" + inventory.id);
                },
                error: function (xhr) {
                    if (xhr.status === 401) {
                        window.location.href = "{{ route('login') }}";
                    }
                }
            });
        });

        $(document).on('click', '.delete-inventory', function () {
            var inventoryId = $(this).data('id');
            console.log("Deleting Inventory ID:", inventoryId);

            if (!inventoryId) {
                Swal.fire('Error', 'Inventory ID is missing!', 'error');
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
                        url: '/api/inventoryy/' + inventoryId,
                        type: 'DELETE',
                        headers: { "Authorization": "Bearer " + token },
                        success: function (response) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Inventory deleted successfully!',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                $('button[data-id="' + inventoryId + '"]').closest('tr').remove();
                                setTimeout(function () {
                                    window.location.href = "{{ route('inventory.index') }}";
                                }, 2000);
                            });
                        },
                        error: function (xhr) {
                            if (xhr.status === 401) {
                                Swal.fire('Unauthorized', 'Your session has expired. Please login again.', 'warning').then(() => {
                                    window.location.href = "{{ route('login') }}";
                                });
                            } else {
                                Swal.fire('Error', 'Failed to delete inventory item.', 'error');
                            }
                        }
                    });
                }
            });
        });

    </script>

    <style>
        .icon-style1 {
            background-color: white;
            color: rgb(157 195 179);
            padding: 5px;
            font-size: 20px;
            border-radius: 50%;
        }

        .icon-style2 {
            color: white;
            padding: 5px;
            font-size: 20px;
            border-radius: 50%;
        }
    </style>
@endsection