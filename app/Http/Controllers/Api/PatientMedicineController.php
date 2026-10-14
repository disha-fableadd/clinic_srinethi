<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\PatientMedicine;
use App\Models\Patients;
use App\Models\Setting;
use App\Models\Treatment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class PatientMedicineController extends Controller
{


   





    public function medicinePdf(Request $request, $id)
    {
        try {
            // ✅ Load patient medicine with relations
            $patientMedicine = \App\Models\PatientMedicine::with(['patient', 'treatment.doctor'])
                ->findOrFail($id);

            // ✅ Fetch clinic details
            $settings = \App\Models\Setting::whereIn('key', [
                'clinic_logo',
                'clinic_name',
                'clinic_address',
                'clinic_phone',
                'clinic_email',
                'clinic_city',
                'clinic_state',
            ])->pluck('value', 'key');

            $clinic_logo = $settings['clinic_logo'] ?? 'admin/assets/img/cliniclogo.png';
            $clinic_logo_path = public_path($clinic_logo);

            // ✅ Decode medicine IDs
            $medicineIds = json_decode($patientMedicine->medicine_id, true) ?? [];

            // ✅ Fetch medicines
            $medicines = \App\Models\Medicine::whereIn('id', $medicineIds)
                ->get(['id', 'name', 'description', 'unit', 'manufacture_date', 'expiry_date'])
                ->keyBy('id');

            $codes = json_decode($patientMedicine->code, true) ?? [];
            $values = json_decode($patientMedicine->value, true) ?? [];
            $valueTypes = json_decode($patientMedicine->value_type, true) ?? [];

            // Fetch codes to show code names instead of IDs
            $codesList = \App\Models\CodeMaster::whereIn('id', $codes)->get()->keyBy('id');

            $medicineDataList = [];
            foreach ($medicineIds as $index => $medId) {
                if (isset($medicines[$medId])) {
                    $medicine = $medicines[$medId];
                    $medicineDataList[] = [
                        'id'              => $medicine->id,
                        'name'            => $medicine->name,
                        'description'     => $medicine->description ?? '--',
                        'unit'            => $medicine->unit ?? 1,
                        'manufacture_date' => $medicine->manufacture_date ?? '--',
                        'expiry_date'     => $medicine->expiry_date ?? '--',
                        'code'            => isset($codes[$index]) && isset($codesList[$codes[$index]]) ? $codesList[$codes[$index]]->code : '--',
                        'value'           => $values[$index] ?? '--',
                        'value_type'      => $valueTypes[$index] ?? '--',
                    ];
                }
            }

            // ✅ Prepare data for Blade
            $data = [
                'date'          => now()->format('d-m-Y'),
                'patient'       => $patientMedicine->patient,
                'treatment'     => $patientMedicine->treatment,
                'medicines'     => $medicineDataList,
                'clinic_logo'   => $clinic_logo_path,
                'clinic_name'   => $settings['clinic_name'] ?? 'Sunshine Clinic',
                'clinic_address' => $settings['clinic_address'] ?? '123 Health Street',
                'clinic_phone'  => $settings['clinic_phone'] ?? '1234567890',
                'clinic_email'  => $settings['clinic_email'] ?? 'clinic@example.com',
                'clinic_city'   => $settings['clinic_city'] ?? 'xyz',
                'clinic_state'  => $settings['clinic_state'] ?? 'xyz',
                'note'          => $patientMedicine->note,
            ];

            // ✅ Generate PDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('patient_medicine.medicine_pdf', $data)
                ->setPaper('A4', 'portrait');

            // ✅ Save directly into /public/storage/patient-medicines/
            $folder   = public_path('storage/patient-medicines/');
            $fileName = "PatientMedicine_{$patientMedicine->id}.pdf";
            $path     = $folder . $fileName;

            // Ensure folder exists
            if (!file_exists($folder)) {
                mkdir($folder, 0777, true);
            }

            // Save PDF
            $pdf->save($path);

            // ✅ Public URL
            $fileUrl = url('public/storage/patient-medicines/' . $fileName);

            // 👉 API request → JSON
            if ($request->wantsJson() || $request->header('Accept') === 'application/json') {
                return response()->json([
                    'status'    => true,
                    'message'   => 'Patient Medicine PDF generated successfully.',
                    'file_url'  => $fileUrl,
                    'file_name' => $fileName,
                ], 200);
            }

            // 👉 Browser request → Download
            return response()->download($path, $fileName);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Medicine PDF generation failed',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }







   public function exportCsv(Request $request)
{
    $branchId = $request->input('branch_id'); // ✅ Branch filter

    $records = PatientMedicine::with(['patient', 'medicine', 'treatment', 'appointment'])
        ->orderBy('created_at', 'desc');

    // ✅ Apply branch filter if provided
    if ($branchId) {
        $records->where('branch_id', $branchId);
    }

    $records = $records->get();

    if ($records->isEmpty()) {
        return response()->json([
            'status' => false,
            'message' => 'No patient medicine records found to export.'
        ]);
    }

    $filename = 'patient_medicine_export_' . now()->format('Ymd_His') . '.csv';
    $folder = 'uploads/exports/';
    $publicPath = public_path($folder);

    if (!File::exists($publicPath)) {
        File::makeDirectory($publicPath, 0777, true);
    }

    $fullPath = $publicPath . $filename;
    $file = fopen($fullPath, 'w');

    // CSV Header
    fputcsv($file, [
        'ID',
        'Patient Name',
        'Medicine Names',
        'Treatment Name',
        'Appointment Date',
        'Note',
        'Created At'
    ]);

    $sr = 1;

    foreach ($records as $row) {
        $medicineNames = [];

        // Decode JSON and fetch medicine names
        $medicineIds = json_decode($row->medicine_id, true);

        if (is_array($medicineIds)) {
            $medicines = \App\Models\Medicine::whereIn('id', $medicineIds)->get();
            $medicineNames = $medicines->pluck('name')->toArray();
        }

        fputcsv($file, [
            $sr++,
            $row->patient->fullname ?? '',
            implode(', ', $medicineNames),
            $row->treatment->name ?? '',
            $row->appointment->date ?? 'N/A',
            $row->note ?? '',
            $row->created_at
        ]);
    }

    fclose($file);

    return response()->json([
        'status' => true,
        'message' => 'Patient medicines exported successfully.',
        'file_url' => url('public/' . $folder . $filename),
        'file_name' => $filename
    ]);
}


  

    public function index(Request $request)
    {
        $branchId = $request->get('branch_id'); // get from frontend

        $patientMedicines = DB::table('patient_medicine')
            ->join('patients', 'patients.id', '=', 'patient_medicine.patient_id')
            ->leftJoin('treatments', 'treatments.id', '=', 'patient_medicine.treatment_id')
            ->select(
                'patient_medicine.id',
                'patients.id as patient_id',
                'patients.fullname as patient_name',
                'patients.profile as profile',
                'patient_medicine.medicine_id',
                'patient_medicine.treatment_id',
                'treatments.name as treatment_name',
                'patient_medicine.note',
                'patient_medicine.code',
                'patient_medicine.value',
                'patient_medicine.value_type',
                'patient_medicine.branch_id', // ✅ also return branch_id if needed
                'patient_medicine.created_at',
                'patient_medicine.updated_at'
            )
            ->when($branchId, function ($query) use ($branchId) {
                $query->where('patient_medicine.branch_id', $branchId); // ✅ correct column
            })
            ->orderBy('patient_medicine.id', 'desc')
            ->get();

        // Process medicine details
        $patientMedicines->transform(function ($item) {
            $medicineIds = json_decode($item->medicine_id, true);
            $codes = json_decode($item->code, true) ?? [];
            $values = json_decode($item->value, true) ?? [];
            $valueTypes = json_decode($item->value_type, true) ?? [];

            $medicines = DB::table('medicines')
                ->whereIn('id', $medicineIds ?? [])
                ->pluck('name')
                ->toArray();

            $item->medicines = $medicines;

            // Fetch codes to show code names
            $codesList = DB::table('code_master')->whereIn('id', $codes)->pluck('code', 'id')->toArray();
            $formattedCodes = [];
            foreach ($codes as $cId) {
                $formattedCodes[] = $codesList[$cId] ?? '--';
            }

            $item->codes = implode(', ', $formattedCodes);
            $item->values = implode(', ', $values);
            $item->value_types = implode(', ', $valueTypes);

            unset($item->medicine_id);
            unset($item->code);
            unset($item->value);
            unset($item->value_type);

            return $item;
        });

        return response()->json($patientMedicines, 200);
    }




    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'patient_id' => 'required|exists:patients,id',
    //         'medicines' => 'required|array',
    //         // 'medicines.*' => 'exists:medicines,id',
    //         'note' => 'nullable|string',
    //         'treatment_id' => 'required|exists:treatments,id',

    //     ]);

    //     $patient_id = $request->patient_id;
    //     $treatment_id = $request->treatment_id;
    //     $medicines = $request->medicines;
    //     $notes = $request->note;


    //     PatientMedicine::create([
    //         'patient_id' => $patient_id,
    //         'medicine_id' => json_encode($medicines),
    //         'note' => $notes,
    //         'treatment_id' => $treatment_id
    //     ]);


    //     return response()->json(['message' => 'Medicines give successfully'], 200);
    // }


    public function store(Request $request)
    {
        $request->validate([
            'patient_id'   => 'required|exists:patients,id',
            'medicines'    => 'required|array',
            'note'         => 'nullable|string',
            'treatment_id' => 'required|exists:treatments,id',
            'branch_id'    => 'required|exists:branches,id', // ✅ validate branch
        ]);

        PatientMedicine::create([
            'patient_id'   => $request->patient_id,
            'medicine_id'  => json_encode($request->medicines),
            'code'         => json_encode($request->codes),
            'value'        => json_encode($request->medicine_values),
            'value_type'   => json_encode($request->medicine_types),
            'note'         => $request->note,
            'treatment_id' => $request->treatment_id,
            'branch_id'    => $request->branch_id, // ✅ save branch
        ]);

        return response()->json(['message' => 'Medicines given successfully'], 200);
    }





    public function show($id)
    {
        $patientMedicine = PatientMedicine::with('patient.treatment')->findOrFail($id);

        $medicineIds = json_decode($patientMedicine->medicine_id) ?? [];
        $codes = json_decode($patientMedicine->code) ?? [];
        $values = json_decode($patientMedicine->value) ?? [];
        $valueTypes = json_decode($patientMedicine->value_type) ?? [];

        // Fetch medicines with category
        $medicines = Medicine::with('category')->whereIn('id', $medicineIds)->get()->keyBy('id');
        
        // Fetch codes to show code names instead of IDs
        $codesList = \App\Models\CodeMaster::whereIn('id', $codes)->get()->keyBy('id');

        $medicineData = [];
        foreach ($medicineIds as $index => $medId) {
            if (isset($medicines[$medId])) {
                $med = $medicines[$medId];
                // Create a data array to ensure pivot fields are included in the JSON response
                $medicineData[] = array_merge($med->toArray(), [
                    'category' => $med->category ? $med->category->toArray() : null,
                    'pivot_code' => isset($codes[$index]) && isset($codesList[$codes[$index]]) ? $codesList[$codes[$index]]->code : 'N/A',
                    'pivot_value' => $values[$index] ?? 'N/A',
                    'pivot_value_type' => $valueTypes[$index] ?? 'N/A',
                ]);
            }
        }

        return response()->json([
            'id' => $patientMedicine->id,
            'note' => $patientMedicine->note,
            'patient' => [
                'id' => $patientMedicine->patient->id,
                'fullname' => $patientMedicine->patient->fullname,
                'phone' => $patientMedicine->patient->phone,
                'age' => $patientMedicine->patient->age,
                'blood_group' => $patientMedicine->patient->blood_group,
                'medical_history' => $patientMedicine->patient->medical_history,
                'treatment' => $patientMedicine->patient->treatment,  // full treatment model
                'profile' => $patientMedicine->patient->profile,
            ],
            'medicines' => $medicineData,
            'medicine_ids' => $medicineIds,
            'codes' => $codes,
            'values' => $values,
            'value_types' => $valueTypes,
            'treatment_id' => $patientMedicine->treatment_id,
        ]);
    }


    /**
     * Update the specified patient medicine record.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'medicines' => 'required|array',
            'medicines.*' => 'exists:medicines,id', // Validate each medicine ID exists
            'note' => 'nullable|string',
            'treatment_id' => 'required|exists:treatments,id',
        ]);

        $patientMedicine = PatientMedicine::findOrFail($id);

        // Convert array into JSON before storing (if needed)
        $patientMedicine->update([
            'patient_id' => $request->patient_id,
            'medicine_id' => json_encode($request->medicines), // Convert array to JSON
            'code' => json_encode($request->codes),
            'value' => json_encode($request->medicine_values),
            'value_type' => json_encode($request->medicine_types),
            'note' => $request->note,
            'treatment_id' => $request->treatment_id
        ]);

        return response()->json(['message' => 'Patient medicine updated successfully']);
    }


    /**
     * Remove the specified patient medicine record.
     */
    public function destroy($id)
    {
        $patientMedicine = PatientMedicine::findOrFail($id);
        $patientMedicine->delete();

        return response()->json(['message' => 'Patient medicine deleted successfully']);
    }
}
