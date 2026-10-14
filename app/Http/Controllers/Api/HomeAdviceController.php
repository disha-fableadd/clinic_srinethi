<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\HomeAdvice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Setting;
use Illuminate\Support\Facades\File;
use App\Models\Branch;

class HomeAdviceController extends Controller
{
    // public function index()
    // {
    //     return response()->json(['data' => HomeAdvice::all()]);
    // }

    public function index(Request $request)
    {
        $query = HomeAdvice::with('branch'); // eager load branch

        // ✅ Filter by branch_id if provided
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $hasDataTable = $request->has('length') || $request->has('start') || $request->has('draw');

        if ($hasDataTable || $request->has('page') || $request->has('per_page')) {
            $page = (int) $request->input('page', 1);
            $perPage = (int) $request->input('per_page', 10);

            if ($hasDataTable) {
                $length = (int) $request->input('length', 10);
                if ($length === -1) {
                    $length = 0;
                }
                $perPage = $length > 0 ? $length : 10;
                $start = (int) $request->input('start', 0);
                $page = (int) floor($start / $perPage) + 1;
            }

            $page = $page > 0 ? $page : 1;
            $perPage = $perPage > 0 ? $perPage : 10;

            $searchValue = $hasDataTable
                ? $request->input('search.value', $request->input('search'))
                : $request->input('search');

            $filteredQuery = clone $query;

            if (!empty($searchValue)) {
                $filteredQuery->where(function ($q) use ($searchValue) {
                    $q->where('template_name', 'like', '%' . $searchValue . '%')
                        ->orWhere('title', 'like', '%' . $searchValue . '%')
                        ->orWhere('description', 'like', '%' . $searchValue . '%')
                        ->orWhereHas('branch', function ($b) use ($searchValue) {
                            $b->where('name', 'like', '%' . $searchValue . '%');
                        });
                });
            }

            $recordsFiltered = (clone $filteredQuery)->count();

            if ($hasDataTable && (int) $request->input('length') === -1) {
                $perPage = $recordsFiltered > 0 ? $recordsFiltered : 10;
            }

            $homeAdvices = $filteredQuery->latest()->forPage($page, $perPage)->get();
            $lastPage = (int) ceil($recordsFiltered / $perPage);

            // Map response
            $homeAdvices = $homeAdvices->map(function ($advice) {
                return [
                    'id'            => $advice->id,
                    'template_name' => $advice->template_name,
                    'branch_name'   => $advice->branch->name ?? 'N/A',
                    'title'         => $advice->title,
                    'description'   => $advice->description,
                    'image'         => $advice->image,
                    'created_at'    => $advice->created_at->toDateTimeString(),
                ];
            });

            return response()->json([
                'status' => true,
                'data'   => $homeAdvices,
                'pagination' => [
                    'current_page' => $page,
                    'last_page' => $lastPage > 0 ? $lastPage : 1,
                    'per_page' => $perPage,
                    'total' => $recordsFiltered,
                ],
            ], 200);
        }

        $homeAdvices = $query->latest()->get();

        // Map response
        $homeAdvices = $homeAdvices->map(function ($advice) {
            return [
                'id'            => $advice->id,
                'template_name' => $advice->template_name,
                'branch_name'   => $advice->branch->name ?? 'N/A',
                'title'         => $advice->title,       // array of titles
                'description'   => $advice->description, // array of descriptions
                'image'         => $advice->image,       // array of image paths
                'created_at'    => $advice->created_at->toDateTimeString(),
            ];
        });

        return response()->json([
            'status' => true,
            'data'   => $homeAdvices,
        ], 200);
    }




    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'template_name' => 'required|string|max:255',
    //         'title.*' => 'required|string|max:255',
    //         'description.*' => 'required|string',
    //         'image.*' => 'nullable|image|mimes:jpg,jpeg,png',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 422);
    //     }

    //     $titles = $request->input('title');
    //     $descriptions = $request->input('description');

