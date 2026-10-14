<?php

use App\Http\Controllers\PlanController;
use App\Http\Controllers\AiOptimizeController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\IpdAdmissionController;
use App\Http\Controllers\KnowledgeBaseController;
use App\Http\Controllers\PathologyReportController;
use App\Http\Controllers\PathologyTestController;
use App\Http\Controllers\PatientMedicineController;
use App\Http\Controllers\SymptomController;
use App\Models\Setting;
use App\Http\Controllers\MachineController;

use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\CodeMasterController;
use App\Http\Controllers\DiagnosisController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DischargeController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\TreatmentController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\CalenderController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\MedicalReportController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\FollowupController;
use App\Http\Controllers\AudioController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OpdVisitController;
use App\Http\Controllers\OtProcedureController;
use App\Http\Controllers\RadiologyReportController;
use App\Http\Controllers\RadiologyTestController;
use App\Http\Controllers\TherapyController;
use App\Http\Controllers\AssignedTherapyController;
use App\Http\Controllers\ReferalDoctorController;



//new module

use App\Http\Controllers\DailyDataController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\HomeAdviceController;
use App\Http\Controllers\SoapController;
use App\Http\Controllers\TreatmentBookingController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\DietChartController;
use App\Http\Controllers\MedicineUnitController;
use App\Http\Controllers\TaxRateController;




//chatbot


Route::get('/chatbot/patients', [ChatbotController::class, 'getPatients']);
Route::get('/chatbot/data', [ChatbotController::class, 'getData']);

