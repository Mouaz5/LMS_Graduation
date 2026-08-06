<x-layouts.app :pageTitle="__('Payment Success')">
    <style>
        .success-container { max-width: 560px; margin: 40px auto; text-align: center; }
        .success-icon {
            width: 80px; height: 80px; background: #d1fae5; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;
        }
        .success-icon svg { width: 40px; height: 40px; color: #059669; }
        .success-title { font-family: 'Playfair Display', serif; font-size: 26px; font-weight: 700; color: #0f172a; margin-bottom: 8px; }
        .success-subtitle { font-size: 14px; color: #64748b; margin-bottom: 32px; }

        .receipt-card {
            background: white; border-radius: 14px; padding: 28px;
            border: 1px solid #f1f5f9; box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            text-align: start; margin-bottom: 24px;
        }
        .receipt-row {
            display: flex; justify-content: space-between; align-items: center;
            padding: 12px 0; border-bottom: 1px solid #f8fafc;
        }
        .receipt-row:last-child { border-bottom: none; }
        .receipt-label { font-size: 13px; color: #94a3b8; font-weight: 500; }
        .receipt-value { font-size: 14px; color: #0f172a; font-weight: 600; }

        .receipt-total {
            display: flex; justify-content: space-between; align-items: center;
            padding: 16px 0 4px; margin-top: 8px; border-top: 2px solid #f1f5f9;
        }
        .receipt-total .receipt-label { font-size: 14px; font-weight: 700; color: #0f172a; }
        .receipt-total .receipt-value { font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 700; color: #4F46E5; }

        .badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 20px; font-size: 11.5px; font-weight: 600; }
        .badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
        .badge-succeeded { background: #d1fae5; color: #059669; }
        .badge-pending { background: #fef9c3; color: #92400e; }

        .btn-actions { display: flex; gap: 12px; justify-content: center; }
        .btn-primary {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 11px 24px; background: #4F46E5; color: white; border: none;
            border-radius: 10px; font-size: 13.5px; font-weight: 600;
            font-family: 'DM Sans', sans-serif; cursor: pointer; transition: all 0.2s;
            text-decoration: none;
        }
        .btn-primary:hover { background: #4338ca; }
        .btn-secondary {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 11px 24px; background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0;
            border-radius: 10px; font-size: 13.5px; font-weight: 600;
            font-family: 'DM Sans', sans-serif; cursor: pointer; transition: all 0.2s;
            text-decoration: none;
        }
        .btn-secondary:hover { background: #f1f5f9; }

        .pending-note {
            background: #fef9c3; border: 1px solid #fde68a;
            padding: 14px 18px; border-radius: 12px; margin-bottom: 24px;
            font-size: 13px; color: #92400e; text-align: center;
        }
    </style>

    <div class="success-container">
        <div class="success-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
        <div class="success-title">{{ __("Payment Submitted!") }}</div>
        <div class="success-subtitle">
            @if($payment->isSucceeded())
                {{ __("Your payment has been confirmed successfully.") }}
            @else
                {{ __("Your payment is being processed. You will receive confirmation shortly.") }}
            @endif
        </div>

        @if(!$payment->isSucceeded())
            <div class="pending-note">
                {{ __("Payment status:") }}
                <span class="badge badge-{{ $payment->status }}">{{ ucfirst($payment->status) }}</span>
                <br><br>
                {{ __("We are waiting for confirmation from the payment provider. This usually takes a few moments.") }}
            </div>
        @endif

        <div class="receipt-card">
            <div class="receipt-row">
                <span class="receipt-label">{{ __("Academic Year") }}</span>
                <span class="receipt-value">{{ $payment->academicYear?->name }}</span>
            </div>
            <div class="receipt-row">
                <span class="receipt-label">{{ __("Student") }}</span>
                <span class="receipt-value">{{ $payment->student?->name ?? '—' }}</span>
            </div>
            <div class="receipt-row">
                <span class="receipt-label">{{ __("Date") }}</span>
                <span class="receipt-value">{{ $payment->created_at?->format('M j, Y H:i') }}</span>
            </div>
            <div class="receipt-row">
                <span class="receipt-label">{{ __("Status") }}</span>
                <span class="receipt-value"><span class="badge badge-{{ $payment->status }}">{{ ucfirst($payment->status) }}</span></span>
            </div>
            <div class="receipt-total">
                <span class="receipt-label">{{ __("Total") }}</span>
                <span class="receipt-value">{{ strtoupper($payment->currency) }} {{ number_format((float) $payment->amount, 2) }}</span>
            </div>
        </div>

        <div class="btn-actions">
            <a href="{{ route('parent.payments.history') }}" class="btn-primary">{{ __("View Payment History") }}</a>
            <a href="{{ route('parent.payments.index') }}" class="btn-secondary">{{ __("Back to Payments") }}</a>
        </div>
    </div>
</x-layouts.app>
