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
        $courts = Court::all();


       
            $requests = BookingRequest::with(['user', 'court'])
            ->where('status', 'pending')
            ->get();

            


            $processedRequests = BookingRequest::with(['user', 'court'])
                ->orderBy('created_at', 'desc')
                ->whereIn('status', ['approved','cancelled'])
                ->get();


            $requestCount = BookingRequest::with(['user', 'court'])
            ->whereDate('created_at', Carbon::today())
            ->count();

            $pendingCount = BookingRequest::where('status', 'pending')
            ->count();

            $approvedCount = BookingRequest::where('status', 'approved')
            ->whereDate('created_at', Carbon::today())
            ->count();

            $cancelledCount = BookingRequest::where('status', 'cancelled')
            ->whereDate('created_at', Carbon::today())
            ->count();
        

        return view('staff.booking_requests.index', compact('requests', 'processedRequests', 'courts', 'requestCount', 'pendingCount', 'approvedCount', 'cancelledCount'));
    }

    public function approve($id)
    {
        $request = BookingRequest::findOrFail($id);
        $request->status = 'approved'; 
        $request->staff_id = Auth::id(); // ✅ ADD THIS
        $request->save(); 

        // Move into bookings
        $booking = Booking::create([
            'user_id'           => $request->user_id,
            'staff_id'          => Auth::id(),
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
        $request->staff_id = Auth::id();
        $request->save(); 
        return redirect()->back()->with('success', 'Booking cancelled successfully.'); 
    }

    
}


