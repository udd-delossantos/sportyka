<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GameSession;
use App\Models\BookingRequest;
use App\Models\Booking;

use App\Models\Court;


class BookingRequestController extends Controller
{
     public function index()
    {


       
            $requests = BookingRequest::with(['user', 'court'])
            ->latest()
            ->get();

            $courts = Court::all();



            $requestCount = BookingRequest::with(['user', 'court'])
            ->count();

            $pendingCount = BookingRequest::where('status', 'pending')
            ->count();

            $approvedCount = BookingRequest::where('status', 'approved')
            ->count();

            $cancelledCount = BookingRequest::where('status', 'cancelled')
            ->count();
        

        return view('admin.booking_requests.index', compact('requests', 'courts', 'requestCount', 'pendingCount', 'approvedCount', 'cancelledCount'));
    }
}

