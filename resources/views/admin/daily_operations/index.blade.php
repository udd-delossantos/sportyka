@extends('layouts.admin.app')
@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="mb-0 text-primary"><strong>Daily Operations</strong></h2>
        </div>
        <div class="col-md-2">
            @if($active)
                <form action="{{ route('admin.daily_operations.close', $active->id) }}" method="POST" class="w-100">
                    @csrf
                    <button 
                        type="submit" 
                        class="btn btn-danger w-100" 
                        onclick="return confirm('Close current operation?')"
                    >
                        Close Today's Operation
                    </button>
                </form>
            @else
                <button class="btn btn-danger w-100" disabled>
                    Close Today's Operation
                </button>
            @endif
        </div>

        <div class="col-md-2">
            <form action="{{ route('admin.daily_operations.open') }}" method="POST" class="w-100">
                @csrf
                <button class="btn btn-success w-100">Open Today's Operation</button>
            </form>
        </div>
        <div class="col-md-2">
            <form action="{{ route('admin.daily_operations.reset') }}" method="POST" class="w-100">
                @csrf
                <button class="btn btn-primary w-100" onclick="return confirm('Reset system and start a new day?')">
                    Open New Day
                </button>
            </form>
        </div>
    </div>


    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><strong>All Records</strong></h4>
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
           <div class="row mb-3 align-items-end">
                <div class="col-md-3">
                    <label for="minMonth">From Month:</label>
                    <input type="month" id="minMonth" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="maxMonth">To Month:</label>
                    <input type="month" id="maxMonth" class="form-control">
                </div>
                <div class="col-md-6">
                    <p>Total Earnings (Filtered Date):</p><h4 class="mb-0">
                        
                        <span id="totalEarnings" class="text-success">₱ 0.00</span>
                    </h4>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="operationsTable">
                    <thead>
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
                                <td>{{ \Carbon\Carbon::parse($op->date)->format('F d, Y') }}</td>                          
                                <td>{{ $op->opened_at->format('M d, Y - h:i A') }}</td>
                                <td>{{ $op->closed_at?->format('M d, Y - h:i A') ?? '—' }}</td>
                                <td>₱{{ number_format($operationPayments[$op->id] ?? 0, 2) }}</td>
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

@push('scripts')
<script>
$(document).ready(function () {
    // Custom filter for month range
    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        let min = $('#minMonth').val();
        let max = $('#maxMonth').val();
        let dateCreated = data[0]; // First column "Date Created"
        
        if (!dateCreated) return true;

        // Convert to YYYY-MM format for comparison
        let dateObj = new Date(dateCreated);
        let dateMonth = dateObj.getFullYear() + "-" + 
                        String(dateObj.getMonth() + 1).padStart(2, '0');

        if ((min === "" || dateMonth >= min) &&
            (max === "" || dateMonth <= max)) {
            return true;
        }
        return false;
    });

    var table = $('#operationsTable').DataTable({
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
            exportOptions: {
                columns: [0,1,2,3] // Exclude Status (4) and Action (5)
            },
            customize: function (win) {
                // Calculate total earnings of filtered table
                let total = 0;
                $('#operationsTable').DataTable().column(3, { search: 'applied' }).data().each(function (val) {
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

                // Optional: make table cleaner in print
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
