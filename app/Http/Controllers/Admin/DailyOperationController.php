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
        $operations = DailyOperation::orderByDesc('date')->get();
        $active = DailyOperation::where('status', 'open')->first();
        return view('admin.daily_operations.index', compact('operations', 'active'));
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

    // Combine
    $totalCash  = $cashTotal + $queueCashTotal;
    $totalGcash = $gcashTotal + $queueGcashTotal;
    $totalCount = $bookingCount + $walkinCount; // ✅ add combined count per court
    $totalPayments = $totalCash + $totalGcash;  // ✅ add total payments per court

    // Add to report
    $report[] = [
        'court_name'     => $court->name,
        'booking_count'  => $bookingCount,
        'walkin_count'   => $walkinCount,
        'cash_total'     => $totalCash,
        'gcash_total'    => $totalGcash,
        'total_count'    => $totalCount,     // ✅ now available in $row
        'total_payments' => $totalPayments,  // ✅ now available in $row
    ];

    // Update overall
    $overallBookingCount += $bookingCount;
    $overallWalkinCount  += $walkinCount;
    $overallCash         += $totalCash;
    $overallGcash        += $totalGcash;
}

$overallPayments = $overallCash + $overallGcash;

return view('admin.daily_operations.show', compact(
    'operation',
    'report',
    'overallBookingCount',
    'overallWalkinCount',
    'overallCash',
    'overallGcash',
    'overallPayments'
));
    }

}
