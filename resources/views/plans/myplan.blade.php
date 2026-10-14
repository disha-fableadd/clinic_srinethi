@extends('layout.app')

<style>
    .page-wrapper {
        background-color: #f5f5f5;
    }

    .content {
        padding: 20px;
    }

    .plan-subtitle {
        font-size: 14px;
        color: #999;
        margin-bottom: 25px;
    }

    .plan-card-main {
        background: linear-gradient(135deg, #2d3e50 0%, #34495e 50%, #d9763f 100%);
        border-radius: 16px;
        padding: 35px;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 40px;
        margin-bottom: 35px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        position: relative;
        overflow: hidden;
    }

    .plan-card-main::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
        z-index: 0;
    }

    .plan-info {
        position: relative;
        z-index: 1;
        flex: 1;
    }

    .plan-name {
        font-size: 42px;
        font-weight: 700;
        margin: 0 0 16px 0;
        letter-spacing: -0.5px;
    }

    .plan-dates {
        display: flex;
        gap: 35px;
        font-size: 15px;
        font-weight: 500;
    }

    .plan-date-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .plan-date-item i {
        font-size: 16px;
    }

    .plan-amount-box {
        background: rgba(255, 255, 255, 0.18);
        backdrop-filter: blur(10px);
        border-radius: 14px;
        padding: 28px 32px;
        text-align: center;
        position: relative;
        z-index: 1;
        border: 1px solid rgba(255, 255, 255, 0.25);
        min-width: 220px;
    }

    .amount-label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        font-weight: 700;
        margin-bottom: 12px;
        opacity: 0.95;
    }

    .amount-value {
        font-size: 38px;
        font-weight: 700;
        margin: 0;
        margin-bottom: 8px;
    }

    .amount-duration {
        font-size: 13px;
        opacity: 0.95;
        font-weight: 500;
    }

    .features-section {
        margin-top: 20px;
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .features-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 25px;
        font-size: 17px;
        font-weight: 600;
        color: #1a1a1a;
    }

    .features-header i {
        color: #2ecc71;
        font-size: 20px;
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }

    .feature-card {
        background: #fafbfc;
        border: 1px solid #e8ecf1;
        border-radius: 8px;
        padding: 16px 18px;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: all 0.3s ease;
    }

    .feature-card:hover {
        background: #f5f8fa;
        border-color: #d9763f;
        box-shadow: 0 2px 8px rgba(217, 118, 63, 0.08);
    }

    .feature-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 20px;
        height: 20px;
        color: #2ecc71;
        font-size: 16px;
    }

    .feature-text {
        flex: 1;
    }

    .feature-text p {
        margin: 0;
        color: #333;
        font-size: 14px;
        font-weight: 500;
        line-height: 1.4;
    }

    .no-plan-container {
        text-align: center;
        padding: 80px 20px;
        background: white;
        border-radius: 12px;
        margin-bottom: 20px;
    }

    .no-plan-icon {
        font-size: 80px;
        color: #bdc3c7;
        margin-bottom: 20px;
    }

    .no-plan-title {
        font-size: 28px;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 10px;
    }

    .no-plan-text {
        font-size: 15px;
        color: #666;
        margin-bottom: 30px;
        max-width: 500px;
        margin-left: auto;
        margin-right: auto;
    }

    .btn-select-plan {
        background-color: #2ecc71 !important;
        color: white !important;
        border: none !important;
        border-radius: 6px;
        padding: 11px 28px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }

    .btn-select-plan:hover {
        background-color: #27ae60 !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(46, 204, 113, 0.3);
    }

    .plan-details-list {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-top: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .plan-details-list h5 {
        margin: 0 0 20px 0;
        color: #1a1a1a;
        font-weight: 600;
        font-size: 16px;
    }

    .detail-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 0;
        border-bottom: 1px solid #e9ecef;
    }

    .detail-item:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-weight: 500;
        color: #666;
        font-size: 14px;
    }

    .detail-value {
        font-weight: 600;
        color: #1a1a1a;
        font-size: 14px;
    }

    .badge-active {
        display: inline-block;
        background-color: #d4edda;
        color: #155724;
        padding: 5px 11px;
        border-radius: 18px;
        font-size: 11px;
        font-weight: 700;
    }

    .badge-inactive {
        display: inline-block;
        background-color: #f8d7da;
        color: #721c24;
        padding: 5px 11px;
        border-radius: 18px;
        font-size: 11px;
        font-weight: 700;
    }

    .action-buttons {
        display: flex;
        gap: 12px;
        margin-top: 25px;
        justify-content: center;
    }

    .btn-action {
        padding: 10px 24px;
        border-radius: 6px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 13px;
    }

    .btn-change-plan {
        background-color: #3498db;
        color: white;
    }

    .btn-change-plan:hover {
        background-color: #2980b9;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
    }

    .btn-upgrade {
        background-color: #f39c12;
        color: white;
    }

    .btn-upgrade:hover {
        background-color: #d68910;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(243, 156, 18, 0.3);
    }

    .expiry-notice {
        background: #fff3cd;
        border: 1px solid #ffc107;
        border-radius: 8px;
        padding: 14px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        color: #856404;
        font-weight: 500;
        font-size: 14px;
    }

    .expiry-notice i {
        font-size: 16px;
    }

    @media (max-width: 768px) {
        .plan-card-main {
            flex-direction: column;
            padding: 25px;
            gap: 20px;
        }

        .plan-amount-box {
            min-width: auto;
            width: 100%;
        }

        .plan-name {
            font-size: 32px;
        }

        .amount-value {
            font-size: 32px;
        }

        .features-grid {
            grid-template-columns: 1fr;
        }

        .action-buttons {
            flex-direction: column;
        }

        .btn-action {
            width: 100%;
            text-align: center;
        }

        .plan-dates {
            gap: 20px;
        }
    }