    //     $imagePaths = [];

    //     if ($request->hasFile('image')) {
    //         foreach ($request->file('image') as $file) {
    //             $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
    //             $file->move(public_path('uploads/homeadvices'), $filename);
    //             $imagePaths[] = 'uploads/homeadvices/' . $filename;
    //         }
    //     } else {
    //         $imagePaths = array_fill(0, count($titles), null);
    //     }

    //     // Save arrays in DB
    //     $homeadviceTemplate = HomeAdvice::create([
    //         'template_name' => $request->template_name,
    //         'title' => $titles,
    //         'description' => $descriptions,
    //         'image' => $imagePaths,
    //     ]);

    //     // Build combined response
    //     $combined = [];
    //     foreach ($titles as $index => $title) {
    //         $combined[] = [
    //             'title' => $title,
    //             'description' => $descriptions[$index] ?? null,
    //             'image' => $imagePaths[$index] ?? null,
    //         ];
    //     }

    //     return response()->json([
    //         'message' => 'homeadvice template created successfully',
    //         'template_name' => $request->template_name,
    //         'data' => $combined
    //     ], 201);
    // }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'template_name' => 'required|string|max:255',
            'title.*'       => 'required|string|max:255',
            'description.*' => 'required|string',
            'image.*'       => 'nullable|image|mimes:jpg,jpeg,png',
            'branch_id'     => 'required|exists:branches,id', // ✅ get branch_id from frontend
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $titles = $request->input('title');
        $descriptions = $request->input('description');
        $branchId = $request->branch_id;

