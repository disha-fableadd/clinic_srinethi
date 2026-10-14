<?php

use App\Http\Controllers\api\AiOptimizeController;
use App\Http\Controllers\Api\CategoryController;

use App\Http\Controllers\Api\IpdAdmissionController;
use App\Http\Controllers\Api\PathologyReportController;
use App\Http\Controllers\Api\PathologyTestController;
use App\Http\Controllers\Api\OpdVisitController;
use App\Http\Controllers\Api\OtProcedureController;
use App\Http\Controllers\Api\DiagnosisController;

use App\Http\Controllers\Api\PatientMedicineController;
use App\Http\Controllers\Api\SymptomController;
use App\Http\Controllers\Api\KnowledgeBaseController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Api\MachineController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\MedicineController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserDetailsController;
use App\Http\Controllers\Api\TreatmentController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AppointmentHistoryController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\PatientDischargeDetailController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\MedicalReportController;
use App\Http\Controllers\Api\UserPermissionController;
use App\Http\Controllers\Api\CodeMasterController;
use App\Http\Controllers\Api\ModuleController;
use App\Models\Categories;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\DashboardController;
use App\Models\User;
use App\Http\Controllers\Api\chartController;
use App\Http\Controllers\Api\SmsController;
use App\Http\Controllers\Api\EmailController;
use App\Http\Controllers\Api\SmsTemplateController;
use App\Http\Controllers\Api\EmailTemplateController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\FollowupController;
use App\Models\DefaultEmailTemplate;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Api\RadiologyTestController;
use App\Http\Controllers\Api\RadiologyReportController;
use App\Http\Controllers\Api\TherapyController;
use App\Http\Controllers\Api\AssignedTherapyController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\TaxRateController;



//new module
use App\Http\Controllers\Api\DailyDataController;
use App\Http\Controllers\Api\HomeAdviceController;

use App\Http\Controllers\Api\AssessmentController;
use App\Http\Controllers\Api\DietChartController;
use App\Http\Controllers\Api\PatientAssignAssessmentController;
use App\Http\Controllers\Api\PatientAssignHomeadviceController;
use App\Http\Controllers\Api\SoapController;
use App\Http\Controllers\Api\TreatmentBookingController;
use App\Http\Controllers\Api\ExpenseController;
use App\Models\Patients;
use App\Http\Controllers\Api\ReferalDoctorController;
use App\Http\Controllers\Api\SeoOptimizerController;
use App\Http\Controllers\Api\MedicineUnitController;
use App\Http\Controllers\Api\PatientAssignDietChartController;
use App\Http\Controllers\api\PlanController;

Route::get('/admin/search-google', [KnowledgeBaseController::class, 'search_google']);

Route::post('/loginweb', [AuthController::class, 'login'])->name('loginweb');
Route::post('/login', [AuthController::class, 'loginAPIs'])->name('login');

Route::post('/logout', [AuthController::class, 'logout']);

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


Route::post('patient/change-password', [PatientController::class, 'changePassword']);
Route::post('/ai/optimize', [AiOptimizeController::class, 'optimize']);


Route::get('/clear-cache', function () {
    Artisan::call('config:clear');
    // Artisan::call('config:cache');
    Artisan::call('view:clear');
    Artisan::call('cache:clear');
    Artisan::call('optimize:clear');
    Artisan::call('route:clear');
    return 'Cache cleared!';
});


