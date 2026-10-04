<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function read(Request $request, string $notification): JsonResponse|RedirectResponse
    {
        $item = $request->user()->notifications()->findOrFail($notification);
        $item->markAsRead();

        if ($request->expectsJson()) {
            return response()->json([
                'read' => true,
                'unread_count' => $request->user()->unreadNotifications()->count(),
            ]);
        }

        return redirect()->route('dashboard');
    }
}
