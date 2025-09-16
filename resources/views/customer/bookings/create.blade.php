@extends('layouts.customer.app')

@section('content')
<div class="container">
    <h2>Book a Court</h2>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('customer.bookings.store') }}" method="POST">
    @csrf

    <div class="mb-3">
        <label>Court</label>
        <select name="court_id" class="form-control" required id="courtSelect">
            @foreach($courts as $court)
                <option value="{{ $court->id }}" data-rate="{{ $court->hourly_rate }}">
                    {{ $court->name }} (₱{{ $court->hourly_rate }}/hr)
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label>Date</label>
        <input type="date" name="booking_date" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Start Time</label>
        <input type="time" name="start_time" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>End Time</label>
        <input type="time" name="end_time" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Expected Duration (HH:MM)</label>
        <input type="text" name="expected_duration" class="form-control" placeholder="e.g. 1:30" pattern="^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$" required id="expectedDuration">
    </div>

    <div class="mb-3">
        <label>GCash Transaction No. (13 digits)</label>
        <input type="text" name="transaction_no" class="form-control" maxlength="13" minlength="13" required>
    </div>

    <div class="mb-3">
        <label>50% Down Payment</label>
        <input type="text" id="computedAmount" class="form-control" readonly>
    </div>

    <button type="submit" class="btn btn-success">Submit Booking</button>
</form>

<script>
    const durationInput = document.getElementById('expectedDuration');
    const rateInput = document.getElementById('courtSelect');
    const computedAmount = document.getElementById('computedAmount');

    function computeAmount() {
        const duration = durationInput.value;
        const selected = rateInput.options[rateInput.selectedIndex];
        const rate = parseFloat(selected.getAttribute('data-rate'));
        if (!duration || isNaN(rate)) return;

        const [hours, minutes] = duration.split(':').map(Number);
        const totalMinutes = (hours * 60) + minutes;
        const total = (rate / 60) * totalMinutes * 0.5;

        computedAmount.value = '₱' + total.toFixed(2);
    }

    durationInput.addEventListener('input', computeAmount);
    rateInput.addEventListener('change', computeAmount);
</script>

</div>
@endsection
