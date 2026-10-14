@extends('layout.app')


<style>
    .filterSelect1 {
        width: 20px;
    }

    @media screen and (max-width: 767px) {
        .first-chart {
            padding-top: 30px !important;
        }

        .users {
            margin-top: 10px !important;
            font-size: 14px !important;
        }
    }

    .dash-widget2 {
        background-color: #fafafa !important;
        border: 1px solid #cfece0;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.3) !important;
    }

    .dash-widget-icon {
        font-size: 24px;
        color: #f89884;
    }

    .dash-widget-info {
        margin-top: 10px;
    }

    .widget-title2 {
        font-size: 16px;
        font-weight: 500;
        color: #6c757d;
    }

    .total-users-count {
        font-size: 24px;
        font-weight: 700;

    }
</style>





@section('content')
    <div class="page-wrapper">
        <div class="content">

            <div class="row">
                <div class="col-12">
                    <div class="card" style="height:auto">
                        <div class="card-header" style="background-color:#f89884;">
                            <h3 class="card-title d-inline-block text-white">
                                <i class="fa fa-flask px-2" style="font-size:20px"></i>Total Income
                            </h3>


                        </div>
                        <div class="card-body">


                            <div class="row mb-3 mx-auto">
                                <div class="col-md-4 col-sm-3 col-6 mt-2">
                                    <label>Year</label>
                                    <select id="filterYeardaily" class="form-control yearfilter select2 filter">
                                        <option value="">All</option>
                                        {{-- Options will be appended by JS --}}
                                    </select>
                                </div>
                                <div class="col-md-4 col-sm-3 col-6 mt-2">
                                    <label>Month</label>
                                    <select id="filterMonthdaily" class="selectmonth select2 form-control filter">
                                        <option></option> <!-- placeholder -->
                                        <option value="">All</option>
                                        @for ($m = 1; $m <= 12; $m++)
                                            <option value="{{ sprintf('%02d', $m) }}"
                                                {{ $m == \Carbon\Carbon::now()->month ? 'selected' : '' }}>
                                                {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>

                                <div class="col-md-4 col-sm-3 col-6 mt-2">
                                    <label>Date</label>
                                    <input type="date" id="filterDatedaily" class="form-control filter" />

                                </div>




                            </div>
                            @if (Auth::user()->role_id == '2' || Auth::user()->role_id == '1')
                                <div id="monthly-summary"></div>
                            @endif
                            <div class="table-responsive">
                                <table id="daily_datatbl" class="table custom-table">
                                    <thead style="background-color:#ff8e29;" class="text-center">
                                        <tr>
                                            <th>Date</th>

                                            
                                        </tr>
                                    </thead>

                                    <tbody id="daily_dataTableBody">
                                        <!-- Dynamic content -->
                                    </tbody>
                                </table>



                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6 col-lg-6 col-xl-6">

                    <div class="card" style="height:auto">
                        <div class="card-header" style="background-color:#f89884;">
                            <h3 class="card-title d-inline-block text-white">
                                <i class="fa fa-money px-2" style="font-size:20px"></i> Module Summary
                            </h3>

                        </div>

                        <div class="row mb-3 mt-3 filter " style="margin:inherit">


                            <div class=" col-md-6 col-sm-3 mb-2 px-2 position-relative">
                                <select id="filterMonthmodule" class="form-control select2 filter">
                                    <option value="">All Months</option>
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}">
                                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class=" col-md-6 col-sm-3 mb-2 px-2 position-relative">
                                <select id="filterYearmodule" class="form-control select2 filter">
                                    <option value="">All Years</option>
                                    @for ($y = date('Y'); $y >= 2000; $y--)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class=" col-md-6 col-sm-3 mb-2 px-2 position-relative m-auto">

                                <input type="date" id="filterDatemodule" class="form-control filter" />

                            </div>

                        </div>
                        <div class="card-body" style="height: auto">
                            <div class="row mb-3 mt-3" style="margin:inherit">
                                <!-- All Appointments Box -->
                                <div class="col-12 col-md-6 mb-2 px-2">
                                    <a href="{{ route('appointment.index') }}">
                                        <div class="dash-widget2 text-center">
                                            <div class="dash-widget-icon mb-2">
                                                <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                                            </div>
                                            <div class="dash-widget-info">
                                                <span class="widget-title2 d-block mb-2">Appointments</span>
                                                <h3 class="total-users-count text-dark">0</h3>
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <!-- All Patients Box -->
                                <div class="col-12 col-md-6 mb-2 px-2">
                                    <a href="{{ route('patients.index') }}">
                                        <div class="dash-widget2 text-center">
                                            <div class="dash-widget-icon mb-2">
                                                <i class="fa fa-user-md" aria-hidden="true"></i>

                                            </div>
                                            <div class="dash-widget-info">
                                                <span class="widget-title2 d-block mb-2">Patient</span>
                                                <h3 class="total-users-count text-dark">0</h3>
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <!-- All Treatment Bookings Box -->
                                <div class="col-12 col-md-6 mb-2 px-2">
                                    <a href="{{ route('treatment_booking.index') }}">
                                        <div class="dash-widget2 text-center">
                                            <div class="dash-widget-icon mb-2">
                                                <i class="fa fa-medkit" aria-hidden="true"></i>

                                            </div>
                                            <div class="dash-widget-info">
                                                <span class="widget-title2 d-block mb-2">Treatment Booking</span>
                                                <h3 class="total-users-count text-dark">0</h3>
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <!-- Followup Box -->
                                <div class="col-12 col-md-6 mb-2 px-2">
                                    <a href="{{ route('followup.index') }}">
                                        <div class="dash-widget2 text-center">
                                            <div class="dash-widget-icon mb-2">
                                                <i class="fa fa-phone" aria-hidden="true"></i>
                                            </div>
                                            <div class="dash-widget-info">
                                                <span class="widget-title2 d-block mb-2">Followup</span>
                                                <h3 class="total-users-count text-dark">0</h3>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>


                        </div>


                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-6 col-xl-6">
                    <div class="card" style="height: auto;">
                        <div class="card-header" style="background-color:#f89884;">
                            <h3 class="card-title d-inline-block text-white">
                                <i class="fa fa-money px-2" style="font-size:20px"></i> Expenses
                            </h3>
                            <button class="btn btn-rounded btn-hdr" id="exportButton"
                                style="background-color: #fed9cf;">
                                <i class="fa fa-download"></i> <span class="hdr-btn-text">Export</span>
                            </button>

                        </div>

                        <div class="row mb-3 mt-3 filter " style="margin:inherit">


                            <div class="col-12 col-md-6 mb-2 px-2 position-relative">
                                <select id="filterMonthexpense" class="form-control select2 filter">
                                    <option value="">All Months</option>
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}">
                                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                        </option>
                                    @endfor
                                </select>

                            </div>
                            <div class="col-12 col-md-6 mb-2 px-2 position-relative">
                                <select id="filterYearexpense" class="form-control select2 filter">
                                    <option value="">All Years</option>
                                    @for ($y = date('Y'); $y >= 2000; $y--)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>

                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="expensestbl" class="table custom-table">
                                    <thead style="background-color:#ff8e29;" class="text-center">
                                        <tr>


                                            <th>Date & time</th>
                                            <th>Amount</th>
                                            <th>Service</th>


                                        </tr>
                                    </thead>
                                    <tbody id="expenseTableBody">

                                    </tbody>


                                </table>
                            </div>
                        </div>
                        <div class="row my-2">
                            <div class="col-md-12 text-center">
                                <h5 style="margin:0;font-size:15px;color:black;font-weight:500">Grand Total: <span
                                        id="grandTotal">₹ 0.00</span></h5>

                            </div>
                        </div>

                    </div>
                </div>
            </div>




        </div>
    </div>

    <!-- Include Chart.js -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $('#filterMonthmodule').select2({
            placeholder: "Select Month",
            allowClear: true,
            width: '100%',

        });
        $('#filterMonthmodule').on('select2:open', function() {
            $('.select2-search__field').attr('placeholder', 'Search Month');
        });
        $('#filterYearmodule').select2({
            placeholder: "Select Year",
            width: '100%',
            allowClear: true
        });
        $('#filterYearmodule').on('select2:open', function() {
            $('.select2-search__field').attr('placeholder', 'Search Year');
        });
        $(document).ready(function() {
            function loadDashboardCounts() {
                let month = $('#filterMonthmodule').val();
                let year = $('#filterYearmodule').val();
                let date = $('#filterDatemodule').val();

                $.ajax({
                    url: '/api/report-counts',
                    type: 'GET',
                    data: {
                        month: month,
                        year: year,
                        date: date
                    },
                    success: function(response) {
                        $('.total-users-count').eq(0).text(response.appointments);
                        $('.total-users-count').eq(1).text(response.patients);
                        $('.total-users-count').eq(2).text(response.treatmentBookings);
                        $('.total-users-count').eq(3).text(response.followups);
                    },
                    error: function(err) {
                        console.error('Error fetching counts', err);
                    }
                });
            }

            // Load counts on page load
            loadDashboardCounts();

            // Trigger reload when filters change
            $('.filter').on('change', function() {
                loadDashboardCounts();
            });
        });
    </script>

    <!-- dailydata -->


    <script>
        // Click ANYWHERE on the group row to open/close its details
        $(document).on('click', 'tr.group-row', function(e) {
            // Don’t toggle if the click is on interactive controls
            if ($(e.target).closest(
                    'button, a, .exportButton, #addBtn, .make-payment-btn, .edit-daily_data, .delete-daily_data, .payment-history'
                ).length) {
                return;
            }

            // Get the target class from the caret icon in this row
            const targetClass = $(this).find('.toggle-group').data('target'); // e.g., "group-20250822"

            // IMPORTANT: details row uses a CLASS, so select with a dot:
            const $targetRow = $(`tr.${targetClass}`);

            // Toggle only THIS row
            $targetRow.toggleClass('d-none');

            // Flip the caret only for this row
            $(this).find('.toggle-group').toggleClass('fa-caret-right fa-caret-down');
        });

        $('.yearfilter').select2({

            width: '100%',
            placeholder: "Select Year",
            allowClear: true,
            minimumResultsForSearch: Infinity
        });
        $('.yearfilter').on('select2:open', function() {
            $('.select2-search__field').attr('placeholder', 'Search Year');
        });


        $('.selectmonth').select2({
            placeholder: "Select Month",
            allowClear: true,
            minimumResultsForSearch: Infinity,
            width: '100%'
        });
        $('.selectmonth').on('select2:open', function() {
            $('.select2-search__field').attr('placeholder', 'Search month');
        });


        $(document).on('click', '.exportButton', function() {
            let date = $(this).data('date'); // directly read from button
            if (!date) {
                console.error('Date not found on exportButton');
                return;
            }
            let formattedDate = date.replace(/^(\d{4})(\d{2})(\d{2})$/, '$1-$2-$3');
            window.location.href = `/export-daily-data/${formattedDate}`;
        });



        $(document).ready(function() {
            let branchId = localStorage.getItem('selectedBranchId');

            // Load years in filter
            $.ajax({
                url: '/api/daily-data/filters/years',
                type: 'GET',
               
                success: function(years) {
                    $('#filterYear').empty();
                    $('#filterYear').append('<option value="">All</option>'); // always first
                    years.forEach(function(year) {
                        $('#filterYear').append(`<option value="${year}">${year}</option>`);
                    });
                    $('#filterYear').trigger('change'); // refresh select2
                }
            });

            $('#filterPatient').select2({
                placeholder: "-- Select Patient --",
                allowClear: true,
                ajax: {
                    url: '/api/patients',
                    type: 'GET',
                    dataType: 'json',
                    delay: 250,
                    headers: {
                        "Authorization": "Bearer " + token
                    },
                    data: function(params) {
                        return {
                            search: params.term // search term sent to API
                        };
                    },
                    processResults: function(data) {
                        // Map patients
                        let results = data.patients.map(p => {
                            let name = p.fullname ?
                                p.fullname.charAt(0).toUpperCase() + p.fullname.slice(1) :
                                '';
                            return {
                                id: p.id,
                                text: name
                            };
                        });

                        results.unshift({
                            id: "",
                            text: "All"
                        });

                        return {
                            results: results
                        };
                    },
                    cache: true
                }
            });


            function ucfirst(str) {
                return str ? str.charAt(0).toUpperCase() + str.slice(1) : 'N/A';
            }

            $(document).on('click', '#daily_dataTableBody tr.group-row', function(e) {
                // If the click was on a button inside the row, ignore
                if ($(e.target).closest('button, a').length) return;

                // Get the date from the export button in this row
                let date = $(this).find('.exportButton').data('date');

                if (!date) {
                    console.error('Date not found for this row');
                    return;
                }

                // Redirect to daily_data.index with date as query
                let url = "{{ route('daily_data.index') }}";
                window.location.href = url;
            });


            function loadDailyData() {
                const year = $('#filterYeardaily').val();
                const month = $('#filterMonthdaily').val();
                const date = $('#filterDatedaily').val();
                let patientId = $("#filterPatient").val();

                $.ajax({
                    url: '/api/daily-data',
                    type: 'GET',
                    data: {
                        year,
                        month,
                        date,
                        patient_id: patientId,
                        branch_id: branchId
                    },
                    success: function(data) {
                        let userRoleId = {{ Auth::user()->role_id }};
                        if ($.fn.DataTable.isDataTable("#daily_datatbl")) {
                            $('#daily_datatbl').DataTable().destroy();
                        }
                        let tableBody = $("#daily_dataTableBody");
                        tableBody.empty();
                        let groupedData = {};
                        let monthlyTotals = {};

                        // Group by date + accumulate monthly
                        data.data.forEach(function(entry) {
                            const date = entry.date;
                            const monthKey = date.substring(0, 7); // "YYYY-MM"

                            // Daily
                            if (!groupedData[date]) {
                                groupedData[date] = {
                                    totalAmount: 0,
                                    receivedAmount: 0,
                                    pendingAmount: 0
                                };
                            }
                            groupedData[date].totalAmount += parseFloat(entry.amount || 0);
                            groupedData[date].receivedAmount += parseFloat(entry.received || 0);
                            groupedData[date].pendingAmount += parseFloat(entry.pending || 0);

                            // Monthly
                            if (!monthlyTotals[monthKey]) {
                                monthlyTotals[monthKey] = {
                                    totalAmount: 0,
                                    receivedAmount: 0,
                                    pendingAmount: 0
                                };
                            }
                            monthlyTotals[monthKey].totalAmount += parseFloat(entry.amount ||
                            0);
                            monthlyTotals[monthKey].receivedAmount += parseFloat(entry
                                .received || 0);
                            monthlyTotals[monthKey].pendingAmount += parseFloat(entry.pending ||
                                0);
                        });

                        // Render daily groups (simplified)
                        Object.keys(groupedData).forEach(function(date) {
                            let group = groupedData[date];
                            let today = new Date().toISOString().split("T")[0];
                            let addBtnHtml = "";
                            let amtColumn = '';

                            if (userRoleId == 1 || userRoleId == 2 || userRoleId == 4) {
                                amtColumn = `
                        <span class="amount mr-3">Total: ₹ ${group.totalAmount.toFixed(2)}</span>
                        <span class="amount mr-3">Received: ₹ ${group.receivedAmount.toFixed(2)}</span>
                        <span class="amount mr-3">Pending: ₹ ${group.pendingAmount.toFixed(2)}</span>
                        <button class="btn btn-rounded exportButton" data-date="${date}" style="background-color: #fed9cf;">
        <i class="fa fa-download"></i> <span class="hdr-btn-text">Export</span>
    </button>
                    `;
                            }



                            let groupRow = `
                    <tr class="group-row" style="background:#f8f9fa; font-weight:bold;">
                        <td>
                            <div class="d-flex justify-content-between align-items-center px-3 py-2">
                                <div>
                                    ${date}
                                </div>
                                <div class="summary" style="display: flex; justify-content: center; align-items: center; gap: 15px; flex-wrap: wrap;">
                                    ${amtColumn}
                                    <div class="action-buttons">
                                        ${addBtnHtml}
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                `;
                            tableBody.append(groupRow);
                        });

                        // Render monthly totals
                        $("#monthly-summary").empty();
                        Object.keys(monthlyTotals).forEach(function(monthKey) {
                            if (month && monthKey.split("-")[1] !== month.padStart(2, '0')) {
                                return;
                            }
                            let totals = monthlyTotals[monthKey];
                            let [y, m] = monthKey.split("-");
                            let dateObj = new Date(y, parseInt(m) - 1);
                            let monthName = dateObj.toLocaleString("en-US", {
                                month: "long",
                                year: "numeric"
                            });
                            let summaryRow = `
                    <div class="d-flex justify-content-center align-items-center">
                        <div class="alert alert-info"
                            style="background:transparent; font-weight:bold; border: 1px solid #cfece0;color: black;border-radius: 50px !important; font-size:14px;padding: 10px 26px !important;">
                            <div style="display:flex; gap:15px;">
                                <span>Total: ₹ ${totals.totalAmount.toFixed(2)}</span>
                                <span>Received: ₹ ${totals.receivedAmount.toFixed(2)}</span>
                                <span>Pending: ₹ ${totals.pendingAmount.toFixed(2)}</span>
                            </div>
                        </div>
                    </div>
                `;
                            $("#monthly-summary").append(summaryRow);
                        });

                        $('#daily_datatbl').DataTable({
                            paging: true,
                            searching: true,
                            ordering: false
                        });
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            }


            loadDailyData();

            // Filters change
            $('#filterYeardaily, #filterMonthdaily, #filterDatedaily, #filterPatient').on('change', function() {
                loadDailyData();
            });


        });
    </script>

    <!-- expense -->

    <script>
        $('#filterMonthexpense').select2({
            placeholder: "Select Month",
            allowClear: true,
            width: '100%',

        });
        $('#filterMonthexpense').on('select2:open', function() {
            $('.select2-search__field').attr('placeholder', 'Search Month');
        });
        $('#filterYearexpense').select2({
            placeholder: "Select Year",
            width: '100%',
            allowClear: true
        });
        $('#filterYearexpense').on('select2:open', function() {
            $('.select2-search__field').attr('placeholder', 'Search Year');
        });
        $(document).on('click', '#exportButton', function() {
            let branchId = localStorage.getItem("selectedBranchId");

            if (!branchId) {
                alert("Please select a branch first.");
                return;
            }

            window.location.href = "{{ route('expense.export') }}" + "?branch_id=" + branchId;
        });

        // Load filter options
        $.ajax({
            url: '/api/expenses/filters',
            type: 'GET',
            success: function(res) {
                // Year
                $('#filterYearexpense').empty().append('<option value="">All Years</option>');
                res.years.forEach(year => {
                    $('#filterYearexpense').append(`<option value="${year}">${year}</option>`);
                });



            }
        });



        $(document).ready(function() {
            let branchId = localStorage.getItem('selectedBranchId');

            function ucfirst(str) {
                return str ? str.charAt(0).toUpperCase() + str.slice(1) : 'N/A';
            }

            let expenseTable;

            function initExpenseTable() {
                if ($.fn.DataTable.isDataTable('#expensestbl')) {
                    return; // Already initialized
                }
                expenseTable = $('#expensestbl').DataTable({
                    destroy: true,
                    ajax: {
                        url: '/api/expenses',
                        data: function(d) {
                            d.month = $('#filterMonthexpense').val();
                            d.year = $('#filterYearexpense').val();
                            d.branch_id = branchId;
                        },
                        beforeSend: function() {
                            // Show loader row in tbody
                            $('#expenseTableBody').html(`
                        <tr>
                            <td colspan="6" class="text-center">
                                <span class="s"></span> Loading...
                            </td>
                        </tr>
                    `);
                        },
                        dataSrc: function(json) {
                            let grandTotal = 0;

                            if (json.data) {
                                grandTotal = json.data.reduce((sum, item) => sum + parseFloat(item
                                    .amount), 0);
                            }

                            $('#grandTotal').text(`₹ ${grandTotal.toFixed(2)}`);

                            return json.data;
                        }


                    },
                    columns: [{
                            data: 'date_time',
                            render: data => data ?? 'N/A'
                        },
                        {
                            data: 'amount',
                            render: data => `₹ ${parseFloat(data).toFixed(2)}`
                        },
                        {
                            data: 'service',
                            render: data => ucfirst(data)
                        },


                    ],
                    paging: true,
                    searching: true,
                    pageLength: 5,
                    ordering: true
                });
            }

            $(document).ready(function() {
                initExpenseTable();
            });


            // Filters
            $('#filterMonthexpense, #filterYearexpense').on('change', function() {
                expenseTable.ajax.reload(null, false);
            });

            // Event bindings
            $(document).off('click', '#exportButton, .delete-expense, .view-expense, .edit-expense');
        });
    </script>
@endsection
