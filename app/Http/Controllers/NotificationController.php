<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function read(Request $request, string $notification): RedirectResponse
    {
        $item = $request->user()->notifications()->findOrFail($notification);
        $item->markAsRead();
        $url = $item->data['url'] ?? route('dashboard');

        return redirect()->to($url);
    }
}
