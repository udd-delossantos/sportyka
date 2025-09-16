@extends('layouts.staff.app')

@section('content')
<div class="container">
    <form action="{{ route('staff.bookings.update', $booking->id) }}" method="POST">
    <!-- Step 2: Show Booked Slots -->
    <div class="card mb-4 shadow" id="bookedSlotsContainer" style="display:none;">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><strong>Booked Time Slots</strong></h5>
            <small class="text-muted" id="bookedDateBadge" style="display:none;"></small> 
        </div>
        <div class="card-body" id="bookedSlotsList">
            <p class="text-muted mb-0">Select a date to view booked slots for this court.</p>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header pb-0">
            <h5><strong>Edit Booking Request</strong></h5>
        </div>
        <div class="card-body">
            @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

       
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Court</label>
                <input type="text" class="form-control" 
                    value="{{ $booking->court->name }} (₱{{ $booking->court->hourly_rate }}/hour)" readonly>
                <input type="hidden" name="court_id" value="{{ $booking->court_id }}">
            </div>
            <div class="row">
                <div class="col-md-4">
                        <!-- Date -->
                        <div class="mb-3">
                            <label for="booking_date" class="form-label">Date</label>
                            <input type="date" name="booking_date" id="booking_date" class="form-control" 
                                value="{{ \Carbon\Carbon::parse($booking->start_time)->toDateString() }}" required>
                        </div>

                </div>

              

                <div class="col-md-4">
                    <!-- Start Time -->
                    <div class="mb-3">
                        <label for="start_time" class="form-label">Start Time</label>
                        <input type="time" name="start_time" id="start_time" class="form-control" 
                            value="{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}" required>
                    </div>

                </div>
                <div class="col-md-4">
                    <!-- End Time -->
                    <div class="mb-3">
                        <label for="end_time" class="form-label">End Time</label>
                        <input type="time" name="end_time" id="end_time" class="form-control" 
                            value="{{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}" readonly required>
                    </div>

                </div>

            </div>


            <!-- Store duration values (readonly but still available) -->
            <input type="hidden" id="expected_hours" value="{{ $booking->expected_hours }}">
            <input type="hidden" id="expected_minutes" value="{{ $booking->expected_minutes }}">


            <!-- Transaction No -->
            <div class="mb-3">
                <label for="transaction_no" class="form-label">GCash Transaction No.</label>
                <input type="text" name="transaction_no" class="form-control" 
                    value="{{ $booking->transaction_no }}" required>
            </div>


        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-success btn-sm">Update Booking</button>
            <a href="{{ route('staff.bookings.index') }}" class="btn btn-secondary btn-sm">Back</a>
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
        let endH = Math.floor(totalMinutes / 60) % 24; // wrap around 24h if needed
        let endM = totalMinutes % 60;

        endInput.value = String(endH).padStart(2, "0") + ":" + String(endM).padStart(2, "0");
    }

    // Auto-update end time when start time changes
    startInput.addEventListener("change", updateEndTime);

    // Run once on load (for edit pages)
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

        // Show date badge
        try {
            const localeDate = new Date(dateVal + 'T00:00:00').toLocaleDateString();
            dateBadge.textContent = `for ${localeDate}`;
            dateBadge.style.display = "inline-block";
        } catch {
            dateBadge.style.display = "none";
        }

        // Filter bookings
        const slots = bookings
            .filter(s => s.court_id == courtId && s.date === dateVal)
            .sort((a, b) => a.start_time.localeCompare(b.start_time));

        if (slots.length === 0) {
            const nice = new Date(dateVal + 'T00:00:00').toLocaleDateString();
            list.innerHTML = `<p class="text-success mb-0">No booked slots for this court on ${nice}.</p>`;
            return;
        }

        // Render slots
        const frag = document.createDocumentFragment();
        slots.forEach(s => {
            const item = document.createElement("div");
            if (s.status === "confirmed") {
                item.className = "alert alert-danger py-2 mb-2";
                item.textContent = `${to12h(s.start_time)} – ${to12h(s.end_time)} (Confirmed)`;
            } else {
                item.className = "alert alert-warning py-2 mb-2";
                item.textContent = `${to12h(s.start_time)} – ${to12h(s.end_time)} (Pending)`;
            }
            frag.appendChild(item);
        });

        list.innerHTML = "";
        list.appendChild(frag);
    }

    if (dateInput) {
        dateInput.addEventListener("change", renderBooked);
        renderBooked(); // run once on page load (useful for edit mode)
    }
});

// Booking date restrictions
const dateInput = document.getElementById("booking_date");
const today = new Date();
const minDate = new Date(today); minDate.setDate(today.getDate() + 1);
const maxDate = new Date(today); maxDate.setDate(today.getDate() + 14);

function formatDate(date) {
    const yyyy = date.getFullYear();
    const mm = String(date.getMonth() + 1).padStart(2, '0');
    const dd = String(date.getDate()).padStart(2, '0');
    return `${yyyy}-${mm}-${dd}`;
}

dateInput.min = formatDate(minDate);
dateInput.max = formatDate(maxDate);


</script>

@endpush
