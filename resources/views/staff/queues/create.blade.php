@extends('layouts.staff.app')

@section('content')
<div class="container-fluid">
    <form action="{{ route('staff.queues.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-7">
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
                                <div class="col-md-6">
                                    <div class="mb-3">
                                    <label>GCash Ref No. (Leave blank if cash)</label>
                                    <input
                                        type="text"
                                        name="transaction_no"
                                        id="transaction_no"
                                        class="form-control"
                                        maxlength="13"
                                        inputmode="numeric"
                                        pattern="\d{13}"
                                        placeholder="13-digit GCash Ref No."
                                        value="{{ old('transaction_no') }}"
                                    >
                                    <div class="invalid-feedback">
                                        Please enter exactly 13 digits (numbers only).
                                    </div>
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

            </div>
            <div class="col-md-5">
                        <div class="card mb-4 shadow" id="queueContainer" style="display:none;">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><strong>Queue – Waiting Customers</strong></h5>
                        <small class="text-muted" id="queueCourtBadge" style="display:none;"></small>
                    </div>
                    <div class="card-body" id="queueList">
                        <p class="text-muted mb-0">Select a court to view waiting customers.</p>
                    </div>
                </div>
                <!-- Booked slots card -->
                <div class="card mb-4 shadow" id="bookedContainer" style="display:none;">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><strong>Booked Slots</strong></h5>
                        <small class="text-muted" id="bookedCourtBadge" style="display:none;"></small>
                    </div>
                    <div class="card-body" id="bookedList">
                        <p class="text-muted mb-0">Select a court to view booked slots.</p>
                    </div>
                </div>


            </div>

        </div>

        <!-- Queue display card -->
      

        
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ====== Data from controller ======
    const queuesByCourt = @json($queuesByCourt);
    const ongoingByCourt = @json($ongoingByCourt ?? []); // ✅ added, safe even if empty
    const bookedByCourt = @json($bookedByCourt ?? []);


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

    const bookedContainer = document.getElementById('bookedContainer');
