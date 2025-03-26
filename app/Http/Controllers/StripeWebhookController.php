<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Customer;
use Stripe\PaymentIntent;
use Stripe\StripeClient;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

use App\Libraries\StripeConnect;
use App\Models\StripePaymentTransaction;
use App\Models\StripePaymentTransfer;
use App\Models\ReachMember;
use App\Models\ReachStripeAccount;
use App\Models\Specialist_call_schedule;
use App\Models\MasterSetting;
use App\Models\CurrencyExchangeRates;
use App\Models\StripeWithdrawalTransaction;
use App\Models\ReachTransaction;
use App\Services\CurrencyService;
use App\Services\NotificationService;
use Illuminate\Support\Str;
use App\Models\ReachMeetingParticipantHistory;

class StripeWebhookController extends Controller
{
    protected $notificationService;
    protected $currencyService;
    public function __construct(NotificationService $notificationService, CurrencyService $currencyService)
    {
        $this->notificationService = $notificationService;
        $this->currencyService = $currencyService;
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }
    /*public function __construct()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }*/

    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            // Invalid signature
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        if ($event->type == 'invoice.payment_succeeded') {
            $invoice = $event->data->object;
            $subscription_id = $invoice->subscription;
            $charge_id = $invoice->charge;

            // Find the transaction and update the status
            $transaction = StripePaymentTransaction::where('stripe_subscription_id', $subscription_id)->first();
            if ($transaction) {

                $interval = $invoice->lines->data[0]->price->recurring->interval; // 'month' or 'year'

                if ($invoice->billing_reason == 'subscription_cycle') {

                    $membership_fee = ($invoice->amount_paid / 100);

                    if ($interval == 'year') {

                        if ($transaction->status == 'P') {
                            $transaction->status = 'A';
                            $transaction->stripe_charge_id = $charge_id;
                            $transaction->save();
                        } else {
                            $paymentRecord = [
                                "member_id" => $transaction->member_id,
                                "stripe_subscription_id" => $subscription_id,
                                "stripe_payment_intend_id" => "",
                                "stripe_charge_id" => $charge_id,
                                "amount_paid" => $membership_fee,
                                "payment_date" => date("Y-m-d"),
                                "currency" => $transaction->currency,
                                "charge_description" => 'Yearly Subscription Membership Fee',
                                "last_4" => "",
                                "status" => "A",
                                "payment_type" => "membership",
                            ];

                            $newTransaction = new StripePaymentTransaction($paymentRecord);
                            $newTransaction->save();
                            $lastInsertedId = $newTransaction->id;
                            // Update member's subscription end date
                            $memberData = ReachMember::find($transaction->member_id);
                            $updateData['members_subscription_end_date'] = date('Y-m-d', strtotime("+1 year", strtotime($memberData->members_subscription_end_date)));
                            $memberData->update($updateData);




                            //insert records to reach_transactions
                            $parent_transaction_id = 'TXN-' . strtoupper(Str::random(10));

                            $payment_to = NULL;

                            $transactionRecord = [
                                "transaction_id" => $parent_transaction_id,
                                "payment_id" => $lastInsertedId,
                                "member_id" => $transaction->member_id,
                                "connected_member_id" => $payment_to,
                                "parent_transaction_id" => NULL,
                                "original_amount" =>  $membership_fee,
                                "reduced_amount" => 0,
                                "actual_amount" =>  $membership_fee,
                                "from_currency" => $transaction->currency,
                                "to_currency" => $transaction->currency,
                                "rate" => 1,
                                "payment_date" => date('Y-m-d H:i:s'),
                                "status" => "Completed",
                                "type" => "Membership",
                                "description" => 'New full membership registration renewal',
                                'transaction_type' => 'Debit'
                            ];

                            $reachtransaction = new ReachTransaction($transactionRecord);
                            $reachtransaction->save();
                        }
                    } elseif ($interval == 'month') {

                        if ($transaction->status == 'P') {
                            $transaction->status = 'A';
                            $transaction->stripe_charge_id = $charge_id;
                            $transaction->save();
                        } else {
                            $paymentRecord = [
                                "member_id" => $transaction->member_id,
                                "stripe_subscription_id" => $subscription_id,
                                "stripe_payment_intend_id" => "",
                                "stripe_charge_id" => $charge_id,
                                "amount_paid" => $membership_fee,
                                "payment_date" => date("Y-m-d"),
                                "currency" => $transaction->currency,
                                "charge_description" => 'Monthly Subscription Membership Fee',
                                "last_4" => "",
                                "status" => "A",
                                "payment_type" => "membership",
                            ];

                            $newTransaction = new StripePaymentTransaction($paymentRecord);
                            $newTransaction->save();

                            // Update member's subscription end date
                            /*$memberData = ReachMember::find($transaction->member_id);
                            $updateData['members_subscription_end_date'] = date('Y-m-d', strtotime("+1 month", strtotime($memberData->members_subscription_end_date)));
                            $memberData->update($updateData); */
                            // Update member's subscription end date
                            // Update member's subscription end date
                            $memberData = DB::table('reach_members')
                                ->where('id', $transaction->member_id)
                                ->first();
                            $newEndDate = date('Y-m-d', strtotime("+1 month", strtotime($memberData->members_subscription_end_date)));

                            $updatedRows = DB::table('reach_members')
                                ->where('id', $transaction->member_id)
                                ->update(['members_subscription_end_date' => $newEndDate]);
                            //insert records to reach_transactions
                            $parent_transaction_id = 'TXN-' . strtoupper(Str::random(10));

                            $payment_to = NULL;
                            $lastInsertedId = $newTransaction->id;
                            $transactionRecord = [
                                "transaction_id" => $parent_transaction_id,
                                "payment_id" => $lastInsertedId,
                                "member_id" => $transaction->member_id,
                                "connected_member_id" => $payment_to,
                                "parent_transaction_id" => NULL,
                                "original_amount" =>  $membership_fee,
                                "reduced_amount" => 0,
                                "actual_amount" =>  $membership_fee,
                                "from_currency" => $transaction->currency,
                                "to_currency" => $transaction->currency,
                                "rate" => 1,
                                "payment_date" => date('Y-m-d H:i:s'),
                                "status" => "Completed",
                                "type" => "Membership",
                                "description" => 'New full membership registration renewal',
                                'transaction_type' => 'Debit'
                            ];

                            $reachtransaction = new ReachTransaction($transactionRecord);
                            $reachtransaction->save();
                        }
                    }
                }
            }
        }

