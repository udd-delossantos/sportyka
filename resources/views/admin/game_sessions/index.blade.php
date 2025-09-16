@extends('layouts.admin.app')
@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><strong>All Sessions</strong></h4>
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
    $('#sessionsTable').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
    });
});
</script>
@endpush
