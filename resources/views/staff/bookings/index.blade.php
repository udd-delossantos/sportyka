@extends('layouts.staff.app')

@section('content')
<div class="container-fluid">
    <div class="px-0">
        <div class="card-body d-flex justify-content-between align-items-center px-0 pt-0">
            <h2 class="mb-0 text-primary"><strong>Scheduled Bookings</strong></h2>
            <div class="d-flex gap-3">
                <div class="card shadow-sm text-center p-2 mr-1 border-bottom-success" style="min-width: 200px;">
                    <h6 class="text-muted mb-1">Confirmed</h6>
                    <h4 class="mb-0 text-success">{{ $confirmedCount }}</h4>
                </div>
                <div class="card shadow-sm text-center p-2 mr-1 border-bottom-primary" style="min-width: 200px;">
                    <h6 class="text-muted mb-1">Ongoing</h6>
                    <h4 class="mb-0 text-primary">{{ $ongoingCount }}</h4>
                </div>
                <div class="card shadow-sm text-center p-2 mr-1 border-bottom-info" style="min-width: 200px;">
                    <h6 class="text-muted mb-1">Completed (Today)</h6>
                    <h4 class="mb-0 text-info">{{ $completedTodayCount }}</h4>
                </div>
                <div class="card shadow-sm text-center p-2 border-bottom-danger" style="min-width: 200px;">
                    <h6 class="text-muted mb-1">Voided (Today)</h6>
                    <h4 class="mb-0 text-danger">{{ $voidedTodayCount }}</h4>
                </div>
            </div>
        </div>

        <!-- Bookings List -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><strong>Bookings List</strong></h4>
                 <div>
                <button id="exportCsv" class="btn btn-info btn-sm">
                    <i class="fas fa-file-csv"></i> CSV
                </button>
                <button id="exportExcel" class="btn btn-success btn-sm">
                    <i class="fas fa-file-excel"></i> Excel
                </button>
                <button id="printTable" class="btn btn-secondary btn-sm">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
            </div>
            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

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
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookings as $booking)
                                <tr>
                                    <td>{{ $booking->user->name }}</td>
                                    <td>{{ $booking->court->name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('F d, Y') }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} - 
                                        {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}
                                    </td>
                                    <td>{{ $booking->expected_hours }}h {{ $booking->expected_minutes }}m</td>
                                    <td>₱{{ number_format($booking->amount, 2) }}</td>
                                    <td>{{ $booking->transaction_no ?? '—' }}</td>
                                    <td>{{ $booking->staff->name ?? '—' }}</td>
                                    <td class="text-light text-center">
                                        <span class="badge bg-{{ 
                                            $booking->status === 'pending' ? 'warning' : 
                                            ($booking->status === 'confirmed' ? 'success' : 
                                            ($booking->status === 'completed' ? 'info' : 'danger')) 
                                        }}">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($booking->status === 'confirmed')
                                            <a href="{{ route('staff.bookings.edit', $booking->id) }}" class="btn btn-sm btn-warning">Move</a>
                                        @else 
                                            —
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Calendar -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><strong>Calendar</strong></h4>
            </div>
            <div class="card-body">
                <div id="calendar"></div>
            </div>
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
    $(document).ready(function () {
        var table = $('#bookingsTable').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        dom: 
            // top (search removed since you already have buttons outside)
            '<"top d-flex justify-content-between align-items-center mb-2"lf>rt' +
            // bottom with pagination aligned right
            '<"bottom d-flex justify-content-between align-items-center"ip>',
        buttons: [
            {
                extend: 'csvHtml5',
                title: 'Scheduled Bookings Records',
                exportOptions: { columns: ':not(:last-child)' }
            },
            {
                extend: 'excelHtml5',
                title: 'Scheduled Bookings Records',
                exportOptions: { columns: ':not(:last-child)' }
            },
            {
                extend: 'print',
                title: 'Scheduled Bookings Records',
                exportOptions: { columns: ':not(:last-child)' }
            }
        ]
    });
    // External buttons
    $('#exportCsv').on('click', function() {
        table.button(0).trigger();
    });
    $('#exportExcel').on('click', function() {
        table.button(1).trigger();
    });
    $('#printTable').on('click', function() {
        table.button(2).trigger();
    });

    });

    document.addEventListener('DOMContentLoaded', function () {
        const calendarEl = document.getElementById('calendar');
        if (calendarEl) {
            const calendar = new FullCalendar.Calendar(calendarEl, {
                themeSystem: 'bootstrap',
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
