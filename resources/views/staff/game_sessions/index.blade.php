@extends('layouts.staff.app') 
@section('title', 'Sporty Ka') 
@section('content')

<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Sessions</h1>
        <a href="{{ route('staff.game_sessions.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50 mr-2"></i> Create Walk-in Session
        </a>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Courts Available</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $availCourtsCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-basketball-ball fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Ongoing Sessions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $sessionCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-running fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Bookings</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $bookingCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Queues</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $queuesCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h5 class="m-0 font-weight-bold text-primary">Active Sessions Monitoring</h5>
        </div>
        <div class="card-body">
            
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            @endif

            <div class="row">
                @php $activeSessions = $sessions->whereIn('status', ['pending', 'ongoing']); @endphp 
                @forelse($activeSessions as $session)
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <div class="card shadow-sm h-100 border-bottom-{{ $session->status === 'ongoing' ? 'success' : 'warning' }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="font-weight-bold text-gray-800 mb-0">{{ $session->court->name }}</h5>
                                <span class="badge badge-{{ $session->status === 'ongoing' ? 'success' : 'warning' }}">
                                    {{ ucfirst($session->status) }}
                                </span>
                            </div>
                            
                            <hr class="sidebar-divider my-2">

                            <div class="small text-gray-800 mb-3">
                                <div class="mb-1"><strong>Customer:</strong> {{ $session->customer_name }}</div>
                                <div class="mb-1"><strong>Type:</strong> {{ ucfirst($session->session_type) }}</div>
                                <div class="mb-1"><strong>Duration:</strong> {{ $session->expected_hours }}h {{ $session->expected_minutes }}m</div>
                                <div class="mb-1"><strong>End Time:</strong> {{ $session->end_time ? \Carbon\Carbon::parse($session->end_time)->format('h:i A') : '—' }}</div>
                            </div>

                            <div class="text-center mb-2">
                                @if($session->status === 'ongoing')
                                    <h4 id="timer-{{ $session->id }}" class="font-weight-bold text-primary mb-1">--:--:--</h4>
                                    <div class="progress progress-sm mb-2">
                                        <div id="progress-bar-{{ $session->id }}" class="progress-bar bg-primary" role="progressbar" style="width: 100%; transition: width 1s linear;"></div>
                                    </div>
                                @else
                                    <h4 class="text-gray-400 mb-3">—</h4>
                                @endif
                            </div>

                            <div class="mt-auto">
                                @if($session->status === 'pending')
                                <div class="d-flex justify-content-between gap-2">
                                    <form class="mb-0 flex-fill mr-1" method="POST" action="{{ route('staff.game_sessions.start', $session->id) }}">
                                        @csrf
                                        <button class="btn btn-success btn-sm btn-block">Start</button>
                                    </form>

                                    <form class="mb-0 flex-fill ml-1" method="POST" action="{{ route('staff.game_sessions.destroy', $session->id) }}" onsubmit="return confirm('Are you sure you want to delete this session?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm btn-block">Delete</button>
                                    </form>
                                </div>
                                @elseif($session->status === 'ongoing')
                                    <form id="end-form-{{ $session->id }}" class="mb-0" method="POST" action="{{ route('staff.game_sessions.end', $session->id) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm btn-block end-session-btn">
                                            <i class="fas fa-stop-circle mr-1"></i> End Session
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <p class="text-gray-500 mb-0">No active sessions running.</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4 h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Today's Bookings</h6>
                    <select id="bookingCourtFilter" class="custom-select custom-select-sm shadow-none w-auto" style="border-color: #e3e6f0;">
                        <option value="">All Courts @if($totalBookingCount > 0)({{ $totalBookingCount }})@endif</option>
                        @foreach($courts as $court)
                            @php $count = $bookingCountByCourt[$court->id] ?? 0; @endphp
                            <option value="{{ $court->name }}">{{ $court->name }} @if($count > 0)({{ $count }})@endif</option>
                        @endforeach
                    </select>
                </div>
                
                @php
                    use Carbon\Carbon;
                    $now = Carbon::now();
                    $closestBookingsByCourt = [];
                    foreach ($courts as $court) {
                        $courtBookings = $bookings->where('court_id', $court->id)->where('status', 'confirmed');
                        $closest = $courtBookings->sortBy(function($b) use ($now) {
                            return abs(Carbon::parse($b->start_time)->diffInMinutes($now, false));
                        })->first();
                        if ($closest) { $closestBookingsByCourt[$court->id] = $closest->id; }
                    }
                @endphp

                <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                    <div id="bookingList">
                        @forelse($bookings as $booking)
                            <div class="card mb-3 border-left-primary booking-card" data-court="{{ $booking->court->name }}">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <div class="font-weight-bold text-gray-800">{{ $booking->user->name }}</div>
                                            <div class="text-xs text-primary text-uppercase font-weight-bold">{{ $booking->court->name }}</div>
                                        </div>
                                        <span class="badge badge-success">{{ ucfirst($booking->status) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between small text-gray-600 mb-2">
                                        <span>{{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}</span>
                                        <span class="font-weight-bold">{{ $booking->expected_hours }}h {{ $booking->expected_minutes }}m</span>
                                    </div>
                                    
                                    @if($booking->status === 'confirmed' && isset($closestBookingsByCourt[$booking->court_id]) && $closestBookingsByCourt[$booking->court_id] === $booking->id)
                                        <form class="mb-0" method="POST" action="{{ route('staff.bookings.startSession', $booking->id) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-primary btn-sm btn-block start-booking-btn">Start Session</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @empty
                            @endforelse
                        
                        <div id="noBookingsMessage" class="text-center py-4" style="{{ $bookings->count() > 0 ? 'display:none;' : '' }}">
                            <p class="text-gray-500 mb-0">No Confirmed Bookings Today.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4 h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Customers in Queue</h6>
                    <select id="courtSort" class="custom-select custom-select-sm shadow-none w-auto" style="border-color: #e3e6f0;">
                        <option value="all">All Courts @if($totalQueueCount > 0)({{ $totalQueueCount }})@endif</option>
                        @foreach($courts as $court)
                            @php $count = $queueCountByCourt[$court->id] ?? 0; @endphp
                            <option value="{{ $court->name }}">{{ $court->name }} @if($count > 0)({{ $count }})@endif</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                    <div id="queueList">
                        @forelse($queues as $queue)
                            <div class="card mb-3 border-left-primary queue-item" data-court="{{ $queue->court->name }}">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <div class="font-weight-bold text-gray-800">
                                                <span class="text-danger mr-1">#{{ $queue->queue_number }}</span> {{ $queue->customer_name }}
                                            </div>
                                            <div class="text-xs text-primary text-uppercase font-weight-bold">{{ $queue->court->name }}</div>
                                        </div>
                                        <span class="badge badge-warning">{{ ucfirst($queue->status) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between small text-gray-600 mb-2">
                                        <span>{{ \Carbon\Carbon::parse($queue->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($queue->end_time)->format('h:i A') }}</span>
                                        <span class="font-weight-bold">{{ $queue->expected_hours }}h {{ $queue->expected_minutes }}m</span>
                                    </div>

                                    @if($queue->queue_number == 1)
                                        <form class="mb-0" action="{{ route('staff.queues.call', $queue->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-primary btn-sm btn-block start-queue-btn">Start Session</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @empty
                             @endforelse

                        <div id="noQueuesMessage" class="text-center py-4" style="{{ $queues->count() > 0 ? 'display:none;' : '' }}">
                            <p class="text-gray-500 mb-0">No Customers in Queue.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h5 class="m-0 font-weight-bold text-primary">Session History</h5>
            <div class="btn-group" role="group">
                <button id="exportCsv" class="btn btn-sm btn-info shadow-sm"><i class="fas fa-file-csv fa-sm text-white-50"></i> CSV</button>
                <button id="exportExcel" class="btn btn-sm btn-success shadow-sm"><i class="fas fa-file-excel fa-sm text-white-50"></i> Excel</button>
                <button id="printTable" class="btn btn-sm btn-secondary shadow-sm"><i class="fas fa-print fa-sm text-white-50"></i> Print</button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="completedSessionsTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>Customer</th>
                            <th>Court</th>
                            <th>Type</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Duration</th>
                            <th>Bill</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($completedSessions as $session)
                        <tr>
                            <td class="font-weight-bold text-gray-800">{{ $session->customer_name }}</td>
                            <td>{{ $session->court->name }}</td>
                            <td>{{ ucfirst($session->session_type) }}</td>
                            <td>{{ \Carbon\Carbon::parse($session->start_time)->format('h:i A') }}</td>
                            <td>{{ \Carbon\Carbon::parse($session->end_time)->format('h:i A') }}</td>
                            <td>
                                @php $start = \Carbon\Carbon::parse($session->start_time); $end = \Carbon\Carbon::parse($session->end_time); $diff = $start->diff($end); @endphp 
                                {{ $diff->h }}h {{ $diff->i }}m
                            </td>
                            <td class="font-weight-bold text-success">₱{{ number_format($session->amount_paid, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
// EXACT COPY OF YOUR ORIGINAL SCRIPT LOGIC
document.addEventListener("DOMContentLoaded", function () {
    // Booking filter
    const bookingFilter = document.getElementById('bookingCourtFilter');
    const bookingCards = document.querySelectorAll('.booking-card'); // Note: Make sure .booking-card class exists in HTML

    // Restore filter state if exists
    let savedBookingCourt = localStorage.getItem('bookingCourt');
    if (savedBookingCourt) {
        bookingFilter.value = savedBookingCourt;
        filterBookings(savedBookingCourt);
    }

    bookingFilter.addEventListener('change', function () {
        let selectedCourt = this.value; // removed toLowerCase to match select values exactly
        localStorage.setItem('bookingCourt', this.value); // save state
        filterBookings(selectedCourt);
    });

    function filterBookings(selectedCourt) {
        let visibleCount = 0;
        // Logic fix: ensure case insensitivity or exact match based on your data
        let searchCourt = selectedCourt.toLowerCase();

        document.querySelectorAll('.booking-card').forEach(card => {
            let court = card.dataset.court.toLowerCase();
            if (selectedCourt === "" || court === searchCourt) {
                card.style.display = "block"; // or "flex" depending on layout, block is safe for div
                visibleCount++;
            } else {
                card.style.display = "none";
            }
        });

        // Toggle empty message
        const emptyMsg = document.getElementById('noBookingsMessage');
        if (emptyMsg) {
            emptyMsg.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    }

    // Queue filter
    const courtSort = document.getElementById("courtSort");
    // const queueItems = document.querySelectorAll(".queue-item"); // (not strictly needed here if we use querySelectorAll inside function)

    // Restore queue filter state if exists
    let savedQueueCourt = localStorage.getItem('queueCourt');
    if (savedQueueCourt) {
        courtSort.value = savedQueueCourt;
        filterQueues(savedQueueCourt);
    }

    courtSort.addEventListener("change", function () {
        const selectedCourt = this.value;
        localStorage.setItem('queueCourt', this.value); // save state
        filterQueues(selectedCourt);
    });

    function filterQueues(selectedCourt) {
        let visibleCount = 0;

        document.querySelectorAll('#queueList .queue-item').forEach(item => {
            const court = item.dataset.court;
            if (selectedCourt === "all" || court === selectedCourt) {
                item.style.display = "block";
                visibleCount++;
            } else {
                item.style.display = "none";
            }
        });

        // Toggle empty message
        const emptyMsg = document.getElementById('noQueuesMessage');
        if (emptyMsg) {
            emptyMsg.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    }
});

// Timers for active sessions
document.addEventListener('DOMContentLoaded', function () {
    @foreach ($activeSessions as $session)
        @if ($session->status === 'ongoing' && $session->start_time)
            (function () {
                const sessionId = {{ $session->id }};
                const timerEl = document.getElementById('timer-{{ $session->id }}');
                const progressBar = document.getElementById('progress-bar-{{ $session->id }}');

                const startTime = new Date("{{ \Carbon\Carbon::parse($session->start_time)->format('Y-m-d H:i:s') }}".replace(' ', 'T'));
                const durationMs = ({{ $session->expected_hours }} * 60 + {{ $session->expected_minutes }}) * 60 * 1000;
                const endTime = startTime.getTime() + durationMs;

                function updateTimer() {
                    const now = new Date().getTime();
                    const remaining = endTime - now;

                    if (remaining <= 0) {
                        if(timerEl) timerEl.innerText = '00:00:00';
                        if (progressBar) progressBar.style.width = '0%';
                        // Ensure the form exists before submitting
                        const endForm = document.getElementById('end-form-{{ $session->id }}');
                        if(endForm) endForm.submit();
                        return;
                    }

                    const hrs = Math.floor((remaining / (1000 * 60 * 60)) % 24);
                    const mins = Math.floor((remaining / (1000 * 60)) % 60);
                    const secs = Math.floor((remaining / 1000) % 60);

                    if(timerEl) timerEl.innerText = `${hrs.toString().padStart(2, '0')}:${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;

                    // Progress bar width calculation
                    if (progressBar) {
                        const percentRemaining = (remaining / durationMs) * 100;
                        progressBar.style.width = `${percentRemaining}%`;
                    }
                }

                updateTimer();
                setInterval(updateTimer, 1000);
            })();
        @endif
    @endforeach
});

// Confirmation Dialogs
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".end-session-btn").forEach(btn => {
        btn.addEventListener("click", function (e) {
            e.preventDefault();
            if (confirm("Are you sure you want to end this session?")) {
                this.closest("form").submit();
            }
        });
    });

    document.querySelectorAll(".start-queue-btn").forEach(btn => {
        btn.addEventListener("click", function (e) {
            e.preventDefault();
            if (confirm("Are you sure you want to start this queue?")) {
                this.closest("form").submit();
            }
        });
    });

    document.querySelectorAll(".start-booking-btn").forEach(btn => {
        btn.addEventListener("click", function (e) {
            e.preventDefault();
            if (confirm("Are you sure you want to start this session?")) {
                this.closest("form").submit();
            }
        });
    });
});

// DataTable
$(document).ready(function () {
    var table = $('#completedSessionsTable').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        order: [],
        // SB Admin 2 style DataTables usually rely on the bootstrap integration 
        // but keeping your DOM configuration is fine for custom buttons placement.
        dom:
            '<"top d-flex justify-content-between align-items-center mb-2"lf>rt' +
            '<"bottom d-flex justify-content-between align-items-center"ip>',
        buttons: [
            {
                extend: 'csvHtml5',
                title: 'Completed Sessions',
                exportOptions: { columns: ':visible' }
            },
            {
                extend: 'excelHtml5',
                title: 'Completed Sessions',
                exportOptions: { columns: ':visible' }
            },
            {
                extend: 'print',
                title: 'Completed Sessions',
                exportOptions: { columns: ':visible' }
            }
        ]
    });

    // External buttons
    $('#exportCsv').on('click', function () {
        table.button(0).trigger();
    });
    $('#exportExcel').on('click', function () {
        table.button(1).trigger();
    });
    $('#printTable').on('click', function () {
        table.button(2).trigger();
    });
});
</script>
@endpush