        return response()->json(['status' => 'success'], 200);
    }

    public function expertPaymentTransfer(Request $request, $id)
    {
        $requestData = $request->all();

        $schedule = Specialist_call_schedule::where('meeting_id', $id)
            ->where('call_status', 'A')
            ->select('id', 'call_booking_id', 'member_id')
            ->first();
        // print("<PRE>");print_r($schedule);die();

        if ($schedule) {
            $entries = ReachMeetingParticipantHistory::where('meeting_id', $id)
                ->whereNotNull('join_time')
                //->whereNotNull('left_time')
                ->get();
            if ($entries->count() === 2) {

                // $schedule->call_status = 'S';
                $schedule->save();

                $payment = StripePaymentTransaction::where('booking_id', $schedule['id'])
                    ->select('stripe_charge_id', 'payment_to', 'amount_paid', 'member_id', 'payment_to', 'payment_id', 'status')
                    ->first();
                if ($payment && $payment->status === 'W') {
                    return response()->json([
                        'status' => 0,
                        'error' => 'Payment already processed for this booking, status is already Withdraw.'
                    ], 400);
                }

                $charge_id = $payment['stripe_charge_id'];
                $member_id = $payment['payment_to'];

                StripePaymentTransaction::where('booking_id', $schedule['id'])->update(['status' => 'W']);
                $specialist = ReachMember::select('members_fname', 'members_lname', 'stripe_account_id')->find($member_id);
                $connected_account_id = $specialist->stripe_account_id;

                if (!$connected_account_id) {
                    return response()->json(['status' => 0, 'error' => 'Connected account not found.'], 404);
                }

                try {

                    $feeSettings = MasterSetting::select('reach_fee')->find(1);
                    $stripe_fee = 3;
                    $service_fee = (($feeSettings['reach_fee'] + $stripe_fee) / 100) * $payment['amount_paid'];
                    $transfer_amount = $payment['amount_paid'] - $service_fee;

                    $description = "Payment to Experts";
                    $meta_data = ['booking_id' => $schedule['call_booking_id'], 'payment_to' => $specialist->members_fname . " " . $specialist->members_lname];

                    $from_currency = ReachMember::where('id', $payment->member_id)->value('currency');
                    $to_currency = ReachMember::where('id', $payment->payment_to)->value('currency');
                    $rate_details = $this->get_converted_amount($transfer_amount, $from_currency, $to_currency,);
                    // echo $rate_details['converted_amount'];die();
                    $exchange_rate = $rate_details['rate'];
                    $converted_amount = $rate_details['converted_amount'];
                    // print("<PRE>");print_r($rate_details );die();
                    $withdraw_receipt = [
                        'member_id'       => $member_id,
                        'transaction_id'  => $charge_id,
                        'account_id'      => $connected_account_id,
                        'transfer_amount' => $converted_amount,
                        'currency'        => $to_currency,
                        'balance_transaction' => 0,
                        'transfer_date'   => date("Y-m-d"),
                        'status'          => "A",


                    ];

                    $transfer = new StripeWithdrawalTransaction($withdraw_receipt);
                    $transfer->save();


                    $this->stripeconnect = new StripeConnect();
                    $transaction  = $this->stripeconnect->transfer_amount($transfer_amount, $connected_account_id, $charge_id, $description, $from_currency, $meta_data);

                    $transfer_receipt = [];

                    if ($transaction['status'] == 1) {

                        $transfer_receipt['member_id']             = $member_id;
                        $transfer_receipt['booking_id']            = $schedule['id'];
                        $transfer_receipt['stripe_transaction_id'] = $transaction['data']['id'];
                        $transfer_receipt['connected_account_id']  = $connected_account_id;
                        $transfer_receipt['transfer_amount']       = $transfer_amount;
                        $transfer_receipt['transfer_date']         = date("Y-m-d");
                        $transfer_receipt['balance_transaction']   = $transaction['data']['balance_transaction'];
                        $transfer_receipt['status']                = "A";
                        $transfer_receipt['from_currency']         = $from_currency;
                        $transfer_receipt['to_currency']           = $to_currency;
                        $transfer_receipt['converted_amount']      = $converted_amount;
                        $transfer_receipt['exchange_rate']      = $exchange_rate;
                        $transfer_receipt['withdraw_id']       = $transfer->id;
                        // print_r($transfer_receipt);die();
                        $transfer = new StripePaymentTransfer($transfer_receipt);
                        $transfer->save();


                        $transaction_id = 'TXN-' . strtoupper(Str::random(10));
                        $parent_transaction_id =  ReachTransaction::where('payment_id', $payment['payment_id'])->value('transaction_id');
                        // $amount = $this->currencyService->getspecialistFee($requestData);

                        $transactionRecord = [
                            "transaction_id" => $transaction_id,
                            "payment_id" => $payment['payment_id'],
                            "member_id" => $member_id,
                            "connected_member_id" => $schedule['member_id'],
                            "parent_transaction_id" => $parent_transaction_id,
                            "original_amount"  =>  $payment['amount_paid'],
                            "reduced_amount"   => $service_fee,
                            "actual_amount"    => $transfer_amount,
                            "from_currency"    => $from_currency,
                            "to_currency"      => $to_currency,
                            "rate" => $this->currencyService->getCurrencyRate($from_currency, $to_currency),
                            "payment_date" => date('Y-m-d H:i:s'),
                            "status" => "Withdraw",
                            "type" => "Book A Call",
                            "description" => 'Expert Booking Fee-Auto Withdraw',
                            'transaction_type' => 'Debit'
                        ];

                        $reachtransaction = new ReachTransaction($transactionRecord);
                        $reachtransaction->save();
                        //end for reach_transactions

                        return response()->json(['status' => 1, 'success' => 'Payment and transfer completed successfully'], 200);
                    } else {

                        $error_msg = $transaction['data']->getMessage();
                        return response()->json(['status' => 0, 'error' => $error_msg], 500);
                    }
                } catch (\Exception $e) {
                    return response()->json(['error' => $e->getMessage()], 500);
                }
            } else {
                return response()->json(['message' => 'Two-way communication is incomplete'], 400);
            }
        } else {
            return response()->json([
                'status'  => 0,
                'error' => 'The booking with the given ID does not exist.'
            ], 404);
        }
    }
    public function get_converted_amount($amount, $currencyCode, $baseCurrency)
    {

        // Validate input
        if ($amount <= 0) {
            return [
                'error' => true,
                'message' => 'Invalid amount. Amount must be greater than 0.',
            ];
        }

        // Fetch the conversion rate for the given currency to the base currency
        if ($baseCurrency === 'USD') {
            $rateToBaseCurrency = CurrencyExchangeRates::where('currency_code', $currencyCode)
                ->value('exchange_rate_to_usd');
        } elseif ($baseCurrency === 'GBP') {
            $rateToBaseCurrency = CurrencyExchangeRates::where('currency_code', $currencyCode)
                ->value('exchange_rate_to_gbp');
        } elseif ($baseCurrency === 'EUR') {
            $rateToBaseCurrency = CurrencyExchangeRates::where('currency_code', $currencyCode)
                ->value('exchange_rate_to_eur');
        } else {
            return [
                'error' => true,
                'message' => 'Unsupported base currency.',
            ];
        }

        // If no conversion rate is found
        if (!$rateToBaseCurrency) {
            return [
                'error' => true,
                'message' => "Conversion rate for $currencyCode to $baseCurrency not found.",
            ];
        }

        // Convert the amount to the base currency
        $convertedAmount = $amount * $rateToBaseCurrency;

        // Return the conversion details
        return [
            'currency' => $currencyCode,
            'original_amount' => round($amount, 4),
            'rate' => number_format($rateToBaseCurrency, 6),
            'converted_amount' => round($convertedAmount, 2), // Rounded to 2 decimal places
        ];
        //return $convertedAmounts;
    }



    public function getSubscriptionDetails(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            // Invalid signature
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        if ($event->type == 'invoice.payment_succeeded') {
            $invoice = $event->data->object;
            $subscription_id = $invoice->subscription;

            // Find the transaction and update the status
            $transaction = StripePaymentTransaction::where('stripe_subscription_id', $subscription_id)->first();

            if ($transaction) {
                $transaction->status = 'A';
                $transaction->save();
            }
        }

        return response()->json(['status' => 'success'], 200);
    }

    public function documentVerification(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = 'whsec_AObXJy4wtYi5Gah5Qpa09l5ADnYWzqZ6';
        $event = null;

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            // Invalid signature
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Verify the event type
        if ($event->type == 'identity.verification_session.verified') {
            $verificationSession = $event->data->object;
            print_r($verificationSession);
            exit();
            // Extract verified details
            $sessionId = $verificationSession->id;
            $email = $verificationSession->metadata->email;
            $verifiedDetails = $verificationSession->verified_outputs;

            // Example: Update user status in the database
            $member = ReachMember::where('verification_id', $sessionId)->first();
            if ($member) {
                $member->is_doc_verified = 1;
                $member->doc_verified_at = now();
                $member->save();
            }
        } elseif ($event['type'] == 'identity.verification_session.requires_input') {
            // At least one of the verification checks failed
            $verification_session = $event->data->object;

            if ($verification_session->last_error->code == 'document_unverified_other') {
                // The document was invalid
            } elseif ($verification_session->last_error->code == 'document_expired') {
                // The document was expired
            } elseif ($verification_session->last_error->code == 'document_type_not_supported') {
                // The document type was not supported
            } else {
                // ...
            }
        }

        return response()->json(['status' => 'success'], 200);
    }
}
