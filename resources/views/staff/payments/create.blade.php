@extends('layouts.staff.app')
@section('content')
<div class="container">
    <form method="POST" action="{{ route('staff.payments.store') }}">
        <div class="card shadow mb4">
            <div class="card-header pb-0">
                <h5><strong>Record Payment</strong></h5>
            </div>
            <div class="card-body">
                @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
                @endif
                @csrf

                <!-- Completed Session Dropdown -->
                <div class="mb-3">
                    <label for="game_session_id">Completed Session</label>
                    <select name="game_session_id" id="game_session_id" class="form-control" required>
                        @foreach($sessions as $session)
                            <option value="{{ $session->id }}" data-amount="{{ $session->amount_paid }}">
                                {{ $session->customer_name }} (₱{{ number_format($session->amount_paid, 2) }}) - {{ $session->court->name  }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Amount Paid (auto-filled & readonly) -->
                <div class="mb-3">
                    <label for="amount">Amount Paid</label>
                    <input 
                        type="number" 
                        name="amount" 
                        id="amount" 
                        class="form-control" 
                        step="0.01" 
                        readonly 
                        required
                    >
                </div>

                <!-- Payment Method -->
                <div class="mb-3">
                    <label for="payment_method">Method</label>
                    <select name="payment_method" id="payment_method" class="form-control" required>
                        <option value="cash">Cash</option>
                        <option value="gcash">GCash</option>
                    </select>
                </div>

                <!-- GCash Transaction Number (hidden unless GCash selected) -->
                <div class="mb-3" id="transactionGroup" style="display:none;">
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

            <div class="card-footer">
                <button type="submit" class="btn btn-success btn-sm">Pay</button>
                <a href="{{ route('staff.payments.index') }}" class="btn btn-secondary btn-sm">Back</a>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Elements
    const sessionSelect = document.getElementById('game_session_id');
    const amountInput = document.getElementById('amount');
    const paymentMethod = document.getElementById('payment_method');
    const transactionInput = document.getElementById('transaction_no');
    const transactionGroup = document.getElementById('transactionGroup');

    // Update the amount field based on selected session's data-amount
    function updateAmount() {
        if (!sessionSelect || !amountInput) return;
        const selectedOption = sessionSelect.options[sessionSelect.selectedIndex];
        const amount = selectedOption ? selectedOption.getAttribute('data-amount') : null;
        amountInput.value = amount ? parseFloat(amount).toFixed(2) : '';
    }

    // Show/hide transaction field depending on payment method
    function toggleTransactionField() {
        if (!paymentMethod || !transactionGroup || !transactionInput) return;
        if (paymentMethod.value === 'gcash') {
            transactionInput.required = true;
            transactionGroup.style.display = 'block';
        } else {
            transactionInput.required = false;
            transactionGroup.style.display = 'none';
            transactionInput.classList.remove('is-invalid');
            // clear field when not required (optional)
            transactionInput.value = '';
        }
    }

    // Remove non-digits and limit to 13 chars
    function sanitizeTransactionValue() {
        if (!transactionInput) return;
        let v = transactionInput.value || '';
        v = v.replace(/\D/g, '').slice(0, 13);
        transactionInput.value = v;
    }

    // Prevent invalid keystrokes and enforce length at typing time
    if (transactionInput) {
        transactionInput.addEventListener('keydown', function (e) {
            const allowedKeys = ['Backspace','Delete','ArrowLeft','ArrowRight','Tab','Home','End'];
            if (allowedKeys.includes(e.key) || e.ctrlKey || e.metaKey) return;
            // allow digits only
            if (!/^\d$/.test(e.key)) {
                e.preventDefault();
                return;
            }
            // prevent more than 13 digits
            const currentDigits = (transactionInput.value || '').replace(/\D/g,'').length;
            if (currentDigits >= 13) {
                e.preventDefault();
            }
        });

        // sanitize on input (covers paste, mobile, IME)
        transactionInput.addEventListener('input', sanitizeTransactionValue);
    }

    // Validate on form submit: if transaction is required, must be exactly 13 digits
    const form = sessionSelect ? sessionSelect.closest('form') : null;
    if (form) {
        form.addEventListener('submit', function (e) {
            // ensure amount is present (safety)
            updateAmount();

            if (transactionInput && transactionInput.required) {
                sanitizeTransactionValue();
                if ((transactionInput.value || '').length !== 13) {
                    e.preventDefault();
                    transactionInput.classList.add('is-invalid');
                    transactionInput.focus();
                    return false;
                } else {
                    transactionInput.classList.remove('is-invalid');
                }
            }
            return true;
        });
    }

    // Init
    updateAmount();
    toggleTransactionField();

    // Event listeners
    if (sessionSelect) sessionSelect.addEventListener('change', updateAmount);
    if (paymentMethod) paymentMethod.addEventListener('change', toggleTransactionField);
});
</script>
@endpush
