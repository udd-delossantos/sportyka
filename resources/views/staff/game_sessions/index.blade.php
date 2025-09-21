@extends('layouts.staff.app') @section('content')
<div class="container-fluid">
    <div class="px-0">
        <div class="card-body d-flex justify-content-between align-items-center px-0 pt-0">
            <h2 class="mb-0 text-primary"><strong>Sessions</strong></h2>
        <div class="d-flex gap-3">
            <div class="card shadow-sm text-center p-2 mr-1 border-bottom-primary" style="min-width: 200px;">
                <h6 class="text-muted mb-1">Courts Available</h6>
                <h4 class="mb-0 text-primary">{{ $availCourtsCount }}</h4>
            </div>
            <div class="card shadow-sm text-center p-2 mr-1 border-bottom-success" style="min-width: 200px;">
                <h6 class="text-muted mb-1">Ongoing Sessions</h6>
                <h4 class="mb-0 text-success">{{ $sessionCount }}</h4>
            </div>
            <div class="card shadow-sm text-center p-2 mr-1 border-bottom-info" style="min-width: 200px;">
                <h6 class="text-muted mb-1">Bookings</h6>
                <h4 class="mb-0 text-info">{{ $bookingCount }}</h4>
            </div>
            <div class="card shadow-sm text-center p-2 border-bottom-danger" style="min-width: 200px;">
                <h6 class="text-muted mb-1">Queues</h6>
                <h4 class="mb-0 text-danger">{{ $queuesCount }}</h4>
            </div>
        </div>

    </div>
    <div class="card mb-4 shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><strong>Session Management</strong></h5>
            <a href="{{ route('staff.game_sessions.create') }}" class="btn btn-primary">Create Walk-in Session</a>
        </div>

        <div class="card-body justify-content-between align-items-center pb-0">
            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="row">
                @php $activeSessions = $sessions->whereIn('status', ['pending', 'ongoing']); @endphp @forelse($activeSessions as $session)
                <div class="col-md-3 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body pb-0">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="text-primary">
                                    <strong>{{ $session->court->name }}</strong>
                                </h5>
                                <span class="badge {{ $session->status === 'ongoing' ? 'bg-success' : 'bg-warning' }} mb-3 text-white">
                                    {{ ucfirst($session->status) }}
                                </span>
                            </div>

                            <p><strong>Customer: </strong>{{ $session->customer_name }}</p>
                            <p><strong>Type: </strong>{{ ucfirst($session->session_type) }}</p>
                            <p><strong>Expected Duration: </strong>{{ $session->expected_hours }}h {{ $session->expected_minutes }}m</p>
                            <p>
                                <strong>End Time: </strong>
                                {{ $session->end_time ? \Carbon\Carbon::parse($session->end_time)->format('h:i A') : '—' }}
                            </p>

                            <p class="mb-3 text-right">
                                @if($session->status === 'ongoing')
                                <span id="timer-{{ $session->id }}" class="fw-bold text-primary">--:--:--</span>
                                @else — @endif
                            </p>
                        </div>

                        <div class="card-footer bg-gray-100">
                            @if($session->status === 'pending')
                            <div class="d-flex justify-content-between">
                                <form class="mb-0" method="POST" action="{{ route('staff.game_sessions.start', $session->id) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-success"><i class="fas fa-play"></i> Start</button>
                                </form>

                                <form class="mb-0" method="POST" action="{{ route('staff.game_sessions.destroy', $session->id) }}" onsubmit="return confirm('Are you sure you want to delete this session?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Delete</button>
                                </form>
                            </div>
                            @elseif($session->status === 'ongoing')
                            <div class="d-flex justify-content-center">
                                <form id="end-form-{{ $session->id }}" class="mb-0" method="POST" action="{{ route('staff.game_sessions.end', $session->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-stop"></i> End Session</button>
                                </form>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="alert text-center mt-0">No active sessions.</div>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="row">
         <div class="col-md-6">
    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><strong>Today's Bookings</strong></h6>
            <!-- Court Filter Dropdown -->
            <select id="bookingCourtFilter" class="form-control form-control-sm w-auto">
                <option value="">All Courts</option>
                @foreach($courts as $court)
                    <option value="{{ $court->name }}">{{ $court->name }}</option>
                @endforeach
            </select>
        </div>

        @php
            use Carbon\Carbon;
            $now = Carbon::now();

            // Find closest booking per court
            $closestBookingsByCourt = [];
            foreach ($courts as $court) {
                $courtBookings = $bookings->where('court_id', $court->id)
                                          ->where('status', 'confirmed');
                $closest = $courtBookings->sortBy(function($b) use ($now) {
                    return abs(Carbon::parse($b->start_time)->diffInMinutes($now, false));
                })->first();

                if ($closest) {
                    $closestBookingsByCourt[$court->id] = $closest->id;
                }
            }
        @endphp

        <!-- Scrollable booking list -->
        <div class="card-body p-0">
            <div id="bookingList" style="max-height: 500px; overflow-y: auto; padding: 1rem;">
                @forelse($bookings as $booking)
                    <div class="card shadow-sm mb-3 booking-card bg-light" data-court="{{ $booking->court->name }}">
                        <div class="card-body p-3">
                            <!-- Header -->
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="mb-1 fw-semibold"><strong>{{ $booking->user->name }}</strong></h6>
                                    <p class="mb-0 text-primary small">{{ $booking->court->name }}</p>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-success text-light">{{ ucfirst($booking->status) }}</span>
                                </div>
                            </div>

                            <!-- Footer Info -->
                            <div class="d-flex justify-content-between text-muted small mb-2">
                                <span>{{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}</span>
                                <span class="text-success">{{ $booking->expected_hours }}h {{ $booking->expected_minutes }}m</span>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex w-100">
                                @if(
                                    $booking->status === 'confirmed' &&
                                    isset($closestBookingsByCourt[$booking->court_id]) &&
                                    $closestBookingsByCourt[$booking->court_id] === $booking->id
                                )
                                    <form class="mb-0 flex-fill" method="POST" action="{{ route('staff.bookings.startSession', $booking->id) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary w-100">Start Session</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="card shadow">
                        <div class="card-body text-center">
                            <p class="mb-0">No Confirmed Bookings Today.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>


        <div class="col-md-6">
    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><strong>Customers in Queue</strong></h6>
            <!-- Responsive Sort Dropdown -->
            <select id="courtSort" class="form-control form-control-sm w-auto">
                <option value="all">All Courts</option>
                @foreach($courts as $court)
                <option value="{{ $court->name }}">{{ $court->name }}</option>
                @endforeach
            </select>
        </div>
        <!-- Added fixed height & scroll -->
        <div class="card-body" style="max-height: 500px; overflow-y: auto; padding: 1rem;">
            <div id="queueList">
                @forelse($queues as $queue)
                <div class="card shadow-sm mb-3 queue-item bg-light" data-court="{{ $queue->court->name }}">
                    <div class="card-body p-3">
                        <!-- Header -->
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="mb-1 fw-semibold">
                                    <strong class="text-danger">#{{ $queue->queue_number }}</strong>
                                    <strong> {{ $queue->customer_name }}</strong>
                                </h6>
                                <p class="mb-0 text-primary small">{{ $queue->court->name }}</p>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-warning text-light">{{ ucfirst($queue->status) }}</span>
                            </div>
                        </div>

                        <!-- Footer Info -->
                        <div class="d-flex justify-content-between text-muted small mb-2">
                            <span>{{ \Carbon\Carbon::parse($queue->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($queue->end_time)->format('h:i A') }}</span>
                            <span class="text-success">{{ $queue->expected_hours }}h {{ $queue->expected_minutes }}m</span>
                        </div>

                        <!-- Action Buttons -->
                        @if($queue->queue_number == 1)
                        <div class="d-flex w-100">
                            <form class="mb-0 flex-fill" action="{{ route('staff.queues.call', $queue->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-sm w-100">Start Session</button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="card shadow">
                    <div class="card-body text-center">
                        <p class="mb-0">No Customers in Queue.</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h5 class="m-0"><strong>Session History</strong></h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="completedSessionsTable">
                    <thead>
                        <tr>
                            <th>Court</th>
                            <th>Customer</th>
                            <th>Type</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Duration</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($completedSessions as $session)
                        <tr>
                            <td>{{ $session->court->name }}</td>
                            <td>{{ $session->customer_name }}</td>
                            <td>{{ ucfirst($session->session_type) }}</td>
                            <td>{{ \Carbon\Carbon::parse($session->start_time)->format('h:i A') }}</td>
                            <td>{{ \Carbon\Carbon::parse($session->end_time)->format('h:i A') }}</td>
                            <td>
                                @php $start = \Carbon\Carbon::parse($session->start_time); $end = \Carbon\Carbon::parse($session->end_time); $diff = $start->diff($end); @endphp {{ $diff->h }}h {{ $diff->i }}m
                            </td>
                            <td>₱{{ number_format($session->amount_paid, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection @push('scripts')
<script>
        document.addEventListener("DOMContentLoaded", function () {
    // Booking filter
    const bookingFilter = document.getElementById('bookingCourtFilter');
    const bookingCards = document.querySelectorAll('.booking-card');

    // Restore filter state if exists
    let savedBookingCourt = localStorage.getItem('bookingCourt');
    if (savedBookingCourt) {
        bookingFilter.value = savedBookingCourt;
        filterBookings(savedBookingCourt);
    }

    bookingFilter.addEventListener('change', function () {
        let selectedCourt = this.value.toLowerCase();
        localStorage.setItem('bookingCourt', this.value); // save state
        filterBookings(selectedCourt);
    });

    function filterBookings(selectedCourt) {
        bookingCards.forEach(card => {
            let court = card.getAttribute('data-court').toLowerCase();
            if (selectedCourt === "" || court === selectedCourt) {
                card.style.display = "block";
            } else {
                card.style.display = "none";
            }
        });
    }


    // Queue filter
    const courtSort = document.getElementById("courtSort");
    const queueItems = document.querySelectorAll(".queue-item");

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
        queueItems.forEach(item => {
            const court = item.getAttribute("data-court");
            if (selectedCourt === "all" || court === selectedCourt) {
                item.style.display = "block";
            } else {
                item.style.display = "none";
            }
        });
    }
});

        document.addEventListener('DOMContentLoaded', function () {
            @foreach ($activeSessions as $session)
                @if ($session->status === 'ongoing' && $session->start_time)
                    (function () {
                        const sessionId = {{ $session->id }};
                        const timerEl = document.getElementById('timer-{{ $session->id }}');
                        const startTime = new Date("{{ \Carbon\Carbon::parse($session->start_time)->format('Y-m-d H:i:s') }}".replace(' ', 'T'));
                        const durationMs = ({{ $session->expected_hours }} * 60 + {{ $session->expected_minutes }}) * 60 * 1000;
                        const endTime = startTime.getTime() + durationMs;

                        function updateTimer() {
                            const now = new Date().getTime();
                            const remaining = endTime - now;

                            if (remaining <= 0) {
                                timerEl.innerText = '00:00:00';$(document).ready(function() {
                                });
                                document.getElementById('end-form-{{ $session->id }}').submit();
                                return;
                            }

                            const hrs = Math.floor((remaining / (1000 * 60 * 60)) % 24);
                            const mins = Math.floor((remaining / (1000 * 60)) % 60);
                            const secs = Math.floor((remaining / 1000) % 60);

                            timerEl.innerText = `${hrs.toString().padStart(2, '0')}:${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
                        }

                        updateTimer();
                        setInterval(updateTimer, 1000);
                    })();
                @endif
            @endforeach
        });

        $(document).ready(function() {
            $('#completedSessionsTable').DataTable({
                pageLength: 5,
                lengthMenu: [5, 10, 25, 50, 100],
            });
        });
</script>
@endpush
