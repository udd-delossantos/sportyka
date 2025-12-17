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
        
         $now = Carbon::now();

        // Find all confirmed bookings that are already past their end_time
        $expiredBookings = Booking::where('status', 'confirmed')
            ->whereRaw("STR_TO_DATE(CONCAT(booking_date, ' ', end_time), '%Y-%m-%d %H:%i:%s') < ?", [$now])
            ->get();

        foreach ($expiredBookings as $expired) {
            $expired->status = 'voided';
            $expired->save();
        }
        // Confirmed bookings for calendar
        $bookings = Booking::with(['user', 'court'])
    ->orderBy('created_at', 'desc')
    ->get();


        $events = $bookings->map(function ($booking) {
            return [
                'title' => $booking->user->name . ' - ' . $booking->court->name,
                'start' => $booking->booking_date . 'T' . $booking->start_time,
                'end'   => $booking->booking_date . 'T' . $booking->end_time,
                'color' => match ($booking->status) {
                    'confirmed' => '#1cc88a',
                    'completed' => '#36b9cc',
                    'ongoing'   => '#4e73df',
                    'voided'    => '#e74a3b',
                }
            ];
        });

        $confirmedCount = Booking::where('status', 'confirmed')->count();
        $ongoingCount = Booking::where('status', 'ongoing')->count();

        $active = \App\Models\DailyOperation::where('status', 'open')->first();
        if (!$active){

            $completedTodayCount = 0;
            $voidedTodayCount = 0;
            $confirmedBookingsTotal = 0;
            $confirmedTodayCount = 0;

        }else{

            $completedTodayCount = Booking::where('status', 'completed')
            ->where('booking_date', Carbon::today())// always today's date
            ->count();

            $voidedTodayCount = Booking::where('status', 'voided')
            ->where('booking_date', Carbon::today()) // always today's date
            ->count();

            $confirmedTodayCount = Booking::whereIn('status', ['confirmed', 'ongoing'])
            ->where('booking_date', Carbon::today())
            ->count();


             $operation = \App\Models\DailyOperation::where('status', 'open')->first();

        $confirmedBookingsTotal = 0;

        if ($operation) {
            $confirmedBookingsTotal = Booking::where('status', 'confirmed')
                ->whereDate('created_at', $operation->date)
                ->sum('amount');
        }
        

        }

        return view('admin.bookings.index', compact('bookings', 'events', 'confirmedCount', 'ongoingCount', 'confirmedTodayCount', 'completedTodayCount', 'voidedTodayCount', 'confirmedBookingsTotal'));
    }

  
}
