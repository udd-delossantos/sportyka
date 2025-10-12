<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
