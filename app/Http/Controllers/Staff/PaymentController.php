<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\DailyOperation;
use App\Models\GameSession;
use App\Models\Queue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class PaymentController extends Controller
{
    public function index()
    {
        $active = \App\Models\DailyOperation::where('status', 'open')->first();

        if (!$active) {
            // create empty paginators for each dataset
            $payments = new LengthAwarePaginator([], 0, 5);
            $totalCash = 0;
            $totalGCash = 0;
            $totalCollected = 0;
            $unsettledCount = 0;
        

        } else {
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

        // 4. Unsettled payments count
        $unsettledCount = GameSession::where('status', 'completed')
        ->where('daily_operation_id', $active->id)
            ->doesntHave('payment')
            ->count();
           
        }


      
        
        return view('staff.payments.index', compact('payments','totalCash', 'totalGCash', 'totalCollected', 'unsettledCount'));
    }

    public function create()
    {

        $active = \App\Models\DailyOperation::where('status', 'open')->first();

        if (!$active) {
            // create empty paginators for each dataset
            $sessions = collect();
        

        } else {
            $sessions = GameSession::where('status', 'completed')
            ->where('daily_operation_id', $active->id)
            ->doesntHave('payment')
            ->get();
           
        }
        
        
        return view('staff.payments.create', compact('sessions'));
    }

    public function store(Request $request)
    {
        $operation = \App\Models\DailyOperation::where('status', 'open')->first();
        
        if (!$operation) {
            return redirect()->back()->with('error', 'You must contact the admin to open today\'s operation first.');

        }

        $validated = $request->validate([
            'game_session_id' => 'required|exists:game_sessions,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string|in:cash,gcash',
            'transaction_no' => 'required_if:payment_method,gcash|nullable|digits:13|unique:payments,transaction_no',
        ]);

        Payment::create([
            'game_session_id' => $validated['game_session_id'],
            'staff_id' => Auth::id(),
            'daily_operation_id' => $operation->id,
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'transaction_no' => $validated['payment_method'] === 'gcash' ? $validated['transaction_no'] : null,
        ]);

        return redirect()->route('staff.payments.index')->with('success', 'Payment recorded.');
    }

  

}
