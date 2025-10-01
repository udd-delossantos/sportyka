<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyOperation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DailyOperationController extends Controller
{
    public function index()
    {
        $operations = DailyOperation::orderBy('date', 'desc')->get();
        $active = DailyOperation::where('status', 'open')->first();

        $operationPayments = [];
        $operationDetails = [];

        foreach ($operations as $operation) {
            $overallCash = 0;
            $overallGcash = 0;

            $courts = \App\Models\Court::all();
            foreach ($courts as $court) {
                // Sessions
                $sessions = \App\Models\GameSession::where('court_id', $court->id)
                    ->whereDate('created_at', $operation->date)
                    ->get();

                // Queues
                $queues = \App\Models\Queue::where('court_id', $court->id)
                    ->whereDate('created_at', $operation->date)
                    ->get();

                // Payments
                $payments = \App\Models\Payment::whereIn('game_session_id', $sessions->pluck('id'))->get();

                $cashTotal  = $payments->where('payment_method', 'cash')->sum('amount');
                $gcashTotal = $payments->where('payment_method', 'gcash')->sum('amount');

                // Queue payments
                $queueCashTotal  = $queues->whereNull('transaction_no')->sum('amount');
                $queueGcashTotal = $queues->whereNotNull('transaction_no')->sum('amount');

                // Confirmed bookings for this court + operation date
                $confirmedBookingsGcash = \App\Models\Booking::where('court_id', $court->id)
                    ->whereDate('created_at', $operation->date) // use created_at, not updated_at
                    ->where('status', 'confirmed')
                    ->sum('amount');

                // Add everything
                $overallCash  += ($cashTotal + $queueCashTotal);
                $overallGcash += ($gcashTotal + $queueGcashTotal + $confirmedBookingsGcash);
            }

            $operationPayments[$operation->id] = $overallCash + $overallGcash;

            $operationDetails[$operation->id] = [
                'overallCash' => $overallCash,
                'overallGcash' => $overallGcash,
            ];
        }

        return view('admin.daily_operations.index', compact('operations', 'active', 'operationPayments', 'operationDetails'));
    }




    public function open(Request $request)
    {
        $today = now()->toDateString();

        // if already open today's operation exists, return
        $existing = DailyOperation::where('date', $today)->first();
        if ($existing && $existing->status === 'open') {
            return redirect()->back()->with('error', 'Today\'s operation is already open.');
        }

        // close any other open operations (safety)
        DailyOperation::where('status', 'open')->update([
            'status' => 'closed',
            'closed_at' => now(),
            'closed_by' => Auth::id(),
        ]);

        $op = DailyOperation::updateOrCreate(
            ['date' => $today],
            ['opened_by' => Auth::id(), 'opened_at' => now(), 'status' => 'open', 'closed_by' => null, 'closed_at' => null]
        );

        return redirect()->back()->with('success', 'Daily operation opened.');
    }

    public function close(Request $request, $id)
    {
        $op = DailyOperation::findOrFail($id);
        if ($op->status === 'closed') {
            return redirect()->back()->with('error', 'Operation is already closed.');
        }

        $op->update([
            'status' => 'closed',
            'closed_at' => now(),
            'closed_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Daily operation closed.');
    }

    /**
     * Reset: close current (if open) then open a new one for today (or a fresh one)
     */
    public function reset(Request $request)
    {
        // Close currently open
        $current = DailyOperation::where('status', 'open')->first();
        if ($current) {
            $current->update([
                'status' => 'closed',
                'closed_at' => now(),
                'closed_by' => Auth::id(),
            ]);
        }

        // Create new operation for today
        $new = DailyOperation::updateOrCreate(
            ['date' => now()->toDateString()],
            ['opened_by' => Auth::id(), 'opened_at' => now(), 'status' => 'open', 'closed_by' => null, 'closed_at' => null]
        );

        return redirect()->back()->with('success', 'System reset: new daily operation opened.');
    }


    public function reopen($id)
{
    // Find the operation (even if closed)
    $operation = DailyOperation::findOrFail($id);

    // Close any currently open day first (optional but recommended to avoid multiple opens)
    DailyOperation::where('status', 'open')->update(['status' => 'closed']);

    // Reopen this one
    $operation->status = 'open';
    $operation->save();

    return redirect()->back()->with('success', 'Daily operation re-opened successfully.');
}



    /**
     * Show details for an operation (sessions & payments)
     */
    public function show($id)
{
    $operation = \App\Models\DailyOperation::findOrFail($id);

    // Get courts
    $courts = \App\Models\Court::all();

    // Initialize arrays
    $report = [];
    $overallBookingCount = 0;
    $overallWalkinCount = 0;
    $overallCash = 0;
    $overallGcash = 0;

    $bookingCount = \App\Models\GameSession::where('session_type', 'booking')->whereDate('created_at', $operation->date)->count();
    $walkinCount  = \App\Models\GameSession::whereIn('session_type', ['walk-in', 'queue'])->whereDate('created_at', $operation->date)->count();



    $cashTotal  = \App\Models\Payment::where('payment_method', 'cash')->sum('amount');
    $gcashTotal = \App\Models\Payment::where('payment_method', 'gcash')->sum('amount');

    // Queue payments
    $queueCashTotal  = \App\Models\Queue::whereNull('transaction_no')->sum('amount');
    $queueGcashTotal = \App\Models\Queue::whereNotNull('transaction_no')->sum('amount');

    // Combine
    $totalCash  = $cashTotal + $queueCashTotal;
    $totalGcash = $gcashTotal + $queueGcashTotal;

    foreach ($courts as $court) {
    // Sessions for this court within this operation day
    $sessions = \App\Models\GameSession::where('court_id', $court->id)  
        ->whereDate('created_at', $operation->date)
        ->get();

    $queues = \App\Models\Queue::where('court_id', $court->id)  
        ->whereDate('created_at', $operation->date)
        ->get();

    $bookingCount = $sessions->where('session_type', 'booking')->count();
    $walkinCount  = $sessions->whereIn('session_type', ['walk-in', 'queue'])->count();

    // Payments for this court today
    $payments = \App\Models\Payment::whereIn('game_session_id', $sessions->pluck('id'))->get();

    $cashTotal  = $payments->where('payment_method', 'cash')->sum('amount');
    $gcashTotal = $payments->where('payment_method', 'gcash')->sum('amount');

    // Queue payments
    $queueCashTotal  = $queues->whereNull('transaction_no')->sum('amount');
    $queueGcashTotal = $queues->whereNotNull('transaction_no')->sum('amount');

    // ✅ Bookings confirmed today (calendar date, not operation date)
    $confirmedBookingsTotal = \App\Models\Booking::where('court_id', $court->id)
        ->whereDate('updated_at', Carbon::today()) // always today's date
        ->where('status', 'confirmed')
        ->sum('amount'); 


    // Combine
    $totalCash  = $cashTotal + $queueCashTotal;
    $totalGcash = $gcashTotal + $queueGcashTotal;
    $totalCount = $bookingCount + $walkinCount;
    $totalPayments = $totalCash + $totalGcash + $confirmedBookingsTotal; // ✅ include confirmed bookings

    // Add to report
    $report[] = [
        'court_name'               => $court->name,
        'booking_count'            => $bookingCount,
        'walkin_count'             => $walkinCount,
        'cash_total'               => $totalCash,
        'gcash_total'              => $totalGcash,
        'confirmed_bookings_total' => $confirmedBookingsTotal, // ✅ new field
        'total_count'              => $totalCount,
        'total_payments'           => $totalPayments,
    ];

    // Update overall
    $overallBookingCount += $bookingCount;
    $overallWalkinCount  += $walkinCount;
    $overallCash         += $totalCash;
    $overallGcash        += $totalGcash;
    $overallConfirmed    = ($overallConfirmed ?? 0) + $confirmedBookingsTotal; // ✅ overall confirmed sum
}

    $overallPayments = $overallCash + $overallGcash + $overallConfirmed;



    return view('admin.daily_operations.show', compact(
        'operation',
        'report',
        'overallBookingCount',
        'overallWalkinCount',
        'overallCash',
        'overallGcash',
        'overallPayments',
        'confirmedBookingsTotal', // ✅ now passed to blade
        'bookingCount',
        'walkinCount',
        'totalCash',
        'totalGcash'
    ));
}


}
