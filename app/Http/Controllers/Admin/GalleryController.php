<?php

namespace App\Http\Controllers\Admin;

use App\Models\MediaGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class GalleryController
{
    public function index()
    {
        return Inertia::render('Admin/Gallery/Index', [
            'items' => MediaGallery::latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'required|string',
            'url' => 'nullable|url|max:400',
            'media' => 'required|file|mimes:jpeg,png,jpg,gif,mp4,webm,mov,avi|max:20480', // Allow image/video up to 20MB
        ]);

        $data = [
            'title' => $validated['title'] ?? null,
            'description' => $validated['description'],
            'url' => $validated['url'] ?? null,
        ];

        if ($request->hasFile('media')) {
            $file = $request->file('media');
            $mime = $file->getMimeType();
            $data['type'] = str_starts_with($mime, 'video') ? 'video' : 'photo';
            $data['filepath'] = $file->store('gallery', 'public');
        }

        MediaGallery::create($data);

        return back()->with('success', 'Média ajouté avec succès.');
    }

    public function update(Request $request, MediaGallery $gallery)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'required|string',
            'url' => 'nullable|url|max:400',
            'media' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,webm,mov,avi|max:20480',
        ]);

        $data = [
            'title' => $validated['title'] ?? null,
            'description' => $validated['description'],
            'url' => $validated['url'] ?? null,
        ];

        if ($request->hasFile('media')) {
            if ($gallery->filepath) {
                Storage::disk('public')->delete($gallery->filepath);
            }
            $file = $request->file('media');
            $mime = $file->getMimeType();
            $data['type'] = str_starts_with($mime, 'video') ? 'video' : 'photo';
            $data['filepath'] = $file->store('gallery', 'public');
        }

        $gallery->update($data);

        return back()->with('success', 'Média mis à jour avec succès.');
    }

    public function destroy(MediaGallery $gallery)
    {
        if ($gallery->filepath) {
            Storage::disk('public')->delete($gallery->filepath);
        }
        $gallery->delete();
        return back()->with('success', 'Média supprimé.');
    }
}