Route::middleware(['session.auth', 'check.permission'])->group(function () {





    //new modules
    // Assessment
    Route::get('/assessment', [AssessmentController::class, 'index'])->name('assessment.index');
    Route::get('/assessment/create', [AssessmentController::class, 'create'])->name('assessment.create');

    Route::get('/assessment/edit/{id}', function ($id) {
        return view('assessment.edit', ['id' => $id]);
    })->name('assessment.edit');

    Route::get('/assessment/show/{id}', function ($id) {
        return view('assessment.show', ['assessment_id' => $id]);
    })->name('assessment.show');
    Route::get('/assessments/export', [AssessmentController::class, 'exportAssessments'])->name('assessments.export');
    //Daily data


    Route::get('/daily_data', [DailyDataController::class, 'index'])->name('daily_data.index');
    Route::get('/daily_data/create', [DailyDataController::class, 'create'])->name('daily_data.create');

    Route::get('/daily_data/show/{id}', function ($id) {
        return view('daily_data.show', ['daily_data_id' => $id]);
    })->name('daily_data.show');


    Route::get('/daily_data/edit/{id}', function ($id) {
        return view('daily_data.edit', ['daily_data_id' => $id]);
    })->name('daily_data.edit');

    Route::get('/export-daily-data/{date}', [DailyDataController::class, 'exportDailyData'])->name('daily_data.export');

    //Expense
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expense.index');
    Route::get('/expenses/create', [ExpenseController::class, 'create'])->name('expense.create');

    Route::get('/expense/edit/{id}', function ($id) {
        return view('expense.edit', ['expense_id' => $id]);
    })->name('expense.edit');

    Route::get('/expense/show/{id}', function ($id) {
        return view('expense.show', ['expense_id' => $id]);
    })->name('expense.show');
    Route::get('/expense/export', [ExpenseController::class, 'export'])->name('expense.export');


    // home advice
    Route::get('/homeadvice', [HomeAdviceController::class, 'index'])->name('homeadvice.index');
    Route::get('/homeadvice/create', [HomeAdviceController::class, 'create'])->name('homeadvice.create');
    Route::get('/homeadvice/show/{id}', [HomeAdviceController::class, 'show'])->name('homeadvice.show');

    Route::get('/homeadvice/edit/{id}', function ($id) {
        return view('homeadvice.edit', ['id' => $id]);
    })->name('homeadvice.edit');

    Route::get('/homeadvice/show/{id}', function ($id) {
        return view('homeadvice.show', ['homeadvice_id' => $id]);
    })->name('homeadvice.show');

    Route::get('/home-advice/export', [HomeAdviceController::class, 'exportHomeAdvice'])->name('homeadvice.export');

    // machine
    Route::get('/machine', [MachineController::class, 'index'])->name('machine.index');
    Route::get('/machine/create', [MachineController::class, 'create'])->name('machine.create');


    Route::get('/machine/edit/{id}', function ($id) {
        return view('machine.edit', ['machine_id' => $id]);
    })->name('machine.edit');


    //soap


    Route::get('/soap', [SoapController::class, 'index'])->name('soap.index');
    Route::get('/soap/create', [SoapController::class, 'create'])->name('soap.create');
    Route::get('/soap/show/{id}', [SoapController::class, 'show'])->name('soap.show');

    Route::get('/soap/edit/{id}', function ($id) {
        return view('soap.edit', ['soap_id' => $id]);
    })->name('soap.edit');

    Route::get('/soap/show/{id}', function ($id) {
        return view('soap.show', ['soap_id' => $id]);
    })->name('soap.show');

    Route::get('/soap/export', [SoapController::class, 'export'])->name('soap.export');

    //Treament_booking
    Route::get('/treatment_booking', [TreatmentBookingController::class, 'index'])->name('treatment_booking.index');
    Route::get('/treatment_booking/create', [TreatmentBookingController::class, 'create'])->name('treatment_booking.create');

    Route::get('/treatment_booking/show/{id}', function ($id) {
        return view('treatment_booking.show', ['treatment_booking_id' => $id]);
    })->name('treatment_booking.show');

    Route::get('/treatment_booking/edit/{id}', function ($id) {
        return view('treatment_booking.edit', ['treatment_booking_id' => $id]);
    })->name('treatment_booking.edit');

    Route::get('/export-treatment-booking', [TreatmentBookingController::class, 'exportTreatmentBooking'])
        ->name('treatment_booking.export');

    // branch
    Route::get('/branch', [BranchController::class, 'index'])->name('branch.index');
    Route::get('/branch/create', [BranchController::class, 'create'])->name('branch.create');
    Route::get('/branch/{id}', function ($id) {
        return view('branch.show', compact('id'));
    })->name('branch.view');
    Route::get('/branch/{id}/edit', function ($id) {
        return view('branch.edit', compact('id'));
    })->name('branch.edit');




    Route::get('/test-sms', function () {
        $controller = app(SmsController::class); // Instantiate your controller
        return $controller->sendSms('+919824781385', 'This is a test SMS from Laravel');
    });

    Route::get('/test-reminders', function () {
        $controller = app(App\Http\Controllers\SmsController::class);
        return $controller->sendAppointmentReminders(true);  // Passing true for test mode
    });

    //taxrate
    Route::get('/taxrate', [TaxRateController::class, 'index'])->name('taxrate.index');

    //medicine unit
    Route::get('medicine_unit', [MedicineUnitController::class, 'index'])->name('medicine_unit.index');
    Route::get('medicine-units/export', [MedicineUnitController::class, 'exportCsv'])
        ->name('medicine-units.export');


    Route::get('/admin/knowledge-base', [KnowledgeBaseController::class, 'index'])->name('knowledge.index');
    Route::get('/admin/search-google', [KnowledgeBaseController::class, 'search']);
});




// Route::get('/', function () {
//     return view('welcome');
// });

Route::view('/forgot-password', 'auth.forgot-password')->middleware('redirect.auth')->name('forgot-password');
Route::view('/reset-password', 'auth.reset-password')->middleware('redirect.auth')->name('reset-password');





// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware('checkSession');



// login
// Route::get('/login', [LoginController::class, 'index'])->name('login');
// Route::post('login', [LoginController::class, 'loginSubmit'])->name('login.submit');

