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

    label.error {
        color: red !important;
        font-size: 13px;
        margin-top: 5px;
        display: block;
    }
</style>

@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="row" style="padding-top:15px">
                <div class="col-6">
                    <h4 class="page-title booking-title">Add Booking</h4>
                </div>
                <div class="col-6 booking-button m-b-2">
                    <a href="{{ route('treatment_booking.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                        <i class="fa fa-arrow-left m-r-5"></i> <span class="hdr-btn-text">Back</span>
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <form id="treatmentBookingForm" method="POST" class="form-container all-form" >
                        @csrf
                        <input type="hidden" name="branch_id" id="branch_id">

                        {{-- Patient --}}
                        <div class="row">
                            <div class="col-md-6 col-12 ">
                                <div class="form-group">
                                    <label><i class="fas fa-user icon-style"></i> Patient <span
                                            class="text-danger">*</span></label>
                                    @if (app('hasPermission')(5, 'create'))
                                        <a href="{{ route('patients.create') }}" target="_blank"
                                            class="btn btn-primary btn-sm float-right">
                                            <i class="fas fa-plus"></i> <span class="hdr-btn-text">Add</span>
                                        </a>
                                    @endif
                                    <select class="form-control select2" name="patient_id" id="patientDropdown" required>
                                        <option value="">Select Patient</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Treatment --}}


                            <div class="col-md-6 col-12 ">
                                <div class="form-group">
                                    <label><i class="fas fa-medkit icon-style"></i> Treatment <span
                                            class="text-danger">*</span></label>
                                    @if (app('hasPermission')(7, 'create'))
                                        <a href="{{ route('treatment.create') }}" target="_blank"
                                            class="btn btn-primary btn-sm float-right">
                                            <i class="fas fa-plus"></i> <span class="hdr-btn-text">Add</span>
                                        </a>
                                    @endif
                                    <select class="form-control select2" name="treatment_id" id="treatmentDropdown"
                                        required>
                                        <option value="">Select Treatment</option>
                                    </select>

                                </div>
                            </div>
                        </div>
                        <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label><i class="fa fa-cogs icon-style"></i> Machine </label>
                                @if (app('hasPermission')(7, 'create'))
                                    <button type="button" class="btn btn-primary btn-sm float-right" data-bs-toggle="modal"
                                        data-bs-target="#addMachineModal">
                                        <i class="fas fa-plus"></i> <span class="hdr-btn-text">Add</span>
                                    </button>
                                @endif
                                <select id="machineDropdown" name="machine_id[]" class="form-control select2" multiple
                                    style="min-height: 100px; max-height: 200px; overflow-y: auto;">
                                    <option value="">Select Machines</option>
                                </select>


                            </div>
                        </div>
                        </div>

            

                {{-- Date --}}
                <div class="row">


                    <div class="col-6">

                        <div class="form-group">
                            <label><i class="fas fa-calendar-day icon-style"></i> Date </label>
                            <input type="date" name="payment_date" class="form-control" id="todayOnlyDate" required>

                        </div>

                    </div>

                    {{-- Plan --}}
                    <div class="col-6">

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

                    </div>

                </div>
                {{-- Remain amount --}}
                <div class="row">


                    <div class="col-6">


                        <div class="form-group">
                            <label>₹ Payment <span
                                    class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="amount" id="amount" class="form-control"
                                placeholder="Enter total payment" required>
                        </div>
                    </div>
                    {{-- Payment Mode --}}
                    <div class="col-6">


                        <div class="form-group">
                            <label><i class="fas fa-money-bill icon-style"></i> Payment Mode <span
                                    class="text-danger">*</span></label>
                            <select name="payment_mode" class="form-control select2" id="payment_mode" required>
                                <option value="">Select Payment Mode</option>
                                <option value="cash">Cash</option>
                                <option value="online">Online</option>
                                <option value="cash+online">Cash+Online</option>
                            </select>
                        </div>
                    </div>
                </div>
                {{-- Paid Type --}}
                <div class="row">

                    <div class="col-4">


                        <div class="form-group d-none" id="paid_amount_type">
                            <label><i class="fas fa-hand-holding-usd icon-style"></i> Paid Type <span
                                    class="text-danger">*</span></label>
                            <select name="paid_type" class="form-control select2" id="paid_type">
                                <option value="">Select Paid Type</option>
                                <option value="fully">Fully</option>
                                <option value="partial">Partial</option>
                            </select>
                        </div>
                    </div>
                    {{-- Cash Amount --}}
                    <div class="form-group d-none col-4" id="cash_amount_div">
                        <label>₹ Cash Amount <span
                                class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="cash" id="cash_amount" class="form-control"
                            placeholder="Enter cash amount">
                        <small id="cashError" class="text-danger d-none"></small>
                    </div>

                    {{-- Online Amount --}}
                    <div class="form-group d-none col-4" id="online_amount_div">
                        <label><i class="fas fa-credit-card icon-style"></i> Online Amount <span
                                class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="online" id="online_amount" style="margin-left: 13px;"
                            class="form-control" placeholder="Enter online amount">
                        <small id="onlineError" class="text-danger d-none"></small>
                    </div>

                    {{-- Amount Paid --}}
                    <div class="col-4">


                        <div class="form-group d-none" id="paid_amount_div">
                            <label><i class="fas fa-coins icon-style"></i> Amount Paid <span
                                    class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="paid_amount" id="paid_amount" class="form-control"
                                placeholder="Enter paid amount">
                        </div>
                    </div>
                    {{-- Pending Amount --}}
                    <div class="col-4">


                        <div class="form-group d-none" id="remain_amount_div">
                            <label><i class="fas fa-hourglass-half icon-style"></i> Pending Amount</label>
                            <input type="number" step="0.01" name="remain_amount" id="remain_amount" class="form-control"
                                readonly>
                        </div>
                    </div>
                </div>
                <div id="bookingSuccess" class="alert alert-success" style="display:none;"></div>

                <button type="submit" class="btn btn-primary submit-btn d-block m-auto"
                    style="padding:8px 50px; border-radius:50px;">
                    Submit
                </button>
                </form>
            </div>
        </div>
    </div>
    </div>



    <!-- Add Machine Modal -->
    <div class="modal fade" id="addMachineModal" tabindex="-1" aria-labelledby="addMachineModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="machineForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #CFECE0; color:black">
                        <h5 class="modal-title" id="addMachineModalLabel">Add Machine</h5>
                        <button type="button" class="btn-close custom-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="machineName" class="form-label">Title</label>
                            <input type="text" class="form-control" id="machineName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="machinedescription" class="form-label">Description</label>
                            <textarea class="form-control" id="machinedescription" name="description" rows="3"
                                style="border-radius:10px"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="machineName" class="form-label">Price</label>
                            <input type="number" class="form-control" id="price" name="price" required>
                        </div>
                        <div id="machineSuccess" class="alert alert-success" style="display:none;"></div>
                        <div id="machineError" class="alert alert-danger" style="display:none;"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save Machine</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Scripts --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset(env('IMAGE_PATH').'admin/assets/js/treatment-booking-create.js') }}"></script>


           
@endsection
