<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class DashboardController extends Controller
{
    public function index()
{


    $operation = \App\Models\DailyOperation::where('status', 'open')->first();

    $active = \App\Models\DailyOperation::where('status', 'open')->first();

    // Initialize variables
    $report = [];
    $overallBookingCount = 0;
    $overallWalkinCount = 0;
    $overallCash = 0;
    $overallGcash = 0;
    $overallPayments = 0;
    $confirmedBookingCount = 0;



    if ($active) {
        $courts = \App\Models\Court::all();

        foreach ($courts as $court) {

            $sessions = \App\Models\GameSession::where('court_id', $court->id)
                ->whereDate('created_at', $active->date)
                ->get();

            $queues = \App\Models\Queue::where('court_id', $court->id)
                ->whereDate('created_at', $active->date)
                ->get();

            $bookingCount = $sessions->where('session_type', 'booking')->count();
            $walkinCount  = $sessions->whereIn('session_type', ['walk-in', 'queue'])->count();

            $confirmedBookingCount =  \App\Models\Booking::where('booking_date')
            ->where('daily_operation_id', $active->id)
            ->whereIn('status', ['confirmed','voided','completed']) // adjust if your column name differs
            ->count();

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
            $totalCount = $bookingCount + $walkinCount;
            $totalPayments = $totalCash + $totalGcash;

            // Add to report
            $report[] = [
                'court_name'     => $court->name,
                'booking_count'  => $bookingCount,
                'walkin_count'   => $walkinCount,
                'cash_total'     => $totalCash,
                'gcash_total'    => $totalGcash,
                'total_count'    => $totalCount,
                'total_payments' => $totalPayments,
            ];

            // Update overall
            $overallBookingCount += $bookingCount;
            $overallWalkinCount  += $walkinCount;
            $overallCash         += $totalCash;
            $overallGcash        += $totalGcash;
        }

        $overallPayments = $overallCash + $overallGcash;
    }else{
         

        
        
    }

    

    return view('staff.dashboard', compact(
        'active',
        'operation',
        'report',
        'overallBookingCount',
        'overallWalkinCount',
        'overallCash',
        'overallGcash',
        'overallPayments',
        'confirmedBookingCount'
      
    ));
}

}
