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
            $sessions = collect();
            $completedSessions = 0;
            $ongoingSessions = 0;
            $pendingSessions = 0;
            $availCourtsCount = 0;

        } else {
            
            $sessions = GameSession::with('court')
            ->where('daily_operation_id', $active->id) // filter to current day
            ->latest()
            ->get();

            $completedSessions = GameSession::where('status', 'completed')
                ->where('daily_operation_id', $active->id)
                ->count();

            $ongoingSessions = GameSession::where('status', 'ongoing')
                ->where('daily_operation_id', $active->id)
                ->count();

            $pendingSessions = GameSession::where('status', 'pending')
                ->where('daily_operation_id', $active->id)
                ->count();

            $availCourtsCount = Court::where('status','available')
            ->count();

        }

        return view('admin.game_sessions.index', compact('sessions', 'completedSessions', 'ongoingSessions', 'pendingSessions', 'availCourtsCount', ));

    }
}
