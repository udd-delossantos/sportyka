<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Payment;
use App\Models\GameSession;
use App\Models\Queue;
use App\Models\BookingRequest;

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
        $confirmedBookingsTotal = 0;
        $bookingCount = 0;
        $walkinCount = 0;
        $totalCash = 0;
        $totalGcash = 0;
        $overallConfirmed = 0;
        $ongoingSessionCount = 0;
        $waitingQueuesCount = 0;
        $pendingBookingCount = 0;
        $pendingPaymentCount = 0;



        if ($active) {
            $courts = \App\Models\Court::all();


            $ongoingSessionCount = GameSession::where('status','ongoing')
            ->where('daily_operation_id', $active->id)
            ->count();

            $waitingQueuesCount = Queue::where('status', 'waiting')
            ->where('daily_operation_id', $active->id)
            ->count();

            $pendingBookingCount = BookingRequest::where('status', 'pending')
            ->count();

            $pendingPaymentCount = GameSession::where('status', 'completed')
            ->where('daily_operation_id', $active->id)
            ->doesntHave('payment')
            ->count();

            foreach ($courts as $court) {
                $sessions = \App\Models\GameSession::where('court_id', $court->id)
                    ->whereDate('created_at', $active->date)
                    ->get();

                $queues = \App\Models\Queue::where('court_id', $court->id)
                    ->whereDate('created_at', $active->date)
                    ->get();

                $bookingCount = $sessions->where('session_type', 'booking')->count();
                $walkinCount = $sessions->whereIn('session_type', ['walk-in', 'queue'])->count();

                $confirmedBookingCount = \App\Models\Booking::whereDate('updated_at', today())
                ->where('status', 'confirmed')
                    ->count();

                // Payments for this court today
                $payments = \App\Models\Payment::whereIn('game_session_id', $sessions->pluck('id'))->get();

                $cashTotal = $payments->where('payment_method', 'cash')->sum('amount');
                $gcashTotal = $payments->where('payment_method', 'gcash')->sum('amount');

                // Queue payments
                $queueCashTotal = $queues->whereNull('transaction_no')->sum('amount');
                $queueGcashTotal = $queues->whereNotNull('transaction_no')->sum('amount');

                // ✅ Bookings confirmed today (calendar date, not operation date)
                $confirmedBookingsTotal = \App\Models\Booking::where('court_id', $court->id)
                    ->whereDate('created_at', $operation->date) // ✅ business day
                    ->sum('amount');

                // Combine
                $totalCash = $cashTotal + $queueCashTotal;
                $totalGcash = $gcashTotal + $queueGcashTotal;
                $totalCount = $bookingCount + $walkinCount;
                $totalPayments = $totalCash + $totalGcash + $confirmedBookingsTotal; // ✅ include confirmed bookings

                // Add to report
                $report[] = [
                    'court_name' => $court->name,
                    'booking_count' => $bookingCount,
                    'walkin_count' => $walkinCount,
                    'cash_total' => $totalCash,
                    'gcash_total' => $totalGcash,
                    'confirmed_bookings_total' => $confirmedBookingsTotal, // ✅ new field
                    'total_count' => $totalCount,
                    'total_payments' => $totalPayments,
                ];

                // Update overall
                $overallBookingCount += $bookingCount;
                $overallWalkinCount += $walkinCount;
                $overallCash += $totalCash;
                $overallGcash += $totalGcash;
                $overallConfirmed = ($overallConfirmed ?? 0) + $confirmedBookingsTotal; // ✅ overall confirmed sum
            }

            $overallPayments = $overallCash + $overallGcash + $overallConfirmed;
        } else {
        }

        return view(
            'staff.dashboard',
            compact(
                'active',
                'operation',
                'report',
                'overallBookingCount',
                'overallWalkinCount',
                'overallCash',
                'overallGcash',
                'overallPayments',
                'confirmedBookingCount',
                'confirmedBookingsTotal',
                'bookingCount',
                'walkinCount',
                'totalCash',
                'totalGcash',
                'overallConfirmed',
                'ongoingSessionCount',
                'waitingQueuesCount',
                'pendingBookingCount',
                'pendingPaymentCount'
            )
        );
    }
}
