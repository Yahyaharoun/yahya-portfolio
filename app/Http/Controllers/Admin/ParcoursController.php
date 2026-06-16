<?php

namespace App\Http\Controllers\Admin;

use App\Models\Parcours;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class ParcoursController
{
    public function index()
    {
        return Inertia::render('Admin/Parcours/Index', [
            'parcours' => Parcours::latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'localisation' => 'required|string|max:255',
            'nom_proprietaire' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
            'lien' => 'nullable|url|max:255',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('parcours', 'public');
        }

        Parcours::create($validated);

        return redirect()->back();
    }

    public function update(Request $request, Parcours $parcour)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'localisation' => 'required|string|max:255',
            'nom_proprietaire' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
            'lien' => 'nullable|url|max:255',
        ]);

        if ($request->hasFile('image')) {
            if ($parcour->image) {
                Storage::disk('public')->delete($parcour->image);
            }
            $validated['image'] = $request->file('image')->store('parcours', 'public');
        }

        $parcour->update($validated);

        return redirect()->back();
    }

    public function destroy(Parcours $parcour)
    {
        if ($parcour->image) {
            Storage::disk('public')->delete($parcour->image);
        }
        $parcour->delete();

        return redirect()->back();
    }
}
