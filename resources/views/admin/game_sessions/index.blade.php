@extends('layouts.admin.app')
@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Sessions</h1>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $ongoingSessions }}</div>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Completed Sessions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $completedSessions }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Pending Sessions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingSessions }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary"><strong>All Sessions</strong></h5>
            <!-- Export buttons -->
            <div class="btn-group" role="group">
                <button id="exportCsv" class="btn btn-sm btn-info shadow-sm"><i class="fas fa-file-csv fa-sm text-white-50"></i> CSV</button>
                <button id="exportExcel" class="btn btn-sm btn-success shadow-sm"><i class="fas fa-file-excel fa-sm text-white-50"></i> Excel</button>
                <button id="printTable" class="btn btn-sm btn-secondary shadow-sm"><i class="fas fa-print fa-sm text-white-50"></i> Print</button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="sessionsTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
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
                            <td class="font-weight-bold text-gray-800">{{ $session->customer_name }}</td>
                            <td>{{ $session->court->name }}</td>
                            <td>{{ ucfirst($session->session_type) }}</td>
                            <td>{{ $session->expected_hours }}h {{ $session->expected_minutes }}m</td>
                            <td>{{ $session->start_time ? \Carbon\Carbon::parse($session->start_time)->format('h:i A') : '—' }}</td>
                            <td>{{ $session->end_time ? \Carbon\Carbon::parse($session->end_time)->format('h:i A') : '—' }}</td>
                            <td>{{ $session->staff->name ?? 'N/A' }}</td>
                            <td class="text-light text-center">
                                @if ($session->status === 'pending')
                                    <span class="badge bg-warning p-2">{{ ucfirst($session->status) }}</span>
                                @elseif ($session->status === 'completed')
                                    <span class="badge bg-info p-2">{{ ucfirst($session->status) }}</span>
                                @elseif ($session->status === 'ongoing')
                                    <span class="badge bg-success p-2">{{ ucfirst($session->status) }}</span>
                                @else
                                    <span class="badge bg-danger p-2">{{ ucfirst($session->status) }}</span>
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
@endsection
@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#sessionsTable').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
         order: [],
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
