@extends('layout.app')
<style>
    .card-footer {
        background-color: #87ceb0 !important;
    }

    .userProfile {
        width: 200px;
        height: 200px;
        object-fit: cover;
        /* or contain */
        border-radius: 20px;
    }

    @media screen and (max-width:767px) {

        .page-title {
            font-size: 18px !important;
        }

        .table-responsive-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            /* Smooth scroll on iOS */
        }

        .table-responsive-wrapper table {
            width: 600px;
            /* or more, depending on number of columns */
            min-width: 100%;
            display: block;
        }

    }
</style>
@section('content')
    <div class="page-wrapper">
        <div class="content" style="height:100vh">
            <div class="row" style="padding-top:15px">
                <div class="col-sm-8 col-8">
                    {{-- <h4 class="page-title" style="text-align:left;">
                        <i class="fa fa-pills icons"></i> Medicine Details
                    </h4> --}}
                </div>

            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card">
                        {{-- <div class="card-footer text-right">
                            <h3 style="float:left" class="text-dark">
                                <i class="fa fa-info-circle icon-style2"></i>
                                <span class="">Medicine Details</span>
                            </h3>
                        </div> --}}

                        <div class="card-footer text-right">
                            <h3 style="float:left" class="text-dark">
                                <i class="fa fa-info-circle icon-style2"></i>
                                <span class="">Medicine Details</span>
                            </h3>
                            <div class="d-flex text-right" style="justify-content: end;">
                                @if (app('hasPermission')(4, 'view'))
                                    <div class="col-sm-4 col-4 text-right m-b-2">
                                        <a href="{{ route('medicine.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                                            <i class="fa fa-arrow-left"></i> <span class="hdr-btn-text">Back</span></a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="card-body mt-3">





                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="row">
                                        <div class="col-lg-6 col-4 text-center margin-img">
                                            <p class="text-dark">
                                                <!-- <strong><i class="fa fa-image icon-style1 "></i> Image: </strong><br> -->
                                                <img id="medicine_image" class="userProfile" src=""
                                                    alt="Medicine Image">
                                            </p>
                                        </div>
                                        <div class="col-lg-6 col-8">
                                            <p class="text-dark margin-text">
                                                <strong><i class="fa fa-id-badge icon-style1"></i> Medicine: </strong>
                                                <span id="medicine_name"></span>
                                            </p>

                                            <hr class="margin-text">
                                            <p class="text-dark margin-text">
                                                <strong><i class="fa fa-cubes icon-style1"></i> Quantity: </strong>
                                                <span id="medicine_quantity"></span>
                                            </p>
                                            <hr class="margin-text">

                                            <p class="text-dark margin-text">
                                                <strong><i class="fas fa-tag icon-style1"></i> Medicine Unit: </strong>
                                                <span id="medicine_unit_display"></span>
                                            </p>
                                            <hr class="margin-text">

                                            <p class="text-dark margin-text">
                                                <strong><i class="fa fa-align-left icon-style1"></i> Category: </strong>
                                                <span id="medicine_category"></span>
                                            </p>
                                            <hr class="margin-text">
                                            <p class="text-dark margin-text">
                                                <strong>₹ Price : </strong>
                                                <span id="medicine_unit"></span>
                                            </p>
                                            <hr class="margin-text">
                                            <p class="text-dark margin-text">
                                                <strong><i class="fa fa-percent icon-style1"></i> GST Option:
                                                </strong>
                                                <span id="medicine_gst_option"></span>
                                            </p>
                                            <hr class="margin-text" id="gst_option_hr">

                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">

                                    <p class="text-dark margin-text">
                                        <strong><i class="fa fa-check-circle icon-style1"></i> Status: </strong>
                                        <span id="medicine_status"></span>
                                    </p>
                                    <hr class="margin-text">

                                    <p class="text-dark margin-text">
                                        <strong><i class="fa fa-align-left icon-style1"></i> Description: </strong>
                                        <span id="medicine_description"></span>
                                    </p>
                                    <hr class="margin-text">

                                    <p class="text-dark margin-text">
                                        <strong><i class="fa fa-calendar-plus icon-style1"></i> Manufacture Date:
                                        </strong>
                                        <span id="medicine_manufacture_date"></span>
                                    </p>
                                    <hr class="margin-text">

                                    <p class="text-dark margin-text">
                                        <strong><i class="fa fa-barcode icon-style1"></i> Batch Number:
                                        </strong>
                                        <span id="medicine_batch_no"></span>
                                    </p>
                                    <hr class="margin-text">



                                    <div id="product_gst_div" style="display: none;">
                                        <p class="text-dark margin-text">
                                            <strong><i class="fa fa-file-invoice-dollar icon-style1"></i> Product GST:
                                            </strong>
                                            <span id="medicine_product_gst"></span>
                                        </p>
                                        <hr class="margin-text">
                                    </div>

                                    <p class="text-dark margin-text">
                                        <strong><i class="fa fa-calendar-check icon-style1"></i> Expiry Date:
                                        </strong>
                                        <span id="medicine_expiry_date"></span>
                                    </p>
                                    <hr class="margin-text">
                                </div>
                            </div>


                            <div class="button mb-4" style="display: flex; justify-content: end; margin: 0 5px;">
                                @if (app('hasPermission')(4, 'update'))
                                    <a href="#" class="btn btn-primary btn-rounded btn-hdr edit-medicine-btn"
                                        style="color:black; margin-right:10px">
                                        <i class="fa fa-pencil-alt"></i> <span class="hdr-btn-text">Edit</span>
                                    </a>
                                @endif
                                @if (app('hasPermission')(4, 'delete'))
                                    <button type="button" class="btn btn-danger btn-rounded btn-hdr delete-medicine"
                                        data-id="{{ $medicine_id }}">
                                        <i class="fa fa-trash"></i> <span class="hdr-btn-text">Delete</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div id="successMessage" class="alert alert-success" style="display:none;"></div>


            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-footer text-right" style="background-color:#87ceb0">
                            <h3 style="float:left" class="text-dark">
                                <i class="fa fa-info-circle icon-style2 text-white"></i>
                                <span class="Patient_name">Medicine's History</span>
                            </h3>
                        </div>


                        <div class="card-body mt-3">
                            <div class="mt-3">
                                <!-- Medicine History Tab -->
                                <div id="medicinehistory1">
                                    <div class="table-responsive-wrapper">
                                        <table class="table table-bordered" id="medicinehistory">
                                            <thead>
                                                <tr>
                                                    <th>S. No.</th>
                                                    <th>Patient</th>
                                                    <th>Date</th>
                                                    <th>Category</th>
                                                    <th>Quantity</th>
                                                    <th>Price</th>
                                                </tr>
                                            </thead>
                                            <tbody id="medicineRecords">

                                            </tbody>
                                        </table>
                                    </div>
                                    <div id="no-medicine-message" class="alert alert-info text-center"
                                        style="display: none;">
                                        No medicine history available.
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
        </div>
    </div>

    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css">
    <script src=" https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>

    <script>
        $(document).ready(function() {
            var pathParts = window.location.pathname.split('/');
            var medicineId = pathParts[pathParts.length - 1];

            // Fetch Medicine Info + History
            $.ajax({
                url: '/api/medicines/' + medicineId,
                type: 'GET',
                success: function(data) {
                    console.log(data);

                    const med = data.medicine;

                    // Safe ucfirst function
                    function ucfirst(str) {
                        str = typeof str === 'string' ? str : '';
                        return str.charAt(0).toUpperCase() + str.slice(1);
                    }

                    // Main Medicine Info
                    $('#medicine_name').text(ucfirst(med.name));
                    $('#medicine_description').text(ucfirst(med.description));
                    $('#medicine_unit').text(med.unit);
                    $('#medicine_unit_display').text(med.medicine_unit ?? 'N/A');
                    $('#medicine_status').text(ucfirst(med.status));
                    $('#medicine_quantity').text(med.quantity);
                    $('#medicine_manufacture_date').text(med.manufacture_date);
                    $('#medicine_batch_no').text(med.batch_no ?? 'N/A');
                    const gstOption = med.gst_option ?? 'Without GST';
                    $('#medicine_gst_option').text(gstOption);

                    function normalizeGstValue(value) {
                        if (value === null || value === undefined || value === '') {
                            return [];
                        }
                        if (Array.isArray(value)) {
                            return value;
                        }
                        if (typeof value === 'object') {
                            return [value];
                        }
                        if (typeof value === 'string') {
                            try {
                                const parsed = JSON.parse(value);
                                if (Array.isArray(parsed)) {
                                    return parsed;
                                }
                                if (typeof parsed === 'object' && parsed !== null) {
                                    return [parsed];
                                }
                            } catch (e) {
                                // Not JSON, fall through
                            }
                            return value.split(',').map(function(v) {
                                return v.trim();
                            }).filter(Boolean);
                        }
                        return [String(value)];
                    }

                    function formatGstDisplay(items, unitValue) {
                        if (!items || items.length === 0) {
                            return '';
                        }
                        const unitNum = parseFloat(unitValue);
                        return items.map(function(item) {
                            if (typeof item === 'object' && item !== null) {
                                let name = item.tax_name || 'GST';
                                let rate = item.tax_rate || '0';
                                let amount = item.tax_amount || 0;
                                return `${name} (${parseFloat(rate).toFixed(2)}%) = ${parseFloat(amount).toFixed(2)}`;
                            }
                            const text = String(item);
                            const match = text.match(/(\d+(\.\d+)?)%/);
                            if (match && Number.isFinite(unitNum)) {
                                const rate = parseFloat(match[1]);
                                const amount = (unitNum * rate) / 100;
                                return `${text} = ${amount.toFixed(2)}`;
                            }
                            return text;
                        }).join(', ');
                    }

                    if (String(gstOption).toLowerCase() === 'with gst') {
                        const gstItems = normalizeGstValue(med.product_gst);
                        const gstText = formatGstDisplay(gstItems, med.unit) || 'N/A';
                        $('#medicine_product_gst').text(gstText);
                        $('#product_gst_div').show();
                    } else {
                        $('#product_gst_div').hide();
                    }
                    $('#medicine_expiry_date').text(med.expiry_date);

                    // Check for med.category and med.category.name
                    const categoryName = med.category && med.category.name ? med.category.name : '';
                    $('#medicine_category').text(ucfirst(categoryName));

                    // Safe image handling
                    if (med.image) {
                        $('#medicine_image').attr('src', med.image).show();
                    } else {
                        $('#medicine_image').hide(); // Or use a default image if needed
                    }

                    $(".edit-medicine-btn").attr("href", "/medicine/edit/" + med.id);

                    // Populate Medicine History
                    const history = data.history;
                    let html = '';

                    if (history.length > 0) {
                        history.forEach((row, index) => {
                            html += `<tr>
                                                    <td>${index + 1}</td>
                                                    <td>${row.patient_name}</td>
                                                    <td>${row.date}</td>
                                                    <td>${row.category}</td>
                                                    <td>${row.quantity}</td>
                                                    <td>${row.price}</td>
                                                </tr>`;
                        });

                        document.getElementById("medicineRecords").innerHTML = html;
                        document.getElementById("medicinehistory").style.display = "table";
                        document.getElementById("no-medicine-message").style.display = "none";

                        $('#medicinehistory').DataTable({
                            "responsive": true,
                            "paging": true,
                            "searching": true,
                            "ordering": true
                        });
                    } else {
                        document.getElementById("medicinehistory").style.display = "none";
                        document.getElementById("no-medicine-message").style.display = "block";
                    }
                },
                error: function() {
                    alert('Failed to fetch Medicine details.');
                }
            });
        });
    </script>

    <script>
        $(document).on('click', '.delete-medicine', function() {
            var medicineId = $(this).data('id');

            if (!medicineId) {
                Swal.fire('Error', 'Medicine ID not found!', 'error');
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
                        url: '/api/medicines/' + medicineId,
                        type: 'DELETE',
                        success: function(response) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Medicine deleted successfully!',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload(); // Reload the page to update the list
                            });
                        },
                        error: function(xhr) {
                            if (xhr.status === 409) {
                                Swal.fire('Cannot Delete', xhr.responseJSON.message, 'warning');
                            } else {
                                console.log(xhr.responseText);
                                Swal.fire('Error',
                                    ' Please remove medicine from patient medicine records first.',
                                    'error');
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
            /* background-color: white; */
            color: white;
            padding: 5px;
            font-size: 20px;
            border-radius: 50%;
        }
    </style>
@endsection