Route::get('/start-queue', function () {
    Artisan::call('queue:work');
    // Artisan::call('queue:work --once');
    return 'Queue job processed once.';
});

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('razorpay-patient', [PatientController::class, 'razorpayPatient']);

    Route::get('patient/pending-amounts/{patient}', [SettingController::class, 'getPendingAmounts']);
    Route::post('razorpay/razorpay-patient', [SettingController::class, 'generateLinkpatient']);


    //daily data pdf
    Route::get('daily-data/{id}/pdf', [DailyDataController::class, 'dailyDataPdf']);

    //razorpay
    Route::post('/save-razorpay-settings', [SettingController::class, 'saveRazorpaySettings']);
    Route::get('/get-razorpay-settings', [SettingController::class, 'getRazorpaySettings']);
    Route::post('/save-project-type', [SettingController::class, 'saveProjectType']);
    Route::post('/razorpay/generate-payment-link', [SettingController::class, 'generateLink']);
    Route::post('/treatment_booking/generate-payment-link-treatment', [SettingController::class, 'generateLinkTreatmentBooking']);
    // Route::post('razorpay/razorpay-patient', [SettingController::class, 'generateLinkpatient']);
    Route::post('/razorpay/discharge/generate-payment-link', [SettingController::class, 'generateDischargePaymentLink']);
    Route::post('/razorpay/report/generate-payment-link', [SettingController::class, 'generateReportPaymentLink']);
    Route::post('/razorpay/invoice/generate-payment-link', [SettingController::class, 'generateInvoicePaymentLink']);
    Route::post('/razorpay/appointment/generate-payment-link', [SettingController::class, 'generateAppointmentPaymentLink']);





    //new module
    Route::apiResource('machines', MachineController::class);
    Route::apiResource('tax-rates', TaxRateController::class);
    // Route::apiResource('medicine-units', App\Http\Controllers\Api\MedicineUnitController::class);
    Route::apiResource('medicine-units', MedicineUnitController::class);
    Route::get('medicine-units-export', [MedicineUnitController::class, 'export']);

    Route::get('/expenses/filters', [ExpenseController::class, 'getExpenseFilters']);
    Route::get('/patients/years', [PatientController::class, 'getYears']);
    Route::apiResource('expenses', ExpenseController::class);
    Route::apiResource('treatment_booking', TreatmentBookingController::class);
    Route::get('/patient-medicine/{id}/download', [PatientMedicineController::class, 'medicineDownload']);

    // Dashboard APIs
    Route::get('/patient-counts', [\App\Http\Controllers\Api\DashboardController::class, 'getPatientCounts']);
    Route::get('/total-patient', [\App\Http\Controllers\Api\DashboardController::class, 'totalPatient']);
    Route::get('/total-followup', [\App\Http\Controllers\Api\DashboardController::class, 'totalFollowup']);
    Route::get('/all-followup', [\App\Http\Controllers\Api\DashboardController::class, 'allFollowup']);
    Route::get('/total-appointment', [\App\Http\Controllers\Api\DashboardController::class, 'totalAppointment']);
    Route::get('/total-today-appointment', [\App\Http\Controllers\Api\DashboardController::class, 'totalTodayAppointment']);
    Route::get('/today-stats', [\App\Http\Controllers\Api\DashboardController::class, 'todayStats']);
    Route::get('/today-birthday-users', [\App\Http\Controllers\Api\DashboardController::class, 'todayBirthdayUsers']);



    Route::get('/treatment_booking/{id}/payment-history', [TreatmentBookingController::class, 'paymentHistory']);
    Route::post('/treatment_booking/{id}/make-payment', [TreatmentBookingController::class, 'makePayment']);
    Route::apiResource('diagnosis', DiagnosisController::class);
    Route::get('/today-data', [AppointmentController::class, 'todayAppointmentsAndFollowups']);

    Route::get('appointments/{id}/download', [AppointmentController::class, 'appointmentPdf']);

    Route::get('medical-reports/{id}/download', [MedicalReportController::class, 'medicalReportPdf']);

    Route::get('/soaps/{id}/download', [SoapController::class, 'soapPdf'])
        ->name('api.soaps.download');

    Route::get('/medicines/{id}/download', [PatientMedicineController::class, 'medicinePdf'])
        ->name('api.medicines.download');

    Route::get('followups/{id}/download', [FollowupController::class, 'followupPdf']);
    Route::get('assigned-therapies/{id}/download', [AssignedTherapyController::class, 'assignedTherapyPdf']);

    Route::get('/treatment-booking/{id}/pdf', [TreatmentBookingController::class, 'treatmentBookingPdf']);


    Route::get('/patients/{id}/history-pdf', [PatientController::class, 'patientHistoryPdf']);
    Route::get('/homeadvice/{id}/download', [HomeAdviceController::class, 'homeadvicePdf'])
        ->name('homeadvice.download');


    Route::apiResource('referal_doctors', ReferalDoctorController::class);
    Route::get('/referal_doctors/{id}', [ReferalDoctorController::class, 'show']);
    Route::get('/treatment-booking/filters/years', [TreatmentBookingController::class, 'getYears']);
    Route::get('/treatment-booking/patients', [TreatmentBookingController::class, 'getFilterPatient']);




 // dite chart
    Route::post('dietchart/update/{id}', [DietChartController::class, 'updatePost']);

    Route::apiResource('dietchart', DietChartController::class);
    Route::get('/dietchart/download/{id}', [DietChartController::class, 'download'])
        ->name('dietchart.download');
