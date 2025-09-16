<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingRequest;
use App\Models\GameSession;
use App\Models\DailyOperation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BookingRequestController extends Controller
{
    public function index()
    {

        $active = DailyOperation::where('status', 'open')->first();

        if (!$active){
            $requests = collect();
            $requestCount = 0;
            $pendingCount = 0;
            $approvedCount = 0;
            $cancelledCount = 0;

        }else{
            $requests = BookingRequest::with(['user', 'court'])
            ->where('daily_operation_id', $active->id)
            ->latest()
            ->get();

            $requestCount = BookingRequest::with(['user', 'court'])
            ->where('daily_operation_id', $active->id)
            ->count();

            $pendingCount = BookingRequest::where('status', 'pending')
            ->where('daily_operation_id', $active->id)
            ->count();

            $approvedCount = BookingRequest::where('status', 'approved')
            ->where('daily_operation_id', $active->id)
            ->count();

            $cancelledCount = BookingRequest::where('status', 'cancelled')
            ->where('daily_operation_id', $active->id)
            ->count();
        }

        return view('staff.booking_requests.index', compact('requests', 'requestCount', 'pendingCount', 'approvedCount', 'cancelledCount'));
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
            'daily_operation_id'=> $request->daily_operation_id,
            'booking_date'      => $request->booking_date,
            'start_time'        => $request->start_time,
            'end_time'          => $request->end_time,
            'expected_hours'    => $request->expected_hours,
            'expected_minutes'  => $request->expected_minutes,
            'amount'            => $request->amount,
            'transaction_no'    => $request->transaction_no,
            'status'            => 'confirmed',
            
        ]);


        return redirect()->back()->with('success', 'Booking approved and confirmed.');
    }


    public function cancel($id) { 
        $request = BookingRequest::findOrFail($id); 
        $request->status = 'cancelled'; 
        $request->save(); 
        return redirect()->back()->with('success', 'Booking cancelled successfully.'); 
    }

    
}


