<x-layouts.app :pageTitle="__('Payments')">
    <style>
        .page-header { margin-bottom: 24px; }
        .page-header h2 { font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 700; color: #0f172a; margin-bottom: 4px; }
        .page-header p { font-size: 13px; color: #64748b; }

        .info-banner {
            display: flex; align-items: center; gap: 10px;
            background: #eef2ff; border: 1px solid #c7d2fe;
            padding: 14px 18px; border-radius: 12px; margin-bottom: 24px;
            font-size: 13px; color: #4338ca;
        }
        .info-banner svg { width: 20px; height: 20px; flex-shrink: 0; }

        .fees-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(360px, 1fr)); gap: 16px; }

        .fee-card {
            background: white; border-radius: 14px; padding: 24px;
            border: 1px solid #f1f5f9; box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            transition: all 0.2s;
        }
        .fee-card:hover { border-color: #c7d2fe; box-shadow: 0 4px 14px rgba(79,70,229,0.08); }

        .fee-year { font-family: 'Playfair Display', serif; font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 4px; }
        .fee-dates { font-size: 12px; color: #94a3b8; margin-bottom: 20px; }

        .fee-amount-row { display: flex; align-items: baseline; gap: 6px; margin-bottom: 20px; }
        .fee-amount { font-family: 'Playfair Display', serif; font-size: 32px; font-weight: 700; color: #4F46E5; }
        .fee-currency { font-size: 14px; font-weight: 600; color: #94a3b8; text-transform: uppercase; }

        .child-select-group { margin-bottom: 16px; }
        .child-select-label { font-size: 11.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.7px; margin-bottom: 6px; display: block; }
        .child-select {
            width: 100%; padding: 9px 14px; border: 1.5px solid #e2e8f0; border-radius: 8px;
            font-size: 13.5px; font-family: 'DM Sans', sans-serif; color: #374151;
            background: #fafafa; outline: none; transition: border 0.2s; box-sizing: border-box;
            cursor: pointer;
        }
        .child-select:focus { border-color: #4F46E5; box-shadow: 0 0 0 3px rgba(79,70,229,0.1); }

        .fee-status {
            display: flex; align-items: center; gap: 8px;
            padding: 10px 14px; border-radius: 10px; font-size: 13px; font-weight: 500;
            margin-bottom: 16px;
        }
        .fee-status-paid { background: #d1fae5; color: #059669; }
        .fee-status-pending { background: #fef9c3; color: #92400e; }
        .fee-status-available { background: #f8fafc; color: #64748b; }

        .btn-pay {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            width: 100%; padding: 12px; background: #4F46E5; color: white; border: none;
            border-radius: 10px; font-size: 14px; font-weight: 600;
            font-family: 'DM Sans', sans-serif; cursor: pointer; transition: all 0.2s;
            text-decoration: none;
        }
        .btn-pay:hover { background: #4338ca; }
        .btn-pay:disabled { background: #e2e8f0; color: #94a3b8; cursor: not-allowed; }
        .btn-pay svg { width: 18px; height: 18px; }

        .btn-history {
            display: flex; align-items: center; justify-content: center; gap: 6px;
            padding: 8px 16px; background: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe;
            border-radius: 8px; font-size: 12.5px; font-weight: 600;
            font-family: 'DM Sans', sans-serif; text-decoration: none; transition: all 0.2s;
        }
        .btn-history:hover { background: #4338ca; color: white; }

        .empty-state { text-align: center; padding: 80px 20px; color: #cbd5e1; background: white; border-radius: 14px; border: 1px solid #f1f5f9; }
        .empty-state svg { margin-bottom: 16px; }
        .empty-state h3 { font-family: 'Playfair Display', serif; font-size: 18px; color: #94a3b8; margin-bottom: 8px; }
        .empty-state p { font-size: 13px; color: #cbd5e1; }

        .history-link { text-align: center; margin-top: 24px; }

        .test-banner {
            display: flex; align-items: center; gap: 10px;
            background: #fef3c7; border: 1px solid #fde68a;
            padding: 14px 18px; border-radius: 12px; margin-bottom: 24px;
            font-size: 13px; color: #92400e;
        }
        .test-banner svg { width: 20px; height: 20px; flex-shrink: 0; }

        .modal-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.5); z-index: 1000;
            align-items: center; justify-content: center; padding: 20px;
        }
        .modal-overlay.active { display: flex; }
        .modal {
            background: white; border-radius: 16px; padding: 32px;
            max-width: 440px; width: 100%; box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }
        .modal-title { font-family: 'Playfair Display', serif; font-size: 20px; font-weight: 700; color: #0f172a; margin-bottom: 4px; }
        .modal-subtitle { font-size: 13px; color: #64748b; margin-bottom: 24px; }
        .modal-summary {
            background: #f8fafc; border-radius: 10px; padding: 16px;
            margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;
        }
        .modal-summary-label { font-size: 12px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .modal-summary-value { font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 700; color: #4F46E5; }
        .modal-summary-currency { font-size: 13px; color: #94a3b8; font-weight: 600; }

        .form-group { margin-bottom: 14px; }
        .form-label { font-size: 11.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.7px; margin-bottom: 6px; display: block; }
        .form-input {
            width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 8px;
            font-size: 14px; font-family: 'DM Sans', sans-serif; color: #374151;
            background: #fafafa; outline: none; transition: border 0.2s; box-sizing: border-box;
        }
        .form-input:focus { border-color: #4F46E5; box-shadow: 0 0 0 3px rgba(79,70,229,0.1); }
        .form-row { display: flex; gap: 12px; }
        .form-row .form-group { flex: 1; }
        .test-card-hint {
            font-size: 11.5px; color: #94a3b8; margin-top: 4px; padding: 8px 12px;
            background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; color: #15803d;
        }
        .modal-actions { display: flex; gap: 12px; margin-top: 20px; }
        .btn-submit {
            flex: 1; padding: 12px; background: #4F46E5; color: white; border: none;
            border-radius: 10px; font-size: 14px; font-weight: 600;
            font-family: 'DM Sans', sans-serif; cursor: pointer; transition: all 0.2s;
        }
        .btn-submit:hover { background: #4338ca; }
        .btn-cancel {
            padding: 12px 20px; background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0;
            border-radius: 10px; font-size: 14px; font-weight: 600;
            font-family: 'DM Sans', sans-serif; cursor: pointer; transition: all 0.2s;
        }
        .btn-cancel:hover { background: #f1f5f9; }
    </style>

    <div class="page-header">
        <h2>{{ __("Tuition Payments") }}</h2>
        <p>{{ __("Pay tuition fees securely via Stripe") }}</p>
    </div>

    <div class="test-banner">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>{{ __("Test Mode: Payments are simulated. Use card number 4242 4242 4242 4242, any future date, and any CVC.") }}</span>
    </div>

    @if($academicYears->isEmpty())
        <div class="empty-state">
            <svg width="56" height="56" fill="none" stroke="#e2e8f0" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            <h3>{{ __("No Tuition Fees Available") }}</h3>
            <p>{{ __("There are no academic years with tuition fees configured yet. Please check back later.") }}</p>
        </div>
    @else
        <div class="fees-grid">
            @foreach($academicYears as $year)
                @php
                    $fee = $year->tuitionFee;
                @endphp
                @if($fee)
                    <div class="fee-card">
                        <div class="fee-year">{{ $year->name }}</div>
                        <div class="fee-dates">{{ $year->start_date?->format('M Y') }} — {{ $year->end_date?->format('M Y') }}</div>

                        <div class="fee-amount-row">
                            <span class="fee-amount">{{ number_format((float) $fee->amount, 2) }}</span>
                            <span class="fee-currency">{{ strtoupper($fee->currency) }}</span>
                        </div>

                        @if($children->isEmpty())
                            <div class="fee-status fee-status-available">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"/></svg>
                                {{ __("No children linked to your account") }}
                            </div>
                        @else
                            <div class="child-select-group">
                                <label class="child-select-label">{{ __("Pay for student") }}</label>
                                <select class="child-select" onchange="updatePayButton(this, {{ $fee->id }})">
                                    @foreach($children as $child)
                                        @php
                                            $key = $year->id . '-' . $child->id;
                                            $existingPayment = $existingPayments->get($key);
                                            $childStatus = $existingPayment?->status?->value ?? 'available';
                                        @endphp
                                        <option value="{{ $child->id }}" data-status="{{ $childStatus }}" data-name="{{ $child->name }}">
                                            {{ $child->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="status-box-{{ $fee->id }}" class="fee-status fee-status-available">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                <span id="status-text-{{ $fee->id }}">{{ __("Awaiting payment") }}</span>
                            </div>

                            <button id="pay-btn-{{ $fee->id }}"
                               type="button"
                               data-year="{{ $year->name }}"
                               data-amount="{{ number_format((float) $fee->amount, 2, '.', '') }}"
                               data-currency="{{ strtoupper($fee->currency) }}"
                               onclick="openPaymentModal({{ $fee->id }}, {{ $children->first()->id }}, '{{ $year->name }}', {{ number_format((float) $fee->amount, 2, '.', '') }}, '{{ strtoupper($fee->currency) }}', '{{ $children->first()->name }}')"
                               class="btn-pay">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                <span id="pay-label-{{ $fee->id }}">{{ __("Pay Now") }}</span>
                            </button>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>

        <div class="history-link">
            <a href="{{ route('parent.payments.history') }}" class="btn-history" style="padding: 10px 24px; font-size: 13px;">
                {{ __("View Payment History") }}
            </a>
        </div>
    @endif

    {{-- Test Payment Modal --}}
    <div class="modal-overlay" id="payment-modal">
        <div class="modal">
            <div class="modal-title">{{ __("Complete Payment") }}</div>
            <div class="modal-subtitle" id="modal-subtitle"></div>

            <div class="modal-summary">
                <div>
                    <div class="modal-summary-label">{{ __("Amount Due") }}</div>
                    <div><span class="modal-summary-value" id="modal-amount"></span> <span class="modal-summary-currency" id="modal-currency"></span></div>
                </div>
            </div>

            <form id="test-payment-form" method="POST" action="">
                @csrf
                <input type="hidden" name="student_user_id" id="modal-student-id">

                <div class="form-group">
                    <label class="form-label">{{ __("Card Number") }}</label>
                    <input type="text" class="form-input" id="card-number" placeholder="4242 4242 4242 4242" maxlength="19" value="4242 4242 4242 4242">
                    <div class="test-card-hint">{{ __("Test card: 4242 4242 4242 4242") }}</div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">{{ __("Expiry") }}</label>
                        <input type="text" class="form-input" placeholder="12/30" maxlength="5" value="12/30">
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __("CVC") }}</label>
                        <input type="text" class="form-input" placeholder="123" maxlength="4" value="123">
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closePaymentModal()">{{ __("Cancel") }}</button>
                    <button type="submit" class="btn-submit">{{ __("Pay Now") }}</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    var currentFeeId = null;
    var currentStudentSelect = null;

    function updatePayButton(selectEl, feeId) {
        currentStudentSelect = selectEl;
        var selected = selectEl.options[selectEl.selectedIndex];
        var status = selected.dataset.status;
        var studentId = selected.value;
        var studentName = selected.dataset.name;

        var statusBox = document.getElementById('status-box-' + feeId);
        var statusText = document.getElementById('status-text-' + feeId);
        var payBtn = document.getElementById('pay-btn-' + feeId);
        var payLabel = document.getElementById('pay-label-' + feeId);

        if (status === 'succeeded') {
            statusBox.className = 'fee-status fee-status-paid';
            statusText.textContent = '{{ __("Paid successfully") }}';
            payBtn.style.display = 'none';
        } else if (status === 'pending') {
            statusBox.className = 'fee-status fee-status-pending';
            statusText.textContent = '{{ __("Payment in progress...") }}';
            payBtn.style.display = 'flex';
            payLabel.textContent = '{{ __("Complete Payment") }}';
        } else {
            statusBox.className = 'fee-status fee-status-available';
            statusText.textContent = '{{ __("Awaiting payment") }}';
            payBtn.style.display = 'flex';
            payLabel.textContent = '{{ __("Pay Now") }}';
        }

        payBtn.setAttribute('onclick', 'openPaymentModal(' + feeId + ', ' + studentId + ', \'' + payBtn.dataset.year + '\', ' + payBtn.dataset.amount + ', \'' + payBtn.dataset.currency + '\', \'' + studentName + '\')');
    }

    function openPaymentModal(feeId, studentId, yearName, amount, currency, studentName) {
        currentFeeId = feeId;
        document.getElementById('modal-subtitle').textContent = yearName + ' — ' + (studentName || '');
        document.getElementById('modal-amount').textContent = parseFloat(amount).toLocaleString('en-US', {minimumFractionDigits: 2});
        document.getElementById('modal-currency').textContent = currency;
        document.getElementById('modal-student-id').value = studentId;
        document.getElementById('test-payment-form').action = '{{ route("parent.payments.test-process", "__FEE__") }}'.replace('__FEE__', feeId);
        document.getElementById('payment-modal').classList.add('active');
    }

    function closePaymentModal() {
        document.getElementById('payment-modal').classList.remove('active');
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.child-select').forEach(function(select) {
            var feeId = select.onchange.toString().match(/(\d+)/)[1];
            updatePayButton(select, feeId);
        });

        document.getElementById('payment-modal').addEventListener('click', function(e) {
            if (e.target === this) closePaymentModal();
        });
    });
    </script>
</x-layouts.app>
