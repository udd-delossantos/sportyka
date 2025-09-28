<?php


namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GameSession;
use App\Models\Booking;
use App\Models\Court;
use App\Models\DailyOperation;
use App\Models\Queue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;


class GameSessionController extends Controller
{
     public function index()
    {

        $this->sweepExpiredSessions();


        $active = \App\Models\DailyOperation::where('status', 'open')->first();

        if (!$active) {
            // create empty paginators for each dataset
            $sessions = collect();
            $completedSessions = collect();
            $bookings = collect();
            $queues = collect();
            $courts = collect();

            $bookingCount = 0;
            $queuesCount = 0;
            $sessionCount = 0;
            $availCourtsCount = 0;
            

        } else {
            $sessions = GameSession::with('court')
                ->where('staff_id', Auth::id())
                ->where('daily_operation_id', $active->id) // filter to current day
                ->get();

            $completedSessions = GameSession::with('court')
                ->where('staff_id', Auth::id())
                ->where('daily_operation_id', $active->id)
                ->where('status', 'completed')
                ->latest()
                ->get();

            $bookings = Booking::with('court', 'user')
                ->where('booking_date', now()->toDateString())
                ->where('status', 'confirmed')
                ->orderBy('start_time')
                ->get();

            $queues = Queue::with('court')
                ->where('status', 'waiting')
                ->where('daily_operation_id', $active->id)
                ->get();

            $courts = Court::all();


            $bookingCount = Booking::where('booking_date', now()->toDateString())
            ->where('status', 'confirmed')
            ->count();

            $queuesCount = Queue::where('status', 'waiting')
            ->where('daily_operation_id', $active->id)
            ->count();

            $sessionCount = GameSession::where('status','ongoing')
            ->where('daily_operation_id', $active->id)
            ->count();

            $availCourtsCount = Court::where('status','available')
            ->count();

        }

        return view('staff.game_sessions.index', compact('sessions', 'completedSessions', 'bookings', 'queues', 'courts', 'bookingCount', 'queuesCount', 'sessionCount', 'availCourtsCount'));

    }

    public function create()
    {
        $courts = Court::all();
        return view('staff.game_sessions.create', compact('courts'));
    }

    public function store(Request $request)
{
    $operation = DailyOperation::where('date', now()->toDateString())
        ->where('status', 'open')
        ->first();

    if (!$operation) {
        return redirect()->back()->with('error', 'You must contact the admin to open today\'s operation first.');
    }
 

    $validated = $request->validate([
        'court_id' => 'required|exists:courts,id',
        'customer_name' => 'required|string|max:255',
        'hours' => 'required|integer|min:0|max:23',
        'minutes' => 'required|integer|min:0|max:59',
    ]);

    $hours = (int) $validated['hours'];
    $minutes = (int) $validated['minutes'];

    $startTime = now();
    $endTime = $startTime->copy()->addHours($hours)->addMinutes($minutes);

    // Check for confirmed booking conflicts
    $hasBookingConflict = Booking::where('court_id', $validated['court_id'])
        ->where('status', 'confirmed')
        ->whereDate('booking_date', now()->toDateString())
        ->where(function ($query) use ($startTime, $endTime) {
            $query->whereBetween('start_time', [$startTime, $endTime])
                  ->orWhereBetween('end_time', [$startTime, $endTime])
                  ->orWhere(function ($inner) use ($startTime, $endTime) {
                      $inner->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                  });
        })
        ->exists();

    if ($hasBookingConflict) {
        return redirect()->back()->with('error', 'Cannot create session - this time overlaps with a confirmed booking.');
    }

    // Check for queue conflicts
    $hasQueueConflict = Queue::where('court_id', $validated['court_id'])
        ->where('status', 'waiting')
        ->whereDate('created_at', now()->toDateString())
        ->where(function ($query) use ($startTime, $endTime) {
            $query->whereBetween('start_time', [$startTime, $endTime])
                  ->orWhereBetween('end_time', [$startTime, $endTime])
                  ->orWhere(function ($inner) use ($startTime, $endTime) {
                      $inner->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                  });
        })
        ->exists();

    if ($hasQueueConflict) {
        return redirect()->back()->with('error', 'Cannot create session - this time overlaps with an existing queue.');
    }

    //  Check if court is in use
    $isCourtInUse = GameSession::where('court_id', $validated['court_id'])
        ->whereIn('status', ['pending', 'ongoing'])
        ->where('session_date', now()->toDateString())
        ->exists();

    if ($isCourtInUse) {
        return redirect()->back()->with('error', 'Cannot create session - court is already in use for an active or pending session.');
    }

    // Create session
    GameSession::create([
        'staff_id' => Auth::id(),
        'court_id' => $validated['court_id'],
        'daily_operation_id' => $operation->id,
        'session_date' => now()->toDateString(),
        'customer_name' => $validated['customer_name'],
        'session_type' => 'walk-in',
        'expected_hours' => $hours,
        'expected_minutes' => $minutes,
        'status' => 'pending',
    ]);

    Court::where('id', $validated['court_id'])->update(['status' => 'in-use']);

    return redirect()->route('staff.game_sessions.index')->with('success', 'Session created.');
}





