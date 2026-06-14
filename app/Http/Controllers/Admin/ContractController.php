<?php

namespace App\Http\Controllers\Admin;

use App\Models\Partnership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ContractController
{
    public function index()
    {
        cache()->put('last_contracts_view', now());
        
        return Inertia::render('Admin/Contracts/Index', [
            'items' => Partnership::latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company' => 'required|string|max:255',
            'website' => 'nullable|url',
            'message' => 'required|string',
            'logo' => 'nullable|image|max:2048',
            'status' => 'nullable|in:new,in_progress,treated,rejected',
        ]);

        $data = [
            'company' => $validated['company'],
            'website' => $validated['website'],
            'message' => $validated['message'],
            'status' => $validated['status'] ?? 'new',
            'type' => 'other', // default for admin created
        ];

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('partnerships', 'public');
        }

        Partnership::create($data);

        return back()->with('success', 'Contrat ajouté avec succès.');
    }

    public function update(Request $request, Partnership $contract)
    {
        $validated = $request->validate([
            'company' => 'required|string|max:255',
            'website' => 'nullable|url',
            'message' => 'required|string',
            'logo' => 'nullable|image|max:2048',
            'status' => 'required|in:new,in_progress,treated,rejected',
        ]);

        $data = [
            'company' => $validated['company'],
            'website' => $validated['website'],
            'message' => $validated['message'],
            'status' => $validated['status'],
        ];

        if ($request->hasFile('logo')) {
            if ($contract->logo_path) {
                Storage::disk('public')->delete($contract->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('partnerships', 'public');
        }

        $contract->update($data);

        return back()->with('success', 'Contrat mis à jour avec succès.');
    }

    public function destroy(Partnership $contract)
    {
        if ($contract->logo_path) {
            Storage::disk('public')->delete($contract->logo_path);
        }
        $contract->delete();
        return back()->with('success', 'Contrat supprimé.');
    }
}
