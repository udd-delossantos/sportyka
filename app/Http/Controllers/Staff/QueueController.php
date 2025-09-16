<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Queue;
use App\Models\Court;
use App\Models\GameSession;
use App\Models\Booking;
use App\Models\DailyOperation;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class QueueController extends Controller
{
    public function index()
{
    $active = DailyOperation::where('status', 'open')->first();

    if (!$active) {
        $queues = collect();
        $waitingCount = 0;
        $calledCount = 0;
        $completedCount = 0;
        $skippedCount = 0;

    } else {

        $now = Carbon::now();

        $expiredQueues = Queue::where('daily_operation_id', $active->id)
        ->where('status', 'waiting')
        ->whereRaw("STR_TO_DATE(CONCAT(CURDATE(), ' ', end_time), '%Y-%m-%d %H:%i:%s') < ?", [$now])
        ->get();

        foreach ($expiredQueues as $expired) {
            $expired->status = 'skipped';
            $expired->queue_number = 0;
            $expired->save();

            
            $this->renumberQueues($expired->court_id, $active->id);
        }


        $queues = Queue::with(['court', 'staff'])
        ->where('daily_operation_id', $active->id)
        ->orderBy('queue_number')
        ->get();

        $waitingCount = Queue::where('status', 'waiting')
        ->where('daily_operation_id', $active->id)
        ->count();


        $calledCount = Queue::where('status', 'called')
        ->where('daily_operation_id', $active->id)
        ->count();


        $completedCount = Queue::where('status', 'completed')
        ->where('daily_operation_id', $active->id)
        ->count();

        $skippedCount = Queue::where('status', 'skipped')
        ->where('daily_operation_id', $active->id)
        ->count();

    }

    return view('staff.queues.index', compact('queues','waitingCount','calledCount','completedCount','skippedCount'));
}


    public function create()
{
    $courts = Court::all();

    $active = DailyOperation::where('status', 'open')->first();

    // Get all waiting queues grouped by court id
    $queuesByCourt = \App\Models\Queue::where('status', 'waiting')
        ->where('daily_operation_id', $active->id)
        ->get()
        ->groupBy('court_id')
        ->map(function ($group) {
            return $group->map(function ($q) {
                return [
                    'id'            => $q->id,
                    'customer'      => $q->customer_name ?? 'Guest',
                    'start_time'    => $q->start_time ? \Carbon\Carbon::parse($q->start_time)->format('h:i A') : null,
                    'end_time'      => $q->end_time ? \Carbon\Carbon::parse($q->end_time)->format('h:i A') : null,
                    'transaction_no'=> $q->transaction_no ?? null,
                    'amount'        => $q->amount ?? 0,
                ];
            })->values();
        });

    // Convert to array (so @json works nicely in blade)
    $queuesByCourt = $queuesByCourt->toArray();

    return view('staff.queues.create', compact('courts', 'queuesByCourt'));
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
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'transaction_no' => 'nullable|string|size:13'
        ]);

        // 🛑 Check conflicts
        $hasBookingConflict = Booking::where('court_id', $validated['court_id'])
            ->whereDate('start_time', now()->toDateString())
            ->where('status', 'confirmed')
            ->where(function ($query) use ($validated) {
                $query->where('start_time', '<', $validated['end_time'])
                      ->where('end_time', '>', $validated['start_time']);
            })
            ->exists();

        if ($hasBookingConflict) {
            return redirect()->back()->with('error', 'Cannot create queue - overlaps with a confirmed booking for this court today.');
        }

        $hasSessionConflict = GameSession::where('court_id', $validated['court_id'])
            ->whereDate('start_time', now()->toDateString())
            ->where('status', 'ongoing')
            ->whereIn('session_type', ['walk-in', 'booking', 'queue'])
            ->where(function ($query) use ($validated) {
                $query->where('start_time', '<=', $validated['end_time'])
                      ->where('end_time', '>=', $validated['start_time']);
            })
            ->exists();

        if ($hasSessionConflict) {
            return redirect()->back()->with('error', 'There is already an ongoing session for this court.');
        }

        $hasQueueConflict = Queue::where('court_id', $validated['court_id'])
            ->whereDate('start_time', now()->toDateString())
            ->where('status', 'waiting')
            ->where(function ($query) use ($validated) {
                $query->where('start_time', '<', $validated['end_time'])
                      ->where('end_time', '>', $validated['start_time']);
            })
            ->exists();

        if ($hasQueueConflict) {
            return redirect()->back()->with('error', 'Cannot create queue - overlaps with another waiting queue for this court.');
        }

        // 💰 Compute payment
        $hours   = (int) $validated['hours'];
        $minutes = (int) $validated['minutes'];
        $court   = Court::findOrFail($validated['court_id']);

        $totalMins = ($hours * 60) + $minutes;
        $ratePerMinute = $court->hourly_rate / 60;
        $totalAmount = round(($totalMins * $ratePerMinute) * 0.5, 2);

        // 📝 Save queue
        $queue = Queue::create([
            'daily_operation_id' => $operation->id,
            'staff_id' => auth()->id(),
            'court_id' => $validated['court_id'],
            'customer_name' => $validated['customer_name'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'expected_hours' => $hours,
            'expected_minutes' => $minutes,
            'amount' => $totalAmount,
            'transaction_no' => $validated['transaction_no'],
            'status' => 'waiting',
        ]);

        // Re-number waiting queues
        $this->renumberQueues($queue->court_id, $operation->id);

        return redirect()->route('staff.queues.index')->with('success', 'Customer added to queue.');
    }

    public function call($id)
    {
        $queue = Queue::findOrFail($id);

        $operation = DailyOperation::where('date', now()->toDateString())
            ->where('status', 'open')
            ->first();

        if (!$operation) {
            return redirect()->back()->with('error', 'Please start today\'s operation first.');
        }

        $isCourtInUse = GameSession::where('court_id', $queue->court_id)
            ->whereIn('status', ['pending', 'ongoing'])
            ->where('session_date', now()->toDateString())
            ->exists();

        if ($isCourtInUse) {
            return redirect()->back()->with('error', 'This court is still in use.');
        }

        $now = Carbon::now();
        $scheduledStart = Carbon::parse($queue->created_at->format('Y-m-d') . ' ' . $queue->start_time);
        $scheduledEnd   = Carbon::parse($queue->created_at->format('Y-m-d') . ' ' . $queue->end_time);

        if ($now->greaterThan($scheduledEnd)) {
            $queue->status = 'skipped';
            $queue->queue_number = 0;
            $queue->save();
            $this->renumberQueues($queue->court_id, $operation->id);
            return redirect()->back()->with('error', 'Queue was skipped due to late start.');
        }

        if ($now->lessThan($scheduledStart)) {
            $durationMinutes = $scheduledStart->diffInMinutes($scheduledEnd);
            $actualEnd = $now->copy()->addMinutes($durationMinutes);
        } else {
            $actualEnd = $scheduledEnd->copy();
        }

        $queue->start_time = $now->format('H:i:s');
        $queue->end_time   = $actualEnd->format('H:i:s');
        $queue->status = 'called';
        $queue->queue_number = 0;
        $queue->save();

        $remainingMinutes = $now->diffInMinutes($actualEnd);
        $hours = floor($remainingMinutes / 60);
        $minutes = $remainingMinutes % 60;

        $session = GameSession::create([
            'court_id' => $queue->court_id,
            'staff_id' => Auth::id(),
            'daily_operation_id' => $operation->id,
            'session_date' => now()->toDateString(),
            'customer_name' => $queue->customer_name,
            'expected_hours' => $hours,
            'expected_minutes' => $minutes,
            'start_time' => $now,
            'end_time' => $actualEnd,
            'status' => 'ongoing',
            'session_type' => 'queue',
            'queue_id' => $queue->id,
        ]);

        Court::where('id', $session->court_id)->update(['status' => 'in-use']);


        

        // Re-number after calling
        $this->renumberQueues($queue->court_id, $operation->id);

        return redirect()->route('staff.game_sessions.index')
            ->with('success', 'Session started for ' . $queue->customer_name);
    }

    public function skip($id)
    {
        $queue = Queue::findOrFail($id);
        $queue->status = 'skipped';
        $queue->queue_number = 0;
        $queue->save();

        // Re-number after skipping
        $this->renumberQueues($queue->court_id, $queue->daily_operation_id);

        return redirect()->back()->with('success', 'Queue skipped successfully.');
    }

    private function renumberQueues($courtId, $operationId)
    {
        $waitingQueues = Queue::where('court_id', $courtId)
            ->where('daily_operation_id', $operationId)
            ->where('status', 'waiting')
            ->orderBy('created_at')
            ->get();

        foreach ($waitingQueues as $index => $queue) {
            $queue->update(['queue_number' => $index + 1]);
        }
    }
}