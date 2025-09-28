@extends('layouts.staff.app')

@section('content')
<div class="container">
    <form method="POST" action="{{ route('staff.game_sessions.store') }}">
        <div class="card shadow mb4">
            <div class="card-header pb-0">
                <h5><strong>Create Session</strong></h5>
            </div>
            <div class="card-body">
                @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
                @endif
                @csrf

                <div class="mb-3">
                    <label for="court_id">Court</label>
                    <select name="court_id" id="court_id" class="form-control" required>
                        @foreach($courts as $court)
                            @if($court->status === 'available')
                                <option value="{{ $court->id }}" data-rate="{{ $court->hourly_rate }}">
                                    {{ $court->name }} (₱{{ $court->hourly_rate }}/hr)
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="customer_name">Customer Name</label>
                    <input type="text" name="customer_name" class="form-control" required />
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="hours">Hours</label>
                        <select name="hours" id="hours" class="form-control" required>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="minutes">Minutes</label>
                        <select name="minutes" id="minutes" class="form-control" required>
                            <option value="0">0</option>
                            <option value="30">30</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 text-center">
                        <h5 class="mb-0">Amount: <span id="amountDisplay" class="text-success">₱0.00</span></h5>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-success btn-sm">Create Session</button>
                <a href="{{ route('staff.game_sessions.index') }}" class="btn btn-secondary btn-sm">Back</a>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const courtSelect = document.getElementById("court_id");
    const hoursSelect = document.getElementById("hours");
    const minutesSelect = document.getElementById("minutes");
    const amountDisplay = document.getElementById("amountDisplay");

    function calculateAmount() {
        const rate = parseFloat(courtSelect.options[courtSelect.selectedIndex].dataset.rate || 0);
        const hours = parseInt(hoursSelect.value || 0);
        const minutes = parseInt(minutesSelect.value || 0);

        const totalHours = hours + (minutes / 60);
        const amount = rate * totalHours;

        amountDisplay.textContent = "₱ " + amount.toFixed(2);
    }

    courtSelect.addEventListener("change", calculateAmount);
    hoursSelect.addEventListener("change", calculateAmount);
    minutesSelect.addEventListener("change", calculateAmount);

    // Run on page load (default values)
    calculateAmount();
});
</script>
@endpush
@endsection
