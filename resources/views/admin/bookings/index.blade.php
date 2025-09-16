@extends('layouts.admin.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><strong>All Bookings</strong></h4>
        </div>
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="bookingsTable">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Court</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Duration</th>
                            <th>Amount</th>
                            <th>Transaction No</th>
                            <th>Approved By</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                         @foreach($bookings as $booking)
                            <tr>
                                <td>{{ $booking->user->name ?? ''}}</td>
                                <td>{{ $booking->court->name }}</td>
                                <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('F d, Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}</td>
                                <td>{{ $booking->expected_hours}}h {{ $booking->expected_minutes }}m</td>
                                <td>₱{{ number_format($booking->amount, 2) }}</td>
                                <td>{{ $booking->transaction_no ?? '—' }}</td>
                                <td class="text-center">
                                    {{ $booking->staff->name ?? '—' }}
                                </td>


                                <td class="text-light text-center">
                                    <span class="badge bg-{{ 
                                        $booking->status === 'pending' ? 'warning' : 
                                        ($booking->status === 'confirmed' ? 'success' : 
                                        ($booking->status === 'completed' ? 'info' : 'danger')) 
                                        }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
    
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><strong>Calendar</strong></h4>
        </div>
        <div class="card-body">
            <div id="calendar"></div>

        </div>

    </div>
  

 
</div>
@endsection
@push('styles')
<style>

/* Day names (Sun, Mon, etc.) */
.fc .fc-col-header-cell-cushion {
  color: #858796 !important;
  font-weight: bold;
  text-decoration: none; /* removes underline */
}

/* Date numbers (1, 2, 3...) */
.fc .fc-daygrid-day-number {
  color: #858796 !important;
  text-decoration: none;
}
</style>
@endpush
@push('scripts')
<script>

        $(document).ready(function() {
        $('#bookingsTable').DataTable({
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50, 100],
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const calendarEl = document.getElementById('calendar');

        if (calendarEl) {
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
                events: @json($events),
                height: "auto"
            });

            calendar.render();
        } else {
            console.error("Calendar div not found.");
        }
    });
</script>
@endpush

