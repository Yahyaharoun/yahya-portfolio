<?php

namespace App\Http\Controllers\Admin;

use App\Models\Skill;
use App\Models\SkillCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class SkillController
{
    public function index()
    {
        return Inertia::render('Admin/Skills/Index', [
            'categories' => SkillCategory::with('skills')->orderBy('sort_order')->get(),
            'skills' => Skill::with('category')->latest()->get()
        ]);
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        SkillCategory::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'sort_order' => SkillCategory::max('sort_order') + 1,
        ]);

        return back()->with('success', 'Catégorie ajoutée.');
    }

    public function updateCategory(Request $request, SkillCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
        ]);

        return back()->with('success', 'Catégorie modifiée.');
    }

    public function destroyCategory(SkillCategory $category)
    {
        $category->delete();
        return back()->with('success', 'Catégorie supprimée.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'level' => 'required|string|in:beginner,intermediate,advanced,expert',
            'proficiency' => 'required|integer|min:0|max:100',
            'skill_category_id' => 'required|exists:skill_categories,id',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'tags' => 'nullable|string'
        ]);

        // Process tags from comma-separated string to array
        $tags = null;
        if (!empty($validated['tags'])) {
            $tags = array_values(array_filter(array_map('trim', explode(',', $validated['tags']))));
        }

        Skill::create([
            'name' => $validated['name'],
            'level' => $validated['level'],
            'proficiency' => $validated['proficiency'],
            'skill_category_id' => $validated['skill_category_id'],
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? null,
            'tags' => $tags,
        ]);

        return back()->with('success', 'Compétence ajoutée avec succès.');
    }

    public function update(Request $request, Skill $skill)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'level' => 'required|string|in:beginner,intermediate,advanced,expert',
            'proficiency' => 'required|integer|min:0|max:100',
            'skill_category_id' => 'required|exists:skill_categories,id',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'tags' => 'nullable|string'
        ]);

        // Process tags from comma-separated string to array
        $tags = null;
        if (!empty($validated['tags'])) {
            $tags = array_values(array_filter(array_map('trim', explode(',', $validated['tags']))));
        }

        $skill->update([
            'name' => $validated['name'],
            'level' => $validated['level'],
            'proficiency' => $validated['proficiency'],
            'skill_category_id' => $validated['skill_category_id'],
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? null,
            'tags' => $tags,
        ]);

        return back()->with('success', 'Compétence mise à jour avec succès.');
    }

    public function destroy(Skill $skill)
    {
        $skill->delete();
        return back()->with('success', 'Compétence supprimée.');
    }
}
