<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Court;
use App\Models\DailyOperation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::where('user_id', Auth::id())->latest()->get();
        return view('customer.bookings.index', compact('bookings'));
    }

    public function create()
    {
        $courts = Court::all();
        return view('customer.bookings.create', compact('courts'));
    }

    public function store(Request $request)
{
    $today = now()->toDateString();
    $operation = DailyOperation::where('date', $today)->where('status', 'open')->first();

    if (!$operation) {
        return back()->with('error', 'Booking not allowed. No open operation today.');
    }

    $validated = $request->validate([
        'court_id' => 'required|exists:courts,id',
        'booking_date' => 'required|date|after_or_equal:today',
        'start_time' => 'required',
        'end_time' => 'required|after:start_time',
        'expected_duration' => ['required', 'regex:/^([0-9]?[0-9]):[0-5][0-9]$/'], // HH:MM
        'transaction_no' => 'required|string|min:13|max:13'
    ]);

    // Check for double booking
    $conflict = Booking::where('court_id', $request->court_id)
        ->where('booking_date', $request->booking_date)
        ->where(function ($query) use ($request) {
            $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                  ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                  ->orWhere(function ($q) use ($request) {
                      $q->where('start_time', '<=', $request->start_time)
                        ->where('end_time', '>=', $request->end_time);
                  });
        })
        ->where('status', '!=', 'cancelled')
        ->exists();

    if ($conflict) {
        return back()->withInput()->with('error', 'This time slot is already booked.');
    }

    // Compute amount based on expected duration
    [$hours, $minutes] = explode(':', $validated['expected_duration']);
    $court = Court::findOrFail($request->court_id);
    $totalMins = ((int)$hours * 60) + (int)$minutes;
    $ratePerMinute = $court->hourly_rate / 60;
    $totalAmount = round(($totalMins * $ratePerMinute) * 0.5, 2); // 50% upfront

    Booking::create([
        'user_id' => Auth::id(),
        'court_id' => $request->court_id,
        'daily_operation_id' => $operation->id,
        'booking_date' => $request->booking_date,
        'start_time' => $request->start_time,
        'end_time' => $request->end_time,
        'expected_hours' => (int) $hours,
        'expected_minutes' => (int) $minutes,
        'amount' => $totalAmount,
        'transaction_no' => $validated['transaction_no'],
        'status' => 'pending',
    ]);

    return redirect()->route('customer.bookings.index')->with('success', 'Booking submitted.');
}

}
