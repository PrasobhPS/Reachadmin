<?php

namespace App\Libraries;

use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Customer;
use Stripe\Refund;
use Stripe\Transfer;
use Stripe\StripeClient;
use Stripe\Product;
use Stripe\Price;
use Stripe\Subscription;
use Stripe\Coupon;
use Stripe\Account;
use Stripe\AccountLink;
use Stripe\OAuth;
use Stripe\Identity\VerificationSession;

class StripeConnect
{
    protected $publishable_key;
    protected $secret_key;
    protected $connect_client_id;
    protected $authorize_uri = 'https://connect.stripe.com/oauth/authorize';
    protected $redirect_to_backend;
    protected $redirect_to_web;

    public function __construct()
    {

        $this->publishable_key = env('STRIPE_KEY');
        $this->secret_key = env('STRIPE_SECRET');
        $this->connect_client_id = env('STRIPE_CLIENT_ID');
        $this->redirect_to_backend = url('settings/connect-stripe');
        $this->redirect_to_web = "https://reach.boats/connect-stripe";

        Stripe::setApiKey($this->secret_key);
    }

    public function createConnectedAccount($arr_data)
    {
        try {
            $stripe = new StripeClient($this->secret_key);

            $account = $stripe->accounts->create([
                'country' => 'GB',
                'email' => $arr_data['email_id'],
                'business_type' => 'individual',
                'capabilities' => [
                    'card_payments' => ['requested' => true],
                    'transfers' => ['requested' => true],
                ],
                'type' => 'express',
                'individual' => [
                    'first_name' => $arr_data['first_name'],
                    'last_name' => $arr_data['last_name'],
                    'phone' => $arr_data['phone'],
                    'dob' => [
                        'day' => $arr_data['dob_day'],
                        'month' => $arr_data['dob_month'],
                        'year' => $arr_data['dob_year'],
                    ],
                    'address' => [
                        'line1' => $arr_data['address_line1'],
                        'city' => $arr_data['city'],
                        'postal_code' => $arr_data['postal_code'],
                        //'country' => 'GB',
                    ],
                ],
                'business_profile' => [
                    'name' => $arr_data['business_name'],
                    'url' => $arr_data['business_url'],
                ],
            ]);

            $accountLink = $stripe->accountLinks->create([
                'account' => $account->id,
                'refresh_url' => $this->redirect_to_backend,
                'return_url' => $this->redirect_to_backend,
                'type' => 'account_onboarding',
            ]);

            return ['status' => 1, 'data' => $account, 'account_link' => $accountLink->url];
        } catch (\Exception $e) {
            return ['status' => 0, 'error' => $e->getMessage()];
        }
    }

    public function create_login_link($connected_account_id)
    {
        try {
            $stripe = new StripeClient($this->secret_key);

            $loginLink = $stripe->accounts->createLoginLink($connected_account_id);

            return ['status' => 1, 'login_link' => $loginLink->url];
        } catch (\Exception $e) {
            return ['status' => 0, 'error' => $e->getMessage()];
        }
    }

    public function getConnectUrl($specialist_id)
    {
        $authorize_request_body = [
            'response_type' => 'code',
            'scope' => 'read_write',
            'client_id' => $this->connect_client_id,
            'suggested_capabilities' => ['card_payments', 'transfers'],
            'state' => $specialist_id,
        ];

        $url = $this->authorize_uri . '?' . http_build_query($authorize_request_body);
        $url .= '&redirect_uri=' . $this->redirect_to_backend;

        return $url;
    }

    public function generate_login_link($connected_account_id, $redirect_url)
    {
        try {
            $url = Account::createLoginLink(
                $connected_account_id,
                ['redirect_url' => $redirect_url]
            );
            return ['status' => 1, 'data' => $url];
        } catch (\Exception $e) {
            return ['status' => 0, 'data' => $e, 'msg' => $e->getMessage()];
        }
    }

