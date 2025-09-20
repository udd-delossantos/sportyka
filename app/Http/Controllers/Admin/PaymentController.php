<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Queue;
use App\Models\GameSession;
use App\Models\DailyOperation;

class PaymentController extends Controller
{
    public function index()
    {
        $active = DailyOperation::where('status', 'open')->first();

        if (!$active) {
            // No active operation → return empty values
            $payments = collect();
            $totalCash = 0;
            $totalGCash = 0;
            $totalCollected = 0;
            $unsettledCount = 0;
        } else {
            // Get all payments for the active operation
            $payments = Payment::with(['session', 'staff'])
                ->where('daily_operation_id', $active->id)
                ->latest()
                ->get();

            // 1. Cash payments (include queue where transaction_no is empty)
            $cashPayments = Payment::where('payment_method', 'cash')
                ->where('daily_operation_id', $active->id)
                ->sum('amount');

            $cashFromQueue = Queue::whereNull('transaction_no')
                ->where('daily_operation_id', $active->id)
                ->sum('amount');

            $totalCash = $cashPayments + $cashFromQueue;

            // 2. GCash payments (include queue where transaction_no is not empty)
            $gcashPayments = Payment::where('payment_method', 'gcash')
                ->where('daily_operation_id', $active->id)
                ->sum('amount');

            $gcashFromQueue = Queue::whereNotNull('transaction_no')
                ->where('daily_operation_id', $active->id)
                ->sum('amount');

            $totalGCash = $gcashPayments + $gcashFromQueue;

            // 3. Total amounts collected today
            $totalCollected = $totalCash + $totalGCash;

            // 4. Unsettled payments count (sessions completed but unpaid)
            $unsettledCount = GameSession::where('status', 'completed')
                ->where('daily_operation_id', $active->id)
                ->doesntHave('payment')
                ->count();
        }

        return view('admin.payments.index', compact(
            'payments',
            'totalCash',
            'totalGCash',
            'totalCollected',
            'unsettledCount'
        ));
    }
}
