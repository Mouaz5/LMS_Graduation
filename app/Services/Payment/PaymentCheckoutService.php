<?php

namespace App\Services\Payment;

use App\Domain\PaymentCheckoutResult;
use App\Enums\PaymentCheckoutStatus;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\TuitionFee;
use App\Models\User;
use App\Services\Parent\ParentAccessService;
use Illuminate\Support\Str;
use Throwable;

class PaymentCheckoutService
{
    public function __construct(
        private ParentAccessService $access,
        private StripeService $stripe,
    ) {}

    public function start(
        User $parent,
        TuitionFee $tuitionFee,
        mixed $studentId,
        string $successUrl,
        string $cancelUrl,
    ): PaymentCheckoutResult {
        if (! $tuitionFee->is_active) {
            return new PaymentCheckoutResult(PaymentCheckoutStatus::INACTIVE_FEE);
        }

        $child = $this->access->findChild($parent, $studentId);
        if (! $child) {
            return new PaymentCheckoutResult(PaymentCheckoutStatus::INVALID_CHILD);
        }

        $existingPending = Payment::where('parent_user_id', $parent->id)
            ->where('academic_year_id', $tuitionFee->academic_year_id)
            ->where('student_user_id', $child->id)
            ->where('status', PaymentStatus::PENDING->value)
            ->first();

        if ($existingPending) {
            try {
                $session = $this->stripe->retrieveCheckoutSession(
                    $existingPending->stripe_checkout_session_id
                );
            } catch (Throwable $exception) {
                throw new PaymentGatewayException(
                    'Unable to retrieve the existing checkout session.',
                    previous: $exception,
                );
            }

            return new PaymentCheckoutResult(
                PaymentCheckoutStatus::PENDING,
                url: $session->url,
                sessionId: $existingPending->stripe_checkout_session_id,
                childName: $child->name,
            );
        }

        $existingSucceeded = Payment::where('parent_user_id', $parent->id)
            ->where('academic_year_id', $tuitionFee->academic_year_id)
            ->where('student_user_id', $child->id)
            ->where('status', PaymentStatus::SUCCEEDED->value)
            ->exists();

        if ($existingSucceeded) {
            return new PaymentCheckoutResult(
                PaymentCheckoutStatus::ALREADY_PAID,
                childName: $child->name,
            );
        }

        $academicYear = $tuitionFee->academicYear;

        try {
            $session = $this->stripe->createCheckoutSession([
                'currency' => $tuitionFee->currency,
                'amount' => $tuitionFee->amount,
                'product_name' => __('Tuition Fee - :year (:student)', [
                    'year' => $academicYear->name,
                    'student' => $child->name,
                ]),
                'product_description' => __('School tuition fee for :student for academic year :year', [
                    'year' => $academicYear->name,
                    'student' => $child->name,
                ]),
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
                'metadata' => [
                    'parent_user_id' => $parent->id,
                    'student_user_id' => $child->id,
                    'academic_year_id' => $academicYear->id,
                    'tuition_fee_id' => $tuitionFee->id,
                ],
                'client_reference_id' => $parent->id,
            ]);
        } catch (Throwable $exception) {
            throw new PaymentGatewayException(
                'Unable to create the checkout session.',
                previous: $exception,
            );
        }

        Payment::create([
            'parent_user_id' => $parent->id,
            'student_user_id' => $child->id,
            'academic_year_id' => $academicYear->id,
            'tuition_fee_id' => $tuitionFee->id,
            'amount' => $tuitionFee->amount,
            'currency' => $tuitionFee->currency,
            'status' => PaymentStatus::PENDING,
            'stripe_checkout_session_id' => $session->id,
        ]);

        return new PaymentCheckoutResult(
            PaymentCheckoutStatus::CREATED,
            url: $session->url,
            sessionId: $session->id,
            childName: $child->name,
        );
    }

    public function createTestPayment(
        User $parent,
        TuitionFee $tuitionFee,
        int $studentId,
    ): PaymentCheckoutResult {
        if (! $tuitionFee->is_active) {
            return new PaymentCheckoutResult(PaymentCheckoutStatus::INACTIVE_FEE);
        }

        $child = $this->access->findChild($parent, $studentId);
        if (! $child) {
            return new PaymentCheckoutResult(PaymentCheckoutStatus::INVALID_CHILD);
        }

        $existingSucceeded = Payment::where('parent_user_id', $parent->id)
            ->where('academic_year_id', $tuitionFee->academic_year_id)
            ->where('student_user_id', $child->id)
            ->where('status', PaymentStatus::SUCCEEDED->value)
            ->exists();

        if ($existingSucceeded) {
            return new PaymentCheckoutResult(
                PaymentCheckoutStatus::ALREADY_PAID,
                childName: $child->name,
            );
        }

        $sessionId = 'test_'.Str::uuid();
        $paymentIntentId = 'pi_test_'.Str::uuid();

        Payment::create([
            'parent_user_id' => $parent->id,
            'student_user_id' => $child->id,
            'academic_year_id' => $tuitionFee->academic_year_id,
            'tuition_fee_id' => $tuitionFee->id,
            'amount' => $tuitionFee->amount,
            'currency' => $tuitionFee->currency,
            'status' => PaymentStatus::SUCCEEDED,
            'stripe_checkout_session_id' => $sessionId,
            'stripe_payment_intent_id' => $paymentIntentId,
            'paid_at' => now(),
        ]);

        return new PaymentCheckoutResult(
            PaymentCheckoutStatus::CREATED,
            sessionId: $sessionId,
            childName: $child->name,
        );
    }
}
