<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use DateTime;
use DateTimeZone;

use App\Models\ReachMember;
use App\Models\ReachSpecialistVideos;
use App\Models\ReachSpecialist;
use App\Models\Specialist_call_schedule;
use App\Models\MasterSetting;

use App\Libraries\MailchimpService;
use App\Models\ReachEmailTemplate;

use Stripe\Stripe;
use Stripe\Charge;
use App\Libraries\StripeConnect;
use App\Models\StripePaymentTransaction;
use App\Models\SpecialistCallRate;
use App\Models\SpecialistUnavailableSchedules;
use App\Models\ReachWorkingHours;
use Carbon\Carbon;
use App\Models\ReachSitePage;
use App\Services\NotificationService;
use App\Models\FcmNotification;
use App\Services\CurrencyService;
use App\Models\ReachTransaction;
use App\Models\SpecialistRating;
use App\Helpers\FeeHelper;

class SpecialistsController extends Controller
{
	protected $notificationService;
	protected $stripeconnect;
	protected $currencyService;
	public function __construct(NotificationService $notificationService, CurrencyService $currencyService)
	{
		$this->notificationService = $notificationService;
		$this->currencyService = $currencyService;
	}


	public function getSpecialistsList(Request $request)
	{
		$member = Auth::guard('sanctum')->user();
		if (!$member) {
			$member = $request->user();
		}

		try {

			if ($member) {
				$specialistList = ReachMember::where('is_specialist', 'Y')
					->where('members_status', 'A')
					->where('id', '!=', $member->id)
					->select('id', 'members_fname', 'members_lname', 'members_employment', 'members_profile_picture', 'members_biography', \DB::raw('COUNT(reach_specialist_videos.video_id) as total_videos'))
					->leftJoin('reach_specialist_videos', function ($join) {
						$join->on('reach_specialist_videos.member_id', '=', 'reach_members.id')
							->whereNull('reach_specialist_videos.deleted_at');
					})
					->groupBy('reach_members.id', 'members_fname', 'members_lname', 'members_employment', 'members_profile_picture', 'members_biography')
					->get()
					->toArray();
			} else {
				$specialistList = ReachMember::where('is_specialist', 'Y')
					->where('members_status', 'A')
					->select('id', 'members_fname', 'members_lname', 'members_employment', 'members_profile_picture', 'members_biography', \DB::raw('COUNT(reach_specialist_videos.video_id) as total_videos'))
					->leftJoin('reach_specialist_videos', function ($join) {
						$join->on('reach_specialist_videos.member_id', '=', 'reach_members.id')
							->whereNull('reach_specialist_videos.deleted_at');
					})
					->groupBy('reach_members.id', 'members_fname', 'members_lname', 'members_employment', 'members_profile_picture', 'members_biography')
					->get()
					->toArray();
			}

			return response()->json([
				'success' => true,
				'message' => 'OK',
				'data' => $specialistList,
				'profilePath' => url('storage')
			], 200);
		} catch (\Exception $e) {

			return response()->json([
				'success' => false,
				'message' => 'An error occurred while fetching data',
				'error' => $e->getMessage()
			], 500);
		}
	}

	public function getSpecialistsVideos($id)
	{
		try {

			$specialistVideos = new \App\Models\ReachSpecialistVideos();
			$videos = $specialistVideos->getSpecialistsVideos($id);

			return response()->json([
				'success' => true,
				'message' => 'OK',
				'data' => $videos,
				'filePath' => url('storage')
			], 200);
		} catch (\Exception $e) {

			return response()->json([
				'success' => false,
				'message' => 'An error occurred while fetching data',
				'error' => $e->getMessage()
			], 500);
		}
	}

