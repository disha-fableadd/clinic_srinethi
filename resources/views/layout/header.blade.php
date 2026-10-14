<style>
    .dropdown-item.active,
    .dropdown-item:active {
        color: #16181b;
        text-decoration: none;
        background-color: #cfece0;
        border-radius: 10px;
    }


    .text-black {
        color: #2e2e2e;
    }

    .notification-link,
    .notification-link:hover {
        color: #2e2e2e;
    }

    .notification-item {
        display: flex;
        align-items: center;
        padding: 15px 10px;
        transition: 0.3s;
    }

    .dropdown-menu .dropdown-divider {
        color: #a5c5fe;
        margin: 0;
    }

    .notifications .notification-item h4 {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 5px;
        font-weight: 500;
    }

    .notifications .notification-item p {
        font-size: 13px;
        margin-bottom: 3px;
        color: #919191;
    }

    .header .custom-btn {

        background-color: #cfece0;

        color: #2e2e2e;

        margin-top: 0px;

        border: none;

        padding: 8px 18px;

        border-radius: 11px;

        font-size: 16px;

        cursor: pointer;

        transition: background-color 0.3s ease;

        box-shadow: 0 2px 3px rgba(0, 0, 0, 0.15);

    }



    .header .custom-btn1 {

        background-color: #cfece0;

        color: #2e2e2e;

        margin: 0px 6px;

        border: none;

        padding: 8px 14px;

        border-radius: 50px;

        font-size: 18px;

        cursor: pointer;

        transition: background-color 0.3s ease;

        box-shadow: 0 2px 3px rgba(0, 0, 0, 0.15);

    }



    .header .search {

        width: 100%;

        position: relative;

        display: flex;

    }



    .header .searchTerm {

        width: 100%;

        border: none;

        border-right: none;

        padding: 10px;

        height: 42px;

        border-radius: 30px 0px 0px 30px;

        outline: none;

        color: rgba(136, 136, 136, 0.78);

        background: #f8f6f5;

    }



    .header .searchTerm:focus {

        color: #888888;

    }



    .header .searchButton {

        width: 75px;

        height: 42px;

        border: none;

        background: #f8f6f5;

        text-align: center;

        color: #888888;

        border-radius: 0 30px 30px 0;

        cursor: pointer;

        font-size: 20px;

    }



    /* (.header .wrap search positioning unified below) */




    .header #toggle_btn {

        color: #888;

    }



    .regular-logo {
        display: block;
        width: auto;
        max-width: 150px;
        max-height: 48px;
        object-fit: contain;
    }



    .mini-logo {

        display: none;

    }



    .mini-sidebar .regular-logo {

        display: none;

    }



    .mini-sidebar .mini-logo {

        display: block;

    }

    /* (button styles unified below) */


    @media (max-width: 1024px) {

        .chart-container {

            width: auto;

            /* height: auto; */

        }



        .header .wrap {

            /* width: 30%;

            position: absolute;

            top: 50%;

            left: 38%;

            transform: translate(-50%, -50%); */

            display: none;

        }



        .appointments {

            display: none !important;

        }

    }

    @media (min-width: 1024px) {

        .appointments {

            display: inline-block !important;

        }


    }


    @media (min-width: 768px) {

        #appointmentStatusChart_admin canvas {
            width: 706px !important;

        }


    }

    @media (max-width: 430px) {

        .header .wrap {

            display: none;

        }

    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 10px;
        /* spacing between icon and logo */
    }

    .mobile_btn {
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: #333;
    }

    /* (Responsive header rules unified below) */




    .mini-sidebar .header .wrap {
        left: 22%;
        /* Adjust the value as per your UI */
    }

    .search-results {

        z-index: 1000;

        max-height: 300px;

        overflow-y: auto;

        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);

    }



    .search-results li {

        cursor: pointer;

        color: black;

    }



    .search-results li:hover {

        background-color: #f0f0f0;

    }

    .sidebar-search-container {
        width: 100%;
        /* Adjust width as needed */
        max-width: 350px;
        /* Increase the max-width */
    }

    #sidebar-search {
        width: 100%;
        /* Ensures input takes full container width */
    }

    .search-results {
        width: 100%;
        /* Ensures dropdown matches input width */
        max-width: 350px;
        /* Same as container */
    }

    .header .custom-btn {
        background-color: #cfece0;
        color: #2e2e2e;
        margin-top: 0px;
        border: none;
        padding: 8px 12px;
        border-radius: 11px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        box-shadow: 0 2px 3px rgba(0, 0, 0, 0.15);
    }

    /* (branchDropdown styles unified below) */


    #sidebar-search {
        width: 76%;
    }

    /* (.profile-dropdown styles unified below) */

    .custom-close {
        background-color: #f5b6a5 !important;
        opacity: 1;
        border: 1px solid #f5b6a5;
        border-radius: 5px;
        padding: 3px 6px;
    }

    @media (max-width: 768px) {

        #branchDropdown,
        .custom-btn {
            padding: 6px 10px !important;
            font-size: 14px !important;
            white-space: normal;
            text-align: left;
        }

        #selectedBranch {
            display: inline-block;
            max-width: 100px;
            overflow: hidden;
            text-overflow: ellipsis;
            vertical-align: middle;
        }

        /* (responsive profile-dropdown unified below) */
    }

    .swal-ok-btn {
        background-color: #ff8c6b !important;
        color: #000 !important;
        padding: 8px 25px !important;
        border-radius: 20px !important;
        border: none !important;
        font-weight: 500;
    }

    .swal-ok-btn:hover {
        background-color: #ff7a55 !important;
    }


    /* =======================================================
       Header Flexbox & Responsive Layout Fix
       ======================================================= */
    .header {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        padding-right: 15px !important;
        overflow: visible !important;
        background-color: #fff;
        height: 65px;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1039;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
    }

    .header .header-left {
        float: none !important;
        position: relative !important;
        width: 230px !important;
        height: 65px !important;
        padding: 0 20px !important;
        display: flex !important;
        align-items: center !important;
        flex-shrink: 0 !important;
        order: 1;
        z-index: 2;
    }

    .mini-sidebar .header .header-left {
        width: 60px !important;
        padding: 0 5px !important;
    }

    .header #toggle_btn {
        float: none !important;
        position: relative !important;
        order: 2;
        width: 50px !important;
        height: 65px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex-shrink: 0 !important;
        color: #888;
        font-size: 20px;
        text-decoration: none !important;
    }

    .header #toggle_btn:hover {
        color: #333;
    }

    .header #mobile_btn {
        display: none !important;
    }

    .header .wrap,
    .mini-sidebar .header .wrap {
        position: static !important;
        transform: none !important;
        left: auto !important;
        top: auto !important;
        order: 3 !important;
        flex: 0 1 220px !important;
        max-width: 220px !important;
        min-width: 140px !important;
        margin: 0 15px 0 10px !important;
        display: flex !important;
        align-items: center !important;
    }

    .header .sidebar-search-container {
        width: 100% !important;
        max-width: 220px !important;
    }

    .header #sidebar-search {
        width: 100% !important;
        height: 38px !important;
        border-radius: 20px !important;
        background-color: #f5f7f9 !important;
        border: 1px solid #e2e8f0 !important;
        padding: 6px 15px !important;
        font-size: 13px !important;
    }

    @media (max-width: 1400px) {
        .header .wrap,
        .mini-sidebar .header .wrap {
            display: none !important;
        }
    }

    /* Header User Menu Base Container */
    .header .nav.user-menu {
        order: 4;
        float: none !important;
        display: flex !important;
        align-items: center !important;
        flex-wrap: nowrap !important;
        gap: 8px;
        margin-left: auto !important;
        margin-right: 0 !important;
        padding-left: 0;
        margin-bottom: 0;
        list-style: none;
        flex-shrink: 0;
        height: 65px;
        position: relative;
        z-index: 99;
    }

    .header .nav.user-menu > li {
        margin: 0 !important;
        display: flex !important;
        align-items: center !important;
        flex-shrink: 0;
    }

    .header .nav.user-menu .dropdown-menu {
        border-radius: 14px;
    }

    .header .nav.user-menu .dropdown-item {
        padding-top: 10px;
        padding-bottom: 10px;
    }

    .header .nav.user-menu .dropdown-item h6 {
        font-size: 14px;
    }

    .header .nav.user-menu .dropdown-item small {
        font-size: 12px;
    }

    .header .project-type-menu {
        width: min(260px, calc(100vw - 32px)) !important;
        position: absolute !important;
        top: 100% !important;
        left: 50% !important;
        right: auto !important;
        transform: translateX(-50%) !important;
    }

    /* Header Dropdowns Normalization */
    .header .nav.user-menu > li.dropdown {
        position: relative !important;
    }

    .header .profile-dropdown {
        position: absolute !important;
        top: 100% !important;
        right: 0 !important;
        left: auto !important;
        transform: none !important;
        will-change: auto !important;
        min-width: 210px !important;
        border-radius: 14px !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.14) !important;
        border: 1px solid rgba(0, 0, 0, 0.08) !important;
        padding: 8px !important;
        margin-top: 4px !important;
        background-color: #fff !important;
        z-index: 1050 !important;
    }

    .header .drop-down-notification {
        position: absolute !important;
        top: 100% !important;
        /* right: 0 !important; */
        left: auto !important;
        transform: none !important;
        will-change: auto !important;
        margin-top: 4px !important;
        border-radius: 14px !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.14) !important;
        border: 1px solid rgba(0, 0, 0, 0.08) !important;
        max-height: 400px;
        overflow: auto;
        width: 300px;
        max-width: calc(100vw - 24px) !important;
        z-index: 1050 !important;
    }

    /* User Profile Avatar */
    .header .user-img {
        display: inline-block;
        position: relative;
        width: 38px;
        height: 38px;
    }

    .header .user-img img {
        width: 38px !important;
        height: 38px !important;
        border-radius: 50%;
        object-fit: cover;
    }

    .header .user-menu.nav > li > a.user-link {
        padding: 0 4px !important;
        /* height: 65px !important;
         */
        display: flex;
        align-items: center;
    }

    .header .user-link::after {
        display: none !important;
    }

    /* Notification Bell Base Styling */
    .header .nav-link.nav-icon {
        height: 38px;
        width: 38px;
        padding: 0 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 11px;
        position: relative !important;
        color: #4b5563 !important;
        transition: background-color 0.2s ease;
    }

    .header .nav-link.nav-icon:hover {
        background-color: #f3f4f6 !important;
        color: #111 !important;
    }

    .header .nav-link.nav-icon .badge-number {
        position: absolute !important;
        top: -3px !important;
        right: -3px !important;
        font-size: 10px !important;
        padding: 2px 5px !important;
        border-radius: 10px !important;
        background-color: #cfece0 !important;
        color: #111 !important;
        font-weight: 600 !important;
        border: 1px solid #fff !important;
        line-height: 1 !important;
    }

    .header .nav-link.nav-icon::after {
        display: none !important;
    }

    /* Plan Badge Base */
    .header-plan-item {
        margin-right: 0;
        flex-shrink: 0;
    }

    .header-current-plan-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        color: #d97706; /* gold/amber crown */
        font-size: 14px;
        flex-shrink: 0;
    }

    .header-current-plan-copy {
        display: flex;
        flex-direction: column;
        justify-content: center;
        text-align: left;
        line-height: 1.15;
    }

    .header-current-plan-copy .header-plan-title {
        font-size: 13px;
        font-weight: 600;
        color: #1f2937;
        line-height: 1.15;
        max-width: 100px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .header-current-plan-copy .header-plan-expiry {
        font-size: 10px;
        font-weight: 500;
        color: #0f766e;
        line-height: 1;
        margin-top: 1px;
        white-space: nowrap;
    }

    /* Desktop View Styling (>= 992px) */
    @media (min-width: 992px) {
        .header .custom-btn,
        .header .header-current-plan {
            height: 38px !important;
            min-height: 38px !important;
            max-height: 38px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            padding: 6px 12px !important;
            border-radius: 11px !important;
            font-size: 13.5px !important;
            font-weight: 500 !important;
            line-height: 1.2 !important;
            background-color: #cfece0 !important;
            color: #2e2e2e !important;
            border: none !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08) !important;
            white-space: nowrap !important;
            transition: all 0.2s ease !important;
            text-decoration: none !important;
            margin: 0 !important;
        }

        .header .custom-btn:hover,
        .header .header-current-plan:hover {
            background-color: #bee5d5 !important;
            color: #111827 !important;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.14) !important;
            text-decoration: none !important;
        }

        .header .custom-btn i {
            font-size: 14px !important;
            margin-right: 6px !important;
            margin-left: 0 !important;
            flex-shrink: 0 !important;
            display: inline-flex;
            align-items: center;
        }

        .header .dropdown-toggle::after {
            margin-left: 6px !important;
            vertical-align: 0.15em !important;
        }

        .header .nav-link.nav-icon {
            height: 44px !important;
            width: 38px !important;
            border-radius: 11px !important;
        }
    }

    /* Responsive Breakpoints */
    @media (max-width: 1250px) {
        .header-plan-expiry {
            display: none !important;
        }
        .header-current-plan {
            padding: 6px 10px;
            gap: 6px;
        }
        .appointments,
        .appointments-item,
        .header-payment-btn,
        .header-register-btn,
        .header .custom-btn.appointments {
            display: none !important;
        }
    }

    @media (max-width: 1024px) {
        .header {
            padding: 0 10px 0 0 !important;
        }

        .header #mobile_btn {
            display: flex !important;
            align-items: center;
            justify-content: center;
            width: 46px !important;
            height: 65px !important;
            position: static !important;
            float: none !important;
            order: 1 !important;
            color: #333 !important;
            font-size: 20px !important;
            flex-shrink: 0 !important;
            z-index: 10;
            text-decoration: none !important;
        }

        .header .header-left {
            position: static !important;
            float: none !important;
            order: 2 !important;
            width: auto !important;
            max-width: 130px !important;
            height: 65px !important;
            padding: 0 5px 0 0 !important;
            display: flex !important;
            align-items: center !important;
            flex-shrink: 0 !important;
        }

        .header .regular-logo {
            max-width: 110px !important;
            max-height: 40px !important;
            width: auto !important;
            height: auto !important;
        }

        .header #toggle_btn {
            display: none !important;
        }

        .header .wrap {
            display: none !important;
        }

        .header .nav.user-menu {
            order: 3 !important;
            margin-left: auto !important;
            gap: 6px !important;
            flex-shrink: 1 !important;
            min-width: 0 !important;
        }

        #selectedProjectTypeLabel {
            display: none !important;
        }
    }

    @media (max-width: 768px) {
        .header .project-type-menu {
        width: min(260px, calc(100vw - 32px)) !important;
        position: absolute !important;
        top: 100% !important;
        left: 0% !important;
        right: auto !important;
        transform: translateX(-50%) !important;
    }
     .header .drop-down-notification {
        position: absolute !important;
        top: 100% !important;
        /* right: 0 !important; */
        left: auto !important;
        transform: none !important;
        will-change: auto !important;
        margin-top: 4px !important;
        border-radius: 14px !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.14) !important;
        border: 1px solid rgba(0, 0, 0, 0.08) !important;
        max-height: 400px;
        overflow: auto;
        width: 300px;
        max-width: calc(100vw - 24px) !important;
        z-index: 1050 !important;
    }
        .header {
            padding: 0 8px 0 0 !important;
        }

        .header #mobile_btn {
            width: 40px !important;
            font-size: 18px !important;
        }

        .header .header-left {
            max-width: 100px !important;
        }

        /* .header .regular-logo {
            max-width: 85px !important;
            max-height: 34px !important;
        } */

        .header .nav.user-menu {
            gap: 4px !important;
        }

        .header .custom-btn:not(.appointments) {
            padding: 6px 8px !important;
            font-size: 13px !important;
            height: 36px !important;
        }

        .appointments,
        .appointments-item,
        .header-payment-btn,
        .header-register-btn,
        .header .custom-btn.appointments {
            display: none !important;
        }

        #branchDropdown {
            width: auto !important;
            max-width: 95px !important;
            height: 36px !important;
            padding: 6px 8px !important;
        }

        #selectedBranch {
            max-width: 60px !important;
            font-size: 12px !important;
        }

        .header-current-plan {
            padding: 5px 8px !important;
            height: 36px !important;
            gap: 5px !important;
        }

        .header-current-plan-copy .header-plan-title {
            font-size: 12px !important;
            max-width: 65px !important;
        }

        .header .user-img,
        .header .user-img img {
            width: 34px !important;
            height: 34px !important;
        }

        .header .nav-link.nav-icon {
            padding: 0 6px !important;
        }

        .header .nav-link.nav-icon i {
            font-size: 15px !important;
        }
    }

    @media (max-width: 480px) {
        .header #mobile_btn {
            width: 36px !important;
            font-size: 17px !important;
        }

        .header .header-left {
            max-width: 80px !important;
        }

        /* .header .regular-logo {
            max-width: 75px !important;
            max-height: 30px !important;
        } */

        .header .nav.user-menu {
            gap: 4px !important;
        }

        /* All header action buttons (plan, branch, project type) are 34px x 34px compact squares */
        .header .custom-btn:not(.appointments),
        .header .header-current-plan,
        #branchDropdown {
            width: 34px !important;
            min-width: 34px !important;
            max-width: 34px !important;
            height: 34px !important;
            min-height: 34px !important;
            max-height: 34px !important;
            padding: 0 !important;
            margin: 0 !important;
            border-radius: 10px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 0 !important;
            background-color: #cfece0 !important;
            color: #2e2e2e !important;
            border: none !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08) !important;
        }

        /* Explicitly hide Daily Register and Generate Payment Link on mobile */
        .appointments,
        .appointments-item,
        .header-payment-btn,
        .header-register-btn,
        .header .custom-btn.appointments {
            display: none !important;
        }

        /* Center icons perfectly with no margins */
        .header .custom-btn:not(.appointments) i,
        .header .header-current-plan-icon,
        #branchDropdown i {
            margin: 0 !important;
            padding: 0 !important;
            font-size: 14px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .header-current-plan-icon {
            color: #d97706 !important;
        }

        /* Hide all text labels and caret arrows in mobile header buttons */
        .header-current-plan-copy,
        #selectedBranch,
        #selectedProjectTypeLabel,
        .header .custom-btn span,
        .header .dropdown-toggle::after {
            display: none !important;
        }

        /* User profile avatar */
        .header .user-img,
        .header .user-img img {
            width: 34px !important;
            height: 34px !important;
        }

        /* Notification icon */
        .header .nav-link.nav-icon {
            width: 34px !important;
            height: 34px !important;
            padding: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .header .nav-link.nav-icon i {
            font-size: 15px !important;
        }

        /* Profile dropdown opens anchored to the right under avatar */
        .header .profile-dropdown {
            position: absolute !important;
            right: 0 !important;
            left: auto !important;
            top: 100% !important;
            transform: none !important;
            min-width: 200px !important;
            max-width: calc(100vw - 20px) !important;
        }
    }


