@extends('layouts.customer.app')

@section('content')
<div class="container">
    <!-- Page Header -->
    <div class="bg-white border-bottom shadow-sm py-3 mb-4">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0 text-primary">Book Session</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Book Session</li>
                </ol>
            </nav>
        </div>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="px-4 py-5 mb-4 text-center shadow rounded-4 bg-light " style="background: linear-gradient(135deg, #e6f0ff, #f8fbff);">
      <img class="d-block mx-auto mb-4 rounded-circle border border-3 border-primary shadow-sm" 
        src="{{ asset('img/sk-logo.png') }}" 
        alt="Sporty Ka Logo" 
        width="100" 
        height="100">

    <h2 class="text-primary">Dear Customer</h2>
    <div class="px-5">
        <p class="lead mb-4">Before booking, please note that a 50% down payment is required, and GCash payments must include a valid 13-digit transaction number. 
       Double bookings are not allowed, so check your court, date, and time carefully. Arrive at least 15 minutes early, and don’t forget to bring 
       your own gear and accessories (rackets, balls, shuttlecocks, etc.) as these are not included in the rental.</p>

    </div>
    
    <hr class="mx-auto" style="width: 120px; height: 3px; background-color: #0d6efd; border: none; border-radius: 2px;">
    <div class="col-lg-8 mx-auto">
        <form action="{{ route('customer.booking_requests.store') }}" method="POST">
            @csrf
            <!-- Step 1 -->
            <div class="step">
                <div class="card mb-4 shadow">
                    <div class="card-header">
                        <h5 class="mb-0 text-primary">Step 1: Select Court</h5>
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
            </div>

            <!-- Step 2 -->
            <div class="step d-none">
                <div class="card mb-4 shadow">
                    <div class="card-header">
                        <h5 class="mb-0 text-primary">Step 2: Pick Date, Duration, and Time</h5>
                    </div>
                    <div class="card-body">
                        {{-- Keep your date & time fields here --}}
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label>Date</label>
                                <input type="date" name="booking_date" id="booking_date" class="form-control" required>
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
                            <div class="col-md-6 mb-3">
                                <label>Start Time</label>
                                <input type="time" name="start_time" class="form-control" required>
                            </div>
                            <div class="col md-6">
                                <div class="mb-3">
                                    <label>End Time</label>
                                    <input type="time" name="end_time" class="form-control" required readonly>
                                </div>
                            </div>
                        </div>
                        <!--Display here the booked time slots-->
                        
                        <!-- Booked Slots Display -->
                        <div id="bookedSlotsContainer" class="mt-3" style="display:none;">
                            <h6 class="fw-bold">Booked Slots <span id="bookedDateBadge" class="badge bg-primary text-light" style="display:none;"></span></h6>
                            <div id="bookedSlotsList"></div>
                        </div>
                    </div>
                </div>
            </div>

            

            <!-- Step 3 -->
            <div class="step d-none">
                <div class="card mb-4 shadow">
                    <div class="card-header">
                        <h5 class="mb-0 text-primary">Step 3: Partial Payment</h5>
                    </div>
                    <div class="card-body">
                        {{-- GCash transaction + QR code --}}
                        <div class="alert alert-warning mb-3">
                            <p class="mb-0">Scan the QR code and pay the 50% down payment. Copy the 13-digit code reference number and paste it below. Please send the exact amount to avoid delays</p>
                        </div>
                        <div class="text-center mb-3">
                            <img src="{{ asset('img/gcash_qr.jpg') }}" alt="GCash QR Code" class="img-fluid" style="max-width:300px;">
                        </div>
                        
                        <div class="mb-3">
                            <label>50% Down Payment</label>
                            <input type="text" id="computedAmount" class="form-control fw-bold" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="transaction_no">GCash Transaction No.</label>
                            <input type="text" name="transaction_no" id="transaction_no" class="form-control" maxlength="13" pattern="\d{13}">
                        </div>
                        
                    </div>
                </div>
            </div>

            <!-- Step 4 -->
            <div class="step d-none">
                <div class="card mb-4 shadow">
                    <div class="card-header">
                        <h5 class="mb-0 text-primary">Step 4: Summary</h5>
                    </div>
                    <div class="card-body" id="summaryBox">
                        <p class="text-muted mb-0">Your booking details will appear here.</p>
                    </div>
                </div>

                <div class="text-center mb-4">
                    <button type="submit" class="btn btn-success btn-lg px-5">Submit Booking</button>
                </div>
            </div>

                <div class="d-grid gap-3 d-sm-grid justify-content-center">
                    <button type="button" id="prevBtn" class="btn btn-secondary d-none">Back</button>
                <button type="button" id="nextBtn" class="btn btn-primary">Next</button>
                </div>

            <!-- Navigation Buttons -->
        </form>
        
    

    </div>
