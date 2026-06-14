<?php

namespace App\Http\Controllers\Admin;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Str;

class ProjectController
{
    public function index()
    {
        return Inertia::render('Admin/Projects/Index', [
            'items' => Project::latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:in_progress,realized',
            'demo_url' => 'nullable|url',
            'logo' => 'nullable|image|max:2048',
        ]);

        $data = [
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']) . '-' . uniqid(),
            'description' => $validated['description'],
            'status' => $validated['status'],
            'demo_url' => $validated['demo_url'],
            'visibility' => 'public',
            'tech_stack' => json_encode([]),
            'screenshots' => json_encode([]),
        ];

        if ($request->hasFile('logo')) {
            $data['thumbnail'] = $request->file('logo')->store('projects', 'public');
        }

        Project::create($data);

        return back()->with('success', 'Projet ajouté avec succès.');
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:in_progress,realized',
            'demo_url' => 'nullable|url',
            'logo' => 'nullable|image|max:2048',
        ]);

        $data = [
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => $validated['status'],
            'demo_url' => $validated['demo_url'],
        ];

        if ($request->hasFile('logo')) {
            if ($project->thumbnail) {
                Storage::disk('public')->delete($project->thumbnail);
            }
            $data['thumbnail'] = $request->file('logo')->store('projects', 'public');
        }

        $project->update($data);

        return back()->with('success', 'Projet mis à jour avec succès.');
    }

    public function destroy(Project $project)
    {
        if ($project->thumbnail) {
            Storage::disk('public')->delete($project->thumbnail);
        }
        $project->delete();
        return back()->with('success', 'Projet supprimé.');
    }
}
