@extends('layouts.admin.app') @section('title', 'Sporty Ka') @section('content')
<div class="container-fluid report-section">
    
    <div class="px-0">
        <div class="card-body d-flex justify-content-between align-items-center px-0 pt-0">
            <h2 class="mb-0 text-primary"><strong>Dashboard</strong></h2>
           <div class="col-md-3 px-0">
    <input type="month" id="monthFilter" class="form-control" 
        value="{{ $month }}" placeholder="Select Month" />
</div>

            <!-- Print Button -->
            <button id="printReport" class="btn btn-sm btn-primary ms-2">
                <i class="fas fa-print"></i> Print Report
            </button>
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
                                Total Earnings
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-light">₱{{ number_format($monthlyEarnings, 2) }}</div>
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
                                Cash Collected
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-light">₱{{ number_format($monthlyCash, 2) }}</div>
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
                                GCash Collected
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-light">₱{{ number_format($monthlyGcash, 2) }}</div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $monthlySessionCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $monthlyWalkinCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $monthlyBookingCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $allBookingCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row mb-4">
        <!-- Line Chart -->
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header"><strong>Earnings Overview (Weekly)</strong></div>
                <div class="card-body text-center">
                    <canvas id="earningsLineChart"></canvas>

                    <!-- Weekly values inline with spacing -->
                    <div class="mt-3 d-flex flex-wrap justify-content-center">
                        @foreach($weeklyEarnings as $week => $amount)
                        <span class="mx-3 my-1"> <strong>{{ $week }}</strong>: ₱{{ number_format($amount, 2) }} </span>
                        @endforeach
                    </div>

                    <hr />
                    <strong>Total: ₱{{ number_format($weeklyEarnings->sum(), 2) }}</strong>
                </div>
            </div>
        </div>

        <!-- Donut Chart -->
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header"><strong>Earnings by Court</strong></div>
                <div class="card-body text-center">
                    <canvas id="earningsDonutChart"></canvas>
                    <div class="mt-3 text-start">
                        @foreach($earningsPerCourt as $court => $amount)
                        <div><strong>{{ $court }}</strong>: ₱{{ number_format($amount, 2) }}</div>
                        @endforeach
                    </div>
                    <hr />
                    <strong>Total: ₱{{ number_format($earningsPerCourt->sum(), 2) }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection @push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Handle month change
        document.getElementById('monthFilter').addEventListener('change', function() {
            const month = this.value;
            window.location.href = "{{ route('admin.dashboard') }}" + "?month=" + month;
        });

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
                    fill: true,
                    tension: 0.3
                }]
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
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b']
                }]
            }
        });
    });
</script>
@endpush
@push('styles')
<style>
    @media print {
        /* Hide everything by default */
        body * {
            visibility: hidden;
        }

        /* Show only the report section */
        .report-section, .report-section * {
            visibility: visible;
        }

        /* Ensure report takes full page */
        .report-section {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

        /* Optional: hide the print button itself */
        #printReport {
            display: none !important;
        }
    }
</style>
@endpush

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
                <div style="font-family: Arial, sans-serif; padding: 20px;">
                    <div style="text-align:center; margin-bottom:20px;">
                        <h2 style="margin:0;">Proving Grounds Sports Center</h2>
                        <h4 style="margin:0;">Monthly Report</h4>
                        <p style="margin:5px 0;">Report for: ${monthText}</p>
                    </div>

                    <table style="width:100%; border-collapse: collapse; margin-bottom:20px; font-size:14px;">
                        <tr><th style="text-align:left; padding:6px; border-bottom:1px solid #ddd;">Total Earnings</th><td style="padding:6px; border-bottom:1px solid #ddd;">₱{{ number_format($monthlyEarnings, 2) }}</td></tr>
                        <tr><th style="text-align:left; padding:6px; border-bottom:1px solid #ddd;">Cash Collected</th><td style="padding:6px; border-bottom:1px solid #ddd;">₱{{ number_format($monthlyCash, 2) }}</td></tr>
                        <tr><th style="text-align:left; padding:6px; border-bottom:1px solid #ddd;">GCash Collected</th><td style="padding:6px; border-bottom:1px solid #ddd;">₱{{ number_format($monthlyGcash, 2) }}</td></tr>
                        <tr><th style="text-align:left; padding:6px; border-bottom:1px solid #ddd;">Total Sessions (Completed)</th><td style="padding:6px; border-bottom:1px solid #ddd;">{{ $monthlySessionCount }}</td></tr>
                        <tr><th style="text-align:left; padding:6px; border-bottom:1px solid #ddd;">Walk-in Sessions (Completed)</th><td style="padding:6px; border-bottom:1px solid #ddd;">{{ $monthlyWalkinCount }}</td></tr>
                        <tr><th style="text-align:left; padding:6px; border-bottom:1px solid #ddd;">Booking Sessions (Completed)</th><td style="padding:6px; border-bottom:1px solid #ddd;">{{ $monthlyBookingCount }}</td></tr>
                        <tr><th style="text-align:left; padding:6px; border-bottom:1px solid #ddd;">All Bookings (Confirmed, Completed, Ongoing, Voided)</th><td style="padding:6px; border-bottom:1px solid #ddd;">{{ $allBookingCount }}</td></tr>
                    </table>

                    <div style="margin-top:20px; font-size:16px;">
                        <p><strong>Total Collected:</strong> ₱{{ number_format($monthlyEarnings, 2) }}</p>
                    </div>

                    <div style="margin-top:30px; text-align:center; font-size:12px;">
                        <p>Generated by Sporty Ka? Management System</p>
                        <p>Printed on: ${new Date().toLocaleString()}</p>
                    </div>
                </div>
            `;

            // Open new window and print
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                <head><title>Monthly Report</title></head>
                <body>${printContent}</body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
        });

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
                    fill: true,
                    tension: 0.3
                }]
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
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b']
                }]
            }
        });
    });
</script>
@endpush