Route::get('/', [LoginController::class, 'index'])->middleware('redirect.auth')->name('login');;

// profile





Route::middleware(['session.auth', 'check.permission'])->group(function () {

    Route::post('/clinic/settings/update', [AuthController::class, 'updateClinicDetails'])->name('clinic.settings.update');


    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/profile/edit', function () {
        return view('editprofile');
    })->name('profile.edit');



    // dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::put('/profile', [ProfileController::class, 'updateProfile']);

    // user
    Route::get('/user', [EmployeeController::class, 'index'])->name('user.index');
    Route::get('/user/create', [EmployeeController::class, 'create'])->name('user.create');

    Route::get('/user/show/{id}', function ($id) {
        return view('user.show', ['user_id' => $id]);
    })->name('user.show');

    Route::get('/user/edit/{id}', function ($id) {
        return view('user.edit', ['user_id' => $id]);
    })->name('user.edit');

    Route::get('/user/profile/{id}', function ($id) {
        return view('user.profile', ['user_id' => $id]);
    })->name('user.profile');

    Route::get('/invoice', [SettingController::class, 'index'])->name('invoice.index');
    Route::get('/invoice/create', [SettingController::class, 'invoice'])->name('invoice.create');
    Route::get('/invoice/pdf/{id}', [SettingController::class, 'pdf'])->name('invoice.pdf');


    // role
    // Route::resource('role', RoleController::class);
    Route::get('/role', [RoleController::class, 'index'])->name('role.index');
    Route::get('/role/create', [RoleController::class, 'create'])->name('role.create');

    Route::get('/role/show/{id}', function ($id) {
        return view('role.show', ['role_id' => $id]);
    })->name('role.show');
    Route::get('/role/edit/{id}', function ($id) {
        return view('role.edit', ['role_id' => $id]);
    })->name('role.edit');



    Route::get('/radiology-tests', [RadiologyTestController::class, 'index'])->name('radiology-tests.index');
    Route::get('/radiology-tests/create', [RadiologyTestController::class, 'create'])->name('radiology-tests.create');
    Route::get('/radiology-tests/{id}', [RadiologyTestController::class, 'show'])->name('radiology-tests.show');
    Route::get('/radiology-tests/edit/{id}', [RadiologyTestController::class, 'edit'])->name('radiology-tests.edit');


    Route::get('/radiology-reports', [RadiologyReportController::class, 'index'])->name('radiology-reports.index');
    Route::get('/radiology-reports/create', [RadiologyReportController::class, 'create'])->name('radiology-reports.create');
    Route::get('/radiology-reports/{id}', [RadiologyReportController::class, 'show'])->name('radiology-reports.show');
    Route::get('/radiology-reports/edit/{id}', [RadiologyReportController::class, 'edit'])->name('radiology-reports.edit');



    //codemaster
    Route::get('/codemaster', [CodeMasterController::class, 'index'])->name('codemaster.index');

    //category

    Route::get('/category', [CategoryController::class, 'index'])->name('category.index');
    Route::get('/category/create', [CategoryController::class, 'create'])->name('category.create');

    Route::get('/category/show/{id}', function ($id) {
        return view('category.show', ['category_id' => $id]);
    })->name('category.show');
    Route::get('/category/edit/{id}', function ($id) {
        return view('category.edit', ['category_id' => $id]);
    })->name('category.edit');


    //patient-medicine

    Route::get('/patientmedicine', [PatientMedicineController::class, 'index'])->name('patient_medicine.index');
    Route::get('/patientmedicine/create', [PatientMedicineController::class, 'create'])->name('patient_medicine.create');

    Route::get('/patientmedicine/show/{id}', function ($id) {
        return view('patient_medicine.show', ['patientmedicine_id' => $id]);
    })->name('patient_medicine.show');

    Route::get('/patientmedicine/edit/{id}', function ($id) {
        return view('patient_medicine.edit', ['patientmedicine_id' => $id]);
    })->name('patient_medicine.edit');



    //therapy

    Route::get('/therapy', [TherapyController::class, 'index'])->name('therapy.index');
    Route::get('/therapy/create', [TherapyController::class, 'create'])->name('therapy.create');

    Route::get('/therapy/show/{id}', function ($id) {
        return view('therapy.show', ['therapy_id' => $id]);
    })->name('therapy.show');


    Route::get('/therapy/edit/{id}', function ($id) {
        return view('therapy.edit', ['therapy_id' => $id]);
    })->name('therapy.edit');

    //AssignedTherapyController

    Route::get('/assign-therapy', [AssignedTherapyController::class, 'index'])->name('assign-therapy.index');
    Route::get('/assign-therapy/create', [AssignedTherapyController::class, 'create'])->name('assign-therapy.create');

    Route::get('/assign-therapy/show/{id}', function ($id) {
        return view('assign-therapy.show', ['assign_therapy_id' => $id]);
    })->name('assign-therapy.show');


    Route::get('/assign-therapy/edit/{id}', function ($id) {
        return view('assign-therapy.edit', ['assign_therapy_id' => $id]);
    })->name('assign-therapy.edit');






    // pathology

    Route::get('/pathology', [PathologyTestController::class, 'index'])->name('pathology.index');
    Route::get('/pathology/create', [PathologyTestController::class, 'create'])->name('pathology.create');

    Route::get('/pathology/show/{id}', function ($id) {
        return view('pathology.show', ['pathology_id' => $id]);
    })->name('pathology.show');

    Route::get('/pathology/edit/{id}', function ($id) {
        return view('pathology.edit', ['pathology_id' => $id]);
    })->name('pathology.edit');


    // pathology-report

    Route::get('/pathology_reports', [PathologyReportController::class, 'index'])->name('pathology_reports.index');
    Route::get('/pathology_reports/create', [PathologyReportController::class, 'create'])->name('pathology_reports.create');

    Route::get('/pathology_reports/show/{id}', function ($id) {
        return view('pathology_reports.show', ['pathology_report_id' => $id]);
    })->name('pathology_reports.show');

    Route::get('/pathology_reports/edit/{id}', function ($id) {
        return view('pathology_reports.edit', ['pathology_report_id' => $id]);
    })->name('pathology_reports.edit');


    // IPD

    Route::get('/ipd_admit', [IpdAdmissionController::class, 'index'])->name('ipd_admit.index');
    Route::get('/ipd_admit/create', [IpdAdmissionController::class, 'create'])->name('ipd_admit.create');

    Route::get('/ipd_admit/show/{id}', function ($id) {
        return view('ipd_admit.show', ['ipd_admit_id' => $id]);
    })->name('ipd_admit.show');

    Route::get('/ipd_admit/edit/{id}', function ($id) {
        return view('ipd_admit.edit', ['ipd_admit_id' => $id]);
    })->name('ipd_admit.edit');


