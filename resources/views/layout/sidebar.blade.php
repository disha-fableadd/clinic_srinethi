@php
    use App\Models\Setting;

    $currentProjectTypeId = (int) Setting::getValue('project_type_id', 1);
@endphp


<style>
    .menu-link.active {

        color: black;

        background-color: #cfece0;

        border-radius: 15px;

        margin: 0 10px;

        padding: 0 1px;

    }



    .submenu.active>a {

        color: black;

        background-color: #cfece0;

        border-radius: 15px;

        margin: 0 10px;

        padding: 12px 15px !important;

    }



    .menu-link1.active {

        background-color: #cfece0;

        color: black;

        border-radius: 15px;

        /* margin: 0 10px ; */



    }



    .submenu ul {

        /* padding-left: 20px; */

        color: black;

    }



    .submenu.active>a .menu-arrow {

        transform: rotate(90deg);

        transition: transform 0.3s ease;

    }
</style>



<div class="sidebar" id="sidebar">

    <div class="sidebar-inner slimscroll">

        <div id="sidebar-menu" class="sidebar-menu mt-3">

            <ul>
                <!-- <li class="menu-title">Main</li> -->


                <li class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">

                    <a href="{{ route('dashboard') }}"> <i class="fa fa-tachometer-alt"></i> <span>Dashboard</span></a>

                </li>



                @if (app('hasPermission')(3, 'view'))

                    <li class="submenu {{ request()->routeIs('user.*') ? 'active' : '' }}">

                        <a href="#"><i class="fa fa-user-md"></i><span>Staffs</span><span
                                class="menu-arrow"></span></a>

                        <ul style="{{ request()->routeIs('user.*') ? 'display: block;' : 'display: none;' }}">

                            @if (app('hasPermission')(3, 'view'))
                                <li class="menu-link1 {{ request()->routeIs('user.index') ? 'active' : '' }} mt-1">

                                    <a href="{{ route('user.index') }}"><i class="fa fa-users icons "></i><span
                                            style="

                                                                                                                                                                                                                                                            padding-left: 5px;">
                                            All
                                            Staffs

                                        </span></a>

                                </li>
                            @endif

                            @if (app('hasPermission')(3, 'create'))
                                <li class="menu-link1 {{ request()->routeIs('user.create') ? 'active' : '' }} mt-1">

                                    <a href="{{ route('user.create') }}"><i class="fa fa-user-plus icons"></i><span
                                            style="

                                                                                                                                                                                                                                                            padding-left: 5px;">
                                            Add
                                            Staff

                                        </span></a>

                                </li>
                            @endif



                        </ul>

                    </li>

                @endif


               


                @php
                    $isPatientRole = optional(Auth::user()?->role)->name === 'Patient';
                    $hasPatient = app('hasPermission')(5, 'view') || app('hasPermission')(5, 'create');
                    $hasDiagnosis = app('hasPermission')(35, 'view') || app('hasPermission')(35, 'create');
                    $hasReferalDoctor = app('hasPermission')(36, 'view') || app('hasPermission')(36, 'create');
                @endphp

                @if ($hasPatient || $hasDiagnosis || $hasReferalDoctor)
                    <li
                        class="submenu {{ request()->routeIs('patients.*', 'diagnosis.*', 'referaldoctor.*') ? 'active' : '' }}">
                        <a href="#">
                            <i class="fa fa-wheelchair"></i>
                            <span>Patients</span>
                            <span class="menu-arrow"></span>
                        </a>

                        <ul
                            style="{{ request()->routeIs('patients.*', 'diagnosis.*', 'referaldoctor.*') ? 'display: block;' : 'display: none;' }}">
                            {{-- Patients --}}
                            @if (app('hasPermission')(5, 'view'))
                                <li class="menu-link1 {{ request()->routeIs('patients.index') ? 'active' : '' }} mt-1">
                                    <a href="{{ route('patients.index') }}">
                                        <i class="fa fa-procedures icons"></i>
                                        <span style="padding-left: 5px;">All Patients</span>
                                    </a>
                                </li>
                            @endif

                            @if (app('hasPermission')(5, 'create'))
                                <li
                                    class="menu-link1 {{ request()->routeIs('patients.create') ? 'active' : '' }} mt-1">
                                    <a href="{{ route('patients.create') }}">
                                        <i class="fa fa-user-injured icons"></i>
                                        <span style="padding-left: 5px;">Add Patient</span>
                                    </a>
                                </li>
                            @endif

                            {{-- Diagnosis --}}
                            @if (app('hasPermission')(35, 'view'))
                                <li
                                    class="menu-link1 {{ request()->routeIs('diagnosis.index') ? 'active' : '' }} mt-1">
                                    <a href="{{ route('diagnosis.index') }}">
                                        <i class="fa fa-stethoscope icons"></i>
                                        <span style="padding-left: 5px;">All Diagnosis</span>
                                    </a>
                                </li>
                            @endif

                            

                            @if (app('hasPermission')(36, 'view'))
                                <li class="menu-link1 {{ request()->routeIs('referaldoctor.index') ? 'active' : '' }}">

                                    <a href="{{ route('referaldoctor.index') }}">
                                        <i class="fa-solid fa-user-doctor"></i>
                                        <span> All Reference Doctor</span></a>
                                </li>
                            @endif

                             @if (app('hasPermission')(5, 'view'))
                                <li class="menu-link1 {{ request()->routeIs('symptoms.index') ? 'active' : '' }}">

                                    <a href="{{ route('symptoms.index') }}"> <i class="fa fa-medkit "></i> <span> All
                                            Symptoms</span></a>

                                </li>
                            @endif
                        </ul>
                    </li>
                @endif


                @php
                    $hasAppointmentView = app('hasPermission')(6, 'view');
                    $hasAppointmentCreate = app('hasPermission')(6, 'create') && !$isPatientRole;
                @endphp

                @if ($hasAppointmentView || $hasAppointmentCreate)

                    <li class="submenu {{ request()->routeIs('appointment.*') ? 'active' : '' }}">

                        <a href="#"><i class="fa fa-calendar"></i>

                            <span>Appointments</span><span class="menu-arrow"></span></a>

                        <ul style="{{ request()->routeIs('appointment.*') ? 'display: block;' : 'display: none;' }}">

                            @if ($hasAppointmentView)
                                <li
                                    class="menu-link1 {{ request()->routeIs('appointment.index') ? 'active' : '' }} mt-1">

                                    <a href="{{ route('appointment.index') }}"><i
                                            class="fa fa-calendar-check icons"></i><span
                                            style="

                                                                                                                                                                                                                                                                            padding-left: 5px;">All

                                            Appointments</span> </a>

                                </li>
                            @endif

                            @if ($hasAppointmentCreate)
                                <li
                                    class="menu-link1 {{ request()->routeIs('appointment.create') ? 'active' : '' }} mt-1">

                                    <a href="{{ route('appointment.create') }}"><i
                                            class="fa fa-calendar-plus icons"></i><span
                                            style="

                                                                                                                                                                                                                                                                            padding-left: 5px;">

                                            Add

                                            Appointment</span></a>

                                </li>
                            @endif



                        </ul>

                    </li>

                @endif

                @php
                    $hasFollowupView = app('hasPermission')(2, 'view');
                    $hasFollowupCreate = app('hasPermission')(2, 'create') && !$isPatientRole;
                @endphp

                @if ($hasFollowupView || $hasFollowupCreate)

                    <li class="submenu {{ request()->routeIs('followup.*') ? 'active' : '' }}">

                        <a href="#"><i class="fa fa-user"></i> <span>Followup</span><span
                                class="menu-arrow"></span></a>

                        <ul style="{{ request()->routeIs('followup.*') ? 'display: block;' : 'display: none;' }}">

                            @if ($hasFollowupView)
                                <li class="menu-link1 {{ request()->routeIs('followup.index') ? 'active' : '' }} mt-1">

                                    <a href="{{ route('followup.index') }}"><i class="fa fa-list icons"></i><span
                                            style="

                                                                                                                                                                                                                                                            padding-left: 5px;">
                                            All

                                            Followup</span></a>

                                </li>
                            @endif

                            @if ($hasFollowupCreate)
                                <li
                                    class="menu-link1 {{ request()->routeIs('followup.create') ? 'active' : '' }} mt-1">

                                    <a href="{{ route('followup.create') }}"><i
                                            class="fa fa-plus-circle icons"></i><span
                                            style="

                                                                                                                                                                                                                                                            padding-left: 5px;">
                                            Add
                                            Followup

                                        </span></a>

                                </li>
                            @endif

                        </ul>

                    </li>

                @endif

              






                @php
                    $hasTreatment = app('hasPermission')(7, 'view') || app('hasPermission')(7, 'create');
                    $hasTreatmentBooking = app('hasPermission')(31, 'view') || app('hasPermission')(31, 'create');
                    $hasSymptoms = app('hasPermission')(7, 'view') || app('hasPermission')(7, 'create');
                @endphp

                @if ($hasTreatment || $hasTreatmentBooking)
                    <li
                        class="submenu {{ request()->routeIs('treatment.*', 'treatment_booking.*', 'symptoms.*') ? 'active' : '' }}">
                        <a href="#">
                            <i class="fa-solid fa-user-doctor"></i> {{-- Changed icon --}}
                            <span>Treatments</span>
                            <span class="menu-arrow"></span>
                        </a>

                        <ul
                            style="{{ request()->routeIs('treatment.*', 'treatment_booking.*', 'symptoms.*') ? 'display: block;' : 'display: none;' }}">
                            {{-- Treatment --}}
                            @if (app('hasPermission')(7, 'view'))
                                <li
                                    class="menu-link1 {{ request()->routeIs('treatment.index') ? 'active' : '' }} mt-1">
                                    <a href="{{ route('treatment.index') }}">
                                        <i class="fa fa-stethoscope icons"></i>
                                        <span style="padding-left: 5px;">All Treatments</span>
                                    </a>
                                </li>
                            @endif

                            @if (app('hasPermission')(7, 'create'))
                                <li
                                    class="menu-link1 {{ request()->routeIs('treatment.create') ? 'active' : '' }} mt-1">
                                    <a href="{{ route('treatment.create') }}">
                                        <i class="fa fa-notes-medical icons"></i>
                                        <span style="padding-left: 5px;">Add Treatment</span>
                                    </a>
                                </li>
                            @endif





                            

                           

                             
                   
              
                        </ul>
                    </li>
                @endif





             

               
              



   @if ($currentProjectTypeId !== 3)
                @if (app('hasPermission')(18, 'view'))
                    <li class="menu-link {{ request()->routeIs('invoice.index') ? 'active' : '' }}">


                        <a href="{{ route('invoice.index') }}"> <i class="fa fa-file-invoice"></i>
                            <span>Invoice</span></a>

                    </li>
                @endif
  @endif

                @if (app('hasPermission')(14, 'view'))
                    <li class="menu-link {{ request()->routeIs('chart') ? 'active' : '' }}">

                        <a href="{{ route('chart') }}"> <i class="fas fa-chart-line"></i> <span>Report</span></a>

                    </li>
                @endif

              


              

                {{-- @if (app('hasPermission')(15, 'view'))

                    <li class="submenu {{ request()->is('sms*') ? 'active' : '' }}">

                        <a href="#"><i class="fas fa-cogs icons"></i> <span>Setting</span><span
                                class="menu-arrow"></span></a>

                        <ul style="{{ request()->is('sms*') ? 'display: block;' : 'display: none;' }}">


                            @if (app('hasPermission')(15, 'view'))
                                <li class="menu-link1 {{ request()->routeIs('sms.template') ? 'active' : '' }} mt-1">

                                    <a href="{{ route('sms.template') }}"><i class="fas fa-cogs icons"></i><span
                                            style="padding-left: 5px;">Settings</span></a>

                                </li>
                            @endif



                        </ul>

                    </li>

                @endif   --}}

                @if (app('hasPermission')(36, 'view'))
                    <li class="menu-link {{ request()->routeIs('taxrate.index') ? 'active' : '' }}">
                        <a href="{{ route('taxrate.index') }}">
                            <i class="fa fa-money-bill"></i>
                            <span>Tax Rate</span>
                        </a>
                    </li>
                @endif


                                 <li><a href="{{ route('plans.myplan') }}"><i class="fa-solid fa-file-invoice-dollar"></i>My Plan Details</a></li>



                @if (app('hasPermission')(15, 'view'))
                    <li class="menu-link {{ request()->routeIs('sms.template') ? 'active' : '' }}">
                        <a href="{{ route('sms.template') }}">
                            <i class="fas fa-cogs icons"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                @endif







                @php
                    $user = Auth::user();
                    $role = optional($user->role)->name;
                @endphp


                @if ($role !== 'Patient')
                    <li class="menu-link {{ request()->routeIs('knowledge.index') ? 'active' : '' }}">
                        <a href="{{ route('knowledge.index') }}">
                            <i class="fas fa-book"></i> <span>Knowledge Base</span>
                        </a>
                    </li>
                @endif



                <li class="menu-link {{ request()->routeIs('profile') ? 'active' : '' }}">
                    <a href="{{ route('profile') }}"><i class="fa fa-user"></i> <span>My Profile</span></a>
                </li>

                <li class="menu-link ">
                    <a href="#" id="logout-button"><i class="fa fa-sign-out-alt"></i> <span>Logout</span></a>
                </li>

            </ul>

        </div>

    </div>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).on('click', '#logout-button', function(e) {
        e.preventDefault(); // Prevent default link behavior

        console.log("Logout button clicked"); // Debugging

        $.ajax({
            url: "/api/logout",
            method: "POST",
            headers: {
                "Authorization": "Bearer " + token,
                "Content-Type": "application/json",
            },
            success: function(response) {
                console.log("Logout successful:", response);

                // Clear storage
                sessionStorage.removeItem('token');
                sessionStorage.clear();

                // Redirect to login page
                window.location.href = "/";
            },
            error: function(xhr) {
                console.error("Logout failed:", xhr.responseText);

                // Force logout
                sessionStorage.removeItem('token');
                sessionStorage.clear();
                window.location.href = "/";
            }
        });
    });
</script>
