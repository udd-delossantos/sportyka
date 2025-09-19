@extends('layouts.admin.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><strong>All Bookings</strong></h4>
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
            <div class="row mb-3 align-items center">
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
@push('scripts')
<script>
    // Custom filter for month range
    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        let min = $('#minMonth').val();
        let max = $('#maxMonth').val();
        let dateCreated = data[2]; // Column index 2 = Date

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

    $(document).ready(function() {
        var table = $('#bookingsTable').DataTable({
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50, 100],
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
                    title: 'All Bookings',
                    exportOptions: { columns: ':visible' }
                }
            ]
        });

        // Insert filters into placeholder
        $("div.date-filters").html(`
            <div class="d-flex align-items-center">
                <label for="minMonth" class="me-2 mb-0">From:</label>
                <input type="month" id="minMonth" class="form-control form-control-sm me-3">
                <label for="maxMonth" class="me-2 mb-0">To:</label>
                <input type="month" id="maxMonth" class="form-control form-control-sm">
            </div>
        `);

        // External export buttons
        $('#exportCsv').on('click', function() {
            table.button(0).trigger();
        });
        $('#exportExcel').on('click', function() {
            table.button(1).trigger();
        });
        $('#printTable').on('click', function() {
            table.button(2).trigger();
        });

        // Redraw on filter change
        $(document).on('change', '#minMonth, #maxMonth', function () {
            table.draw();
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
