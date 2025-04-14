<?php

namespace App\Http\Controllers\Api\Portal\Admin;

use App\Http\Controllers\Controller;
use App\Models\Api\PortalBill;
use App\Models\Notification;
use App\Models\Portal;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = Notification::where('type', 'organizations')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'notifications' => $notifications,
        ]);
    }

    public function show($id)
    {
        $notification = Notification::find($id);

        if (!$notification) {
            return response()->json(['message' => 'Notification not found'], 404);
        }

        $bill = PortalBill::find($notification->sender_id);

        if (!$bill) {
            return response()->json(['message' => 'Related bill not found'], 404);
        }

        $portal = Portal::find($bill->portal_id);

        return response()->json([
            'notification' => $notification,
            'bill' => [
                'id' => $bill->id,
                'image_url' => asset('storage/' . $bill->bill),
                'owner of the organizer is' => $portal ? $portal->name : 'Unknown'
            ]
        ]);
    }
}
