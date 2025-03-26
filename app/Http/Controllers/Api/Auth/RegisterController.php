<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Mail;
use App\Libraries\MailchimpService;
use App\Models\ReachMember;
use App\Models\ReachEmailTemplate;
use App\Models\ReachMemberRefferals;

use Stripe\Stripe;
use Stripe\Charge;
use App\Libraries\StripeConnect;
use App\Models\StripePaymentTransaction;
use App\Models\MasterSetting;
use App\Models\FcmNotification;

use MailchimpMarketing\ApiClient;
use DateTime;
use App\Models\ReferralTypes;
use Carbon\Carbon;
use App\Services\NotificationService;
use App\Services\CurrencyService;
use App\Models\ReachTransaction;


class RegisterController extends Controller
{
    protected $stripeconnect;
    protected $notificationService;
    protected $currencyService;
    public function __construct(NotificationService $notificationService, CurrencyService $currencyService)
    {
        $this->notificationService = $notificationService;
        $this->currencyService = $currencyService;
        $this->stripeconnect = new StripeConnect();
    }

    public function signup(Request $request)
    {
        $requestData = $request->all();

        $validator = Validator::make($requestData, [
            'members_fname' => 'required|string|max:255',
            'members_lname' => 'required|string|max:255',
            //'members_email' => 'required|email|unique:reach_members,members_email',
            'members_email' => [
                'required',
                'email',
                Rule::unique('reach_members', 'members_email')->where(function ($query) {
                    return $query->where('is_deleted', '!=', 'Y');
                })
            ],
            'referral_code' => [
                'nullable',
                Rule::exists('reach_members', 'referral_code')->where(function ($query) {
                    return $query->where('is_deleted', '!=', 'Y');
                })
            ],
        ], [
            'members_fname.required' => 'The first name is required.',
            'members_lname.required' => 'The last name is required.',
            'members_email.required' => 'The email is required.',
            'members_email.email' => 'Please enter a valid email address.',
            'members_email.unique' => 'The email address has already been taken.',
            'referral_code.exists' => 'The referral code is invalid or does not exist.',
        ]);

        $validator->sometimes('members_password', 'required', function () use ($requestData) {
            return empty($requestData['appleLogin']) || !$requestData['appleLogin'];
        });


        // If validation fails, return the errors
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            $firstErrors = "";

            foreach ($errors as $field => $errorMessages) {
                $firstErrors = $errorMessages[0];
            }
            return response()->json(['error' => $firstErrors], 422);
        }

        $emailtoken = Str::random(10);

        $requestData['members_status'] = 'I';
        $requestData['members_type'] = 'F';
        $requestData['members_payment_status'] = 'I';
        $requestData['email_verify_token'] = $emailtoken;

        // $requestData['members_subscription_start_date'] = date('Y-m-d');
        $currentDate = new DateTime();
        //$currentDate->modify('+30 days');
        //$requestData['members_subscription_end_date'] = $currentDate->format('Y-m-d');
        $members_password = '';
        if (isset($requestData['members_password'])) {
            $members_password = $requestData['members_password'];
            $requestData['members_password'] = Hash::make($members_password);
        }


        if (isset($requestData['members_dob'])) {
            if ($requestData['members_dob'] === '-00-00') {
                unset($requestData['members_dob']);
            }
        }

        try {

            $isMobile = preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);

            $tokenName = $isMobile ? 'authToken_mobile' : 'authToken_web';

            $member = ReachMember::create($requestData);
            $token = $member->createToken($tokenName)->plainTextToken;

            if (isset($requestData['device_token']) && $requestData['device_token'] != '') {

                $existingDevice = FcmNotification::where('token', $requestData['device_token'])->first();
                if (!$existingDevice) {

                    $device_arr = [
                        'member_id' => $member->id,
                        'token' => $requestData['device_token'],
                        'device_type' => $requestData['device_type'],
                        'is_login' => 1,
                    ];
                    FcmNotification::create($device_arr);
                } else {

                    $existingDevice->update([
                        'member_id' => $member->id,
                        'device_type' => $requestData['device_type'],
                        'is_login' => 1,
                    ]);
                }
            }


            if (isset($requestData['referral_code'])) {
                $referringUserId = ReachMember::where('referral_code', $requestData['referral_code'])->pluck('id')->first();
                if ($referringUserId) {
                    $refferal = [
                        'member_id' => $member->id,
                        'refferal_member_id' => $referringUserId,
                        'refferal_code' => $request->referral_code
                    ];
                    $refferal = ReachMemberRefferals::create($refferal);

                    $User = ReachMember::where('id', $member->id)->select('members_fname', 'members_lname')->first();
                    //print_r($referringUser);die();
                    $firstName = $User->members_fname;
                    $lastName = $User->members_lname;
                    $referringMemberName = $firstName . ' ' . $lastName;


                    $message = "{$referringMemberName} joined as a free member using your referral code.";

                    $url_keyword = 'MyReferral';
                    $to = $referringUserId;

                    $this->notificationService->new_notification('0', '0', $member->id, $to, $message, $url_keyword);
                    //end for notification
                }
            }

            // Add user to Mailchimp list
            $this->subscribeToMailchimp($member, ['Reach-Member', 'Free-Member']);

            // Fetch Email Template
            $emailTemplate = ReachEmailTemplate::where('template_type', 'verify_email')->first();

            // Prepare Email Subject and Body
            $result = "";
            $subject = $emailTemplate->template_subject;
            $body = $emailTemplate->template_message;
            $tags = explode(",", $emailTemplate->template_tags);

            $link = config('site.url') . '/verify-email?token=' . $emailtoken;

            $replace = [$member->members_fname, $link];

            $body = str_replace($tags, $replace, $body);

            // Send Email to user
            $to = $member->members_email;
            $cc = [];
            //$bcc = $emailTemplate->template_bcc_address ? explode(',', $emailTemplate->template_bcc_address) : [];
            $bcc = [];
            $attachments = [];

