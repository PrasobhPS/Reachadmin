<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Interview_schedule;
use App\Models\ReachJob;
use App\Models\ReachMember;
use App\Models\ReachEmployeeDetails;
use Carbon\Carbon;
use App\Services\NotificationService;
use App\Models\FcmNotification;
use App\Models\Specialist_call_schedule;
use Laravel\Sanctum\PersonalAccessToken;
use App\Services\CurrencyService;
use App\Models\CurrencyExchangeRates;
use App\Models\ReachMeetingParticipantHistory;
use App\Models\StripePaymentTransaction;
use App\Models\ReachTransaction;
use Stripe\Stripe;
use App\Models\MasterSetting;
use App\Models\StripeWithdrawalTransaction;
use App\Models\StripePaymentTransfer;
use Illuminate\Support\Str;
use App\Libraries\StripeConnect;
use App\Console\Commands\ProcessStripeSubscriptions;
use Illuminate\Support\Facades\Mail;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\ProcessStripeSubscriptions::class,

    ];

    /**
     * Define the application's command schedule.
     */


    protected function schedule(Schedule $schedule): void
    {
        //$schedule->command(ProcessStripeSubscriptions::class)->dailyAt('00:10');
        $schedule->command(ProcessStripeSubscriptions::class)->twiceDaily(1, 5);
        $schedule->call(function () {
            $notificationService = new NotificationService();
            $time = Carbon::now()->addMinutes(10)->format('H:i');
            $currentDate = Carbon::now()->format('Y-m-d');
            //whereRaw('DATE_FORMAT(interview_time, "%H:%i") = ?', [$time])

            $schedules = Interview_schedule::where('interview_date', $currentDate)
                ->whereRaw('DATE_FORMAT(interview_time, "%H:%i") = ?', [$time])
                ->where('interview_status', 'A')
                ->get()->toArray();

            foreach ($schedules as $schedule) {
                $member = ReachJob::select('member_id')
                    ->where('id', $schedule['job_id'])
                    ->first();

                $memberdetails = ReachMember::select('members_fname', 'members_lname')
                    ->where('id', $member['member_id'])
                    ->first();

                $jobmemberName = $memberdetails['members_fname'] . ' ' . $memberdetails['members_lname'];
                $employee = ReachEmployeeDetails::select('member_id')
                    ->where('employee_id', $schedule['employee_id'])
                    ->first();

                $empoyeememberdetails = ReachMember::select('members_fname', 'members_lname')
                    ->where('id', $employee['member_id'])
                    ->first();

                $fullName = $empoyeememberdetails['members_fname'] . ' ' . $empoyeememberdetails['members_lname'];

                $message = "You have an interview scheduled in 10 minutes with " . $fullName;
                $url_keyword = 'Interview';
                $to  = $member['member_id'];

                $notificationService->new_notification($schedule['employee_id'], $schedule['job_id'], '0', $to, $message, $url_keyword);

                //for job
                $message     = "You have an interview scheduled in 10 minutes with " . $jobmemberName;
                $url_keyword = 'Interview';
                $to          = $employee['member_id'];

                $notificationService->new_notification($schedule['employee_id'], $schedule['job_id'], '0', $to, $message, $url_keyword);
            }
        })->everyTenMinutes();
        //call scheduled in 10 minutes notification
        $schedule->call(function () {
            $notificationService = new NotificationService();
            $time = Carbon::now()->addMinutes(10)->format('H:i');
            $currentDate = Carbon::now()->format('Y-m-d');
            $bookacallSchedule = Specialist_call_schedule::where('call_scheduled_date', $currentDate)
                ->whereRaw('DATE_FORMAT(call_scheduled_time, "%H:%i") = ?', [$time])
                ->where('call_status', 'A')
                ->get()->toArray();

            foreach ($bookacallSchedule as $bookacall) {
                $to = $bookacall['specialist_id'];
                $memberdetails = ReachMember::select('members_fname', 'members_lname')
                    ->where('id', $bookacall['member_id'])
                    ->first();
                $memberName = $memberdetails['members_fname'] . ' ' . $memberdetails['members_lname'];
                $message = "You have a call scheduled in 10 minutes with " . $memberName;
                $url_keyword = 'Specialist';
                $notificationService->new_notification('0', '0', '0', $to, $message, $url_keyword);

                $to = $bookacall['member_id'];
                $specialistdetails = ReachMember::select('members_fname', 'members_lname')
                    ->where('id', $bookacall['specialist_id'])
                    ->first();
                $specialistName = $specialistdetails['members_fname'] . ' ' . $specialistdetails['members_lname'];
                $message = "You have a call scheduled in 10 minutes with " . $specialistName;
                $url_keyword = 'Member';
                $notificationService->new_notification('0', '0', '0', $to, $message, $url_keyword);
            }
        })->everyTenMinutes();
        //end call scheduled in 10 minutes notification
        //book a call notification 24 hour before
        $schedule->call(function () {

            $notificationService = new NotificationService();
            $currentDate = Carbon::now()->addDay()->format('Y-m-d');
            $time_24h = Carbon::now()->format('H:i');
            $bookacallSchedule24hour = Specialist_call_schedule::where('call_scheduled_date', $currentDate)
                ->whereRaw('DATE_FORMAT(call_scheduled_time, "%H:%i") = ?', [$time_24h])
                ->where('call_status', 'A')
                ->get()->toArray();

            foreach ($bookacallSchedule24hour as $bookacall24hour) {
                //  Mail::raw('You have a call scheduled in 24 hour with Specialist.', function ($message) {
                //     $message->to('soumyavinay.techmaven@gmail.com')
                //         ->subject('Cron Job Execution Notification');
                // });
                $to = $bookacall24hour['specialist_id'];
                $memberdetails = ReachMember::select('members_fname', 'members_lname')
                    ->where('id', $bookacall24hour['member_id'])
                    ->first();
                $memberName = $memberdetails['members_fname'] . ' ' . $memberdetails['members_lname'];
                $message = "You have a call scheduled in 24 hour with " . $memberName;
                $url_keyword = 'Specialist';
                $notificationService->new_notification('0', '0', '0', $to, $message, $url_keyword);

                $to = $bookacall24hour['member_id'];
                $specialistdetails = ReachMember::select('members_fname', 'members_lname')
                    ->where('id', $bookacall24hour['specialist_id'])
                    ->first();
                $specialistName = $specialistdetails['members_fname'] . ' ' . $specialistdetails['members_lname'];
                $message = "You have a call scheduled in 24 hour with " . $specialistName;
                $url_keyword = 'Member';
                $notificationService->new_notification('0', '0', '0', $to, $message, $url_keyword);
            }
        })->everyThirtyMinutes();
        //end book a call notification 24 hour before
        //call scheduled in 10 minutes notification
        $schedule->call(function () {

            $notificationService = new NotificationService();
            /*$currentDate = Carbon::now();  // Get current date and time
            $futureTime = $currentDate->subHours(1);

            $bookacallacceptnotification = Specialist_call_schedule::where('call_scheduled_date', '=', $futureTime->toDateString()) // Date check
                ->whereRaw('DATE_FORMAT(call_scheduled_time, "%H:%i") = ?', [$futureTime->format('H:i')]) // Time check
                ->whereIn('call_status', ['R', 'P'])
                ->get()->toarray();*/
            $currentDate = Carbon::now()->addDays(2)->format('Y-m-d');
            $time_48h = Carbon::now()->format('H:i');
            $bookacallacceptnotification = Specialist_call_schedule::where('call_scheduled_date', $currentDate)
                ->whereRaw('DATE_FORMAT(call_scheduled_time, "%H:%i") = ?', [$time_48h])
                ->whereIn('call_status', ['R', 'P'])
                ->get()->toArray();
            // print("<PRE>");print_r($bookacallacceptnotification);die();
            foreach ($bookacallacceptnotification as $bookacall1) {

                $to = $bookacall1['specialist_id'];

                // Mail::raw('The 48 hour cron job has started in dev.', function ($message) use ($bookacall1) {
                //     $message->to('soumyavinay.techmaven@gmail.com')
                //         ->subject('The 48 hour cron job has started in dev ' . $bookacall1['specialist_id']);
                // });
                $memberdetails = ReachMember::select('members_fname', 'members_lname')
                    ->where('id', $bookacall1['member_id'])
                    ->first();
                $memberName = $memberdetails['members_fname'] . ' ' . $memberdetails['members_lname'];
                $message = "You have a call scheduled in 48 hours; please accept or reschedule.";
                $url_keyword = 'Specialist';
                $notificationService->new_notification('0', '0', '0', $to, $message, $url_keyword);
            }
        })->everyThirtyMinutes();
        //for cancel subscriotion
        $schedule->call(function () {

            $currentDate = Carbon::now()->format('Y-m-d');
            $freeMember = ReachMember::where('members_subscription_end_date', $currentDate)
                ->where('subscription_status', 'I')
                ->get()->toArray();
            foreach ($freeMember as $member) {
                ReachMember::where('id', $member['id'])->update(['members_type' => 'F', 'is_specialist' => 'N']);
                PersonalAccessToken::where('tokenable_id', $member['id'])->delete();
                FcmNotification::where('member_id', $member['id'])->delete();
                // ReachMember::where('members_subscription_plan', NULL)->update(['members_type' => 'F']);
                $transaction = StripePaymentTransaction::where('member_id', $member['id'])
                    ->where('payment_type', 'membership')
                    ->orderBy('created_at', 'desc')
                    ->first();

                if ($transaction) {

                    $subscriptionId = $transaction->stripe_subscription_id;

                    // Initialize the Stripe connection
                    $this->stripeconnect = new StripeConnect();

                    // Retrieve the subscription details to check if it exists
                    $subscription = $this->stripeconnect->retrieve_subscription($subscriptionId);

                    if ($subscription['status'] === 1 && !empty($subscription['data'])) {

                        $cancellation = $this->stripeconnect->cancel_subscription($subscriptionId);

                        if ($cancellation['status'] === 1) {

                            $arrayData = ['subscription_status' => 'I'];
                            $member->update($arrayData);
                        }
                    }
                }
                $requestData['members_type'] = 'F';
                $requestData['is_specialist'] = 'N';
                \App\Libraries\SocketIO::sendMemberTypeUpdate($member['id'], $requestData['members_type'], $requestData['is_specialist']);
            }
        })->dailyAt('00:00'); // Runs every day at midnight (00:00)

        //end for subscription

        //for currency exchange
        $schedule->call(function () {

            $currencies = ['USD', 'GBP', 'EUR'];
            foreach ($currencies as $currency) {

                $this->currencyService = new CurrencyService();

                $rates = $this->currencyService->getExchangeRates($currency);
                // print("<PRE>");print_r( $rates );die();
                if ($rates) {
                    CurrencyExchangeRates::updateOrCreate(
                        ['currency_code' => $currency], // Match by currency code
                        [
                            'exchange_rate_to_usd' => $rates['USD'] ?? 0,
                            'exchange_rate_to_gbp' => $rates['GBP'] ?? 0,
                            'exchange_rate_to_eur' => $rates['EUR'] ?? 0,
                        ]
                    );
                }
            }
            // print("<PRE>");print_r($rates);die();
        })->dailyAt('00:00');
        //for end currency exchange
        //for  freeUpReservedBookings
        $schedule->call(function () {

            $reservationTimeout = now()->subMinutes(5);
            $expiredBookings = Specialist_call_schedule::where('booking_status', 'R')
                ->where('call_status', 'PA')
                ->where('created_at', '<=', $reservationTimeout)
                ->get();
            foreach ($expiredBookings as $booking) {
                Specialist_call_schedule::where('id', $booking['id'])->update(['booking_status' => 'L']);
            }
        })->everyMinute(); // end for freeUpReservedBookings

        //for  payment for specialist
        $schedule->call(function () {

            $currencyService = app(CurrencyService::class);
            $now = Carbon::now();

            $schedule = Specialist_call_schedule::where('call_status', 'A')
                ->where('booking_status', 'S')
                // ->where('id', '484')
                ->whereRaw(
                    "
        CONCAT(call_scheduled_date, ' ', ADDTIME(uk_scheduled_time, 
            SEC_TO_TIME(
                CASE 
                     WHEN timeSlot LIKE '%1h%' THEN 3600 
                    WHEN timeSlot LIKE '%30%' THEN 1800 
                    WHEN timeSlot LIKE '%1 h%' THEN 3600 
                    ELSE 0 
                END
            )
        )) <= ?",
                    [$now]
                )
                ->get();

            foreach ($schedule as $schedule) {
                // Mail::raw('The specialist payment cron job has started.', function ($message) {
                //     $message->to('soumyavinay.techmaven@gmail.com')
                //         ->subject('Cron Job Execution Notification');
                // });
                $meetingId = $schedule->meeting_id;
                $entries = ReachMeetingParticipantHistory::where('meeting_id', $meetingId)
                    ->whereNotNull('join_time')
                    //->whereNotNull('left_time')
                    ->get();

                if ($entries->count() === 2) {

                    $schedule->call_status = 'S';
                    $schedule->save();
                    $payment = StripePaymentTransaction::where('booking_id', $schedule['id'])
                        ->select('stripe_charge_id', 'payment_to', 'amount_paid', 'member_id', 'payment_to', 'payment_id', 'status')
                        ->first();



                    if ($payment && $payment->status === 'W') {

                        return response()->json([
                            'status' => 0,
                            'error' => 'Payment already processed for this booking, status is already Withdraw.'
                        ], 400);
                    } else {

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
                            $transaction  = $this->stripeconnect->transfer_amount($transfer_amount, $connected_account_id, $charge_id, $description,  $from_currency, $meta_data);

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
                                $transfer_receipt['exchange_rate']        = $exchange_rate;
                                $transfer_receipt['withdraw_id']          = $transfer->id;

                                $transfer = new StripePaymentTransfer($transfer_receipt);
                                $transfer->save();


                                $transaction_id = 'TXN-' . strtoupper(Str::random(10));
                                $parent_transaction_id =  ReachTransaction::where('payment_id', $payment['payment_id'])->value('transaction_id');
                                // $amount = $this->currencyService->getspecialistFee($requestData);
                                $this->currencyService = new CurrencyService();
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
                                //print("<PRE>");print_r($transactionRecord);die();

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
                    }
                }
            }
        })->everyThirtyMinutes();
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
    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
