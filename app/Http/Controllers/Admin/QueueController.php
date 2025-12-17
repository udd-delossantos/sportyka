<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Queue;
use Illuminate\Pagination\LengthAwarePaginator;


class QueueController extends Controller
{
    public function index()
    {
         $active = \App\Models\DailyOperation::where('status', 'open')->first();

        if (!$active) {
            // create empty paginators for each dataset
            $queues = collect();
            $waitingCount = 0;
            $calledCount = 0;
            $completedCount = 0;
            $skippedCount = 0;
            $queueCashCollected = 0;
            $queueGCashCollected = 0;
            $queueTotalCollected = 0;

        } else {
            $queues = Queue::with(['court', 'staff'])
            ->where('daily_operation_id', $active->id)
            ->latest()
            ->get(); // No get()

             // 💰 Queue collections (COMPLETED only)
            $queueCashCollected = Queue::where('daily_operation_id', $active->id)
                ->whereNull('transaction_no')
                ->sum('amount');

            $queueGCashCollected = Queue::where('daily_operation_id', $active->id)
                ->whereNotNull('transaction_no')
                ->sum('amount');

            $queueTotalCollected = $queueCashCollected + $queueGCashCollected;

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

    return view('admin.queues.index', compact('queues',
    'waitingCount',
    'calledCount',
    'completedCount',
    'skippedCount', 
    'queueCashCollected', 
    'queueGCashCollected', 
    'queueTotalCollected'));
    }
}
