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
        $bookings = Booking::with(['user', 'court'])->latest()->get();

        // Pending requests for staff review
        $requests = BookingRequest::with(['user', 'court'])->latest()->get();

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

       

            $completedTodayCount = 0;
            $voidedTodayCount = 0;


            $completedTodayCount = Booking::where('status', 'completed')
            ->whereDate('created_at', Carbon::today())
            ->count();

            $voidedTodayCount = Booking::where('status', 'voided')
            ->whereDate('created_at', Carbon::today())
            ->count();
        

        return view('staff.bookings.index', compact('bookings', 'requests', 'events', 'confirmedCount', 'ongoingCount', 'completedTodayCount', 'voidedTodayCount'));
    }

    public function approve($id)
    {
        $request = BookingRequest::findOrFail($id);

        // Move into bookings
        $booking = Booking::create([
            'user_id'           => $request->user_id,
            'court_id'          => $request->court_id,
            'booking_date'      => $request->booking_date,
            'start_time'        => $request->start_time,
            'end_time'          => $request->end_time,
            'expected_hours'    => $request->expected_hours,
            'expected_minutes'  => $request->expected_minutes,
            'amount'            => $request->amount,
            'transaction_no'    => $request->transaction_no,
            'status'            => 'confirmed',
            'staff_id'          => Auth::id(),
        ]);

        // Delete the request once approved
        $request->delete();

        return redirect()->back()->with('success', 'Booking approved and confirmed.');
    }

    public function cancel($id, Request $request)
    {
        if ($request->type === 'request') {
            // Cancel a pending request
            $req = BookingRequest::findOrFail($id);
            $req->delete();
            return redirect()->back()->with('success', 'Booking request cancelled.');
        } else {
            // Cancel an existing booking
            $booking = Booking::findOrFail($id);
            $booking->status = 'cancelled';
            $booking->save();
            return redirect()->back()->with('success', 'Booking cancelled successfully.');
        }
    }

    public function startSession($id)
    {
        $booking = Booking::with(['user', 'court'])->findOrFail($id);

        $operation = DailyOperation::where('date', now()->toDateString())
            ->where('status', 'open')
            ->first();

        if (!$operation) {
            return redirect()->back()->with('error', 'Please start today\'s operation first.');
        }

        $isCourtInUse = GameSession::where('court_id', $booking->court_id)
            ->whereIn('status', ['pending', 'ongoing'])
            ->where('session_date', now()->toDateString())
            ->exists();

        if ($isCourtInUse) {
            return redirect()->back()->with('error', 'This court is still in use.');
        }

        $now = Carbon::now();

        $scheduledStart = Carbon::parse("{$booking->booking_date} {$booking->start_time}");
        $scheduledEnd   = Carbon::parse("{$booking->booking_date} {$booking->end_time}");

        if ($now->greaterThan($scheduledEnd)) {
            $booking->status = 'voided';
            $booking->save();
            return redirect()->back()->with('error', 'Booking was voided because the end time was exceeded.');
        }

        if ($now->lessThan($scheduledStart)) {
            $durationMinutes = $scheduledStart->diffInMinutes($scheduledEnd);
            $actualEnd = $now->copy()->addMinutes($durationMinutes);
        } else {
            $actualEnd = $scheduledEnd->copy();
        }

        $remainingMinutes = $now->diffInMinutes($actualEnd);
        $hours = floor($remainingMinutes / 60);
        $minutes = $remainingMinutes % 60;

        $session = GameSession::create([
            'court_id'           => $booking->court_id,
            'staff_id'           => Auth::id(),
            'daily_operation_id' => $operation->id,
            'session_date'       => now()->toDateString(),
            'customer_name'      => $booking->user->name,
            'expected_hours'     => $hours,
            'expected_minutes'   => $minutes,
            'start_time'         => $now,
            'end_time'           => $actualEnd,
            'status'             => 'ongoing',
            'session_type'       => 'booking',
            'booking_id'         => $booking->id,
        ]);

        $booking->status = 'ongoing';
        $booking->save();

        $session = Court::where('id', $session->court_id)->update(['status' => 'in-use']);


        return redirect()->back()->with('success', 'Session started from booking.');
    }


    public function edit(Booking $booking)
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

    return view('staff.bookings.edit', compact('booking', 'courts', 'allBookings'));
}

public function update(Request $request, $id)
{
    $booking = Booking::findOrFail($id);

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

    $hours = $request->input('hours', $booking->expected_hours);
    $minutes = $request->input('minutes', $booking->expected_minutes);
    $totalMins = ((int)$hours * 60) + (int)$minutes;

    $ratePerMinute = $court->hourly_rate / 60;
    $totalAmount = round(($totalMins * $ratePerMinute) * 0.5, 2);


    $booking->update([
        'court_id' => $request->court_id,
        'booking_date' => $request->booking_date,
        'start_time' => $request->start_time,
        'end_time' => $request->end_time,
        'expected_hours' => $hours,
        'expected_minutes' => $minutes,
        'amount' => $totalAmount,
        'transaction_no' => $request->transaction_no,
    ]);

    return redirect()->route('staff.bookings.index')
                     ->with('success', 'Booking request updated successfully.');
}


    
}