//patient assign diet-chart
    Route::apiResource('patient-assign-dietchart', PatientAssignDietChartController::class);
    Route::get('/patient/{patientId}/diet-charts', [PatientAssignDietChartController::class, 'getByPatient']);
    //    Route::get('patient/{patientId}/diet-charts/{dietChartId}/download', [PatientAssignDietChartController::class, 'dietChartPdf']);
    //   Route::get('/patient/{patientId}/diet-charts/{dietChartId}/download', [PatientAssignDietChartController::class, 'dietChartPdf']);
    Route::get('/patient/{patientId}/diet-charts/{dietChartId}/download', [PatientAssignDietChartController::class, 'dietChartPdf']);


    Route::get('patient/treatment-bookings/{id}', [TreatmentBookingController::class, 'treatmentHistory']);
    Route::delete('/patient-assessments/{id}', [PatientAssignAssessmentController::class, 'destroy']);
    Route::delete('/patient-patienthome/{id}', [PatientAssignHomeadviceController::class, 'destroy']);
    //branch
    Route::apiResource('branches', BranchController::class);
    // selected branch name is selected on dropdown
    Route::get('/set-branch/{branch_id}', [BranchController::class, 'setBranch'])->name('branch.set');

    Route::apiResource('patient-assign-assessment', PatientAssignAssessmentController::class);
    Route::get('patient/{patientId}/assessments', [PatientAssignAssessmentController::class, 'getByPatient']);
    Route::get('/assessment-templates', [PatientAssignAssessmentController::class, 'index']);


    Route::apiResource('soap', SoapController::class);
    Route::apiResource('assessment', AssessmentController::class);
    Route::post('/assessments', [AssessmentController::class, 'store']);

    Route::apiResource('daily-data', DailyDataController::class);
    Route::get('/daily-data/{id}/payment-history', [DailyDataController::class, 'paymentHistorydailydata']);
    Route::post('/daily-data/{id}/make-payment', [DailyDataController::class, 'makePayment']);
    Route::get('/daily-data/payment-history/{id}', [DailyDataController::class, 'paymentHistory']);
    Route::get('/daily-data/filters/years', [DailyDataController::class, 'getAvailableYears']);




    Route::get('/today-stats', [UserController::class, 'getTodayStats']);
    Route::get('/today-birthday-users', [UserController::class, 'getTodayBirthdayUsers']);

    Route::get('/userss', [UserController::class, 'getalluserss'])->name('getalluserss'); //done


    Route::get('all-notifications', [SettingController::class, 'notificationView'])->name('notificationView');
    Route::post('notifications/{id}', [SettingController::class, 'update'])->name('notification.update');


    //download csv file
    Route::get('/treatment-booking-exportcsv', [TreatmentBookingController::class, 'treatmentbookingexportCsv']);
    Route::get('/appointments/export-csv', [AppointmentController::class, 'exportCsv']);
    Route::get('/users/export-csv', [UserController::class, 'exportCsv']);
    Route::get('/patients/export-csv', [PatientController::class, 'exportCsv']);
    Route::get('/treatments/export-csv', [TreatmentController::class, 'exportCsv']);
    Route::get('/pathology-tests/export-csv', [PathologyTestController::class, 'exportCsv']);
    Route::get('/pathology-reports/export-csv', [PathologyReportController::class, 'exportCsv']);
    Route::get('/ipd-admissions/export-csv', [IpdAdmissionController::class, 'exportCsv']);
    Route::get('/followups/export-csv', [FollowupController::class, 'exportCsv']);
    Route::get('/medicines/export-csv', [MedicineController::class, 'exportCsv']);
    Route::post('/medicines/{id}/update-quantity', [MedicineController::class, 'updateQuantity']);
    Route::get('/categories/export-csv', [CategoryController::class, 'exportCsv']);
    Route::get('/patient-medicine/export-csv', [PatientMedicineController::class, 'exportCsv']);
    Route::get('/services/export-csv', [ServiceController::class, 'exportCsv']);
    Route::get('/opd-visits/export-csv', [OpdVisitController::class, 'exportCsv']);
    Route::get('/ot-procedures/export-csv', [OtProcedureController::class, 'exportCsv']);
    Route::get('/discharges/export-csv', [PatientDischargeDetailController::class, 'exportCsv']);
    Route::get('/inventory/export-csv', [InventoryController::class, 'exportCsv']);
    Route::get('/suppliers/export-csv', [SupplierController::class, 'exportCsv']);
    Route::get('/medical-reports/export-csv', [MedicalReportController::class, 'exportCsv']);
    Route::get('/radiology-tests/export-csv', [RadiologyTestController::class, 'exportCsv']);
    Route::get('/radiology-reports/export-csv', [RadiologyReportController::class, 'exportCsv']);
    Route::get('/email-templates/export-csv', [EmailTemplateController::class, 'exportCsv']);
    Route::get('/send-emails/export-csv', [EmailTemplateController::class, 'send_exportCsv']);
    Route::get('/sms-templates/export-csv', [SmsTemplateController::class, 'exportCsv']);
    Route::get('/send-sms/export-csv', [SmsTemplateController::class, 'send_sms_exportCsv']);
    Route::get('/therapy/export-csv', [TherapyController::class, 'exportTherapyCsv']);
    Route::get('/assigned-therapies/export-csv', [AssignedTherapyController::class, 'exportAssignedTherapiesCsv']);

    Route::get('/soaps/export', [SoapController::class, 'SoapexportCsv']);


    Route::get('/daily-datas/export-csv/csv', [DailyDataController::class, 'exportCsv']);
    Route::get('/home-advice/export', [HomeAdviceController::class, 'exportCsv']);

    Route::get('/invoice/pdf/{id}', [AuthController::class, 'downloadPdf'])->name('invoice.download');

    Route::get('/assessments/export-csv', [AssessmentController::class, 'exportCsv'])->name('assessments.export');
    Route::get('/soap/export-csv', [SoapController::class, 'exportCsv'])->name('soap.export');

    Route::get('/homeadvice/export-csv', [HomeAdviceController::class, 'exportCsv'])->name('homeadvice.export');

    Route::get('expenses/export/csv', [ExpenseController::class, 'exportCsv']);

    Route::get('/homeadvice/{id}/download', [HomeAdviceController::class, 'homeadvicePdf'])
        ->name('homeadvice.download');
    Route::get('/homeadvice/{id}/download', [HomeAdviceController::class, 'download'])
        ->name('homeadvice.download');
    Route::apiResource('patient-assign-homeadvice', PatientAssignHomeadviceController::class);
    Route::get('patient/{patientId}/homeadvices', [PatientAssignHomeadviceController::class, 'getByPatient']);
    Route::get('/homeadvices', [PatientAssignHomeadviceController::class, 'index']);


    // download assessment
    Route::get('/assessments/{id}/download', [AssessmentController::class, 'download'])
        ->name('assessment.download');
    Route::get('/assessments/export-csv', [AssessmentController::class, 'exportCsv'])->name('assessments.export');

    //New modules 
    //1)pathology
    Route::apiResource('pathology-tests', PathologyTestController::class);
    //2)pathology-reports
    Route::apiResource('pathology-reports', PathologyReportController::class);
    //3)IPD
    Route::apiResource('ipd-admissions', IpdAdmissionController::class);

    //get department wise service


    Route::get('/get-services-by-department', [ServiceController::class, 'getServicesByDepartment']);

    // OPD Visit
    Route::post('/opd-visits', [OpdVisitController::class, 'store']);
    Route::get('/all-opd-visits', [OpdVisitController::class, 'index']);
    Route::delete('/delete-opd-visit/{id}', [OpdVisitController::class, 'destroy']);
    Route::get('/opd/edit-show/{id}', [OPDVisitController::class, 'show']);
    Route::post('/opd/update/{id}', [OPDVisitController::class, 'update']);
    Route::get('opds/{id}/download', [OpdVisitController::class, 'opdVisitPdf']);

    // OT
    Route::post('/ot-procedures', [OtProcedureController::class, 'store']);
    Route::get('/all-ot-procedures', [OtProcedureController::class, 'index']);
    Route::delete('/delete-ot/{id}', [OtProcedureController::class, 'destroy']);
    Route::get('/ot/edit-show/{id}', [OtProcedureController::class, 'show']);
    Route::post('/ot/update/{id}', [OtProcedureController::class, 'update']);

    //therapy
    Route::apiResource('therapies', TherapyController::class);
    Route::get('/therapy-list', [TherapyController::class, 'api_index']);
    //assign therapy
    Route::apiResource('assigned-therapies', AssignedTherapyController::class);


    Route::post('/symptoms/store', [SymptomController::class, 'store'])->name('symptoms.store');
    Route::get('/symptoms/list', [SymptomController::class, 'getSymptoms'])->name('symptoms.list');
    // Route::get('/symptoms', [SymptomController::class, 'index']);

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::apiResource('symptoms', SymptomController::class); //done
    });
    Route::post('/users/change-password', [UserController::class, 'changePassword']); //done

    Route::post('/users/{id}/notify', [UserController::class, 'sendNotification']); //done

    Route::post('/update-clinic-details', [AuthController::class, 'updateClinicDetails']); //done

    Route::post('/save-settings', [SettingController::class, 'saveSettings']); //DONE

    Route::get('/user', [AuthController::class, 'user'])->name('user'); //done



    Route::apiResource('rolee', RoleController::class); //done

    Route::apiResource('patient-medicines', PatientMedicineController::class); //done

    Route::apiResource('category', CategoryController::class); //done    

    Route::post('followup/createFollowup', [FollowupController::class, 'createFollowup']); //done

    Route::apiResource('followup', FollowupController::class); //done

    Route::apiResource('users', UserController::class); //done

    Route::apiResource('medicines', MedicineController::class); //done

    Route::apiResource('patient', PatientController::class); //done

    Route::apiResource('codemaster', CodeMasterController::class);
    Route::apiResource('sms', SmsController::class);
    Route::get('/get-firebase-settings', [SettingController::class, 'getFirebaseSettings']);
    Route::get('/download-firebase-json', [SettingController::class, 'downloadFirebaseJson']);

