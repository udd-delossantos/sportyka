@extends('layouts.staff.app')

@section('title', 'Staff Dashboard')

@section('content')
<style>
    /* Professional Card Styling */
    .report-card { border-radius: 12px; border: none; }
    .stat-card-v2 {
        border-radius: 12px;
        padding: 1.25rem;
        color: white;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        transition: transform 0.2s;
    }

    
    .bg-main-total { background: linear-gradient(45deg, #4e73df, #224abe); }
    .bg-cash { background: linear-gradient(45deg, #1cc88a, #13855c); }
    .bg-gcash { background: linear-gradient(45deg, #36b9cc, #258391); }
    .bg-upfront { background: linear-gradient(45deg, #f6c23e, #dda20a); }

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

    /* Print logic */
    @media print {
        .btn, .sidebar, .navbar, .card-header button { display: none !important; }
        .stat-card-v2 { color: black !important; border: 1px solid #eee !important; background: none !important; box-shadow: none !important; }
        .card { border: none !important; }
    }
</style>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card-v2 bg-main-total text-white">
                <div class="small text-white-50 font-weight-bold text-uppercase">Total Collected (Today)</div>
                <div class="h3 font-weight-bold mb-0">₱{{ number_format($overallPayments, 2) }}</div>
                <div class="mt-2 small"><i class="fas fa-wallet mr-1"></i> Combined Gross</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card-v2 bg-cash text-white">
                <div class="small text-white-50 font-weight-bold text-uppercase">Cash in Hand</div>
                <div class="h3 font-weight-bold mb-0">₱{{ number_format($overallCash, 2) }}</div>
                <div class="mt-2 small"><i class="fas fa-money-bill-wave mr-1"></i> Cashier Counter</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card-v2 bg-gcash text-white">
                <div class="small text-white-50 font-weight-bold text-uppercase">GCash (Cashier)</div>
                <div class="h3 font-weight-bold mb-0">₱{{ number_format($overallGcash, 2) }}</div>
                <div class="mt-2 small"><i class="fas fa-mobile-alt mr-1"></i> Paid via QR</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card-v2 bg-upfront text-white">
                <div class="small text-white-50 font-weight-bold text-uppercase">GCash (Upfront)</div>
                <div class="h3 font-weight-bold mb-0">₱{{ number_format($overallConfirmed, 2) }}</div>
                <div class="mt-2 small"><i class="fas fa-receipt mr-1"></i> Online Bookings</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Sessions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $overallBookingCount + $overallWalkinCount }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-calendar-check fa-2x text-primary"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Walk-ins</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $overallWalkinCount }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-walking fa-2x text-success"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Bookings</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $overallBookingCount }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-book fa-2x text-info"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Approved Today</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $confirmedBookingCount }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-clipboard-check fa-2x text-warning"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow report-card mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white">
            <h5 class="m-0 font-weight-bold text-primary">
                @if($operation)
                    Daily Operations: {{ \Carbon\Carbon::parse($operation->date)->format('F j, Y') }}
                @else
                    No Active Operation Found
                @endif
            </h5>
            <button id="printTable" class="btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-print fa-sm text-white-50"></i> Print Report
            </button>
        </div>
        <div class="card-body" id="reportContent">
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

            <div class="row mt-4">
                <div class="col-md-6">
                    <h6 class="font-weight-bold text-primary mb-3">Operational Status</h6>
                    <div class="row">
                        <div class="col-sm-3 mb-3">
                            <div class="card border-left-primary shadow-sm py-2 bg-light">
                                <div class="card-body py-1">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Ongoing Sessions</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <span class="spinner-grow spinner-grow-sm text-success mr-1" role="status" style="width: 10px; height: 10px;"></span>
                                                {{ $ongoingSessionCount ?? 0 }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-play-circle fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3 mb-3">
                            <div class="card border-left-success shadow-sm py-2 bg-light">
                                <div class="card-body py-1">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Waiting Customers</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $waitingQueuesCount ?? 0 }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-users fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3 mb-3">
                            <div class="card border-left-info shadow-sm py-2 bg-light">
                                <div class="card-body py-1">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Pending Bookings</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $pendingBookingCount ?? 0 }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-3 mb-3">
                            <div class="card border-left-warning shadow-sm py-2 bg-light">
                                <div class="card-body py-1">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Pending Payments</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $pendingPaymentCount ?? 0 }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-muted"><i class="fas fa-info-circle"></i> These metrics represent real-time status of today's court usage.</p>
                </div>
                <div class="col-md-6">
                    <div class="card bg-light border-0">
                        <div class="card-body py-2">
                            <h6 class="font-weight-bold text-primary mb-3">Revenue Summary</h6>
                            <div class="d-flex justify-content-between mb-2 small">
                                <span>Total Counter Collection (Cash + GCash):</span>
                                <span class="font-weight-bold">₱{{ number_format($overallCash + $overallGcash, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 small">
                                <span>Total Upfront (Online):</span>
                                <span class="font-weight-bold">₱{{ number_format($overallConfirmed, 2) }}</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between text-dark font-weight-bold h5">
                                <span>DAILY TOTAL:</span>
                                <span>₱{{ number_format($overallPayments, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Print Header Template --}}
<div id="printHeader" class="d-none">
    <div style="text-align:center; padding-bottom: 20px; border-bottom: 2px solid #4e73df; margin-bottom: 20px;">
        <h2 style="color: #4e73df; margin: 0;">Proving Grounds Sports Center</h2>
        <h4 style="color: #5a5c69; margin: 5px 0;">Staff Operations Report</h4>
        <p style="margin: 0;">Date: {{ $operation ? \Carbon\Carbon::parse($operation->date)->format('F j, Y') : 'N/A' }}</p>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.getElementById('printTable').addEventListener('click', function () {
    const printHeader = document.getElementById('printHeader').innerHTML;
    const reportTable = document.getElementById('reportContent').innerHTML;
    
    const originalBody = document.body.innerHTML;
    
    document.body.innerHTML = `
        <div style="padding: 40px;">
            ${printHeader}
            ${reportTable}
            <div style="margin-top: 50px; border-top: 1px solid #eee; padding-top: 10px;">
                <p><strong>System Signature:</strong> Sporty Ka? Management System</p>
                <p><strong>Printed By:</strong> Staff User</p>
            </div>
        </div>
    `;
    
    window.print();
    document.body.innerHTML = originalBody;
    window.location.reload();
});
</script>
@endpush