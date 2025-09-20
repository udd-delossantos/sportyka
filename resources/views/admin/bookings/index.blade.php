@extends('layouts.admin.app')

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
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><strong>All Bookings</strong></h4>
            <h4>Total Earnings (Filtered Date): <span id="totalEarnings" class="text-success">₱ 0.00</span></h4>
            <!-- Export buttons -->
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
            @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
            @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="minMonth">From Month:</label>
                    <input type="month" id="minMonth" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="maxMonth">To Month:</label>
                    <input type="month" id="maxMonth" class="form-control">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="bookingsTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Court</th>
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
                                <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('F d, Y') }}</td>
                                <td>{{ $booking->user->name ?? ''}}</td>
                                <td>{{ $booking->court->name }}</td>
                                <td>{{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}</td>
                                <td>{{ $booking->expected_hours}}h {{ $booking->expected_minutes }}m</td>
                                <td>₱{{ number_format($booking->amount, 2) }}</td>
                                <td>{{ $booking->transaction_no ?? '—' }}</td>
                                <td class="text-center">{{ $booking->staff->name ?? '—' }}</td>
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

    <!-- Calendar Section -->
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
    // Custom filter for month range
    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        let min = $('#minMonth').val();
        let max = $('#maxMonth').val();
        let dateCreated = data[0]; // Column index 0 = Date

        if (!dateCreated) return true;

        let dateObj = new Date(dateCreated);
        let dateMonth = dateObj.getFullYear() + "-" + 
                        String(dateObj.getMonth() + 1).padStart(2, '0');

        if ((min === "" || dateMonth >= min) &&
            (max === "" || dateMonth <= max)) {
            return true;
        }
        return false;
    });

    var table = $('#bookingsTable').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        dom:
            '<"top d-flex justify-content-between align-items-center mb-2"lf>rt' +
            '<"bottom d-flex justify-content-between align-items-center"ip>',
        buttons: [
            {
                extend: 'csvHtml5',
                title: 'All Bookings',
                exportOptions: { columns: ':visible' }
            },
            {
                extend: 'excelHtml5',
                title: 'All Bookings',
                exportOptions: { columns: ':visible' }
            },
            {
                extend: 'print',
                title: '',
                exportOptions: { columns: ':visible'},
                customize: function (win) {
                    // Calculate total earnings of filtered table
                    let total = 0;
                    $('#bookingsTable').DataTable().column(5, { search: 'applied' }).data().each(function (val) {
                        total += parseFloat(val.replace(/[^0-9.-]+/g, "")) || 0;
                    });

                    // Get selected filter values
                    let from = $('#minMonth').val();
                    let to   = $('#maxMonth').val();

                    // Format YYYY-MM into Month YYYY
                    function formatMonth(ym) {
                        if (!ym) return '';
                        let [year, month] = ym.split("-");
                        let d = new Date(year, month - 1);
                        return d.toLocaleString('default', { month: 'long', year: 'numeric' });
                    }

                    let rangeText = '';
                    if (from && to) rangeText = `${formatMonth(from)} - ${formatMonth(to)}`;
                    else if (from) rangeText = `From ${formatMonth(from)}`;
                    else if (to)   rangeText = `Up to ${formatMonth(to)}`;
                    else rangeText = 'All Records';

                    // Add custom header + footer
                    $(win.document.body).prepend(`
                        <div style="text-align:center; margin-bottom:20px;">
                            <h2>Proving Grounds Sports Center</h2>
                            <h4>Bookings Report</h4>
                            <p>Date Range: ${rangeText}</p>
                        </div>
                    `);

                    $(win.document.body).append(`
                        <div style="margin-top:20px; font-size:16px;">
                            <p><strong>Total Earnings:</strong> ₱ ${total.toFixed(2)}</p>
                            <p style="margin-top:30px;">Generated by Sporty Ka? Management System</p>
                        </div>
                    `);

                    $(win.document.body).find('table')
                        .addClass('compact')
                        .css('font-size', '12px');
                }
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

    // Redraw table when filters change
    $('#minMonth, #maxMonth').on('change', function () {
        table.draw();
    });

    // Live total earnings update
    table.on('draw', function () {
        let total = 0;
        table.column(5, { search: 'applied' }).data().each(function (val) {
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