</style>

@section('content')
<div class="page-wrapper">
    <div class="content">
        <!-- Page Title and Description -->
        <h4 style="margin: 0 0 5px 0; color: #1a1a1a; font-weight: 600; font-size: 22px;">My Plan Details</h4>
        <p class="plan-subtitle">Overview of your active subscription plan and limits</p>

        @if ($userPlan)
            <!-- Main Plan Card -->
            <div class="plan-card-main">
                <div class="plan-info">
                    <h2 class="plan-name">{{ $userPlan->name }}</h2>
                    <div class="plan-dates">
                        <div class="plan-date-item">
                            <i class="fas fa-calendar-check"></i>
                            <span>Start Date: {{ $userPlan->start_date->format('d-m-Y') }}</span>
                        </div>
                        <div class="plan-date-item">
                            <i class="fas fa-calendar-times"></i>
                            <span>End Date: {{ $userPlan->end_date->format('d-m-Y') }}</span>
                        </div>
                    </div>
                </div>

                <div class="plan-amount-box">
                    <div class="amount-label">Total Amount</div>
                    <p class="amount-value">₹{{ number_format($userPlan->total_amount, 2) }}</p>
                    <div class="amount-duration">{{ $userPlan->duration }}</div>
                </div>
            </div>

            <!-- Included Features -->
            @if ($userPlan->features && count($userPlan->features) > 0)
                <div class="features-section">
                    <div class="features-header">
                        <i class="fas fa-check-circle"></i>
                        Included Features
                    </div>

                    <div class="features-grid">
                        @foreach ($userPlan->features as $feature)
                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div class="feature-text">
                                    <p>{{ $feature }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Expiry Notice (if plan expiring soon) -->
            @if ($userPlan->end_date < now()->addDays(30))
                <div class="expiry-notice">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>Your plan expires on {{ $userPlan->end_date->format('d-m-Y') }}. Consider upgrading to avoid service interruption.</span>
                </div>
            @endif


        @else
            <!-- No Plan Selected -->
            <div class="no-plan-container">
                <div class="no-plan-icon">
                    <i class="fas fa-inbox"></i>
                </div>
                <h3 class="no-plan-title">No Plan Selected</h3>
                <p class="no-plan-text">You haven't selected a plan yet. Choose a plan to unlock all features and get started with your clinic management system.</p>
                <a href="{{ route('plans.planlist') }}" class="btn-select-plan">
                    <i class="fas fa-list"></i> View Available Plans
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
