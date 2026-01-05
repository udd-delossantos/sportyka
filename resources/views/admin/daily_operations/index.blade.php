@extends('layouts.admin.app')
@section('title', 'Daily Operations')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Daily Operations</h1>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100 py-2 {{ $active ? 'border-left-success' : 'border-left-secondary' }}">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold {{ $active ? 'text-success' : 'text-secondary' }} text-uppercase mb-1">
                                Daily Operation Status
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 mb-3">
                                {{ $active ? 'Open' : 'Closed' }}
                            </div>
                            
                            @if($active)
                                <form action="{{ route('admin.daily_operations.close', $active->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-icon-split btn-sm" onclick="return confirm('Close current operation?')">
                                        <span class="icon text-white-50"><i class="fas fa-stop"></i></span>
                                        <span class="text">Close Operation</span>
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.daily_operations.open') }}" method="POST">
                                    @csrf
                                    <button class="btn btn-success btn-icon-split btn-sm">
                                        <span class="icon text-white-50"><i class="fas fa-play"></i></span>
                                        <span class="text">Open Current Operation</span>
                                    </button>
                                </form>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas {{ $active ? 'fa-door-open text-success' : 'fa-door-closed text-gray-400' }} fa-4x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100 py-2 border-left-primary">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                System Reset
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 mb-3">
                                Ready for New Day
                            </div>
                            <form action="{{ route('admin.daily_operations.reset') }}" method="POST">
                                @csrf
                                <button class="btn btn-primary btn-icon-split btn-sm" onclick="return confirm('Reset system and start a new day?')">
                                    <span class="icon text-white-50"><i class="fas fa-sync-alt"></i></span>
                                    <span class="text">Open New Daily Operation</span>
                                </button>
                            </form>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-4x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
            <h5 class="m-0 font-weight-bold text-primary">All Records</h5>
            
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
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            @endif

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
                        <div class="h4 mb-0 font-weight-bold text-success" id="totalEarnings">₱ 0.00</div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="operationsTable" width="100%" cellspacing="0">
                    <thead class="thead-light text-gray-800">
                        <tr>
                            <th>Date Created</th>                          
                            <th>Opened At</th>
                            <th>Closed At</th>
                            <th>Total Collected</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Action</th>      
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($operations as $op)
                            <tr>
                                <td class="font-weight-bold text-gray-800">{{ \Carbon\Carbon::parse($op->date)->format('F d, Y') }}</td>                          
                                <td class="small">{{ $op->opened_at->format('M d, Y - h:i A') }}</td>
                                <td class="small">{{ $op->closed_at?->format('M d, Y - h:i A') ?? '—' }}</td>
                                <td class="font-weight-bold text-success">₱{{ number_format($operationPayments[$op->id] ?? 0, 2) }}</td>
                                <td class="text-center">
                                    @php
                                        $statusBadge = match(strtolower($op->status)) {
                                            'open', 'active' => 'badge-success',
                                            'closed' => 'badge-secondary',
                                            default => 'badge-warning'
                                        };
                                    @endphp
                                    <span class="badge {{ $statusBadge }} p-2">{{ ucfirst($op->status) }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle btn btn-light btn-sm shadow-sm" href="#" role="button" id="dropdownMenuLink{{$op->id}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink{{$op->id}}">
                                            <div class="dropdown-header">Actions:</div>
                                            <a class="dropdown-item" href="{{ route('admin.daily_operations.show', $op->id) }}">
                                                <i class="fas fa-eye fa-sm fa-fw mr-2 text-gray-400"></i> View Report
                                            </a>
                                            
                                            @if(!$active || $op->id !== $active->id)
                                                <div class="dropdown-divider"></div>
                                                <form action="{{ route('admin.daily-operations.reopen', $op->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="dropdown-item text-warning" onclick="return confirm('Re-open this day?')">
                                                        <i class="fas fa-undo fa-sm fa-fw mr-2 text-gray-400"></i> Re-open Day
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
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
$(document).ready(function () {
    // Custom filter for month range (Unchanged logic)
    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        let min = $('#minMonth').val();
        let max = $('#maxMonth').val();
        let dateCreated = data[0];
        
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

    // DataTable Init
    var table = $('#operationsTable').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        order: [], // Default sort by date desc

        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        buttons: [
            {
                extend: 'csvHtml5',
                title: 'Daily Operations',
                exportOptions: { columns: ':visible' }
            },
            {
                extend: 'excelHtml5',
                title: 'Daily Operations',
                exportOptions: { columns: ':visible' }
            },
            {
                extend: 'print',
                title: '',
                exportOptions: { columns: [0,1,2,3] },
                customize: function (win) {
                    // (Keep existing print logic)
                    let total = 0;
                    $('#operationsTable').DataTable().column(3, { search: 'applied' }).data().each(function (val) {
                        total += parseFloat(val.replace(/[^0-9.-]+/g, "")) || 0;
                    });
                    let from = $('#minMonth').val();
                    let to   = $('#maxMonth').val();

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

                    $(win.document.body).prepend(`
                        <div style="text-align:center; margin-bottom:20px;">
                            <h2>Proving Grounds Sports Center</h2>
                            <h4>Monthly Report</h4>
                            <p>Date Range: ${rangeText}</p>
                        </div>
                    `);

                    $(win.document.body).append(`
                        <div style="margin-top:20px; font-size:16px;">
                            <p><strong>Total Collected:</strong> ₱ ${total.toFixed(2)}</p>
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

    // External buttons trigger
    $('#exportCsv').on('click', function() { table.button(0).trigger(); });
    $('#exportExcel').on('click', function() { table.button(1).trigger(); });
    $('#printTable').on('click', function() { table.button(2).trigger(); });
    
    // Redraw table when filters change
    $('#minMonth, #maxMonth').on('change', function () {
        table.draw();
    });

    // Update Earnings
    table.on('draw', function () {
        let total = 0;
        table.column(3, { search: 'applied' }).data().each(function (val) {
            total += parseFloat(val.replace(/[^0-9.-]+/g, "")) || 0;
        });
        $('#totalEarnings').text("₱ " + total.toFixed(2));
    });
});
</script>
@endpush