	public function getSpecialistsProfile($id)
	{

		try {

			$specialist = ReachMember::select('members_fname', 'members_lname', 'members_profile_picture', 'members_biography', 'members_employment', 'currency', 'reach_specialist_videos.video_file as latest_video', 'reach_specialist_videos.video_thumb as thumb_image', 'reach_specialist_call_rates.rate as specialist_call_rates')
				->leftJoin('reach_specialist_videos', function ($join) {
					$join->on('reach_specialist_videos.member_id', '=', 'reach_members.id')
						->whereNull('reach_specialist_videos.deleted_at')
						->whereRaw('reach_specialist_videos.video_id = 
	                        (SELECT video_id FROM reach_specialist_videos WHERE member_id = reach_members.id AND video_status = "A" AND deleted_at IS NULL ORDER BY video_id DESC LIMIT 1)');
				})
				->leftJoin('reach_specialist_call_rates', 'reach_specialist_call_rates.specialist_id', '=', 'reach_members.id') // Join for the call rate
				->find($id);

			if ($specialist) {
				if (!empty($specialist->specialist_call_rates)) {

					$specialist->specialist_call_rates = json_decode($specialist->specialist_call_rates, true);
				} else {

					$feeSettings = MasterSetting::select(
						'specialist_booking_fee',
						'specialist_booking_fee_half_hour',
						'specialist_booking_fee_extra',
						'specialist_booking_fee_euro',
						'specialist_booking_fee_half_hour_euro',
						'specialist_booking_fee_extra_euro',
						'specialist_booking_fee_dollar',
						'specialist_booking_fee_half_hour_dollar',
						'specialist_booking_fee_extra_dollar'
					)->find(1);

					if ($feeSettings) {
						// Set fallback fee rates
						$specialist->specialist_call_rates = [
							'extra_EUR' => $feeSettings->specialist_booking_fee_extra_euro,
							'one_EUR' => $feeSettings->specialist_booking_fee_euro,
							'half_EUR' => $feeSettings->specialist_booking_fee_half_hour_euro,
							'extra_USD' => $feeSettings->specialist_booking_fee_extra_dollar,
							'one_USD' => $feeSettings->specialist_booking_fee_dollar,
							'half_USD' => $feeSettings->specialist_booking_fee_half_hour_dollar,
							'extra_GBP' => $feeSettings->specialist_booking_fee_extra,
							'one_GBP' => $feeSettings->specialist_booking_fee,
							'half_GBP' => $feeSettings->specialist_booking_fee_half_hour,
						];
					}
				}
			}
			$sitepage = ReachSitePage::where('site_page_slug', 'experts')
				->where('site_page_status', 'A')
				->first();
			if ($sitepage) {
				$specialist->expert_call_title = $sitepage->expert_call_title;
				$specialist->expert_call_description = $sitepage->expert_call_description;
			}
			if (!$specialist) {
				return response()->json([
					'success' => false,
					'message' => 'Specialist not found'
				], 404);
			}
			return response()->json([
				'success' => true,
				'message' => 'OK',
				'data' => $specialist,
				'filePath' => url('storage')
			], 200);
		} catch (\Exception $e) {

			return response()->json([
				'success' => false,
				'message' => 'An error occurred while fetching data',
				'error' => $e->getMessage()
			], 500);
		}
	}

	public function bookACall(Request $request)
	{
		$requestData = $request->all();
		$booking_type = isset($requestData['type']) ? $requestData['type'] : 'Booking';
		$validator = Validator::make($requestData, [
			'specialist_id' => 'required',
			'call_scheduled_time' => 'required',
			'call_scheduled_date' => 'required',
			'timeSlot' => 'required',
		], [
			'specialist_id.required' => 'The specialist id is required.',
			'call_scheduled_time.required' => 'The schedule time is required.',
			'call_scheduled_date.required' => 'The schedule date is required.',
			'timeSlot' => 'TimeSlot is required.',
		]);

		if ($validator->fails()) {

			return response()->json(['error' => $validator->errors()->first()], 422);
		} else {

			$member = $request->user();

			$currency = $requestData['currency'];
			$mappedTimezone = $requestData['call_scheduled_timezone'];
			$call_scheduled_time = date("H:i", strtotime($requestData['call_scheduled_time']));

			// Convert the time to UK time
			$dateTime = new DateTime($requestData['call_scheduled_date'] . ' ' . $call_scheduled_time, new DateTimeZone($mappedTimezone));
			$dateTime->setTimezone(new DateTimeZone('Europe/London'));
			$ukScheduledTime = $dateTime->format('H:i:s');

			$feeSettings = MasterSetting::select(
				'specialist_booking_fee',
				'specialist_booking_fee_half_hour',
				'specialist_booking_fee_euro',
				'specialist_booking_fee_half_hour_euro',
				'specialist_booking_fee_dollar',
				'specialist_booking_fee_half_hour_dollar',
				'specialist_booking_fee_extra',
				'specialist_booking_fee_extra_euro',
				'specialist_booking_fee_extra_dollar'
			)->find(1);

			if ($currency === 'EUR') {
				$adminSetting['one_EUR'] = $feeSettings['specialist_booking_fee_euro'];
				$adminSetting['half_EUR'] = $feeSettings['specialist_booking_fee_half_hour_euro'];
				$adminSetting['extra_EUR'] = $feeSettings['specialist_booking_fee_extra_euro'];
			} else if ($currency === 'USD') {
				$adminSetting['one_USD'] = $feeSettings['specialist_booking_fee_dollar'];
				$adminSetting['half_USD'] = $feeSettings['specialist_booking_fee_half_hour_dollar'];
				$adminSetting['extra_USD'] = $feeSettings['specialist_booking_fee_extra_dollar'];
			} else {
				$adminSetting['one_GBP'] = $feeSettings['specialist_booking_fee'];
				$adminSetting['half_GBP'] = $feeSettings['specialist_booking_fee_half_hour'];
				$adminSetting['extra_GBP'] = $feeSettings['specialist_booking_fee_extra'];
			}

			$callRate = SpecialistCallRate::where('specialist_id', $requestData['specialist_id'])
				->select('rate')
				->orderBy('created_at', 'desc')
				->first();
			if ($callRate) {
				$rateArray = json_decode($callRate->rate, true);
			} else {
				$rateArray = []; // or set a default value
			}

			if ($requestData['timeSlot'] === '1hr' || $requestData['timeSlot'] === '1 hour') {
				$type = 'one_';
			} else {
				$type = 'half_';
			}

			if (!empty($requestData['meeting_id'])) {

				$parentSchedule = Specialist_call_schedule::where('member_id', $member->id)
					->where('meeting_id', $requestData['meeting_id'])
					->first();

				if ($parentSchedule) {
					$type = 'extra_';
				}
			}
			$rateIndex = $type . '' . $currency;
			$arrayData = [
				'call_scheduled_time' => $call_scheduled_time,
				'call_scheduled_date' => $requestData['call_scheduled_date'],
				'call_scheduled_timezone' => $mappedTimezone,
				'uk_scheduled_time' => $ukScheduledTime,
				//'call_fee' => ($rateArray && $rateArray[$rateIndex]) ? $rateArray[$rateIndex] : $adminSetting[$rateIndex],
				'timeSlot' => $requestData['timeSlot'],
				'call_status' => $requestData['call_status'],
				'booking_status' => 'S',
			];
			if (isset($requestData['call_scheduled_reason'])) {
				$arrayData['call_scheduled_reason'] = $requestData['call_scheduled_reason'];
			}
		}

		try {
			DB::beginTransaction();
			$parent_transaction_id = '';
			$scheduleExistsQuery = Specialist_call_schedule::whereIn('call_status', ['P', 'R', 'A', 'PA'])
				->whereNotIn('booking_status', ['F', 'L'])
				->where('specialist_id', $requestData['specialist_id'])
				->where('call_scheduled_date', $requestData['call_scheduled_date'])
				->where('uk_scheduled_time', $ukScheduledTime);

			if (isset($requestData['booking_id'])) {
				$scheduleExistsQuery->where('id', '!=', $requestData['booking_id']);
			}

			$scheduleExists = $scheduleExistsQuery->exists();

			if ($scheduleExists) {
				DB::rollBack();
				return response()->json(['error' => 'Call is already assigned for this date and time'], 500);
			}
			$existingBooking = Specialist_call_schedule::where('specialist_id', $requestData['specialist_id'])
				->whereNotIn('booking_status', ['F', 'L'])
				->whereIn('call_status', ['P', 'R', 'A', 'PA'])
				->where('call_scheduled_date', $requestData['call_scheduled_date'])
				->where('uk_scheduled_time', $ukScheduledTime)
				->lockForUpdate(); // Lock the record to prevent other transactions from reading/// Add the condition if booking_id is provided
			if (isset($requestData['booking_id'])) {
				$existingBooking->where('id', '!=', $requestData['booking_id']);
			}

			$existingBooking = $existingBooking->first();
			// Check if the time slot is already booked
			if ($existingBooking) {
				// If the booking exists, return an error response
				DB::rollBack(); // Rollback the transaction as no booking can be made
				return response()->json([
					'error' => 'The selected time slot is already booked for this specialist.'
				], 400);
			}

			if (isset($requestData['booking_id'])) {
				$arrayData['member_rearrange'] = $requestData['is_member'];
				$schedule = Specialist_call_schedule::find($requestData['booking_id']);
				if ($schedule) {
					$schedule->update($arrayData);
					DB::commit();
					//for notification
					$memberName = $member->members_fname . ' ' . $member->members_lname;
					$appointmentDate = Carbon::parse($requestData['call_scheduled_date'])->format('d/m/y');
					$time = date("H:i", strtotime($requestData['call_scheduled_time']));
					$message = "Your appointment with {$memberName} has been rescheduled to {$appointmentDate} at {$time}.";
					if ($requestData['is_member'] == '1') {
						$url_keyword = 'Specialist';
						$to = $schedule['specialist_id'];
					} else {
						$url_keyword = 'Member';
						$to = $schedule['member_id'];
					}
					$this->notificationService->new_notification('0', '0', $member->id, $to, $message, $url_keyword);
					//end for notification
					//Booking Email to member
					$emailTemplate = ReachEmailTemplate::where('template_type', 'booking_updates')->first();

					$result = "<ul style='list-style-type: none;padding: 0px;'>
		            			<li><strong>Booking ID:</strong> " . $schedule->call_booking_id . "</li>
		            			<li><strong>New Date:</strong> " . date("m-d-Y", strtotime($schedule->call_scheduled_date)) . "</li>
		            			<li><strong>New Time:</strong> " . date("h:i A", strtotime($schedule->uk_scheduled_time)) . " London (GMT)</li>
		            			<li><strong>Expert Name:</strong> " . $schedule->specialist->members_fname . " " . $schedule->specialist->members_lname . "</li>
		            		   </ul>";

					$subject = $emailTemplate->template_subject . $schedule->call_booking_id;
					$body = $emailTemplate->template_message;
					$tags = explode(",", $emailTemplate->template_tags);
					$replace = [$schedule->member->members_fname, $result];
					$body = str_replace($tags, $replace, $body);

					// Send Email to user
					$to = $schedule->member->members_email;
					$cc = [];
					//$bcc = $emailTemplate->template_bcc_address ? explode(',', $emailTemplate->template_bcc_address) : [];
					$bcc = [];

					$mailchimpService = new MailchimpService();
					$mailchimpService->sendTemplateEmail($to, $body, $subject, NULL, $cc, $bcc);

					//Booking Email to Expert
					$emailTemplate2 = ReachEmailTemplate::where('template_type', 'booking_updates_ex')->first();

					$result2 = "<ul style='list-style-type: none;padding: 0px;'>
		            			<li><strong>Booking ID:</strong> " . $schedule->call_booking_id . "</li>
		            			<li><strong>New Date:</strong> " . date("m-d-Y", strtotime($schedule->call_scheduled_date)) . "</li>
		            			<li><strong>New Time:</strong> " . date("h:i A", strtotime($schedule->uk_scheduled_time)) . " London (GMT)</li>
		            			<li><strong>Member Name:</strong> " . $schedule->member->members_fname . " " . $schedule->member->members_lname . "</li>
		            		   </ul>";

					$subject2 = $emailTemplate2->template_subject . $schedule->call_booking_id;
					$body2 = $emailTemplate2->template_message;
					$tags = explode(",", $emailTemplate2->template_tags);
					$replace = [$schedule->specialist->members_fname, $result2];
					$body2 = str_replace($tags, $replace, $body2);

					// Send Email to Expert
					$to2 = $schedule->specialist->members_email;
					$cc2 = [];
					$bcc2 = [];
					$mailchimpService->sendTemplateEmail($to2, $body2, $subject2, NULL, $cc2, $bcc2);
				} else {
					return response()->json(['error' => 'Booking not found'], 404);
				}
			} else {
				$arrayData['call_fee'] = ($rateArray && $rateArray[$rateIndex]) ? $rateArray[$rateIndex] : $adminSetting[$rateIndex];
				$arrayData['member_id'] = $member->id;
				$arrayData['specialist_id'] = $requestData['specialist_id'];
				// Find the parent schedule if meeting_id is provided
				// $arrayData['extended_parent_id'] = null;
				if (!empty($requestData['meeting_id'])) {

					$parentSchedule = Specialist_call_schedule::where('member_id', $member->id)
						->where('meeting_id', $requestData['meeting_id'])
						->first();

					if ($parentSchedule) {
						$arrayData['extended_parent_id'] = $parentSchedule->id;
					}
					$arrayData['meeting_id'] = $requestData['meeting_id'];
				} else {

					$meeting_id = Specialist_call_schedule::generateMeetingId();
					$arrayData['meeting_id'] = $meeting_id;
				}
				//Process Stripe Payment
				if ($booking_type == 'AutoBooking') {
					$payment = $this->bookingPaymentCardDetails($request);
				} else {
					$payment = $this->paymentCardDetails($request);
				}

				if ($payment['success']) {

					$schedule = Specialist_call_schedule::create($arrayData);
					$updateData['call_booking_id'] = 'RC' . date('Y') . '-000' . $schedule->id;
					$schedule->update($updateData);
					DB::commit();

					// Update transaction booking id
					$transaction = StripePaymentTransaction::find($payment['transaction_id']);
					if ($transaction) {
						$transaction->update(['booking_id' => $schedule->id]);

						$charge = Charge::retrieve($payment['charge_id']);
						$charge->metadata['booking_id'] = $updateData['call_booking_id'];
						$charge->save();
					}

					//insert records to reach_transactions
					$parent_transaction_id = 'TXN-' . strtoupper(Str::random(10));
					$amount = $this->currencyService->getspecialistFee($requestData, $member->id);

					$transactionRecord = [
						"transaction_id" => $parent_transaction_id,
						"payment_id" => $payment['transaction_id'],
						"member_id" => $member->id,
						"connected_member_id" => $requestData['specialist_id'],
						"parent_transaction_id" => NULL,
						"original_amount" => $amount['member_fee'],
						"reduced_amount" => 0,
						"actual_amount" => $amount['actual_amount'],
						"from_currency" => $requestData['currency'],
						"to_currency" => $requestData['currency'],
						"rate" => 1,
						"payment_date" => date('Y-m-d H:i:s'),
						"status" => "Completed",
						"type" => "Book A Call",
						"description" => 'Book A Call',
						'transaction_type' => 'Debit'
					];

					$reachtransaction = new ReachTransaction($transactionRecord);
					$reachtransaction->save();
					//end for reach_transactions

					//for notification
					$memberName = $member->members_fname . ' ' . $member->members_lname;
					$appointmentDate = Carbon::parse($requestData['call_scheduled_date'])->format('d/m/y');
					$appointmentTime = date("H:i", strtotime($requestData['call_scheduled_time']));
					$message = "{$memberName} has booked an appointment with you on {$appointmentDate} at {$appointmentTime}.";
					$url_keyword = 'Specialist';
					$this->notificationService->new_notification('0', '0', $member->id, $requestData['specialist_id'], $message, $url_keyword);
					//end for notification
					//Booking Email to member
					$emailTemplate = ReachEmailTemplate::where('template_type', 'booking_call')->first();

					$result = "<ul style='list-style-type: none;padding: 0px;'>
		            			<li><strong>Booking ID:</strong> " . $schedule->call_booking_id . "</li>
		            			<li><strong>Date:</strong> " . date("m-d-Y", strtotime($schedule->call_scheduled_date)) . "</li>
		            			<li><strong>Time:</strong> " . date("h:i A", strtotime($schedule->uk_scheduled_time)) . " London (GMT)</li>
		            			<li><strong>Expert Name:</strong> " . $schedule->specialist->members_fname . " " . $schedule->specialist->members_lname . "</li>
		            		   </ul>";

					$subject = $emailTemplate->template_subject . $schedule->call_booking_id;
					$body = $emailTemplate->template_message;
					$tags = explode(",", $emailTemplate->template_tags);
					$replace = [$schedule->member->members_fname, $result];
					$body = str_replace($tags, $replace, $body);

					// Send Email to user
					$to = $schedule->member->members_email;
					$cc = [];
					//$bcc = $emailTemplate->template_bcc_address ? explode(',', $emailTemplate->template_bcc_address) : [];
					$bcc = [];

					$mailchimpService = new MailchimpService();
					$mailchimpService->sendTemplateEmail($to, $body, $subject, NULL, $cc, $bcc);

					//Booking Email to Experts
					$emailTemplate2 = ReachEmailTemplate::where('template_type', 'booking_received')->first();

					$result2 = "<ul style='list-style-type: none;padding: 0px;'>
		            			<li><strong>Booking ID:</strong> " . $schedule->call_booking_id . "</li>
		            			<li><strong>New Date:</strong> " . date("m-d-Y", strtotime($schedule->call_scheduled_date)) . "</li>
		            			<li><strong>New Time:</strong> " . date("h:i A", strtotime($schedule->uk_scheduled_time)) . " London (GMT)</li>
		            		   </ul>";

					$member_dts = "<ul style='list-style-type: none;padding: 0px;'>
		            			<li><strong>Member Name:</strong> " . $schedule->member->members_fname . " " . $schedule->member->members_lname . "</li>
		            			<li><strong>Email Id:</strong> " . $schedule->member->members_email . "</li>
		            			<li><strong>Phone Number:</strong> " . $schedule->member->members_phone . "</li>
		            		   </ul>";

					$subject2 = $emailTemplate2->template_subject . $schedule->call_booking_id;
					$body2 = $emailTemplate2->template_message;
					$tags = explode(",", $emailTemplate2->template_tags);
					$replace = [$schedule->specialist->members_fname, $result2, $member_dts];
					$body2 = str_replace($tags, $replace, $body2);

					// Send Email to Expert
					$to2 = $schedule->specialist->members_email;
					$cc2 = [];
					$bcc2 = [];
					$mailchimpService->sendTemplateEmail($to2, $body2, $subject2, NULL, $cc2, $bcc2);
				} else {
					return response()->json(['error' => 'Your payment was declined. Please try again.'], 500);
				}
			}

			return response()->json(['success' => true, 'message' => isset($requestData['booking_id']) ? 'Booking updated successfully' : 'Booking created successfully', 'data' => ['Schedule_id' => $schedule->id, 'transactionId' => $parent_transaction_id, 'specialist_name' => $schedule->specialist->members_fname . " " . $schedule->specialist->members_lname]], 200);
		} catch (\Exception $e) {

			return response()->json(['error' => 'Failed to create schedule' . $e], 500);
		}
	}


	public function CallScheduleList(Request $request)
	{
		$requestData = $request->all();

		$validator = Validator::make($requestData, [
			'specialist_id' => 'required',
			'call_scheduled_date' => 'required',
		], [
			'specialist_id.required' => 'The specialist id is required.',
			'call_scheduled_date.required' => 'The schedule date is required.',
		]);

		if ($validator->fails()) {
			return response()->json(['error' => $validator->errors()->first()], 422);
		} else {

			try {
				$schedule = Specialist_call_schedule::whereIn('call_status', ['P', 'R', 'A'])
					->where('specialist_id', $requestData['specialist_id'])
					->where('call_scheduled_date', $requestData['call_scheduled_date'])
					->select('call_scheduled_time')
					->get()->toArray();
				return response()->json([
					'success' => true,
					'message' => 'OK',
					'data' => $schedule,
				], 200);
			} catch (\Exception $e) {

				return response()->json([
					'success' => false,
					'message' => 'An error occurred while fetching data',
					'error' => $e->getMessage()
				], 500);
			}
		}
	}

	public function bookingHistory(Request $request)
	{
		try {

			$member = $request->user();

			$stripe_url = "";
			$stripe_varify = false;

			if ($member->stripe_account_id == '') {
				$stripe_varify = false;
			} else {
				if ($member->stripe_account_id != '') {

					//Stripe account verification status
					$this->stripeconnect = new StripeConnect();
					$verification = $this->stripeconnect->checkAccountVerification($member->stripe_account_id);

					if ($verification['status'] === 1) {
						$stripe_varify = true;
						// $redirect_on_logout = "https://test1.reach.boats/profile";
						// $express_access_url_response = $this->stripeconnect->generate_login_link($member->stripe_account_id, $redirect_on_logout);
						// if ($express_access_url_response['status'] === 1) {
						// 	$stripe_url = $express_access_url_response['data']->url;
						// }
					} else {
						$stripe_varify = false;
						$delete_stripe_account = $this->stripeconnect->deleteOldStripeAccount($member->stripe_account_id);
						$member->update(['stripe_account_id' => '', 'stripe_account_url' => '']);
					}
				}
			}

			$history = Specialist_call_schedule::with(['member:id,members_fname,members_lname,members_employment,members_profile_picture,members_country,currency'])
				->withActiveMembers()
				->leftJoin('stripe_payment_transaction', 'stripe_payment_transaction.booking_id', '=', 'reach_member_specialist_call_schedule.id')
				->where('specialist_id', $member->id)
				->select(
					DB::raw('COALESCE(MAX(stripe_payment_transaction.specialist_amount), MIN(reach_member_specialist_call_schedule.call_fee)) as call_fee'),
					DB::raw('SUM(COALESCE(stripe_payment_transaction.specialist_amount, reach_member_specialist_call_schedule.call_fee)) as total_call_fee'),
					DB::raw('MIN(reach_member_specialist_call_schedule.id) as id'),
					DB::raw('MIN(specialist_id) as specialist_id'),
					DB::raw('MIN(timeSlot) as timeSlot'),
					DB::raw('MIN(call_scheduled_date) as call_scheduled_date'),
					DB::raw('MIN(uk_scheduled_time) as uk_scheduled_time'),
					DB::raw('MIN(call_status) as call_status'),
					DB::raw('MIN(reach_member_specialist_call_schedule.member_id) as member_id'),
					DB::raw('MIN(call_scheduled_reason) as call_scheduled_reason'),
					DB::raw('MIN(member_rearrange) as member_rearrange'),
					DB::raw("SUM(CASE WHEN timeSlot = '1 hour' OR timeSlot = '1hr' THEN 60 ELSE 30 END) as duration"),
					DB::raw('MIN(meeting_id) as meeting_id'),
					DB::raw('MIN(reach_member_specialist_call_schedule.created_at) as min_created_at') // Fix for ORDER BY
				)
				->groupBy('meeting_id')
				->orderBy('min_created_at', 'desc')
				//->orderBy('reach_member_specialist_call_schedule.created_at', 'desc')
				->get()
				->map(function ($schedule) {
					// $addminutes = 30;
					// if ($schedule->timeSlot === '1 hour' || $schedule->timeSlot === '1hr') {
					// 	$addminutes = 60;
					// }
					static $totalMinutes = 0; // Track total duration

					$addminutes = ($schedule->timeSlot === '1 hour' || $schedule->timeSlot === '1hr') ? 60 : 30;
					$totalMinutes += $addminutes; // Sum total minutes

					$currentDateTime = Carbon::now();
					$scheduledDateTime = Carbon::parse($schedule->call_scheduled_date . ' ' . $schedule->uk_scheduled_time)
						->addMinute($addminutes);

					$currentDateTime = Carbon::now(); // Get the current date and time
					$scheduledDateTime = Carbon::parse($schedule->call_scheduled_date . ' ' . $schedule->uk_scheduled_time)
						->addMinute($addminutes);

					// Update call_status based on the current time comparison
					if ($scheduledDateTime->lessThan($scheduledDateTime) && $schedule->call_status !== 'H') {
						$schedule->call_status = 'H';
					}

					return [
						'id' => $schedule->id,
						'call_scheduled_date' => $schedule->call_scheduled_date,
						'uk_scheduled_time' => $schedule->uk_scheduled_time,
						'timeSlot' => $schedule->timeSlot === '1 hour' ? '1hr' : $schedule->timeSlot,
						'call_fee' => number_format($schedule->total_call_fee, 2, '.', ''),
						'specialist_id' => $schedule->specialist_id,
						'member_id' => $schedule->member_id,
						'members_fname' => $schedule->member->members_fname ?? '',
						'members_lname' => $schedule->member->members_lname ?? '',
						'members_employment' => $schedule->member->members_employment ?? '',
						'members_profile_picture' => $schedule->member->members_profile_picture ?? '',
						'members_country' => $schedule->member->members_country ?? '',
						'call_status' => $schedule->call_status,
						'call_scheduled_reason' => $schedule->call_scheduled_reason,
						'meeting_link' => $schedule->meeting_id,
						'currency' => $schedule->member->currency ?? 'GBP',
						'duration' => (int) $schedule->duration // Format total duration
					];
				})
				->toArray();

			return response()->json([
				'success' => true,
				'message' => 'OK',
				'data' => $history,
				'filePath' => url('storage'),
				'stripe_url' => $stripe_url,
				'stripe_verified' => $stripe_varify,
			], 200);
		} catch (\Exception $e) {

			return response()->json([
				'success' => false,
				'message' => 'An error occurred while fetching data',
				'error' => $e->getMessage()
			], 500);
		}
	}
	// public function bookingHistory(Request $request)
	// {
	// 	try {

	// 		$member = $request->user();

	// 		$stripe_url = "";
	// 		$stripe_varify = false;

	// 		if ($member->stripe_account_id == '') {
	// 			$stripe_varify = false;
	// 		} else {
	// 			if ($member->stripe_account_id != '') {

	// 				//Stripe account verification status
	// 				$this->stripeconnect = new StripeConnect();
	// 				$verification = $this->stripeconnect->checkAccountVerification($member->stripe_account_id);

	// 				if ($verification['status'] === 1) {
	// 					$stripe_varify = true;
	// 					// $redirect_on_logout = "https://test1.reach.boats/profile";
	// 					// $express_access_url_response = $this->stripeconnect->generate_login_link($member->stripe_account_id, $redirect_on_logout);
	// 					// if ($express_access_url_response['status'] === 1) {
	// 					// 	$stripe_url = $express_access_url_response['data']->url;
	// 					// }
	// 				} else {
	// 					$stripe_varify = false;
	// 					$delete_stripe_account = $this->stripeconnect->deleteOldStripeAccount($member->stripe_account_id);
	// 					$member->update(['stripe_account_id' => '', 'stripe_account_url' => '']);
	// 				}
	// 			}
	// 		}

	// 		$history = Specialist_call_schedule::with(['member:id,members_fname,members_lname,members_employment,members_profile_picture,members_country,currency'])->withActiveMembers()
	// 			->leftJoin('stripe_payment_transaction', 'stripe_payment_transaction.booking_id', '=', 'reach_member_specialist_call_schedule.id')
	// 			//->where('call_status', $type)
	// 			->where('specialist_id', $member->id)
	// 			->select(
	// 				'id',
	// 				'specialist_id',
	// 				'timeSlot',
	// 				'call_scheduled_date',
	// 				'uk_scheduled_time',
	// 				'call_fee',
	// 				'call_status',
	// 				'reach_member_specialist_call_schedule.member_id',
	// 				'call_scheduled_reason',
	// 				'meeting_id',
	// 				'member_rearrange',
	// 				DB::raw('COALESCE(stripe_payment_transaction.specialist_amount, reach_member_specialist_call_schedule.call_fee) as call_fee')
	// 			)
	// 			->orderBy('reach_member_specialist_call_schedule.created_at', 'desc')
	// 			->get()
	// 			->map(function ($schedule) {
	// 				$addminutes = 30;
	// 				if ($schedule->timeSlot === '1 hour' || $schedule->timeSlot === '1hr') {
	// 					$addminutes = 60;
	// 				}

	// 				$currentDateTime = Carbon::now(); // Get the current date and time
	// 				$scheduledDateTime = Carbon::parse($schedule->call_scheduled_date . ' ' . $schedule->uk_scheduled_time)
	// 					->addMinute($addminutes);

	// 				// Update call_status based on the current time comparison
	// 				if ($scheduledDateTime->lessThan($currentDateTime) && $schedule->call_status !== 'H') {
	// 					$schedule->call_status = 'H';
	// 				}

	// 				return [
	// 					'id' => $schedule->id,
	// 					'call_scheduled_date' => $schedule->call_scheduled_date,
	// 					'uk_scheduled_time' => $schedule->uk_scheduled_time,
	// 					'timeSlot' => $schedule->timeSlot === '1 hour' ? '1hr' : $schedule->timeSlot,
	// 					'call_fee' => $schedule->call_fee,
	// 					'specialist_id' => $schedule->specialist_id,
	// 					'member_id' => $schedule->member_id,
	// 					'members_fname' => $schedule->member->members_fname ?? '',
	// 					'members_lname' => $schedule->member->members_lname ?? '',
	// 					'members_employment' => $schedule->member->members_employment ?? '',
	// 					'members_profile_picture' => $schedule->member->members_profile_picture ?? '',
	// 					'members_country' => $schedule->member->members_country ?? '',
	// 					'call_status' => $schedule->call_status,
	// 					'call_scheduled_reason' => $schedule->call_scheduled_reason,
	// 					'meeting_link' => $schedule->meeting_id,
	// 					'currency' => $schedule->member->currency ?? 'GBP',
	// 				];
	// 			})
	// 			->toArray();

	// 		return response()->json([
	// 			'success' => true,
	// 			'message' => 'OK',
	// 			'data' => $history,
	// 			'filePath' => url('storage'),
	// 			'stripe_url' => $stripe_url,
	// 			'stripe_verified' => $stripe_varify,
	// 		], 200);
	// 	} catch (\Exception $e) {

	// 		return response()->json([
	// 			'success' => false,
	// 			'message' => 'An error occurred while fetching data',
	// 			'error' => $e->getMessage()
	// 		], 500);
	// 	}
	// }

	public function cancelBooking(Request $request)
	{
		$member = $request->user();

		$requestData = $request->all();

		$validator = Validator::make($requestData, [
			'booking_id' => 'required',
		], [
			'booking_id.required' => 'The booking id is required.',
		]);

		if ($validator->fails()) {
			return response()->json(['error' => $validator->errors()->first()], 422);
		} else {

			try {
				$schedule = Specialist_call_schedule::with('member')->find($requestData['booking_id']);

				if ($schedule) {

					//Cancel Payment
					$transaction = StripePaymentTransaction::where('booking_id', $requestData['booking_id'])->where('refund_status', 0)->first();

					if ($transaction) {
						$refund_amount = $this->calculateRefundAmount($transaction, $schedule);
						$refund_amount = round($refund_amount, 2);



						$this->stripeconnect = new StripeConnect();
						if ($refund_amount > 0) {
							$refundResponse = $this->stripeconnect->refund_charge($transaction->stripe_charge_id, $refund_amount);

							if ($refundResponse['status'] === 1) {

								$transaction->update(['refund_status' => 1, 'refund_amount' => $refund_amount]);

								$schedule->call_status = 'C';
								$schedule->cancelled_by = $member->id;
								$schedule->cancelled_on = date("Y-m-d");
								$schedule->save();
								//for notification
								$memberName = $member->members_fname . ' ' . $member->members_lname;

								$appointmentDate = Carbon::parse($schedule->call_scheduled_date)->format('d/m/y');
								$message = "Your booking with {$memberName} on {$appointmentDate} has been Cancelled.";

								$url_keyword = 'Specialist';
								$to = $schedule['specialist_id'];

								$this->notificationService->new_notification('0', '0', $member->id, $to, $message, $url_keyword);
								//end for notification
								$transaction_id = 'TXN-' . strtoupper(Str::random(10));
								$parent_transaction_id = ReachTransaction::where('payment_id', $transaction['payment_id'])->value('transaction_id');
								$transaction = StripePaymentTransaction::where('payment_id', $transaction['payment_id'])->first();

								$transactionRecord = [
									"transaction_id" => $transaction_id,
									"payment_id" => $transaction['payment_id'],
									"member_id" => $member->id,
									"connected_member_id" => $schedule['specialist_id'],
									"parent_transaction_id" => $parent_transaction_id,
									"original_amount" => $transaction->amount_paid,
									"reduced_amount" => ($transaction->amount_paid - $refund_amount),
									"actual_amount" => $refund_amount,
									"from_currency" => $member->currency,
									"to_currency" => $member->currency,
									"rate" => $this->currencyService->getCurrencyRate($member->currency, $member->currency),
									"payment_date" => date('Y-m-d H:i:s'),
									"status" => "Completed",
									"type" => "Refunded",
									"description" => 'Expert fee Refunded',
									'transaction_type' => 'Credit'
								];

								$reachtransaction = new ReachTransaction($transactionRecord);
								$reachtransaction->save();
								//end for reach_transactions
								return response()->json(['success' => true, 'message' => 'Booking cancelled and payment refunded successfully'], 200);
							} else {
								return response()->json(['error' => 'Failed to process refund: ' . $refundResponse['msg']], 500);
							}
						} else {

							$schedule->call_status = 'C';
							$schedule->cancelled_by = $member->id;
							$schedule->cancelled_on = date("Y-m-d");
							$schedule->save();
							//for notification
							$memberName = $member->members_fname . ' ' . $member->members_lname;

							$appointmentDate = Carbon::parse($schedule->call_scheduled_date)->format('d/m/y');
							$message = "Your booking with {$memberName} on {$appointmentDate} has been Cancelled.";

							$url_keyword = 'Specialist';
							$to = $schedule['specialist_id'];

							$this->notificationService->new_notification('0', '0', $member->id, $to, $message, $url_keyword);
							//end for notification
							$transaction_id = 'TXN-' . strtoupper(Str::random(10));
							$parent_transaction_id = ReachTransaction::where('payment_id', $transaction['payment_id'])->value('transaction_id');
							$transaction = StripePaymentTransaction::where('payment_id', $transaction['payment_id'])->first();

							$transactionRecord = [
								"transaction_id" => $transaction_id,
								"payment_id" => $transaction['payment_id'],
								"member_id" => $member->id,
								"connected_member_id" => $schedule['specialist_id'],
								"parent_transaction_id" => $parent_transaction_id,
								"original_amount" => $transaction->amount_paid,
								"reduced_amount" => ($transaction->amount_paid - $refund_amount),
								"actual_amount" => $refund_amount,
								"from_currency" => $member->currency,
								"to_currency" => $member->currency,
								"rate" => $this->currencyService->getCurrencyRate($member->currency, $member->currency),
								"payment_date" => date('Y-m-d H:i:s'),
								"status" => "Completed",
								"type" => "Refunded",
								"description" => 'Expert fee Refunded',
								'transaction_type' => 'Credit'
							];

							$reachtransaction = new ReachTransaction($transactionRecord);
							$reachtransaction->save();
							//end for reach_transactions
							return response()->json(['success' => true, 'message' => 'Booking cancelled and payment refunded successfully'], 200);
						}
					} else {
						return response()->json(['error' => 'Payment transaction not found'], 404);
					}


					//Cancel Email to member
					$emailTemplate = ReachEmailTemplate::where('template_type', 'booking_cancelled')->first();

					$result = "";
					$subject = $emailTemplate->template_subject . $schedule->call_booking_id;
					$body = $emailTemplate->template_message;
					$tags = explode(",", $emailTemplate->template_tags);
					$replace = [$schedule->member->members_fname, $schedule->call_booking_id];
					$body = str_replace($tags, $replace, $body);

					// Send Email to user
					$to = $schedule->member->members_email;
					$cc = [];
					//$bcc = $emailTemplate->template_bcc_address ? explode(',', $emailTemplate->template_bcc_address) : [];
					$bcc = [];

					$mailchimpService = new MailchimpService();
					$mailchimpService->sendTemplateEmail($to, $body, $subject, NULL, $cc, $bcc);

					return response()->json([
						'success' => true,
						'message' => 'Booking cancelled successfully!',
					], 200);
				} else {
					return response()->json([
						'success' => false,
						'message' => 'Booking not found!',
						'error' => 'The booking with the given ID does not exist.'
					], 404);
				}
			} catch (\Exception $e) {

				return response()->json([
					'success' => false,
					'message' => 'An error occurred while cancelling the booking.',
					'error' => $e->getMessage()
				], 500);
			}
		}
	}

	public function acceptBooking(Request $request)
	{

		$requestData = $request->all();
		$member = $request->user();
		$validator = Validator::make($requestData, [
			'booking_id' => 'required',
		], [
			'booking_id.required' => 'The booking id is required.',
		]);

		if ($validator->fails()) {
			return response()->json(['error' => $validator->errors()->first()], 422);
		} else {

			try {

				$schedule = Specialist_call_schedule::find($requestData['booking_id']);

				if ($schedule) {

					if ($schedule['call_status'] == 'P' || $schedule['call_status'] == 'R') {

						$memberName = $member->members_fname . ' ' . $member->members_lname;
						$specialist = ReachMember::select('id', 'members_fname', 'members_lname')->find($schedule['specialist_id']);
						$specialistName = $specialist->members_fname . ' ' . $specialist->members_lname; // Specialist's full name
						$appointmentDate = Carbon::parse($schedule->call_scheduled_date)->format('d/m/y');
						$interviewTime = Carbon::createFromFormat('H:i:s', $schedule['uk_scheduled_time']);
						$formattedTime = $interviewTime->format('H:i');
						if ($schedule['call_status'] == 'P') {
							$url_keyword = 'Member';
							$to = $schedule['member_id'];
							$message = "Your booking with {$specialistName} on {$appointmentDate} at {$formattedTime} has been accepted.";
						} else if ($schedule['call_status'] == 'R') {
							$url_keyword = 'Specialist';
							$to = $schedule['specialist_id'];
							$message = "Your booking with {$memberName} on {$appointmentDate} at {$formattedTime} has been accepted.";
						}
						$schedule->call_status = 'A';
						$schedule->save();
						StripePaymentTransaction::where('booking_id', $requestData['booking_id'])->update(['status' => 'A']);
						//for notification
						$this->notificationService->new_notification('0', '0', $member->id, $to, $message, $url_keyword);
						//end for notification

						//Booking Email to member
						$emailTemplate = ReachEmailTemplate::where('template_type', 'booking_confirmation')->first();

						$result = "<ul style='list-style-type: none;padding: 0px;'>
		            			<li><strong>Booking ID:</strong> " . $schedule->call_booking_id . "</li>
		            			<li><strong>Date:</strong> " . date("m-d-Y", strtotime($schedule->call_scheduled_date)) . "</li>
		            			<li><strong>Time:</strong> " . date("h:i A", strtotime($schedule->uk_scheduled_time)) . " London (GMT)</li>
		            			<li><strong>Expert Name:</strong> " . $schedule->specialist->members_fname . " " . $schedule->specialist->members_lname . "</li>
		            		   </ul>";

						$subject = $emailTemplate->template_subject . $schedule->call_booking_id;
						$body = $emailTemplate->template_message;
						$tags = explode(",", $emailTemplate->template_tags);
						$replace = [$schedule->member->members_fname, $result];
						$body = str_replace($tags, $replace, $body);

						// Send Email to user
						$to = $schedule->member->members_email;
						$cc = [];
						//$bcc = $emailTemplate->template_bcc_address ? explode(',', $emailTemplate->template_bcc_address) : [];
						$bcc = [];

						$mailchimpService = new MailchimpService();
						$mailchimpService->sendTemplateEmail($to, $body, $subject, NULL, $cc, $bcc);

						return response()->json([
							'success' => true,
							'message' => 'Booking accepted successfully!',
						], 200);
					} else {
						return response()->json([
							'success' => false,
							'message' => 'An error occurred while accepting the booking.',
						], 404);
					}
				} else {
					return response()->json([
						'success' => false,
						'message' => 'Booking not found!',
						'error' => 'The booking with the given ID does not exist.'
					], 404);
				}
			} catch (\Exception $e) {

				return response()->json([
					'success' => false,
					'message' => 'An error occurred while accepting the booking.',
					'error' => $e->getMessage()
				], 500);
			}
		}
	}

	/*public function paymentCardDetails(Request $request)
			 {
				 $requestData = $request->all();


				 $requestData = $request->all();
				 $member = $request->user();
				 $specialist = ReachMember::select('id', 'members_fname', 'members_lname')->find($requestData['specialist_id']);

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
					 $this->stripeconnect = new StripeConnect();
					 $existingCustomer = $this->stripeconnect->customer_retrive_by_email($member->members_email);


					 if ($existingCustomer['status'] === 1 && !empty($existingCustomer['data'])) {
						 $customer = $existingCustomer['data'][0];

						 // Convert the stripeToken into a PaymentMethod
						 $paymentMethod = $this->stripeconnect->create_payment_method($request->stripeToken);
						 if ($paymentMethod['status'] === 0) {
							 return [
								 'success' => false,
								 'message' => 'Failed to create payment method.',
								 'error' => $paymentMethod['error'],
							 ];
						 }

						 // Attach the new card to the existing customer
						 $this->stripeconnect->attach_payment_method($customer['id'], $paymentMethod['data']['id']);
						 $paymentMethodId = $paymentMethod['data']['id'];
					 } else {

						 // Create a new customer
						 $customer_arr = [
							 'email' => $member->members_email,
							 'name' => $member->members_fname . " " . $member->members_lname,
							 'source' => $request->stripeToken,
							 "address" => [
								 'line1' => (isset($member->members_address)) ? $member->members_address : '',
								 'country' => (isset($member->members_country)) ? $member->members_country : '',
								 'city' => (isset($member->members_town)) ? $member->members_town : '',
								 'postal_code' => (isset($member->members_postcode)) ? $member->members_postcode : ''
							 ],
						 ];

						 $customerDts = $this->stripeconnect->create_customer($member->members_email, $customer_arr);
						 $customer = $customerDts['data'];

						 // Convert the stripeToken into a PaymentMethod
						 $paymentMethod = $this->stripeconnect->create_payment_method($request->stripeToken);
						 $paymentMethodId = $paymentMethod['data']['id'];
					 }

					 //$feeSettings = MasterSetting::select('specialist_booking_fee')->find(1);
					 $feeSettings = MasterSetting::select(
						 'specialist_booking_fee',
						 'specialist_booking_fee_half_hour',
						 'specialist_booking_fee_extra',
						 'specialist_booking_fee_euro',
						 'specialist_booking_fee_half_hour_euro',
						 'specialist_booking_fee_extra_euro',
						 'specialist_booking_fee_dollar',
						 'specialist_booking_fee_half_hour_dollar',
						 'specialist_booking_fee_extra_dollar'
					 )->find(1);

					 if ($requestData['currency'] === 'EUR') {
						 $adminSetting['one_EUR'] = $feeSettings['specialist_booking_fee_euro'];
						 $adminSetting['half_EUR'] = $feeSettings['specialist_booking_fee_half_hour_euro'];
						 $adminSetting['extra_EUR'] = $feeSettings['specialist_booking_fee_extra_euro'];
					 } else if ($requestData['currency'] === 'USD') {
						 $adminSetting['one_USD'] = $feeSettings['specialist_booking_fee_dollar'];
						 $adminSetting['half_USD'] = $feeSettings['specialist_booking_fee_half_hour_dollar'];
						 $adminSetting['extra_USD'] = $feeSettings['specialist_booking_fee_extra_dollar'];
					 } else {
						 $adminSetting['one_GBP'] = $feeSettings['specialist_booking_fee'];
						 $adminSetting['half_GBP'] = $feeSettings['specialist_booking_fee_half_hour'];
						 $adminSetting['extra_GBP'] = $feeSettings['specialist_booking_fee_extra'];
					 }

					 $callRate = SpecialistCallRate::where('specialist_id', $requestData['specialist_id'])
						 ->select('rate')
						 ->orderBy('created_at', 'desc')
						 ->first();
					 if ($callRate) {
						 $rateArray = json_decode($callRate->rate, true);
					 } else {
						 $rateArray = []; // or set a default value
					 }

					 if ($requestData['timeSlot'] === '1hr' || $requestData['timeSlot'] === '1 hour') {
						 $type = 'one_';
					 } else {
						 $type = 'half_';
					 }

					 if (!empty($requestData['meeting_id'])) {

						 $parentSchedule = Specialist_call_schedule::where('member_id', $member->id)
							 ->where('meeting_id', $requestData['meeting_id'])
							 ->first();

						 if ($parentSchedule) {
							 $type = 'extra_';
						 }
					 }


					 $rateIndex = $type . '' . $requestData['currency'];
					 // Create a PaymentIntent instead of directly charging the card
					 $paymentIntentData = [
						 'amount' => (($rateArray && $rateArray[$rateIndex]) ? $rateArray[$rateIndex] : $adminSetting[$rateIndex]) * 100,
						 'currency' => $requestData['currency'],
						 'customer' => $customer['id'],
						 'payment_method' => $paymentMethodId,
						 'off_session' => true,
						 'confirm' => true,
						 'description' => 'Specialist Booking Fee',
						 'metadata' => [
							 'payment_to' => $specialist->members_fname . " " . $specialist->members_lname,
							 'payment_from' => $member->members_fname . " " . $member->members_lname,
						 ],
					 ];

					 $paymentIntent = $this->stripeconnect->create_payment_intent($paymentIntentData);

					 if ($paymentIntent['status'] === 1) {
						 $paymentIntentData = $paymentIntent['data'];
						 $payment_intent_id = $paymentIntentData['id'];
						 $latest_charge_id = $paymentIntentData['latest_charge'];

						 // Retrieve the charge details
						 $charge = $this->stripeconnect->retrieve_charge($latest_charge_id);
						 if ($charge['status'] === 1) {
							 $chargeData = $charge['data'];
							 $charge_id = $chargeData['id'];
							 $last_4 = $chargeData['payment_method_details']['card']['last4'] ?? '';
							 $feeSettings = MasterSetting::select('reach_fee')->find(1);

							 $stripe_change = 3;
							 $service_fee = (($feeSettings['reach_fee'] + $stripe_change) / 100) * $chargeData['amount'] / 100;
							 $transfer_amounts = ($chargeData['amount'] / 100) - $service_fee;

							 $paymentRecord = [
								 "member_id" => $member->id,
								 "payment_to" => $specialist->id,
								 "stripe_payment_intend_id" => $payment_intent_id,
								 "stripe_charge_id" => $charge_id,
								 "amount_paid" => $chargeData['amount'] / 100,
								 "payment_date" => date("Y-m-d"),
								 "currency" => $requestData['currency'],

								 "charge_description" => $chargeData['description'],
								 "last_4" => $last_4,
								 "balance_transaction" => $chargeData['balance_transaction'],
								 "status" => "P",
								 "payment_type" => "bookcall",
								 "specialist_amount" => $transfer_amounts
							 ];
							 $transaction = new StripePaymentTransaction($paymentRecord);
							 $transaction->save();

							 return [
								 'success' => true,
								 'message' => 'Payment created successfully!',
								 'transaction_id' => $transaction->payment_id,
								 'charge_id' => $charge_id,
							 ];
						 } else {
							 return [
								 'success' => false,
								 'message' => 'Charge retrieval failed.',
							 ];
						 }
					 } else {

						 return [
							 'success' => false,
							 'message' => 'PaymentIntent creation failed.',
						 ];
					 }
				 } catch (\Exception $e) {

					 return [
						 'success' => false,
						 'message' => 'An error occurred while processing payment for booking.',
						 'error' => $e->getMessage()
					 ];
				 }
			 }*/

	public function getMeetingLink()
	{
		$url = 'https://meeting.yoursupdate.com/api/meeting/create';
		$token = '29|AndrAg5lHpLhZQwrrcdb43YWk6xAlBYpXXE43tgO';
		$data = ['subject' => 'REACH'];

		try {
			$response = Http::withToken($token)->post($url, $data);

			if ($response->successful()) {
				return $response->json();
			} else {
				return response()->json(['error' => 'Failed to create meeting'], $response->status());
			}
		} catch (\Exception $e) {
			return response()->json(['error' => $e->getMessage()], 500);
		}
	}

	public function setSpecialistCallRate(Request $request)
	{
		$member = $request->user();

		$validatedData = $request->validate([
			'rate' => 'required|array',
		]);
		//print_r($validatedData);die();
		$specialistCallRate = SpecialistCallRate::updateOrCreate(
			['specialist_id' => $member->id],
			['rate' => json_encode($validatedData['rate'])]
		);

		$message = $specialistCallRate->wasRecentlyCreated
			? 'Expert call rate created successfully'
			: 'Expert call rate updated successfully';
		return response()->json([
			'success' => true,
			'message' => $message,
			'data' => $specialistCallRate
		], 201);
	}
	public function getSpecialistCallRate(Request $request)
	{

		try {
			// Fetch the latest call rate
			$member = $request->user();
			$callRate = SpecialistCallRate::where('specialist_id', $member->id)
				->select('id', 'specialist_id', 'rate')
				->orderBy('created_at', 'desc')
				->first(); // Retrieve the latest entry

			if ($callRate) {
				return response()->json([
					"specialist_id" => $callRate->specialist_id,
					"rate" => json_decode($callRate->rate, true) // Decode the JSON string to array
				]);
			} else {
				$feeSettings = MasterSetting::select(
					'specialist_booking_fee',
					'specialist_booking_fee_half_hour',
					'specialist_booking_fee_extra',
					'specialist_booking_fee_euro',
					'specialist_booking_fee_half_hour_euro',
					'specialist_booking_fee_extra_euro',
					'specialist_booking_fee_dollar',
					'specialist_booking_fee_half_hour_dollar',
					'specialist_booking_fee_extra_dollar'
				)->find(1);
				if ($feeSettings) {
					// Format the fallback fee in the same way
					$specialist = [
						'extra_GBP' => $feeSettings->specialist_booking_fee_extra,
						'one_GBP' => $feeSettings->specialist_booking_fee,
						'half_GBP' => $feeSettings->specialist_booking_fee_half_hour,
						'extra_EUR' => $feeSettings->specialist_booking_fee_extra_euro,
						'one_EUR' => $feeSettings->specialist_booking_fee_euro,
						'half_EUR' => $feeSettings->specialist_booking_fee_half_hour_euro,
						'extra_USD' => $feeSettings->specialist_booking_fee_extra_dollar,
						'one_USD' => $feeSettings->specialist_booking_fee_dollar,
						'half_USD' => $feeSettings->specialist_booking_fee_half_hour_dollar,
					];
					return response()->json([
						"rate" => $specialist // Decode the JSON string to array
					]);
				}
			}
		} catch (\Exception $e) {
			return response()->json(
				[
					'success' => false,
					'message' => 'An error occurred while fetching data',
					'error' => $e->getMessage()
				],
				500
			);
		}
	}

	public function getAvailableTimeSlots(Request $request)
	{
		$member = $request->user();
		$loggedInMemberId = $member['id'];
		$requestData = $request->all();

		$validator = Validator::make($requestData, [
			'specialist_id' => 'required',
			'call_scheduled_date' => 'required',
			//'timeSlot' => 'required',
		], [
			'specialist_id.required' => 'The specialist id is required.',
			'call_scheduled_date.required' => 'The schedule date is required.',
			//'timeSlot.required' => 'The TimeSlot is required.',
		]);

		if ($validator->fails()) {
			return response()->json(['error' => $validator->errors()->first()], 422);
		} else {
			$memberId = $requestData['specialist_id'];
			$scheduledDate = $requestData['call_scheduled_date'];
			//$timeInterval = $requestData['timeSlot'];
			$timeInterval = !empty($requestData['timeSlot']) ? $requestData['timeSlot'] : '1hr';
			$currency = ReachMember::select('currency')->find($loggedInMemberId);
			$scheduleQuery = Specialist_call_schedule::where('call_scheduled_date', $scheduledDate)
				->where(function ($query) use ($memberId, $loggedInMemberId) {
					$query->where('specialist_id', $memberId)
						->orWhere('specialist_id', $loggedInMemberId)
						->orWhere('member_id', $loggedInMemberId)
						->orWhere('member_id', $memberId);
				})
				->where(function ($query) use ($loggedInMemberId) {
					// Check if logged-in user has a booking
					$query->where(function ($query) use ($loggedInMemberId) {
						$query->where('member_id', $loggedInMemberId)
							->whereIn('call_status', ['P', 'R', 'A'])
							->whereIn('booking_status', ['S']);
					})
						->orWhere(function ($query) use ($loggedInMemberId) {
							$query->where('member_id', '!=', $loggedInMemberId)
								->whereIn('call_status', ['PA', 'P', 'R', 'A'])
								->whereIn('booking_status', ['R', 'S']);
						});
				});

			$schedule = $scheduleQuery->get(['call_scheduled_time', 'timeSlot'])->toArray();

			$blockedSlots = [];

			foreach ($schedule as $slot) {
				$time = $slot['call_scheduled_time'];
				$timeSlot = $slot['timeSlot'];
				try {
					// Parse the scheduled time
					$startTime = Carbon::createFromFormat('H:i:s', $time);
					if ($timeInterval === '1hr' || $timeInterval === '1 hour') {
						if ($startTime->format('i') === '30') {
							$blockedSlots[] = $startTime->copy()->subMinutes(30)->format('H:i');
							$blockedSlots[] = $startTime->format('H:i');
						} else {
							$blockedSlots[] = $startTime->format('H:i');
							$blockedSlots[] = $startTime->copy()->addMinutes(30)->format('H:i');
						}
					} elseif ($timeInterval === '30min' || $timeInterval === '30 min') {
						$blockedSlots[] = $startTime->format('H:i');
					}
				} catch (\Exception $e) {
					error_log('Time format error: ' . $e->getMessage());
				}
			}

			$specialist_call_rates = SpecialistCallRate::where('specialist_id', $memberId)->first();
			//print("<PRE>");print_r($specialist_call_rates);die();

			$formattedRates = [
				'extra' => 0,
				'one' => 0,
				'half' => 0,
			];
			if (!empty($specialist_call_rates)) {
				$specialist_call_rates = json_decode($specialist_call_rates['rate'], true);
				if ($currency->currency === 'EUR') {
					$formattedRates['extra'] = (float) ($specialist_call_rates['extra_EUR'] ?? 0);
					$formattedRates['one'] = (float) ($specialist_call_rates['one_EUR'] ?? 0);
					$formattedRates['half'] = (float) ($specialist_call_rates['half_EUR'] ?? 0);
				} else if ($currency->currency === 'USD') {
					$formattedRates['extra'] = (float) ($specialist_call_rates['extra_USD'] ?? 0);
					$formattedRates['one'] = (float) ($specialist_call_rates['one_USD'] ?? 0);
					$formattedRates['half'] = (float) ($specialist_call_rates['half_USD'] ?? 0);
				} else {
					$formattedRates['extra'] = (float) ($specialist_call_rates['extra_GBP'] ?? 0);
					$formattedRates['one'] = (float) ($specialist_call_rates['one_GBP'] ?? 0);
					$formattedRates['half'] = (float) ($specialist_call_rates['half_GBP'] ?? 0);
				}
			} else {
				$feeSettings = MasterSetting::select(
					'specialist_booking_fee',
					'specialist_booking_fee_half_hour',
					'specialist_booking_fee_extra',
					'specialist_booking_fee_euro',
					'specialist_booking_fee_half_hour_euro',
					'specialist_booking_fee_extra_euro',
					'specialist_booking_fee_dollar',
					'specialist_booking_fee_half_hour_dollar',
					'specialist_booking_fee_extra_dollar'
				)->find(1);
				if ($feeSettings) {
					// Format the fallback fee in the same way
					if ($currency->currency === 'EUR') {
						$formattedRates['extra'] = (float) ($feeSettings->specialist_booking_fee_extra_euro);
						$formattedRates['one'] = (float) ($feeSettings->specialist_booking_fee_euro);
						$formattedRates['half'] = (float) ($feeSettings->specialist_booking_fee_half_hour_euro);
					} else if ($currency->currency === 'USD') {
						$formattedRates['extra'] = (float) ($feeSettings->specialist_booking_fee_extra_dollar);
						$formattedRates['one'] = (float) ($feeSettings->specialist_booking_fee_dollar);
						$formattedRates['half'] = (float) ($feeSettings->specialist_booking_fee_half_hour_dollar);
					} else {
						$formattedRates['extra'] = (float) ($feeSettings->specialist_booking_fee_extra);
						$formattedRates['one'] = (float) ($feeSettings->specialist_booking_fee);
						$formattedRates['half'] = (float) ($feeSettings->specialist_booking_fee_half_hour);
					}
				}
			}
			$workingHours = ReachWorkingHours::where('member_id', $memberId)->get();

			// Get unavailable schedules for the member on the scheduled date
			$unavailableSchedules = SpecialistUnavailableSchedules::where('member_id', $memberId)
				->where('call_unavailable_date', $scheduledDate)
				->first();

			// Prepare available time slots
			$availableTimeSlots = [];
			$unavailableTimes = [];

			// Collect unavailable times if any
			if ($unavailableSchedules) {
				$unavailableTimes = json_decode($unavailableSchedules->unavailable_time, true);
			}

			if ($workingHours->isNotEmpty()) {
				foreach ($workingHours as $entry) {
					$days = json_decode($entry->days);
					$times = json_decode($entry->working_hours);

					// Check if the scheduled date matches the working days
					if (in_array(date('D', strtotime($scheduledDate)), $days)) {
						foreach ($times as $time) {
							// Split the time range into start and end times
							[$startTime, $endTime] = explode('-', $time);
							$startTime = trim($startTime);
							$endTime = trim($endTime);

							// Convert to timestamps for easier manipulation
							$startTimestamp = strtotime($startTime);
							$endTimestamp = strtotime($endTime);

							// Create 30-minute increments
							while ($startTimestamp < $endTimestamp) {
								$nextTimestamp = $startTimestamp + (30 * 60); // Add 30 minutes
								$timeSlotStart = date('H:i', $startTimestamp);
								$timeSlotEnd = date('H:i', $nextTimestamp);

								// Check if the time slot overlaps with any unavailable time slots
								$isUnavailable = false;
								foreach ($unavailableTimes as $unavailableTime) {
									// Check if the current time slot overlaps with the unavailable time
									if ($this->isTimeSlotOverlapping("$timeSlotStart-$timeSlotEnd", $unavailableTime)) {
										$isUnavailable = true;
										break;
									}
								}

								// Only add to available time slots if not unavailable
								if (!$isUnavailable) {
									$availableTimeSlots[] = $timeSlotStart;
								}

								$startTimestamp = $nextTimestamp;
							}
						}
					}
				}
			} else {
				// Set default time slots for that day if no working hours are available
				$availableTimeSlots = [];
				// Set default time range
				$defaultStart = '09:00 AM';
				$defaultEnd = '06:00 PM';
				// Convert to timestamps for easier manipulation
				$startTimestamp = strtotime($defaultStart);
				$endTimestamp = strtotime($defaultEnd);
				// Create 30-minute increments for default time slots
				while ($startTimestamp < $endTimestamp) {
					$timeSlot = date('H:i ', $startTimestamp);
					$availableTimeSlots[] = $timeSlot; // Add to available time slots
					$startTimestamp += (30 * 60); // Increment by 30 minutes
				}
			}

			return response()->json([
				'status' => 'success',
				'member_id' => $memberId,
				'scheduled_date' => $scheduledDate,
				'available_time_slots' => $availableTimeSlots,
				'schedule_time' => $blockedSlots,
				'call_rates' => $formattedRates,
				'currency' => $currency->currency,
			]);
		}
	}

	private function loggedInUserHasBooking($loggedInMemberId)
	{
		return Specialist_call_schedule::where('member_id', $loggedInMemberId)
			->whereIn('call_status', ['P', 'R', 'A'])
			->exists(); // returns true if any such booking exists
	}


	/**
	 * Check if two time slots overlap.
	 */
	private function isTimeSlotOverlapping($timeSlot, $unavailableTime)
	{
		// Split the time slots into start and end times
		[$start, $end] = explode('-', $timeSlot);
		[$unavailableStart, $unavailableEnd] = explode('-', $unavailableTime);

		// Convert to timestamps for comparison
		$startTimestamp = strtotime(trim($start));
		$endTimestamp = strtotime(trim($end));
		$unavailableStartTimestamp = strtotime(trim($unavailableStart));
		$unavailableEndTimestamp = strtotime(trim($unavailableEnd));

		// Check for overlap
		return ($startTimestamp < $unavailableEndTimestamp && $endTimestamp > $unavailableStartTimestamp);
	}
	protected function calculateRefundAmount($transaction, $schedule)
	{
		// Retrieve the scheduled call time

		$scheduledDateTime = Carbon::parse($schedule->call_scheduled_date . ' ' . $schedule->call_scheduled_time);

		// Calculate the time difference between now and the scheduled time
		$currentTime = Carbon::now();
		$hoursDifference = $currentTime->diffInHours($scheduledDateTime, false);
		// Retrieve cancellation fees from settings
		$feeSettings = MasterSetting::select('member_cancel_fee', 'specialist_cancel_fee')->find(1);

		// Check if the cancellation is 48 hours before or after the scheduled call
		if ($hoursDifference >= 48) {
			// If more than 48 hours before the scheduled time, full refund
			return $transaction->amount_paid * ((100 - $feeSettings['member_cancel_fee']) / 100);
		} else {
			// If within 48 hours, apply the member cancellation fee
			return 0;
		}
	}
	public function test_notification()
	{

		$this->notificationService->new_notification('0', '0', 186, 147, 'test', 'Specialist');
	}

	public function specialistRating(Request $request)
	{
		$validated = $request->validate([
			'meeting_id' => 'required',
			'rating' => [
				'required',
				'numeric',
				'min:0.5',
				'max:5',
				'regex:/^[0-5](\.[05])?$/',
			],
			'review' => 'nullable|string',
		]);

		// Fetch the member and meeting ID
		$member = $request->user();
		$meetingId = $validated['meeting_id'];

		// Retrieve the schedule using the meeting ID
		$schedule = Specialist_call_schedule::where('meeting_id', $meetingId)
			->where('member_id', $member->id)
			->first();

		if (!$schedule) {
			return response()->json([
				'message' => 'Invalid meeting ID or schedule not found.',
			], 404);
		}

		// Prepare data for creating or updating the rating
		$data = [
			'specialist_id' => $schedule->specialist_id,
			'member_id' => $member->id,
		];

		$ratingData = [
			'rating' => $validated['rating'],
			'review' => $validated['review'],
		];

		// Create or update the rating
		$rating = SpecialistRating::updateOrCreate($data, $ratingData);

		return response()->json([
			'message' => 'Rating submitted successfully.',
			'data' => $rating,
		], 201);
	}


	public function specialistAlreadyRated(Request $request)
	{
		$member = $request->user();
		$meetingId = $request->input('meeting_id');

		// Fetch the parent schedule
		$parentSchedule = Specialist_call_schedule::where('member_id', $member->id)
			->where('meeting_id', $meetingId)
			->first();

		if (!$parentSchedule) {
			return response()->json([
				'success' => false,
				'message' => 'Schedule not found for the provided meeting ID.',
			], 404);
		}

		$specialistId = $parentSchedule->specialist_id;

		// Check if the specialist is already rated
		$alreadyRated = SpecialistRating::where('specialist_id', $specialistId)->where('member_id', $member->id)->exists();

		return response()->json([
			'success' => $alreadyRated,
			'message' => $alreadyRated ? 'Specialist already rated' : 'Specialist not rated yet',
		], 200);
	}

	public function getAllRatings($id)
	{
		try {
			//$ratings = SpecialistRating::with('member')->where('specialist_id', $id)->get();
			$ratings = SpecialistRating::where('specialist_id', $id)
				->with(['member:id,members_fname,members_lname,members_profile_picture']) // Assuming there is a `member` relation defined in the `SpecialistRating` model
				->orderBy('created_at', 'desc') // Order by the most recent ratings
				->get()
				->map(function ($rating) {
					// Format the created_at date
					$rating->date = Carbon::parse($rating->created_at)->format('d/m/Y'); // Format the date
					return $rating;
				});

			$averageRating = SpecialistRating::where('specialist_id', $id)->avg('rating');
			return response()->json([
				'success' => true,
				'message' => 'OK',
				'data' => $ratings,
				'averageRating' => $averageRating ? round($averageRating * 2) / 2 : 0,
			], 200);
		} catch (\Exception $e) {

			return response()->json([
				'success' => false,
				'message' => 'An error occurred while fetching data',
				'error' => $e->getMessage()
			], 500);
		}
	}

	public function bookingPaymentCardDetails(Request $request)
	{

		$requestData = $request->all();

		$member_details = ReachMember::select('id', 'members_fname', 'members_lname', 'currency')->find($requestData['member_id']);
		$member = $request->user() ?? $member_details;

		//$member = $request->user();

		$stripePaymentIntendId = StripePaymentTransaction::where('member_id', $member->id)
			->pluck('stripe_payment_intend_id')
			->last();
		if (is_null($stripePaymentIntendId)) {
			return response()->json([
				'success' => false,
				'message' => 'Stripe payment intend ID not found for this member.'
			], 200);
		}

		$specialist = ReachMember::select('id', 'members_fname', 'members_lname')->find($requestData['specialist_id']);


		try {

			$this->stripeconnect = new StripeConnect();
			$previousPaymentIntent = $this->stripeconnect->retrieve_payment_intent($stripePaymentIntendId);
			print_R($previousPaymentIntent);
			die();
			if (!$previousPaymentIntent || $previousPaymentIntent['status'] !== 1) {
				return [
					'success' => false,
					'message' => 'Invalid PaymentIntent ID or PaymentIntent retrieval failed.',
				];
			}

			$paymentMethodId = $previousPaymentIntent['payment_method'];
			$requestData['currency'] = $requestData['currency'] ?? $member->currency;
			$fee_setting = FeeHelper::getFeeSettingsAndRates($requestData['currency'], $requestData['specialist_id']);

			if ($requestData['timeSlot'] === '1hr' || $requestData['timeSlot'] === '1 hour') {
				$type = 'one_';
			} else {
				$type = 'half_';
			}

			if (!empty($requestData['meeting_id'])) {

				$parentSchedule = Specialist_call_schedule::where('member_id', $member->id)
					->where('meeting_id', $requestData['meeting_id'])
					->first();

				if ($parentSchedule) {
					$type = 'extra_';
				}
			}


			$rateIndex = $type . '' . $requestData['currency'];
			// Create a PaymentIntent instead of directly charging the card
			$paymentIntentData = [
				//'amount' => (($fee_setting['rateArray ']&& $fee_setting['rateArray'][$rateIndex]) ? $fee_setting['rateArray'][$rateIndex] : $fee_setting['adminSetting'][$rateIndex]) * 100,
				'amount' => (($fee_setting['rateArray'] && $fee_setting['rateArray'][$rateIndex]) ? $fee_setting['rateArray'][$rateIndex] : $fee_setting['adminSetting'][$rateIndex]) * 100,
				'currency' => $requestData['currency'],
				'customer' => $previousPaymentIntent['customer'],
				'payment_method' => $paymentMethodId,
				'off_session' => true,
				'confirm' => true,
				'description' => 'Specialist Booking Fee',
				'metadata' => [
					'payment_to' => $specialist->members_fname . " " . $specialist->members_lname,
					'payment_from' => $member->members_fname . " " . $member->members_lname,

				],
			];


			$paymentIntent = $this->stripeconnect->create_payment_intent($paymentIntentData);

			if ($paymentIntent['status'] === 1) {

				$paymentIntentData = $paymentIntent['data'];
				$payment_intent_id = $paymentIntentData['id'];
				$latest_charge_id = $paymentIntentData['latest_charge'];

				// Retrieve the charge details
				$charge = $this->stripeconnect->retrieve_charge($latest_charge_id);

				if ($charge['status'] === 1) {
					$chargeData = $charge['data'];
					$charge_id = $chargeData['id'];
					$last_4 = $chargeData['payment_method_details']['card']['last4'] ?? '';
					$feeSettings = MasterSetting::select('reach_fee')->find(1);

					$stripe_change = 3;
					$service_fee = (($feeSettings['reach_fee'] + $stripe_change) / 100) * $chargeData['amount'] / 100;
					$transfer_amounts = ($chargeData['amount'] / 100) - $service_fee;
					$bookingId = isset($requestData['booking_id']) ? $requestData['booking_id'] : null;
					$paymentRecord = [
						"member_id" => $member->id,
						"payment_to" => $specialist->id,
						"stripe_payment_intend_id" => $payment_intent_id,
						"stripe_charge_id" => $charge_id,
						"amount_paid" => $chargeData['amount'] / 100,
						"payment_date" => date("Y-m-d"),
						"currency" => $requestData['currency'],
						"booking_id" => $bookingId,
						"charge_description" => $chargeData['description'],
						"last_4" => $last_4,
						"balance_transaction" => $chargeData['balance_transaction'],
						"status" => "P",
						"payment_type" => "bookcall",
						"specialist_amount" => $transfer_amounts,
					];


					$transaction = new StripePaymentTransaction($paymentRecord);
					$transaction->save();

					return [
						'success' => true,
						'message' => 'Payment created successfully!',
						'transaction_id' => $transaction->payment_id,
						'charge_id' => $charge_id,
					];
				} else {
					return [
						'success' => false,
						'message' => 'Charge retrieval failed.',
					];
				}
			} else {

				return [
					'success' => false,
					'message' => 'PaymentIntent creation failed.',
				];
			}
		} catch (\Exception $e) {

			return [
				'success' => false,
				'message' => 'An error occurred while processing payment for booking.',
				'error' => $e->getMessage()
			];
		}
	}
	public function reserveaCall(Request $request)
	{
		$requestData = $request->all();
		//print_r($requestData);die();
		$booking_type = isset($requestData['type']) ? $requestData['type'] : 'Booking';
		$validator = Validator::make($requestData, [
			'specialist_id' => 'required',
			'call_scheduled_time' => 'required',
			'call_scheduled_date' => 'required',
			'timeSlot' => 'required',
		], [
			'specialist_id.required' => 'The specialist id is required.',
			'call_scheduled_time.required' => 'The schedule time is required.',
			'call_scheduled_date.required' => 'The schedule date is required.',
			'timeSlot' => 'TimeSlot is required.',
		]);

		if ($validator->fails()) {

			return response()->json(['error' => $validator->errors()->first()], 422);
		} else {
			$member = $request->user();
			$currency = $requestData['currency'];
			$mappedTimezone = $requestData['call_scheduled_timezone'];
			$call_scheduled_time = date("H:i", strtotime($requestData['call_scheduled_time']));

			// Convert the time to UK time
			$dateTime = new DateTime($requestData['call_scheduled_date'] . ' ' . $call_scheduled_time, new DateTimeZone($mappedTimezone));
			$dateTime->setTimezone(new DateTimeZone('Europe/London'));
			$ukScheduledTime = $dateTime->format('H:i:s');
			$specialistId = $requestData['specialist_id'];




			//checking already exists
			$scheduleExistsQuerymember = Specialist_call_schedule::whereIn('call_status', ['PA'])
				->where('booking_status', 'R')
				->where('specialist_id', $requestData['specialist_id'])
				//->where('call_scheduled_date', $requestData['call_scheduled_date'])
				//->where('uk_scheduled_time', $ukScheduledTime)
				->where('member_id', $member->id);
			$scheduleChks = $scheduleExistsQuerymember->first();

			if ($scheduleChks) {
				$schedule = Specialist_call_schedule::find($scheduleChks->id);
				if ($schedule) {
					$schedule->call_scheduled_date = $requestData['call_scheduled_date'];
					$schedule->call_scheduled_time = $call_scheduled_time;
					$schedule->uk_scheduled_time = $call_scheduled_time;
					$schedule->save();
				}
				return [
					'success' => true,
					'message' => 'Booking reserverd successfully!',
					'booking_id' => $scheduleChks->id,

				];
			} else {

				$fee_setting = FeeHelper::getFeeSettingsAndRates($currency, $specialistId);

				if ($requestData['timeSlot'] === '1hr' || $requestData['timeSlot'] === '1 hour') {
					$type = 'one_';
				} else {
					$type = 'half_';
				}


				$rateIndex = $type . '' . $currency;
				$arrayData = [
					'call_scheduled_time' => $call_scheduled_time,
					'call_scheduled_date' => $requestData['call_scheduled_date'],
					'call_scheduled_timezone' => $mappedTimezone,
					'uk_scheduled_time' => $ukScheduledTime,
					//'call_fee' => ($rateArray && $rateArray[$rateIndex]) ? $rateArray[$rateIndex] : $adminSetting[$rateIndex],
					'timeSlot' => $requestData['timeSlot'],
					'call_status' => 'PA',
				];
				if (isset($requestData['call_scheduled_reason'])) {
					$arrayData['call_scheduled_reason'] = $requestData['call_scheduled_reason'];
				}
				try {
					DB::beginTransaction();
					$parent_transaction_id = '';
					$scheduleExistsQuery = Specialist_call_schedule::whereIn('call_status', ['P', 'R', 'A', 'PA'])
						//->where('booking_status', '!=', 'F')
						//->where('booking_status', '!=', 'L')
						->whereNotIn('booking_status', ['F', 'L'])
						->where('specialist_id', $requestData['specialist_id'])
						->where('call_scheduled_date', $requestData['call_scheduled_date'])
						->where('uk_scheduled_time', $ukScheduledTime);


					$scheduleExists = $scheduleExistsQuery->exists();

					if ($scheduleExists) {
						DB::rollBack();
						return response()->json(['error' => 'Call is already assigned for this date and time'], 500);
					}
					$existingBooking = Specialist_call_schedule::where('specialist_id', $requestData['specialist_id'])
						//->where('booking_status', '!=', 'F')
						->whereNotIn('booking_status', ['F', 'L'])
						->whereIn('call_status', ['P', 'R', 'A', 'PA'])
						->where('call_scheduled_date', $requestData['call_scheduled_date'])
						->where('uk_scheduled_time', $ukScheduledTime)
						->lockForUpdate(); // Lock the record to prevent other transactions from reading/// Add the condition if booking_id is provided
					if (isset($requestData['booking_id'])) {
						$existingBooking->where('id', '!=', $requestData['booking_id']);
					}

					$existingBooking = $existingBooking->first();
					// Check if the time slot is already booked
					if ($existingBooking) {
						// If the booking exists, return an error response
						DB::rollBack(); // Rollback the transaction as no booking can be made
						return response()->json([
							'error' => 'The selected time slot is already booked for this specialist.'
						], 400);
					}

					$arrayData['call_fee'] = ($fee_setting['rateArray'] && $fee_setting['rateArray'][$rateIndex]) ? $fee_setting['rateArray'][$rateIndex] : $fee_setting['adminSetting'][$rateIndex];
					$arrayData['member_id'] = $member->id;
					$arrayData['specialist_id'] = $requestData['specialist_id'];
					// Find the parent schedule if meeting_id is provided
					// $arrayData['extended_parent_id'] = null;
					if (!empty($requestData['meeting_id'])) {

						$parentSchedule = Specialist_call_schedule::where('member_id', $member->id)
							->where('meeting_id', $requestData['meeting_id'])
							->first();

						if ($parentSchedule) {
							$arrayData['extended_parent_id'] = $parentSchedule->id;
						}
					}




					$schedule = Specialist_call_schedule::create($arrayData);
					$updateData['call_booking_id'] = 'RC' . date('Y') . '-000' . $schedule->id;
					$schedule->update($updateData);
					DB::commit();
					return [
						'success' => true,
						'message' => 'Booking reserverd successfully!',
						'booking_id' => $schedule->id,

					];
				} catch (\Exception $e) {

					return response()->json(['error' => 'Failed to create schedule' . $e], 500);
				}
			}
		}
	}

	public function updateBookingStatus(Request $request)
	{
		$requestData = $request->all();
		$booking_type = isset($requestData['type']) ? $requestData['type'] : 'Booking';
		$validator = Validator::make($requestData, [
			'booking_id' => 'required',
		], [
			'booking_id.required' => 'The booking id is required.',
		]);
		$bookingId = $requestData['booking_id'];
		$bookingData = Specialist_call_schedule::where('id', $requestData['booking_id'])->first();
		if ($bookingData['booking_status'] !== 'L') {
			if ($validator->fails()) {

				return response()->json(['error' => $validator->errors()->first()], 422);
			} else {
				$member = $request->user();
				try {
					$stripePayment = DB::table('stripe_payment_transaction')
						->where('booking_id', $requestData['booking_id'])->first();
					if ($stripePayment === null) {

						DB::beginTransaction();

						if ($booking_type == 'AutoBooking') {
							$callSchedule = DB::table('reach_member_specialist_call_schedule')->where('id', $requestData['booking_id'])->first();
							$request = new Request((array) $callSchedule);
							$payment = $this->bookingPaymentCardDetails($request);
						} else {
							$validator = Validator::make($requestData, [
								'stripeToken' => 'required|string',
							]);

							if ($validator->fails()) {
								return [
									'success' => false,
									'message' => 'Stripe Token is Misssing.',
								];
							}
							$callSchedule = DB::table('reach_member_specialist_call_schedule')->where('id', $requestData['booking_id'])->first();
							$mergedData = array_merge((array) $callSchedule, ['stripeToken' => $requestData['stripeToken']]);

							$request = new Request($mergedData);
							$payment = $this->paymentCardDetails($request);
						}

						if ($payment['success'] == '1') {
							DB::commit();
							$schedule = Specialist_call_schedule::find($requestData['booking_id']);

							if ($schedule) {

								// Update transaction booking id
								$transaction = StripePaymentTransaction::find($payment['transaction_id']);
								if ($transaction) {
									$transaction->update(['booking_id' => $schedule->id]);

									$charge = Charge::retrieve($payment['charge_id']);
									$charge->metadata['booking_id'] = $requestData['booking_id'];
									$charge->save();
								}

								//insert records to reach_transactions
								$parent_transaction_id = 'TXN-' . strtoupper(Str::random(10));
								$callSchedule = DB::table('reach_member_specialist_call_schedule')->where('id', $requestData['booking_id'])->first();
								$member_details = ReachMember::select('id', 'members_fname', 'members_lname', 'currency')->find($schedule['member_id']);
								$mergedData = array_merge((array) $callSchedule, $member_details->toArray());

								// Create a new Request object with the merged data
								$requestData = new Request($mergedData);

								$amount = $this->currencyService->getspecialistFee($requestData, $member->id);

								$transactionRecord = [
									"transaction_id" => $parent_transaction_id,
									"payment_id" => $payment['transaction_id'],
									"member_id" => $member->id,
									"connected_member_id" => $requestData['specialist_id'],
									"parent_transaction_id" => NULL,
									"original_amount" => $amount['member_fee'],
									"reduced_amount" => 0,
									"actual_amount" => $amount['actual_amount'],
									"from_currency" => $requestData['currency'],
									"to_currency" => $requestData['currency'],
									"rate" => 1,
									"payment_date" => date('Y-m-d H:i:s'),
									"status" => "Completed",
									"type" => "Book A Call",
									"description" => 'Book A Call',
									'transaction_type' => 'Debit'
								];
								$meeting_id = Specialist_call_schedule::generateMeetingId();

								$result = Specialist_call_schedule::where('id', $bookingId)
									->update([
										'call_status' => 'P',
										'booking_status' => 'S',
										'meeting_id' => $meeting_id
									]);
								$reachtransaction = new ReachTransaction($transactionRecord);
								$reachtransaction->save();
								DB::commit();
								//end for reach_transactions

								//for notification
								$memberName = $member->members_fname . ' ' . $member->members_lname;
								$appointmentDate = Carbon::parse($requestData['call_scheduled_date'])->format('d/m/y');
								$appointmentTime = date("H:i", strtotime($requestData['call_scheduled_time']));
								$message = "{$memberName} has booked an appointment with you on {$appointmentDate} at {$appointmentTime}.";
								$url_keyword = 'Specialist';
								$this->notificationService->new_notification('0', '0', $member->id, $requestData['specialist_id'], $message, $url_keyword);
								//end for notification
								//Booking Email to member
								$emailTemplate = ReachEmailTemplate::where('template_type', 'booking_call')->first();

								$result = "<ul style='list-style-type: none;padding: 0px;'>
		            			<li><strong>Booking ID:</strong> " . $schedule->call_booking_id . "</li>
		            			<li><strong>Date:</strong> " . date("m-d-Y", strtotime($schedule->call_scheduled_date)) . "</li>
		            			<li><strong>Time:</strong> " . date("h:i A", strtotime($schedule->uk_scheduled_time)) . " London (GMT)</li>
		            			<li><strong>Expert Name:</strong> " . $schedule->specialist->members_fname . " " . $schedule->specialist->members_lname . "</li>
		            		   </ul>";

								$subject = $emailTemplate->template_subject . $schedule->call_booking_id;
								$body = $emailTemplate->template_message;
								$tags = explode(",", $emailTemplate->template_tags);
								$replace = [$schedule->member->members_fname, $result];
								$body = str_replace($tags, $replace, $body);

								// Send Email to user
								$to = $schedule->member->members_email;
								$cc = [];
								//$bcc = $emailTemplate->template_bcc_address ? explode(',', $emailTemplate->template_bcc_address) : [];
								$bcc = [];

								$mailchimpService = new MailchimpService();
								$mailchimpService->sendTemplateEmail($to, $body, $subject, NULL, $cc, $bcc);

								//Booking Email to Experts
								$emailTemplate2 = ReachEmailTemplate::where('template_type', 'booking_received')->first();

								$result2 = "<ul style='list-style-type: none;padding: 0px;'>
								<li><strong>Booking ID:</strong> " . $schedule->call_booking_id . "</li>
								<li><strong>New Date:</strong> " . date("m-d-Y", strtotime($schedule->call_scheduled_date)) . "</li>
								<li><strong>New Time:</strong> " . date("h:i A", strtotime($schedule->uk_scheduled_time)) . " London (GMT)</li>
							</ul>";

								$member_dts = "<ul style='list-style-type: none;padding: 0px;'>
		            			<li><strong>Member Name:</strong> " . $schedule->member->members_fname . " " . $schedule->member->members_lname . "</li>
		            			<li><strong>Email Id:</strong> " . $schedule->member->members_email . "</li>
		            			<li><strong>Phone Number:</strong> " . $schedule->member->members_phone . "</li>
		            		   </ul>";

								$subject2 = $emailTemplate2->template_subject . $schedule->call_booking_id;
								$body2 = $emailTemplate2->template_message;
								$tags = explode(",", $emailTemplate2->template_tags);
								$replace = [$schedule->specialist->members_fname, $result2, $member_dts];
								$body2 = str_replace($tags, $replace, $body2);

								// Send Email to Expert
								$to2 = $schedule->specialist->members_email;
								$cc2 = [];
								$bcc2 = [];
								$mailchimpService->sendTemplateEmail($to2, $body2, $subject2, NULL, $cc2, $bcc2);


								return response()->json(['success' => true, 'message' => 'Payment successfully completed', 'data' => ['Schedule_id' => $schedule->id, 'transactionId' => $parent_transaction_id, 'specialist_name' => $schedule->specialist->members_fname . " " . $schedule->specialist->members_lname, 'amount' => $amount['actual_amount']]], 200);
							}
						} else {
							return response()->json(['error' => 'Your payment was declined. Please try again.'], 500);
						}
					} else {
						return response()->json(['error' => 'Invalid payment details'], 500);
					}
				} catch (\Exception $e) {

					return response()->json(['error' => 'Failed to create schedule' . $e], 500);
				}
			}
		} else {
			return response()->json(['error' => 'Session timeout. Please try again'], 500);
		}
	}


	public function paymentCardDetails(Request $request)
	{
		$requestData = $request->all();
		// Validate the stripeToken

		$member_details = ReachMember::select('id', 'members_fname', 'members_lname', 'currency', 'members_email', 'members_address', 'members_country', 'members_town', 'members_postcode')->find($requestData['member_id']);
		$member = $request->user() ?? $member_details;
		$specialist = ReachMember::select('id', 'members_fname', 'members_lname')->find($requestData['specialist_id']);

		try {

			// Check if customer with the same email exists
			$this->stripeconnect = new StripeConnect();
			$existingCustomer = $this->stripeconnect->customer_retrive_by_email($member->members_email);


			if ($existingCustomer['status'] === 1 && !empty($existingCustomer['data'])) {
				$customer = $existingCustomer['data'][0];

				// Convert the stripeToken into a PaymentMethod
				$paymentMethod = $this->stripeconnect->create_payment_method($request->stripeToken);
				if ($paymentMethod['status'] === 0) {
					return [
						'success' => false,
						'message' => 'Failed to create payment method.',
						'error' => $paymentMethod['error'],
					];
				}

				// Attach the new card to the existing customer
				$this->stripeconnect->attach_payment_method($customer['id'], $paymentMethod['data']['id']);
				$paymentMethodId = $paymentMethod['data']['id'];
			} else {

				// Create a new customer
				$customer_arr = [
					'email' => $member->members_email,
					'name' => $member->members_fname . " " . $member->members_lname,
					'source' => $request->stripeToken,
					"address" => [
						'line1' => (isset($member->members_address)) ? $member->members_address : '',
						'country' => (isset($member->members_country)) ? $member->members_country : '',
						'city' => (isset($member->members_town)) ? $member->members_town : '',
						'postal_code' => (isset($member->members_postcode)) ? $member->members_postcode : ''
					],
				];

				$customerDts = $this->stripeconnect->create_customer($member->members_email, $customer_arr);
				$customer = $customerDts['data'];

				// Convert the stripeToken into a PaymentMethod
				$paymentMethod = $this->stripeconnect->create_payment_method($request->stripeToken);
				$paymentMethodId = $paymentMethod['data']['id'];
			}

			$requestData['currency'] = $requestData['currency'] ?? $member->currency;
			$fee_setting = FeeHelper::getFeeSettingsAndRates($requestData['currency'], $requestData['specialist_id']);


			if ($requestData['timeSlot'] === '1hr' || $requestData['timeSlot'] === '1 hour') {
				$type = 'one_';
			} else {
				$type = 'half_';
			}

			if (!empty($requestData['meeting_id'])) {

				$parentSchedule = Specialist_call_schedule::where('member_id', $member->id)
					->where('meeting_id', $requestData['meeting_id'])
					->first();

				if ($parentSchedule) {
					$type = 'extra_';
				}
			}


			$rateIndex = $type . '' . $requestData['currency'];
			// Create a PaymentIntent instead of directly charging the card
			$paymentIntentData = [
				'amount' => (($fee_setting['rateArray'] && $fee_setting['rateArray'][$rateIndex]) ? $fee_setting['rateArray'][$rateIndex] : $fee_setting['adminSetting'][$rateIndex]) * 100,
				'currency' => $requestData['currency'],
				'customer' => $customer['id'],
				'payment_method' => $paymentMethodId,
				'off_session' => true,
				'confirm' => true,
				'description' => 'Specialist Booking Fee',
				'metadata' => [
					'payment_to' => $specialist->members_fname . " " . $specialist->members_lname,
					'payment_from' => $member->members_fname . " " . $member->members_lname,
					'member_email' => $member->members_email,
					'member_id' => $member->id,
				],
			];

			$paymentIntent = $this->stripeconnect->create_payment_intent($paymentIntentData);

			if ($paymentIntent['status'] === 1) {
				$paymentIntentData = $paymentIntent['data'];
				$payment_intent_id = $paymentIntentData['id'];
				$latest_charge_id = $paymentIntentData['latest_charge'];

				// Retrieve the charge details
				$charge = $this->stripeconnect->retrieve_charge($latest_charge_id);
				if ($charge['status'] === 1) {
					$chargeData = $charge['data'];
					$charge_id = $chargeData['id'];
					$last_4 = $chargeData['payment_method_details']['card']['last4'] ?? '';
					$feeSettings = MasterSetting::select('reach_fee')->find(1);

					$stripe_change = 3;
					$service_fee = (($feeSettings['reach_fee'] + $stripe_change) / 100) * $chargeData['amount'] / 100;
					$transfer_amounts = ($chargeData['amount'] / 100) - $service_fee;

					$paymentRecord = [
						"member_id" => $member->id,
						"payment_to" => $specialist->id,
						"stripe_payment_intend_id" => $payment_intent_id,
						"stripe_charge_id" => $charge_id,
						"amount_paid" => $chargeData['amount'] / 100,
						"payment_date" => date("Y-m-d"),
						"currency" => $requestData['currency'],

						"charge_description" => $chargeData['description'],
						"last_4" => $last_4,
						"balance_transaction" => $chargeData['balance_transaction'],
						"status" => "P",
						"payment_type" => "bookcall",
						"specialist_amount" => $transfer_amounts
					];
					$transaction = new StripePaymentTransaction($paymentRecord);
					$transaction->save();

					return [
						'success' => true,
						'message' => 'Payment created successfully!',
						'transaction_id' => $transaction->payment_id,
						'charge_id' => $charge_id,
					];
				} else {
					return [
						'success' => false,
						'message' => 'Charge retrieval failed.',
					];
				}
			} else {

				return [
					'success' => false,
					'message' => 'PaymentIntent creation failed.',
				];
			}
		} catch (\Exception $e) {

			return [
				'success' => false,
				'message' => 'An error occurred while processing payment for booking.',
				'error' => $e->getMessage()
			];
		}
	}
}
