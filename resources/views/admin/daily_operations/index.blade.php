@extends('layouts.admin.app')
@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header">
             <div class="row">
                <div class="col md-4">
                    <h4 class="m-0 font-weight-bold text-secondary">Daily Operations</h4>
                </div>
                <div class="col md-8 text-right">
                    <form action="{{ route('admin.daily_operations.open') }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-success">Open Today's Operation</button>
                    </form>

                    @if($active)
                        <form action="{{ route('admin.daily_operations.close', $active->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-danger" onclick="return confirm('Close current operation?')">Close Today's Operation</button>
                        </form>
                    @endif

                    <form action="{{ route('admin.daily_operations.reset') }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-primary" onclick="return confirm('Reset system and start a new day?')">Open New Day</button>
                    </form>

                </div>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
            @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="operationsTable">
                    <thead>
                        <tr>
                            <th>Date Created</th>                          
                            <th>Opened At</th>
                            <th>Closed At</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Action</th>      
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($operations as $op)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($op->date)->format('F d, Y') }}</td>                          
                                <td>{{ $op->opened_at->format('M d, Y - h:i A') }}</td>
                                <td>{{ $op->closed_at?->format('M d, Y - h:i A') ?? '—' }}</td>
                                <td class="text-center">{{ ucfirst($op->status) }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.daily_operations.show', $op->id) }}" class="btn btn-sm btn-info">View Report</a>
                                    
                                    <form action="{{ route('admin.daily-operations.reopen', $op->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('Re-open this day?')">
                                            Re-open
                                        </button>
                                    </form>
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
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#operationsTable').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
    });
});
</script>
@endpush