//new
    Route::get('followups/count-by-doctor', [FollowupController::class, 'countByDoctor']);
    Route::get('/pending-payments', [DailyDataController::class, 'getPendingPayments']);



    Route::apiResource('user-details', UserDetailsController::class); //done

    Route::apiResource('treatments', TreatmentController::class); //done

    Route::post('/appointmentHistory/store', [AppointmentHistoryController::class, 'store']); //done

    Route::apiResource('appointmentHistory', AppointmentHistoryController::class); //done

    Route::post('/appointments/complete', [AppointmentController::class, 'complete']);

    Route::middleware('auth:api')->get('/doctors', function (Request $request) {
        $user = $request->user();
        $userId = $user->id;
        $role = $user->role->name;

        $branchId = $request->get('branch_id'); // get branch_id from request

        // Fetch all users with role 'Doctor' (filter by branch if provided)
        $doctors = User::whereHas('role', function ($q) {
            $q->where('name', 'Doctor');
        })
            ->when($branchId, function ($q) use ($branchId) {
                $q->where('branch_id', $branchId); // assumes users table has branch_id
            })
            ->select('id', 'fullname', 'profile', 'created_by', 'role_id', 'branch_id')
            ->with('role:name')
            ->get();

        // Fetch Admins (optionally branch filter if needed)
        $admins = User::whereHas('role', function ($q) {
            $q->where('name', 'Admin');
        })
            ->select('id', 'fullname', 'profile', 'role_id')
            ->get()
            ->map(function ($admin) {
                $admin->fullname .= ' (Admin)';
                return $admin;
            });

        // Merge doctors + admins
        $allDoctors = $doctors->merge($admins);

        // Role-specific formatting
        if ($role === 'Admin') {
            foreach ($allDoctors as $doc) {
                if ($doc->id === $userId) {
                    $doc->fullname = str_replace('(Admin)', '', $doc->fullname);
                    $doc->fullname .= ' (Self)';
                }
            }
        } elseif ($role === 'Doctor') {
            // Only return self
            return response()->json([
                'doctors' => [
                    [
                        'id' => $user->id,
                        'fullname' => $user->fullname . ' (You)',
                        'profile' => $user->profile,
                        'branch_id' => $user->branch_id
                    ]
                ]
            ]);
        }

        return response()->json(['doctors' => $allDoctors->values()]);
    });




    Route::get('/appointments/app', [AppointmentController::class, 'indexForApp']);

    Route::post('/invoice', [SettingController::class, 'generateInvoice'])->name('invoice.create'); //done

    Route::get('/invoice', [SettingController::class, 'index'])->name('invoice.index'); //done
    Route::delete('/invoice/{id}', [SettingController::class, 'destroy'])->name('invoice.destroy'); //done

    // Route::post('/api/invoices/generate', [SettingController::class, 'generateAppointmentInvoice']);
    Route::post('/invoices/generate-appointment-invoice', [SettingController::class, 'generateAppointmentInvoice'])->middleware('auth:api'); //done

    Route::get('/doctors-patients', [UserController::class, 'getAppointmentStatusCounts']); //done

    Route::get('/patients', function () {
        return \App\Models\Patients::select('id', 'fullname')->get();
    });
    Route::get('/radiology-tests', function () {
        return \App\Models\RadiologyTest::select('id', 'test_name')->get();
    });

    Route::get('/appointments/admin', [AppointmentController::class, 'getAdminAppointments']); //done

    // Route::apiResource('appointments', AppointmentController::class); //done

    Route::apiResource('appointments', AppointmentController::class);



    Route::apiResource('services', ServiceController::class); //done

    Route::get('patient-discharge-details/by-patient', [PatientDischargeDetailController::class, 'getByPatient']);
    Route::apiResource('patient-discharge-details', PatientDischargeDetailController::class); //done

    Route::apiResource('suppliers', SupplierController::class); //done

    Route::apiResource('inventoryy', InventoryController::class); //done

    Route::apiResource('medical-reports', MedicalReportController::class); //done

    Route::apiResource('permissions', UserPermissionController::class);

    Route::get('modules', [ModuleController::class, 'index']); //done

    // Route::get('/categories', function () {
    //     return response()->json(Categories::all());
    // }); 


    Route::get('/categories', function (Request $request) {
        $query = Categories::query();

        // ✅ Only fetch categories for this branch
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        return response()->json($query->orderBy('name')->get());
    });

    Route::get('total-followup', [UserController::class, 'getTodayfollowup']); //done
    Route::get('total-today-appointment', [UserController::class, 'getTodayAppointment']); //done
    Route::get('total-patient', [UserController::class, 'getTotalPatient']); //done
    Route::get('total-appointment', [UserController::class, 'getTotalAppointment']); //done
    Route::get('all-followup', [UserController::class, 'getAllFollowups']); //done


    Route::get('/calendar', [AppointmentController::class, 'getcalander']); //done
    Route::get('/calendar-statuses', [AppointmentController::class, 'getStatuses']); //done
  // Plans CRUD
    Route::get('/getAllPlans',[PlanController::class, 'getAllPlans'])->name('plans.getAll');
    Route::get('/getPlanById/{id}',[PlanController::class, 'getPlanById'])->name('plans.getById');
    Route::post('/createPlan',[PlanController::class, 'createPlan'])->name('plans.create');
    Route::post('/updatePlan',[PlanController::class, 'updatePlan'])->name('plans.update');
    Route::post('/deletePlan/{id}',[PlanController::class, 'deletePlan'])->name('plans.delete');
    Route::post('/togglePlanStatus/{id}',[PlanController::class, 'togglePlanStatus'])->name('plans.toggleStatus');
    Route::post('/assignPlanToUser',[PlanController::class, 'assignPlanToUser'])->name('plans.assignToUser');
    Route::get('/getUserPlan',[PlanController::class, 'getUserPlan'])->name('plans.getUserPlan');

    Route::get('/users', [UserController::class, 'getUsers']); //done

    Route::get('/search', [SearchController::class, 'search']); //done

    Route::get('/chart-data', [chartController::class, 'getChartData']); //done

    Route::post('/send-sms', [SmsController::class, 'sendSms'])->name('send.sms'); //done

    Route::get('/sms/data', [SmsController::class, 'getSmsData']); //done
    Route::delete('/sms/{id}', [SmsController::class, 'deleteSms'])->name('sms.delete'); //done

    Route::get('/sms-templates', [SmsTemplateController::class, 'getTemplates']); //done
    Route::apiResource('/sms-template', SmsTemplateController::class);
    Route::get('/sms-templates/{id}', [SmsTemplateController::class, 'show_details']);


    Route::post('/save-settings', [SettingController::class, 'saveSettings']); //done
    Route::get('/get-sms-settings', [SettingController::class, 'getSmsSettings']); //done
    Route::get('/get-whatsapp-settings', [SettingController::class, 'getWhatsappSettings']);
    // routes/api.php
    Route::post('/save-sms-status', [SettingController::class, 'saveSmsStatus']);
    Route::post('/save-whatsapp-status', [SettingController::class, 'saveWhatsappStatus']);

    Route::get('/get-service-status', [SettingController::class, 'getStatus']);
    Route::post('/save-service-status', [SettingController::class, 'updateStatus']);




    Route::apiResource('email', EmailController::class);
    Route::apiResource('/email-templates', EmailTemplateController::class); //done
    // Route::post('/email-templates', [EmailTemplateController::class, 'store']);
    Route::get('/default-email-templates', [App\Http\Controllers\Api\EmailTemplateController::class, 'getAllTemplates']); //done
    Route::get('/emails-template', [EmailTemplateController::class, 'getTemplates']); //done
    Route::post('/send-email', [EmailTemplateController::class, 'sendemail'])->name('send.email'); //done

    Route::get('/emails-template/{id}', [EmailTemplateController::class, 'getTemplateById']); //done
    Route::get('/emails', [EmailTemplateController::class, 'getEmails']); //done
    Route::delete('/emails/{id}', [EmailTemplateController::class, 'deleteEmail']); //done

    route::get('/radiology-tests', [RadiologyTestController::class, 'index']);
    Route::apiResource('radiology-tests', RadiologyTestController::class);

    Route::get('/radiology-reports', [RadiologyReportController::class, 'index']);
    Route::post('/radiology-reports', [RadiologyReportController::class, 'store']);
    Route::post('/radiology-reports-update', [RadiologyReportController::class, 'update']);
    Route::get('/radiology-reports/{id}', [RadiologyReportController::class, 'show']);
    Route::delete('/radiology-reports/{id}', [RadiologyReportController::class, 'destroy']);






    // daily data
    Route::get('/patients', function () {
        return Patients::select('id', 'name')->orderBy('name')->get();
    });

    // patient filter api
    Route::get('/patient/therapy/{patientId}', [TherapyController::class, 'getPatientTherapies']);

    // patient filter
    Route::get('/patients', [PatientController::class, 'list']);


    // soap
    Route::get('/patient/soap/{patientId}', [SoapController::class, 'getPatientSoap']);
    Route::get('patient/{id}/medical-reports', [MedicalReportController::class, 'patientReports']);
});

