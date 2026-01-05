@extends('layouts.admin.app')

@section('content')
<style>
    /* Custom Styling for the Report */
    .report-card { border-radius: 12px; border: none; }
    .stat-card {
        border-radius: 10px;
        padding: 20px;
        color: white;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .bg-revenue { background: linear-gradient(45deg, #1cc88a, #13855c); }
    .bg-sessions { background: linear-gradient(45deg, #4e73df, #224abe); }
    .bg-payments { background: linear-gradient(45deg, #f6c23e, #dda20a); }
    
    /* Table Enhancements */
    .table thead th {
        background-color: #f8f9fc;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        color: #4e73df;
    }
    .badge-booking { background-color: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe; }
    .badge-walkin { background-color: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    /* Print Specific Logic */
    @media print {
        .btn, .card-footer, .sidebar, .navbar, #printTable { display: none !important; }
        .card { border: none !important; box-shadow: none !important; }
        .container-fluid { width: 100% !important; padding: 0 !important; }
        .stat-card { color: black !important; border: 1px solid #ddd !important; box-shadow: none !important; background: none !important; }
    }
</style>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Daily Operations Report</h1>
        <div>
            <a href="{{ route('admin.daily_operations.index') }}" class="btn btn-secondary btn-sm mr-2">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <button id="printTable" class="btn btn-primary btn-sm shadow-sm">
                <i class="fas fa-print fa-sm text-white-50"></i> Generate Printout
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-4 col-md-6">
            <div class="stat-card bg-revenue">
                <div class="small text-white-50 uppercase font-weight-bold">Overall Total Revenue</div>
                <div class="h2 font-weight-bold mb-0">₱{{ number_format($overallPayments, 2) }}</div>
                <div class="mt-2 small"><i class="fas fa-wallet"></i> Combined Cash & GCash</div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="stat-card bg-sessions">
                <div class="small text-white-50 uppercase font-weight-bold">Total Sessions</div>
                <div class="h2 font-weight-bold mb-0">{{ $overallBookingCount + $overallWalkinCount }}</div>
                <div class="mt-2 small">
                    <span class="mr-2 text-white font-weight-bold">{{ $overallBookingCount }} Bookings</span>
                    <span class="text-white font-weight-bold">{{ $overallWalkinCount }} Walk-ins</span>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="stat-card bg-payments">
                <div class="small text-white-50 uppercase font-weight-bold">Cashier Collections</div>
                <div class="h2 font-weight-bold mb-0">₱{{ number_format($overallCash + $overallGcash, 2) }}</div>
                <div class="mt-2 small"><i class="fas fa-cash-register"></i> Total collected at counter</div>
            </div>
        </div>
    </div>

    <div class="card shadow report-card mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Daily Report for {{ \Carbon\Carbon::parse($operation->date)->format('F j, Y') }}
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle border">
                    <thead class="text-center">
                        <tr>
                            <th class="text-left">Court</th>
                            <th>Bookings</th>
                            <th>Walk-ins</th>
                            <th class="border-right">Total Sessions</th>
                            <th>Cash</th>
                            <th>GCash (Counter)</th>
                            <th>GCash (Upfront)</th>
                            <th class="bg-primary text-white">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @foreach($report as $row)
                        <tr>
                            <td class="text-left font-weight-bold text-dark">{{ $row['court_name'] }}</td>
                            <td><span class="badge badge-booking px-2 py-1">{{ $row['booking_count'] }}</span></td>
                            <td><span class="badge badge-walkin px-2 py-1">{{ $row['walkin_count'] }}</span></td>
                            <td class="border-right font-weight-bold">{{ $row['walkin_count'] + $row['booking_count'] }}</td>
                            <td>₱{{ number_format($row['cash_total'], 2) }}</td>
                            <td>₱{{ number_format($row['gcash_total'], 2) }}</td>
                            <td>₱{{ number_format($row['confirmed_bookings_total'], 2) }}</td>
                            <td class="table-primary font-weight-bold text-primary">
                                ₱{{ number_format($row['cash_total'] + $row['gcash_total'] + $row['confirmed_bookings_total'], 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-light font-weight-bold text-center">
                        <tr>
                            <td class="text-left">TOTALS</td>
                            <td>{{ $overallBookingCount }}</td>
                            <td>{{ $overallWalkinCount }}</td>
                            <td class="border-right">{{ $overallBookingCount + $overallWalkinCount }}</td>
                            <td>₱{{ number_format($overallCash, 2) }}</td>
                            <td>₱{{ number_format($overallGcash, 2) }}</td>
                            <td>₱{{ number_format($overallConfirmed, 2) }}</td>
                            <td class="bg-primary text-white">₱{{ number_format($overallPayments, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Hidden Print Header --}}
<div id="print-header" class="d-none">
    <div style="text-align:center; margin-bottom:30px; border-bottom: 2px solid #333; padding-bottom: 10px;">
        <h1 style="margin:0;">Proving Grounds Sports Center</h1>
        <p style="margin:0;">Official Daily Operations Report</p>
        <h3 style="margin:5px 0;">{{ \Carbon\Carbon::parse($operation->date)->format('F j, Y') }}</h3>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.getElementById("printTable").addEventListener("click", function () {
        // We use window.print() and CSS @media rules instead of breaking the DOM
        const printHeader = document.getElementById('print-header').innerHTML;
        const mainContent = document.querySelector('.container-fluid').innerHTML;
        
        const originalBody = document.body.innerHTML;
        
        document.body.innerHTML = `<div class="p-5">${printHeader}${mainContent}</div>`;
        window.print();
        document.body.innerHTML = originalBody;
        window.location.reload(); // Restores event listeners
    });
</script>
@endpush