    public function start($id)
    {
        $session = GameSession::findOrFail($id);
        if ($session->status === 'pending') {
            $session->start_time = now();
            $session->status = 'ongoing';
            $session->end_time = now()
                ->addHours((int) $session->expected_hours)
                ->addMinutes((int) $session->expected_minutes);
            $session->save();
        }

        if ($session->status === 'ongoing' && $session->end_time <= now()) {
            $session->status = 'completed';
            $session->save();

        Court::where('id', $session->court_id)->update(['status' => 'in-use']);
        }
        
        return redirect()->back()->with('success', 'Session started.');
    }



    private function sweepExpiredSessions(): void
    {
        // Find all sessions that should already be finished
        $expired = \App\Models\GameSession::with(['court'])
            ->whereIn('session_type',['walk-in','booking','queue'])
            ->where('status', 'ongoing')
            ->where('end_time', '<=', now())
            ->get();

        foreach ($expired as $s) {
            // (Optional) compute amount if you charge automatically
            if ($s->court && !$s->amount_paid && $s->start_time) {
                $minutes = \Carbon\Carbon::parse($s->start_time)->diffInMinutes($s->end_time);
                $hours   = (int) ceil($minutes / 60);
                $rate    = $s->court->hourly_rate;
                $finalRate = in_array($s->session_type, ['booking','queue']) ? ($rate / 2) : $rate;
                $s->amount_paid = $hours * $finalRate;
            }

            $s->status = 'completed';
            $s->save();

            Court::where('id', $session->court_id)->update(['status' => 'available']);

            
        }
    }


    public function end($id)
    {

        
        $session = GameSession::with('court')->findOrFail($id);
        if ($session->status === 'ongoing') {
           $start = Carbon::parse($session->start_time);
        $end = now();
        $rate = $session->court->hourly_rate;

        // Convert expected_duration (HH:MM) into minutes
        if (strpos($session->expected_duration, ':') !== false) {
            [$hoursPart, $minutesPart] = explode(':', $session->expected_duration);
            $expectedMinutes = ((int)$hoursPart * 60) + (int)$minutesPart;
        } else {
            // fallback if it's already numeric (minutes)
            $expectedMinutes = (int)$session->expected_duration;
        }

        // Convert to fractional hours
        $hours = $expectedMinutes / 60;

        // Round UP to the nearest 0.5 hr
        $hours = ceil($hours * 2) / 2;

        // Compute base charge
        $sessionRate = $rate * $hours;

        // If booking or queue, only charge 50%
        $finalRate = in_array($session->session_type, ['booking', 'queue']) 
            ? ($sessionRate / 2) 
            : $sessionRate;

        // Round to whole peso
        $finalRate = round($finalRate, 0);

        // Update session
        $session->end_time = $end;
        $session->amount_paid = $finalRate;
        $session->status = 'completed';
        $session->save();

            Court::where('id', $session->court_id)->update(['status' => 'available']);

            // ✅ If session came from a booking, update booking status too
            if ($session->session_type === 'booking' && $session->booking_id) {
                \App\Models\Booking::where('id', $session->booking_id)
                    ->update(['status' => 'completed']);
            }

            if ($session->session_type === 'queue' && $session->queue_id) {
                \App\Models\Queue::where('id', $session->queue_id)
                ->update(['status' => 'completed']);
            }

        }

        
        return redirect()->back()->with('success', 'Session ended and billed.');
    }

    public function destroy($id)
    {
        $session = GameSession::where('status', 'pending')->where('staff_id', Auth::id())->findOrFail($id);
        $session->delete();

        return redirect()->back()->with('success', 'Pending session deleted.');
    }

}