    // Create a connected account for the user
    public function create_account($user_data)
    {
        try {
            $individualData = [
                'first_name' => $user_data['first_name'],
                'last_name' => $user_data['last_name'],
                // 'phone' => $user_data['phone'],
                'address' => [
                    'line1' => $user_data['address_line1'] ?? '',
                    'city' => $user_data['city'] ?? '',
                    // 'postal_code' => $user_data['postal_code'] ?? '',
                ],
            ];

            // Add DOB only if all values are set and numeric
            if (
                !empty($user_data['dob_day']) &&
                !empty($user_data['dob_month']) &&
                !empty($user_data['dob_year']) &&
                is_numeric($user_data['dob_day']) &&
                is_numeric($user_data['dob_month']) &&
                is_numeric($user_data['dob_year'])
            ) {

                $individualData['dob'] = [
                    'day' => (int) $user_data['dob_day'],
                    'month' => (int) $user_data['dob_month'],
                    'year' => (int) $user_data['dob_year'],
                ];
            }
            $account = Account::create([
                'type' => 'express',
                'country' => $user_data['country'],
                'email' => $user_data['email_id'],
                'business_type' => 'individual',
                'capabilities' => [
                    'transfers' => ['requested' => true],
                    'card_payments' => ['requested' => true],
                ],
                'individual' => $individualData,
            ]);

            $accountLink = AccountLink::create([
                'account' => $account->id,
                'refresh_url' => $this->redirect_to_web,
                'return_url' => $this->redirect_to_web,
                'type' => 'account_onboarding',
            ]);

            return ['status' => 1, 'data' => $account, 'account_link' => $accountLink->url];
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            // Return Stripe-specific error message
            return ['status' => 0, 'msg' => $e->getMessage()];
        } catch (\Exception $e) {
            // Return general error message for other exceptions
            return ['status' => 0, 'msg' => 'An unexpected error occurred: ' . $e->getMessage()];
        }
    }

    public function deleteOldStripeAccount($accountId)
    {
        try {
            $stripe = new StripeClient($this->secret_key);

            // Delete the account
            $account = $stripe->accounts->delete($accountId);

            return ['status' => 1, 'message' => 'Account deleted successfully.'];
        } catch (\Exception $e) {
            return ['status' => 0, 'msg' => $e->getMessage()];
        }
    }

    public function checkAccountVerification($stripeAccountId)
    {
        try {
            // Retrieve the connected account
            $account = Account::retrieve($stripeAccountId);

            // Check if there are any verification requirements outstanding
            if (empty($account->requirements->currently_due)) {
                return ['status' => 1, 'message' => 'The Stripe account has been fully verified.'];
            } else {
                return ['status' => 0, 'data' => $account->requirements->currently_due];
            }
        } catch (Exception $e) {
            return ['status' => 0, 'data' => $e, 'msg' => $e->getMessage()];
        }
    }

    public function getAccessToken($code)
    {
        try {
            $response = OAuth::token([
                'grant_type' => 'authorization_code',
                'code'       => $code,
            ]);
            return ['status' => 1, 'data' => $response];
        } catch (Exception $e) {
            return ['status' => 0, 'msg' => $e->getMessage()];
        }
    }

    public function deauthorize($stripe_user_id)
    {
        try {
            $response = OAuth::deauthorize([
                'client_id'      => $this->connect_client_id,
                'stripe_user_id' => $stripe_user_id
            ]);

            return ['status' => 1, 'data' => $response];
        } catch (Exception $e) {
            return ['status' => 0, 'data' => $e, 'msg' => $e->getMessage()];
        }
    }

    private function _result2Array($param)
    {
        $return = json_decode(json_encode($param), true);
        return $return;
    }

    public function transfer_amount($amount, $connected_account_id, $charge_id, $description, $currency, $meta_data = [])
    {
        if ($amount > 0) {
            try {

                // Create a Transfer to a connected account (later):
                $transfer_array = [
                    'amount'             => round($amount * 100),
                    'currency'           => $currency,
                    'destination'        => $connected_account_id,
                    "source_transaction" => $charge_id,
                    'description'        => $description,
                    'metadata'           => $meta_data
                ];
                //print("<PRE>");print_r($transfer_array);die();
                $transfer                  = Transfer::create($transfer_array);

                $transfer                  = $this->_result2Array($transfer);

                $update_arr['description'] = $description;
                $updated_ch                = $this->update_charge_connected($connected_account_id, $transfer['destination_payment'], $update_arr);

                return ['status' => 1, 'data' => $transfer];
            } catch (Exception $e) {

                return ['status' => 0, 'data' => $e, 'msg' => $e->getMessage()];
            }
        } else {
            return ['status' => 0, 'data' => 'Amount must be greater than zero.passed ' . $amount];
        }
    }

