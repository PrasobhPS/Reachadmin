<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Customer;
use Stripe\PaymentIntent;
use Stripe\StripeClient;

use App\Libraries\StripeConnect;
use App\Models\StripePaymentTransaction;
use App\Models\StripePaymentTransfer;
use App\Models\ReachMember;
use App\Models\ReachStripeAccount;
use App\Models\Specialist_call_schedule;
use App\Models\MasterSetting;

class PaymentController extends Controller
{
	public function __construct()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }

	public function showPaymentForm()
    {
        /*$stripe = new StripeClient(env('STRIPE_SECRET'));
        $paymentIntent = $stripe->paymentIntents->create([
            'amount' => 1000,
            'currency' => 'gbp',
            'automatic_payment_methods' => ['enabled' => true],
        ]);

        return view('payment.payment-form', [
            'clientSecret' => $paymentIntent->client_secret,
        ]);*/

        return view('payment.payment-form');
    }

    public function processPayment(Request $request)
    {
        try {

            // Check if customer with the same email exists
            $existingCustomer = Customer::all(['email' => $request->email]);

            if ($existingCustomer->count() > 0) {
                $customer = $existingCustomer->data[0];
            } else {

                // Create a new customer
                $customer = Customer::create([
                    'email' => $request->email,
                    'name' => $request->name,
                    'source' => $request->stripeToken,
                ]);
            }

            // Charge the Customer instead of the card:
            $charge = Charge::create([
                'amount' => 10000,
                'currency' => 'GBP',
                'customer' => $customer->id,
                'receipt_email' => $request->email,
                'description' => 'Booking Fee',
                // 'metadata' => [
                //     'customer_name' => $request->name,
                //     'customer_email' => $request->email,
                // ],
            ]);

            $charge_id = $charge->id;
            $payment_intent_id = $charge->id;
            $last_4 = $charge->payment_method_details->card->last4 ?? '';

            $paymentRecord = [
                "member_id"                 => 25,
                "payment_to"                => 9,
                "booking_id"                => 19,
                "stripe_payment_intend_id"  => $payment_intent_id,
                "stripe_charge_id"          => $charge_id,
                "amount_paid"               => $charge->amount,
                "payment_date"              => date("Y-m-d"),
                "currency"                  => "GBP",
                "charge_description"        => $charge->description,
                "last_4"                    => $last_4,
                "balance_transaction"       => $charge->balance_transaction,
                "status"                    => "A",
            ];
            $transaction = new StripePaymentTransaction($paymentRecord);
            $transaction->save();

            // Update the charge metadata with the actual booking_id
            $charge = Charge::retrieve($charge_id);
            $charge->metadata['booking_id'] = "Booking 0001";
            $charge->save();

            return redirect()->route('payment.success');
        } catch (\Exception $ex) {

            return redirect()->route('book.now')->with('error', $ex->getMessage());
        }
    }

    public function paymentSuccess()
    {
        return view('payment/payment-success');
    }

    public function doPaymentTransfer(Request $request)
    {
        $requestData = $request->all();

        /*$validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|string',
            'description' => 'required|string',
            'payment_method_id' => 'required|string',
            'customer_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }*/

        $transfer_amount = $request->amount;
        $currency = 'GBP';
        $description = $request->description;

        $payment = StripePaymentTransaction::where('payment_id', $request->payment_id)
                    ->select('stripe_charge_id', 'payment_to')
                    ->first();

        $charge_id = $payment['stripe_charge_id'];
        $member_id = $payment['payment_to'];

        $connected_account_id = ReachStripeAccount::where('member_id', $member_id)->pluck('stripe_user_id')->first();
        if (!$connected_account_id) {
            return response()->json(['error' => 'Connected account not found.'], 404);
        }

        try {

            $this->stripeconnect = new StripeConnect();

            $transfer  = $this->stripeconnect->transfer_amount($transfer_amount, $connected_account_id, $charge_id, $description);

            print_r($transfer);
            exit();

            $transfer_receipt = [];

            if ($transfer['status'] == 1) {

                $transfer_receipt['paid_to_client_id']     = "";
                $transfer_receipt['type']                  = "specialist_fee";
                $transfer_receipt['status']                = "completed";
                $transfer_receipt['date']                  = date("Y-m-d");
                $transfer_receipt['stripe_transaction_id'] = $transfer['data']['id'];
                $transfer_receipt['approved_by']           = 0;

                $transfer = new StripePaymentTransaction($transfer_receipt);
                $transfer->save();

                return response()->json(['status' => 1, 'success' => 'Payment and transfer completed successfully'], 200);
            } else {

                $error_msg = $transfer['data']->getMessage();
                return response()->json(['status' => 0, 'error' => $error_msg], 500);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function doPaymentCardDetails(Request $request)
    {
        $requestData = $request->all();

        try {

            $this->stripeconnect = new StripeConnect();

            $payment_intent_array = [
                'amount'      => 1000,
                'currency'    => "gbp",
                'description' => "Test Specialist Booking",
                'payment_method_data' => [
                    'type' => 'card',
                    'card' => [
                        'token' => $request->stripeToken,
                    ],
                ],
                'confirm' => true,
                'metadata' => [
                    'payment_to' => 'Specialist Name',
                    'payment_from' => '9',
                    'member_id' => '9',
                ],
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never',
                ],
            ];
            $payment_intent = $this->stripeconnect->createPaymentIntend($payment_intent_array);

            if ($payment_intent['status'] == 1) {
                $intend_id = $payment_intent['data']['id'];

                /*Payment::create([
                    'payment_intent_id' => $paymentIntent->id,
                    'amount' => $paymentIntent->amount,
                    'currency' => $paymentIntent->currency,
                    'description' => $paymentIntent->description,
                    'status' => $paymentIntent->status,
                ]);*/

                return response()->json(['status' => 1, 'success' => 'Payment completed successfully'], 200);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}