@extends('layout.app')

<style>
    .form-control.is-invalid,
    .was-validated .form-control:invalid {
        border: 1px solid #cfece0 !important;
    }

    .icon-style {
        margin-right: 8px;
        color: #007bff;
    }

    .plan-title {
        padding-left: 70px !important;
        text-align: center !important;
    }

    .plan-button {
        padding-right: 60px !important;
        text-align: center !important;
    }

    .form-container {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 30px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .form-group label {
        font-weight: 500;
        color: #333;
        margin-bottom: 8px;
    }

    .form-control {
        border-radius: 6px;
        border: 1px solid #cfece0 !important;
        padding: 10px 12px;
        font-size: 14px;
    }

    .form-control:focus {
        border-color: #ff8e29 !important;
        box-shadow: 0 0 0 0.2rem rgba(255, 142, 41, 0.15) !important;
    }

    .select2-container--default .select2-selection--single {
        border-radius: 6px;
        border: 1px solid #cfece0 !important;
        height: 40px;
    }

    .btn-submit {
        background-color: #cfece0 !important;
        color: #000 !important;
        border: none !important;
        border-radius: 50px;
        padding: 10px 50px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    textarea.form-control {
        border-radius: 10px;
    }

    .btn-submit:hover {
        background-color: #b8d9c8 !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .btn-back {
        background-color: #fed9cf !important;
        color: #000 !important;
        border: none !important;
        border-radius: 50px;
        padding: 8px 20px;
        font-weight: 500;
    }

    .btn-back:hover {
        background-color: #fcc9bb !important;
    }

    .text-danger {
        color: #ff4d4f !important;
    }

    .error-message {
        color: #ff4d4f;
        font-size: 12px;
        margin-top: 4px;
    }

    .info-box {
        background-color: #e6f7ff;
        border: 1px solid #91d5ff;
        border-radius: 6px;
        padding: 12px;
        margin-top: 8px;
        font-size: 13px;
        color: #0050b3;
    }

    @media screen and (max-width: 768px) {
        .plan-title {
            padding-left: 15px !important;
            text-align: left !important;
        }

        .plan-button {
            padding-right: 15px !important;
            text-align: right !important;
        }

        .form-container {
            width: 100% !important;
            padding: 20px;
        }
    }
</style>

@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="row" style="padding-top: 15px">
                <div class="col-6">
                    <h4 class="page-title plan-title">Add Plan</h4>
                </div>
                <div class="col-6 m-b-2 eye-btn plan-button">
                    <a href="{{ route('plans.planlist') }}" class="btn btn-primary btn-rounded btn-hdr">
                        <i class="fa fa-arrow-left m-r-5"></i> <span class="hdr-btn-text">Back</span>
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <form class="form-container" method="POST" action="{{ route('plans.store') }}" style="width: 60%;">
                        @csrf

                        <div class="row">
                            <!-- Plan Name -->
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-tag icon-style"></i>Plan Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        placeholder="Enter Plan Name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
<input type="hidden" id="duration" name="duration">
<input type="hidden" id="total_amount" name="total_amount">
                            <!-- Price -->
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-dollar-sign icon-style"></i>Price (Per Month) <span
                                            class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="price" id="price"
                                        class="form-control @error('price') is-invalid @enderror" placeholder="0.00"
                                        value="{{ old('price') }}" required onchange="calculateDuration();">
                                    @error('price')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Start Date -->
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-calendar-check icon-style"></i>Start Date <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="start_date" id="start_date"
                                        class="form-control @error('start_date') is-invalid @enderror"
                                        value="{{ old('start_date') }}" required onchange="calculateDuration();">
                                    @error('start_date')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- End Date -->
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-calendar-times icon-style"></i>End Date <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="end_date" id="end_date"
                                        class="form-control @error('end_date') is-invalid @enderror"
                                        value="{{ old('end_date') }}" required onchange="calculateDuration();">
                                    @error('end_date')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Duration (Read-only, calculated automatically) -->
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-calendar icon-style"></i>Duration</label>
                                    <input type="text" id="duration_display" class="form-control"
                                        placeholder="Calculated automatically" readonly style="background-color: #f5f5f5;">

                                </div>
                            </div>

                            <!-- Total Amount (Read-only, calculated automatically) -->
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-coins icon-style"></i>Total Amount</label>
                                    <input type="text" id="total_amount_display" class="form-control"
                                        placeholder="Calculated automatically" readonly style="background-color: #f5f5f5;">

                                </div>
                            </div>

                            <!-- Subtitle -->
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-comment icon-style"></i>Remark</label>
                                    <input type="text" name="subtitle" class="form-control" placeholder="Enter Remark"
                                        value="{{ old('subtitle') }}">
                                    @error('subtitle')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- User Limit -->
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-users icon-style"></i>User Limit</label>
                                    <input type="number" name="user_limit" class="form-control" placeholder="Unlimited"
                                        value="{{ old('user_limit') }}">
                                    @error('user_limit')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Branch Limit -->
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-code-branch icon-style"></i>Branch Limit</label>
                                    <input type="number" name="branch_limit" class="form-control"
                                        placeholder="Unlimited" value="{{ old('branch_limit') }}">
                                    @error('branch_limit')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Storage Limit -->
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-database icon-style"></i>Storage Limit (GB)</label>
                                    <input type="number" name="storage_limit" class="form-control" placeholder="0"
                                        value="{{ old('storage_limit') }}">
                                    @error('storage_limit')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-toggle-on icon-style"></i> <span class="hdr-btn-text">Status</span></label>
                                    <select name="is_active" class="form-control">
                                        <option value="1" {{ old('is_active') == 1 ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="0" {{ old('is_active') == 0 ? 'selected' : '' }}>Inactive
                                        </option>
                                    </select>
                                    @error('is_active')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Features -->
                            <div class="col-6">
                                <div class="form-group">
                                    <label><i class="fas fa-star icon-style"></i>Features</label>
                                    <textarea name="features" rows="4" class="form-control" placeholder="One feature per line or comma separated">{{ old('features') }}</textarea>
                                    <small class="text-muted">Enter each feature on a new line or separated by
                                        commas</small>
                                    @error('features')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div style="text-align: right; margin-top: 30px;">
                            <button type="submit" class="btn btn-submit">
                                <i class="fa fa-save"></i> Save Plan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Calculate on page load if old values exist
            if (document.getElementById('start_date').value && document.getElementById('end_date').value) {
                calculateDuration();
            }
        });

        /**
         * Calculate duration in months and days between start and end dates
         */
        function calculateDuration() {
            let start = $('#start_date').val();
            let end = $('#end_date').val();

            if (start && end) {

                let startDate = new Date(start);
                let endDate = new Date(end);

                if (endDate < startDate) {
                    $('#duration_display').val('');
                    $('#total_amount_display').val('');
                    return;
                }

                let years = endDate.getFullYear() - startDate.getFullYear();
                let months = endDate.getMonth() - startDate.getMonth();
                let days = endDate.getDate() - startDate.getDate();

                if (days < 0) {
                    months--;
                    const prevMonth = new Date(endDate.getFullYear(), endDate.getMonth(), 0);
                    days += prevMonth.getDate();
                }

                if (months < 0) {
                    years--;
                    months += 12;
                }

                let duration = '';

                if (years > 0) duration += years + ' Year ';
                if (months > 0) duration += months + ' Month ';
                if (days > 0) duration += days + ' Day';

                $('#duration_display').val(duration.trim());

                // Same ERP calculation
                let totalMonths = (years * 12) + months + (days / 30);

                let price = parseFloat($('#price').val()) || 0;

                let totalAmount = price * totalMonths;

                $('#total_amount_display').val(totalAmount > 0 ? totalAmount.toFixed(2) : '');

                // Hidden fields for form submit
                $('#duration').val(duration.trim());
                $('#total_amount').val(totalAmount.toFixed(2));
            }
        }

        $('#start_date,#end_date,#price').on('change keyup', calculateDuration);

        /**
         * Calculate total amount based on price and duration
         */
        // function calculateTotalAmount() {
        //     try {
        //         const priceInput = document.getElementById('price');
        //         const startDateInput = document.getElementById('start_date');
        //         const endDateInput = document.getElementById('end_date');
        //         const totalAmountDisplay = document.getElementById('total_amount_display');

        //         if (!priceInput || !startDateInput || !endDateInput || !totalAmountDisplay) {
        //             console.error('Required elements not found');
        //             return;
        //         }

        //         const price = parseFloat(priceInput.value);
        //         const startValue = startDateInput.value;
        //         const endValue = endDateInput.value;

        //         if (!price || !startValue || !endValue) {
        //             totalAmountDisplay.value = '';
        //             return;
        //         }

        //         const startDate = new Date(startValue + 'T00:00:00');
        //         const endDate = new Date(endValue + 'T00:00:00');

        //         // Validate dates
        //         if (isNaN(startDate.getTime()) || isNaN(endDate.getTime())) {
        //             console.error('Invalid date format');
        //             return;
        //         }

        //         if (endDate <= startDate) {
        //             totalAmountDisplay.value = '';
        //             return;
        //         }

        //         // Calculate months and days
        //         let months = 0;
        //         let tempDate = new Date(startDate);

        //         // Count complete months
        //         while (true) {
        //             let nextMonth = new Date(tempDate);
        //             nextMonth.setMonth(nextMonth.getMonth() + 1);
        //             if (nextMonth > endDate) {
        //                 break;
        //             }
        //             months++;
        //             tempDate = new Date(nextMonth);
        //         }

        //         // Calculate remaining days
        //         const daysRemaining = Math.floor((endDate - tempDate) / (1000 * 60 * 60 * 24));

        //         // Calculate duration in months (with decimal for days)
        //         const durationInMonths = months + (daysRemaining / 30);
        //         const totalAmount = price * durationInMonths;

        //         totalAmountDisplay.value = totalAmount.toFixed(2);

        //     } catch (error) {
        //         console.error('Error in calculateTotalAmount:', error);
        //     }
        // }
    </script>
@endsection