const bookedList = document.getElementById('bookedList');
const bookedCourtBadge = document.getElementById('bookedCourtBadge');


    function getCurrentTime() {
        const now = new Date();
        return now.getHours() * 60 + now.getMinutes();
    }

    function timeToMinutes(time) {
        if (!time) return null;
        const [h, m] = time.split(':').map(Number);
        return (h * 60) + m;
    }

    function minutesToTime(mins) {
        const h = Math.floor(mins / 60);
        const m = mins % 60;
        return `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}`;
    }

    function ampmTo24(time) {
        if (!time) return null;
        const match = time.match(/(\d+):(\d+)\s?(AM|PM)/i);
        if (!match) return null;

        let h = parseInt(match[1]);
        const m = match[2];
        const mer = match[3].toUpperCase();

        if (mer === 'PM' && h !== 12) h += 12;
        if (mer === 'AM' && h === 12) h = 0;

        return `${String(h).padStart(2,'0')}:${m}`;
    }

    function enforceFutureTime() {
        const selectedMinutes = timeToMinutes(startTimeInput.value);
        const nowMinutes = getCurrentTime();

        if (selectedMinutes !== null && selectedMinutes < nowMinutes) {
            alert('You cannot select a past time.');
            startTimeInput.value = '';
            endTimeInput.value = '';
            return;
        }

        updateEndTime();
    }

    function computeAmount() {
        const hours = parseInt(hoursInput.value) || 0;
        const minutes = parseInt(minutesInput.value) || 0;
        const selectedCourt = courtSelect.options[courtSelect.selectedIndex];
        const rate = parseFloat(selectedCourt.getAttribute('data-rate'));

        if (isNaN(rate)) return computedAmount.value = '';

        const totalMinutes = (hours * 60) + minutes;
        if (totalMinutes <= 0) return computedAmount.value = '';

        const total = (rate / 60) * totalMinutes * 0.5;
        computedAmount.value = '₱' + total.toFixed(2);
    }

    function updateEndTime() {
        if (!startTimeInput.value) return;

        let [h, m] = startTimeInput.value.split(':').map(Number);
        let d = new Date();
        d.setHours(h, m, 0);
        d.setMinutes(d.getMinutes() + (hoursInput.value * 60) + parseInt(minutesInput.value));

        endTimeInput.value = `${String(d.getHours()).padStart(2,'0')}:${String(d.getMinutes()).padStart(2,'0')}`;
    }

    function escapeHtml(str) {
        return String(str).replace(/[&<>"']/g, s => ({
            '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'
        }[s]));
    }

    function renderQueue() {
        const courtId = courtSelect.value;
        const courtName = courtSelect.options[courtSelect.selectedIndex].text.split(' (')[0];

        queueCourtBadge.textContent = `for ${courtName}`;
        queueCourtBadge.style.display = 'inline-block';
        queueContainer.style.display = 'block';

        queueList.innerHTML = '';

        if (ongoingByCourt[courtId]) {
            const ongoing = document.createElement('div');
            ongoing.className = 'alert alert-success mb-3';
            ongoing.innerHTML = `
                <strong>Ongoing Session</strong>
                — Ends at: <strong>${escapeHtml(ongoingByCourt[courtId].end_time)}</strong>
            `;
            queueList.appendChild(ongoing);
        }

        const waiting = queuesByCourt[courtId] || [];
        if (waiting.length === 0) {
            queueList.innerHTML += `<p class="text-success mb-0">No waiting customers.</p>`;
            return;
        }

        waiting.forEach(q => {
            const div = document.createElement('div');
            div.className = 'alert alert-warning py-2 mb-2';
            div.innerHTML = `<strong>${escapeHtml(q.customer)}</strong>
                             <span class="text-muted"> — ${escapeHtml(q.start_time)} to ${escapeHtml(q.end_time)}</span>`;
            queueList.appendChild(div);
        });
    }

    /* ================================
       ✅ AUTO-FILL START TIME (ADD ONLY)
    ================================= */
    function autoFillStartTime() {
        const courtId = courtSelect.value;
        let candidates = [];

        if (ongoingByCourt[courtId]) {
            const t = ampmTo24(ongoingByCourt[courtId].end_time);
            if (t) candidates.push(timeToMinutes(t));
        }

        if (queuesByCourt[courtId]?.length) {
            const lastQueue = queuesByCourt[courtId][queuesByCourt[courtId].length - 1];
            const t = ampmTo24(lastQueue.end_time);
            if (t) candidates.push(timeToMinutes(t));
        }

        candidates.push(getCurrentTime());

        const finalMinutes = Math.max(...candidates);
        startTimeInput.value = minutesToTime(finalMinutes);

        startTimeInput.dispatchEvent(new Event('input'));
    }

    /* ====== EXISTING LISTENERS ====== */
    courtSelect.addEventListener('change', () => {
        computeAmount();
        renderQueue();
            renderBookedSlots(); // ✅ ADD

        autoFillStartTime(); // ✅ added
    });

    [hoursInput, minutesInput].forEach(i =>
        i.addEventListener('change', () => { computeAmount(); updateEndTime(); })
    );

    startTimeInput.addEventListener('change', enforceFutureTime);
    startTimeInput.addEventListener('input', enforceFutureTime);


    function renderBookedSlots() {
    const courtId = courtSelect.value;
    const courtName = courtSelect.options[courtSelect.selectedIndex].text.split(' (')[0];

    bookedCourtBadge.textContent = `for ${courtName}`;
    bookedCourtBadge.style.display = 'inline-block';
    bookedContainer.style.display = 'block';

    bookedList.innerHTML = '';

    const booked = bookedByCourt[courtId] || [];

    if (booked.length === 0) {
        bookedList.innerHTML = `<p class="text-success mb-0">No booked slots.</p>`;
        return;
    }

    booked.forEach(b => {
        const div = document.createElement('div');
        div.className = 'alert alert-warning py-2 mb-2';
        div.innerHTML = `
            <strong>${escapeHtml(b.customer)}</strong>
            <span class="text-muted"> — ${escapeHtml(b.start_time)} to ${escapeHtml(b.end_time)}</span>
        `;
        bookedList.appendChild(div);
    });
}


    /* ====== INITIAL RUN ====== */
    computeAmount();
    renderQueue();
    autoFillStartTime(); // ✅ added
    renderBookedSlots();

});

document.addEventListener('DOMContentLoaded', function () {
    const transactionInput = document.getElementById('transaction_no');
    if (!transactionInput) return;

    // Block letters & symbols while typing
    transactionInput.addEventListener('keydown', function (e) {
        const allowedKeys = [
            'Backspace','Delete','ArrowLeft','ArrowRight',
            'Tab','Home','End'
        ];

        if (allowedKeys.includes(e.key) || e.ctrlKey || e.metaKey) return;

        // allow digits only
        if (!/^\d$/.test(e.key)) {
            e.preventDefault();
            return;
        }

        // max 13 digits
        if (transactionInput.value.length >= 13) {
            e.preventDefault();
        }
    });

    // Sanitize pasted / mobile input
    transactionInput.addEventListener('input', function () {
        transactionInput.value = transactionInput.value
            .replace(/\D/g, '')
            .slice(0, 13);
    });

    // Optional strict validation on submit
    const form = transactionInput.closest('form');
    if (form) {
        form.addEventListener('submit', function (e) {
            if (transactionInput.value && transactionInput.value.length !== 13) {
                e.preventDefault();
                transactionInput.classList.add('is-invalid');
                transactionInput.focus();
            } else {
                transactionInput.classList.remove('is-invalid');
            }
        });
    }
});
</script>
@endpush
