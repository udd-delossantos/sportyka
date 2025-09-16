@extends('layouts.customer.app')

@section('content')
<div class="container">
     <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h2 class="text-primary"><strong>Book Session</strong></h2>
            <p class="mb-0">Fill out the form below to request a court booking.</p>
        </div>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('customer.booking_requests.store') }}" method="POST">
        @csrf

  <!-- Step 1: Select Court --> 
<div class="card mb-4 shadow">
   <div class="card-header">
      <h5 class="mb-0"><strong>Select Court</strong></h5>
   </div>
   <div class="card-body">
      <div class="row g-3">
         @foreach($courts as $court) 
         <div class="col-md-4">
            <label class="w-100">
               <input type="radio" name="court_id" value="{{ $court->id }}" data-rate="{{ $court->hourly_rate }}" class="btn-check court-radio" required> 
               <div class="p-3 border rounded text-center court-option h-100 hover-shadow">
                  <div class="fw-bold fs-5">{{ $court->name }}</div>
                  <div class="text-muted">{{ $court->sport }}</div>
                  <div class="text-primary fw-bold">₱{{ $court->hourly_rate }}/hour</div>
               </div>
            </label>
         </div>
         @endforeach 
      </div>
   </div>
</div>

<!-- Step 2: Show Booked Slots -->
<div class="card mb-4 shadow" id="bookedSlotsContainer" style="display:none;">
   <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0"><strong>Booked Time Slots</strong></h5>
      <small class="text-muted" id="bookedDateBadge" style="display:none;"></small> 
   </div>
   <div class="card-body" id="bookedSlotsList">
      <p class="text-muted mb-0">Select a court and a date to view booked slots.</p>
   </div>
</div>

<!-- Step 3: Select Date, Time & Duration -->
<div class="card mb-4 shadow">
    <div class="card-header">
        <h5 class="mb-0"><strong>Date, Time, & Duration</strong></h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col md-6">
                <div class="mb-3">
                    <label>Date</label>
                    <input type="date" name="booking_date" id="booking_date" class="form-control" required>
                </div>
            </div>
            <div class="col md-6">
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
            </div>
        </div>
        <div class="row">
            <div class="col md-6">
                 <div class="mb-3">
                    <label>Start Time</label>
                    <input type="time" name="start_time" class="form-control" required>
                </div>
            </div>
            <div class="col md-6">
                <div class="mb-3">
                    <label>End Time</label>
                    <input type="time" name="end_time" class="form-control" required readonly>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Step 4: Partial Payment -->
<div class="card mb-4 shadow">
    <div class="card-header">
        <h5 class="mb-0"><strong>Partial Payment</strong></h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col md-6">
                <div class="mb-3">
                       {{-- GCash Transaction Number (hidden unless GCash selected) --}}
                <div class="mb-3" id="transactionGroup" >
                    <label for="transaction_no">GCash Transaction No. (13 digits)</label>
                    <input
                        type="text"
                        name="transaction_no"
                        id="transaction_no"
                        class="form-control"
                        maxlength="13"
                        inputmode="numeric"
                        pattern="\d{13}"
                        placeholder="Enter 13-digit code"
                    >
                    <div class="invalid-feedback">Please enter exactly 13 digits (numbers only).</div>
                </div>
                </div>
            </div>
          
            <div class="col md-6">
                  <div class="mb-3">
                    <label class="fw-semibold">50% Down Payment</label>
                    <input type="text" id="computedAmount" class="form-control fw-bold" readonly>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Step 5: Summary -->
<div class="card mb-4 shadow">
    <div class="card-header">
        <h5 class="mb-0"><strong>Summary</strong></h5>
    </div>
    <div class="card-body" id="summaryBox">
        <p class="text-muted mb-0">Your booking details will appear here.</p>
    </div>
</div>

