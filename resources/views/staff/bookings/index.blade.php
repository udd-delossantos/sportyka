@extends('layouts.staff.app')
@section('title', 'Bookings Schedule')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Bookings Schedule</h1>
      
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Collected DP Today</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($confirmedBookingsTotal, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-coins fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Confirmed/Ongoing</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $confirmedTodayCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Completed Today</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $completedTodayCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-double fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Voided Today</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $voidedTodayCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ban fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h5 class="m-0 font-weight-bold text-primary"></i>Schedule Calendar</h5>
                </div>
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-12 col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h5 class="m-0 font-weight-bold text-primary"></i>Detailed Bookings List</h5>
                      <div class="btn-group shadow-sm" role="group">
                        <button id="exportCsv" class="btn btn-sm btn-info">
                            <i class="fas fa-file-csv fa-sm text-white-50 mr-1"></i> CSV
                        </button>
                        <button id="exportExcel" class="btn btn-sm btn-success">
                            <i class="fas fa-file-excel fa-sm text-white-50 mr-1"></i> Excel
                        </button>
                        <button id="printTable" class="btn btn-sm btn-secondary">
                            <i class="fas fa-print fa-sm text-white-50 mr-1"></i> Print
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger border-left-danger shadow-sm">{{ session('error') }}</div>
                    @endif
                    @if(session('success'))
                        <div class="alert alert-success border-left-success shadow-sm">{{ session('success') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="bookingsTable" width="100%" cellspacing="0">
                            <thead class="thead-light text-gray-800">
                                <tr class="text-gray-800">
                                    <th>Customer</th>
                                    <th>Details</th>
                                    <th>Amount</th>
                                    <th>Ref No.</th>
                                    <th>Requested At</th>
                                    <th>Approved By</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings as $booking)
                                    <tr>
                                        <td class="font-weight-bold text-gray-800 align-middle">{{ $booking->user->name }}</td>
                                          <td class="font-weight-bold text-primary">
                                    <span class="font">{{ $booking->court->name }}</span><br>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($booking->booking_date)->format('F d, Y') }}<br>
                                        {{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} - 
                                        {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}<br>
                                        {{ $booking->expected_hours }}h {{ $booking->expected_minutes }}m
                                    </small>
                                </td>
                                        <td class="text-success font-weight-bold align-middle">₱{{ number_format($booking->amount, 2) }}</td>
                                        <td class="align-middle"><code>{{ $booking->transaction_no ?? '—' }}</code></td>
                                        <td class="small align-middle">
                                        {{ \Carbon\Carbon::parse($booking->created_at)->format('F d, Y') }}
                                        </td>
                                        <td class="align-middle">{{ $booking->staff->name ?? '—' }}</span></td>
                                        <td class="text-center align-middle">
                                            @php
                                                $statusClass = [
                                                    'pending' => 'warning',
                                                    'confirmed' => 'success',
                                                    'ongoing' => 'primary',
                                                    'completed' => 'info',
                                                ][$booking->status] ?? 'danger';
                                            @endphp
                                            <span class="badge badge-{{ $statusClass }} p-2">{{ ucfirst($booking->status) }}</span>
                                        </td>
                                       <td class="text-center align-middle">
    @if($booking->status === 'confirmed')
        <a href="{{ route('staff.bookings.edit', $booking->id) }}" 
           class="btn btn-sm btn-warning shadow-sm" 
           title="Move Booking">
            <i class="fas fa-exchange-alt"></i> Move
        </a>
    @else 
        <span class="text-gray-400 small">—</span>
    @endif
</td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
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
    } /* Date numbers (1, 2, 3...) */
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
            order: [], // Sort by date by default
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"row" <"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            buttons: [
                { extend: 'csvHtml5', title: 'Scheduled_Bookings_' + new Date().toLocaleDateString(), exportOptions: { columns: ':not(:last-child)' } },
                { extend: 'excelHtml5', title: 'Scheduled_Bookings_' + new Date().toLocaleDateString(), exportOptions: { columns: ':not(:last-child)' } },
                { extend: 'print', title: 'Scheduled Bookings Records', exportOptions: { columns: ':not(:last-child)' } }
            ]
        });

        $('#exportCsv').on('click', () => table.button(0).trigger());
        $('#exportExcel').on('click', () => table.button(1).trigger());
        $('#printTable').on('click', () => table.button(2).trigger());
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