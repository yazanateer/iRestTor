<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class BusinessController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Businesses/Index', [
            'businesses' => Business::latest()->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Businesses/Create');
    }

    public function store(Request $request)
    {
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'slug' => ['nullable', 'string', 'max:255', 'unique:businesses,slug'],
        'phone' => ['nullable', 'string', 'max:255'],
        'email' => ['nullable', 'email', 'max:255'],
        'address' => ['nullable', 'string', 'max:255'],
        'timezone' => ['required', 'string', 'max:255'],
        'is_active' => ['boolean'],
    ]);

    $validated['slug'] = filled($validated['slug'] ?? null)
        ? Str::slug($validated['slug'])
        : Str::slug($validated['name']);

    Business::create($validated);

    return redirect()
        ->route('admin.businesses.index')
        ->with('success', 'Business created successfully.');
    }

    public function edit(Business $business)
    {
        $business->load('branding');
        return Inertia::render('Admin/Businesses/Edit', [
            'business' => $business,
        ]);
    }

    public function update(Request $request, Business $business)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:businesses,slug,' . $business->id],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'timezone' => ['required', 'string', 'max:255'],
            'is_active' => ['boolean'],
            'primary_color' => ['required', 'string', 'max:20'],
            'secondary_color' => ['required', 'string', 'max:20'],
            'accent_color' => ['required', 'string', 'max:20'],
            'public_title' => ['nullable', 'string', 'max:255'],
            'public_subtitle' => ['nullable', 'string', 'max:255'],
            'public_description' => ['nullable', 'string'],
            'theme_style' => ['required', 'string', 'max:50'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'cover_image' => ['nullable', 'image', 'max:4096'],

        ]);

        $business->update($validated);

        $logoPath = $business->branding?->logo_path;
        $coverImagePath = $business->branding?->cover_image_path;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('business-branding/logos', 'public');
        }
        if ($request->hasFile('cover_image')) {
            $coverImagePath = $request->file('cover_image')->store('business-branding/covers', 'public');
        }

        $business->branding()->updateOrCreate(
        ['business_id' => $business->id],
        [
            'primary_color' => $validated['primary_color'],
            'secondary_color' => $validated['secondary_color'],
            'accent_color' => $validated['accent_color'],
            'public_title' => $validated['public_title'] ?? null,
            'public_subtitle' => $validated['public_subtitle'] ?? null,
            'public_description' => $validated['public_description'] ?? null,
            'theme_style' => $validated['theme_style'],
            'logo_path' => $logoPath,
            'cover_image_path' => $coverImagePath,
        ]
        );

        return redirect()
            ->route('admin.businesses.index')
            ->with('success', 'Business updated successfully.');
    }

    public function destroy(Business $business)
    {

         $business->delete();
         return redirect()
            ->route('admin.businesses.index')
            ->with('success', 'Business deleted successfully.');
    }


}
