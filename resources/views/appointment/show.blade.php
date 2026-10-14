@extends('layout.app')

<style>
    @media screen and (max-width:767px) {
        .page-title {
            font-size: 16px !important;
            margin-top: 8px !important;
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

        .icon-style1 {
            font-size: 17px;
        }
    }

    .content {
        height: 100vh;
    }

    .top-padding {
        padding-top: 15px;
    }

    .card-footer {
        background-color: #87ceb0 !important;
    }

    .float-left {
        float: left;
    }

    .justify-end {
        justify-content: end;
    }

    .userProfile {
        border-radius: 20px;
    }

    .button {
        display: flex;
        justify-content: end;
        margin: 0 5px;
    }

    .edit-appointment-btn {
        color: black;
        margin-right: 10px;
    }

    #successMessage {
        display: none;
    }

    .btn-outline-success:hover {
        color: black !important;
        background-color: #cfece0 !important;
        border-color: #cfece0 !important;
    }

    .btn-outline-success {
        color: black !important;
        background-color: #cfece0 !important;
        border-color: #cfece0 !important;
    }

    button.btn.btn-sm.btn-outline-success.generate-appointment-link-btn {
        margin-right: 10px;
        border-radius: 50px;
    }
</style>


@section('content')
    @php
        $isPatientRole = optional(Auth::user()?->role)->name === 'Patient';
    @endphp
    <div class="page-wrapper">
        <div class="content">
            <div class="row mt-2 top-padding">
                {{-- <div class="col-sm-8 col-8">
                    <h4 class="page-title " style="text-align:left;"> Appointment Details</h4>
                </div> --}}

            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-footer text-right">
                            <h3 class="float-left text-dark">
                                <i class="fa fa-info-circle icon-style2"></i>
                                <span class="">Appointment Details</span>
                            </h3>

                            <div class="d-flex text-right justify-end">

                                <div class="mr-2 m-b-2">
                                    <a href="javascript:void(0)" class="btn btn-primary btn-rounded btn-hdr download-appointment"
                                        data-id="{{ $appointment_id }}">
                                        <i class="fa fa-download"></i> <span class="hdr-btn-text">PDF</span>
                                    </a>
                                </div>


                                @if (app('hasPermission')(6, 'view'))
                                    <div class=" text-right m-b-2">
                                        <a href="{{ route('appointment.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                                            <i class="fa fa-arrow-left m-r-5 icon3"></i> <span class="hdr-btn-text">Back</span></a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="card-body mt-3">

                            <div class="row g-3">
                                <!-- Left Column: Image + Patient Info -->
                                <div class="col-12 col-md-8">
                                    <div class="row g-3">
                                        <!-- Patient Image -->
                                        <div class="col-12 col-sm-6 text-center text-sm-start">
                                            <img id="file" class="userProfile" src="" width="200"
                                                height="200" alt="report Image"
                                                onerror="this.onerror=null;this.src='{{ asset(env('IMAGE_PATH') . 'admin/assets/img/img1.png') }}';">
                                        </div>

                                        <!-- Patient Info -->
                                        <div class="col-12 col-sm-6">
                                            <p class="text-dark margin-text">
                                                <strong><i class="fas fa-user-injured icon-style1"></i> Patient: </strong>
                                                <span id="patient"></span>
                                            </p>
                                            <hr class="margin-text">

                                            <p class="text-dark margin-text">
                                                <strong><i class="fas fa-user-md icon-style1"></i> Doctor: </strong>
                                                <span id="doctor"></span>
                                            </p>
                                            <hr class="margin-text">

                                            <p class="text-dark margin-text">
                                                <strong><i class="fas fa-stethoscope icon-style1"></i> Treatment: </strong>
                                                <span id="treatment"></span>
                                            </p>
                                            <hr class="margin-text">

                                            @php
                                                $currentProjectTypeId = (int) \App\Models\Setting::getValue('project_type_id', 1);
                                            @endphp
                                            @if($currentProjectTypeId !== 3)
                                            <p class="text-dark margin-text">
                                                <strong><i class="fas fa-calendar-day icon-style1"></i> Type: </strong>
                                                <span id="type"></span>
                                            </p>
                                            <hr class="margin-text">

                                            <p class="text-dark margin-text">
                                                <strong><i class="fas fa-calendar-alt icon-style1"></i> Status: </strong>
                                                <span id="status"></span>
                                            </p>
                                            <hr class="margin-text">
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Column: Date, Duration, Location -->
                                <div class="col-12 col-md-4">
                                    <p class="text-dark margin-text">
                                        <strong><i class="fas fa-clock icon-style1"></i> Date: </strong>
                                        <span id="date"></span>
                                    </p>
                                    <hr class="margin-text">

                                    <p class="text-dark margin-text">
                                        <strong><i class="fas fa-hourglass-half icon-style1"></i> Time: </strong>
                                        <span id="duration"></span>
                                    </p>
                                    <hr class="margin-text">

                                    @if($currentProjectTypeId !== 3)
                                    <p class="text-dark margin-text">
                                        <strong><i class="fas fa-pencil-alt icon-style1"></i> Followup Update: </strong>
                                        <span id="update"></span>
                                    </p>
                                    <hr class="margin-text">

                                    <p class="text-dark margin-text">
                                        <strong><i class="fas fa-map-marker-alt icon-style1"></i> Clinic Location: </strong>
                                        <span id="location"></span>
                                    </p>
                                    <hr class="margin-text">
                                    @endif
                                </div>
                            </div>

                            <div class="button mb-4">
                                @if (!$isPatientRole)
                                    <button class="btn btn-sm btn-outline-success generate-appointment-link-btn"
                                        data-id="{{ $appointment_id }}" data-status="{{ $appointments->payment_status ?? '' }}">
                                        <i class="fa fa-link"></i> Payment Link
                                    </button>
                                @endif


                                @if (app('hasPermission')(6, 'update') && !$isPatientRole)
                                    <a href="#" class="btn btn-primary btn-rounded btn-hdr edit-appointment-btn "> 
                                        <i class="fa fa-pencil-alt"></i> <span class="hdr-btn-text">Edit</span> </a>
                                    @endif 
                                    @if (app('hasPermission')(6, 'delete') && !$isPatientRole)
                                        <button type="button" class="btn btn-danger btn-rounded btn-hdr delete-report"
                                            data-id="{{ $appointment_id }}"> <i class="fa fa-trash"></i> <span class="hdr-btn-text">Delete</span> </button>
                                    @endif
                            </div>

                        </div>
                    </div>
                </div>
            </div>


            <div id="successMessage" class="alert alert-success"></div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-footer text-right">
                            <h3 class="float-left text-dark">
                                <i class="fa fa-info-circle icon-style2 text-white"></i>
                                <span class="Patient_name">
                                    {{ isset($appointments->patient, $appointments->patient->fullname) ? \Illuminate\Support\Str::ucfirst($appointments->patient->fullname) . "'s Details" : '' }}
                                </span>
                            </h3>

                        </div>

                        <div class="card-body mt-3">
                            <ul class="nav nav-tabs" id="patientTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="appointments-tab" data-toggle="tab" href="#appointments"
                                        role="tab">Appointment History</a>
                                </li>

                            </ul>
                            <div class="tab-content mt-3" id="patientTabsContent">
                                <div class="tab-pane active show" id="appointments" role="tabpanel">
                                    <div class="table-responsive-wrapper">
                                        @if (isset($appointments, $appointments->appointment_history) && $appointments->appointment_history->isNotEmpty())
                                            <table class="table table-bordered" id="appointmentTable">
                                                <thead>
                                                    <tr>
                                                        <th>Doctor</th>
                                                        <th>Treatment</th>
                                                        <th>Date/Time</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="appointmentRecords">
                                                    @foreach ($appointments->appointment_history as $history)
                                                        <tr>
                                                            <td>{{ ucfirst($appointments->doctor->fullname ?? '--') }}</td>
                                                            <td>{{ ucfirst($appointments->treatment->name ?? '--') }}</td>
                                                            <td>
                                                                {{ \Carbon\Carbon::parse($history['date'])->format('d/m/Y') }}
                                                                {{ \Carbon\Carbon::parse($history['time'])->format('h:i A') }}
                                                            </td>
                                                            <td>
                                                                <span
                                                                    class="custom-badge btn {{ $history['status'] ?? '' }} btn-rounded">
                                                                    {{ ucfirst($history['status'] ?? '--') }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <div class="alert alert-info text-center">No appointment history available.
                                            </div>
                                        @endif
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).on('click', '.download-appointment', function() {
            const appointmentId = $(this).data('id');

            $.ajax({
                url: `/api/appointments/${appointmentId}/download`,
                method: 'GET',
                xhrFields: {
                    responseType: 'blob' // important to handle PDF
                },
                success: function(response, status, xhr) {
                    Swal.close(); // Close loading alert

                    // Get filename from headers if set
                    let filename = `Appointment_${appointmentId}.pdf`;
                    const disposition = xhr.getResponseHeader('Content-Disposition');
                    if (disposition && disposition.indexOf('filename=') !== -1) {
                        filename = disposition.split('filename=')[1].replace(/"/g, '');
                    }

                    // Create a blob link to download
                    const blob = new Blob([response], {
                        type: 'application/pdf'
                    });
                    const link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = filename;
                    link.click();
                },
                error: function(xhr) {
                    Swal.close(); // Close loading alert

                    let message = "Failed to download PDF.";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: message
                    });
                }
            });
        });
        $(document).ready(function() {


            $(document).ready(function() {
                $('#appointmentTable').DataTable({
                    paging: true,
                    searching: true,
                    ordering: true
                });
            });


        });
        $(document).ready(function() {
            // new DataTable('#followupTable');
            // new DataTable('#appointmentTable');


            let AppointmentId = "{{ $appointment_id }}";


            let token = @json(session('access_token'));
            let userId = @json(session('user_id'));


            $.ajax({
                url: "/api/appointments/" + AppointmentId,
                method: "GET",
                dataType: "json",
                headers: {
                    "Authorization": "Bearer " + token
                },
                success: function(response) {
                    if (response.success) {
                        var appointment = response.data;
                        const followups = response.followups;


                        // Check if the profile exists and is not null or undefined
                        let imageUrl = appointment.patient.profile

                        // Set the image source
                        $("#file").attr("src", imageUrl).on("error", function() {
                            // If image fails to load, set default image
                            $(this).attr("src",
                                "{{ asset(env('IMAGE_PATH') . 'admin/assets/img/img1.png') }}"
                            );
                        });



                        function ucfirst(str) {
                            return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
                        }

                        $("#patient").text(ucfirst(appointment.patient.fullname));
                        $("#doctor").text(ucfirst(appointment.doctor.fullname));
                        $("#treatment").text(ucfirst(appointment.treatment.name));
                        $("#type").text(ucfirst(appointment.appoint_type));
                        $("#status").text(ucfirst(appointment.status));
                        $("#date").text(appointment.date);
                        $("#duration").text(moment(appointment.duration, "HH:mm").format("hh:mm A"));
                        $("#update").text(ucfirst(appointment.followup_update));
                        $("#location").text(ucfirst(appointment.clinic_location));

                        $(".edit-appointment-btn").attr("href", "/appointment/edit/" + appointment.id);
                        if (appointment.status.toLowerCase() === 'completed') {
                            $(".edit-appointment-btn").hide();
                        }






                    } else {
                        alert("Failed to fetch appointment details");
                    }


                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    alert("Error fetching appointment details");
                }
            });

            // Delete functionality
            $(".delete-report").on("click", function() {
                var appointmentId = $(this).data('id');

                if (!appointmentId) {
                    Swal.fire('Error', 'Appointment ID not found!', 'error');
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
                            url: '/api/appointments/' + appointmentId,
                            method: 'DELETE',
                            success: function(response) {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'Appointment deleted successfully!',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    $('button[data-id="' + appointmentId + '"]')
                                        .closest('tr').remove();
                                    setTimeout(function() {
                                        window.location.href =
                                            "{{ route('appointment.index') }}";
                                    }, 1500);
                                });
                            },
                            error: function() {
                                Swal.fire('Error', 'Error deleting appointment.',
                                    'error');
                            }
                        });
                    }
                });
            });

        });

        $(document).on('click', '.generate-appointment-link-btn', function() {

            let appointmentId = $(this).data('id');
            let status = $(this).data('status');

            if (status === 'paid') {
                Swal.fire({
                    icon: 'info',
                    title: 'No Pending Amount',
                    text: 'This appointment payment is already completed.'
                });
                return;
            }

            Swal.fire({
                title: 'Generate Payment Link',
                html: `
            <label style="float:left;margin-left:50px;">Enter Amount (₹)</label>
            <input type="number"
                id="paymentAmount"
                class="swal2-input"
                placeholder="Enter amount"
                min="1"
                style="width:80%;margin:10px auto;">
        `,
                showCancelButton: true,
                confirmButtonText: 'Generate Link',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#cfece0',
                cancelButtonColor: '#f89884',
                customClass: {
                    confirmButton: 'swal-confirm-btn'
                },
                preConfirm: () => {
                    const amount = document.getElementById('paymentAmount').value;
                    if (!amount || amount <= 0) {
                        Swal.showValidationMessage('Please enter a valid amount');
                    }
                    return amount;
                }
            }).then((result) => {

                if (!result.isConfirmed) return;

                $.ajax({
                    url: `/api/razorpay/appointment/generate-payment-link`,
                    type: 'POST',
                    data: {
                        appointment_id: appointmentId,
                        amount: result.value,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {

                        navigator.clipboard.writeText(res.payment_link);

                        Swal.fire({
                            icon: 'success',
                            title: 'Payment Link Generated',
                            html: `
                        <p><strong>Patient Name:</strong> ${res.patient_name}</p>
                        <p><strong>Contact No:</strong> ${res.patient_phone}</p>
                        <p><strong>Amount:</strong> ₹${res.amount}</p>

                        <input type="text"
                            value="${res.payment_link}"
                            readonly
                            style="width:100%;padding:12px;margin-top:15px;border-radius:25px;border:1.5px solid #ff8c6b;font-size:18px;"
                        />

                        <div style="text-align:center;font-size:13px;margin-top:6px;">
                            Link copied to clipboard ✔
                        </div>
                    `,
                            showConfirmButton: false, // ❌ removes OK button
                            showCancelButton: true, // ✅ only Close button
                            cancelButtonText: 'Close',
                            cancelButtonColor: '#f89884',
                        });
                    },
                    error: function(xhr) {
                        Swal.fire('Error',
                            xhr.responseJSON?.message || 'Failed to generate payment link',
                            'error');
                    }
                });
            });
        });
    </script>

    <style>
        /* .card-footer {
                                                                                                                                                        border-top-left-radius: 20px;
                                                                                                                                                        border-top-right-radius: 20px;
                                                                                                                                                    } */

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
