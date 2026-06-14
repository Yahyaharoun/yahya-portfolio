<?php

namespace App\Http\Controllers\Admin;

use App\Models\Certification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CertificationController
{
    public function index()
    {
        return Inertia::render('Admin/Certifications/Index', [
            'items' => Certification::latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'organization' => 'required|string|max:255',
            'issued_at' => 'required|date',
            'image' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
        ]);

        $data = [
            'title' => $validated['title'],
            'organization' => $validated['organization'],
            'issued_at' => $validated['issued_at'],
            'description' => $validated['description'] ?? null,
        ];

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('certifications', 'public');
        }

        Certification::create($data);

        return back()->with('success', 'Certification ajoutée avec succès.');
    }

    public function update(Request $request, Certification $certification)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'organization' => 'required|string|max:255',
            'issued_at' => 'required|date',
            'image' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
        ]);

        $data = [
            'title' => $validated['title'],
            'organization' => $validated['organization'],
            'issued_at' => $validated['issued_at'],
            'description' => $validated['description'] ?? null,
        ];

        if ($request->hasFile('image')) {
            if ($certification->image_path) {
                Storage::disk('public')->delete($certification->image_path);
            }
            $data['image_path'] = $request->file('image')->store('certifications', 'public');
        }

        $certification->update($data);

        return back()->with('success', 'Certification mise à jour avec succès.');
    }

    public function destroy(Certification $certification)
    {
        if ($certification->image_path) {
            Storage::disk('public')->delete($certification->image_path);
        }
        $certification->delete();
        return back()->with('success', 'Certification supprimée.');
    }
}
