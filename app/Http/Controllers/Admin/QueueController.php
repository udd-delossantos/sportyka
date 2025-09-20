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

        } else {
            $queues = Queue::with(['court', 'staff'])
            ->where('daily_operation_id', $active->id)
            ->latest()
            ->get(); // No get()

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

    return view('admin.queues.index', compact('queues','waitingCount','calledCount','completedCount','skippedCount'));
    }
}
