<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingRequest;
use App\Models\GameSession;
use App\Models\DailyOperation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Court;

use Carbon\Carbon;

use App\Mail\BookingApprovedMail;
use Illuminate\Support\Facades\Mail;

class BookingRequestController extends Controller
{
    public function index()
    {


       
            $requests = BookingRequest::with(['user', 'court'])
            ->where('status', 'pending')
            ->latest()
            ->get();

            $courts = Court::all();


            $processedRequests = BookingRequest::with(['user', 'court'])
            ->whereIn('status', ['approved', 'cancelled'])
            ->latest()
            ->get();

            $requestCount = BookingRequest::with(['user', 'court'])
            ->count();

            $pendingCount = BookingRequest::where('status', 'pending')
            ->count();

            $approvedCount = BookingRequest::where('status', 'approved')
            ->count();

            $cancelledCount = BookingRequest::where('status', 'cancelled')
            ->count();
        

        return view('staff.booking_requests.index', compact('requests', 'processedRequests', 'courts', 'requestCount', 'pendingCount', 'approvedCount', 'cancelledCount'));
    }

    public function approve($id)
    {
        $request = BookingRequest::findOrFail($id);
        $request->status = 'approved'; 
        $request->save(); 

        // Move into bookings
        $booking = Booking::create([
            'user_id'           => $request->user_id,
            'staff_id'          => auth()->id(),
            'court_id'          => $request->court_id,
            'booking_date'      => $request->booking_date,
            'start_time'        => $request->start_time,
            'end_time'          => $request->end_time,
            'expected_hours'    => $request->expected_hours,
            'expected_minutes'  => $request->expected_minutes,
            'amount'            => $request->amount,
            'transaction_no'    => $request->transaction_no,
            'status'            => 'confirmed',
            
        ]);

         // Send email to customer
            Mail::to($request->user->email)->send(new BookingApprovedMail($request));


        return redirect()->back()->with('success', 'Booking approved and confirmed.');
    }


    public function cancel($id) { 
        $request = BookingRequest::findOrFail($id); 
        $request->status = 'cancelled'; 
        $request->save(); 
        return redirect()->back()->with('success', 'Booking cancelled successfully.'); 
    }

    
}


