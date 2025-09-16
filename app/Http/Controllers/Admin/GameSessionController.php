<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\GameSession;
use App\Models\Booking;
use App\Models\Court;
use App\Models\DailyOperation;
use App\Models\Queue;
use Illuminate\Pagination\LengthAwarePaginator;


class GameSessionController extends Controller
{
    public function index()
    {
        $active = \App\Models\DailyOperation::where('status', 'open')->first();

        if (!$active) {
            // create empty paginators for each dataset
            $sessions = new LengthAwarePaginator([], 0, 5);

        } else {
            
            $sessions = GameSession::with('court')
                ->where('daily_operation_id', $active->id) // filter to current day
                ->latest()
                ->get();

        }

        return view('admin.game_sessions.index', compact('sessions'));

    }
}
