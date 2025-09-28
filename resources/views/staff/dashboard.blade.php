@extends('layouts.staff.app')

@section('title', 'Staff Dashboard')
@section('content')
<div class="container-fluid">
    <div class="px-0">
        <div class="card-body d-flex justify-content-between align-items-center px-0 pt-0">
            <h2 class="mb-0 text-primary"><strong>Dashboard</strong></h2>
            <!-- Print Button -->
        </div>
    </div>

    <!-- Cash / GCash -->
    <div class="row mb4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card bg-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-light text-uppercase mb-1">
                                Total Collected (Today)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-light">₱{{ number_format($overallPayments, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-light"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card bg-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-light text-uppercase mb-1">
                                Cash Collected (Today)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-light">₱{{ number_format($overallCash, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill fa-2x text-light"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card bg-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-light text-uppercase mb-1">
                                GCash Collected (Today)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-light">₱{{ number_format($overallGcash, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-mobile-screen fa-2x text-light"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <!-- Earnings -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Sessions (Completed)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $overallBookingCount + $overallWalkinCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Walk-ins & Queues -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Walk-in Sessions (Completed)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $overallWalkinCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bookings -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Booking Sessions (Completed)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $overallBookingCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Confirmed Bookings -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Confirmed Bookings
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $confirmedBookingCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

 
        

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
            @if($operation)
                <h4 class="mb-0 text-primary">
                    <strong>Daily Report – {{ \Carbon\Carbon::parse($operation->date)->format('F j, Y') }}</strong>
                </h4>
            @else
                <h4 class="mb-0 text-primary">
                    <strong>No Active Daily Operation</strong>
                </h4>
            @endif

            <!-- Export buttons -->
            <div>
                <button id="printTable" class="btn btn-primary btn-sm">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>
            <div class="card-body" id="reportSection">
                <table class="table table-bordered" >
                    <thead>
                        <tr>
                            <th>COURT</th>
                            <th colspan="2" class="text-center">SESSION COUNT</th>
                            <th colspan="3" class="text-center">TOTAL COLLECTED</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($report as $row)
                        <tr>
                            <td>{{ $row['court_name'] }}</td>
                            <td>
                                
                                BOOKING: {{ $row['booking_count'] }} 
                                
                            </td>
                            <td>
                                WALK-IN: {{ $row['walkin_count'] }}

                            </td>
                            <td>
                                
                            CASH: ₱{{ number_format($row['cash_total'], 2) }} 
                            

                            </td>
                            <td>
                                GCASH: ₱{{ number_format($row['gcash_total'], 2) }} 
                            </td>
                            <td>
                                TOTAL: ₱{{ number_format($row['cash_total'] + $row['gcash_total'], 2) }}
                            </td>
                        </tr>
                        @endforeach

                        <!-- Overall Totals -->
                        <tr class="fw-bold bg-light">
                            <td><strong>TOTAL:</strong></td>
                            <td colspan="2" class="text-center"><strong>CUSTOMERS: {{ $overallBookingCount + $overallWalkinCount }}</strong></td>
                            <td colspan="3" class="text-center"><strong>COLLECTED: ₱{{ number_format($overallPayments, 2) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
                 <div class="mt-4">
                    <h5>Summary (Today)</h5>
                    <ul class="list-group">
                        <li class="list-group-item">TOTAL CASH: ₱{{ number_format($overallCash, 2) }}</li>
                        <li class="list-group-item">TOTAL GCASH: ₱{{ number_format($overallGcash, 2) }}</li>
                        <li class="list-group-item bg-light"><strong>OVERALL TOTAL: ₱{{ number_format($overallPayments, 2) }}</strong></li>
                    </ul>
                </div>


            </div>

        </div>
 

</div>
   
@endsection
@push('scripts')
<script>
document.getElementById('printTable').addEventListener('click', function () {
    // target the last .card-body (where your table + summary lives)
    var reportSection = document.querySelectorAll('.card-body');
    var printTarget = reportSection[reportSection.length - 1].innerHTML;

    var printContents = `
        <div style="text-align:center; margin-bottom:20px;">
            <h2>Proving Grounds Sports Center</h2>
            <h4>Daily Operations Report</h4>
            <p>
                Date: 
                @if($operation)
                    {{ \Carbon\Carbon::parse($operation->date)->format('F j, Y') }}
                @else
                    N/A
                @endif
            </p>
        </div>
        ${printTarget}
        <div style="margin-top:30px; text-align:left;">
            <p><strong>Generated by Sporty Ka? Management System</strong></p>
        </div>
    `;


    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    location.reload(); // reload so styles and scripts come back
});
</script>

@endpush



