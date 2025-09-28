<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DailyOperation;
use App\Models\Court;
use App\Models\GameSession;
use App\Models\Payment;
use App\Models\Queue;
use App\Models\Booking; 
use Carbon\Carbon;
use DB;

class DashboardController extends Controller
{
    public function index(Request $request)
{
    // Picked month or default to current
    $month = $request->input('month', Carbon::now()->format('Y-m'));
    $startDate = Carbon::parse($month . '-01')->startOfMonth();
    $endDate   = Carbon::parse($month . '-01')->endOfMonth();

    // Initialize totals
    $monthlyCash = 0;
    $monthlyGcash = 0;
    $monthlySessionCount = 0;
    $monthlyBookingCount = 0;
    $monthlyWalkinCount = 0;
    $allBookingCount = 0;
    $confirmedBookingCount = 0; // ✅ new
    $monthlySessionsCount = 0;

    $courts = Court::all();
    foreach ($courts as $court) {
        // Sessions in this month
        $sessions = GameSession::where('court_id', $court->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $queues = Queue::where('court_id', $court->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        // Booking & Walk-in counts
        $monthlySessionCount += $sessions->whereIn('session_type', ['walk-in', 'queue', 'booking'])->count();
        $monthlyBookingCount += $sessions->where('session_type', 'booking')->count();
        $monthlyWalkinCount  += $sessions->whereIn('session_type', ['walk-in', 'queue'])->count();

        // All bookings for the selected month (any status)
        $allBookingCount = Booking::whereMonth('booking_date', $startDate->month)
            ->whereYear('booking_date', $startDate->year)
            ->count();

        // ✅ Confirmed bookings only
        $confirmedBookingCount = Booking::whereMonth('booking_date', $startDate->month)
            ->whereYear('booking_date', $startDate->year)
            ->where('status', 'confirmed') // adjust if your column name differs
            ->count();

        // Payments
        $payments = Payment::whereIn('game_session_id', $sessions->pluck('id'))->get();
        $cashTotal  = $payments->where('payment_method', 'cash')->sum('amount');
        $gcashTotal = $payments->where('payment_method', 'gcash')->sum('amount');

        // Queue payments
        $queueCashTotal  = $queues->whereNull('transaction_no')->sum('amount');
        $queueGcashTotal = $queues->whereNotNull('transaction_no')->sum('amount');

        $monthlyCash  += ($cashTotal + $queueCashTotal);
        $monthlyGcash += ($gcashTotal + $queueGcashTotal);
    }

    $monthlyEarnings = $monthlyCash + $monthlyGcash;

    // Earnings per court (Donut Chart) including queues
    $earningsPerCourt = Court::all()->mapWithKeys(function ($court) use ($startDate, $endDate) {
        // Payments linked to sessions of this court
        $sessionPayments = Payment::join('game_sessions', 'payments.game_session_id', '=', 'game_sessions.id')
            ->where('game_sessions.court_id', $court->id)
            ->whereBetween('payments.created_at', [$startDate, $endDate])
            ->sum('payments.amount');

        // Queue earnings for this court
        $queueEarnings = Queue::where('court_id', $court->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        return [$court->name => $sessionPayments + $queueEarnings];
    });

    // Weekly Earnings (Line Chart) - include queues + payments
    $weeksInMonth = ceil($startDate->daysInMonth / 7);
    $weeklyEarnings = collect();

    for ($week = 1; $week <= $weeksInMonth; $week++) {
        $weekStart = $startDate->copy()->addDays(($week - 1) * 7);
        $weekEnd   = $weekStart->copy()->addDays(6);

        // Clamp inside the month
        if ($weekStart < $startDate) $weekStart = $startDate;
        if ($weekEnd > $endDate) $weekEnd = $endDate;

        // Payments
        $paymentTotal = Payment::whereBetween('created_at', [$weekStart, $weekEnd])->sum('amount');

        // Queues
        $queueTotal = Queue::whereBetween('created_at', [$weekStart, $weekEnd])->sum('amount');

        $weeklyEarnings->put("Week $week", $paymentTotal + $queueTotal);
    }

    return view('admin.dashboard', compact(
        'month',
        'monthlyEarnings',
        'monthlyCash',
        'monthlyGcash',
        'monthlySessionCount',
        'monthlyBookingCount',
        'monthlyWalkinCount',
        'allBookingCount',
        'confirmedBookingCount', // ✅ pass to view
        'earningsPerCourt',
        'weeklyEarnings'
    ));
}

    public function printReport(Request $request)
{
    $month = $request->get('month', now()->format('Y-m'));

    // Reuse the same logic as your dashboard
    $monthlyEarnings = $this->getMonthlyEarnings($month);
    $monthlyCash = $this->getMonthlyCash($month);
    $monthlyGcash = $this->getMonthlyGcash($month);
    $monthlySessionCount = $this->getMonthlySessionCount($month);
    $monthlyWalkinCount = $this->getMonthlyWalkinCount($month);
    $monthlyBookingCount = $this->getMonthlyBookingCount($month);
    $allBookingCount = $this->getAllBookingCount($month);

    return view('admin.reports.print_report', compact(
        'month',
        'monthlyEarnings',
        'monthlyCash',
        'monthlyGcash',
        'monthlySessionCount',
        'monthlyWalkinCount',
        'monthlyBookingCount',
        'allBookingCount'
    ));
}

}
