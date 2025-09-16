<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\BookingRequest;
use App\Models\Court;
use App\Models\DailyOperation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon; 



class BookingRequestController extends Controller
{
    public function index()
    {
        $bookingRequests = BookingRequest::where('user_id', Auth::id())->latest()->paginate(5);
        return view('customer.booking_requests.index', compact('bookingRequests'));
    }

   public function create()
{
    $courts = \App\Models\Court::all();

    // Fetch confirmed + pending bookings from BOTH Booking & BookingRequest
    $confirmedBookings = \App\Models\Booking::whereIn('status', ['confirmed', 'pending'])->get();
    $pendingRequests = \App\Models\BookingRequest::whereIn('status', ['confirmed', 'pending'])->get();

    // Merge collections
    $allBookings = $confirmedBookings->concat($pendingRequests)->map(function ($booking) {
        return [
            'court_id'   => $booking->court_id,
            'date'       => \Carbon\Carbon::parse($booking->booking_date)->toDateString(),
            'start_time' => \Carbon\Carbon::parse($booking->start_time)->format('H:i'),
            'end_time'   => \Carbon\Carbon::parse($booking->end_time)->format('H:i'),
            'status'     => $booking->status,
        ];
    });

    return view('customer.booking_requests.create', compact('courts', 'allBookings'));
}


    public function store(Request $request)
{
    $today = now()->toDateString();
    $operation = DailyOperation::where('date', $today)->where('status', 'open')->first();


    $validated = $request->validate([
        'court_id' => 'required|exists:courts,id',
        'booking_date' => 'required|date|after_or_equal:today',
        'start_time' => 'required',
        'end_time' => 'required|after:start_time',
        'hours' => 'required|integer|min:0|max:23',
        'minutes' => 'required|integer|min:0|max:59',
        'transaction_no' => 'required|string|min:13|max:13'
    ]);

    // Check for conflicts in bookings OR requests
   $conflictInBookings = Booking::where('court_id', $request->court_id)
    ->where('booking_date', $request->booking_date)
    ->where('status', '!=', 'voided')
    ->where(function ($query) use ($request) {
        $query->where(function ($q) use ($request) {
            // Overlap happens only if start < new_end AND end > new_start
            $q->where('start_time', '<', $request->end_time)
              ->where('end_time', '>', $request->start_time);
        });
    })
    ->exists();

$conflictInRequests = BookingRequest::where('court_id', $request->court_id)
    ->where('booking_date', $request->booking_date)
    ->where('status', 'pending')
    ->where(function ($query) use ($request) {
        $query->where(function ($q) use ($request) {
            $q->where('start_time', '<', $request->end_time)
              ->where('end_time', '>', $request->start_time);
        });
    })
    ->exists();

if ($conflictInBookings || $conflictInRequests) {
    return back()->withInput()->with('error', 'This time slot is already reserved. Please select an available time slot.');
}


    // Compute amount (50% upfront)
    $validated = $request->validate([
    'court_id' => 'required|exists:courts,id',
    'booking_date' => 'required|date|after_or_equal:today',
    'start_time' => 'required',
    'end_time' => 'required|after:start_time',
    'hours' => 'required|integer|min:0|max:23',
    'minutes' => 'required|integer|min:0|max:59',
    'transaction_no' => 'required|string|size:13'
]);

    $court = Court::findOrFail($validated['court_id']);

    // Compute total duration in minutes
    $totalMins = ((int)$validated['hours'] * 60) + (int)$validated['minutes'];

    // Compute amount (50% downpayment)
    $ratePerMinute = $court->hourly_rate / 60;
    $totalAmount = round(($totalMins * $ratePerMinute) * 0.5, 2);

    BookingRequest::create([
        'user_id' => Auth::id(),
        'court_id' => $validated['court_id'],
        'daily_operation_id' => $operation->id,
        'booking_date' => $validated['booking_date'],
        'start_time' => $validated['start_time'],
        'end_time' => $validated['end_time'],
        'expected_hours' => (int) $validated['hours'],
        'expected_minutes' => (int) $validated['minutes'],
        'amount' => $totalAmount,
        'transaction_no' => $validated['transaction_no'],
        'status' => 'pending',
    ]);


        return redirect()->route('customer.booking_requests.create')->with('success', 'Booking request submitted and awaiting staff approval.');
    }



    public function edit(BookingRequest $bookingRequest)
{
    $courts = \App\Models\Court::all();

    $confirmedBookings = \App\Models\Booking::whereIn('status', ['confirmed', 'pending'])->get();
    $pendingRequests   = \App\Models\BookingRequest::whereIn('status', ['confirmed', 'pending'])->get();

    $allBookings = $confirmedBookings->concat($pendingRequests)->map(function ($booking) {
        return [
            'court_id'   => $booking->court_id,
            'date'       => \Carbon\Carbon::parse($booking->booking_date)->toDateString(),
            'start_time' => \Carbon\Carbon::parse($booking->start_time)->format('H:i'),
            'end_time'   => \Carbon\Carbon::parse($booking->end_time)->format('H:i'),
            'status'     => $booking->status,
        ];
    });

    return view('customer.booking_requests.edit', compact('bookingRequest', 'courts', 'allBookings'));
}

public function update(Request $request, $id)
{
    $bookingRequest = BookingRequest::findOrFail($id);

    $request->validate([
        'court_id' => 'required|exists:courts,id',
        'booking_date' => 'required|date',
        'start_time' => 'required',
        'end_time' => 'required|after:start_time',
        'transaction_no' => 'required|string|size:13',
    ]);


     // Check for conflicts in bookings OR requests
    $conflictInBookings = Booking::where('court_id', $request->court_id)
        ->where('booking_date', $request->booking_date)
        ->where(function ($query) use ($request) {
            $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                  ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                  ->orWhere(function ($q) use ($request) {
                      $q->where('start_time', '<=', $request->start_time)
                        ->where('end_time', '>=', $request->end_time);
                  });
        })
        ->where('status', '!=', 'voided')
        ->exists();

    $conflictInRequests = BookingRequest::where('court_id', $request->court_id)
        ->where('booking_date', $request->booking_date)
        ->where('status', 'pending')
        ->where(function ($query) use ($request) {
            $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                  ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                  ->orWhere(function ($q) use ($request) {
                      $q->where('start_time', '<=', $request->start_time)
                        ->where('end_time', '>=', $request->end_time);
                  });
        })
        ->exists();

    if ($conflictInBookings || $conflictInRequests) {
        return back()->withInput()->with('error', 'This time slot is already reserved. Please select an available time slot');
    }


      // If your form allows updating hours/minutes, recalc amount
    $court = Court::findOrFail($request->court_id);

    $hours = $request->input('hours', $bookingRequest->expected_hours);
    $minutes = $request->input('minutes', $bookingRequest->expected_minutes);
    $totalMins = ((int)$hours * 60) + (int)$minutes;

    $ratePerMinute = $court->hourly_rate / 60;
    $totalAmount = round(($totalMins * $ratePerMinute) * 0.5, 2);


    $bookingRequest->update([
        'court_id' => $request->court_id,
        'booking_date' => $request->booking_date,
        'start_time' => $request->start_time,
        'end_time' => $request->end_time,
        'expected_hours' => $hours,
        'expected_minutes' => $minutes,
        'amount' => $totalAmount,
        'transaction_no' => $request->transaction_no,
    ]);

    return redirect()->route('customer.booking_requests.index')
                     ->with('success', 'Booking request updated successfully.');
}




}