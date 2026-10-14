@extends('layout.app')

<style>
    /* Make the multiple select field expand nicely */
    .select2-container--default.select2-container--focus .select2-selection--multiple,
    .select2-container--default .select2-selection--multiple {
        min-height: 50px;
        /* Minimum height */
        max-height: 150px;
        /* Maximum height */
        overflow-y: auto;
        /* Scroll when too many items */
        padding: 5px 10px;
        border-radius: 10px;
        display: flex;
        flex-wrap: wrap;
        /* Important: allow items to go to next line */
        align-items: center;
    }

    .select2-selection__choice {
        margin: 2px 5px;
        padding: 3px 8px;
        font-size: 14px;
        border-radius: 12px;
        display: flex;
        align-items: center;
    }

    /* Match Daily Data form styles */
    .select2-container--default .select2-selection--multiple {
        box-shadow: none;
        font-size: 14px;
        min-height: 40px !important;
        border-radius: 50px !important;
        padding: 0.469rem 0.75rem;
        border-color: rgb(207, 236, 224) !important;
    }

    .select2-selection__rendered {
        text-align: left !important;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        margin: 0px;
    }

    .select2-container--default .select2-selection--multiple .select2-search__field {
        margin: 0 !important;
    }

    .select2-container .select2-search--inline .select2-search__field {
        margin-top: 0 !important;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        background-color: rgb(207, 236, 224) !important;
        color: black !important;
        border-radius: 20px !important;
        border: none;
        font-size: 14px;
        font-weight: normal !important;
        max-width: fit-content;
        /* margin-top: 0 !important; */
    }


    .booking-title {
        padding-left: 80px !important;
        text-align: center !important;
    }

    .booking-button {
        padding-right: 10px !important;
        text-align: center !important;
    }
    .form-container {
        width: 60% !important;
        padding-bottom: 60px !important;
    }

    @media screen and (max-width:767px) {
        .booking-title {
            padding-left: 15px !important;
            text-align: left !important;
        }

        .booking-button {
            padding-right: 15px !important;
            text-align: right !important;
        }

        .page-title {
            font-size: 20px;
        }
    }
</style>

@section('content')
    <div class="page-wrapper" data-treatment-booking-id="{{ $treatment_booking_id }}">
        <div class="content">
            <div class="row" style="padding-top:15px">
                <div class="col-6">
                    <h4 class="page-title booking-title">Edit Booking</h4>
                </div>
                <div class="col-6 booking-button m-b-2">
                    <a href="{{ route('treatment_booking.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                        <i class="fa fa-arrow-left m-r-5"></i> <span class="hdr-btn-text">Back</span>
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <form id="edittreatmentBookingForm" method="POST" class="form-container all-form">
                        @csrf

                        {{-- Patient --}}
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label><i class="fas fa-user icon-style"></i> Patient <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control select2" name="patient_id" id="patientDropdown" required>
                                        <option value="">Select Patient</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Treatment --}}

                            <div class="col-6">
                                <div class="form-group">
                                    <label><i class="fas fa-medkit icon-style"></i> Treatment <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control select2" name="treatment_id" id="treatmentDropdown"
                                        required>
                                        <option value="">Select Treatment</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-12">
                                {{-- Machine --}}
                                <div class="form-group">
                                    <label><i class="fa fa-cogs icon-style"></i> Machine <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control select2" name="machine_id[]" id="machineDropdown"
                                        style="min-height: 100px; max-height: 200px; overflow-y: auto;" required multiple>
                                        <option value="">Select Machine</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Date --}}
                        <div class="form-group">
                            <label><i class="fas fa-calendar-day icon-style"></i> Date <span
                                    class="text-danger">*</span></label>
                            <input type="date" name="payment_date" class="form-control" id="todayOnlyDate" required>
                        </div>

                        {{-- Plan --}}
                        <div class="form-group">
                            <label><i class="fas fa-calendar-day icon-style"></i> Plan <span
                                    class="text-danger">*</span></label>
                            <select name="plan" class="form-control select2" id="plan" required>
                                <option value="">Select Plan</option>
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                            </select>
                        </div>

                        <div id="bookingSuccess" class="alert alert-success" style="display:none;"></div>
                        <div id="bookingError" class="alert alert-danger" style="display:none;"></div>

                        <button type="submit" class="btn btn-primary submit-btn d-block m-auto"
                            style="padding:8px 50px; border-radius:50px;">
                            Submit
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        window.bookingId = @json($treatment_booking_id);
        window.treatmentBookingIndexRoute = @json(route('treatment_booking.index'));
    </script>

    <script src="{{ asset(env('IMAGE_PATH').'admin/assets/js/treatment-booking-edit.js') }}"></script>

@endsection
