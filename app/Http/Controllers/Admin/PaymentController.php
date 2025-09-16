<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Pagination\LengthAwarePaginator;


class PaymentController extends Controller
{
    public function index()
    {

        $active = \App\Models\DailyOperation::where('status', 'open')->first();

        if (!$active) {
            // create empty paginators for each dataset
            $payments = new LengthAwarePaginator([], 0, 5);
        }
        else{
            
            $payments = Payment::with(['session', 'staff'])
            ->where('daily_operation_id', $active->id)
            ->latest()
            ->get();


        }

       
        return view('admin.payments.index', compact('payments'));
    }
}