</style>


@php
    use App\Models\Notification;
    use App\Models\ProjectType;
    use App\Models\Setting;

    $user = Auth::user();
    $userRole = $user->role->name ?? null;

    $notifications = collect(); // Initialize as an empty collection
    $notificationCount = 0;
    if (!empty($user) && !empty($user->id)) {
        if ($userRole == 'Admin') {
            $notifications = Notification::where('is_read', false)->orderBy('created_at', 'desc')->get();
        } else {
            $notifications = Notification::where('receiver_id', $user->id ?? '')
                ->where('is_read', false)
                ->orderBy('created_at', 'desc')
                ->get();
        }
        $notificationCount = $notifications->count();
    }

    $currentProjectTypeId = (int) Setting::getValue('project_type_id', 1);
    $currentProjectType = ProjectType::find($currentProjectTypeId);
    $projectTypes = ProjectType::where('status', 1)->orderBy('id')->get();

    $headerPlan = null;
    $headerPlanName = 'Starter';
    $headerPlanExpiry = null;

    if ($user) {
        $headerPlan = $user->plan()->first() ?? \App\Models\Plan::find($user->plan_id ?? null);
    }

    if ($headerPlan) {
        $headerPlanName = $headerPlan->name ?? 'Starter';
        $headerPlanExpiry = $headerPlan->end_date ? \Carbon\Carbon::parse($headerPlan->end_date)->format('d-m-Y') : null;
    } else {
        $headerPlanName = Setting::where('key', 'current_plan_name')->value('value') ?? 'Starter';
        $headerPlanExpiry = Setting::where('key', 'current_plan_expiry')->value('value');
    }

