<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Court;

class DashboardController extends Controller
{
      public function index()
    {
        // Fetch all courts
        $courts = Court::all();

        // Pass courts to the customer dashboard view
        return view('customer.dashboard', compact('courts'));
    }

      public function show(Court $court)
    {
        return view('customer.courts.show', compact('court'));
    }

}
