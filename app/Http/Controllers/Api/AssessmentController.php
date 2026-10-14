<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentTemplate;
use App\Models\Setting;
use App\Models\Notification;
use App\Models\Therapy;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;
use Sabberworm\CSS\Settings;
use App\Models\Branch;

class AssessmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }




 public function exportCsv(Request $request)
{
    $branchId = $request->input('branch_id'); // Branch filter

    $assessments = AssessmentTemplate::query()
        ->orderBy('created_at', 'desc');

    // Apply branch filter if provided
    if ($branchId) {
        $assessments->where('branch_id', $branchId);
    }

    $assessments = $assessments->get();

    if ($assessments->isEmpty()) {
        return response()->json([
            'status' => false,
            'message' => 'No assessment records found to export.'
        ]);
    }

    $filename = 'assessment_export_' . now()->format('Ymd_His') . '.csv';
    $folder = 'uploads/exports/';
    $publicPath = public_path($folder);

    // Create folder if not exists
    if (!File::exists($publicPath)) {
        File::makeDirectory($publicPath, 0777, true);
    }

    $fullPath = $publicPath . $filename;
    $file = fopen($fullPath, 'w');

    // CSV Header
    fputcsv($file, [
        'ID',
        'Name',
        'Title(s)',
        'Description(s)',
        'Image(s)',
        'Created At'
    ]);

    $sr = 1;
    foreach ($assessments as $assessment) {
        $titles       = is_array($assessment->title) ? implode(' | ', $assessment->title) : $assessment->title;
        $descriptions = is_array($assessment->description) ? implode(' | ', $assessment->description) : $assessment->description;
        $images       = is_array($assessment->image) ? implode(' | ', $assessment->image) : $assessment->image;

        fputcsv($file, [
            $sr++,
            $assessment->name,
            $titles,
            $descriptions,
            $images,
            $assessment->created_at ? $assessment->created_at->format('d-M-Y h:i A') : 'N/A'
        ]);
    }

    fclose($file);

    return response()->json([
        'status' => true,
        'message' => 'Assessment records exported successfully.',
       'file_url' => url('public/' . $folder . $filename),
        'file_name' => $filename
    ]);
}





   

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'          => 'required|string|max:255',
            'title'         => 'required|array|min:1',
            'title.*'       => 'required|string|max:255',
            'description'   => 'required|array|min:1',
            'description.*' => 'required|string',
            'image.*'       => 'nullable|image|mimes:jpg,jpeg,png',
            'branch_id'     => 'required|exists:branches,id', // ✅ validate branch
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $imagePaths = [];

        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $file) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/assessments'), $filename);
                $imagePaths[] = 'uploads/assessments/' . $filename;
            }
        } else {
            $imagePaths = array_fill(0, count($request->title), null);
        }

        $assessmentTemplate = AssessmentTemplate::create([
            'name'        => $request->name,
            'title'       => $request->title,
            'description' => $request->description,
            'image'       => $imagePaths,
            'branch_id'   => $request->branch_id, // ✅ store branch from request
        ]);

        return response()->json([
            'message' => 'Assessment template created successfully',
            'data'    => $assessmentTemplate,
        ], 201);
    }


    // index method
    // public function index()
    // {

    //     $assessments = AssessmentTemplate::latest()->get();

    //     return response()->json([
    //         'status' => true,
    //         'data' => $assessments
    //     ], 200);
    // }

    public function index(Request $request)
    {
        $query = AssessmentTemplate::with('branch'); // eager load branch

        // ✅ Filter by branch
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
                    $q->where('name', 'like', '%' . $searchValue . '%')
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

            $assessments = $filteredQuery->latest()->forPage($page, $perPage)->get();
            $lastPage = (int) ceil($recordsFiltered / $perPage);

            $assessments = $assessments->map(function ($assessment) {
                return [
                    'id'          => $assessment->id,
                    'name'        => $assessment->name,
                    'title'       => $assessment->title,
                    'description' => $assessment->description,
                    'image'       => $assessment->image,
                    'branch_name' => $assessment->branch->name ?? 'N/A',
                    'created_at'  => $assessment->created_at->toDateTimeString(),
                ];
            });

            return response()->json([
                'status' => true,
                'data'   => $assessments,
                'pagination' => [
                    'current_page' => $page,
                    'last_page' => $lastPage > 0 ? $lastPage : 1,
                    'per_page' => $perPage,
                    'total' => $recordsFiltered,
                ],
            ], 200);
        }

        $assessments = $query->latest()->get();

        // ✅ Format response with branch name
        $assessments = $assessments->map(function ($assessment) {
            return [
                'id'          => $assessment->id,
                'name'        => $assessment->name,
                'title'       => $assessment->title,
                'description' => $assessment->description,
                'image'       => $assessment->image,
                'branch_name' => $assessment->branch->name ?? 'N/A',
                'created_at'  => $assessment->created_at->toDateTimeString(),
            ];
        });

        return response()->json([
            'status' => true,
            'data'   => $assessments
        ], 200);
    }

    // delete

    public function destroy($id)
    {
        $assessment = AssessmentTemplate::find($id);

        if (!$assessment) {
            return response()->json([
                'status' => false,
                'message' => 'Assessment not found.'
            ], 404);
        }

        $assessment->delete();

        return response()->json([
            'status' => true,
            'message' => 'Assessment deleted successfully.'
        ], 200);
    }


    // public function show($id)
    // {
    //     $assessment = AssessmentTemplate::findOrFail($id);

    //     return response()->json($assessment);
    // }

     public function show($id)
    {
        $assessment = AssessmentTemplate::findOrFail($id);

      
        $clinicLogo = Setting::getValue('clinic_logo', 'uploads/default-logo.png');

        return response()->json(
            array_merge($assessment->toArray(), [
                'clinic_logo' => config('app.url') . 'public/' . ltrim($clinicLogo, '/')
            ])
        );
    }




    public function update(Request $request, $id)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'title'         => 'required|array|min:1',
            'title.*'       => 'required|string|max:255',
            'description'   => 'required|array|min:1',
            'description.*' => 'required|string',
            'image.*'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        $assessment = AssessmentTemplate::findOrFail($id);

        $assessment->name        = $request->name;
        $assessment->title       = array_values($request->title);
        $assessment->description = array_values($request->description);

        // Keep old images if not replaced
        $images = $assessment->image ?? [];

        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $index => $file) {
                if ($file) {
                    // Upload new file
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/assessments'), $filename);

                    // Replace only this index with the new uploaded file
                    $images[$index] = 'uploads/assessments/' . $filename;
                }
                // else → no upload for this index → keep old one
            }
        }

        // Reindex array (important for JSON storage)
        $assessment->image = array_values($images);
        $assessment->save();

        return response()->json([
            'success' => true,
            'message' => 'Assessment updated successfully'
        ]);
    }


    // public function download($id)
    // {
    //     $assessment = AssessmentTemplate::findOrFail($id);

    //     $titles = is_array($assessment->title) ? $assessment->title : json_decode($assessment->title, true) ?? [];
    //     $descriptions = is_array($assessment->description) ? $assessment->description : json_decode($assessment->description, true) ?? [];
    //     $images = is_array($assessment->image) ? $assessment->image : json_decode($assessment->image, true) ?? [];

    //     // Fetch settings as key-value pair
    //     $settings = (new Setting())->getSettings();

    //     $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('assessment.pdf', compact('assessment', 'titles', 'descriptions', 'images', 'settings'));
    //     return $pdf->download('assessment-template-' . $assessment->id . '.pdf');
    // }


    public function download(Request $request, $id)
    {
        try {

            $assessment = AssessmentTemplate::findOrFail($id);


            $titles       = is_array($assessment->title) ? $assessment->title : json_decode($assessment->title, true) ?? [];
            $descriptions = is_array($assessment->description) ? $assessment->description : json_decode($assessment->description, true) ?? [];
            $images       = is_array($assessment->image) ? $assessment->image : json_decode($assessment->image, true) ?? [];


            $settings = (new Setting())->getSettings();


            $data = [
                'assessment'   => $assessment,
                'titles'       => $titles,
                'descriptions' => $descriptions,
                'images'       => $images,
                'settings'     => $settings,
            ];


            $pdf = \Pdf::loadView('assessment.pdf', $data)
                ->setPaper('A4', 'portrait');

            $folder   = public_path('storage/assessments/');
            $filename = "assessment-template-{$assessment->id}.pdf";
            $path     = $folder . $filename;


            if (!file_exists($folder)) {
                mkdir($folder, 0777, true);
            }


            $pdf->save($path);


            $fileUrl = url('public/storage/assessments/' . $filename);

            // 👉 If API/Postman → return JSON
            if ($request->wantsJson() || $request->header('Accept') === 'application/json') {
                return response()->json([
                    'status'    => true,
                    'message'   => 'Assessment PDF generated successfully.',
                    'file_url'  => $fileUrl,
                    'file_name' => $filename,
                ]);
            }

            // 👉 Otherwise (Browser) → download directly
            return response()->download($path, $filename);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Assessment PDF generation failed',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