    public function update_charge_connected($connected_account_id, $charge_id, $update_arr)
    {

        try {

            $invoice = Charge::update($charge_id, $update_arr, ['stripe_account' => $connected_account_id]);
            $result  = $this->_result2Array($invoice);

            return ['status' => 1, 'data' => $result];
        } catch (Exception $e) {
            return ['status' => 0, 'data' => $e, 'msg' => $e->getMessage()];
        }

        return false;
    }

    public function withdraw_amount($amount, $currency, $connectedAccountId)
    {
        try {
            // Convert amount to cents (if needed based on currency)
            $amountInCents = intval($amount * 100);

            // Create transfer to connected account
            $transfer = Transfer::create([
                'amount' => $amountInCents,
                'currency' => $currency,
                'destination' => $connectedAccountId,
            ]);

            return [
                'status' => 1,
                'data'   => $transfer,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 0,
                'data'   => $e,
            ];
        }
    }

    public function addFundsToConnectedAccount($amount, $currency, $connectedAccountId, $meta_data = [])
    {
        try {

            $stripe = new StripeClient($this->secret_key);
            $amountInCents = intval($amount * 100);

            // Create a charge to add funds to the connected account
            $charge = $stripe->charges->create([
                'amount' => $amountInCents,
                'currency' => $currency,
                'description' => 'Withdraw Amount',
                'source' => 'tok_bypassPendingInternational',
                'transfer_data' => [
                    'destination' => $connectedAccountId,
                ],
                'metadata' => $meta_data,
            ]);

            return [
                'status' => 1,
                'message' => 'Funds added successfully to the connected account.',
                'data' => $charge,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 0,
                'message' => 'Failed to add funds to the connected account.',
                'error' => $e->getMessage(),
            ];
        }
    }








    // Create a new Stripe customer.
    public function create_customer($member_email, $customer_arr = array())
    {
        try {

            $customer = Customer::create($customer_arr);
            $data     = $this->_result2Array($customer);

            return ['status' => 1, 'data' => $data];
        } catch (Exception $e) {
            return ['status' => 0, 'data' => $e, 'msg' => $e->getMessage()];
        }
    }

    public function customer_retrive_by_email($email)
    {

        try {

            $customer = Customer::all(['email' => $email]);

            $data     = $this->_result2Array($customer);

            return ['status' => 1, 'data' => $data['data']];
        } catch (Exception $e) {
            return ['status' => 0, 'data' => $e, 'msg' => $e->getMessage()];
        }
    }

