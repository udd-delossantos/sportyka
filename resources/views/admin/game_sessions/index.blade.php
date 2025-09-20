@extends('layouts.admin.app')
@section('content')
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
                <h4 class="mb-0 text-success">{{ $ongoingSessions }}</h4>
            </div>
            <div class="card shadow-sm text-center p-2 mr-1 border-bottom-info" style="min-width: 200px;">
                <h6 class="text-muted mb-1">Completed Sessions</h6>
                <h4 class="mb-0 text-info">{{ $completedSessions }}</h4>
            </div>
            <div class="card shadow-sm text-center p-2 border-bottom-danger" style="min-width: 200px;">
                <h6 class="text-muted mb-1">Pending Sessions</h6>
                <h4 class="mb-0 text-danger">{{ $pendingSessions }}</h4>
            </div>
        </div>
        </div>

    </div>
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><strong>All Sessions</strong></h4>
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
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="sessionsTable">
                    <thead>
                        <tr>
                            <th>Customer Name</th>
                            <th>Court</th>
                            <th>Session Type</th>
                            <th>Expected Duration</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Created By</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sessions as $session)
                        <tr>
                            <td>{{ $session->customer_name }}</td>
                            <td>{{ $session->court->name }}</td>
                            <td>{{ ucfirst($session->session_type) }}</td>
                            <td>{{ $session->expected_hours }}h {{ $session->expected_minutes }}m</td>
                            <td>{{ $session->start_time ? \Carbon\Carbon::parse($session->start_time)->format('h:i A') : '—' }}</td>
                            <td>{{ $session->end_time ? \Carbon\Carbon::parse($session->end_time)->format('h:i A') : '—' }}</td>
                            <td>{{ $session->staff->name ?? 'N/A' }}</td>
                            <td class="text-light text-center">
                                <span class="badge bg-{{ 
                                    $session->status === 'pending' ? 'warning' : 
                                    ($session->status === 'in_progress' ? 'success' : 
                                    ($session->status === 'completed' ? 'info' : 'danger')) 
                                }}">
                                    {{ ucfirst($session->status) }}
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
@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#sessionsTable').DataTable({
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
                title: 'All Sessions',
                exportOptions: { columns: ':visible' }
            },
            {
                extend: 'excelHtml5',
                title: 'All Sessions',
                exportOptions: { columns: ':visible' }
            },
            {
                extend: 'print',
                title: 'All Sessions',
                exportOptions: { columns: ':visible' }
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
</script>
@endpush