@endphp

<div class="header">

    <div class="header-left d-flex align-items-center h-100">

        <a href="{{ route('dashboard') }}" class="logo">

            @php
                

                $clinicLogo =
                    Setting::where('key', 'clinic_logo')->value('value') ??
                    env('IMAGE_PATH') . 'admin/assets/img/cliniclogo.png';
                $clinicName = Setting::where('key', 'clinic_name')->value('value') ?? 'Clinic';
            @endphp

            <img src="{{ asset(env('IMAGE_PATH') . $clinicLogo) }}" class="regular-logo" alt="{{ $clinicName }}"
                style="height:auto;width:100%">
            {{-- <span class="clinic_name" style="font-size:15px !important">{{ $clinicName }}</span> --}}

        </a>

        <a href="{{ route('dashboard') }}" class="logo1">

            <img src="{{ asset(env('IMAGE_PATH') . $clinicLogo) }}" alt="Mini Logo" class="mini-logo"
                style="height:auto;width:70%;">


        </a>

    </div>



    <a id="toggle_btn" class="h-100 d-flex align-items-center" href="javascript:void(0);"><i class="fa fa-bars"></i></a>

    <a id="mobile_btn" class="mobile_btn float-left" href="#sidebar"><i class="fa fa-bars"></i></a>



    <div class="wrap">

        <div class="sidebar-search-container position-relative">

            <input type="text" id="sidebar-search" class="form-control" placeholder="Search...">

            <ul class="list-group search-results position-absolute w-100 d-none"></ul>

        </div>

    </div>


    <ul class="nav user-menu float-right d-flex align-items-center h-100">
        @auth
        <li class="nav-item me-2 d-flex align-items-center header-plan-item">
            <a href="{{ route('plans.myplan') }}" class="header-current-plan"
                title="{{ $headerPlanName }} - {{ $headerPlanExpiry ? 'Expires: ' . $headerPlanExpiry : 'View your plan' }}">
                <span class="header-current-plan-icon"><i class="fas fa-crown"></i></span>
                <span class="header-current-plan-copy">
                    <strong class="header-plan-title">{{ $headerPlanName }}</strong>
                    <small class="header-plan-expiry">{{ $headerPlanExpiry ? 'Expires: ' . $headerPlanExpiry : 'View plan' }}</small>
                </span>
            </a>
        </li>
        @endauth
        @auth
            @if (auth()->user()->role->name === 'Admin')
                <li class="nav-item dropdown has-arrow">
                    <a href="#" id="branchDropdown"
                        class="btn custom-btn dropdown-toggle d-flex align-items-center" data-toggle="dropdown">
                        <i class="fa-solid fa-building"></i>
                        <span id="selectedBranch">{{ ucfirst(session('branch_name') ?? 'Select Branch') }}</span>
                    </a>
                    <div class="dropdown-menu">
                        @foreach ($allBranches as $branch)
                            <a class="dropdown-item {{ session('branch_id') == $branch->id ? 'active' : '' }}"
                                href="javascript:void(0);"
                                onclick="setBranch('{{ $branch->id }}', '{{ $branch->name }}')">
                                {{ ucfirst($branch->name) }}
                            </a>
                        @endforeach
                    </div>
                </li>
            @else
                <li class="nav-item">
                    <a href="#" class="btn custom-btn d-flex align-items-center">
                        <i class="fa-solid fa-building"></i>
                        <span>{{ ucfirst(auth()->user()->branch->name ?? 'No Branch') }}</span>
                    </a>
                </li>
            @endif
        @endauth
        <li class="nav-item dropdown has-arrow">
            <a href="#" class="btn custom-btn dropdown-toggle d-flex align-items-center" data-toggle="dropdown" data-display="static">
                <i class="fas fa-layer-group"></i>
                <span id="selectedProjectTypeLabel">{{ $currentProjectType->name ?? 'Hospital HMS' }}</span>
            </a>

            <div class="dropdown-menu project-type-menu shadow border-0 rounded-lg p-2">

                @foreach ($projectTypes as $projectType)
                    <a href="javascript:void(0);"
                        class="dropdown-item rounded p-2 mb-2 {{ $currentProjectTypeId === $projectType->id ? 'active' : '' }}"
                        data-project-type-id="{{ $projectType->id }}"
                        data-project-type-name="{{ $projectType->name }}">
                        <div class="d-flex align-items-center">
                            <div class="mr-3">
                                <i class="fas {{ $projectType->icon ?? 'fa-hospital' }} fa-lg text-primary"></i>
                            </div>

                            <div>
                                <h6 class="mb-0 font-weight-bold" style="font-size: 14px;">
                                    {{ $projectType->name }}
                                </h6>
                                <small class="text-muted d-block" style="font-size: 11px;">
                                    {{ $projectType->code === 'hospital' ? 'Complete Hospital Management' : ($projectType->code === 'dental' ? 'Dental Clinic Management' : 'Physiotherapy Management') }}
                                </small>
                            </div>
                        </div>
                    </a>
                @endforeach

            </div>
        </li>

        @if (Setting::getValue('razorpay_status') === 'on')
            <li class="nav-item header-payment-btn appointments-item">
                <button class="btn custom-btn appointments" data-toggle="modal" data-target="#razorpay">
                    Generate Payment Link
                </button>
            </li>
        @endif

        @if (app('hasPermission')(30, 'create'))
            <li class="nav-item header-register-btn appointments-item">
                <a href="{{ route('daily_data.create') }}" class="btn custom-btn appointments">
                    <i class="fa-solid fa-plus me-1 mr-1"></i> Daily Register
                </a>
            </li>
        @endif

        {{-- <li class="nav-item dropdown has-arrow">

            @if (app('hasPermission')(6, 'create') && optional(auth()->user()?->role)->name !== 'Patient')
                <a href="{{ route('appointment.create') }}" style="padding-right:10px">

                    <button class="btn custom-btn appointments"><i class="fa-solid fa-plus"></i> <span class="hdr-btn-text">Create</span>
                        Appointment</button>

                </a>
            @endif

        </li> --}}


        <li class="nav-item dropdown has-arrow">
            <a href="#" class="nav-link nav-icon" data-toggle="dropdown" data-display="static">
                <i class="fa fa-bell"></i>
                @if ($notificationCount > 0)
                    <span class="badge badge-number">{{ $notificationCount ?? 0 }}</span>
                @endif
            </a>

            <ul class="dropdown-menu dropdown-menu-right notifications drop-down-notification"
                style="border: none; max-height: 400px; overflow: auto; width: 300px;">



                @if (isset($notifications) && $notifications->isEmpty())
                    <li class="dropdown-header" style="position: sticky; top: 0; background: white; z-index: 100; border-bottom: 1px solid #eee;">
                        You have no new notifications
                        <a href="#"><span class=" p-2 ms-2"> </span></a>
                    </li>
                @else
                    <li class="dropdown-header d-flex justify-content-between align-items-center" style="position: sticky; top: 0; background: white; z-index: 100; border-bottom: 1px solid #eee;">
                        New notifications
                        <a href="{{ route('notificationView') }}"><span class="badge rounded-pill p-2 ms-2 text-black"
                                style="background-color: #cfece0; font-size: small; font-weight: 500;">View
                                all</span></a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    @foreach ($notifications->take(5) as $notification)
                        @if (!$notification->read)
                            @php
                                $module = $notification->info['module'] ?? '';
                                $moduleId = $notification->info['module_id'] ?? '';
                                $href = '#'; // Default

                                if ($module === 'appointment') {
                                    $href = route('appointment.show', $moduleId);
                                } elseif ($module === 'followup') {
                                    $href = route('followup.show', $moduleId);
                                } else {
                                    $href = url()->current(); // Stay on the current page
                                }
                            @endphp

                            <a href="{{ $href }}" class="notification-link"
                                data-id="{{ $notification->id ?? '' }}"
                                data-url="{{ route('notification.update', $notification->id) }}">
                                <li class="notification-item">
                                    <div>
                                        <h4>{{ $notification->info['message'] ?? 'New Notification' }}</h4>
                                        <!-- <p>Creator: {{-- ucfirst($notification->sender_user->first_name ?? 'Creator') . ' ' . ucfirst($notification->sender_user->last_name ?? '') --}}</p> -->
                                        <p>Date: {{ optional($notification->created_at)->format('d/m/Y') ?? '' }}</p>
                                    </div>
                                </li>
                            </a>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                        @endif
                    @endforeach
                @endif


            </ul>
        </li>



        <li class="nav-item dropdown has-arrow">

            <a href="#" class="dropdown-toggle nav-link user-link" data-toggle="dropdown" data-display="static"
                style="height:65px !important">

                <span class="user-img">
                    <img class="rounded-circle mr-1 user-image"
                        src="{{ Auth::user()->profile ? asset(Auth::user()->profile) : asset('/admin/assets/img/img1.png') }}"
                        width="30" alt="Profile Image">

                    <span class="status online"></span>
                </span>




            </a>

            <div class="dropdown-menu dropdown-menu-right drop-down-name profile-dropdown">

                {{-- Profile Link --}}
                <a class="dropdown-item py-2 d-flex align-items-center" href="{{ route('profile') }}"
                    style="font-size: 16px;">
                    <img class="rounded-circle mr-2 user-image"
                        src="{{ Auth::user()->profile ? asset(Auth::user()->profile) : asset('/admin/assets/img/img1.png') }}"
                        width="30" alt="Profile Image">
                    <span class="user-name">{{ Auth::user()->name }}</span>
                </a>


                {{-- Knowledge Base Link --}}
                @php
                    $user = Auth::user();
                    $role = optional($user->role)->name;
                @endphp

                @if ($role !== 'Patient')
                    <a class="dropdown-item py-2 d-flex align-items-center {{ request()->routeIs('knowledge.index') ? 'active' : '' }}"
                        href="{{ route('knowledge.index') }}" style="font-size: 16px;">
                        <i class="fas fa-book mr-2"></i>
                        Knowledge Base
                    </a>
                @endif
                @if ($role == 'Patient')
                    <a href="#" class="dropdown-item py-2 d-flex align-items-center" data-toggle="modal"
                        data-target="#changePasswordModal" onclick="setUserId({{ Auth::id() }})"
                        style="font-size: 16px;">
                        <i class="fas fa-lock mr-2"></i>
                        Change Password
                    </a>
                @endif


                {{-- Branch link --}}
                @if ($role == 'Admin')
                    <a class="dropdown-item py-2 d-flex align-items-center {{ request()->routeIs('branch.index') ? 'active' : '' }}"
                        href="{{ route('branch.index') }}" style="font-size: 16px;">
                        <i class="fa-solid fa-building mr-2"></i>
                        My Branches
                    </a>
                @endif

                @if (auth()->user()->role->name === 'Admin')
                    <a class="dropdown-item py-2 d-flex align-items-center {{ request()->routeIs('sms.template') ? 'active' : '' }}"
                        href="{{ route('sms.template') }}" style="font-size: 16px;">
                        <i class="fas fa-cogs icons mr-2"></i></i>
                        Settings
                    </a>
                @endif

                {{-- Logout Link --}}
                <a class="dropdown-item py-2 d-flex align-items-center" href="#" id="logout-button"
                    style="font-size: 16px;">
                    <i class="fa fa-sign-out-alt mr-2"></i>
                    Logout
                </a>

            </div>


        </li>

    </ul>