// diet chart
Route::get('/dietchart', [DietChartController::class, 'index'])->name('dietchart.index');
Route::get('/dietchart/create', [DietChartController::class, 'create'])->name('dietchart.create');
Route::get('/dietchart/edit/{id}', function ($id) {
    return view('dietchart.edit', ['id' => $id]);
})->name('dietchart.edit');
Route::get('/dietchart/show/{id}', function ($id) {
    return view('dietchart.show', ['dietchart_id' => $id]);
})->name('dietchart.show');
Route::get('/dietchart/export', [DietChartController::class, 'exportDietChart'])->name('dietchart.export');

    // OPD Visit
    Route::get('/opdvisit', [OpdVisitController::class, 'index'])->name('opd_visit.index');
    Route::get('/opd_visit/create', [OpdVisitController::class, 'create'])->name('opd_visit.create');
    Route::get('/opd/edit/{id}', [OpdVisitController::class, 'edit'])->name('opd_visit.edit');
    Route::get('/opd/display/{id}', [OpdVisitController::class, 'display'])->name('opd_visit.show');

    // OT
    Route::get('/ot-procedures-all', [OtProcedureController::class, 'index'])->name('ot.index');
    Route::get('/ot/create', [OtProcedureController::class, 'create'])->name('ot.create');
    Route::get('/ot/edit/{id}', [OtProcedureController::class, 'edit'])->name('ot.edit');
    Route::get('/ot/display/{id}', [OtProcedureController::class, 'display'])->name('ot.show');

    // Route::get('/opd/show/{id}', function ($id) {
    //     return view('opd.show', ['opd_id' => $id]);
    // })->name('opd.show');

    // Route::get('/opd/edit/{id}', function ($id) {
    //     return view('opd.edit', ['opd_id' => $id]);
    // })->name('opd.edit');

    // followup

    Route::get('/followup', [FollowupController::class, 'index'])->name('followup.index');
    Route::get('/followup/create', [FollowupController::class, 'create'])->name('followup.create');

    Route::get('/followup/show/{id}', function ($id) {
        return view('followup.show', ['followup_id' => $id]);
    })->name('followup.show');
    Route::get('/followup/edit/{id}', function ($id) {
        return view('followup.edit', ['followup_id' => $id]);
    })->name('followup.edit');

    // discharge
    Route::get('/discharge', [DischargeController::class, 'index'])->name('discharge.index');
    Route::get('/discharge/create', [DischargeController::class, 'create'])->name('discharge.create');

    Route::get('/discharge/show/{id}', function ($id) {
        return view('discharge.show', ['discharge_id' => $id]);
    })->name('discharge.show');

    Route::get('/discharge/edit/{id}', function ($id) {
        return view('discharge.edit', ['discharge_id' => $id]);
    })->name('discharge.edit');


    //patirents
    Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
    Route::get('/patients/create', [PatientController::class, 'create'])->name('patients.create');

    Route::get('/patient/show/{id}', function ($id) {
        return view('patients.show', ['patient_id' => $id]);
    })->name('patient.show');

    Route::get('/patient/edit/{id}', function ($id) {
        return view('patients.edit', ['patient_id' => $id]);
    })->name('patient.edit');




    //medicine
    Route::get('/medicine', [MedicineController::class, 'index'])->name('medicine.index');
    Route::get('/medicine/create', [MedicineController::class, 'create'])->name('medicine.create');

    Route::get('/medicine/show/{id}', function ($id) {
        return view('medicine.show', ['medicine_id' => $id]);
    })->name('medicine.show');

    Route::get('/medicine/edit/{id}', function ($id) {
        return view('medicine.edit', ['medicine_id' => $id]);
    })->name('medicine.edit');


    // diagnosis
    Route::get('/diagnosis', [DiagnosisController::class, 'index'])->name('diagnosis.index');
    Route::get('/diagnosis/create', [DiagnosisController::class, 'create'])->name('diagnosis.create');

    Route::get('/diagnosis/edit/{id}', function ($id) {
        return view('diagnosis.edit', ['id' => $id]);
    })->name('diagnosis.edit');



    Route::get('/diagnosis/show/{id}', function ($id) {
        return view('diagnosis.show', ['diagnosis_id' => $id]);
    })->name('diagnosis.show');

    //appointment
    Route::get('/appointment', [AppointmentController::class, 'index'])->name('appointment.index');
    Route::get('/appointment/create', [AppointmentController::class, 'create'])->name('appointment.create');
    Route::get('/appointment/show/{id}', [AppointmentController::class, 'show'])->name('appointment.show');
    Route::get('/appointment/edit/{id}', [AppointmentController::class, 'edit'])->name('appointment.edit');

    //treatment

    Route::get('/treatment', [TreatmentController::class, 'index'])->name('treatment.index');
    Route::get('/treatment/create', [TreatmentController::class, 'create'])->name('treatment.create');
    Route::get('/treatment/show/{id}', function ($id) {
        return view('treatment.show', ['treatment_id' => $id]);
    })->name('treatment.show');

    Route::get('/treatment/edit/{id}', function ($id) {
        return view('treatment.edit', ['treatment_id' => $id]);
    })->name('treatment.edit');


    Route::get('/symptoms', [SymptomController::class, 'index'])->name('symptoms.index');
    Route::get('/symptoms/create', [SymptomController::class, 'create'])->name('symptoms.create');


    Route::get('/symptoms/edit/{id}', function ($id) {
        return view('symptoms.edit', ['symptoms_id' => $id]);
    })->name('symptoms.edit');


    //services
    Route::get('/service', [ServiceController::class, 'index'])->name('service.index');
    Route::get('/service/create', [ServiceController::class, 'create'])->name('service.create');

    Route::get('/service/show/{id}', function ($id) {
        return view('service.show', ['service_id' => $id]);
    })->name('service.show');

    Route::get('/service/edit/{id}', function ($id) {
        return view('service.edit', ['service_id' => $id]);
    })->name('service.edit');
    //inventory

    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');

    Route::get('/inventory/create', [InventoryController::class, 'create'])->name('inventory.create');

    Route::get('/inventory/show/{id}', function ($id) {
        return view('inventory.show', ['inventory_id' => $id]);
    })->name('inventory.show');

    Route::get('/inventory/edit/{id}', function ($id) {
        return view('inventory.edit', ['inventory_id' => $id]);
    })->name('inventory.edit');



    //suplier


    Route::get('/supplier', [SupplierController::class, 'index'])->name('supplier.index');
    Route::get('/supplier/create', [SupplierController::class, 'create'])->name('supplier.create');

    Route::get('/supplier/show/{id}', function ($id) {
        return view('supplier.show', ['supplier_id' => $id]);
    })->name('supplier.show');

    Route::get('/supplier/edit/{id}', function ($id) {
        return view('supplier.edit', ['supplier_id' => $id]);
    })->name('supplier.edit');


    // referalDoctor
    Route::get('/referaldoctor', [ReferalDoctorController::class, 'index'])->name('referaldoctor.index');
    Route::get('/referaldoctor/create', [ReferalDoctorController::class, 'create'])->name('referaldoctor.create');
    Route::get('/referaldoctor/show/{id}', [ReferalDoctorController::class, 'show'])->name('referaldoctor.show');

    Route::get('/referaldoctor/edit/{id}', function ($id) {
        return view('referaldoctor.edit', ['id' => $id]);
    })->name('referaldoctor.edit');

    Route::get('/referaldoctor/show/{id}', function ($id) {
        return view('referaldoctor.show', ['id' => $id]);
    })->name('referaldoctor.show');


    //report
    Route::get('/report', [MedicalReportController::class, 'index'])->name('report.index');

    Route::get('/report/create', [MedicalReportController::class, 'create'])->name('report.create');

    Route::get('/report/show/{id}', function ($id) {
        return view('report.show', ['report_id' => $id]);
    })->name('report.show');

    Route::get('/report/edit/{id}', function ($id) {
        return view('report.edit', ['report_id' => $id]);
    })->name('report.edit');

  //plans
    Route::get('/plan', [PlanController::class,'index'])->name('plans.planlist');
    Route::get('/add-plan', [PlanController::class,'create'])->name('plans.addplan');
    Route::post('/add-plan', [PlanController::class,'store'])->name('plans.store');
    Route::get('/plans/{plan}', [PlanController::class,'show'])->name('plans.show');
    Route::get('/edit-plan/{plan}', [PlanController::class,'edit'])->name('plans.edit');
    Route::put('/edit-plan/{plan}', [PlanController::class,'update'])->name('plans.update');
    Route::delete('/plans/{plan}', [PlanController::class,'destroy'])->name('plans.destroy');
    Route::get('/my-plan', [PlanController::class,'myplan'])->name('plans.myplan');
    Route::get('/plan-details', [PlanController::class, 'myPlanDetails'])->name('plans.details');
    // API endpoint for list
    Route::get('/api/plans-list', [PlanController::class,'apiList'])->name('plans.api-list');


    //calender
    Route::get('/calender', [CalenderController::class, 'index'])->name('calender.index');
    //chart
    Route::get('/chart', [ChartController::class, 'index'])->name('chart');


    Route::get('/sms', [SmsController::class, 'index'])->name('sms.index');
    Route::get('/template', [SmsController::class, 'template'])->name('sms.template');


    Route::get('/sms-template/create', function () {
        return view('sms.create');
    })->name('sms.create');
    Route::get('/sms-template/edit/{id}', function ($id) {
        return view('sms.edit', ['template_id' => $id]);
    })->name('sms.edit');

    Route::get('/sms/data', [SmsController::class, 'getSmsData']);
    // routes/api.php
    Route::delete('/sms/{id}', [SmsController::class, 'deleteSms'])->name('sms.delete');


    Route::post('/send-sms', [SmsController::class, 'sendSms'])->name('send.sms');


    // Route::post('/send-sms', [SmsController::class, 'handleSendSms'])->name('send.sms');






    Route::get('/email', [EmailController::class, 'index'])->name('email.index');

    Route::get('/email-template/create', function () {
        return view('email.create');
    })->name('email.create');

    Route::get('/email-template/edit/{id}', function ($id) {
        return view('email.edit', ['emailtemplate_id' => $id]);
    })->name('email.edit');


    Route::get('/email-template/show/{id}', function ($id) {
        return view('email.show', ['emailtemplate_id' => $id]);
    })->name('email.show');



    // Route::get('/sms/create', [SmsController::class, 'create'])->name('sms.create');
    Route::get('all-notifications', [NotificationController::class, 'notificationView'])->name('notificationView');
    Route::post('notifications/{id}', [NotificationController::class, 'update'])->name('notification.update');
    Route::post('/hide-today-appointments', function () {
        session()->forget('show_today_appointment_modal');
        return response()->json(['status' => 'success']);
    })->name('hide.today.appointments');
});