// routes/api.php
Route::middleware('auth:sanctum')->get('/profile', [AuthController::class, 'profile']); //done
Route::middleware('auth:sanctum')->put('/profile', [AuthController::class, 'updateProfile']);

// Route::middleware(['auth:sanctum'])->group(function () {
//     Route::post('/change-password', [AuthController::class, 'changePassword'])->name('api.change-password');
// });
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('homeadvice', HomeAdviceController::class);
});

Route::post('/api-chatbot', [SeoOptimizerController::class, 'store']);


Route::middleware('auth:sanctum')->get('/invoice-data', [SettingController::class, 'invoiceDataDropdown']);
Route::middleware('auth:sanctum')->post('/change-password', [AuthController::class, 'changePassword']);

Route::middleware('auth:sanctum')->post('/profile/edit', [AuthController::class, 'updateProfile']); //done

Route::middleware('auth:sanctum')->get('/treatments', [TreatmentController::class, 'getTreatments']); //done
Route::middleware('auth:sanctum')->get('/servicess', [ServiceController::class, 'getServices']); //done
Route::middleware('auth:sanctum')->get('/supplierss', [SupplierController::class, 'getSuppliers']); //done

Route::middleware('auth:sanctum')->post('/appointments', [AppointmentController::class, 'store']);
// Route::middleware('auth:sanctum')->post('/users', [UserController::class, 'store']);

Route::get('/permissions', [UserPermissionController::class, 'getUserPermissions'])->middleware('auth:sanctum'); //done

Route::middleware('auth:sanctum')->get('/IPDpatientss', [PatientController::class, 'getpatientsforIPD']); //done
Route::middleware('auth:sanctum')->get('/diagnoses', [DiagnosisController::class, 'getDiagnoses']);

Route::middleware('auth:sanctum')->get('/patientss', [PatientController::class, 'getPatients']); //done
Route::middleware('auth:sanctum')->get('/medicine', [MedicineController::class, 'getmedicine']); //done

Route::middleware('auth:sanctum')->get('/page-patients', [PatientController::class, 'page']);
Route::middleware('auth:sanctum')->get('/patients/filter', [PatientController::class, 'filterPatients']);

Route::post('/forgot-password', [AuthController::class, 'forgotPassword']); //done
Route::post('/reset-password', [AuthController::class, 'resetPassword']);//done
