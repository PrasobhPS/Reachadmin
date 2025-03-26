<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StripePaymentTransaction;
use App\Models\ReachMember;
use App\Models\ReachTransaction;
use Stripe\Stripe;
use Stripe\Subscription;
use Stripe\Invoice;
use Stripe\PaymentMethod;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Stripe\PaymentIntent;


class ProcessStripeSubscriptions extends Command
{
    protected $signature = 'stripe:process-subscriptions';
    protected $description = 'Process Stripe subscription renewals and update member data';

    public function handle()
    {
        try {

            Stripe::setApiKey(env('STRIPE_SECRET'));

            // Get active subscriptions that need renewal
            $transactions = $this->getSubscriptionsForRenewal();
            $processed = 0;
            foreach ($transactions as $transaction) {
                try {
                    $this->processSubscription($transaction);
                    $processed++;
                } catch (\Exception $e) {
                    Log::error("Failed to process subscription {$transaction->id}: " . $e->getMessage());
                    $this->error("Error processing subscription {$transaction->id}: " . $e->getMessage());
                }
            }

            // Mail::raw('Successfully processed {$processed} subscriptions', function ($message) {
            //     $message->to('soumyavinay.techmaven@gmail.com')
            //         ->subject('Subscription Execution Notification');
            // });
            $this->info("Successfully processed {$processed} subscriptions");
            return 0;
        } catch (\Exception $e) {
            Log::error('Stripe subscription processing failed: ' . $e->getMessage());
            $this->error('Stripe subscription processing failed: ' . $e->getMessage());
            return 1;
        }
    }

    private function getSubscriptionsForRenewal()
    {
        $date = Carbon::now()->toDateString();


        $query = StripePaymentTransaction::where('payment_type', 'membership')
            //->where('member_id', '204')
            ->whereNotNull('stripe_subscription_id')
            ->where('subscription_status', 'A')
            ->join('reach_members', 'stripe_payment_transaction.member_id', '=', 'reach_members.id')
            ->where(DB::raw('DATE(reach_members.members_subscription_end_date)'), '<=', $date)
            ->select(
                'stripe_payment_transaction.*',
                'reach_members.members_subscription_end_date',
                'reach_members.id as members_id',
                'reach_members.members_type',
                'reach_members.is_specialist'
            );

        $sql = $query->toSql();
        return $query->get();
    }

    private function processSubscription($transaction)
    {
        // Fetch subscription and latest invoice from Stripe
        $subscription = Subscription::retrieve($transaction->stripe_subscription_id);

        $invoice = Invoice::retrieve($subscription->latest_invoice);

        if (!$invoice->paid || $invoice->status !== 'paid') {
            // Mail::raw('Successfully processed Not paid subscriptions', function ($message) {
            //     $message->to('soumyavinay.techmaven@gmail.com')
            //         ->subject('Subscription Execution Notification Not paid');
            // });
            if (Carbon::parse($transaction->members_subscription_end_date)->lessThanOrEqualTo(Carbon::now()) && $transaction->members_type === 'M' && $transaction->is_specialist === 'N') {
                ReachMember::where('id', $transaction->id)->update(['members_type' => 'F']);
                $transaction->members_type = 'F';
            }
            return;
        }
        // Mail::raw('Successfully processed  paid subscriptions', function ($message) {
        //     $message->to('soumyavinay.techmaven@gmail.com')
        //         ->subject('Subscription Execution Notification paid');
        // });
        $membershipFee = $invoice->amount_paid / 100;
        $interval = $invoice->lines->data[0]->price->recurring->interval;

        if ($invoice->billing_reason !== 'subscription_cycle') {
            Log::info("Skipping non-renewal invoice for subscription {$transaction->stripe_subscription_id}");
            return;
            // Mail::raw('Skipping non-renewal invoice for subscription', function ($message) {
            //     $message->to('soumyavinay.techmaven@gmail.com')
            //         ->subject('Skipping non-renewal invoice for subscription');
            // });
        }

        // Get the payment method details
        $last4 = $this->getPaymentMethodLast4($subscription->default_payment_method);

        // Handle initial pending status
        if ($transaction->status === 'P') {
            $this->updatePendingTransaction($transaction, $invoice->charge);
            return;
        }

        // Create new transaction record
        $newTransaction = $this->createPaymentTransaction($transaction, $subscription, $invoice, $membershipFee, $interval, $last4);

        // Update member's subscription end date
        $this->updateMemberSubscription($transaction->member_id, $interval);

        // Create reach transaction record
        $this->createReachTransaction($newTransaction, $membershipFee, $transaction);

        Log::info("Successfully processed renewal for subscription {$transaction->stripe_subscription_id}");
    }

    private function getPaymentMethodLast4($paymentMethodId)
    {
        try {
            if (empty($paymentMethodId)) {
                Log::info("Payment method ID is empty or null for subscription.");
                return 'N/A'; // Return a placeholder value instead of empty string
            }

            $paymentMethod = PaymentMethod::retrieve($paymentMethodId);

            // Check for card details in expected locations
            if (isset($paymentMethod->card) && isset($paymentMethod->card->last4)) {
                return $paymentMethod->card->last4;
            }

            if (
                isset($paymentMethod->billing_details) &&
                isset($paymentMethod->billing_details->card) &&
                isset($paymentMethod->billing_details->card->last4)
            ) {
                return $paymentMethod->billing_details->card->last4;
            }

            // Try to check if it's a different payment type with a last4 property
            if (isset($paymentMethod->type)) {
                $type = $paymentMethod->type;
                if (isset($paymentMethod->$type) && isset($paymentMethod->$type->last4)) {
                    return $paymentMethod->$type->last4;
                }
            }

            Log::warning("Card details not found for payment method: {$paymentMethodId}");
            return 'N/A';
        } catch (\Exception $e) {
            Log::error("Error retrieving payment method {$paymentMethodId}: " . $e->getMessage());
            return 'N/A';
        }
    }


