@extends('layout.app')

<style>
    :root {
        --theme-mint-soft: #cfece0;
        --theme-mint-dark: #26846b;
        --theme-coral-strip: #f89884;
        --theme-coral-dark: #e07b66;
        --card-bg: #ffffff;
        --border-color: #eaf3ef;
        --text-dark: #2c3e50;
        --text-muted: #7f8c8d;
    }

    /* Top Strip matching the uploaded Coral Header Reference */
    .report-top-banner {
        background: linear-gradient(90deg, #f89884 0%, #faaa99 100%);
        border-radius: 16px;
        padding: 22px 28px;
        margin-bottom: 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        box-shadow: 0 4px 15px rgba(248, 152, 132, 0.25);
        color: #ffffff;
    }

    .report-title-wrap h3 {
        font-size: 23px;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .report-title-wrap p {
        color: rgba(255, 255, 255, 0.92);
        font-size: 13.5px;
        margin: 0;
    }

    .report-filter-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .filter-btn-group .btn {
        border-radius: 10px;
        padding: 7px 16px;
        font-size: 13px;
        font-weight: 600;
        border: 1px solid rgba(255, 255, 255, 0.4);
        background: rgba(255, 255, 255, 0.2);
        color: #ffffff;
        transition: all 0.2s ease;
    }

    .filter-btn-group .btn:hover {
        background: rgba(255, 255, 255, 0.35);
        color: #fff;
    }

    .filter-btn-group .btn.active {
        background: #ffffff !important;
        color: #e07b66 !important;
        border-color: #ffffff !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .btn-export-rep {
        background: #ffffff;
        color: #26846b !important;
        border-radius: 10px;
        padding: 8px 18px;
        font-size: 13.5px;
        font-weight: 700;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        transition: all 0.2s ease;
    }

    .btn-export-rep:hover {
        background: #cfece0;
        color: #1a624f !important;
        transform: translateY(-1px);
    }

    /* Metric Stat Card - Beautiful Mint & Coral Touch */
    .rep-stat-card {
        background: #ffffff;
        border: 1px solid #e7f5ef;
        border-radius: 16px;
        padding: 20px 22px;
        margin-bottom: 22px;
        position: relative;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        box-shadow: 0 4px 14px rgba(38, 132, 107, 0.04);
        height: calc(100% - 22px);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .rep-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(248, 152, 132, 0.12);
        border-color: #cfece0;
    }

    .rep-stat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .rep-stat-title {
        font-size: 13.5px;
        font-weight: 600;
        color: #64748b;
        margin: 0;
    }

    .rep-stat-icon-wrap {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }

    .icon-mint {
        color: #26846b;
        background: #cfece0;
    }

    .icon-coral {
        color: #e07b66;
        background: #fee9e4;
    }

    .rep-stat-number {
        font-size: 26px;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
        line-height: 1.2;
    }

    .rep-watermark-icon {
        position: absolute;
        right: -10px;
        bottom: -15px;
        font-size: 78px;
        color: rgba(38, 132, 107, 0.04);
        pointer-events: none;
    }

    /* Container Cards */
    .rep-box-card {
        background: #ffffff;
        border: 1px solid #edf6f2;
        border-radius: 18px;
        padding: 24px;
        margin-bottom: 25px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.02);
    }

    .rep-box-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .rep-box-header h4 {
        font-size: 17px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 3px;
    }

    .rep-box-header p {
        font-size: 13px;
        color: #94a3b8;
        margin: 0;
    }

    /* Revenue Summary Strip (Mint + Coral Accents) */
    .revenue-summary-strip {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        margin-bottom: 22px;
        background: #fbfdfc;
        border: 1px solid #cfece0;
        border-radius: 14px;
        padding: 16px 22px;
    }

    .rev-pill {
        flex: 1;
        min-width: 120px;
    }

    .rev-pill span {
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.5px;
        display: block;
        margin-bottom: 4px;
    }

    .rev-pill h5 {
        font-size: 19px;
        font-weight: 700;
        margin: 0;
    }

    .color-mint { color: #26846b !important; }
    .color-coral { color: #f89884 !important; }

    /* Status Mini Tiles */
    .status-summary-tile {
        background: #f8fbf9;
        border: 1px solid #e7f4ee;
        border-radius: 14px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 12px;
    }

    .status-tile-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .status-tile-info h5 {
        font-size: 18px;
        font-weight: 700;
        margin: 0;
        color: #1e293b;
    }

    .status-tile-info p {
        font-size: 12.5px;
        color: #64748b;
        margin: 0;
    }

    /* Progress bar */
    .rep-progress {
        height: 9px;
        border-radius: 10px;
        background: #f1f5f9;
        overflow: hidden;
        margin-top: 6px;
    }

    .rep-progress-bar-mint {
        background: linear-gradient(90deg, #26846b, #48b899);
        height: 100%;
        border-radius: 10px;
    }

    .rep-progress-bar-coral {
        background: linear-gradient(90deg, #f89884, #faaa99);
        height: 100%;
        border-radius: 10px;
    }

    /* Tables with Theme Headers */
    .rep-table-wrap {
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid #e6f3ed;
    }

    .rep-table {
        margin: 0;
        width: 100%;
        border-collapse: separate;
    }

    .rep-table thead th {
        background: #cfece0;
        color: #1e293b;
        font-size: 12.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 13px 18px;
        border: none;
    }

    .rep-table tbody td {
        padding: 13px 18px;
        font-size: 13.5px;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .rep-table tbody tr:hover {
        background-color: #f7fcf9;
    }

    .badge-pill-custom {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }

    .badge-paid {
        background: #cfece0;
        color: #1a624f;
    }

    .badge-pending {
        background: #fee9e4;
        color: #d9534f;
    }

    .badge-failed {
        background: #f1f5f9;
        color: #64748b;
    }

    .view-all-link {
        font-size: 13.5px;
        font-weight: 600;
        color: #26846b;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .view-all-link:hover {
        color: #f89884;
        text-decoration: underline;
    }

    /* Table Pagination Styling */
    .rep-pagination-wrap {
        padding-top: 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }

    .rep-pagination-info {
        font-size: 13px;
        font-weight: 500;
        color: #64748b;
    }

    .rep-pagination-info strong {
        color: #1e293b;
        font-weight: 600;
    }

    .rep-pagination {
        display: flex;
        align-items: center;
        gap: 6px;
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .rep-pagination .page-item {
        margin: 0;
    }

    .rep-pagination .page-link {
        border-radius: 8px !important;
        border: 1px solid #e2e8f0;
        color: #475569;
        font-size: 13px;
        font-weight: 600;
        padding: 6px 12px;
        min-width: 34px;
        height: 34px;
        text-align: center;
        background: #ffffff;
        transition: all 0.2s ease;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }

    .rep-pagination .page-link:hover:not(.disabled) {
        background: #cfece0;
        color: #26846b;
        border-color: #26846b;
    }

    .rep-pagination .page-item.active .page-link {
        background: #26846b !important;
        border-color: #26846b !important;
        color: #ffffff !important;
        box-shadow: 0 2px 6px rgba(38, 132, 107, 0.25);
    }

    .rep-pagination .page-item.disabled .page-link {
        color: #cbd5e1 !important;
        cursor: not-allowed;
        background: #f8fafc;
        border-color: #f1f5f9;
        pointer-events: none;
    }
</style>

@section('content')
<div class="page-wrapper">
    <div class="content">

        <!-- Top Header with Coral & Mint Color Combination -->
        <div class="report-top-banner">
            <div class="report-title-wrap">
                <h3><i class="fas fa-chart-line"></i> Clinic Reports & Analytics</h3>
                <p>Monitor patient registrations, doctor performance, appointment flow, and clinic revenue in real time.</p>
            </div>
            <div class="report-filter-wrap">
                <div class="btn-group filter-btn-group" role="group">
                    <button type="button" class="btn period-filter-btn" data-filter="today">Today</button>
                    <button type="button" class="btn period-filter-btn" data-filter="week">Weekly</button>
                    <button type="button" class="btn period-filter-btn active" data-filter="month">Monthly</button>
                    <button type="button" class="btn period-filter-btn" data-filter="year">Yearly</button>
                   
                </div>
                
            </div>
        </div>

        <!-- 8 Stat Metric Boxes with Mint & Coral Palette -->
        <div class="row">
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="rep-stat-card">
                    <div class="rep-stat-header">
                        <span class="rep-stat-title">Total Patients</span>
                        <div class="rep-stat-icon-wrap icon-mint"><i class="fas fa-user-injured"></i></div>
                    </div>
                    <h3 class="rep-stat-number" id="cardTotalPatients">0</h3>
                    <i class="fas fa-user-injured rep-watermark-icon"></i>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="rep-stat-card">
                    <div class="rep-stat-header">
                        <span class="rep-stat-title">Doctors & Staff</span>
                        <div class="rep-stat-icon-wrap icon-coral"><i class="fas fa-user-md"></i></div>
                    </div>
                    <h3 class="rep-stat-number" id="cardTotalDoctors">0</h3>
                    <i class="fas fa-user-md rep-watermark-icon"></i>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="rep-stat-card">
                    <div class="rep-stat-header">
                        <span class="rep-stat-title">Appointments (Period)</span>
                        <div class="rep-stat-icon-wrap icon-mint"><i class="fas fa-calendar-check"></i></div>
                    </div>
                    <h3 class="rep-stat-number" id="cardPeriodAppointments">0</h3>
                    <i class="fas fa-calendar-check rep-watermark-icon"></i>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="rep-stat-card">
                    <div class="rep-stat-header">
                        <span class="rep-stat-title">Follow-ups Scheduled</span>
                        <div class="rep-stat-icon-wrap icon-coral"><i class="fas fa-clipboard-check"></i></div>
                    </div>
                    <h3 class="rep-stat-number" id="cardPeriodFollowups">0</h3>
                    <i class="fas fa-clipboard-check rep-watermark-icon"></i>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="rep-stat-card">
                    <div class="rep-stat-header">
                        <span class="rep-stat-title">Active Treatments</span>
                        <div class="rep-stat-icon-wrap icon-mint"><i class="fas fa-stethoscope"></i></div>
                    </div>
                    <h3 class="rep-stat-number" id="cardTotalTreatments">0</h3>
                    <i class="fas fa-stethoscope rep-watermark-icon"></i>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="rep-stat-card">
                    <div class="rep-stat-header">
                        <span class="rep-stat-title">Reference Doctors</span>
                        <div class="rep-stat-icon-wrap icon-coral"><i class="fas fa-hospital-user"></i></div>
                    </div>
                    <h3 class="rep-stat-number" id="cardTotalRefDoctors">0</h3>
                    <i class="fas fa-hospital-user rep-watermark-icon"></i>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="rep-stat-card">
                    <div class="rep-stat-header">
                        <span class="rep-stat-title">Total Revenue</span>
                        <div class="rep-stat-icon-wrap icon-mint"><i class="fas fa-rupee-sign"></i></div>
                    </div>
                    <h3 class="rep-stat-number color-mint" id="cardTotalRevenue">₹ 0</h3>
                    <i class="fas fa-rupee-sign rep-watermark-icon"></i>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="rep-stat-card">
                    <div class="rep-stat-header">
                        <span class="rep-stat-title">Invoices / Billings</span>
                        <div class="rep-stat-icon-wrap icon-coral"><i class="fas fa-file-invoice-dollar"></i></div>
                    </div>
                    <h3 class="rep-stat-number color-coral" id="cardTotalTransactions">0</h3>
                    <i class="fas fa-file-invoice-dollar rep-watermark-icon"></i>
                </div>
            </div>
        </div>

        <!-- Revenue Overview Chart Section (Coral & Mint accents) -->
        <div class="row">
            <div class="col-12">
                <div class="rep-box-card">
                    <div class="rep-box-header">
                        <div>
                            <h4>Revenue Overview</h4>
                            <p>Track your billing trends and clinic transaction performance</p>
                        </div>
                    </div>

                    <div class="revenue-summary-strip">
                        <div class="rev-pill">
                            <span>Total Revenue</span>
                            <h5 class="text-dark" id="stripTotalRevenue">₹ 0</h5>
                        </div>
                        <div class="rev-pill">
                            <span>Paid Revenue</span>
                            <h5 class="color-mint" id="stripPaidInvoices">₹ 0</h5>
                        </div>
                        <div class="rev-pill">
                            <span>Pending Revenue</span>
                            <h5 class="color-coral" id="stripPendingInvoices">₹ 0</h5>
                        </div>
                        <div class="rev-pill">
                            <span>Paid Invoices Count</span>
                            <h5 class="text-secondary" id="stripCompletedCount">0</h5>
                        </div>
                    </div>

                    <div style="position: relative; height: 320px; width: 100%;">
                        <canvas id="revenueTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Breakdown Doughnut + Clinic Progress Bars -->
        <div class="row">
            <div class="col-lg-6 col-md-12">
                <div class="rep-box-card" style="height: calc(100% - 25px);">
                    <div class="rep-box-header">
                        <div>
                            <h4>Appointment Status Distribution</h4>
                            <p>Overview of appointment progress in selected period</p>
                        </div>
                    </div>
                    <div class="row align-items-center">
                        <div class="col-sm-6 text-center">
                            <div style="max-height: 220px; position: relative;">
                                <canvas id="statusDoughnutChart"></canvas>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="status-summary-tile">
                                <div class="status-tile-icon" style="background:#cfece0; color:#26846b;">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="status-tile-info">
                                    <h5 id="countCompletedApp">0</h5>
                                    <p>Completed Appointments</p>
                                </div>
                            </div>

                            <div class="status-summary-tile">
                                <div class="status-tile-icon" style="background:#fee9e4; color:#e07b66;">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="status-tile-info">
                                    <h5 id="countUpcomingApp">0</h5>
                                    <p>Upcoming / Confirmed</p>
                                </div>
                            </div>

                            <div class="status-summary-tile">
                                <div class="status-tile-icon" style="background:#f1f5f9; color:#64748b;">
                                    <i class="fas fa-times-circle"></i>
                                </div>
                                <div class="status-tile-info">
                                    <h5 id="countCancelledApp">0</h5>
                                    <p>Cancelled</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-12">
                <div class="rep-box-card" style="height: calc(100% - 25px);">
                    <div class="rep-box-header">
                        <div>
                            <h4>Clinic Activity Analysis</h4>
                            <p>Summary of patient care and treatment volume</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <div class="status-summary-tile" style="margin: 0;">
                                <div class="status-tile-icon" style="background:#cfece0; color:#26846b;">
                                    <i class="fas fa-user-injured"></i>
                                </div>
                                <div class="status-tile-info">
                                    <h5 id="tilePatientsCount">0</h5>
                                    <p>Registered Patients</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <div class="status-summary-tile" style="margin: 0;">
                                <div class="status-tile-icon" style="background:#fee9e4; color:#e07b66;">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div class="status-tile-info">
                                    <h5 id="tileFollowupsCount">0</h5>
                                    <p>Follow-up Visits</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span style="font-size: 13.5px; font-weight: 600; color: #475569;">Appointment Completion Rate</span>
                            <span style="font-size: 13.5px; font-weight: 700; color: #26846b;" id="completionRateLabel">0%</span>
                        </div>
                        <div class="rep-progress">
                            <div class="rep-progress-bar-mint" id="completionProgressBar" style="width: 0%;"></div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span style="font-size: 13.5px; font-weight: 600; color: #475569;">Payment Collection Rate</span>
                            <span style="font-size: 13.5px; font-weight: 700; color: #e07b66;" id="collectionRateLabel">0%</span>
                        </div>
                        <div class="rep-progress">
                            <div class="rep-progress-bar-coral" id="collectionProgressBar" style="width: 0%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table 1: Treatment Performance Report -->
        <div class="row">
            <div class="col-12">
                <div class="rep-box-card">
                    <div class="rep-box-header">
                        <div>
                            <h4>Treatment Performance Report</h4>
                            <p>Breakdown of clinic treatments, assigned doctors, and appointment counts</p>
                        </div>
                        <a href="{{ route('treatment.index') }}" class="view-all-link">
                            View All <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                    <div class="rep-table-wrap table-responsive">
                        <table class="table rep-table">
                            <thead>
                                <tr>
                                    <th>Treatment Name</th>
                                    <th>Doctor In-Charge</th>
                                    <th>Standard Fee</th>
                                    <th>Appointments Booked</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="treatmentReportTableBody">
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Loading treatment data...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination for Treatment Performance -->
                    <div class="rep-pagination-wrap" id="treatmentPaginationWrap">
                        <div class="rep-pagination-info" id="treatmentPaginationInfo"></div>
                        <ul class="pagination rep-pagination" id="treatmentPagination"></ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table 2: Recent Transactions / Invoices Report -->
        <div class="row">
            <div class="col-12">
                <div class="rep-box-card">
                    <div class="rep-box-header">
                        <div>
                            <h4>Recent Transaction Report</h4>
                            <p>Filterable view of patient billings and payment statuses</p>
                        </div>
                        <a href="{{ route('invoice.index') }}" class="view-all-link">
                            View All <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                    <div class="rep-table-wrap table-responsive">
                        <table class="table rep-table">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Patient Name</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Payment Method</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="transactionReportTableBody">
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Loading transaction data...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination for Recent Transactions -->
                    <div class="rep-pagination-wrap" id="transactionPaginationWrap">
                        <div class="rep-pagination-info" id="transactionPaginationInfo"></div>
                        <ul class="pagination rep-pagination" id="transactionPagination"></ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    let revenueChartInstance = null;
    let statusDoughnutChartInstance = null;
    let activeFilter = 'month';

    function getSelectedBranchId() {
        return localStorage.getItem('selectedBranchId') || '';
    }

    function formatCurrency(amount) {
        let val = Number(amount) || 0;
        return '₹ ' + val.toLocaleString('en-IN');
    }

    function loadReportData(filter = 'month') {
        const token = localStorage.getItem('token') || '';
        const branchId = getSelectedBranchId();

        let headers = {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        };
        if (token) {
            headers['Authorization'] = 'Bearer ' + token;
        }

        $.ajax({
            url: "{{ url('/api/report-overview') }}",
            type: "GET",
            dataType: "json",
            headers: headers,
            data: {
                filter: filter,
                branch_id: branchId
            },
            success: function(res) {
                renderMetrics(res.metrics);
                renderRevenueTrend(res.revenue_chart);
                renderStatusDoughnut(res.appointment_distribution);
                renderTreatmentTable(res.treatment_performance);
                renderTransactionTable(res.recent_transactions);
            },
            error: function(xhr, status, error) {
                console.error("Error loading report data:", xhr.responseText || error);
            }
        });
    }

    function renderMetrics(m) {
        if (!m) return;

        $('#cardTotalPatients').text(m.total_patients ?? 0);
        $('#cardTotalDoctors').text(m.total_doctors ?? 0);
        $('#cardPeriodAppointments').text(m.total_appointments ?? 0);
        $('#cardPeriodFollowups').text(m.total_followups ?? 0);
        $('#cardTotalTreatments').text(m.total_treatments ?? 0);
        $('#cardTotalRefDoctors').text(m.total_reference_doctors ?? 0);
        $('#cardTotalRevenue').text(formatCurrency(m.total_revenue));
        $('#cardTotalTransactions').text(m.total_transactions ?? 0);

        $('#stripTotalRevenue').text(formatCurrency(m.total_revenue));
        $('#stripPaidInvoices').text(formatCurrency(m.paid_revenue));
        $('#stripPendingInvoices').text(formatCurrency(m.pending_revenue));
        $('#stripCompletedCount').text(m.paid_transactions ?? 0);

        $('#countCompletedApp').text(m.completed_appointments ?? 0);
        $('#countUpcomingApp').text(m.upcoming_appointments ?? 0);
        $('#countCancelledApp').text(m.cancelled_appointments ?? 0);

        $('#tilePatientsCount').text(m.total_patients ?? 0);
        $('#tileFollowupsCount').text(m.total_followups ?? 0);

        // Completion Rate
        let totalApps = Number(m.total_appointments) || 0;
        let completedApps = Number(m.completed_appointments) || 0;
        let compRate = totalApps > 0 ? Math.round((completedApps / totalApps) * 100) : 0;
        $('#completionRateLabel').text(compRate + '%');
        $('#completionProgressBar').css('width', compRate + '%');

        // Payment Collection Rate
        let totalRev = Number(m.total_revenue) || 0;
        let paidRev = Number(m.paid_revenue) || 0;
        let collRate = totalRev > 0 ? Math.round((paidRev / totalRev) * 100) : 0;
        $('#collectionRateLabel').text(collRate + '%');
        $('#collectionProgressBar').css('width', collRate + '%');
    }

    function renderRevenueTrend(chartData) {
        const ctx = document.getElementById('revenueTrendChart').getContext('2d');
        if (revenueChartInstance) {
            revenueChartInstance.destroy();
        }

        let gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(248, 152, 132, 0.45)');
        gradient.addColorStop(0.6, 'rgba(207, 236, 224, 0.25)');
        gradient.addColorStop(1, 'rgba(255, 255, 255, 0.0)');

        revenueChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData?.labels || [],
                datasets: [{
                    label: 'Revenue (₹)',
                    data: chartData?.values || [],
                    borderColor: '#f89884',
                    backgroundColor: gradient,
                    borderWidth: 2.8,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#26846b',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ' Revenue: ₹ ' + Number(context.parsed.y).toLocaleString('en-IN');
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            callback: function(val) {
                                return '₹' + val.toLocaleString('en-IN');
                            }
                        }
                    }
                }
            }
        });
    }

    function renderStatusDoughnut(dist) {
        const ctx = document.getElementById('statusDoughnutChart').getContext('2d');
        if (statusDoughnutChartInstance) {
            statusDoughnutChartInstance.destroy();
        }

        let completed = dist?.completed || 0;
        let upcoming = dist?.upcoming || 0;
        let cancelled = dist?.cancelled || 0;

        let chartData = [completed, upcoming, cancelled];
        let bgColors = ['#26846b', '#f89884', '#cbd5e1'];
        if (completed === 0 && upcoming === 0 && cancelled === 0) {
            chartData = [1];
            bgColors = ['#cfece0'];
        }

        statusDoughnutChartInstance = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'Upcoming / Confirmed', 'Cancelled'],
                datasets: [{
                    data: chartData,
                    backgroundColor: bgColors,
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }

    let allTreatmentsData = [];
    let treatmentPage = 1;
    const treatmentPageSize = 5;

    let allTransactionsData = [];
    let transactionPage = 1;
    const transactionPageSize = 5;

    function renderPaginationControls(paginationId, infoId, wrapId, currentPage, totalItems, pageSize, onPageChange) {
        const $pagination = $('#' + paginationId);
        const $info = $('#' + infoId);
        const $wrap = $('#' + wrapId);

        $pagination.empty();

        if (!totalItems || totalItems === 0) {
            $info.html('Showing <strong>0</strong> of <strong>0</strong> entries');
            $wrap.hide();
            return;
        }

        $wrap.show();

        const totalPages = Math.ceil(totalItems / pageSize) || 1;
        const startItem = (currentPage - 1) * pageSize + 1;
        const endItem = Math.min(currentPage * pageSize, totalItems);

        $info.html(`Showing <strong>${startItem}</strong> to <strong>${endItem}</strong> of <strong>${totalItems}</strong> entries`);

        // Previous Button
        const prevDisabled = currentPage <= 1 ? 'disabled' : '';
        const $prev = $(`<li class="page-item ${prevDisabled}"><a class="page-link" href="javascript:void(0)" aria-label="Previous"><i class="fas fa-chevron-left" style="font-size: 11px;"></i></a></li>`);
        if (currentPage > 1) {
            $prev.find('a').on('click', function(e) {
                e.preventDefault();
                onPageChange(currentPage - 1);
            });
        }
        $pagination.append($prev);

        // Page Numbers logic
        let pages = [];
        if (totalPages <= 7) {
            for (let i = 1; i <= totalPages; i++) pages.push(i);
        } else {
            if (currentPage <= 4) {
                pages = [1, 2, 3, 4, 5, '...', totalPages];
            } else if (currentPage >= totalPages - 3) {
                pages = [1, '...', totalPages - 4, totalPages - 3, totalPages - 2, totalPages - 1, totalPages];
            } else {
                pages = [1, '...', currentPage - 1, currentPage, currentPage + 1, '...', totalPages];
            }
        }

        pages.forEach(p => {
            if (p === '...') {
                $pagination.append(`<li class="page-item disabled"><span class="page-link" style="border:none; background:transparent;">...</span></li>`);
            } else {
                const activeClass = p === currentPage ? 'active' : '';
                const $pageItem = $(`<li class="page-item ${activeClass}"><a class="page-link" href="javascript:void(0)">${p}</a></li>`);
                if (p !== currentPage) {
                    $pageItem.find('a').on('click', function(e) {
                        e.preventDefault();
                        onPageChange(p);
                    });
                }
                $pagination.append($pageItem);
            }
        });

        // Next Button
        const nextDisabled = currentPage >= totalPages ? 'disabled' : '';
        const $next = $(`<li class="page-item ${nextDisabled}"><a class="page-link" href="javascript:void(0)" aria-label="Next"><i class="fas fa-chevron-right" style="font-size: 11px;"></i></a></li>`);
        if (currentPage < totalPages) {
            $next.find('a').on('click', function(e) {
                e.preventDefault();
                onPageChange(currentPage + 1);
            });
        }
        $pagination.append($next);
    }

    function renderTreatmentTable(treatments) {
        if (treatments !== undefined) {
            allTreatmentsData = treatments || [];
            treatmentPage = 1;
        }

        const tbody = $('#treatmentReportTableBody');
        tbody.empty();

        if (!allTreatmentsData || allTreatmentsData.length === 0) {
            tbody.append('<tr><td colspan="5" class="text-center py-4 text-muted">No treatments found.</td></tr>');
            renderPaginationControls('treatmentPagination', 'treatmentPaginationInfo', 'treatmentPaginationWrap', 1, 0, treatmentPageSize, null);
            return;
        }

        const startIndex = (treatmentPage - 1) * treatmentPageSize;
        const endIndex = startIndex + treatmentPageSize;
        const pageItems = allTreatmentsData.slice(startIndex, endIndex);

        pageItems.forEach(function(item) {
            let doc = item.doctor_name || 'General Staff';
            let price = item.price ? ('₹ ' + Number(item.price).toLocaleString('en-IN')) : 'Free / Not set';
            let count = item.total_appointments || 0;
            let statusText = (item.status === 0 || item.status === '0' || item.status === 'Inactive') ? 'Inactive' : 'Active';
            let statusBadge = statusText === 'Active' ? 'badge-paid' : 'badge-failed';

            tbody.append(`
                <tr>
                    <td style="font-weight: 600; color: #1e293b;">${item.name || '--'}</td>
                    <td><i class="fas fa-user-md mr-1" style="color: #26846b;"></i> ${doc}</td>
                    <td style="font-weight: 600;">${price}</td>
                    <td><span class="badge" style="background:#cfece0; color:#26846b; font-size: 13px; font-weight: 700; padding: 6px 12px; border-radius: 8px;">${count}</span></td>
                    <td><span class="badge-pill-custom ${statusBadge}">${statusText}</span></td>
                </tr>
            `);
        });

        renderPaginationControls('treatmentPagination', 'treatmentPaginationInfo', 'treatmentPaginationWrap', treatmentPage, allTreatmentsData.length, treatmentPageSize, function(newPage) {
            treatmentPage = newPage;
            renderTreatmentTable();
        });
    }

    function renderTransactionTable(transactions) {
        if (transactions !== undefined) {
            allTransactionsData = transactions || [];
            transactionPage = 1;
        }

        const tbody = $('#transactionReportTableBody');
        tbody.empty();

        if (!allTransactionsData || allTransactionsData.length === 0) {
            tbody.append('<tr><td colspan="7" class="text-center py-4 text-muted">No transactions found for this period.</td></tr>');
            renderPaginationControls('transactionPagination', 'transactionPaginationInfo', 'transactionPaginationWrap', 1, 0, transactionPageSize, null);
            return;
        }

        const startIndex = (transactionPage - 1) * transactionPageSize;
        const endIndex = startIndex + transactionPageSize;
        const pageItems = allTransactionsData.slice(startIndex, endIndex);

        pageItems.forEach(function(item) {
            let status = (item.status || 'Pending').toLowerCase();
            let badgeClass = 'badge-pending';
            if (status === 'paid' || status === 'completed') badgeClass = 'badge-paid';
            else if (status === 'failed' || status === 'cancelled') badgeClass = 'badge-failed';

            let date = item.date || '--';
            let amount = formatCurrency(item.amount);
            let method = item.method || 'Cash / Offline';

            tbody.append(`
                <tr>
                    <td style="font-weight: 700; color: #26846b;">#INV-${item.id}</td>
                    <td style="font-weight: 600;">${item.patient_name || '--'}</td>
                    <td><span class="text-capitalize">${item.type || 'General'}</span></td>
                    <td>${date}</td>
                    <td style="font-weight: 700; color: #1e293b;">${amount}</td>
                    <td>${method}</td>
                    <td><span class="badge-pill-custom ${badgeClass}">${item.status || 'Pending'}</span></td>
                </tr>
            `);
        });

        renderPaginationControls('transactionPagination', 'transactionPaginationInfo', 'transactionPaginationWrap', transactionPage, allTransactionsData.length, transactionPageSize, function(newPage) {
            transactionPage = newPage;
            renderTransactionTable();
        });
    }

    $(document).ready(function() {
        loadReportData(activeFilter);

        $('.period-filter-btn').on('click', function() {
            $('.period-filter-btn').removeClass('active');
            $(this).addClass('active');
            activeFilter = $(this).data('filter');
            loadReportData(activeFilter);
        });
    });
</script>
@endsection