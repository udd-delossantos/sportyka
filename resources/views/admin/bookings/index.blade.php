@extends('layouts.admin.app')

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
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Collected DP Today
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ₱{{ number_format($confirmedBookingsTotal, 2) }}
                            </div>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Confirmed / Ongoing
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $confirmedTodayCount }}
                            </div>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Completed Today
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $completedTodayCount }}
                            </div>
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
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Voided Today
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $voidedTodayCount }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ban fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Calendar --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary">Schedule</h5>
        </div>
        <div class="card-body">
            <div id="calendar"></div>
        </div>
    </div>

    {{-- Detailed List --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary">Detailed Bookings List</h5>

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

            <div class="row mb-4 p-3 bg-light rounded mx-1 border">
                <div class="col-md-4 mb-2 mb-md-0">
                    <label class="small font-weight-bold text-gray-600">From Month</label>
                    <input type="month" id="minMonth" class="form-control form-control-sm">
                </div>

                <div class="col-md-4 mb-2 mb-md-0">
                    <label class="small font-weight-bold text-gray-600">To Month</label>
                    <input type="month" id="maxMonth" class="form-control form-control-sm">
                </div>

                <div class="col-md-4 d-flex align-items-center justify-content-end">
                    <div class="text-right">
                        <div class="small font-weight-bold text-gray-600">Filtered Earnings</div>
                        <div class="h4 mb-0 font-weight-bold text-success" id="totalEarnings">
                            ₱ 0.00
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="bookingsTable" width="100%">
                    <thead class="thead-light text-gray-800">
                        <tr class="text-gray-800">
                            <th>Customer</th>
                            <th>Details</th>
                            <th>Amount</th>
                            <th>Ref No.</th>
                            <th>Requested At</th>
                            <th>Approved By</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                            <tr>
                                <td class="font-weight-bold text-gray-800 align-middle">
                                    {{ $booking->user->name }}
                                </td>
                                <td class="font-weight-bold text-primary">
                                    {{ $booking->court->name }}<br>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($booking->booking_date)->format('F d, Y') }}<br>
                                        {{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} -
                                        {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}<br>
                                        {{ $booking->expected_hours }}h {{ $booking->expected_minutes }}m
                                    </small>
                                </td>
                                <td class="text-success font-weight-bold align-middle">
                                    ₱{{ number_format($booking->amount, 2) }}
                                </td>
                                <td class="align-middle">
                                    <code>{{ $booking->transaction_no ?? '—' }}</code>
                                </td>
                                <td class="small align-middle">
                                    {{ \Carbon\Carbon::parse($booking->created_at)->format('F d, Y') }}
                                </td>
                                <td class="align-middle">
                                    {{ $booking->staff->name ?? '—' }}
                                </td>
                                <td class="text-center align-middle">
                                    @php
                                        $statusClass = [
                                            'pending' => 'warning',
                                            'confirmed' => 'success',
                                            'ongoing' => 'primary',
                                            'completed' => 'info',
                                        ][$booking->status] ?? 'danger';
                                    @endphp
                                    <span class="badge badge-{{ $statusClass }} p-2">
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

    // Month range filter (Requested At = column 4)
    $.fn.dataTable.ext.search.push(function (settings, data) {
        let min = $('#minMonth').val();
        let max = $('#maxMonth').val();
        let dateCreated = data[4];

        if (!dateCreated) return true;

        let dateObj = new Date(dateCreated);
        let dateMonth = dateObj.getFullYear() + "-" +
                        String(dateObj.getMonth() + 1).padStart(2, '0');

        return (!min || dateMonth >= min) && (!max || dateMonth <= max);
    });

    var table = $('#bookingsTable').DataTable({
        pageLength: 10,
        order: []
    });

    $('#minMonth, #maxMonth').on('change', function () {
        table.draw();
    });

    // Live earnings update (Amount = column 2)
    table.on('draw', function () {
        let total = 0;
        table.column(2, { search: 'applied' }).data().each(function (val) {
            total += parseFloat(val.replace(/[^0-9.-]+/g, "")) || 0;
        });
        $('#totalEarnings').text("₱ " + total.toFixed(2));
    });
});

// FullCalendar init
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
