@extends('layouts.admin.app')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Booking Requests</h1>
    </div>

    <div class="row">

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Requests (Today)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $requestCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
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
                                Approved (Today)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $approvedCount }}</div>
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
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Cancelled (Today)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $cancelledCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ban fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Action</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingCount }}</div>
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
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h5 class="m-0 font-weight-bold text-primary">All Booking Requests</h5>
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
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered" id="bookingRequestsTable" width="100%" cellspacing="0">
                    <thead class="thead-light text-gray-800"> 
                        <tr class="bg-gray-100">
                            <th>Customer</th>
                            <th>Details</th> <th>Amount</th>
                            <th>Ref No.</th>
                            <th>Actioned By</th>
                            <th>Requested At</th>
                            <th>Updated</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $request)
                            <tr>
                                <td class="align-middle font-weight-bold text-gray-800">
                                    {{ $request->user->name }}
                                </td>
                                <td class="font-weight-bold text-primary">
                                    <span class="font">{{ $request->court->name }}</span><br>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($request->booking_date)->format('F d, Y') }}<br>
                                        {{ \Carbon\Carbon::parse($request->start_time)->format('h:i A') }} - 
                                        {{ \Carbon\Carbon::parse($request->end_time)->format('h:i A') }}<br>
                                        {{ $request->expected_hours }}h {{ $request->expected_minutes }}m
                                    </small>
                                </td>
                                <td class="font-weight-bold text-success align-middle">
                                    ₱{{ number_format($request->amount, 2) }}
                                </td>
                                <td class="align-middle"><code>{{ $request->transaction_no ?? '—' }}</code>
                                    
                                </td>
                                <td class="align-middle">
                                    {{ $request->staff->name ?? '—' }}
                                </td>
                                 <td class="align-middle small">
                                        {{ \Carbon\Carbon::parse($request->created_at)->format('F d, Y') }}
                                        </td>
                                <td class="align-middle small">
                                    {{ \Carbon\Carbon::parse($request->updated_at)->format('M d, Y h:i A') }}
                                </td>
                                <td class="align-middle text-center">
                                    @php
                                        $statusClass = 'secondary';
                                        if($request->status === 'approved') $statusClass = 'success';
                                        elseif($request->status === 'pending') $statusClass = 'warning';
                                        elseif($request->status === 'cancelled') $statusClass = 'danger';
                                        elseif($request->status === 'completed') $statusClass = 'info';
                                    @endphp
                                    <span class="badge badge-{{ $statusClass }} p-2">
                                        {{ ucfirst($request->status) }}
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
document.addEventListener("DOMContentLoaded", function () {

    // --- DATATABLE INITIALIZATION ---
    $(document).ready(function() {
        var table = $('#bookingRequestsTable').DataTable({
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50, 100],
            order: [], 
            // SB Admin 2 style DOM positioning
            dom: 
                "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            
            buttons: [
                { extend: 'csvHtml5', exportOptions: { columns: ':visible' } },
                { extend: 'excelHtml5', exportOptions: { columns: ':visible' } },
                { extend: 'print', exportOptions: { columns: ':visible' } }
            ]
        });

        // Link custom buttons to DataTable functions
        $('#exportCsv').on('click', function() { table.button(0).trigger(); });
        $('#exportExcel').on('click', function() { table.button(1).trigger(); });
        $('#printTable').on('click', function() { table.button(2).trigger(); });
    });
});
</script>
@endpush