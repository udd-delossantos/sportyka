@extends('layouts.staff.app')
@section('title', 'Create Session')
@section('content')

<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">New Walk-in Session</h1>
        <a href="{{ route('staff.game_sessions.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 mr-2"></i> Back to Dashboard
        </a>
    </div>

    <form method="POST" action="{{ route('staff.game_sessions.store') }}">
        @csrf
        <div class="row">
            
            <div class="col-lg-7">
                <div class="card shadow mb-4 border-left-primary">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Session Details</h6>
                    </div>
                    <div class="card-body">
                        
                        @if(session('error'))
                            <div class="alert alert-danger border-left-danger" role="alert">
                                <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="courtSelect" class="font-weight-bold text-gray-700">Select Court</label>
                            <select name="court_id" id="courtSelect" class="form-control" required style="height: calc(1.5em + .75rem + 2px);">
                                @foreach($courts as $court)
                                    @if($court->status === 'available')
                                        <option value="{{ $court->id }}" data-rate="{{ $court->hourly_rate }}">
                                            {{ $court->name }} (₱{{ $court->hourly_rate }}/hr)
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Only available courts are listed.</small>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700">Customer Name</label>
                            <input type="text" name="customer_name" class="form-control" placeholder="Enter customer name..." required />
                        </div>

                        <hr class="sidebar-divider my-4">

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="hours" class="font-weight-bold text-gray-700">Hours</label>
                                <select name="hours" id="hours" class="form-control">
                                    <option value="0">0 Hour</option>
                                    <option value="1">1 Hour</option>
                                    <option value="2">2 Hours</option>
                                    <option value="3">3 Hours</option>
                                    <option value="4">4 Hours</option>
                                    <option value="5">5 Hours</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="minutes" class="font-weight-bold text-gray-700">Minutes</label>
                                <select name="minutes" id="minutes" class="form-control">
                                    <option value="0">0 Minutes</option>
                                    <option value="1">1 Minutes</option>
                                    <option value="30">30 Minutes</option>
                                </select>
                            </div>
                        </div>

                        <div class="alert alert-secondary mt-3 text-center">
                            <span class="text-gray-600 font-weight-bold">Total Amount to Pay</span>
                            <h2 class="font-weight-bold text-success mb-0 mt-2" id="amountDisplay">₱0.00</h2>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block btn-lg mt-4 shadow-sm">
                            <i class="fas fa-check-circle mr-2"></i> Create Session
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                
                <div class="card shadow border-left-warning mb-4" id="queueContainer" style="display:none;">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-warning">Waitlist / Queue</h6>
                        <span class="badge badge-light badge-counter" id="queueCourtBadge" style="display:none; font-size: 0.8rem;"></span>
                    </div>
                    <div class="card-body p-2" style="max-height: 300px; overflow-y: auto;">
                        <div id="queueList">
                            <div class="text-center text-gray-500 my-3">Select a court to view waiting customers.</div>
                        </div>
                    </div>
                </div>

                <div class="card shadow border-left-info mb-4" id="bookedContainer" style="display:none;">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-info">Booked Slots</h6>
                        <span class="badge badge-light badge-counter" id="bookedCourtBadge" style="display:none; font-size: 0.8rem;"></span>
                    </div>
                    <div class="card-body p-2" style="max-height: 300px; overflow-y: auto;">
                        <div id="bookedList">
                            <div class="text-center text-gray-500 my-3">No booked slots.</div>
                        </div>
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
        queueContainer.style.display = 'flex'; // Changed to flex to respect d-flex class if added, or block
        queueContainer.classList.remove('d-none');
        queueCourtBadge.style.display = 'inline-block';
        queueCourtBadge.textContent = `${courtName}`;
        queueList.innerHTML = '';

        const waiting = queuesByCourt[courtId] || [];
        if (!waiting.length) {
            queueList.innerHTML = `<div class="text-center py-4 text-gray-500"><i class="fas fa-check-circle text-gray mb-2"></i><br>No waiting customers.</div>`;
        } else {
            waiting.forEach(q => {
                queueList.innerHTML += `
                    <div class="card mb-2 shadow-sm">
                        <div class="card-body p-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="font-weight-bold text-gray-800">${escapeHtml(q.customer)}</div>
                                <span class="badge badge-warning">Waiting</span>
                            </div>
                            <div class="small text-gray-600 mt-1">
                                <i class="far fa-clock mr-1"></i> ${q.start_time} - ${q.end_time}
                            </div>
                        </div>
                    </div>`;
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
            bookedList.innerHTML = `<div class="text-center py-4 text-gray-500"><i class="fas fa-calendar-check text-gray mb-2"></i><br>No booked slots for today.</div>`;
        } else {
            booked.forEach(b => {
                bookedList.innerHTML += `
                    <div class="card mb-2 shadow-sm">
                        <div class="card-body p-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="font-weight-bold text-gray-800">${escapeHtml(b.customer)}</div>
                                <span class="badge badge-success">Confirmed</span>
                            </div>
                            <div class="small text-gray-600 mt-1">
                                <i class="far fa-clock mr-1"></i> ${b.start_time} - ${b.end_time}
                            </div>
                        </div>
                    </div>`;
            });
        }
    }

    function calculateAmount() {
        const rate = parseFloat(courtSelect.selectedOptions[0].dataset.rate || 0);
        const total = rate * (parseInt(hours.value) + (parseInt(minutes.value) / 60));
        // Using toLocaleString for better currency formatting
        amountDisplay.textContent = '₱' + total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    courtSelect.addEventListener('change', () => {
        renderCards();
        calculateAmount();
    });

    hours.addEventListener('change', calculateAmount);
    minutes.addEventListener('change', calculateAmount);

    // Initial run
    if(courtSelect.options.length > 0) {
        renderCards();
        calculateAmount();
    }
});
</script>
@endpush