</div>

{{-- <div class="current-date text-end px-3 py-2">
    <button class="btn custom-btn appointments" id="currentDateTime">
        {{ now()->format('d M Y, h:i A') }}
    </button>
</div> --}}

<!-- model change password -->
<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="changePasswordForm">

            <input type="hidden" id="userIdForPassword">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#cfece0">
                    <h5 class="modal-title">Change Password</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>

                    <br><br>
                    <div id="passwordsuccessMessage" class="alert alert-success" style="display:none;"></div>
                    <div id="passworderrorMessage" class="alert alert-danger" style="display:none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update Password</button>
                </div>
            </div>
        </form>

    </div>
</div>


{{-- model for Razorpay --}}
<div class="modal fade" id="razorpay" tabindex="-1">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header" style="background-color:#cfece0">
                <h5 class="modal-title">Payment Link</h5>
                <button type="button" class="custom-close js-close-razorpay">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="modal-body">
                <form id="razorpayFormpatient">
                    <!-- Patient -->
                    <div class="form-group">
                        <label>Patient</label>
                        <select class="form-control" id="razorpay_patient_id" name="patient_id">
                            <option value="">Select Patient</option>
                        </select>
                    </div>

                    <!-- Amount -->
                    <div class="form-group mt-2">
                        <label>Amount</label>
                        <input type="number" class="form-control" id="amount-razoray" name="amount"
                            placeholder="Enter amount">
                    </div>

                    <button type="submit" class="btn btn-primary btn-block mt-3">
                        Generate Payment Link
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>


