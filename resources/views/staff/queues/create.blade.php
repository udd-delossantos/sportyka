@extends('layouts.staff.app')
@section('title', 'Add to Queue')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Add Customer to Queue</h1>
        <a href="{{ route('staff.queues.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 mr-2"></i> Back to Queue List
        </a>
    </div>

    <form action="{{ route('staff.queues.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-lg-7">
                <div class="card shadow mb-4 border-left-primary">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Queue Details</h6>
                    </div>
                    <div class="card-body">
                        @if(session('error'))
                            <div class="alert alert-danger border-left-danger" role="alert">
                                <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold text-gray-700">Select Court</label>
                                    <select name="court_id" class="form-control" required id="courtSelect">
                                        @foreach($courts as $court)
                                            <option value="{{ $court->id }}" data-rate="{{ $court->hourly_rate }}">
                                                {{ $court->name }} (₱{{ number_format($court->hourly_rate, 2) }}/hr)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold text-gray-700">Customer Name</label>
                                    <input type="text" name="customer_name" class="form-control" required value="{{ old('customer_name') }}" placeholder="Enter name...">
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label class="font-weight-bold text-gray-700">Duration (Hours)</label>
                                <select name="hours" class="form-control">
                                    @for($i=1; $i<=5; $i++) <option value="{{ $i }}">{{ $i }} Hour{{ $i > 1 ? 's' : '' }}</option> @endfor
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="font-weight-bold text-gray-700">Duration (Minutes)</label>
                                <select name="minutes" class="form-control">
                                    <option value="0">0 Minutes</option>
                                    <option value="30">30 Minutes</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label class="font-weight-bold text-gray-700">Start Time</label>
                                <input type="time" name="start_time" id="start_time" class="form-control" required value="{{ old('start_time') }}">
                                <small class="text-muted">Calculated based on current queue end time.</small>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="font-weight-bold text-gray-700">Expected End Time</label>
                                <input type="time" name="end_time" id="end_time" class="form-control bg-light" required readonly>
                            </div>
                        </div>

                        <hr class="sidebar-divider">

                        <div class="row align-items-center">
                            <div class="col-md-7">
                                <div class="form-group">
                                    <label class="font-weight-bold text-gray-700">GCash Ref No. (Optional)</label>
                                    <input type="text" name="transaction_no" id="transaction_no" class="form-control" maxlength="13" placeholder="13-digit reference number">
                                    <div class="invalid-feedback">Please enter exactly 13 digits.</div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="alert alert-secondary text-center mb-0 py-2">
                                    <span class="small font-weight-bold text-gray-600">50% Downpayment</span>
                                    <h4 class="font-weight-bold text-success mb-0" id="computedAmount">₱0.00</h4>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block btn-lg mt-4 shadow-sm">
                            <i class="fas fa-plus-circle mr-2"></i> Add to Queue
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card shadow mb-4 border-left-warning" id="queueContainer" style="display:none;">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-warning">Current Queue</h6>
                        <span class="badge badge-light" id="queueCourtBadge"></span>
                    </div>
                    <div class="card-body p-2" style="max-height: 250px; overflow-y: auto;" id="queueList">
                        </div>
                </div>

                <div class="card shadow mb-4 border-left-info" id="bookedContainer" style="display:none;">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-info">Booked Today</h6>
                        <span class="badge badge-light" id="bookedCourtBadge"></span>
                    </div>
                    <div class="card-body p-2" style="max-height: 250px; overflow-y: auto;" id="bookedList">
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
    const ongoingByCourt = @json($ongoingByCourt ?? []);
    const bookedByCourt = @json($bookedByCourt ?? []);

    const courtSelect = document.getElementById('courtSelect');
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');
    const hoursSelect = document.querySelector('select[name="hours"]');
    const minutesSelect = document.querySelector('select[name="minutes"]');
    const amountDisplay = document.getElementById('computedAmount');

    const queueContainer = document.getElementById('queueContainer');
    const queueList = document.getElementById('queueList');
    const queueCourtBadge = document.getElementById('queueCourtBadge');

    const bookedContainer = document.getElementById('bookedContainer');
    const bookedList = document.getElementById('bookedList');
    const bookedCourtBadge = document.getElementById('bookedCourtBadge');

    function escapeHtml(str) {
        return String(str).replace(/[&<>"']/g, s => ({
            '&':'&amp;',
            '<':'&lt;',
            '>':'&gt;',
            '"':'&quot;',
            "'":'&#039;'
        }[s]));
    }

    function ampmTo24(timeStr) {
        if (!timeStr) return null;
        const [time, modifier] = timeStr.split(' ');
        let [hours, minutes] = time.split(':');
        if (hours === '12') hours = '00';
        if (modifier === 'PM') hours = parseInt(hours, 10) + 12;
        return `${String(hours).padStart(2, '0')}:${minutes}`;
    }

    function timeToMinutes(time) {
        const [h, m] = time.split(':').map(Number);
        return (h * 60) + m;
    }

    function minutesToTime(mins) {
        const h = Math.floor(mins / 60) % 24;
        const m = mins % 60;
        return `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}`;
    }

    function updateLogic() {
        const courtId = courtSelect.value;

        renderCards();
        autoFillTime(courtId);
        calculateAll();
    }

    function autoFillTime(courtId) {
        let now = new Date();
        let currentMins = (now.getHours() * 60) + now.getMinutes();
        let lastMins = currentMins;

        if (ongoingByCourt[courtId]) {
            let end24 = ampmTo24(ongoingByCourt[courtId].end_time);
            lastMins = Math.max(lastMins, timeToMinutes(end24));
        }

        const waiting = queuesByCourt[courtId] || [];
        if (waiting.length > 0) {
            let lastQueueEnd = ampmTo24(waiting[waiting.length - 1].end_time);
            lastMins = Math.max(lastMins, timeToMinutes(lastQueueEnd));
        }

        startTimeInput.value = minutesToTime(lastMins);
    }

    function calculateAll() {
        if (!startTimeInput.value) return;

        let startMins = timeToMinutes(startTimeInput.value);
        let durationMins = (parseInt(hoursSelect.value) * 60) + parseInt(minutesSelect.value);
        endTimeInput.value = minutesToTime(startMins + durationMins);

        const rate = parseFloat(courtSelect.options[courtSelect.selectedIndex].dataset.rate);
        const total = (rate / 60) * durationMins;

        amountDisplay.textContent = '₱' + total.toLocaleString('en-US', { minimumFractionDigits: 2 });
    }

    /* =====================
       COPIED LIST LOGIC
    ====================== */
    function renderCards() {
    const courtId = courtSelect.value;
    const courtName = courtSelect.options[courtSelect.selectedIndex].text.split(' (')[0];

    /* ===== QUEUE (WITH ONGOING) ===== */
    queueContainer.style.display = 'flex';
    queueContainer.classList.remove('d-none');
    queueCourtBadge.style.display = 'inline-block';
    queueCourtBadge.textContent = `${courtName}`;
    queueList.innerHTML = '';

    /* ---- ONGOING SESSION ---- */
    if (ongoingByCourt[courtId]) {
        queueList.innerHTML += `
            <div class="card mb-2 shadow-sm">
                <div class="card-body p-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="font-weight-bold text-gray-800">
                            ${escapeHtml(ongoingByCourt[courtId].customer ?? 'Ongoing Session')}
                        </div>
                        <span class="badge badge-success">Ongoing</span>
                    </div>
                    <div class="small text-gray-600 mt-1">
                        <i class="far fa-clock mr-1"></i>
                        Ends at: ${ongoingByCourt[courtId].end_time}
                    </div>
                </div>
            </div>
            <hr></hr>
        `;
    }

    /* ---- WAITING QUEUE ---- */
    const waiting = queuesByCourt[courtId] || [];
    if (!waiting.length) {
        queueList.innerHTML += `
            <div class="text-center py-4 text-gray-500">
                <i class="fas fa-check-circle text-gray mb-2"></i><br>
                No waiting customers.
            </div>
        `;
    } else {
        waiting.forEach(q => {
            queueList.innerHTML += `
                <div class="card mb-2 shadow-sm">
                    <div class="card-body p-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="font-weight-bold text-gray-800">
                                ${escapeHtml(q.customer)}
                            </div>
                            <span class="badge badge-warning text-white">Waiting</span>
                        </div>
                        <div class="small text-gray-600 mt-1">
                            <i class="far fa-clock mr-1"></i>
                            ${q.start_time} - ${q.end_time}
                        </div>
                    </div>
                </div>
            `;
        });
    }

    /* ===== BOOKED ===== */
    bookedContainer.style.display = 'flex';
    bookedContainer.classList.remove('d-none');
    bookedCourtBadge.style.display = 'inline-block';
    bookedCourtBadge.textContent = `${courtName}`;
    bookedList.innerHTML = '';

    const booked = bookedByCourt[courtId] || [];
    if (!booked.length) {
        bookedList.innerHTML = `
            <div class="text-center py-4 text-gray-500">
                <i class="fas fa-calendar-check text-gray mb-2"></i><br>
                No booked slots for today.
            </div>
        `;
    } else {
        booked.forEach(b => {
            bookedList.innerHTML += `
                <div class="card mb-2 shadow-sm">
                    <div class="card-body p-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="font-weight-bold text-gray-800">
                                ${escapeHtml(b.customer)}
                            </div>
                            <span class="badge badge-success">Confirmed</span>
                        </div>
                        <div class="small text-gray-600 mt-1">
                            <i class="far fa-clock mr-1"></i>
                            ${b.start_time} - ${b.end_time}
                        </div>
                    </div>
                </div>
            `;
        });
    }
}


    // Listeners
    courtSelect.addEventListener('change', updateLogic);
    startTimeInput.addEventListener('input', calculateAll);
    [hoursSelect, minutesSelect].forEach(el => el.addEventListener('change', calculateAll));

    // GCash Validation
    const txInput = document.getElementById('transaction_no');
    txInput.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '').slice(0, 13);
        this.classList.toggle('is-invalid', this.value.length > 0 && this.value.length < 13);
    });

    updateLogic();
});
</script>
@endpush