    private function updatePendingTransaction($transaction, $chargeId)
    {
        $transaction->status = 'A';
        $transaction->stripe_charge_id = $chargeId;
        $transaction->save();
    }

    private function createPaymentTransaction($transaction, $subscription, $invoice, $membershipFee, $interval, $last4)
    {


        if (empty($last4) || $last4 == 'N/A') {
            $lastTransaction = StripePaymentTransaction::where('member_id', $transaction->member_id)
                ->whereNotNull('last_4') // Ensure last_4 is not null
                ->orderBy('payment_date', 'desc') // Get the latest transaction
                ->first();

            $last4 = $lastTransaction ? $lastTransaction->last_4 : 'N/A';
        }


        $paymentRecord = [
            "member_id" => $transaction->member_id,
            "stripe_subscription_id" => $subscription->id,
            "stripe_charge_id" => $invoice->charge,
            "amount_paid" => $membershipFee,
            "payment_date" => Carbon::now()->format('Y-m-d'),
            "currency" => $transaction->currency,
            "charge_description" => ucfirst($interval) . 'ly Subscription Membership Fee',
            "last_4" =>  $last4, // Add the last4 field
            "status" => "A",
            "payment_type" => "membership",
            "stripe_payment_intend_id" => $invoice->payment_intent ?? '', // Add payment intent if available
        ];

        return StripePaymentTransaction::create($paymentRecord);
    }

    private function updateMemberSubscription($memberId, $interval)
    {
        $member = ReachMember::find($memberId);
        $currentEndDate = Carbon::parse($member->members_subscription_end_date);

        $newEndDate = $interval === 'year'
            ? $currentEndDate->addYear()
            : $currentEndDate->addMonth();

        $member->members_subscription_end_date = $newEndDate->format('Y-m-d');
        $member->save();
    }

    private function createReachTransaction($transaction, $membershipFee, $originalTransaction)
    {
        $transactionRecord = [
            "transaction_id" => 'TXN-' . strtoupper(Str::random(10)),
            "payment_id" => $transaction->id,
            "member_id" => $transaction->member_id,
            "connected_member_id" => null,
            "parent_transaction_id" => null,
            "original_amount" => $membershipFee,
            "reduced_amount" => 0,
            "actual_amount" => $membershipFee,
            "from_currency" => $originalTransaction->currency,
            "to_currency" => $originalTransaction->currency,
            "rate" => 1,
            "payment_date" => Carbon::now(),
            "status" => "Completed",
            "type" => "Membership",
            "description" => 'Membership subscription renewal',
            'transaction_type' => 'Debit'
        ];

        return ReachTransaction::create($transactionRecord);
    }
    private function getPaymentMethodLast4new($paymentMethodId)
    {
        try {
            if (empty($paymentMethodId)) {
                Log::info("Payment method ID is empty or null for subscription");
                return '';
            }

            try {
                // First, try to get the payment method directly
                $paymentMethod = PaymentMethod::retrieve($paymentMethodId);

                // Check card details in the standard location
                if (isset($paymentMethod->card) && isset($paymentMethod->card->last4)) {
                    return $paymentMethod->card->last4;
                }

                // Check for other payment types that might have last4
                if (isset($paymentMethod->type)) {
                    $type = $paymentMethod->type;
                    if (isset($paymentMethod->$type) && isset($paymentMethod->$type->last4)) {
                        return $paymentMethod->$type->last4;
                    }
                }
            } catch (\Exception $pmError) {
                Log::warning("Could not retrieve payment method directly: " . $pmError->getMessage());

                // If retrieving the payment method fails, try to get it from the customer
                try {
                    // First, try to get the subscription to find the customer
                    $subscription = Subscription::retrieve($transaction->stripe_subscription_id);
                    if (!empty($subscription->customer)) {
                        $customer = \Stripe\Customer::retrieve($subscription->customer);
                        if (!empty($customer->invoice_settings->default_payment_method)) {
                            $defaultPaymentMethod = PaymentMethod::retrieve($customer->invoice_settings->default_payment_method);
                            if (isset($defaultPaymentMethod->card) && isset($defaultPaymentMethod->card->last4)) {
                                return $defaultPaymentMethod->card->last4;
                            }
                        }

                        // Try getting from the most recent charge
                        if (!empty($subscription->latest_invoice)) {
                            $invoice = Invoice::retrieve($subscription->latest_invoice);
                            if (!empty($invoice->charge)) {
                                $charge = \Stripe\Charge::retrieve($invoice->charge);
                                if (
                                    isset($charge->payment_method_details) &&
                                    isset($charge->payment_method_details->card) &&
                                    isset($charge->payment_method_details->card->last4)
                                ) {
                                    return $charge->payment_method_details->card->last4;
                                }
                            }
                        }
                    }
                } catch (\Exception $customerError) {
                    Log::warning("Could not retrieve last4 from customer or charges: " . $customerError->getMessage());
                }
            }

            Log::error("Could not find last4 for payment method: {$paymentMethodId}");
            return '';
        } catch (\Exception $e) {
            Log::error("Fatal error retrieving payment method: " . $e->getMessage());
            return '';
        }
    }
}
