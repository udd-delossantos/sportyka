<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Queue;
use App\Models\Booking;
use App\Models\GameSession;
use App\Models\DailyOperation;
use Illuminate\Support\Carbon;
class PaymentController extends Controller
{
   public function index()
{
    $active = \App\Models\DailyOperation::where('status', 'open')->first();

    if (!$active) {
        $payments = collect();
        $totalCash = 0;
        $totalGCash = 0;
        $totalCollected = 0;
        $unsettledCount = 0;
    } else {
        $payments = Payment::with(['session', 'staff'])
            ->where('daily_operation_id', $active->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Cash: Payment method 'cash' + queues with no transaction_no
        $totalCash = Payment::where('daily_operation_id', $active->id)
            ->where('payment_method', 'cash')
            ->sum('amount');

        // GCash: Payment method 'gcash' + queues with transaction_no + confirmed bookings today
        $totalGCash = Payment::where('daily_operation_id', $active->id)
            ->where('payment_method', 'gcash')
            ->sum('amount');
        // Total collected
        $totalCollected = $totalCash + $totalGCash;

        // Unsettled payments count
        $unsettledCount = GameSession::where('status', 'completed')
            ->where('daily_operation_id', $active->id)
            ->doesntHave('payment')
            ->count();
    }

    return view('admin.payments.index', compact('payments','totalCash', 'totalGCash', 'totalCollected', 'unsettledCount'));
}

}
