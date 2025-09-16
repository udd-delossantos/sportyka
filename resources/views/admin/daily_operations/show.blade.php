@extends('layouts.admin.app')

@section('content')
<div class="container">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h4 class="m-0 font-weight-bold text-primary">
                Daily Report – {{ \Carbon\Carbon::parse($operation->date)->format('F j, Y') }}

            </h4>
            
        </div>
        <div class="card-body">

            <table class="table table-bordered">
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
                        <td><strong>OVERALL:</strong></td>
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
        <div class="card-footer text-left">
            <a href="{{ route('admin.daily_operations.index') }}" class="btn btn-secondary btn-sm">Back</a>

        </div>
    </div>
</div>
@endsection