Route::get('/invoice-preview', function () {
    // Simulate getting settings from database
    $settings = Setting::whereIn('key', [
        'clinic_logo',
        'clinic_name',
        'clinic_address',
        'clinic_phone',
        'clinic_email',
        'clinic_city',
        'clinic_state'
    ])->pluck('value', 'key');
    $clinic_logo = $settings['clinic_logo'] ?? 'admin/assets/img/cliniclogo.png';
    $clinic_name = $settings['clinic_name'] ?? 'Sunshine Clinic';
    $clinic_address = $settings['clinic_address'] ?? '123 Health Street, City, Country';
    $clinic_phone = $settings['clinic_phone'] ?? '9876543210';
    $clinic_email = $settings['clinic_email'] ?? 'clinic@example.com';
    $clinic_city = $settings['clinic_city'] ?? 'xyz';
    $clinic_state = $settings['clinic_state'] ?? 'xyz';

    // dd($settings, $clinic_address);
    $invoice_no = 'INV-1001';
    $date = now()->format('d-m-Y');
    $invoice_type = 'service';

    $patient = (object) [
        'fullname' => 'John Doe',
        'address' => '123 Health St, Wellness City',
        'phone' => '1234567890'
    ];

    $services = [
        ['name' => 'Consultation', 'quantity' => 1, 'price' => 500, 'total' => 500],
        ['name' => 'X-Ray', 'quantity' => 2, 'price' => 300, 'total' => 600]
    ];

    $subtotal = collect($services)->sum('total');
    $tax = 10;
    $discount = 5;
    $tax_amount = ($subtotal * $tax) / 100;
    $grand_total = $subtotal + $tax_amount - $discount;

    return view('invoice.pdf', compact(
        'clinic_logo',
        'clinic_name',
        'clinic_address',
        'clinic_phone',
        'clinic_state',
        'clinic_city',
        'clinic_email',
        'invoice_no',
        'date',
        'invoice_type',
        'patient',
        'services',
        'subtotal',
        'tax',
        'tax_amount',
        'discount',
        'grand_total'
    ));
});

