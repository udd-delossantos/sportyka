@extends('layouts.admin.app')
@section('title', 'Sporty Ka - Dashboard')

@section('content')

{{-- 1. FIXED: Defined Shared Colors here to ensure Legend and Chart ALWAYS match --}}
@php
    $chartColors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69', '#f8f9fc', '#2e59d9', '#17a673'];
@endphp

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

</style>
<div class="container-fluid report-section">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        
        <div class="d-flex align-items-center">
            <form class="form-inline mr-2">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-0 small font-weight-bold text-gray-600">Period:</span>
                    </div>
                    <input type="month" id="monthFilter" class="form-control bg-light border-0 small" value="{{ $month }}" placeholder="Select Month" aria-label="Search" aria-describedby="basic-addon2">
                </div>
            </form>
            <button id="printReport" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-print fa-sm text-white-50 mr-1"></i> Generate Report
            </button>
        </div>
    </div>

    <div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card-v2 bg-main-total text-white">
            <div class="small text-white-50 font-weight-bold text-uppercase">Total Earnings (Monthly)</div>
            <div class="h3 font-weight-bold mb-0">₱{{ number_format($monthlyEarnings, 2) }}</div>
            <div class="mt-2 small"><i class="fas fa-wallet mr-1"></i>Combined Gross</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card-v2 bg-cash text-white">
            <div class="small text-white-50 font-weight-bold text-uppercase">Cash Collected</div>
            <div class="h3 font-weight-bold mb-0">₱{{ number_format($monthlyCash, 2) }}</div>
            <div class="mt-2 small"><i class="fas fa-money-bill-wave mr-1"></i> Cashier Counter</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card-v2 bg-gcash text-white">
            <div class="small text-white-50 font-weight-bold text-uppercase">GCash Collected</div>
            <div class="h3 font-weight-bold mb-0">₱{{ number_format($monthlyGcash, 2) }}</div>
            <div class="mt-2 small"><i class="fas fa-mobile-alt mr-1"></i> Paid via QR</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card-v2 bg-upfront text-white">
            <div class="small text-white-50 font-weight-bold text-uppercase">GCash Upfront (Bookings)</div>
            <div class="h3 font-weight-bold mb-0">₱{{ number_format($confirmedBookingsTotalAmount, 2) }}</div>
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
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Sessions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $monthlySessionCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-primary"></i>
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
                                Walk-in Sessions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $monthlyWalkinCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-walking fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Booking Sessions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $monthlyBookingCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bookmark fa-2x text-info"></i>
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
                                All Confirmed Bookings</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $allBookingCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Earnings Overview (Weekly)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area mb-4" style="height: 320px;">
                        <canvas id="earningsLineChart"></canvas>
                    </div>
                    
                    <h6 class="small font-weight-bold text-gray-600 text-uppercase mb-2">Weekly Breakdown</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm text-center mb-0" width="100%">
                            <thead class="bg-light text-primary small">
                                <tr>
                                    @foreach($weeklyEarnings as $week => $amount)
                                        <th>{{ $week }}</th>
                                    @endforeach
                                    <th>TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="font-weight-bold text-gray-800">
                                    @foreach($weeklyEarnings as $week => $amount)
                                        <td>₱{{ number_format($amount, 2) }}</td>
                                    @endforeach
                                    <td class="text-success">₱{{ number_format($weeklyEarnings->sum(), 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Earnings by Court</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="earningsDonutChart"></canvas>
                    </div>
                    
                    <div class="mt-4 text-center small">
                        <ul class="list-group list-group-flush text-left">
                            @foreach($earningsPerCourt as $court => $amount)
                                {{-- 2. FIXED: Use the shared array with Modulo to cycle colors safely --}}
                                <li class="list-group-item d-flex justify-content-between align-items-center px-2 py-2">
                                    <span>
                                        <i class="fas fa-circle mr-2" style="color: {{ $chartColors[$loop->index % count($chartColors)] }}"></i>
                                        {{ $court }}
                                    </span>
                                    <span class="font-weight-bold">₱{{ number_format($amount, 2) }}</span>
                                </li>
                            @endforeach
                            <li class="list-group-item d-flex justify-content-between align-items-center bg-light px-2 py-2">
                                <span class="font-weight-bold text-gray-800">TOTAL</span>
                                <span class="font-weight-bold text-success">₱{{ number_format($earningsPerCourt->sum(), 2) }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Handle month change
        document.getElementById('monthFilter').addEventListener('change', function() {
            const month = this.value;
            window.location.href = "{{ route('admin.dashboard') }}" + "?month=" + month;
        });

        // Print functionality - styled like DataTables print
        document.getElementById('printReport').addEventListener('click', function() {
            const month = document.getElementById('monthFilter').value || "{{ now()->format('Y-m') }}";

            // Format YYYY-MM into "Month YYYY"
            function formatMonth(ym) {
                if (!ym) return '';
                let [year, month] = ym.split("-");
                let d = new Date(year, month - 1);
                return d.toLocaleString('default', { month: 'long', year: 'numeric' });
            }

            let monthText = formatMonth(month);

            // Build printable content
            let printContent = `
                <div style="font-family: 'Nunito', Arial, sans-serif; padding: 40px;">
                    <div style="text-align:center; margin-bottom:40px;">
                        <h1 style="margin:0; color:#4e73df;">Proving Grounds Sports Center</h1>
                        <h3 style="margin:0; color:#5a5c69;">Monthly Financial Report</h3>
                        <p style="margin:5px 0; color:#858796;">Period: <strong>${monthText}</strong></p>
                    </div>

                    <h4 style="border-bottom: 2px solid #4e73df; padding-bottom: 5px; color:#4e73df; margin-bottom: 15px;">Financial Summary</h4>
                    <table style="width:100%; border-collapse: collapse; margin-bottom:30px; font-size:14px;">
                        <tr style="background-color: #f8f9fc;">
                            <th style="text-align:left; padding:12px; border:1px solid #e3e6f0;">Description</th>
                            <th style="text-align:right; padding:12px; border:1px solid #e3e6f0;">Amount / Count</th>
                        </tr>
                        <tr>
                            <td style="padding:10px; border:1px solid #e3e6f0;">Total Earnings</td>
                            <td style="padding:10px; border:1px solid #e3e6f0; text-align:right; font-weight:bold;">₱{{ number_format($monthlyEarnings, 2) }}</td>
                        </tr>
                        <tr>
                            <td style="padding:10px; border:1px solid #e3e6f0;">Cash Collected</td>
                            <td style="padding:10px; border:1px solid #e3e6f0; text-align:right;">₱{{ number_format($monthlyCash, 2) }}</td>
                        </tr>
                        <tr>
                            <td style="padding:10px; border:1px solid #e3e6f0;">GCash Collected</td>
                            <td style="padding:10px; border:1px solid #e3e6f0; text-align:right;">₱{{ number_format($monthlyGcash, 2) }}</td>
                        </tr>
                        <tr>
                            <td style="padding:10px; border:1px solid #e3e6f0;">Confirmed Bookings (Upfront)</td>
                            <td style="padding:10px; border:1px solid #e3e6f0; text-align:right;">₱{{ number_format($confirmedBookingsTotalAmount ?? 0, 2) }}</td>
                        </tr>
                    </table>

                    <h4 style="border-bottom: 2px solid #1cc88a; padding-bottom: 5px; color:#1cc88a; margin-bottom: 15px;">Operational Stats</h4>
                    <table style="width:100%; border-collapse: collapse; margin-bottom:20px; font-size:14px;">
                        <tr style="background-color: #f8f9fc;">
                            <th style="text-align:left; padding:12px; border:1px solid #e3e6f0;">Metric</th>
                            <th style="text-align:right; padding:12px; border:1px solid #e3e6f0;">Count</th>
                        </tr>
                        <tr>
                            <td style="padding:10px; border:1px solid #e3e6f0;">Total Sessions (Completed)</td>
                            <td style="padding:10px; border:1px solid #e3e6f0; text-align:right;">{{ $monthlySessionCount }}</td>
                        </tr>
                        <tr>
                            <td style="padding:10px; border:1px solid #e3e6f0;">Walk-in Sessions</td>
                            <td style="padding:10px; border:1px solid #e3e6f0; text-align:right;">{{ $monthlyWalkinCount }}</td>
                        </tr>
                        <tr>
                            <td style="padding:10px; border:1px solid #e3e6f0;">Booking Sessions</td>
                            <td style="padding:10px; border:1px solid #e3e6f0; text-align:right;">{{ $monthlyBookingCount }}</td>
                        </tr>
                        <tr>
                            <td style="padding:10px; border:1px solid #e3e6f0;">Total Confirmed Bookings</td>
                            <td style="padding:10px; border:1px solid #e3e6f0; text-align:right;">{{ $allBookingCount }}</td>
                        </tr>
                    </table>

                    <div style="margin-top:40px; text-align:right; font-size:18px; border-top: 2px solid #5a5c69; padding-top:10px;">
                        <p><strong>Grand Total Collected:</strong> <span style="color:#1cc88a;">₱{{ number_format($monthlyEarnings, 2) }}</span></p>
                    </div>

                    <div style="margin-top:50px; text-align:center; font-size:12px; color:#858796;">
                        <p>Generated by Sporty Ka? Management System</p>
                        <p>Printed on: ${new Date().toLocaleString()}</p>
                    </div>
                </div>
            `;

            // Open new window and print
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                <head>
                    <title>Monthly Report - ${monthText}</title>
                    <style>@page { size: A4; margin: 0; }</style>
                </head>
                <body style="margin:0;">${printContent}</body>
                </html>
            `);
            printWindow.document.close();
            setTimeout(function() {
                printWindow.print();
            }, 500);
        });

        // Config for Charts styling
        Chart.defaults.font.family = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
        Chart.defaults.color = '#858796';

        // Line Chart
        const ctxLine = document.getElementById('earningsLineChart').getContext('2d');
        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: {!! json_encode($weeklyEarnings->keys()) !!},
                datasets: [{
                    label: 'Earnings (₱)',
                    data: {!! json_encode($weeklyEarnings->values()) !!},
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78,115,223,0.05)',
                    pointRadius: 3,
                    pointBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointBorderColor: "rgba(78, 115, 223, 1)",
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    fill: true,
                    // 3. FIXED: Reduced tension from 0.3 to 0.1 to prevent line "swinging" into next week visually
                    tension: 0.1 
                }]
            },
            options: {
                maintainAspectRatio: false,
                layout: { padding: { left: 10, right: 25, top: 25, bottom: 0 } },
                plugins: {
                    legend: { display: false },
                },
                scales: {
                    x: { grid: { display: false, drawBorder: false }, ticks: { maxTicksLimit: 7 } },
                    y: { 
                        ticks: { 
                            maxTicksLimit: 5, padding: 10, 
                            callback: function(value) { return '₱' + value; } 
                        },
                        grid: { color: "rgb(234, 236, 244)", zeroLineColor: "rgb(234, 236, 244)", drawBorder: false, borderDash: [2], zeroLineBorderDash: [2] }
                    }
                }
            }
        });

        // Donut Chart
        const ctxDonut = document.getElementById('earningsDonutChart').getContext('2d');
        new Chart(ctxDonut, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($earningsPerCourt->keys()) !!},
                datasets: [{
                    data: {!! json_encode($earningsPerCourt->values()) !!},
                    // 4. FIXED: Use the exact same PHP array for chart colors to match Legend
                    backgroundColor: {!! json_encode($chartColors) !!},
                    hoverBackgroundColor: {!! json_encode($chartColors) !!},
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                },
                cutout: '80%',
            }
        });
    });
</script>
@endpush