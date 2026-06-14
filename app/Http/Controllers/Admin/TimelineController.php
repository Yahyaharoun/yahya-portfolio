<?php

namespace App\Http\Controllers\Admin;

use App\Models\TimelineItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class TimelineController
{
    public function index()
    {
        return Inertia::render('Admin/Timeline/Index', [
            'items' => TimelineItem::orderBy('date_start', 'desc')->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'organization' => 'nullable|string|max:255',
            'type' => 'required|string|in:experience,education,achievement,diploma',
            'date_start' => 'nullable|date',
            'date_end' => 'nullable|date|after_or_equal:date_start',
            'description' => 'nullable|string',
            'is_current' => 'nullable|boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        $validated['is_current'] = $request->boolean('is_current');

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('timeline', 'public');
        }

        TimelineItem::create($validated);

        return back()->with('success', 'Élément ajouté avec succès.');
    }

    public function update(Request $request, TimelineItem $timeline)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'organization' => 'nullable|string|max:255',
            'type' => 'required|string|in:experience,education,achievement,diploma',
            'date_start' => 'nullable|date',
            'date_end' => 'nullable|date|after_or_equal:date_start',
            'description' => 'nullable|string',
            'is_current' => 'nullable|boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        $validated['is_current'] = $request->boolean('is_current');

        if ($request->hasFile('image')) {
            if ($timeline->image_path) {
                Storage::disk('public')->delete($timeline->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('timeline', 'public');
        }

        $timeline->update($validated);

        return back()->with('success', 'Élément mis à jour avec succès.');
    }

    public function destroy(TimelineItem $timeline)
    {
        if ($timeline->image_path) {
            Storage::disk('public')->delete($timeline->image_path);
        }
        $timeline->delete();
        return back()->with('success', 'Parcours supprimé.');
    }
}
