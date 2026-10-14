<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DietChart;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Validator;

class DietChartController extends Controller
{
       public function index(Request $request)
    {
        $query = DietChart::query();

        // Filter by branch_id if provided
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $dietCharts = $query->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $dietCharts
        ]);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'branch_id' => 'nullable|integer',
            'name' => 'required|string|max:255',
            'title' => 'required|array',
            'description' => 'required|array',
            'time' => 'required|array',
            'image.*' => 'nullable|image|mimes:jpg,jpeg,png,gif',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $imagePaths = [];

        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $file) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/diet_charts'), $filename);
                $imagePaths[] = 'uploads/diet_charts/' . $filename;
            }
        } else {
            $imagePaths = array_fill(0, count($request->title), null);
        }

        $dietChart = DietChart::create([
            'branch_id' => $request->branch_id,
            'name' => $request->name,
            'title' => $request->title,
            'description' => $request->description,
            'time' => $request->time,
            'image' => $imagePaths,
        ]);

        return response()->json(['message' => 'Diet chart created successfully!', 'data' => $dietChart]);
    }

    // public function show($id)
    // {
    //     $dietChart = DietChart::findOrFail($id); // 404 if not found
    //     $clinicLogo = Setting::getValue('clinic_logo', 'default-logo.png'); // Default logo

    //     return response()->json(
    //         array_merge($dietChart->toArray(), [
    //             'clinic_logo' => url($clinicLogo)
    //         ])
    //     );
    // }

       public function show($id)
    {
        $dietChart = DietChart::findOrFail($id); // 404 if not found
        $clinicLogo = Setting::getValue('clinic_logo', 'uploads/default-logo.png'); // Default logo

        return response()->json(
            array_merge($dietChart->toArray(), [
                'clinic_logo' => config('app.url') . 'public/' . ltrim($clinicLogo, '/')
            ])
        );
    }







    public function update(Request $request, $id)
    {
        $dietChart = DietChart::find($id);
        if (!$dietChart) {
            return response()->json(['message' => 'Diet chart not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'title' => 'sometimes|required|array',
            'description' => 'sometimes|required|array',
            'time' => 'sometimes|required|array',
            'image.*' => 'nullable|image|mimes:jpg,jpeg,png,gif',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $imagePaths = $dietChart->image ?? [];

        // Handle newly uploaded images
        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $file) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                // Store in public/uploads/diet_charts
                $file->move(public_path('uploads/diet_charts'), $filename);

                // Save relative path for frontend
                $imagePaths[] = 'uploads/diet_charts/' . $filename;
            }
        }

        $dietChart->update([
            'name' => $request->name ?? $dietChart->name,
            'title' => $request->title ?? $dietChart->title,
            'description' => $request->description ?? $dietChart->description,
            'time' => $request->time ?? $dietChart->time,
            'image' => $imagePaths,
        ]);

        return response()->json(['message' => 'Diet chart updated successfully!', 'data' => $dietChart]);
    }


  public function updatePost(Request $request, $id)
{
    $dietChart = DietChart::find($id);

    if (!$dietChart) {
        return response()->json(['message' => 'Diet chart not found'], 404);
    }

    $validator = Validator::make($request->all(), [
        'name' => 'sometimes|required|string|max:255',
        'title' => 'sometimes|required|array',
        'description' => 'sometimes|required|array',
        'time' => 'sometimes|required|array',
        'image.*' => 'nullable|image|mimes:jpg,jpeg,png,gif',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

   
    $oldImages = $dietChart->image ?? [];

   
    $newImages = [];

    // Check for new uploads
    if ($request->hasFile('image')) {
        foreach ($request->file('image') as $file) {
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/diet_charts'), $filename);

            $newImages[] = 'uploads/diet_charts/' . $filename;
        }

        // If new images uploaded → replace old ones
        $finalImages = $newImages;
    } else {
        // No new upload → keep old ones
        $finalImages = $oldImages;
    }

    // Update fields
    $dietChart->name = $request->name ?? $dietChart->name;
    $dietChart->title = $request->title ?? $dietChart->title;
    $dietChart->description = $request->description ?? $dietChart->description;
    $dietChart->time = $request->time ?? $dietChart->time;
    $dietChart->image = $finalImages;   // ← Correct image logic
    $dietChart->save();

    // Response also returns final images
    return response()->json([
        'message' => 'Diet chart updated successfully!',
        'data' => [
            'id' => $dietChart->id,
            'name' => $dietChart->name,
            'title' => $dietChart->title,
            'description' => $dietChart->description,
            'time' => $dietChart->time,
            'image' => $finalImages   // ✔ If updated → new images, else old ones
        ]
    ], 200);
}

    public function destroy($id)
    {
        $dietChart = DietChart::find($id);
        if (!$dietChart) {
            return response()->json(['message' => 'Diet chart not found'], 404);
        }

        $dietChart->delete();
        return response()->json(['message' => 'Diet chart deleted successfully!']);
    }

    public function download(Request $request, $id)
    {
        // dd('wqe');
        try {
            $dietChart = DietChart::findOrFail($id);

            // Decode JSON arrays if needed
            $titles = is_array($dietChart->title) ? $dietChart->title : json_decode($dietChart->title, true) ?? [];
            $descriptions = is_array($dietChart->description) ? $dietChart->description : json_decode($dietChart->description, true) ?? [];
            $images = is_array($dietChart->image) ? $dietChart->image : json_decode($dietChart->image, true) ?? [];
            $times = is_array($dietChart->time) ? $dietChart->time : json_decode($dietChart->time, true) ?? [];

            // Fetch clinic settings if needed
            $settings = (new Setting())->getSettings();

            $data = [
                'dietChart' => $dietChart,
                'titles' => $titles,
                'descriptions' => $descriptions,
                'images' => $images,
                'times' => $times,
                'settings' => $settings,
            ];

            // Generate PDF
            $pdf = \Pdf::loadView('dietchart.pdf', $data)
                ->setPaper('A4', 'portrait');

            // Save folder & filename
            $folder = public_path('storage/dietcharts/');
            $filename = "dietchart-{$dietChart->id}.pdf";
            $path = $folder . $filename;

            if (!file_exists($folder)) {
                mkdir($folder, 0777, true);
            }

            $pdf->save($path);

            $fileUrl = url('public/storage/dietcharts/' . $filename);

            // If API request → return JSON
            if ($request->wantsJson() || $request->header('Accept') === 'application/json') {
                return response()->json([
                    'status' => true,
                    'message' => 'Diet Chart PDF generated successfully.',
                    'file_url' => $fileUrl,
                    'file_name' => $filename,
                ]);
            }

            // Otherwise → download directly
            return response()->download($path, $filename);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Diet Chart PDF generation failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
