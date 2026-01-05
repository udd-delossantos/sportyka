@extends('layouts.staff.app')
@section('title', 'Record Payment')
@section('content')

<div class="container-fluid">
    
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Process Payment</h1>
        <a href="{{ route('staff.payments.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 mr-2"></i> Back to List
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            
            <form method="POST" action="{{ route('staff.payments.store') }}">
                @csrf
                <div class="card shadow mb-4 border-left-primary">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">New Transaction</h6>
                    </div>
                    <div class="card-body">
                        
                        @if(session('error'))
                        <div class="alert alert-danger border-left-danger" role="alert">
                            <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
                        </div>
                        @endif

                        <div class="form-group">
                            <label for="game_session_id" class="font-weight-bold text-gray-700">Select Completed Session</label>
                            <select name="game_session_id" id="game_session_id" class="form-control" required style="height: calc(1.5em + .75rem + 2px);">
                                <option value="" disabled selected>-- Choose a session --</option>
                                @foreach($sessions as $session)
                                    <option value="{{ $session->id }}" data-amount="{{ $session->amount_paid }}">
                                        {{ $session->customer_name }} — {{ $session->court->name  }} (₱{{ number_format($session->amount_paid, 2) }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Only pending/unpaid completed sessions are listed.</small>
                        </div>

                        <hr class="sidebar-divider my-4">

                        <div class="form-group">
                            <label for="amount" class="font-weight-bold text-gray-700">Amount Due</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-success text-white border-success">₱</span>
                                </div>
                                <input 
                                    type="number" 
                                    name="amount" 
                                    id="amount" 
                                    class="form-control form-control-lg bg-light text-success font-weight-bold" 
                                    step="0.01" 
                                    readonly 
                                    required
                                >
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="payment_method" class="font-weight-bold text-gray-700">Payment Method</label>
                            <select name="payment_method" id="payment_method" class="form-control" required>
                                <option value="cash">Cash</option>
                                <option value="gcash">GCash</option>
                            </select>
                        </div>

                        <div class="form-group" id="transactionGroup" style="display:none;">
                            <label for="transaction_no" class="font-weight-bold text-info">GCash Ref No. (13 digits)</label>
                            <input
                                type="text"
                                name="transaction_no"
                                id="transaction_no"
                                class="form-control border-info"
                                maxlength="13"
                                inputmode="numeric"
                                pattern="\d{13}"
                                placeholder="Example: 0001234567890"
                            >
                            <div class="invalid-feedback">Please enter exactly 13 digits (numbers only).</div>
                            <small class="form-text text-muted">Enter the reference number from the receipt.</small>
                        </div>

                    </div>    

                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-success shadow-sm btn-lg">
                            <i class="fas fa-check mr-2"></i> Confirm Payment
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// EXACT COPY OF YOUR ORIGINAL SCRIPT
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