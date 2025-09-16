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
            $queues = new LengthAwarePaginator([], 0, 2);

        } else {
            $queues = Queue::with(['court', 'staff'])
            ->where('daily_operation_id', $active->id)
            ->latest()
            ->get(); // No get()
            
        }

    return view('admin.queues.index', compact('queues'));
    }
}
