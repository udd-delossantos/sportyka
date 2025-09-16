@extends('layouts.customer.app')

@section('content')
<div class="container">
    <h2>Your Bookings</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('customer.bookings.create') }}" class="btn btn-primary mb-3">New Booking</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Court</th>
                <th>Date</th>
                <th>Time</th>
                <th>Duration</th>
                <th>Transaction No.</th>
                <th>Amount</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($bookings as $booking)
                <tr>
                    <td>{{ $booking->court->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('F d, Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}</td>
                    <td>{{ $booking->expected_hours }}h {{ $booking->expected_minutes }}m</td>
                    <td>{{ $booking->transaction_no ?? '—' }}</td>
                    <td>₱{{ number_format($booking->amount, 2) }}</td>
                    <td class="text-center text-light">
                        @if ($booking->status === 'pending')
                            <span class="badge bg-warning">Pending</span>
                        @elseif ($booking->status === 'confirmed')
                            <span class="badge bg-success">Confirmed</span>
                        @elseif ($booking->status === 'cancelled')
                            <span class="badge bg-danger">Cancelled</span>
                        @elseif ($booking->status === 'completed')
                            <span class="badge bg-info">Completed</span>
                        @else
                            <span class="badge bg-secondary">{{ ucfirst($booking->status) }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center">No bookings yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
