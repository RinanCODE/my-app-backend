<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CertificateTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificateTemplateController extends Controller
{
    /**
     * Display a listing of templates.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Get default templates and user's custom templates
        $templates = CertificateTemplate::where(function($query) use ($user) {
            $query->where('is_default', true)
                  ->orWhere('user_id', $user->id);
        })
        ->where('is_active', true)
        ->orderBy('is_default', 'desc')
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json($templates);
    }

    /**
     * Store a newly created template.
     */
    public function store(Request $request)
    {
        // Frontend sends customization_settings as JSON string inside multipart/form-data.
        // Normalize it here so validation and casting work correctly.
        if (is_string($request->input('customization_settings'))) {
            $decoded = json_decode($request->input('customization_settings'), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $request->merge(['customization_settings' => $decoded]);
            }
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:default,custom,uploaded',
            'html_template' => 'nullable|string',
            'css_styles' => 'nullable|string',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'customization_settings' => 'nullable|array',
        ]);

        $user = $request->user();
        $validated['user_id'] = $user->id;

        // Handle background image upload
        if ($request->hasFile('background_image')) {
            $image = $request->file('background_image');
            $imagePath = $image->store('certificates/templates/backgrounds', 'public');
            $validated['background_image_path'] = $imagePath;
        }

        unset($validated['background_image']);

        $template = CertificateTemplate::create($validated);

        return response()->json($template, 201);
    }

    /**
     * Display the specified template.
     */
    public function show(CertificateTemplate $certificateTemplate)
    {
        return response()->json($certificateTemplate);
    }

    /**
     * Update the specified template.
     */
    public function update(Request $request, CertificateTemplate $certificateTemplate)
    {
        // Only allow updating user's own templates or if it's a default template being customized
        $user = $request->user();
        if ($certificateTemplate->user_id !== $user->id && !$certificateTemplate->is_default) {
            return response()->json([
                'message' => 'Unauthorized. You can only update your own templates.'
            ], 403);
        }

        // Frontend sends customization_settings as JSON string inside multipart/form-data.
        // Normalize it here so validation and casting work correctly.
        if (is_string($request->input('customization_settings'))) {
            $decoded = json_decode($request->input('customization_settings'), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $request->merge(['customization_settings' => $decoded]);
            }
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'html_template' => 'nullable|string',
            'css_styles' => 'nullable|string',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'customization_settings' => 'nullable|array',
        ]);

        // Handle background image upload
        if ($request->hasFile('background_image')) {
            // Delete old image if exists
            if ($certificateTemplate->background_image_path && Storage::exists('public/' . $certificateTemplate->background_image_path)) {
                Storage::delete('public/' . $certificateTemplate->background_image_path);
            }
            
            $image = $request->file('background_image');
            $imagePath = $image->store('certificates/templates/backgrounds', 'public');
            $validated['background_image_path'] = $imagePath;
        }

        unset($validated['background_image']);
        $certificateTemplate->update($validated);

        return response()->json($certificateTemplate);
    }

    /**
     * Remove the specified template.
     */
    public function destroy(CertificateTemplate $certificateTemplate)
    {
        $user = request()->user();
        
        // Cannot delete default templates
        if ($certificateTemplate->is_default) {
            return response()->json([
                'message' => 'Cannot delete default templates.'
            ], 403);
        }

        // Only allow deleting own templates
        if ($certificateTemplate->user_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized. You can only delete your own templates.'
            ], 403);
        }

        // Delete background image if exists
        if ($certificateTemplate->background_image_path && Storage::exists('public/' . $certificateTemplate->background_image_path)) {
            Storage::delete('public/' . $certificateTemplate->background_image_path);
        }

        $certificateTemplate->delete();

        return response()->json(['message' => 'Template deleted successfully']);
    }

    /**
     * Get default templates.
     */
    public function getDefaults()
    {
        $templates = CertificateTemplate::where('is_default', true)
            ->where('is_active', true)
            ->get();

        return response()->json($templates);
    }
}
