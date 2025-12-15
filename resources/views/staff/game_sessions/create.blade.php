@extends('layouts.staff.app')

@section('content')
<div class="container-fluid">
    <form method="POST" action="{{ route('staff.game_sessions.store') }}">
        @csrf
        <div class="row">
            <div class="col-md-7">
                <!-- ================= FORM CARD ================= -->
        <div class="card shadow mb4">
            <div class="card-header pb-0">
                <h5><strong>Create Session</strong></h5>
            </div>
            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="mb-3">
                    <label>Court</label>
                    <select name="court_id" id="courtSelect" class="form-control" required>
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
                    <label>Customer Name</label>
                    <input type="text" name="customer_name" class="form-control" required />
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Hours</label>
                        <select name="hours" id="hours" class="form-control" required>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label>Minutes</label>
                        <select name="minutes" id="minutes" class="form-control" required>
                            <option value="0">0</option>
                            <option value="30">30</option>
                        </select>
                    </div>
                </div>

                <div class="text-center">
                    <h5>Amount: <span id="amountDisplay" class="text-success">₱0.00</span></h5>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-success btn-sm">Create Session</button>
                <a href="{{ route('staff.game_sessions.index') }}" class="btn btn-secondary btn-sm">Back</a>
            </div>
        </div>

            </div>
            <div class="col-md-5">
                <!-- ================= QUEUE CARD ================= -->
        <div class="card mb-4 shadow" id="queueContainer" style="display:none;">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><strong>Queue – Waiting Customers</strong></h5>
                <small class="text-muted" id="queueCourtBadge" style="display:none;"></small>
            </div>
            <div class="card-body" id="queueList">
                <p class="text-muted mb-0">Select a court to view waiting customers.</p>
            </div>
        </div>

        <!-- ================= BOOKED SLOTS CARD ================= -->
        <div class="card mb-4 shadow" id="bookedContainer" style="display:none;">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><strong>Booked Time Slots</strong></h5>
                <small class="text-muted" id="bookedCourtBadge" style="display:none;"></small>
            </div>
            <div class="card-body" id="bookedList">
                <p class="text-muted mb-0">No booked slots.</p>
            </div>
        </div>

            </div>
        </div>

        

        
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const queuesByCourt = @json($queuesByCourt);
    const bookedByCourt = @json($bookedByCourt);

    const courtSelect = document.getElementById('courtSelect');

    const queueContainer = document.getElementById('queueContainer');
    const queueList = document.getElementById('queueList');
    const queueCourtBadge = document.getElementById('queueCourtBadge');

    const bookedContainer = document.getElementById('bookedContainer');
    const bookedList = document.getElementById('bookedList');
    const bookedCourtBadge = document.getElementById('bookedCourtBadge');

    const hours = document.getElementById('hours');
    const minutes = document.getElementById('minutes');
    const amountDisplay = document.getElementById('amountDisplay');

    function escapeHtml(str) {
        return String(str).replace(/[&<>"']/g, s => ({
            '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'
        }[s]));
    }

    function renderCards() {
        const courtId = courtSelect.value;
        const courtName = courtSelect.options[courtSelect.selectedIndex].text.split(' (')[0];

        /* ===== QUEUE ===== */
        queueContainer.style.display = 'block';
        queueCourtBadge.style.display = 'inline-block';
        queueCourtBadge.textContent = `for ${courtName}`;
        queueList.innerHTML = '';

        const waiting = queuesByCourt[courtId] || [];
        if (!waiting.length) {
            queueList.innerHTML = `<p class="text-success mb-0">No waiting customers.</p>`;
        } else {
            waiting.forEach(q => {
                queueList.innerHTML += `
                    <div class="alert alert-warning py-2 mb-2">
                        <strong>${escapeHtml(q.customer)}</strong>
                        <span class="text-muted"> — ${q.start_time} to ${q.end_time}</span>
                    </div>`;
            });
        }

        /* ===== BOOKED ===== */
        bookedContainer.style.display = 'block';
        bookedCourtBadge.style.display = 'inline-block';
        bookedCourtBadge.textContent = `for ${courtName}`;
        bookedList.innerHTML = '';

        const booked = bookedByCourt[courtId] || [];
        if (!booked.length) {
            bookedList.innerHTML = `<p class="text-success mb-0">No booked slots for today.</p>`;
        } else {
            booked.forEach(b => {
                bookedList.innerHTML += `
                    <div class="alert alert-info py-2 mb-2">
                        <strong>${escapeHtml(b.customer)}</strong>
                        <span class="text-muted"> — ${b.start_time} to ${b.end_time}</span>
                    </div>`;
            });
        }
    }

    function calculateAmount() {
        const rate = parseFloat(courtSelect.selectedOptions[0].dataset.rate || 0);
        const total = rate * (parseInt(hours.value) + (parseInt(minutes.value) / 60));
        amountDisplay.textContent = '₱' + total.toFixed(2);
    }

    courtSelect.addEventListener('change', () => {
        renderCards();
        calculateAmount();
    });

    hours.addEventListener('change', calculateAmount);
    minutes.addEventListener('change', calculateAmount);

    renderCards();
    calculateAmount();
});
</script>
@endpush
