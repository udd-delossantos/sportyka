@extends('layouts.staff.app')

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
            <h5 class="m-0 font-weight-bold text-primary mr-3">Pending Requests</h5>
            <div class="dropdown no-arrow">
                <select id="courtFilter" class="form-control form-control-sm border-left-primary ml-auto" style="width: 200px;">
                    <option value="">All Courts</option>
                    @foreach($courts as $court)
                        <option value="{{ $court->id }}">{{ $court->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="card-body bg-light">
            <div class="row" id="bookingList" style="max-height: 650px; overflow-y: auto;">
                @forelse($requests as $request)
                <div class="col-xl-4 col-md-6 mb-4 booking-card" 
                     data-court="{{ $request->court_id }}"
                     data-status="{{ $request->status }}">
                    
                    <div class="card border-bottom-primary shadow-sm h-100 py-0">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center mb-2">
                                <div class="col mr-2">
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $request->user->name }}</div>
                                    <div class="text-sm font-weight-bold text-primary mb-1">
                                        {{ $request->court->name }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="h5 mb-0 font-weight-bold text-success">
                                        ₱{{ number_format($request->amount, 0) }}
                                    </div>
                                </div>
                            </div>

                            <hr class="sidebar-divider my-2">

                            <div class="mb-3 mt-3">
                                <div class="small text-gray-600 mb-1">
                                    <i class="fas fa-calendar-alt fa-fw mr-1"></i> 
                                    {{ \Carbon\Carbon::parse($request->booking_date)->format('M d, Y') }}
                                </div>
                                <div class="small text-gray-600 mb-1">
                                    <i class="fas fa-clock fa-fw mr-1"></i> 
                                    {{ \Carbon\Carbon::parse($request->start_time)->format('h:i A') }} - 
                                    {{ \Carbon\Carbon::parse($request->end_time)->format('h:i A') }} <span class="font-weight-bold">({{ $request->expected_hours }}h {{ $request->expected_minutes }}m)</span>
                                </div>
                                <div class="small text-gray-600">
                                    <i class="fas fa-hashtag fa-fw mr-1"></i> 
                                    {{ $request->transaction_no ?? 'N/A' }}
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6 pr-1">
                                    <form action="{{ route('staff.booking_requests.approve', $request->id) }}" method="POST" class="approve-form">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm btn-block approve-btn">
                                            <span class="text">Approve</span>
                                        </button>
                                    </form>
                                </div>
                                <div class="col-6 pl-1">
                                    <form action="{{ route('staff.booking_requests.cancel', $request->id) }}" method="POST" class="cancel-form">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm btn-block cancel-btn">
                                            <span class="text">Decline</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center">
                    <p class="text-gray-500 mb-0">No pending requests.</p>
                </div>
                @endforelse
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
                        <tr>
                            <th>Customer</th>
                            <th>Details</th> <th>Amount</th>
                            <th>Ref. No</th>
                            <th>Actioned By</th>
                            <th>Requested At</th>
                            <th>Updated</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($processedRequests as $processedRequest)
                            <tr>
                                <td class="align-middle font-weight-bold text-gray-800">
                                    {{ $processedRequest->user->name }}
                                </td>
                                <td class="font-weight-bold text-primary">
                                    <span class="font">{{ $processedRequest->court->name }}</span><br>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($processedRequest->booking_date)->format('F d, Y') }}<br>
                                        {{ \Carbon\Carbon::parse($processedRequest->start_time)->format('h:i A') }} - 
                                        {{ \Carbon\Carbon::parse($processedRequest->end_time)->format('h:i A') }}<br>
                                        {{ $processedRequest->expected_hours }}h {{ $processedRequest->expected_minutes }}m
                                    </small>
                                </td>
                                <td class="font-weight-bold text-success align-middle">
                                    ₱{{ number_format($processedRequest->amount, 2) }}
                                </td>
                                <td class="align-middle"><code>{{ $processedRequest->transaction_no ?? '—' }}</code>
                                    
                                </td>
                                <td class="align-middle">
                                    {{ $processedRequest->staff->name ?? '—' }}
                                </td>
                                 <td class="align-middle small">
                                        {{ \Carbon\Carbon::parse($processedRequest->created_at)->format('F d, Y') }}
                                        </td>
                                <td class="align-middle small">
                                    {{ \Carbon\Carbon::parse($processedRequest->updated_at)->format('M d, Y h:i A') }}
                                </td>
                                <td class="align-middle text-center">
                                    @php
                                        $statusClass = 'secondary';
                                        if($processedRequest->status === 'approved') $statusClass = 'success';
                                        elseif($processedRequest->status === 'pending') $statusClass = 'warning';
                                        elseif($processedRequest->status === 'cancelled') $statusClass = 'danger';
                                        elseif($processedRequest->status === 'completed') $statusClass = 'info';
                                    @endphp
                                    <span class="badge badge-{{ $statusClass }} p-2">
                                        {{ ucfirst($processedRequest->status) }}
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

        // --- FILTER LOGIC (UPDATED FOR GRID LAYOUT) ---
        document.getElementById('courtFilter').addEventListener('change', function () {
            const courtId = this.value;
            // Note: In Grid layout, the 'booking-card' class is on the COL div, so we hide the whole column
            document.querySelectorAll('.booking-card').forEach(card => {
                if (!courtId || card.dataset.court === courtId) {
                    card.style.display = ''; // Restore default display (block/flex)
                } else {
                    card.style.display = 'none';
                }
            });
        });

        // --- CONFIRMATION DIALOGS ---
        document.addEventListener("click", function (e) {
            // APPROVE
            if (e.target.closest(".approve-btn")) {
                e.preventDefault();
                let form = e.target.closest("form");
                if (!form) return;
                let ok = confirm("Are you sure you want to APPROVE this request?");
                if (ok) form.submit();
            }
            // CANCEL
            if (e.target.closest(".cancel-btn")) {
                e.preventDefault();
                let form = e.target.closest("form");
                if (!form) return;
                let ok = confirm("Are you sure you want to DECLINE/CANCEL this request?");
                if (ok) form.submit();
            }
        });
    });

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
</script>
@endpush