        $imagePaths = [];

        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $file) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/homeassessments'), $filename);
                $imagePaths[] = 'uploads/homeassessments/' . $filename;
            }
        } else {
            $imagePaths = array_fill(0, count($titles), null);
        }

        // ✅ Save HomeAssessment template
        $homeAssessmentTemplate = HomeAdvice::create([
            'template_name' => $request->template_name,
            'title'         => $titles,
            'description'   => $descriptions,
            'image'         => $imagePaths,
            'branch_id'     => $branchId,
        ]);

        // Build combined response
        $combined = [];
        foreach ($titles as $index => $title) {
            $combined[] = [
                'title'       => $title,
                'description' => $descriptions[$index] ?? null,
                'image'       => $imagePaths[$index] ?? null,
            ];
        }

        return response()->json([
            'message'       => 'HomeAssessment template created successfully',
            'template_name' => $request->template_name,
            'branch_id'     => $branchId,
            'data'          => $combined
        ], 201);
    }




    /**
     * Show single record.
     */
    // public function show($id)
    // {
    //     $homeAdvice = HomeAdvice::findOrFail($id);

    //     $combined = [];
    //     $titles = $homeAdvice->title ?? [];
    //     $descriptions = $homeAdvice->description ?? [];
    //     $images = $homeAdvice->image ?? [];

    //     foreach ($titles as $index => $title) {
    //         $combined[] = [
    //             'title' => $title,
    //             'description' => $descriptions[$index] ?? null,
    //             'image' => $images[$index] ?? null,
    //         ];
    //     }

    //     return response()->json([
    //         'id' => $homeAdvice->id,
    //         'template_name' => $homeAdvice->template_name,
    //         'items' => $combined,
    //     ]);
    // }

     public function show($id)
    {
        $homeAdvice = HomeAdvice::findOrFail($id);

        $combined = [];
        $titles = $homeAdvice->title ?? [];
        $descriptions = $homeAdvice->description ?? [];
        $images = $homeAdvice->image ?? [];

        foreach ($titles as $index => $title) {
            $combined[] = [
                'title' => $title,
                'description' => $descriptions[$index] ?? null,
                'image' => $images[$index] ?? null,
            ];
        }

        $clinicLogo = Setting::getValue('clinic_logo', 'uploads/default-logo.png');

        return response()->json([
            'id' => $homeAdvice->id,
            'template_name' => $homeAdvice->template_name,
            'items' => $combined,
            'clinic_logo' => config('app.url') . 'public/' . ltrim($clinicLogo, '/'),
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'template_name'   => 'required|string|max:255',
            'title'           => 'required|array|min:1',
            'title.*'         => 'required|string|max:255',
            'description'     => 'required|array|min:1',
            'description.*'   => 'required|string',
            'image.*'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        $assessment = HomeAdvice::findOrFail($id);

        $assessment->template_name = $request->template_name;
        $assessment->title         = array_values($request->title);
        $assessment->description   = array_values($request->description);

        // Get old images (make sure it's an array)
        $images = $assessment->image ?? [];

        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $index => $file) {
                if ($file) {
                    // upload new file
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/homeadvices'), $filename);

                    // replace only this index with the new uploaded file
                    $images[$index] = 'uploads/homeadvices/' . $filename;
                }
                // if no file uploaded for this index → keep the old one
            }
        }

        $assessment->image = array_values($images); // reset indexes properly
        $assessment->save();

        return response()->json([
            'success' => true,
            'message' => 'Assessment updated successfully'
        ]);
    }


    /**
     * Delete a record.
     */
    public function destroy($id)
    {
        $homeAdvice = HomeAdvice::findOrFail($id);
        $homeAdvice->delete();

        return response()->json(['message' => 'Home Advice deleted successfully!']);
    }



    public function exportCsv(Request $request)
    {
        $branchId = $request->input('branch_id'); // ✅ Branch filter

        $homeAdvices = HomeAdvice::orderBy('created_at', 'desc');

        // ✅ Apply branch filter if provided
        if ($branchId) {
            $homeAdvices->where('branch_id', $branchId);
        }

        $homeAdvices = $homeAdvices->get();

        if ($homeAdvices->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No Home Advice records found to export.'
            ]);
        }

        $filename = 'homeadvice_export_' . now()->format('Ymd_His') . '.csv';
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
            'Template Name',
            'Title(s)',
            'Description(s)',
            'Image(s)',

            'Created At'
        ]);

        $sr = 1;
        foreach ($homeAdvices as $advice) {
            $titles       = is_array($advice->title) ? implode(' | ', $advice->title) : $advice->title;
            $descriptions = is_array($advice->description) ? implode(' | ', $advice->description) : $advice->description;
            $images       = is_array($advice->image) ? implode(' | ', $advice->image) : $advice->image;

            fputcsv($file, [
                $sr++,
                $advice->template_name,
                $titles,
                $descriptions,
                $images,

                $advice->created_at
            ]);
        }

        fclose($file);

        return response()->json([
            'status' => true,
            'message' => 'Home Advice records exported successfully.',
            'file_url' => url('public/' . $folder . $filename),
            'file_name' => $filename
        ]);
    }






    // pdf


    // public function download($id)
    // {

    //  public function download($id)
    // {
    //     $homeadvice = HomeAdvice::findOrFail($id);

    //     $titles = is_array($homeadvice->title) ? $homeadvice->title : json_decode($homeadvice->title, true) ?? [];
    //     $descriptions = is_array($homeadvice->description) ? $homeadvice->description : json_decode($homeadvice->description, true) ?? [];
    //     $images = is_array($homeadvice->image) ? $homeadvice->image : json_decode($homeadvice->image, true) ?? [];

    //     // Fetch settings as key-value pair
    //     $settings = (new Setting())->getSettings();

    //     $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('homeadvice.pdf', compact('homeadvice', 'titles', 'descriptions', 'images', 'settings'));
    //     return $pdf->download('homeadvice-template-' . $homeadvice->id . '.pdf');
    // }

    public function download($id, Request $request)
    {
        try {
            // ✅ Fetch record
            $homeadvice = \App\Models\HomeAdvice::findOrFail($id);

            // ✅ Decode arrays safely
            $titles       = is_array($homeadvice->title) ? $homeadvice->title : json_decode($homeadvice->title, true) ?? [];
            $descriptions = is_array($homeadvice->description) ? $homeadvice->description : json_decode($homeadvice->description, true) ?? [];
            $images       = is_array($homeadvice->image) ? $homeadvice->image : json_decode($homeadvice->image, true) ?? [];

            // ✅ Load settings
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

            // ✅ Data for Blade
            $data = [
                'date'          => now()->format('d-m-Y'),
                'homeadvice'    => $homeadvice,
                'titles'        => $titles,
                'descriptions'  => $descriptions,
                'images'        => $images,
                'clinic_logo'   => $clinic_logo_path,
                'settings'      => $settings,
            ];

            // ✅ Generate PDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('homeadvice.pdf', $data)
                ->setPaper('A4', 'portrait');

            // ✅ Save inside /public/storage/homeadvice/
            $folder   = public_path('storage/homeadvice/');
            $filename = "HomeAdvice_{$homeadvice->id}.pdf";
            $path     = $folder . $filename;

            // Ensure folder exists
            if (!file_exists($folder)) {
                mkdir($folder, 0777, true);
            }

            // Save PDF
            $pdf->save($path);

            // ✅ Build Public URL
            $fileUrl = url('public/storage/homeadvice/' . $filename);

            // 👉 If API/Postman → JSON response
            if ($request->wantsJson() || $request->header('Accept') === 'application/json') {
                return response()->json([
                    'status'    => true,
                    'message'   => 'HomeAdvice PDF generated successfully.',
                    'file_url'  => $fileUrl, // e.g. https://hms-demo.fableadtech.com/public/storage/homeadvice/HomeAdvice_33.pdf
                    'file_name' => $filename,
                ]);
            }

            // 👉 Otherwise → browser download
            return response()->download($path);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'HomeAdvice PDF generation failed',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function homeadvicePdf(Request $request, $id)
    {
        try {
            // ✅ Fetch record
            $homeadvice = \App\Models\HomeAdvice::findOrFail($id);

            // ✅ Decode arrays safely
            $titles       = is_array($homeadvice->title) ? $homeadvice->title : json_decode($homeadvice->title, true) ?? [];
            $descriptions = is_array($homeadvice->description) ? $homeadvice->description : json_decode($homeadvice->description, true) ?? [];
            $images       = is_array($homeadvice->image) ? $homeadvice->image : json_decode($homeadvice->image, true) ?? [];

            // ✅ Load settings
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

            // ✅ Data for Blade
            $data = [
                'date'          => now()->format('d-m-Y'),
                'homeadvice'    => $homeadvice,
                'titles'        => $titles,
                'descriptions'  => $descriptions,
                'images'        => $images,
                'clinic_logo'   => $clinic_logo_path,
                'settings'      => $settings,
            ];

            // ✅ Generate PDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('homeadvice.pdf', $data)
                ->setPaper('A4', 'portrait');

            // ✅ Save inside /public/storage/homeadvice/
            $folder   = public_path('storage/homeadvice/');
            $filename = "HomeAdvice_{$homeadvice->id}.pdf";
            $path     = $folder . $filename;

            // Ensure folder exists
            if (!file_exists($folder)) {
                mkdir($folder, 0777, true);
            }

            // Save PDF
            $pdf->save($path);

            // ✅ Build Public URL
            $fileUrl = url('public/storage/homeadvice/' . $filename);

            // 👉 If API/Postman → JSON response
            if ($request->wantsJson() || $request->header('Accept') === 'application/json') {
                return response()->json([
                    'status'    => true,
                    'message'   => 'HomeAdvice PDF generated successfully.',
                    'file_url'  => $fileUrl, // e.g. https://hms-demo.fableadtech.com/public/storage/homeadvice/HomeAdvice_33.pdf
                    'file_name' => $filename,
                ]);
            }

            // 👉 Otherwise → browser download
            return response()->download($path);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'HomeAdvice PDF generation failed',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
