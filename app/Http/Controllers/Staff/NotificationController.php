<?php

namespace App\Http\Controllers\Staff;


use App\Http\Controllers\Controller;
use App\Models\BookingRequest;

class NotificationController extends Controller
{
    public function getNotifications()
    {
        $count = BookingRequest::where('status', 'pending')->count();

        return response()->json([
            'count' => $count
        ]);
    }
}
