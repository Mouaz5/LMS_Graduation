<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Payment;
use App\Models\TuitionFee;
use App\Services\Payment\StripeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PaymentWebController extends Controller
{
    public function __construct(
        private StripeService $stripeService
    ) {}

    public function index(): View
    {
        $parent = Auth::user();
        $children = $parent->children()->with('studentProfile.classroom.grade')->get();

        $academicYears = AcademicYear::with(['tuitionFee' => function ($q) {
            $q->where('is_active', true);
        }])->orderBy('start_date', 'desc')->get();

        $existingPayments = Payment::where('parent_user_id', $parent->id)
            ->whereIn('status', ['pending', 'succeeded'])
            ->get()
            ->keyBy(fn ($p) => $p->academic_year_id . '-' . $p->student_user_id);

        $currency = config('services.stripe.currency', 'usd');

        return view('parent.payments.index', compact(
            'parent', 'children', 'academicYears', 'existingPayments', 'currency'
        ));
    }

    public function checkout(Request $request, TuitionFee $tuitionFee): RedirectResponse
    {
        $parent = Auth::user();

        if (!$tuitionFee->is_active) {
            return redirect()->route('parent.payments.index')
                ->with('error', __('This tuition fee is no longer available.'));
        }

        $studentId = $request->query('student');
        $child = $studentId
            ? $parent->children()->where('student_user_id', $studentId)->first()
            : $parent->children()->first();

        if (!$child) {
            return redirect()->route('parent.payments.index')
                ->with('error', __('Please select a valid student to pay for.'));
        }

        $existingPending = Payment::where('parent_user_id', $parent->id)
            ->where('academic_year_id', $tuitionFee->academic_year_id)
            ->where('student_user_id', $child->id)
            ->where('status', 'pending')
            ->first();

        if ($existingPending) {
            return redirect($existingPending->stripe_checkout_session_id);
        }

        $existingSucceeded = Payment::where('parent_user_id', $parent->id)
            ->where('academic_year_id', $tuitionFee->academic_year_id)
            ->where('student_user_id', $child->id)
            ->where('status', 'succeeded')
            ->exists();

        if ($existingSucceeded) {
            return redirect()->route('parent.payments.history')
                ->with('error', __('You have already paid for :name for this academic year.', ['name' => $child->name]));
        }

        $academicYear = $tuitionFee->academicYear;

        try {
            $session = $this->stripeService->createCheckoutSession([
                'currency' => $tuitionFee->currency,
                'amount' => $tuitionFee->amount,
                'product_name' => __('Tuition Fee - :year (:student)', ['year' => $academicYear->name, 'student' => $child->name]),
                'product_description' => __('School tuition fee for :student for academic year :year', ['year' => $academicYear->name, 'student' => $child->name]),
                'success_url' => route('parent.payments.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('parent.payments.index'),
                'metadata' => [
                    'parent_user_id' => $parent->id,
                    'student_user_id' => $child?->id,
                    'academic_year_id' => $academicYear->id,
                    'tuition_fee_id' => $tuitionFee->id,
                ],
                'client_reference_id' => $parent->id,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('parent.payments.index')
                ->with('error', __('Unable to initiate payment. Please try again later.'));
        }

        $payment = Payment::create([
            'parent_user_id' => $parent->id,
            'student_user_id' => $child?->id,
            'academic_year_id' => $academicYear->id,
            'tuition_fee_id' => $tuitionFee->id,
            'amount' => $tuitionFee->amount,
            'currency' => $tuitionFee->currency,
            'status' => 'pending',
            'stripe_checkout_session_id' => $session->id,
        ]);

        return redirect($session->url);
    }

    public function testProcess(Request $request, TuitionFee $tuitionFee): RedirectResponse
    {
        $parent = Auth::user();

        $validated = $request->validate([
            'student_user_id' => 'required|integer',
        ]);

        $child = $parent->children()->where('student_user_id', $validated['student_user_id'])->first();

        if (!$child) {
            return redirect()->route('parent.payments.index')
                ->with('error', __('Invalid student selection.'));
        }

        $existingSucceeded = Payment::where('parent_user_id', $parent->id)
            ->where('academic_year_id', $tuitionFee->academic_year_id)
            ->where('student_user_id', $child->id)
            ->where('status', 'succeeded')
            ->exists();

        if ($existingSucceeded) {
            return redirect()->route('parent.payments.history')
                ->with('error', __('You have already paid for :name for this academic year.', ['name' => $child->name]));
        }

        $testSessionId = 'test_' . uniqid();

        $payment = Payment::create([
            'parent_user_id' => $parent->id,
            'student_user_id' => $child->id,
            'academic_year_id' => $tuitionFee->academic_year_id,
            'tuition_fee_id' => $tuitionFee->id,
            'amount' => $tuitionFee->amount,
            'currency' => $tuitionFee->currency,
            'status' => 'succeeded',
            'stripe_checkout_session_id' => $testSessionId,
            'stripe_payment_intent_id' => 'pi_test_' . uniqid(),
            'paid_at' => now(),
        ]);

        return redirect()->route('parent.payments.success', ['session_id' => $testSessionId]);
    }

    public function success(Request $request): View|RedirectResponse
    {
        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            return redirect()->route('parent.payments.index');
        }

        $payment = Payment::where('stripe_checkout_session_id', $sessionId)
            ->where('parent_user_id', Auth::id())
            ->first();

        if (!$payment) {
            return redirect()->route('parent.payments.index')
                ->with('error', __('Payment session not found.'));
        }

        $payment->load(['academicYear', 'tuitionFee']);

        return view('parent.payments.success', compact('payment'));
    }

    public function history(): View
    {
        $parent = Auth::user();
        $payments = Payment::where('parent_user_id', $parent->id)
            ->with(['academicYear', 'student'])
            ->orderByDesc('created_at')
            ->paginate(20);

        $currency = config('services.stripe.currency', 'usd');

        return view('parent.payments.history', compact('parent', 'payments', 'currency'));
    }
}
