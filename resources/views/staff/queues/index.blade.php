@extends('layouts.staff.app')
@section('content')
<div class="container-fluid">
    <div class="px-0">
        <div class="card-body d-flex justify-content-between align-items-center px-0 pt-0">
            <h2 class="mb-0 text-primary"><strong>Queues</strong></h2>
        <div class="d-flex gap-3">
            <div class="card shadow-sm text-center p-2 mr-1 border-bottom-warning" style="min-width: 200px;">
                <h6 class="text-muted mb-1">Waiting</h6>
                <h4 class="mb-0 text-warning">{{ $waitingCount }}</h4>
            </div>
            <div class="card shadow-sm text-center p-2 mr-1 border-bottom-success" style="min-width: 200px;">
                <h6 class="text-muted mb-1">Called</h6>
                <h4 class="mb-0 text-success">{{ $calledCount }}</h4>
            </div>
            <div class="card shadow-sm text-center p-2 mr-1 border-bottom-info" style="min-width: 200px;">
                <h6 class="text-muted mb-1">Completed</h6>
                <h4 class="mb-0 text-info">{{ $completedCount }}</h4>
            </div>
            <div class="card shadow-sm text-center p-2 border-bottom-danger" style="min-width: 200px;">
                <h6 class="text-muted mb-1">Skipped</h6>
                <h4 class="mb-0 text-danger">{{ $skippedCount }}</h4>
            </div>
        </div>

    </div>
    

</div>

    


    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><strong>Queue Management</strong></h4>
            <a href="{{ route('staff.queues.create') }}" class="btn btn-primary">Add Queue</a>
        </div>
        <div class="card-body">
            @if(session('error'))<div class="alert alert-danger">{{ session('error') }}@endif
            @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div> @endif
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="queuesTable">
                    <thead>
                        <tr>
                            <th>Queue No</th>
                            <th>Customer Name</th>
                            <th>Court</th>
                            <th>Time</th>
                            <th>Duration</th>
                            <th>Amount</th>
                            <th>Transaction No</th>
                            <th>Staff</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                       @foreach($queues as $queue)
                                <tr>
                                    <td class="text-center">
                                        <h6 class="text-danger"><strong>#{{ $queue->queue_number }}</strong></h6>
                                    </td>
                                    <td>{{ $queue->customer_name }}</td>
                                    <td>{{ $queue->court->name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($queue->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($queue->end_time)->format('h:i A') }}</td>
                                    <td>{{ $queue->expected_hours}}h {{ $queue->expected_minutes }}m</td>
                                    <td>₱{{ number_format($queue->amount, 2) }}</td>
                                    <td>{{ $queue->transaction_no ?? 'N/A' }}</td>
                                    <td>{{ $queue->staff->name ?? '—' }}</td>

                                    <td class="text-light text-center">
                                        <span class="badge bg-{{ 
                                            $queue->status === 'waiting' ? 'warning' : 
                                            ($queue->status === 'called' ? 'success' : 
                                            ($queue->status === 'completed' ? 'info' : 'danger')) 
                                            }}">
                                            {{ ucfirst($queue->status) }}
                                        </span>
                                    </td>
                                    <td class="text-center d-flex justify-content-center gap-2 text-center">
                                        @if($queue->status === 'waiting')
                                            <form action="{{ route('staff.queues.call', $queue->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm mx-1">Call</button>
                                            </form>
                                            <form action="{{ route('staff.queues.skip', $queue->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to skip this queue?');">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-sm">Skip</button>
                                            </form>
                                        @else
                                            <span class="text-muted">No Action</span>
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
    $('#queuesTable').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
    });
});
</script>
@endpush