{{-- script for branch select --}}


<script>
    let patientsLoaded = false; // global variable
    let patientsData = [];

    function loadPatientsrazorpay(selectedPatientId = null) {
        let branchId = localStorage.getItem('selectedBranchId');
        if (patientsLoaded) {
            // Already loaded, just select patient
            if (selectedPatientId) {
                $('#razorpay_patient_id').val(selectedPatientId).trigger('change');
            }
            return;
        }

        $.ajax({
            url: "{{ url('/api/razorpay-patient') }}",
            type: "GET",
            data: {
                branch_id: branchId
            },
            success: function(response) {
                if (response.status) {
                    patientsData = response.data;
                    let options = '<option value="">Select Patient</option>';
                    $.each(patientsData, function(index, patient) {
                        options +=
                            `<option value="${patient.id}">${patient.fullname} (${patient.phone})</option>`;
                    });
                    $('#razorpay_patient_id').html(options);

                    // Select clicked patient if provided
                    if (selectedPatientId) {
                        $('#razorpay_patient_id').val(selectedPatientId).trigger('change');
                    }

                    patientsLoaded = true;
                }
            },
            error: function() {
                alert('Failed to load patients');
            }
        });
    }

    $(document).ready(function() {
        let branchId = localStorage.getItem('selectedBranchId');


        // --- Open modal and select patient ---
        $(document).on('click', '.generate-payment-link', function() {
            let patientId = $(this).data('patient-id');
            $('#razorpay').modal('show');
            loadPatientsrazorpay(patientId);
        });
        $('#razorpay').on('shown.bs.modal', function() {
            let patientId = $(this).data('patient-id');
            $('#razorpay').modal('show');
            loadPatientsrazorpay(patientId);
        });

        // --- Submit Razorpay form ---
        $('#razorpayFormpatient').on('submit', function(e) {
            e.preventDefault();

            let patient_id = $('#razorpay_patient_id').val();
            let amount = $('#amount-razoray').val();

            if (!patient_id || !amount || amount <= 0) {
                Swal.fire('Info', 'Please select patient and enter valid amount.', 'info');
                return;
            }

            $.ajax({
                url: "{{ url('/api/razorpay/razorpay-patient') }}",
                type: "POST",
                data: {
                    patient_id: patient_id,
                    amount: amount,
                    _token: '{{ csrf_token() }}'
                },
                success: function(res) {
                    // Copy link
                    if (res.payment_link) {
                        navigator.clipboard.writeText(res.payment_link);
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Payment Link Generated',
                        html: `
                        <div style="text-align:center; margin-top:5px;">
                            <p><strong>Patient Name:</strong> ${res.patient_name ?? '-'}</p>
                            <p><strong>Contact No:</strong> ${res.patient_phone ?? '-'}</p>
                            <p><strong>Amount:</strong> ₹${amount}</p>
                            <input 
                                type="text"
                                value="${res.payment_link}"
                                readonly
                                style="
                                    width:100%;
                                    padding:12px;
                                    margin-top:15px;
                                    border-radius:25px;
                                    border: 1.5px solid #ff8c6b;
                                    text-align:left;
                                    font-size:20px;
                                    background:#fff;
                                    color: #4a4a4a;
                                "
                            >
                            <div style="text-align:center; font-size:13px; color:#666; margin-top:6px;">
                                Link copied to clipboard ✔
                            </div>
                        </div>
                    `,
                        showConfirmButton: true,
                        confirmButtonText: 'OK',
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: 'swal-ok-btn'
                        }
                    });

                    $('#razorpayFormpatient')[0].reset();
                    $('#razorpay').modal('hide');
                },
                error: function(xhr) {
                    Swal.fire('Error', xhr.responseJSON?.message ||
                        'Failed to generate payment link.', 'error');
                }
            });
        });


    });

    // --- Close modal button ---
    $(document).on('click', '.js-close-razorpay', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('#razorpay').modal('hide');
        $('#razorpayFormpatient')[0].reset();
        return false;
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Get branch info from backend session
        let branchId = "{{ session('branch_id') }}";
        let branchName = "{{ session('branch_name') }}";

        if (branchId && branchName) {
            // Store in localStorage
            localStorage.setItem('selectedBranchId', branchId);
            localStorage.setItem('selectedBranchName', branchName);

            // Update UI button
            let selectedBranchBtn = document.getElementById('selectedBranch');
            if (selectedBranchBtn) {
                selectedBranchBtn.innerText = branchName;
            }

            // Update hidden input (for forms)
            let hiddenInput = document.getElementById('branch_id');
            if (hiddenInput) {
                hiddenInput.value = branchId;
            }

            // Load data for the branch
            // loadBranchData(branchId);
        }
    });

    // Function to switch branch (if admin wants to select another branch)
    function setBranch(branchId, branchName) {
        localStorage.setItem('selectedBranchId', branchId);
        localStorage.setItem('selectedBranchName', branchName);

        document.getElementById('selectedBranch').innerText = branchName;

        let hiddenInput = document.getElementById('branch_id');
        if (hiddenInput) {
            hiddenInput.value = branchId;
        }

        // loadBranchData(branchId);
    }

    function setProjectType(projectTypeId, projectTypeName) {
        const tokenValue = token;

        if (!tokenValue || tokenValue === "null") {
            Swal.fire('Session expired', 'Please login again.', 'warning');
            return;
        }

        $.ajax({
            url: "/api/save-project-type",
            method: "POST",
            headers: {
                "Authorization": "Bearer " + tokenValue,
                "Content-Type": "application/json",
                "Accept": "application/json"
            },
            data: JSON.stringify({
                project_type_id: projectTypeId
            }),
            success: function(response) {
                const label = document.getElementById('selectedProjectTypeLabel');
                if (label) {
                    label.innerText = projectTypeName;
                }

                document.querySelectorAll('[data-project-type-id]').forEach(function(item) {
                    item.classList.remove('active');
                });

                const activeItem = document.querySelector('[data-project-type-id="' + projectTypeId + '"]');
                if (activeItem) {
                    activeItem.classList.add('active');
                }

                document.dispatchEvent(new CustomEvent('projectTypeChanged', {
                    detail: {
                        projectTypeId: parseInt(projectTypeId)
                    }
                }));


                Swal.fire({
                    icon: 'success',
                    title: 'Project updated',
                    text: response?.message || 'Project type updated successfully.',
                    timer: 1400,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Unable to update project type.';
                Swal.fire('Error', message, 'error');
            }
        });
    }

    $(document).on('click', '.project-type-menu [data-project-type-id]', function(e) {
        e.preventDefault();

        const projectTypeId = $(this).data('project-type-id');
        const projectTypeName = $(this).data('project-type-name');

        setProjectType(projectTypeId, projectTypeName);
    });
</script>




<script>
    let token = @json(session('access_token'));
    let userId = @json(session('user_id'));
    let role = @json(session('role'));
    let permission = @json(session('permissions'));

    console.log(token);
    // console.log(userId);
    // console.log(role);
    // console.log(permission);



    if (!token || token === "null" || token === null) {
        window.location.href = "{{ route('login') }}";
    }



    $(document).on('click', '#logout-button', function(e) {
        e.preventDefault(); // Prevent default link behavior

        // console.log("Logout button clicked"); // Debugging

        $.ajax({
            url: "/api/logout",
            method: "POST",
            headers: {
                "Authorization": "Bearer " + token,
                "Content-Type": "application/json",
            },
            success: function(response) {
                // console.log("Logout successful:", response);

                // 🔹 Clear both storages
                sessionStorage.removeItem('token');
                sessionStorage.clear();
                localStorage.removeItem('selectedBranchId');
                localStorage.removeItem('selectedBranchName');
                localStorage.clear();

                // Redirect to login page
                window.location.href = "/";
            },
            error: function(xhr) {
                // console.error("Logout failed:", xhr.responseText);

                // 🔹 Force clear storages anyway
                sessionStorage.removeItem('token');
                sessionStorage.clear();
                localStorage.removeItem('selectedBranchId');
                localStorage.removeItem('selectedBranchName');
                localStorage.clear();

                window.location.href = "/";
            }
        });
    });






    function setUserId(userId) {
        $('#userIdForPassword').val(userId);
    }
    $(document).on('click', '.change-password', function() {


        const userId = $(this).data('id');
        $('#userIdForPassword').val(userId);
        $('#changePasswordForm')[0].reset();
        $('#changePasswordModal').modal('show');
    });




    $('#changePasswordForm').submit(function(e) {
        e.preventDefault();

        // Clear previous messages
        $('#passwordsuccessMessage').hide().text('');
        $('#passworderrorMessage').hide().text('');

        const password = $('input[name="password"]').val();
        const confirmPassword = $('input[name="password_confirmation"]').val();

        // Validation: Check if passwords match
        if (password !== confirmPassword) {
            $('#passworderrorMessage').html('Confirm password does not match.').show();
            return; // Stop form submission
        }

        const formData = {
            user_id: $('#userIdForPassword').val(),
            password: password,
            password_confirmation: confirmPassword,
        };

        $.ajax({
            url: 'api/patient/change-password',
            type: 'POST',
            data: formData,
            success: function(response) {
                $('#passwordsuccessMessage').text(response.message).show();

                setTimeout(() => {
                    $('#changePasswordModal').modal('hide');
                    $('#passwordsuccessMessage').hide();

                    if (response.logout) {
                        window.location.href = '/'; // Now redirects to login, not dashboard
                    }

                }, 2000);
            },
            error: function(xhr) {
                const errors = xhr.responseJSON.errors;
                let message = 'An error occurred.';

                if (errors) {
                    message = Object.values(errors).flat().join('<br>');
                } else if (xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }

                $('#passworderrorMessage').html(message).show();
            }
        });
    });














    $(document).ready(function() {




        $.ajax({

            url: '/api/profile',

            method: 'GET',

            headers: {

                'Authorization': 'Bearer ' + token

            },

            success: function(response) {

                //console.log("Profile API Response:", response); // Debugging line



                if (response.user) {

                    $('.user-image').attr('src', response.user.profile);



                    $('.user-name').text(response.user.fullname);

                } else {

                    // console.log('No user data found in response');

                }

            },

            error: function(xhr) {

                console.error("Error fetching profile data:", xhr.responseText);

            }

        });

    });











    $(document).ready(function() {
        // List of sidebar modules with parameters
        var modules = [{
                name: "Dashboard",
                url: "/dashboard"
            },
            {
                name: "User",
                url: "/user"
            },
            {
                name: "Medicines",
                url: "/medicine"
            },
            {
                name: "Patients",
                url: "/patients"
            },
            {
                name: "Appointments",
                url: "/appointment"
            },
            {
                name: "Treatments",
                url: "/treatment"
            },
            {
                name: "Services",
                url: "/service"
            },
            {
                name: "Calender",
                url: "/calender"
            },
            {
                name: "Discharge",
                url: "/discharge"
            },
            {
                name: "Inventory",
                url: "/inventory"
            },
            {
                name: "Supplier",
                url: "/supplier"
            },
            {
                name: "Medical Report",
                url: "/report"
            },
            {
                name: "Chart",
                url: "/chart"
            },
            {
                name: "Role",
                url: "/role"
            },
            {
                name: "Pathology",
                url: "/pathology"
            },
            {
                name: "Pathology Reports",
                url: "/pathology_reports"
            },
            {
                name: "Ipd Admit",
                url: "/ipd_admit"
            },
            {
                name: "Opd Visits",
                url: "/opdvisit"
            },
            {
                name: "OT ",
                url: "/ot-procedures-all"
            },
            {
                name: "Radiology Tests",
                url: "/radiology-tests"
            },
            {
                name: "Radiology Reports",
                url: "/radiology-reports"
            },


            {
                name: "Therapy",
                url: "/therapies"
            },
            {
                name: "Assign Therapies",
                url: "/assigned-therapies"
            },
            {
                name: "Assessment",
                url: "/assessment"
            },
            {
                name: "HomeAdvice",
                url: "/homeadvice"
            },
            {
                name: "Daily Data",
                url: "/daily-data"
            },
            {
                name: "Treatment Booking",
                url: "/treatment_booking"
            },
            {
                name: "SOAP",
                url: "/soap"
            },
            {
                name: "Expense",
                url: "/expenses"
            },
            {
                name: "Code",
                url: "/codemaster"
            },
            {
                name: "Diagnosis",
                url: "/diagnosis/create"
            },
            {
                name: "Machine",
                url: "/machine"
            },
            {
                name: "Symptoms",
                url: "/symptoms"
            },
            {
                name: "Patient Medicine",
                url: "/patientmedicine"
            },
            {
                name: "Invoice",
                url: "/invoice"
            },
            {
                name: "Referral  Doctor",
                url: "/referaldoctor"
            },
            {
                name: "Diagnostic service",
                url: "/service"
            },









        ];

        // Function to display the search results
        function displayResults(filteredModules) {
            var resultsList = $(".search-results");
            resultsList.empty(); // Clear previous results

            if (filteredModules.length > 0) {
                filteredModules.forEach(function(module) {
                    resultsList.append(
                        '<a href="' + module.url + '" class="text-decoration-none text-dark">' +
                        '<li class="list-group-item">' + module.name + '</li>' +
                        '</a>'
                    );
                });
            } else {
                resultsList.append('<li class="list-group-item text-dark">No results found</li>');
            }

            resultsList.removeClass('d-none'); // Show the results list
        }

        // Event listener for search input
        $("#sidebar-search").on("input", function() {
            var query = $(this).val().toLowerCase();

            // Filter modules based on the query
            var filteredModules = modules.filter(function(module) {
                return module.name.toLowerCase().includes(query);
            });

            // Display the filtered results
            displayResults(filteredModules);

            // Hide the list when input is cleared
            if (query === '') {
                $(".search-results").addClass('d-none');
            }
        });

        // Optional: Hide results when clicking outside the search container
        $(document).on("click", function(event) {
            if (!$(event.target).closest('.wrap').length) {
                $(".search-results").addClass('d-none');
            }
        });
    });
</script>