            $mailchimpService = new MailchimpService();
            $mailchimpService->sendTemplateEmail($to, $body, $subject, NULL, $cc, $bcc, $attachments);
            $currentServerTimezone = Carbon::now('Europe/London');
            $time = $currentServerTimezone->format('Y-m-d H:i:s');
            return response()->json(['success' => true, 'message' => 'OK', 'data' => ['Token' => $token, 'Member_id' => $member->id, 'Member_type' => 'F', 'Member_fullname' => $member->members_fname . ' ' . $member->members_lname, 'date_time' => $time]], 200);
        } catch (\Exception $e) {
            // If an error occurs during creation, return an error response
            return response()->json(['error' => 'Failed to create member' . $e], 500);
        }
    }

    public function paidRegistration(Request $request)
    {
        $requestData = $request->all();

        $member = Auth::guard('sanctum')->user();
        if (!$member) {
            $member = $request->user();
        }

        if ($member) {
            $validator = Validator::make($requestData, [
                'members_fname' => 'required|string|max:255',
                'members_lname' => 'required|string|max:255',
                'members_email' => [
                    'required',
                    'email',
                    Rule::unique('reach_members')->ignore($member->id)->where(function ($query) {
                        return $query->where('is_deleted', '!=', 'Y');
                    }),
                ],
                'referral_code' => [
                    'nullable',
                    Rule::exists('reach_members', 'referral_code')->where(function ($query) {
                        return $query->where('is_deleted', '!=', 'Y')->where('members_type', 'M');
                    })
                ],
                //'members_phone' => 'required',
            ], [
                'members_fname.required' => 'The first name is required.',
                'members_lname.required' => 'The last name is required.',
                'members_email.required' => 'The email is required.',
                'members_email.email' => 'Please enter a valid email address.',
                'members_email.unique' => 'The email address has already been taken.',
                'referral_code.exists' => 'The referral code is invalid or does not exist.',
                //'members_phone.required' => 'The phone number is required.',
            ]);
        } else {
            $validator = Validator::make($requestData, [
                'members_fname' => 'required|string|max:255',
                'members_lname' => 'required|string|max:255',
                'members_email' => [
                    'required',
                    'email',
                    Rule::unique('reach_members', 'members_email')->where(function ($query) {
                        return $query->where('is_deleted', '!=', 'Y');
                    })
                ],
                //'members_phone' => 'required',
                'members_password' => 'required',
                'referral_code' => [
                    'nullable',
                    Rule::exists('reach_members', 'referral_code')->where(function ($query) {
                        return $query->where('is_deleted', '!=', 'Y')->where('members_type', 'M');
                    })
                ],
            ], [
                'members_fname.required' => 'The first name is required.',
                'members_lname.required' => 'The last name is required.',
                'members_email.required' => 'The email is required.',
                'members_email.email' => 'Please enter a valid email address.',
                'members_email.unique' => 'The email address has already been taken.',
                //'members_phone.required' => 'The phone number is required.',
                'members_password.required' => 'The password is required.',
                'referral_code.exists' => 'The referral code is invalid or does not exist.',
            ]);
        }

        // If validation fails, return the errors
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            $firstErrors = "";

            foreach ($errors as $field => $errorMessages) {
                $firstErrors = $errorMessages[0];
            }
            return response()->json(['error' => $firstErrors], 422);
        } else {

            $currentDate = new DateTime();
            /*if ($requestData['subscription_plan'] == "Annual") {
                $currentDate->modify('+1 year');
            } else {
                $currentDate->modify('+1 month');
            }*/


            if ($member) {
                unset($requestData['members_password']);
            } else {
                $members_password = $requestData['members_password'];
                $requestData['members_password'] = Hash::make($requestData['members_password']);
            }


            $referral_code = ReachMember::generateReferralCode();
            $firstReferralType = ReferralTypes::first();
            if ($firstReferralType) {
                $referral_type_id = $firstReferralType->id;
                $referral_rate = $firstReferralType->referral_rate; // Assuming 'rate' is a column in referral_types
            }
            $startDate = new DateTime();
            $endDate = clone $startDate;
            if ($requestData['subscription_plan'] === 'Annual') {
                $endDate->modify('+1 year');
            } else {
                $endDate->modify('+1 month');
            }
            $arrayData = [
                'members_name_title' => $requestData['members_name_title'],
                'members_fname' => $requestData['members_fname'],
                'members_lname' => $requestData['members_lname'],
                'members_email' => $requestData['members_email'],
                'members_phone_code' => $requestData['members_phone_code'],
                'members_phone' => $requestData['members_phone'],
                'members_address' => $requestData['members_address'],
                'members_country' => $requestData['members_country'],
                'members_region' => $requestData['members_region'],
                'members_postcode' => $requestData['members_postcode'],
                'members_town' => $requestData['members_town'],
                'members_subscription_plan' => $requestData['subscription_plan'],
                'members_subscription_start_date' => $startDate->format('Y-m-d'),
                'members_subscription_end_date' => $endDate->format('Y-m-d'),
                'subscription_status' => 'A',
                'members_type' => 'M',
                'members_status' => 'A',
                'referral_code' => $referral_code,
                'referral_type_id' => $referral_type_id,
                'referral_rate' => $referral_rate,
                'currency' => $requestData['currency'] ?? 'GBP',
                'ios_payment_token' => $requestData['ios_payment_token'] ?? '',
                'is_email_verified' => 1
            ];
        }

        try {

            //Process Stripe Payment
            if ($member) {
                $transactions = StripePaymentTransaction::where('member_id', $member->id)
                    ->where('payment_type', 'membership')
                    ->where('status', 'P')
                    ->get();
            } else {
                $transactions = collect();
            }

            if ($transactions->isEmpty()) {
                $payment = $this->paymentCardDetails($requestData);

                if ($payment['success']) {

                    if ($member) {
                        $member->update($arrayData);

                        // Check if the member already has a token
                        $existingToken = $member->tokens()->first();

                        if ($existingToken) {
                            $token = $request->bearerToken();
                        } else {
                            $token = $member->createToken('authToken')->plainTextToken;

                            if (isset($requestData['device_token']) && $requestData['device_token'] != '') {

                                $existingDevice = FcmNotification::where('token', $requestData['device_token'])->first();
                                if (!$existingDevice) {

                                    $device_arr = [
                                        'member_id' => $member->id,
                                        'token' => $requestData['device_token'],
                                        'device_type' => $requestData['device_type'],
                                        'is_login' => 1,
                                    ];
                                    FcmNotification::create($device_arr);
                                } else {

                                    $existingDevice->update([
                                        'member_id' => $member->id,
                                        'device_type' => $requestData['device_type'],
                                        'is_login' => 1,
                                    ]);
                                }
                            }
                        }
                        //insert records to reach_transactions
                        $parent_transaction_id = 'TXN-' . strtoupper(Str::random(10));
                        $amount = $this->currencyService->getMembershipFee($requestData);
                        if (isset($requestData['referral_code'])) {
                            $referringUserId = ReachMember::where('referral_code', $requestData['referral_code'])->pluck('id')->first();
                            $payment_to = $referringUserId;
                        } else {
                            $payment_to = NULL;
                        }
                        $transactionRecord = [
                            "transaction_id" => $parent_transaction_id,
                            "payment_id" => $payment['transaction_id'],
                            "member_id" => $member->id,
                            "connected_member_id" => $payment_to,
                            "parent_transaction_id" => NULL,
                            "original_amount" => $amount['membership_fee'],
                            "reduced_amount" => $amount['discount_amount'],
                            "actual_amount" => $amount['actual_amount'],
                            "from_currency" => $requestData['currency'],
                            "to_currency" => $requestData['currency'],
                            "rate" => 1,
                            "payment_date" => date('Y-m-d H:i:s'),
                            "status" => "Completed",
                            "type" => "Membership",
                            "description" => 'New full membership registration with full access to features',
                            'transaction_type' => 'Debit'
                        ];

                        $reachtransaction = new ReachTransaction($transactionRecord);
                        $reachtransaction->save();
                        //end for reach_transactions
                        if (isset($requestData['referral_code'])) {
                            $referringUserId = ReachMember::where('referral_code', $requestData['referral_code'])->pluck('id')->first();
                            if ($referringUserId) {
                                /*$refferal = [
                                'member_id'=>$member->id,
                                'refferal_member_id'=>$referringUserId,
                                'refferal_code'=>$request->referral_code
                            ];
                            $refferal = ReachMemberRefferals::create($refferal);*/
                                $refferal = ReachMemberRefferals::updateOrCreate(
                                    [
                                        // Search criteria to find an existing record
                                        'member_id' => $member->id,
                                        'refferal_member_id' => $referringUserId
                                    ],
                                    [
                                        // Fields to update or set if creating a new record
                                        'refferal_code' => $request->referral_code
                                    ]
                                );

                                //for notification
                                $User = ReachMember::where('id', $member->id)->select('members_fname', 'members_lname')->first();
                                //print_r($referringUser);die();
                                $firstName = $User->members_fname;
                                $lastName = $User->members_lname;
                                $referringMemberName = $firstName . ' ' . $lastName;


                                $message = "You have a pending referral amount from {$referringMemberName}. It will be available to withdraw after one month.";

                                $url_keyword = 'MyReferral';
                                $to = $referringUserId;

                                $this->notificationService->new_notification('0', '0', $member->id, $to, $message, $url_keyword);
                                //end for notification
                                //insert records to reach_transactions
                                if ($requestData['subscription_plan'] == "Monthly") {
                                    $discountAmount = $amount['actual_amount'];
                                } else {
                                    $discountAmount = $amount['discount_amount'];
                                }
                                $transaction_id = 'TXN-' . strtoupper(Str::random(10));
                                $transactionRecord = [
                                    "transaction_id" => $transaction_id,
                                    "payment_id" => $payment['transaction_id'],
                                    "member_id" => $referringUserId,
                                    "connected_member_id" => $member->id,
                                    "parent_transaction_id" => $parent_transaction_id,
                                    "original_amount" => $discountAmount,
                                    "reduced_amount" => NuLL,
                                    "actual_amount" => $discountAmount,
                                    "from_currency" => $requestData['currency'],
                                    "to_currency" => $amount['parent_currency'],
                                    "rate" => $this->currencyService->getCurrencyRate($requestData['currency'], $amount['parent_currency']),
                                    "payment_date" => date('Y-m-d H:i:s'),
                                    "status" => "Pending",
                                    "type" => "Referral",
                                    "description" => $message,
                                    'transaction_type' => 'Credit'
                                ];

                                $reachtransaction = new ReachTransaction($transactionRecord);
                                $reachtransaction->save();
                                //end for reach_transactions
                            }
                        }

                        $this->subscribeToMailchimp($member, ['Reach-Member', 'Full-Member']);
                    } else {

                        $arrayData['members_password'] = $requestData['members_password'];
                        $member = ReachMember::create($arrayData);
                        $token = $member->createToken('authToken')->plainTextToken;

                        if (isset($requestData['device_token']) && $requestData['device_token'] != '') {

                            $existingDevice = FcmNotification::where('token', $requestData['device_token'])->first();
                            if (!$existingDevice) {

                                $device_arr = [
                                    'member_id' => $member->id,
                                    'token' => $requestData['device_token'],
                                    'device_type' => $requestData['device_type'],
                                    'is_login' => 1,
                                ];
                                FcmNotification::create($device_arr);
                            } else {

                                $existingDevice->update([
                                    'member_id' => $member->id,
                                    'device_type' => $requestData['device_type'],
                                    'is_login' => 1,
                                ]);
                            }
                        }

                        //insert records to reach_transactions
                        $parent_transaction_id = 'TXN-' . strtoupper(Str::random(10));
                        $amount = $this->currencyService->getMembershipFee($requestData);
                        if (isset($requestData['referral_code'])) {
                            $referringUserId = ReachMember::where('referral_code', $requestData['referral_code'])->where('members_type', 'M')->pluck('id')->first();
                            $payment_to = $referringUserId;
                        } else {
                            $payment_to = NULL;
                        }
                        $transactionRecord = [
                            "transaction_id" => $parent_transaction_id,
                            "payment_id" => $payment['transaction_id'],
                            "member_id" => $member->id,
                            "connected_member_id" => $payment_to,
                            "parent_transaction_id" => NULL,
                            "original_amount" => $amount['membership_fee'],
                            "reduced_amount" => $amount['discount_amount'],
                            "actual_amount" => $amount['actual_amount'],
                            "from_currency" => $requestData['currency'],
                            "to_currency" => $requestData['currency'],
                            "rate" => 1,
                            "payment_date" => date('Y-m-d H:i:s'),
                            "status" => "Completed",
                            "type" => "Membership",
                            "description" => 'New full membership registration with full access to features',
                            'transaction_type' => 'Debit'
                        ];

                        $reachtransaction = new ReachTransaction($transactionRecord);
                        $reachtransaction->save();
                        //end for reach_transactions
                        if (isset($requestData['referral_code'])) {
                            $referringUserId = ReachMember::where('referral_code', $requestData['referral_code'])->where('members_type', 'M')->pluck('id')->first();
                            if ($referringUserId) {
                                $refferal = [
                                    'member_id' => $member->id,
                                    'refferal_member_id' => $referringUserId,
                                    'refferal_code' => $request->referral_code
                                ];
                                $refferal = ReachMemberRefferals::create($refferal);


                                //for notification
                                $User = ReachMember::where('id', $member->id)->select('members_fname', 'members_lname')->first();
                                //print_r($referringUser);die();
                                $firstName = $User->members_fname;
                                $lastName = $User->members_lname;
                                $referringMemberName = $firstName . ' ' . $lastName;


                                $message = "You have a pending referral amount from {$referringMemberName}. It will be available to withdraw after one month.";

                                $url_keyword = 'MyReferral';
                                $to = $referringUserId;

                                $this->notificationService->new_notification('0', '0', $member->id, $to, $message, $url_keyword);
                                //end for notification

                                //insert records to reach_transactions
                                if ($requestData['subscription_plan'] == "Monthly") {
                                    $discountAmount = $amount['actual_amount'];
                                } else {
                                    $discountAmount = $amount['discount_amount'];
                                }
                                $transaction_id = 'TXN-' . strtoupper(Str::random(10));
                                $transactionRecord = [
                                    "transaction_id" => $transaction_id,
                                    "payment_id" => $payment['transaction_id'],
                                    "member_id" => $referringUserId,
                                    "connected_member_id" => $member->id,
                                    "parent_transaction_id" => $parent_transaction_id,
                                    "original_amount" => $discountAmount,
                                    "reduced_amount" => NuLL,
                                    "actual_amount" => $discountAmount,
                                    "from_currency" => $requestData['currency'],
                                    "to_currency" => $amount['parent_currency'],
                                    "rate" => $this->currencyService->getCurrencyRate($requestData['currency'], $amount['parent_currency']),
                                    "payment_date" => date('Y-m-d H:i:s'),
                                    "status" => "Pending",
                                    "type" => "Referral",
                                    "description" => $message,
                                    'transaction_type' => 'Credit'
                                ];

                                $reachtransaction = new ReachTransaction($transactionRecord);
                                $reachtransaction->save();
                                //end for reach_transactions


                            }
                        }

                        $this->subscribeToMailchimp($member, ['Reach-Member', 'Full-Member']);

                        // Fetch Email Template
                        $emailTemplate = ReachEmailTemplate::where('template_type', 'user_registration')->first();

                        // Prepare Email Subject and Body
                        $result = "";
                        $subject = $emailTemplate->template_subject;
                        $body = $emailTemplate->template_message;
                        $tags = explode(",", $emailTemplate->template_tags);
                        $replace = [$member->members_fname, $member->members_email, $members_password];
                        $body = str_replace($tags, $replace, $body);

                        // Send Email to user
                        $to = $member->members_email;
                        $cc = [];
                        //$bcc = $emailTemplate->template_bcc_address ? explode(',', $emailTemplate->template_bcc_address) : [];
                        $bcc = [];
                        $attachments = [];

                        $mailchimpService = new MailchimpService();
                        $mailchimpService->sendTemplateEmail($to, $body, $subject, NULL, $cc, $bcc, $attachments);
                    }

                    // Update transaction
                    $transaction = StripePaymentTransaction::find($payment['transaction_id']);
                    if ($transaction) {
                        $transaction->update(['member_id' => $member->id]);
                    }
                    $currentServerTimezone = Carbon::now('Europe/London');
                    $time = $currentServerTimezone->format('Y-m-d H:i:s');

                    return response()->json(['success' => true, 'message' => 'OK', 'data' => ['Token' => $token, 'Member_id' => $member->id, 'Member_type' => 'M', 'Member_fullname' => $member->members_fname . ' ' . $member->members_lname, 'date_time' => $time, 'currency' => $member->currency]], 200);
                }
            } else {
                $currentServerTimezone = Carbon::now('Europe/London');
                $time = $currentServerTimezone->format('Y-m-d H:i:s');
                $existingToken = $member->tokens()->first();
                if ($existingToken) {
                    $token = $request->bearerToken();
                } else {
                    $token = $member->createToken('authToken')->plainTextToken;
                }
                return response()->json(['success' => true, 'message' => 'Already Subscribed', 'data' => ['Token' => $token, 'Member_id' => $member->id, 'Member_type' => 'M', 'Member_fullname' => $member->members_fname . ' ' . $member->members_lname, 'date_time' => $time, 'currency' => $member->currency]], 200);
            }
        } catch (\Exception $e) {
            // If an error occurs during creation, return an error response
            return response()->json(['error' => 'Failed to create member' . $e], 500);
        }
    }

    public function forgotPassword(Request $request)
    {
        $requestData = $request->all();

        $validator = Validator::make($requestData, [
            'members_email' => 'required|email',
        ], [
            'members_email.required' => 'The email is required.',
            'members_email.email' => 'Please enter a valid email address.',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            $firstErrors = "";

            foreach ($errors as $field => $errorMessages) {
                $firstErrors = $errorMessages[0];
            }
            return response()->json(['error' => $firstErrors], 422);
        } else {

            $member = ReachMember::where('members_status', 'A')
                ->where('members_email', $requestData['members_email'])
                ->first();

            if (!$member) {
                return response()->json(['error' => 'Email not found'], 404);
            }

            // Generate a unique token
            $token = Str::random(10);

            // Save the token in the database
            $member->update(['password_reset_token' => $token, 'password_reset_time' => now()]);

            // Send an email notification
            $emailTemplate = ReachEmailTemplate::where('template_type', 'reset_password')->first();

            // Prepare Email Subject and Body

            $link = config('site.url') . '/reset-password?token=' . $token;

            $subject = $emailTemplate->template_subject;
            $content = $emailTemplate->template_message;
            $tags = explode(",", $emailTemplate->template_tags);
            $replace = [$member->members_fname, $link];
            $body = str_replace($tags, $replace, $content);
            $to = $member->members_email;

            $mailchimpService = new MailchimpService();
            $mailchimpService->sendTemplateEmail($to, $body, $subject);

            return response()->json(['success' => true, 'message' => 'Email sent successfully.', 'data' => ['Member_id' => $member->id, 'Member_fullname' => $member->members_fname . ' ' . $member->members_lname]], 200);
        }
    }

    public function resetPassword(Request $request)
    {
        $requestData = $request->all();

        $validator = Validator::make($requestData, [
            'new_password' => 'required|min:8|confirmed',
            'token' => 'required',
        ], [
            'new_password.required' => 'The new password is required.',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            $firstErrors = "";

            foreach ($errors as $field => $errorMessages) {
                $firstErrors = $errorMessages[0];
            }

            return response()->json(['error' => $firstErrors], 422);
        } else {

            $new_password = $request->new_password;
            $token = $request->token;

            $member = ReachMember::where('password_reset_token', $token)
                ->first();

            if (!$member) {
                return response()->json(['error' => 'Invalid token or email id'], 422);
            }

            $member->update(['members_password' => Hash::make($new_password), 'password_reset_token' => null]);

            return response()->json(['success' => true, 'message' => 'Password reset successfully', 'data' => ['Member_id' => $member->id, 'Member_fullname' => $member->members_fname . ' ' . $member->members_lname]]);
        }
    }

    public function checkMemberEmailExists(Request $request)
    {
        $requestData = $request->all();

        $validator = Validator::make($requestData, [
            'members_email' => [
                'required',
                'email',
                function ($attribute, $value, $fail) {
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $fail('Please enter a valid email address.');
                        return;
                    }

                    if (!str_contains($value, '@')) {
                        $fail('Email must contain @ symbol.');
                        return;
                    }

                    $domain = substr(strrchr($value, "@"), 1);

                    if (!$domain || !checkdnsrr($domain, "MX")) {
                        $fail('Please enter a valid email with an active domain.');
                    }
                },
            ],
        ], [
            'members_email.required' => 'The email is required.',
            'members_email.email' => 'Please enter a valid email address.',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $member = ReachMember::where('members_email', $requestData['members_email'])->first();

        if ($member) {
            return response()->json(['success' => false, 'message' => 'Member email exists'], 200);
        } else {
            return response()->json(['success' => true, 'message' => 'Member email not found'], 200);
        }
    }


    /* private function subscribeToMailchimp($member, $tags = [])
     {
         $mailchimp = new ApiClient();
         $mailchimp->setConfig([
             'apiKey' => env('MAILCHIMP_API_KEY'),
             'server' => substr(env('MAILCHIMP_API_KEY'), strpos(env('MAILCHIMP_API_KEY'), '-') + 1)
         ]);

         try {

             $listId = env('MAILCHIMP_LIST_ID');
             $subscriberHash = md5(strtolower($member->members_email));
             $user = $mailchimp->lists->getListMember($listId, $subscriberHash);

             if ($user->status === 'subscribed') {
                 // Update tags for the existing subscriber
                 $mailchimp->lists->updateListMemberTags($listId, $subscriberHash, [
                     'tags' => array_map(function ($tag) {
                         return ['name' => $tag, 'status' => 'active'];
                     }, $tags)
                 ]);
             } else {

                 $response = $mailchimp->lists->addListMember(env('MAILCHIMP_LIST_ID'), [
                     'email_address' => $member->members_email,
                     'status' => 'subscribed',
                     'merge_fields' => [
                         'FNAME' => $member->members_fname,
                         'LNAME' => $member->members_lname,
                         'ADDRESS' => (isset($member->members_address)) ? $member->members_address : '',
                         'PHONE' => (isset($member->members_phone)) ? "+" . $member->members_phone_code . $member->members_phone : '',
                     ],
                     'tags' => $tags
                 ]);
             }
         } catch (\MailchimpMarketing\ApiException $e) {
             //throw new \Exception('Error from MailChimp: ' . $e->getMessage());
             return response()->json(['success' => false, 'message' => 'Error from MailChimp subscribe'], 200);
         }
     }*/
    private function subscribeToMailchimp($member, $tags = [])
    {
        try {
            $mailchimp = new ApiClient();
            $mailchimp->setConfig([
                'apiKey' => env('MAILCHIMP_API_KEY'),
                'server' => substr(env('MAILCHIMP_API_KEY'), strpos(env('MAILCHIMP_API_KEY'), '-') + 1)
            ]);

            $listId = env('MAILCHIMP_LIST_ID');
            $subscriberHash = md5(strtolower($member->members_email));

            try {
                // Try to get existing member
                $user = $mailchimp->lists->getListMember($listId, $subscriberHash);

                // If we get here, user exists - update their tags
                if ($user->status === 'subscribed') {
                    $mailchimp->lists->updateListMemberTags($listId, $subscriberHash, [
                        'tags' => array_map(function ($tag) {
                            return ['name' => $tag, 'status' => 'active'];
                        }, $tags)
                    ]);

                    return ['success' => true, 'message' => 'Member tags updated successfully'];
                }
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                // Handle 404 (member not found) by creating new member
                if ($e->getResponse()->getStatusCode() === 404) {
                    $response = $mailchimp->lists->addListMember($listId, [
                        'email_address' => $member->members_email,
                        'status' => 'subscribed',
                        'merge_fields' => [
                            'FNAME' => $member->members_fname,
                            'LNAME' => $member->members_lname,
                            'ADDRESS' => $member->members_address ?? '',
                            'PHONE' => isset($member->members_phone)
                                ? "+" . $member->members_phone_code . $member->members_phone
                                : '',
                        ],
                        'tags' => $tags
                    ]);

                    return ['success' => true, 'message' => 'New member subscribed successfully'];
                }

                // Re-throw other client errors
                throw $e;
            }
        } catch (\MailchimpMarketing\ApiException $e) {
            \Log::error('Mailchimp API Error: ' . $e->getMessage(), [
                'email' => $member->members_email,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            return ['success' => false, 'message' => 'Error subscribing to mailing list'];
        } catch (\Exception $e) {
            \Log::error('Unexpected error in Mailchimp subscription: ' . $e->getMessage(), [
                'email' => $member->members_email,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            return ['success' => false, 'message' => 'Unexpected error in mailing list subscription'];
        }
    }

    public function subscribeNewsletter(Request $request)
    {
        $requestData = $request->all();

        $validator = Validator::make($requestData, [
            'user_email' => 'required|email',
        ], [
            'user_email.required' => 'The email is required.',
            'user_email.email' => 'Please enter a valid email address.',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 200);
        }

        $mailchimp = new ApiClient();
        $mailchimp->setConfig([
            'apiKey' => env('MAILCHIMP_API_KEY'),
            'server' => substr(env('MAILCHIMP_API_KEY'), strpos(env('MAILCHIMP_API_KEY'), '-') + 1)
        ]);

        $email = $requestData['user_email'];
        $listId = env('MAILCHIMP_LIST_ID');
        $tags = ['Subscriber', 'News', 'Events', 'Offers'];

        try {
            // Check if the user is already subscribed
            $subscriberHash = md5(strtolower($email));

            try {
                $member = $mailchimp->lists->getListMember($listId, $subscriberHash);

                if ($member->status === 'subscribed') {
                    return response()->json(['success' => false, 'message' => 'This email is already subscribed to the newsletter.'], 200);
                } else {
                    // If found but not subscribed, update the status and tags
                    $response = $mailchimp->lists->updateListMember($listId, $subscriberHash, [
                        'status' => 'subscribed',
                        'tags' => $tags
                    ]);

                    return response()->json(['success' => true, 'message' => 'Email subscribed successfully.'], 200);
                }
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                // If the user is not found (404 error), subscribe them
                if ($e->getResponse()->getStatusCode() === 404) {
                    try {
                        $response = $mailchimp->lists->addListMember($listId, [
                            'email_address' => $email,
                            'status' => 'subscribed',
                            'tags' => $tags
                        ]);

                        return response()->json(['success' => true, 'message' => 'Email subscribed successfully.'], 200);
                    } catch (\MailchimpMarketing\ApiException $e) {
                        return response()->json(['success' => false, 'message' => 'Invalid email address'], 200);
                    }
                } else {
                    return response()->json(['success' => false, 'message' => 'Invalid email address'], 200);
                }
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Invalid email address'], 200);
        }
    }

    public function paymentCardDetails($requestData)
    {
        // Validate the stripeToken
        $validator = Validator::make($requestData, [
            'stripeToken' => 'required',
        ]);

        if ($validator->fails()) {

            return [
                'success' => false,
                'message' => 'Stripe Token is Misssing.',
            ];
        }
        try {


            // Check if customer with the same email exists
            // $this->stripeconnect = new StripeConnect();
            $existingCustomer = $this->stripeconnect->customer_retrive_by_email($requestData['members_email']);

            $referred_by = "";
            $referral_dts = [];

            if (isset($requestData['referral_code'])) {
                $referral_dts = ReachMember::where('referral_code', $requestData['referral_code'])->where('members_type', 'M')->first();
                if ($referral_dts) {
                    $referred_by = $referral_dts->members_fname . ' ' . $referral_dts->members_lname;
                }
            }

            if ($existingCustomer['status'] === 1 && !empty($existingCustomer['data'])) {
                $customer = $existingCustomer['data'][0];
            } else {
                // Create a new customer
                $customer_arr = [
                    'email' => $requestData['members_email'],
                    'name' => $requestData['members_fname'] . " " . $requestData['members_lname'],
                    'source' => $requestData['stripeToken'],
                    "address" => [
                        'line1' => $requestData['members_address'] ?? '',
                        'country' => $requestData['members_country'] ?? '',
                        'city' => $requestData['members_town'] ?? '',
                        'postal_code' => $requestData['members_postcode'] ?? ''
                    ],
                ];


                $customerDts = $this->stripeconnect->create_customer($requestData['members_email'], $customer_arr);
                $customer = $customerDts['data'];
            }

            // Retrieve the membership fee
            $feeSettings = MasterSetting::select(
                'full_membership_fee',
                'monthly_membership_fee',
                'full_membership_fee_euro',
                'full_membership_fee_dollar',
                'monthly_membership_fee_euro',
                'monthly_membership_fee_dollar'
            )->find(1);


            if ($requestData['subscription_plan'] == "Monthly") {
                if ($requestData['currency'] == 'EUR') {
                    $membership_fee = $feeSettings['monthly_membership_fee_euro'];
                } else if ($requestData['currency'] == 'USD') {
                    $membership_fee = $feeSettings['monthly_membership_fee_dollar'];
                } else {
                    $membership_fee = $feeSettings['monthly_membership_fee'];
                }
            } else {
                if ($requestData['currency'] == 'EUR') {
                    $membership_fee = $feeSettings['full_membership_fee_euro'];
                } else if ($requestData['currency'] == 'USD') {
                    $membership_fee = $feeSettings['full_membership_fee_dollar'];
                } else {
                    $membership_fee = $feeSettings['full_membership_fee'];
                }
            }

            $discountAmount = 0;
            $referralDiscount = 0;
            $coupon_id = null;

            // Handle referral discount
            if (!empty($referral_dts)) {

                if ($requestData['subscription_plan'] == "Monthly") {
                    if ($referral_dts['currency'] == 'EUR') {
                        $parent_membership_fee = $feeSettings['monthly_membership_fee_euro'];
                    } else if ($referral_dts['currency'] == 'USD') {
                        $parent_membership_fee = $feeSettings['monthly_membership_fee_dollar'];
                    } else {
                        $parent_membership_fee = $feeSettings['monthly_membership_fee'];
                    }
                } else {
                    if ($referral_dts['currency'] == 'EUR') {
                        $parent_membership_fee = $feeSettings['full_membership_fee_euro'];
                    } else if ($referral_dts['currency'] == 'USD') {
                        $parent_membership_fee = $feeSettings['full_membership_fee_dollar'];
                    } else {
                        $parent_membership_fee = $feeSettings['full_membership_fee'];
                    }
                }

                $referral_rate = $referral_dts->referral_rate / 100;
                // $discountAmount = $membership_fee * 0.10;
                $referralDiscount = $membership_fee * $referral_rate;
                if ($requestData['subscription_plan'] == "Monthly") {
                    $discountAmount = $membership_fee - $referralDiscount;
                } else {
                    $discountAmount = $membership_fee * $referral_rate;
                }
                // Create a coupon in Stripe for 10% off

                $coupon = $this->stripeconnect->create_discount_coupon($referral_dts->referral_rate);

                $coupon_id = $coupon['data']['id'];
            }

            // Final membership fee after applying the discount
            $final_membership_fee = $membership_fee - $referralDiscount;


            // $original_amount_paid = $final_membership_fee;
            // Create a product if it does not exist
            $product = $this->stripeconnect->retrieve_or_create_product('Membership');
            $product_id = $product['data']['id'];

            // Create a price if it does not exist
            $interval = $requestData['subscription_plan'] === "Annual" ? 'year' : 'month';
            $priceData = [
                'product' => $product_id,
                'unit_amount' => $membership_fee * 100,
                'currency' => strtolower($requestData['currency']),
                'recurring' => [
                    'interval' => $interval,
                ],
            ];
            $price = $this->stripeconnect->retrieve_or_create_price($priceData);
            $price_id = $price['data']['id'];

            // Create a subscription
            $subscriptionData = [
                'customer' => $customer['id'],
                'items' => [
                    ['price' => $price_id],
                ],
                'metadata' => [
                    'subscription_plan' => $requestData['subscription_plan'],
                    'membership_fee' => $membership_fee . "(" . $requestData['currency'] . ")",
                    'referred_by' => $referred_by,
                ],

            ];

            if ($coupon_id) {
                $subscriptionData['coupon'] = $coupon_id;
            }

            $subscription = $this->stripeconnect->create_subscription($subscriptionData);

            if ($subscription['status'] === 1) {

                $subscriptionData = $subscription['data'];
                $subscription_id = $subscriptionData['id'];
                $latest_invoice = $subscriptionData['latest_invoice'];
                $last_4 = $subscriptionData['payment_method_details']['card']['last4'] ?? '';
                $charge_id = "";

                $paymentData = [
                    'customer_id' => $customer['id'],
                    'currency' => $requestData['currency'],
                    'member_email' => $requestData['members_email'],
                    'member_name' => $requestData['members_fname'] . " " . $requestData['members_lname'],
                ];
                $response = $this->stripeconnect->generate_cash_payment_intent($paymentData);

                //$response = $this->stripeconnect->getLatestInvoiceDetails($latest_invoice);

                if ($response['status'] === 1) {
                    $payment_intent_id = $response['payment_intent_id'];
                    $cardDetails = $this->stripeconnect->getPaymentIntentCardLast4($payment_intent_id);
                    $last_4 = $cardDetails['last4'] ?? '';

                    //$charge_id = $response['charge_id'];

                    //$cardDetails = $this->stripeconnect->getCardLast4FromChargeId($charge_id);
                    //$last_4 = $cardDetails['last4'] ?? '';
                } else {
                    $payment_intent_id = "";
                }

                $paymentRecord = [
                    "stripe_payment_intend_id" => $payment_intent_id,
                    "stripe_subscription_id" => $subscription_id,
                    "stripe_charge_id" => $charge_id,
                    "amount_paid" => $membership_fee,
                    "payment_date" => date("Y-m-d"),
                    "currency" => $requestData['currency'],
                    "charge_description" => 'Membership Fee',
                    "last_4" => $last_4,
                    "balance_transaction" => "",
                    "status" => "P",
                    "payment_type" => "membership",
                    "original_amount_paid" => isset($final_membership_fee) ? $final_membership_fee : 0,
                    "parent_currency" => isset($referral_dts['currency']) ? $referral_dts['currency'] : 0,
                ];

                if ($coupon_id) {
                    $paymentRecord['discount_amount'] = $discountAmount;
                    $paymentRecord['discount_type'] = "R";
                }

                $transaction = new StripePaymentTransaction($paymentRecord);
                $transaction->save();

                return [
                    'success' => true,
                    'message' => 'Payment created successfully!',
                    'transaction_id' => $transaction->payment_id,
                    'subscription_id' => $subscription_id,
                ];
            } else {

                return [
                    'success' => false,
                    'message' => 'Subscription creation failed.',
                ];
            }
        } catch (\Exception $e) {

            return [
                'success' => false,
                'message' => 'An error occurred while payment for subscription.',
                'error' => $e->getMessage()
            ];
        }
    }

    public function updateMembership(Request $request)
    {
        $requestData = $request->all();

        $member = Auth::guard('sanctum')->user();
        if (!$member) {
            $member = $request->user();
        }

        if ($member) {
            $validator = Validator::make($requestData, [
                'members_fname' => 'required|string|max:255',
                'members_lname' => 'required|string|max:255',
                'members_email' => [
                    'required',
                    'email',
                    Rule::unique('reach_members')->ignore($member->id)->where(function ($query) {
                        return $query->where('is_deleted', '!=', 'Y');
                    }),
                ],
                'referral_code' => [
                    'nullable',
                    Rule::exists('reach_members', 'referral_code')->where(function ($query) {
                        return $query->where('is_deleted', '!=', 'Y');
                    })
                ],
                //'members_phone' => 'required',
            ], [
                'members_fname.required' => 'The first name is required.',
                'members_lname.required' => 'The last name is required.',
                'members_email.required' => 'The email is required.',
                'members_email.email' => 'Please enter a valid email address.',
                'members_email.unique' => 'The email address has already been taken.',
                'referral_code.exists' => 'The referral code is invalid or does not exist.',
                //'members_phone.required' => 'The phone number is required.',
            ]);
        } else {
            $validator = Validator::make($requestData, [
                'members_fname' => 'required|string|max:255',
                'members_lname' => 'required|string|max:255',
                'members_email' => [
                    'required',
                    'email',
                    Rule::unique('reach_members', 'members_email')->where(function ($query) {
                        return $query->where('is_deleted', '!=', 'Y');
                    })
                ],
                //'members_phone' => 'required',
                'members_password' => 'required',
                'referral_code' => [
                    'nullable',
                    Rule::exists('reach_members', 'referral_code')->where(function ($query) {
                        return $query->where('is_deleted', '!=', 'Y');
                    })
                ],
            ], [
                'members_fname.required' => 'The first name is required.',
                'members_lname.required' => 'The last name is required.',
                'members_email.required' => 'The email is required.',
                'members_email.email' => 'Please enter a valid email address.',
                'members_email.unique' => 'The email address has already been taken.',
                //'members_phone.required' => 'The phone number is required.',
                'members_password.required' => 'The password is required.',
                'referral_code.exists' => 'The referral code is invalid or does not exist.',
            ]);
        }

        // If validation fails, return the errors
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        } else {
            $randomString = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz"), 0, 3);
            $randomNumber = rand(100, 999);
            $member_token = $randomString . $randomNumber;
        }

        try {

            if ($member) {
                unset($requestData['members_password']);
            } else {
                $members_password = $requestData['members_password'];
                $requestData['members_password'] = Hash::make($requestData['members_password']);
            }
            if ($requestData['members_dob'] === '-00-00') {
                $requestData['members_dob'] = NULL;
            }


            $arrayData = [
                'members_name_title' => $requestData['members_name_title'],
                'members_fname' => $requestData['members_fname'],
                'members_lname' => $requestData['members_lname'],
                'members_email' => $requestData['members_email'],
                'members_phone_code' => $requestData['members_phone_code'],
                'members_phone' => $requestData['members_phone'],
                'members_address' => $requestData['members_address'],
                'members_country' => $requestData['members_country'],
                'members_region' => $requestData['members_region'],
                'members_postcode' => $requestData['members_postcode'],
                'members_town' => $requestData['members_town'],
                'members_street' => $requestData['members_street'],
                'members_dob' => $requestData['members_dob'],
                'members_type' => 'F',
                'members_status' => 'A',
                'currency' => $requestData['currency'] ?? 'GBP',
                'members_subscription_plan' => $requestData['subscription_plan'],
                'ios_payment_token' => $member_token,
            ];


            if ($member) {
                $member->update($arrayData);
            } else {
                $arrayData['members_password'] = $requestData['members_password'];
                $member = ReachMember::create($arrayData);
            }
            if (isset($requestData['referral_code'])) {
                $referringUserId = ReachMember::where('referral_code', $requestData['referral_code'])->pluck('id')->first();
                if ($referringUserId) {
                    $refferal = [
                        'member_id' => $member->id,
                        'refferal_member_id' => $referringUserId,
                        'refferal_code' => $requestData['referral_code']
                    ];
                    $refferal = ReachMemberRefferals::create($refferal);
                }
            }
            $encodedToken = base64_encode($member_token);
            $existingToken = $member->tokens()->first();
            if ($existingToken && !empty($existingToken->plainTextToken)) {
                $token = $existingToken->plainTextToken;
            } else {
                $token = $member->createToken('authToken')->plainTextToken;
            }
            $currentServerTimezone = Carbon::now('Europe/London');
            $time = $currentServerTimezone->format('Y-m-d H:i:s');
            return response()->json(['success' => true, 'message' => 'OK', 'data' => ['Token' => $token, 'Member_id' => $member->id, 'Member_type' => 'F', 'Member_fullname' => $member->members_fname . ' ' . $member->members_lname, 'date_time' => $time], 'member_token' => $encodedToken], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update member details' . $e], 500);
        }
    }

    public function checkTokenExists(Request $request)
    {
        $requestData = $request->all();

        $validator = Validator::make($requestData, [
            'token' => 'required',
        ], [
            'token.required' => 'The token is required.',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        } else {

            $token = $request->token;

            $member = ReachMember::where('password_reset_token', $token)->first();

            if (!$member) {
                return response()->json(['error' => 'Invalid token or token expired'], 422);
            } else {

                //return response()->json(['success' => true, 'message' => 'Valid token'], 200);
                // Check if the token has expired (e.g., 1 hour expiration)
                $expirationTime = 60; // Time in minutes
                $currentTime = now();
                $tokenGenerationTime = Carbon::parse($member->password_reset_time);

                if ($tokenGenerationTime && $tokenGenerationTime->diffInMinutes($currentTime) > $expirationTime) {
                    return response()->json(['error' => 'Token has expired'], 422);
                } else {
                    return response()->json(['success' => true, 'message' => 'Valid token'], 200);
                }
            }
        }
    }

    // public function makeSecondPayment(Request $request)
    // {
    //     $requestData = $request->all();
    //     try {
    //         // Validate request data
    //         $validator = Validator::make($requestData, [
    //             'payment_intent_id' => 'required|string',
    //             'amount' => 'required|numeric|min:1',
    //             'currency' => 'required|string',
    //         ]);

    //         if ($validator->fails()) {
    //             return [
    //                 'success' => false,
    //                 'message' => 'Validation failed.',
    //                 'errors' => $validator->errors(),
    //             ];
    //         }

    //         // Retrieve the PaymentIntent by ID
    //         $paymentIntentId = $requestData['payment_intent_id'];

    //         // $this->stripeconnect = new StripeConnect();
    //         $previousPaymentIntent = $this->stripeconnect->retrieve_payment_intent($paymentIntentId);

    //         if (!$previousPaymentIntent || $previousPaymentIntent['status'] !== 1) {
    //             return [
    //                 'success' => false,
    //                 'message' => 'Invalid PaymentIntent ID or PaymentIntent retrieval failed.',
    //             ];
    //         }

    //         $paymentMethodId = $previousPaymentIntent['payment_method'];

    //         // Create a new PaymentIntent for the second payment
    //         $newPaymentIntentData = [
    //             'amount' => $requestData['amount'] * 100,
    //             'currency' => strtolower($requestData['currency']),
    //             'customer' => $previousPaymentIntent['customer'],
    //             'payment_method' => $paymentMethodId, 
    //             'off_session' => true,
    //             'confirm' => true, 
    //             'description'  => 'Specialist Booking Fee',
    //             'metadata' => [
    //                 'payment_to' => $specialist->members_fname . " " . $specialist->members_lname,
    //                 'payment_from' => $member->members_fname . " " . $member->members_lname,
    //             ],
    //         ];

    //         $newPaymentIntent = $this->stripeconnect->create_payment_intent($newPaymentIntentData);

    //         if ($newPaymentIntent['status'] === 1) {
    //             return [
    //                 'success' => true,
    //                 'message' => 'Second payment successfully processed.',
    //                 'payment_intent_id' => $newPaymentIntent['id'],
    //                 'status' => $newPaymentIntent['status'],
    //             ];
    //         } else {
    //             return [
    //                 'success' => false,
    //                 'message' => 'Failed to process the second payment.',
    //                 'error' => $newPaymentIntent['error'] ?? 'Unknown error.',
    //             ];
    //         }

    //     } catch (\Exception $e) {
    //         // Handle general errors
    //         return [
    //             'success' => false,
    //             'message' => 'An error occurred while processing the payment.',
    //             'error' => $e->getMessage(),
    //         ];
    //     }
    // }
    public function updateRegistration(Request $request)
    {
        $memberId = $request->input('member_id');
        if (!$memberId) {
            return response()->json([
                'success' => false,
                'message' => 'Member ID is required'
            ], 400);
        }
        $member = ReachMember::where('id', $memberId)->first();

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Member not found'
            ], 404);
        }

        $memberArray = $member ? $member->toArray() : [];
        $requestData = array_merge($memberArray, [
            'stripeToken' => $request->input('stripeToken'),
            'subscription_plan' => $memberArray['members_subscription_plan'] ?? null,
        ]);
        unset($requestData['members_subscription_plan']);

        $startDate = new DateTime();
        $currentDate = new DateTime();
        if ($requestData['subscription_plan'] == "Annual") {
            $currentDate->modify('+1 year');
        } else {
            $currentDate->modify('+1 month');
        }




        $arrayData = [
            'members_subscription_plan' => $requestData['subscription_plan'],
            'members_subscription_start_date' => $startDate->format('Y-m-d'),
            'members_subscription_end_date' => $currentDate->format('Y-m-d'),
            'members_type' => 'M',
        ];


        try {

            //Process Stripe Payment
            $payment = $this->paymentCardDetails($requestData);

            if ($payment['success']) {

                if ($member) {
                    $member->update($arrayData);

                    // Check if the member already has a token
                    $existingToken = $member->tokens()->first();

                    if ($existingToken) {
                        $token = $request->bearerToken();
                    } else {
                        $token = $member->createToken('authToken')->plainTextToken;

                        if (isset($requestData['device_token']) && $requestData['device_token'] != '') {

                            $existingDevice = FcmNotification::where('token', $requestData['device_token'])->first();
                            if (!$existingDevice) {

                                $device_arr = [
                                    'member_id' => $member->id,
                                    'token' => $requestData['device_token'],
                                    'device_type' => $requestData['device_type'],
                                    'is_login' => 1,
                                ];
                                FcmNotification::create($device_arr);
                            } else {

                                $existingDevice->update([
                                    'member_id' => $member->id,
                                    'device_type' => $requestData['device_type'],
                                    'is_login' => 1,
                                ]);
                            }
                        }
                    }
                    //insert records to reach_transactions
                    $parent_transaction_id = 'TXN-' . strtoupper(Str::random(10));
                    $amount = $this->currencyService->getMembershipFee($requestData);
                    if (isset($requestData['referral_code'])) {
                        $referringUserId = ReachMember::where('referral_code', $requestData['referral_code'])->pluck('id')->first();
                        $payment_to = $referringUserId;
                    } else {
                        $payment_to = NULL;
                    }
                    $transactionRecord = [
                        "transaction_id" => $parent_transaction_id,
                        "payment_id" => $payment['transaction_id'],
                        "member_id" => $member->id,
                        "connected_member_id" => $payment_to,
                        "parent_transaction_id" => NULL,
                        "original_amount" => $amount['membership_fee'],
                        "reduced_amount" => $amount['discount_amount'],
                        "actual_amount" => $amount['actual_amount'],
                        "from_currency" => $requestData['currency'],
                        "to_currency" => $requestData['currency'],
                        "rate" => 1,
                        "payment_date" => date('Y-m-d H:i:s'),
                        "status" => "Completed",
                        "type" => "Membership",
                        "description" => 'New full membership registration with full access to features',
                        'transaction_type' => 'Debit'
                    ];

                    $reachtransaction = new ReachTransaction($transactionRecord);
                    $reachtransaction->save();
                    //end for reach_transactions
                    if (isset($requestData['referral_code'])) {
                        $referringUserId = ReachMember::where('referral_code', $requestData['referral_code'])->pluck('id')->first();
                        if ($referringUserId) {
                            /*$refferal = [
                                'member_id'=>$member->id,
                                'refferal_member_id'=>$referringUserId,
                                'refferal_code'=>$request->referral_code
                            ];
                            $refferal = ReachMemberRefferals::create($refferal);*/
                            $refferal = ReachMemberRefferals::updateOrCreate(
                                [
                                    // Search criteria to find an existing record
                                    'member_id' => $member->id,
                                    'refferal_member_id' => $referringUserId
                                ],
                                [
                                    // Fields to update or set if creating a new record
                                    'refferal_code' => $request->referral_code
                                ]
                            );

                            //for notification
                            $User = ReachMember::where('id', $member->id)->select('members_fname', 'members_lname')->first();
                            //print_r($referringUser);die();
                            $firstName = $User->members_fname;
                            $lastName = $User->members_lname;
                            $referringMemberName = $firstName . ' ' . $lastName;


                            $message = "You have a pending referral amount from {$referringMemberName}. It will be available to withdraw after one month.";

                            $url_keyword = 'MyReferral';
                            $to = $referringUserId;

                            $this->notificationService->new_notification('0', '0', $member->id, $to, $message, $url_keyword);
                            //end for notification
                            //insert records to reach_transactions
                            if ($requestData['subscription_plan'] == "Monthly") {
                                $discountAmount = $amount['actual_amount'];
                            } else {
                                $discountAmount = $amount['discount_amount'];
                            }
                            $transaction_id = 'TXN-' . strtoupper(Str::random(10));
                            $transactionRecord = [
                                "transaction_id" => $transaction_id,
                                "payment_id" => $payment['transaction_id'],
                                "member_id" => $referringUserId,
                                "connected_member_id" => $member->id,
                                "parent_transaction_id" => $parent_transaction_id,
                                "original_amount" => $discountAmount,
                                "reduced_amount" => NuLL,
                                "actual_amount" => $discountAmount,
                                "from_currency" => $requestData['currency'],
                                "to_currency" => $amount['parent_currency'],
                                "rate" => $this->currencyService->getCurrencyRate($requestData['currency'], $amount['parent_currency']),
                                "payment_date" => date('Y-m-d H:i:s'),
                                "status" => "Pending",
                                "type" => "Referral",
                                "description" => $message,
                                'transaction_type' => 'Credit'
                            ];

                            $reachtransaction = new ReachTransaction($transactionRecord);
                            $reachtransaction->save();
                            //end for reach_transactions
                        }
                    }

                    $this->subscribeToMailchimp($member, ['Reach-Member', 'Full-Member']);
                }
                // Update transaction
                $transaction = StripePaymentTransaction::find($payment['transaction_id']);
                if ($transaction) {
                    $transaction->update(['member_id' => $member->id]);
                }
                $currentServerTimezone = Carbon::now('Europe/London');
                $time = $currentServerTimezone->format('Y-m-d H:i:s');

                return response()->json(['success' => true, 'message' => 'OK', 'data' => ['Token' => $token, 'Member_id' => $member->id, 'Member_type' => 'M', 'Member_fullname' => $member->members_fname . ' ' . $member->members_lname, 'date_time' => $time, 'currency' => $member->currency]], 200);
            }
        } catch (\Exception $e) {
            // If an error occurs during creation, return an error response
            return response()->json(['error' => 'Failed to create member' . $e], 500);
        }
    }

    public function emailVerify(Request $request)
    {
        $requestData = $request->all();

        $validator = Validator::make($requestData, [
            'token' => 'required',
        ], [
            'token.required' => 'The token is required.',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        } else {

            $emailtoken = $request->token;

            $member = ReachMember::where('email_verify_token', $emailtoken)->first();

            if (!$member) {
                return response()->json(['error' => 'Invalid token or token expired'], 422);
            } else {
                $member->is_email_verified = 1;
                $member->email_verify_token = NULL;
                $member->members_status = 'A';
                $member->save();

                $isMobile = preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);

                // Create a new token for the current session
                $tokenName = $isMobile ? 'authToken_mobile' : 'authToken_web';

                $token = $member->createToken($tokenName)->plainTextToken;
                $currentServerTimezone = Carbon::now('Europe/London');
                $time = $currentServerTimezone->format('Y-m-d H:i:s');
                //, 'data' => ['Token' => $token, 'Member_id' => $member->id, 'Member_type' => 'F', 'Member_fullname' => $member->members_fname . ' ' . $member->members_lname, 'date_time' => $time, 'members_email' => $member->members_email]

                $emailTemplate = ReachEmailTemplate::where('template_type', 'free_registration')->first();

                // Prepare Email Subject and Body
                $result = "";
                $subject = $emailTemplate->template_subject;
                $body = $emailTemplate->template_message;
                $tags = explode(",", $emailTemplate->template_tags);
                $replace = [$member->members_fname];
                $body = str_replace($tags, $replace, $body);

                // Send Email to user
                $to = $member->members_email;
                $cc = [];
                //$bcc = $emailTemplate->template_bcc_address ? explode(',', $emailTemplate->template_bcc_address) : [];
                $bcc = [];
                $attachments = [];

                $mailchimpService = new MailchimpService();
                $mailchimpService->sendTemplateEmail($to, $body, $subject, NULL, $cc, $bcc, $attachments);

                return response()->json(['success' => true, 'message' => 'OK', 'tokenName' => $tokenName], 200);
            }
        }
    }

    public function resendEmail(Request $request)
    {
        $requestData = $request->all();

        $validator = Validator::make($requestData, [
            'email' => 'required',
        ], [
            'email.required' => 'Email ID is required.',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        } else {
            $member = ReachMember::where('members_email', $requestData['email'])
                ->select('is_email_verified', 'email_verify_token', 'members_fname', 'members_email', 'members_status')
                ->first();

            if ($member && $member->is_email_verified === 0 && $member->members_status === 'I') {
                $emailTemplate = ReachEmailTemplate::where('template_type', 'verify_email')->first();
                // Prepare Email Subject and Body
                $result = "";
                $subject = $emailTemplate->template_subject;
                $body = $emailTemplate->template_message;
                $tags = explode(",", $emailTemplate->template_tags);

                $link = config('site.url') . '/verify-email?token=' . $member->email_verify_token;
                $replace = [$member->members_fname, $link];
                $body = str_replace($tags, $replace, $body);
                // Send Email to user
                $to = $member->members_email;
                $cc = [];
                //$bcc = $emailTemplate->template_bcc_address ? explode(',', $emailTemplate->template_bcc_address) : [];
                $bcc = [];
                $attachments = [];
                $mailchimpService = new MailchimpService();
                $mailchimpService->sendTemplateEmail($to, $body, $subject, NULL, $cc, $bcc, $attachments);
                $currentServerTimezone = Carbon::now('Europe/London');
                $time = $currentServerTimezone->format('Y-m-d H:i:s');
                return response()->json(['success' => true, 'message' => 'OK', 'email' => $requestData['email'], 'name' => $member->members_fname], 200);
            } else {
                return response()->json(['success' => false, 'message' => 'Email Already Validated', 'email' => $requestData['email'], 'name' => $member->members_fname], 200);
            }
        }
    }
}
