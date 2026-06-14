<?php

namespace App\Http\Controllers\Admin;

use App\Models\Diploma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class DiplomaController
{
    public function index()
    {
        return Inertia::render('Admin/Diplomas/Index', [
            'diplomas' => Diploma::orderBy('year', 'desc')->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'institution' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 10),
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('diplomas', 'public');
        }

        Diploma::create($validated);

        return back()->with('success', 'Diplôme ajouté avec succès.');
    }

    public function update(Request $request, Diploma $diploma)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'institution' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 10),
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('image')) {
            if ($diploma->image_path) {
                Storage::disk('public')->delete($diploma->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('diplomas', 'public');
        }

        $diploma->update($validated);

        return back()->with('success', 'Diplôme modifié avec succès.');
    }

    public function destroy(Diploma $diploma)
    {
        $diploma->delete();

        return back()->with('success', 'Diplôme supprimé avec succès.');
    }
}