<!-- GCash QR Code Section -->
<div class="card mb-4 shadow">
    <div class="card-header">
        <h5 class="mb-0"><strong>GCash QR Code</strong></h5>
    </div>
    <div class="card-body text-center">
        <p class="mb-3">Scan the QR Code below to pay your 50% down payment:</p>
        <img src="{{ asset('img/gcash_qr.jpg') }}" alt="GCash QR Code" class="img-fluid rounded shadow-sm" style="max-width:300px;">
        <p class="mt-3 text-muted">Make sure to copy your 13-digit Transaction Number from your GCash receipt and enter it in the form above.</p>
    </div>
</div>

<div class="alert alert-warning mb-4">
    <strong>Note:</strong> Please double-check your booking details before submitting. 
    Once booking is confirmed, you may have to message our Facebook page for your request to be changed. 
    A 50% down payment is required to confirm your booking. 
    Make sure to enter the correct 13-digit transaction/reference number.
</div>

<div class="text-center mb-4">
    <button type="submit" class="btn btn-success btn-lg px-5">Submit Booking</button>
</div>
</form>
</div>

<style>
    .court-option {
        transition: all 0.3s ease-in-out;
    }
    .court-option:hover, 
    input[name="court_id"]:checked + .court-option {
        border: 2px solid #0d6efd;
        background-color: #f8f9fa;
        transform: translateY(-3px);
    }
    .hover-shadow:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
