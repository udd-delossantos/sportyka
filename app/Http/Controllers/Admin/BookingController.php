<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\GameSession;
use App\Models\DailyOperation;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class BookingController extends Controller
{
     public function index()
    {
        
        $bookings = Booking::with(['user', 'court', 'staff'])
        ->latest()
        ->get();

        $events = $bookings->map(function ($booking) {
            return [
                'title' => $booking->user->name . ' - ' . $booking->court->name,
                'start' => $booking->booking_date . 'T' . $booking->start_time,
                'end'   => $booking->booking_date . 'T' . $booking->end_time,
                'color' => match ($booking->status) {
                    'confirmed' => '#1cc88a',
                    'completed'     => '#36b9cc', 
                    'ongoing'     => '#4e73df',
                    'voided'     => '#e74a3b',
                }
            ];
        });

        return view('admin.bookings.index', compact('bookings', 'events'));
    }

  
}