//pathelogy
Route::middleware(['session.auth', 'check.permission'])->group(function () {
    Route::get('/export-ot-procedures', [OtProcedureController::class, 'exportOtProcedures'])->name('ot.export');
    Route::get('/export-opd-visits', [OpdVisitController::class, 'exportOpdVisits'])->name('opd_visit.export');

    Route::get('/export-pathology', [PathologyTestController::class, 'exportpathology'])->name('pathology.export');
    Route::get('/export-pathology-reports', [PathologyReportController::class, 'exportPathologyReports'])->name('pathology-reports.export');
    Route::get('/ipd', [IpdAdmissionController::class, 'ipd'])->name('ipd.export');

    Route::get('/export-treatments', [TreatmentController::class, 'exportTreatments'])->name('treatments.export');
    Route::get('/radio-test/export', [RadiologyTestController::class, 'exportRadiologyTests'])->name('radio-test.export');
    Route::get('/export-radiology-reports', [RadiologyReportController::class, 'exportRadiologyReports'])->name('radio-report.export');

    Route::get('/export-therapy', [TherapyController::class, 'exportTherapies'])->name('therapy.export');
    Route::get('/export-assigntherapy', [AssignedTherapyController::class, 'exportAssignedTherapies'])->name('assign-therapy.export');
    Route::get('/diagnosis/export-csv', [DiagnosisController::class, 'exportCsv'])->name('diagnosis.export');

    Route::get('/export/appointments', [AppointmentController::class, 'exportAppointments'])->name('appointments.export');
    Route::get('/category/export', [CategoryController::class, 'export'])->name('category.export');
    Route::get('/discharge/export', [DischargeController::class, 'exportDischarges'])->name('discharge.export');
    Route::get('/followups/export-csv', [FollowupController::class, 'exportCsv'])->name('followup.export');
    Route::get('/inventory/export', [InventoryController::class, 'exportInventory'])->name('inventory.export');
    Route::get('/invoice/export', [DashboardController::class, 'export'])->name('invoice.export');
    Route::get('/medicines/export', [MedicineController::class, 'exportMedicines'])->name('medicine.export');
    Route::get('/patientmedicine/export', [PatientMedicineController::class, 'export'])->name('patientmedicine.export');
    Route::get('/patient/export', [PatientController::class, 'exportPatients'])->name('patient.export');
    Route::get('/report/export', [MedicalReportController::class, 'exportReports'])->name('report.export');
    Route::get('/services/export', [ServiceController::class, 'exportServices'])->name('service.export');
    Route::get('suppliers/export', [SupplierController::class, 'exportSuppliers'])->name('supplier.export');
    Route::get('/staff/export', [DashboardController::class, 'exportStaffs'])->name('staff.export');
    Route::get('/sms/export', [SmsController::class, 'exportSms'])->name('sms.export');
    Route::get('/sms-template/export', [SmsController::class, 'export'])->name('smstemplate.export');
    Route::get('/export-email-templates', [EmailController::class, 'exportTemplates'])->name('export.email.templates');
    Route::get('/export-send-emails', [EmailController::class, 'exportSendEmails'])->name('export.send.emails');
    Route::get('/assessment/export-csv', [AssessmentController::class, 'exportCsv'])->name('assessment.export');
    // Route::get('/export/home-advice', [HomeAdviceController::class, 'exportCsv'])->name('homeadvice.export');
    Route::get('/soap/export', [SoapController::class, 'exportCsv'])->name('soap.export');
});
