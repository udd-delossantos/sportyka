@extends('layouts.staff.app')

@section('content')
<div class="container">
    <form action="{{ route('staff.queues.store') }}" method="POST">
        @csrf

        <!-- Queue display card -->
        <div class="card mb-4 shadow" id="queueContainer" style="display:none;">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><strong>Queue – Waiting Customers</strong></h5>
                <small class="text-muted" id="queueCourtBadge" style="display:none;"></small>
            </div>
            <div class="card-body" id="queueList">
                <p class="text-muted mb-0">Select a court to view waiting customers.</p>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header pb-0">
                <h5><strong>Add Customer to Queue</strong></h5>
            </div>
            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="row">
                    <div class="col-sm-6">
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
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label>Customer Name</label>
                            <input type="text" name="customer_name" class="form-control" required value="{{ old('customer_name') }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="hours">Hours</label>
                        <select name="hours" class="form-control" required>
                            <option value="0">0</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="minutes">Minutes</label>
                        <select name="minutes" class="form-control" required>
                            <option value="0">0</option>
                            <option value="30">30</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label>Start Time</label>
                            <input type="time" name="start_time" class="form-control" required value="{{ old('start_time') }}">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label>End Time</label>
                            <input type="time" name="end_time" class="form-control" required value="{{ old('end_time') }}" readonly>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label>GCash Transaction No. (If GCash Payment Only)</label>
                            <input type="text" name="transaction_no" class="form-control" maxlength="13" minlength="13" value="{{ old('transaction_no') }}">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label>50% Down Payment</label>
                            <input type="text" id="computedAmount" class="form-control" readonly>
                        </div>
                    </div>
                </div>

            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-success btn-sm">Add to Queue</button>
                <a href="{{ route('staff.queues.index') }}" class="btn btn-secondary btn-sm">Back</a>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ====== Data from controller ======
    const queuesByCourt = @json($queuesByCourt);

    // ====== DOM elements ======
    const courtSelect = document.getElementById('courtSelect');
    const queueContainer = document.getElementById('queueContainer');
    const queueList = document.getElementById('queueList');
    const queueCourtBadge = document.getElementById('queueCourtBadge');

    const hoursInput = document.querySelector('select[name="hours"]');
    const minutesInput = document.querySelector('select[name="minutes"]');
    const startTimeInput = document.querySelector('input[name="start_time"]');
    const endTimeInput = document.querySelector('input[name="end_time"]');
    const computedAmount = document.getElementById('computedAmount');

    // compute down payment
    function computeAmount() {
        const hours = parseInt(hoursInput.value) || 0;
        const minutes = parseInt(minutesInput.value) || 0;
        const selectedCourt = courtSelect.options[courtSelect.selectedIndex];
        const rate = parseFloat(selectedCourt.getAttribute('data-rate'));

        if (isNaN(rate)) {
            computedAmount.value = '';
            return;
        }

        const totalMinutes = (hours * 60) + minutes;
        if (totalMinutes <= 0) {
            computedAmount.value = '';
            return;
        }

        const ratePerMinute = rate / 60;
        const total = totalMinutes * ratePerMinute * 0.5; // 50% down
        computedAmount.value = '₱' + total.toFixed(2);
    }

    // compute end time from start + duration
    function updateEndTime() {
        const startTime = startTimeInput.value;
        const hours = parseInt(hoursInput.value) || 0;
        const minutes = parseInt(minutesInput.value) || 0;

        if (!startTime) return;

        let [startHour, startMinute] = startTime.split(':').map(Number);
        let startDate = new Date();
        startDate.setHours(startHour, startMinute, 0);

        startDate.setMinutes(startDate.getMinutes() + (hours * 60) + minutes);

        let endHour = String(startDate.getHours()).padStart(2, '0');
        let endMinute = String(startDate.getMinutes()).padStart(2, '0');
        endTimeInput.value = `${endHour}:${endMinute}`;
    }

    // escape helper
    function escapeHtml(unsafe) {
        return String(unsafe)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // Render queue for selected court
    function renderQueue() {
        const courtId = courtSelect.value;
        const selectedCourtName = courtSelect.options[courtSelect.selectedIndex].text.split(' (')[0] || 'Court';

        queueCourtBadge.textContent = `for ${selectedCourtName}`;
        queueCourtBadge.style.display = 'inline-block';

        const waiting = (queuesByCourt[courtId] && Array.isArray(queuesByCourt[courtId])) ? queuesByCourt[courtId] : [];

        queueContainer.style.display = 'block';

        if (waiting.length === 0) {
            queueList.innerHTML = `<p class="text-success mb-0">No waiting customers for ${selectedCourtName}. </p>`;
            return;
        }

        const frag = document.createDocumentFragment();
        waiting.forEach(q => {
            const item = document.createElement('div');
            item.className = 'alert alert-info py-2 mb-2';
            item.innerHTML = `<strong>${escapeHtml(q.customer)}</strong> 
                              <span class="text-muted"> — ${escapeHtml(q.start_time)} to ${escapeHtml(q.end_time)}</span>`;
            frag.appendChild(item);
        });

        queueList.innerHTML = '';
        queueList.appendChild(frag);
    }

    // Attach listeners
    courtSelect.addEventListener('change', () => {
        computeAmount();
        renderQueue();
    });

    [hoursInput, minutesInput].forEach(input => {
        input.addEventListener('change', () => { computeAmount(); updateEndTime(); });
    });
    startTimeInput.addEventListener('input', updateEndTime);

    // initial run
    computeAmount();
    updateEndTime();
    renderQueue();
});
</script>
@endpush