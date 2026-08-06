<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\Payment\StripeService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Event;

class StripeWebhookController extends Controller
{
    public function __construct(
        private StripeService $stripeService
    ) {}

    public function handle(Request $request): Response
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        if (!$signature) {
            return response('Missing signature', 400);
        }

        try {
            $event = $this->stripeService->constructWebhookEvent($payload, $signature);
        } catch (\UnexpectedValueException $e) {
            Log::warning('Stripe webhook: invalid payload - ' . $e->getMessage());
            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::warning('Stripe webhook: signature verification failed - ' . $e->getMessage());
            return response('Invalid signature', 400);
        }

        try {
            switch ($event->type) {
                case 'checkout.session.completed':
                    $this->handleCheckoutCompleted($event);
                    break;

                case 'checkout.session.expired':
                    $this->handleCheckoutExpired($event);
                    break;

                case 'payment_intent.payment_failed':
                    $this->handlePaymentFailed($event);
                    break;

                case 'charge.refunded':
                    $this->handleChargeRefunded($event);
                    break;

                default:
                    Log::info('Stripe webhook: unhandled event type - ' . $event->type);
            }
        } catch (\Exception $e) {
            Log::error('Stripe webhook processing error: ' . $e->getMessage());
            return response('Webhook processing error', 500);
        }

        return response('Webhook handled', 200);
    }

    private function handleCheckoutCompleted(Event $event): void
    {
        $session = $event->data->object;

        if ($session->payment_status !== 'paid') {
            return;
        }

        $payment = Payment::where('stripe_checkout_session_id', $session->id)->first();

        if (!$payment) {
            Log::warning('Stripe webhook: payment not found for session ' . $session->id);
            return;
        }

        if ($payment->status === 'succeeded') {
            return;
        }

        $payment->update([
            'status' => 'succeeded',
            'stripe_payment_intent_id' => $session->payment_intent,
            'paid_at' => now(),
        ]);
    }

    private function handleCheckoutExpired(Event $event): void
    {
        $session = $event->data->object;

        $payment = Payment::where('stripe_checkout_session_id', $session->id)->first();

        if (!$payment || !$payment->isPending()) {
            return;
        }

        $payment->update([
            'status' => 'failed',
            'failure_reason' => 'Checkout session expired',
        ]);
    }

    private function handlePaymentFailed(Event $event): void
    {
        $intent = $event->data->object;

        $payment = Payment::where('stripe_payment_intent_id', $intent->id)->first();

        if (!$payment) {
            return;
        }

        if ($payment->status === 'succeeded') {
            return;
        }

        $lastError = $intent->last_payment_error?->message ?? 'Payment failed';

        $payment->update([
            'status' => 'failed',
            'failure_reason' => $lastError,
        ]);
    }

    private function handleChargeRefunded(Event $event): void
    {
        $charge = $event->data->object;

        $payment = Payment::where('stripe_payment_intent_id', $charge->payment_intent)->first();

        if (!$payment) {
            return;
        }

        $payment->update([
            'status' => 'refunded',
        ]);
    }
}
