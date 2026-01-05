@extends('layouts.staff.app')

@section('title', 'Edit Booking')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Booking Request</h1>
        <a href="{{ route('staff.bookings.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 mr-2"></i> Back to List
        </a>
    </div>

    <form action="{{ route('staff.bookings.update', $booking->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-lg-7">
                <div class="card shadow mb-4 border-left-primary">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Booking Details</h6>
                    </div>
                    <div class="card-body">
                        
                        @if(session('error'))
                            <div class="alert alert-danger border-left-danger" role="alert">
                                <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
                            </div>
                        @endif

                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700">Court</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light"><i class="fas fa-table-tennis text-primary"></i></span>
                                </div>
                                <input type="text" class="form-control bg-light" 
                                    value="{{ $booking->court->name }} (₱{{ number_format($booking->court->hourly_rate, 2) }}/hour)" readonly>
                            </div>
                            <input type="hidden" name="court_id" value="{{ $booking->court_id }}">
                        </div>

                        <div class="form-row mt-4">
                            <div class="form-group col-md-4">
                                <label for="booking_date" class="font-weight-bold text-gray-700">Date</label>
                                <input type="date" name="booking_date" id="booking_date" class="form-control" 
                                    value="{{ \Carbon\Carbon::parse($booking->start_time)->toDateString() }}" required>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="start_time" class="font-weight-bold text-gray-700">Start Time</label>
                                <input type="time" name="start_time" id="start_time" class="form-control" 
                                    value="{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}" required>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="end_time" class="font-weight-bold text-gray-700">End Time</label>
                                <input type="time" name="end_time" id="end_time" class="form-control bg-light" 
                                    value="{{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}" readonly required>
                                <small class="text-muted">Calculated based on duration</small>
                            </div>
                        </div>

                        <hr class="sidebar-divider my-4">
                         <div class="row">
                            <div class="col-md-6">
                                 <div class="form-group">
                                        <label for="transaction_no" class="font-weight-bold text-gray-700">GCash Transaction No.</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-light"><i class="fas fa-receipt text-gray-500"></i></span>
                                            </div>
                                             <input type="text" name="transaction_no" id="transaction_no" class="form-control" maxlength="13" placeholder="13-digit reference number"
                                              value="{{ $booking->transaction_no }}" required placeholder="Enter Reference No.">
                                            <div class="invalid-feedback">Please enter exactly 13 digits.</div>
                                        </div>
                                    </div>

                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-info">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fas fa-info-circle fa-2x"></i>
                                </div>
                                <div class="col">
                                    <span class="small font-weight-bold text-uppercase">Booking Duration</span><br>
                                    <strong>{{ $booking->expected_hours }} Hour(s) and {{ $booking->expected_minutes }} Minute(s)</strong>
                                </div>
                            </div>
                        </div>


                            </div>

                        </div>

                       

                        <input type="hidden" id="expected_hours" value="{{ $booking->expected_hours }}">
                        <input type="hidden" id="expected_minutes" value="{{ $booking->expected_minutes }}">
                       

                        
                        <button type="submit" class="btn btn-primary btn-block btn-lg mt-4 shadow-sm font-weight-bold">
                            <i class="fas fa-save mr-2"></i> Update Booking Details
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card shadow border-left-info mb-4" id="bookedSlotsContainer" style="display:none;">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-info">Booked Slots</h6>
                        <span class="badge badge-light badge-counter" id="bookedDateBadge" style="display:none; font-size: 0.8rem;"></span>
                    </div>
                    <div class="card-body p-2" style="max-height: 500px; overflow-y: auto;">
                        <div id="bookedSlotsList">
                            <div class="text-center text-gray-500 my-3">
                                <i class="fas fa-spinner fa-spin mr-2"></i> Loading schedule...
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card bg-light shadow-sm mb-4">
                    <div class="card-body py-3">
                        <p class="text-xs text-gray-600 mb-0">
                            <i class="fas fa-lightbulb text-warning mr-1"></i> 
                            <strong>Pro-tip:</strong> Use the "Booked Slots" list to avoid overlapping schedules.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const startInput = document.getElementById("start_time");
    const endInput   = document.getElementById("end_time");
    const hours      = parseInt(document.getElementById("expected_hours").value) || 0;
    const minutes    = parseInt(document.getElementById("expected_minutes").value) || 0;

    function updateEndTime() {
        if (!startInput.value) return;

        let [h, m] = startInput.value.split(":").map(Number);

        // Add duration
        let totalMinutes = h * 60 + m + (hours * 60) + minutes;
        let endH = Math.floor(totalMinutes / 60) % 24; 
        let endM = totalMinutes % 60;

        endInput.value = String(endH).padStart(2, "0") + ":" + String(endM).padStart(2, "0");
    }

    startInput.addEventListener("change", updateEndTime);
    updateEndTime();
});

document.addEventListener("DOMContentLoaded", function () {
    const bookings   = @json($allBookings); 
    const dateInput  = document.getElementById("booking_date");
    const courtId    = document.querySelector("input[name='court_id']").value;
    const container  = document.getElementById("bookedSlotsContainer");
    const list       = document.getElementById("bookedSlotsList");
    const dateBadge  = document.getElementById("bookedDateBadge");

    function to12h(HHmm) {
        const [H, M] = HHmm.split(':').map(Number);
        const ampm = H >= 12 ? 'PM' : 'AM';
        const h = (H % 12) || 12;
        return `${h}:${String(M).padStart(2, '0')} ${ampm}`;
    }

    function renderBooked() {
        const dateVal = dateInput.value;
        if (!dateVal) {
            container.style.display = "none";
            return;
        }

        container.style.display = "block";

        try {
            const dateObj = new Date(dateVal + 'T00:00:00');
            const options = { month: 'short', day: 'numeric', year: 'numeric' };
            dateBadge.textContent = dateObj.toLocaleDateString('en-US', options);
            dateBadge.style.display = "inline-block";
        } catch {
            dateBadge.style.display = "none";
        }

        // Filter bookings
        const slots = bookings
            .filter(s => s.court_id == courtId && s.date === dateVal)
            .sort((a, b) => a.start_time.localeCompare(b.start_time));

        if (slots.length === 0) {
            list.innerHTML = `
                <div class="text-center py-4 text-gray-500">
                    <i class="fas fa-calendar-check text-gray mb-2"></i><br>
                    No booked slots for this date.
                </div>`;
            return;
        }

        const frag = document.createDocumentFragment();
        slots.forEach(s => {
            const outerDiv = document.createElement("div");
            outerDiv.className = "card mb-2 shadow-sm";
            
            const isConfirmed = s.status === 'confirmed';
            const badgeClass = isConfirmed ? 'badge-success' : 'badge-warning';
            const statusLabel = isConfirmed ? 'Confirmed' : 'Pending';

            outerDiv.innerHTML = `
                <div class="card-body p-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="font-weight-bold text-gray-800">
                            <i class="far fa-clock mr-1 text-danger"></i> 
                            ${to12h(s.start_time)} – ${to12h(s.end_time)}
                        </div>
                        <span class="badge ${badgeClass}">${statusLabel}</span>
                    </div>
                </div>`;
            frag.appendChild(outerDiv);
        });

        list.innerHTML = "";
        list.appendChild(frag);
    }

    if (dateInput) {
        dateInput.addEventListener("change", renderBooked);
        renderBooked(); 
    }

    // GCash Validation
    const txInput = document.getElementById('transaction_no');
    txInput.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '').slice(0, 13);
        this.classList.toggle('is-invalid', this.value.length > 0 && this.value.length < 13);
    });
});
</script>
@endpush