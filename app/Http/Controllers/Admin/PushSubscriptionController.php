<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PushSubscriptionController
{
    /**
     * Store a new push subscription for the authenticated admin.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'endpoint' => 'required|string',
            'keys.auth' => 'required|string',
            'keys.p256dh' => 'required|string',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->updatePushSubscription(
            $validated['endpoint'],
            $validated['keys']['p256dh'],
            $validated['keys']['auth']
        );

        return response()->json(['message' => 'Subscription successful.']);
    }

    /**
     * Delete a push subscription.
     */
    public function destroy(Request $request)
    {
        $validated = $request->validate([
            'endpoint' => 'required|string',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->deletePushSubscription($validated['endpoint']);

        return response()->json(['message' => 'Subscription removed.']);
    }
}