</div>

    

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
            if (container) container.style.display = 'none';
            return;
        }
        if (container) container.style.display = 'block';

        if (!dateVal) {
            if (dateBadge) dateBadge.style.display = 'none';
            if (list) list.innerHTML = `<p class="text-muted mb-0">Pick a date to view booked slots for this court.</p>`;
            return;
        }

        try {
            const localeDate = new Date(dateVal + 'T00:00:00').toLocaleDateString();
            if (dateBadge) {
                dateBadge.textContent = `for ${localeDate}`;
                dateBadge.style.display = 'inline-block';
            }
        } catch {
            if (dateBadge) dateBadge.style.display = 'none';
        }

        const slots = bookings
            .filter(s => s.court_id == courtId && s.date === dateVal)
            .sort((a, b) => a.start_time.localeCompare(b.start_time));

        if (!list) return;

        if (slots.length === 0) {
            const nice = new Date(dateVal + 'T00:00:00').toLocaleDateString();
            list.innerHTML = `<p class="text-success mb-0">No booked slots yet for this date.</p>`;
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
    const transactionInput = document.getElementById('transaction_no');

    function computeAmount() {
        const hours = parseInt(hoursInput ? hoursInput.value : 0) || 0;
        const minutes = parseInt(minutesInput ? minutesInput.value : 0) || 0;
        const selectedCourt = document.querySelector('input[name="court_id"]:checked');

        if (!selectedCourt) {
            if (computedAmount) computedAmount.value = '';
            return;
        }

        const rate = parseFloat(selectedCourt.getAttribute('data-rate'));
        const totalMinutes = (hours * 60) + minutes;

        if (totalMinutes <= 0 || isNaN(rate)) {
            if (computedAmount) computedAmount.value = '';
            return;
        }

        const ratePerMinute = rate / 60;
        const total = totalMinutes * ratePerMinute * 0.5;
        if (computedAmount) computedAmount.value = '₱' + total.toFixed(2);
    }

    function updateEndTime() {
        if (!startTimeInput) return;
        const startTime = startTimeInput.value;
        const hours = parseInt(hoursInput ? hoursInput.value : 0) || 0;
        const minutes = parseInt(minutesInput ? minutesInput.value : 0) || 0;

        if (!startTime) return;

        let [startHour, startMinute] = startTime.split(':').map(Number);
        let startDate = new Date();
        startDate.setHours(startHour, startMinute, 0);

        startDate.setMinutes(startDate.getMinutes() + (hours * 60) + minutes);

        let endHour = String(startDate.getHours()).padStart(2, '0');
        let endMinute = String(startDate.getMinutes()).padStart(2, '0');
        if (endTimeInput) endTimeInput.value = `${endHour}:${endMinute}`;
    }

    function renderSummary() {
        const selectedCourt = document.querySelector('input[name="court_id"]:checked');
        const court = selectedCourt ? (selectedCourt.closest('label')?.querySelector('.fw-bold.fs-5')?.innerText || '') : '';
        const rate = selectedCourt ? selectedCourt.getAttribute('data-rate') : '';
        const date = dateInput ? new Date(dateInput.value).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric'}) : '';
        const start = startTimeInput && startTimeInput.value ? new Date(`1970-01-01T${startTimeInput.value}`).toLocaleTimeString('en-US',{hour:'numeric',minute:'2-digit'}) : '';
        const end = endTimeInput && endTimeInput.value ? new Date(`1970-01-01T${endTimeInput.value}`).toLocaleTimeString('en-US',{hour:'numeric',minute:'2-digit'}) : '';
        const hours = hoursInput ? hoursInput.value : 0;
        const minutes = minutesInput ? minutesInput.value : 0;
        const dp = computedAmount ? computedAmount.value : '';
        const transactionNo = transactionInput ? transactionInput.value : '';

        if (!court || !date || !start || !end) {
            if (summaryBox) summaryBox.innerHTML = `<p class="text-muted mb-0">Your booking details will appear here.</p>`;
            return;
        }

        if (summaryBox) summaryBox.innerHTML = `
            <ul class="list-group">
                <li class="list-group-item"><strong>Court:</strong> ${court} (₱${rate}/hour)</li>
                <li class="list-group-item"><strong>Date:</strong> ${date}</li>
                <li class="list-group-item"><strong>Time:</strong> ${start} – ${end}</li>
                <li class="list-group-item"><strong>Duration:</strong> ${hours}h ${minutes}m</li>
                <li class="list-group-item"><strong>50% Down Payment:</strong> ${dp}</li>
                ${transactionNo ? `<li class="list-group-item"><strong>GCash Reference No.:</strong> ${transactionNo}</li>` : ''}
            </ul>
        `;
    }

    if (hoursInput && minutesInput) {
        [hoursInput, minutesInput].forEach(input => input.addEventListener('change', () => { computeAmount(); updateEndTime(); renderSummary(); }));
    }
    if (startTimeInput) startTimeInput.addEventListener('input', () => { updateEndTime(); renderSummary(); });
    if (dateInput) dateInput.addEventListener('input', renderSummary);
    courtRadios.forEach(radio => radio.addEventListener('change', () => { computeAmount(); renderSummary(); }));
    if (transactionInput) transactionInput.addEventListener('input', renderSummary);

    // Booking date restrictions (no persistence)
    if (dateInput) {
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
    }

    // transaction input helpers (numeric only, max 13)
    if (transactionInput) {
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

        transactionInput.addEventListener('input', function () {
            let v = transactionInput.value || '';
            v = v.replace(/\D/g, '').slice(0, 13);
            transactionInput.value = v;
        });

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
    }

    // ==========================
    // MULTI-STEP LOGIC (NO persistence)
    // ==========================
    const steps = document.querySelectorAll(".step");
    const nextBtn = document.getElementById("nextBtn");
    const prevBtn = document.getElementById("prevBtn");
    // start always at step 0 (no localStorage)
    let currentStep = 0;

    function showStep(index) {
        steps.forEach((step, i) => {
            step.classList.toggle("d-none", i !== index);
        });
        if (prevBtn) prevBtn.classList.toggle("d-none", index === 0);
        if (nextBtn) nextBtn.classList.toggle("d-none", index === steps.length - 1);
    }

    function showError(message, stepIndex) {
        clearError();
        const targetStep = steps[stepIndex] || steps[currentStep];
        if (!targetStep) return;
        const cardBody = targetStep.querySelector(".card-body") || targetStep;
        let errorDiv = document.createElement("div");
        errorDiv.id = "stepError";
        errorDiv.className = "alert alert-danger";
        errorDiv.style.marginBottom = "1rem";
        errorDiv.textContent = message;
        cardBody.prepend(errorDiv);
    }

    function clearError() {
        const existing = document.getElementById("stepError");
        if (existing) existing.remove();
    }

    function validateStep(stepIndex) {
        clearError();
        const step = steps[stepIndex];
        if (!step) return true;

        // find required fields within this step (inputs/selects/textareas)
        const inputs = step.querySelectorAll("input[required], select[required], textarea[required]");
        for (const input of inputs) {
            // radio groups -> ensure at least one in group is checked (within the step)
            if (input.type === "radio") {
                const name = input.name;
                const checked = step.querySelector(`input[name="${name}"]:checked`);
                if (!checked) {
                    input.focus();
                    showError("Please complete all required fields before continuing.", stepIndex);
                    return false;
                }
                continue;
            }

            // checkbox: must be checked
            if (input.type === "checkbox") {
                if (!input.checked) {
                    input.focus();
                    showError("Please complete all required fields before continuing.", stepIndex);
                    return false;
                }
                continue;
            }

            // normal inputs/selects/textareas: non-empty trimmed value
            const val = input.value;
            if (typeof val === 'undefined' || val === null || String(val).trim() === '') {
                input.focus();
                showError("Please complete all required fields before continuing.", stepIndex);
                return false;
            }
        }

        // extra check for Step 2 (index 1): slot conflict
        if (stepIndex === 1) {
            const courtId = document.querySelector('.court-radio:checked')?.value;
            const dateVal = document.querySelector('input[name="booking_date"]')?.value;
            const startVal = document.querySelector('input[name="start_time"]')?.value;
            const endVal = document.querySelector('input[name="end_time"]')?.value;

            if (courtId && dateVal && startVal && endVal) {
                const conflicts = bookings.filter(s =>
                    s.court_id == courtId &&
                    s.date === dateVal &&
                    !(
                        endVal <= s.start_time || 
                        startVal >= s.end_time
                    )
                );
                if (conflicts.length > 0) {
                    showError("The selected time overlaps with an already booked slot.", stepIndex);
                    return false;
                }
            }
        }

        // === NEW: enforce transaction number on Step 3 (index 2) ===
        if (stepIndex === 2) {
            // prefer an input inside the current step; fallback to global by id
            const tx = step.querySelector('#transaction_no') || document.getElementById('transaction_no');
            if (!tx) {
                showError("Transaction number is required.", stepIndex);
                return false;
            }
            const digits = (tx.value || '').replace(/\D/g, '');
            if (digits.length !== 13) {
                tx.classList.add('is-invalid');
                tx.focus();
                showError("Please enter a valid 13-digit GCash transaction number.", stepIndex);
                return false;
            } else {
                tx.classList.remove('is-invalid');
            }
        }

        return true;
    }

    if (nextBtn) {
        nextBtn.addEventListener("click", function () {
            if (!validateStep(currentStep)) return;
            if (currentStep < steps.length - 1) {
                currentStep++;
                showStep(currentStep);
            }
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener("click", function () {
            if (currentStep > 0) {
                currentStep--;
                showStep(currentStep);
            }
        });
    }

    // initialize
    showStep(currentStep);
});
</script>
@endpush
