<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Partnership;
use App\Rules\NotDisposableEmail;
use App\Rules\ValidPhoneRegex;
use App\Models\User;
use App\Notifications\NewPartnershipProposal;

class PartnershipController
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company' => 'required|string|max:200',
            'email' => ['required', 'email:rfc,dns', 'max:180', new NotDisposableEmail],
            'phone' => ['required', 'string', 'max:30', new ValidPhoneRegex],
            'type' => 'required|string|in:client,technology,strategic,academic,sponsorship,other,partnership,investment,contract,consulting',
            'message' => 'required|string|max:2000',
        ]);

        $partnership = Partnership::create([
            'company' => $validated['company'],
            'contact_email' => $validated['email'],
            'contact_phone' => $validated['phone'],
            'type' => $validated['type'],
            'message' => $validated['message'],
            'ip_address' => $request->ip(),
            'status' => 'new'
        ]);

        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            $admin->notify(new NewPartnershipProposal($partnership));
        }

        return back()->with('success', 'Proposition envoyée avec succès.');
    }
}