    public function create_payment_method($token)
    {
        try {
            $stripe = new StripeClient($this->secret_key);

            // Create PaymentMethod using the token
            $paymentMethod = $stripe->paymentMethods->create([
                'type' => 'card',
                'card' => ['token' => $token],
            ]);

            return [
                'status' => 1,
                'data' => $paymentMethod,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 0,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function attach_payment_method($customer_id, $payment_method_id)
    {
        try {
            $stripe = new StripeClient($this->secret_key);
            $stripe->paymentMethods->attach(
                $payment_method_id,
                ['customer' => $customer_id]
            );

            // Optionally, you might want to update the default payment method for the customer
            $stripe->customers->update($customer_id, [
                'invoice_settings' => [
                    'default_payment_method' => $payment_method_id,
                ],
            ]);

            return [
                'status' => 1,
                'message' => 'Payment method attached successfully.',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 0,
                'error' => $e->getMessage(),
            ];
        }
    }


    // Create a new Stripe charge.
    public function create_charge($arr_data)
    {
        try {

            $charge = Charge::create($arr_data);
            $data   = $this->_result2Array($charge);

            return ['status' => 1, 'data' => $data];
        } catch (Exception $e) {
            return ['status' => 0, 'data' => $e, 'msg' => $e->getMessage()];
        }
    }

    public function retrieve_charge($charge_id = null, $metadata = [])
    {
        if ($charge_id != null) {
            try {

                $charge = Charge::retrieve($charge_id);

                if (!empty($metadata)) {
                    foreach ($metadata as $key => $value) {
                        $charge->metadata[$key] = $value;
                    }
                    $charge->save();
                }

                $data = $this->_result2Array($charge);
                return ['status' => 1, 'data' => $data];
            } catch (Exception $e) {
                return ['status' => 0, 'data' => $e, 'msg' => $e->getMessage()];
            }
        }

        return false;
    }

    public function refund_charge($charge_id, $amount = null)
    {
        try {
            $refund = Refund::create([
                'charge' => $charge_id,
                'amount' => $amount * 100,
            ]);

            return ['status' => 1, 'data' => $refund];
        } catch (Exception $e) {
            return ['status' => 0, 'data' => $e, 'msg' => $e->getMessage()];
        }
    }

    public function retrieve_or_create_product($name)
    {
        try {

            $products = Product::all(['limit' => 5]);
            foreach ($products->data as $product) {
                if ($product->name === $name) {
                    $data = $this->_result2Array($product);
                    return ['status' => 1, 'data' => $data];
                }
            }

            $product = Product::create(['name' => $name]);
            return ['status' => 1, 'data' => $product];
        } catch (Exception $e) {
            return ['status' => 0, 'data' => $e, 'msg' => $e->getMessage()];
        }
    }

    // public function retrieve_or_create_price($priceData)
    // {
    //     try {
    //         $prices = Price::all([
    //             'product' => $priceData['product'],
    //             'currency' => $priceData['currency'],
    //             'recurring' => ['interval' => $priceData['recurring']['interval']]
    //         ]);
    //         if (!empty($prices->data)) {
    //             return ['status' => 1, 'data' => $prices->data[0]];
    //         }

    //         $price = Price::create($priceData);
    //         return ['status' => 1, 'data' => $price];
    //     } catch (Exception $e) {
    //         return ['status' => 0, 'data' => $e, 'msg' => $e->getMessage()];
    //     }
    // }
    public function retrieve_or_create_price($priceData)
    {
        try {
            // Retrieve existing prices for the product with the same currency and interval
            $prices = Price::all([
                'product' => $priceData['product'],
                'currency' => $priceData['currency'],
                'recurring' => ['interval' => $priceData['recurring']['interval']]
            ]);

            // Check if an existing price matches the exact unit_amount
            foreach ($prices->data as $existing_price) {
                if ($existing_price->unit_amount == $priceData['unit_amount']) {
                    return ['status' => 1, 'data' => $existing_price];
                }
            }

            // If no matching price found, create a new one
            $price = Price::create($priceData);
            return ['status' => 1, 'data' => $price];
        } catch (Exception $e) {
            return ['status' => 0, 'data' => $e, 'msg' => $e->getMessage()];
        }
    }

    public function create_subscription($subscriptionData)
    {
        try {
            $subscription = Subscription::create($subscriptionData);
            return ['status' => 1, 'data' => $subscription];
        } catch (Exception $e) {
            return ['status' => 0, 'data' => $e, 'msg' => $e->getMessage()];
        }
    }

    public function cancel_subscription($subscriptionId)
    {
        try {
            $subscription = Subscription::retrieve($subscriptionId);
            $cancellation = $subscription->cancel(['at_period_end' => false]);
            return [
                'status' => 1,
                'data' => $cancellation,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 0,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function retrieve_subscription($subscriptionId)
    {
        try {
            $subscription = Subscription::retrieve($subscriptionId);
            return [
                'status' => 1,
                'data' => $subscription,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 0,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function create_payment_intent($intentData)
    {
        try {
            $stripe = new StripeClient($this->secret_key);

            // Create PaymentIntent
            $paymentIntent = $stripe->paymentIntents->create($intentData);

            return [
                'status' => 1,
                'data' => $paymentIntent,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 0,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function retrieve_payment_intent($paymentIntentId)
    {
        try {
            $stripe = new StripeClient($this->secret_key);

            // Retrieve the PaymentIntent by ID
            // $paymentIntent = $stripe->paymentIntents->retrieve($paymentIntentId);
            $paymentIntent = $stripe->paymentIntents->retrieve(
                $paymentIntentId,
                ['expand' => ['payment_method']]
            );
            return [
                'status' => 1,
                'customer' => $paymentIntent->customer,
                'payment_method' => $paymentIntent->payment_method,
                'data' => $paymentIntent, // Full PaymentIntent object
            ];
        } catch (\Exception $e) {
            return [
                'status' => 0,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function create_discount_coupon($discount_percent)
    {
        try {
            $coupon = Coupon::create([
                'percent_off' => $discount_percent,
                'duration' => 'once',
            ]);

            return ['status' => 1, 'data' => $coupon];
        } catch (\Exception $e) {
            return ['status' => 0, 'msg' => $e->getMessage()];
        }
    }

    public function createVerificationSession($member)
    {
        try {
            $verificationSession = VerificationSession::create([
                'type' => 'document', // Can be 'document' or 'id_number'
                'metadata' => [
                    'employer_id' => $member->id,
                    'email' => $member->members_email,
                ],
            ]);

            $stripe = new StripeClient($this->secret_key);

            // Create an ephemeral key for the VerificationSession
            $ephemeralKey = $stripe->ephemeralKeys->create([
                'verification_session' => $verificationSession->id,
            ], [
                'stripe_version' => '2024-06-20'
            ]);

            return ['status' => 1, 'url' => $verificationSession->url, 'session_id' => $verificationSession->id, 'client_secret' => $verificationSession->client_secret, 'ephemeral_key_secret' => $ephemeralKey->secret];
        } catch (\Exception $e) {
            return ['status' => 0, 'msg' => $e->getMessage()];
        }
    }

    public function checkVerificationStatus($verificationSessionId)
    {
        try {
            $verificationSession = VerificationSession::retrieve($verificationSessionId);

            return [
                'status' => 1,
                'verification' => $verificationSession,
                'status_code' => $verificationSession->status,
            ];
        } catch (\Exception $e) {
            return ['status' => 0, 'msg' => $e->getMessage()];
        }
    }

    public function get_account_by_email($email)
    {
        try {
            $accounts = Account::all(['limit' => 100]);

            foreach ($accounts->data as $account) {
                if ($account->email === $email) {
                    return $account;
                }
            }
        } catch (Exception $e) {
            return null;
        }

        return null;
    }

    public function generate_cash_payment_intent($requestData)
    {
        try {
            $stripe = new StripeClient($this->secret_key);

            // Step 1: Create a PaymentIntent for $1 charge
            $paymentIntentData = [
                'amount' => 100, // $1 in cents
                'currency' => strtolower($requestData['currency']),
                'payment_method_types' => ['card'],
                'description' => 'Cash Payment Verification',
                'metadata' => [
                    'customer_email' => $requestData['member_email'],
                    'customer_name' => $requestData['member_name'],
                    'purpose' => 'Generate PaymentIntent ID',
                ],
            ];

            if (isset($requestData['customer_id'])) {
                $paymentIntentData['customer'] = $requestData['customer_id'];
            }

            $paymentIntent = $stripe->paymentIntents->create($paymentIntentData);

            // Step 2: Attach a Payment Method if a customer is provided
            if (isset($requestData['customer_id'])) {
                $paymentMethods = $stripe->paymentMethods->all([
                    'customer' => $requestData['customer_id'],
                    'type' => 'card',
                ]);

                if (empty($paymentMethods->data)) {
                    return [
                        'status' => 0,
                        'msg' => 'No payment method found for the customer. Attach a payment method before proceeding.',
                    ];
                }

                $paymentMethodId = $paymentMethods->data[0]->id;

                // Confirm the PaymentIntent with the payment method
                $confirmedIntent = $stripe->paymentIntents->confirm($paymentIntent->id, [
                    'payment_method' => $paymentMethodId,
                ]);
            } else {
                // For one-time PaymentIntent, confirm without a customer
                $confirmedIntent = $stripe->paymentIntents->confirm($paymentIntent->id);
            }

            // Step 3: Refund the $1 charge
            if ($confirmedIntent->status === 'succeeded') {
                $refund = Refund::create([
                    'payment_intent' => $paymentIntent->id,
                    'amount' => 100, // Refund the $1 charge
                ]);

                return [
                    'status' => 1,
                    'msg' => 'PaymentIntent created and refunded successfully.',
                    'payment_intent_id' => $paymentIntent->id,
                    'client_secret' => $paymentIntent->client_secret,
                    'refund_status' => $refund->status,
                ];
            }

            return [
                'status' => 0,
                'msg' => 'PaymentIntent confirmation failed.',
            ];
        } catch (\Exception $e) {
            return ['status' => 0, 'msg' => $e->getMessage()];
        }
    }

    public function getPaymentIntentCardLast4($paymentIntentId)
    {
        try {
            $stripe = new StripeClient($this->secret_key);

            // Retrieve the PaymentIntent
            $paymentIntent = $stripe->paymentIntents->retrieve($paymentIntentId);

            // Get the payment method ID
            $paymentMethodId = $paymentIntent->payment_method;

            if (!$paymentMethodId) {
                return [
                    'status' => 0,
                    'msg' => 'Payment method not found for this PaymentIntent.',
                ];
            }

            // Retrieve the payment method details
            $paymentMethod = $stripe->paymentMethods->retrieve($paymentMethodId);

            // Extract the last 4 digits of the card
            if ($paymentMethod->type === 'card') {
                $last4 = $paymentMethod->card->last4;

                return [
                    'status' => 1,
                    'last4' => $last4,
                    'msg' => 'Last 4 digits of the card retrieved successfully.',
                ];
            }

            return [
                'status' => 0,
                'msg' => 'Payment method is not a card.',
            ];
        } catch (\Exception $e) {
            return ['status' => 0, 'msg' => $e->getMessage()];
        }
    }

    public function generate_new_card_payment_intent($paymentData)
    {

        try {
            $stripe = new StripeClient($this->secret_key);

            // Step 1: Create a PaymentIntent for $1 charge
            $paymentIntentData = [
                'amount' => 100, // $1 in cents
                'currency' => strtolower($paymentData['currency']),
                'payment_method' => $paymentData['paymentMethod'],
                'customer' => $paymentData['customer_id'],
                'description' => 'Cash Payment Verification',
                'metadata' => [
                    'customer_email' => $paymentData['member_email'],
                    'customer_name' => $paymentData['member_name'],
                    'purpose' => 'Generate PaymentIntent ID',
                ],
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never',
                ],
            ];

            if (isset($requestData['customer_id'])) {
                $paymentIntentData['customer'] = $requestData['customer_id'];
            }

            $paymentIntent = $stripe->paymentIntents->create($paymentIntentData);

            // For one-time PaymentIntent, confirm without a customer

            $confirmedIntent = $stripe->paymentIntents->confirm($paymentIntent->id, [
                'payment_method' => $paymentData['paymentMethod'],
            ]);


            // Step 3: Refund the $1 charge
            if ($confirmedIntent->status === 'succeeded') {
                $refund = Refund::create([
                    'payment_intent' => $paymentIntent->id,
                    'amount' => 100, // Refund the $1 charge
                ]);

                return [
                    'status' => 1,
                    'msg' => 'PaymentIntent created and refunded successfully.',
                    'payment_intent_id' => $paymentIntent->id,
                    'client_secret' => $paymentIntent->client_secret,
                    'refund_status' => $refund->status,
                ];
            }

            return [
                'status' => 0,
                'msg' => 'PaymentIntent confirmation failed.',
            ];
        } catch (\Exception $e) {
            return ['status' => 0, 'msg' => $e->getMessage()];
        }
    }

    public function getLatestInvoiceDetails($invoiceId)
    {
        try {
            $stripe = new StripeClient($this->secret_key);

            // Retrieve the invoice from Stripe
            $invoice = $stripe->invoices->retrieve($invoiceId);

            // Extract the payment intent ID
            $paymentIntentId = $invoice->payment_intent ?? null;
            $chargeId = $invoice->charge ?? null;

            if (!$paymentIntentId) {
                return [
                    'status' => 0,
                    'message' => 'No payment intent found for this invoice.',
                ];
            }

            // Retrieve the payment intent
            $paymentIntent = $stripe->paymentIntents->retrieve($paymentIntentId);

            // Retrieve latest charge from PaymentIntent
            $last4 = '';
            if (!empty($paymentIntent->charges->data)) {
                $latestCharge = $paymentIntent->charges->data[0];

                // Check if charge has a payment method and it's a card
                if (isset($latestCharge->payment_method_details->card)) {
                    $last4 = $latestCharge->payment_method_details->card->last4;
                }
            }

            return [
                'status' => 1,
                'invoice_id' => $invoiceId,
                'payment_intent_id' => $paymentIntentId,
                'last4' => $last4,
                'charge_id' => $chargeId,
                'message' => 'Invoice details retrieved successfully.',
            ];
        } catch (\Stripe\Exception\ApiErrorException $e) {
            return [
                'status' => 0,
                'message' => 'Stripe API Error: ' . $e->getMessage(),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 0,
                'message' => 'An unexpected error occurred: ' . $e->getMessage(),
            ];
        }
    }

    public function getCardLast4FromChargeId($chargeId)
    {
        $stripe = new StripeClient($this->secret_key);

        try {
            // Retrieve the charge details
            $charge = $stripe->charges->retrieve($chargeId);

            // Check if payment method exists
            if (isset($charge->payment_method)) {
                // Retrieve the payment method details
                $paymentMethod = $stripe->paymentMethods->retrieve($charge->payment_method);

                // Extract card details
                $cardDetails = $paymentMethod->card;

                return [
                    'status' => 1,
                    'last4' => $cardDetails->last4,
                ];
            } elseif (isset($charge->source) && $charge->source->object === 'card') {
                // If using source instead of payment_method
                $cardDetails = $charge->source;

                return [
                    'status' => 1,
                    'last4' => $cardDetails->last4,
                ];
            } else {
                return [
                    'status' => 0,
                    'message' => 'No card details found for this charge.',
                ];
            }
        } catch (\Stripe\Exception\ApiErrorException $e) {
            return [
                'status' => 0,
                'message' => 'An unexpected error occurred: ' . $e->getMessage(),
            ];
        }
    }















    /*********** Below function is for reference testing ****************/
    public function createPaymentIntend($payment_intent_array)
    {

        try {
            $intent         = \Stripe\PaymentIntent::create($payment_intent_array);
            $payment_intent = $this->_result2Array($intent);
            return ['status' => 1, 'data' => $payment_intent];
        } catch (Exception $e) {

            return ['status' => 0, 'data' => $e, 'msg' => $e->getMessage()];
        }
    }

    public function updatePaymentIntend($intend_id, $payment_intent_array)
    {

        try {
            $intent         = \Stripe\PaymentIntent::update($intend_id, $payment_intent_array);
            $payment_intent = $this->_result2Array($intent);
            return ['status' => 1, 'data' => $payment_intent];
        } catch (Exception $e) {

            return ['status' => 0, 'data' => $e, 'msg' => $e->getMessage()];
        }
    }

    public function confirmPaymentIntend($intend_id, $payment_intent_array)
    {

        try {
            $payment_intent      = \Stripe\PaymentIntent::retrieve($intend_id);
            $payment_intent_data = @$payment_intent->confirm($intend_id, $payment_intent_array);

            return ['status' => 1, 'data' => $payment_intent_data];
        } catch (Exception $e) {

            return ['status' => 0, 'data' => $e, 'msg' => $e->getMessage()];
        }
    }

    public function cancelPaymentIntend($intend_id)
    {

        try {
            $payment_intent = \Stripe\PaymentIntent::retrieve($intend_id);
            $result         = @$payment_intent->cancel($intend_id, []);

            return ['status' => 1, 'data' => $result];
        } catch (Exception $e) {

            return ['status' => 0, 'data' => $e, 'msg' => $e->getMessage()];
        }
    }
}