</style>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const bookings = @json($allBookings); 
    const courtRadios = document.querySelectorAll(".court-radio");
    const dateInput = document.querySelector('input[name="booking_date"]');
    const container = document.getElementById("bookedSlotsContainer");
    const list = document.getElementById("bookedSlotsList");
    const dateBadge = document.getElementById("bookedDateBadge");

    function to12h(HHmm) {
        const [H, M] = HHmm.split(':').map(Number);
        const ampm = H >= 12 ? 'PM' : 'AM';
        const h = (H % 12) || 12;
        return `${h}:${String(M).padStart(2, '0')} ${ampm}`;
    }

    function selectedCourtId() {
        const c = document.querySelector('.court-radio:checked');
        return c ? c.value : null;
    }

    function renderBooked() {
        const courtId = selectedCourtId();
        const dateVal = dateInput ? dateInput.value : '';

        if (!courtId) {
            container.style.display = 'none';
            return;
        }
        container.style.display = 'block';

        if (!dateVal) {
            dateBadge.style.display = 'none';
            list.innerHTML = `<p class="text-muted mb-0">Pick a date to view booked slots for this court.</p>`;
            return;
        }

        try {
            const localeDate = new Date(dateVal + 'T00:00:00').toLocaleDateString();
            dateBadge.textContent = `for ${localeDate}`;
            dateBadge.style.display = 'inline-block';
        } catch {
            dateBadge.style.display = 'none';
        }

        const slots = bookings
            .filter(s => s.court_id == courtId && s.date === dateVal)
            .sort((a, b) => a.start_time.localeCompare(b.start_time));

        if (slots.length === 0) {
            const nice = new Date(dateVal + 'T00:00:00').toLocaleDateString();
            list.innerHTML = `<p class="text-success mb-0">No booked slots for this court on ${nice}.</p>`;
            return;
        }

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

    courtRadios.forEach(r => r.addEventListener("change", renderBooked));
    if (dateInput) dateInput.addEventListener("change", renderBooked);

    // ==============================
    // SUMMARY + COMPUTED AMOUNT
    // ==============================
    const hoursInput = document.querySelector('select[name="hours"]');
    const minutesInput = document.querySelector('select[name="minutes"]');
    const startTimeInput = document.querySelector('input[name="start_time"]');
    const endTimeInput = document.querySelector('input[name="end_time"]');
    const computedAmount = document.getElementById('computedAmount');
    const summaryBox = document.getElementById('summaryBox');

    function computeAmount() {
        const hours = parseInt(hoursInput.value) || 0;
        const minutes = parseInt(minutesInput.value) || 0;
        const selectedCourt = document.querySelector('input[name="court_id"]:checked');

        if (!selectedCourt) {
            computedAmount.value = '';
            return;
        }

        const rate = parseFloat(selectedCourt.getAttribute('data-rate'));
        const totalMinutes = (hours * 60) + minutes;

        if (totalMinutes <= 0 || isNaN(rate)) {
            computedAmount.value = '';
            return;
        }

        const ratePerMinute = rate / 60;
        const total = totalMinutes * ratePerMinute * 0.5;
        computedAmount.value = '₱' + total.toFixed(2);
    }

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

    function renderSummary() {
        const selectedCourt = document.querySelector('input[name="court_id"]:checked');
        const court = selectedCourt ? selectedCourt.closest('label').querySelector('.fw-bold.fs-5').innerText : '';
        const rate = selectedCourt ? selectedCourt.getAttribute('data-rate') : '';
        const date = dateInput ? new Date(dateInput.value).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric'}) : '';
        const start = startTimeInput.value ? new Date(`1970-01-01T${startTimeInput.value}`).toLocaleTimeString('en-US',{hour:'numeric',minute:'2-digit'}) : '';
        const end = endTimeInput.value ? new Date(`1970-01-01T${endTimeInput.value}`).toLocaleTimeString('en-US',{hour:'numeric',minute:'2-digit'}) : '';
        const hours = hoursInput.value || 0;
        const minutes = minutesInput.value || 0;
        const dp = computedAmount.value;

        if (!court || !date || !start || !end) {
            summaryBox.innerHTML = `<p class="text-muted mb-0">Your booking details will appear here.</p>`;
            return;
        }

        summaryBox.innerHTML = `
            <ul class="list-group">
                <li class="list-group-item"><strong>Court:</strong> ${court} (₱${rate}/hour)</li>
                <li class="list-group-item"><strong>Date:</strong> ${date}</li>
                <li class="list-group-item"><strong>Time:</strong> ${start} – ${end}</li>
                <li class="list-group-item"><strong>Duration:</strong> ${hours}h ${minutes}m</li>
                <li class="list-group-item"><strong>50% Down Payment:</strong> ${dp}</li>
            </ul>
        `;
    }

    [hoursInput, minutesInput].forEach(input => input.addEventListener('change', () => { computeAmount(); updateEndTime(); renderSummary(); }));
    startTimeInput.addEventListener('input', () => { updateEndTime(); renderSummary(); });
    dateInput.addEventListener('input', renderSummary);
    courtRadios.forEach(radio => radio.addEventListener('change', () => { computeAmount(); renderSummary(); }));
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


document.addEventListener('DOMContentLoaded', function () {
    const transactionInput = document.getElementById('transaction_no');
    if (!transactionInput) return;

    // Allow only numbers & max 13 digits while typing
    transactionInput.addEventListener('keydown', function (e) {
        const allowedKeys = ['Backspace','Delete','ArrowLeft','ArrowRight','Tab','Home','End'];
        if (allowedKeys.includes(e.key) || e.ctrlKey || e.metaKey) return;
        if (!/^\d$/.test(e.key)) {
            e.preventDefault();
            return;
        }
        const currentDigits = (transactionInput.value || '').replace(/\D/g,'').length;
        if (currentDigits >= 13) {
            e.preventDefault();
        }
    });

    // Sanitize on input (covers paste, mobile, IME)
    transactionInput.addEventListener('input', function () {
        let v = transactionInput.value || '';
        v = v.replace(/\D/g, '').slice(0, 13);
        transactionInput.value = v;
    });

    // Final check on form submit
    const form = transactionInput.closest('form');
    if (form) {
        form.addEventListener('submit', function (e) {
            let v = transactionInput.value || '';
            if (v.length !== 13) {
                e.preventDefault();
                transactionInput.classList.add('is-invalid');
                transactionInput.focus();
                return false;
            } else {
                transactionInput.classList.remove('is-invalid');
            }
        });
    }
});
</script>
</script>
@endpush