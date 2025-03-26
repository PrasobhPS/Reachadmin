<?php

namespace App\Http\Controllers\Api;

use App\Services\NotificationService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


use App\Models\ReachMember;
use App\Models\ReachCountry;
use App\Models\ReachProfileDisplayStatus;
use App\Models\Specialist_call_schedule;
use App\Models\ReachEmployeeDetails;
use App\Models\ReachJob;
use App\Models\ReachLikedmatch;
use App\Models\ReachEmployeeSearch;
use App\Models\ReachBlockedMembers;
use App\Models\ReachMemberRefferals;
use App\Models\MasterSetting;
use App\Models\SpecialistUnavailableSchedules;
use App\Models\ReachWorkingHours;
use App\Models\Interview_schedule;
use App\Models\MemberActivationLog;
use Carbon\Carbon;
use Wester\ChunkUpload\Chunk;
use Wester\ChunkUpload\Validation\Exceptions\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use DateTime; // Import DateTime if you are using it directly

use App\Libraries\StripeConnect;
use App\Models\StripePaymentTransaction;
use App\Models\Notification;
use App\Models\StripeWithdrawalTransaction;
use Illuminate\Support\Facades\Auth;
use App\Models\SpecialistCallRate;
use App\Models\StripePaymentTransfer;
use App\Models\ReachMeetingParticipantHistory;
use App\Models\CurrencyExchangeRates;
use App\Models\WithdrawalTransactionHistory;
use App\Models\ReachTransaction;
use App\Services\CurrencyService;
use App\Models\SpecialistRating;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\FcmNotification;


class MemberController extends Controller
{

	protected $currencyService;
	public function __construct(NotificationService $notificationService, CurrencyService $currencyService)
	{
		$this->notificationService = $notificationService;
		$this->currencyService = $currencyService;
	}

	public function getProfile(Request $request)
	{
		$member = $request->user();

		if ($member) {

			//$memberDetails = $member->toArray();
			$memberDetails = $member->only(['id', 'members_name_title', 'members_fname', 'members_lname', 'members_email', 'members_phone', 'members_phone_code', 'members_dob', 'members_address', 'members_country', 'members_region', 'members_postcode', 'members_town', 'members_street', 'members_profile_picture', 'members_interest', 'members_employment', 'members_employment_history', 'members_biography', 'members_about_me', 'members_type', 'referral_code', 'members_subscription_plan', 'members_subscription_start_date', 'members_subscription_end_date', 'subscription_status', 'is_specialist']);

			$country = ReachCountry::where('country_status', 'A')->where('id', $member->members_phone_code)->first();
			if ($country) {
				$memberDetails['members_dial_code'] = '+' . $country->country_phonecode;
			}


			if ($member->members_profile_picture != '') {
				// Define the file name
				$filename = $member->members_profile_picture;

				// Define the local paths for both folders
				$profileImagesPath = storage_path('app/public/' . $filename);

				// Check if the file exists in the cropped_profile_pic folder
				if (file_exists($profileImagesPath)) {
					$members_profile_picture_path = asset('storage/' . $filename);
				} else {
					// File not found in either folder, set to empty string (or a default image if desired)
					$members_profile_picture_path = "";
				}

				$memberDetails['members_profile_picture'] = $members_profile_picture_path;
			} else {
				$memberDetails['members_profile_picture'] = "";
			}
			// $memberDetails['members_subscription_end_date'] = date('d/m/Y', strtotime($member->members_subscription_end_date));
			$memberDetails['members_employment_history'] = html_entity_decode(strip_tags($member->members_employment_history));
			$memberDetails['members_biography'] = html_entity_decode(strip_tags($member->members_biography));
			$memberDetails['members_about_me'] = html_entity_decode(strip_tags($member->members_about_me));


			if ($member->members_type == 'M') {
				$type = 'Member';
			} else if ($member->members_type == 'T') {
				$type = 'Trial Member';
			} else if ($member->members_type == 'F') {
				$type = 'Free Member';
			} else {
				$type = 'Non Member';
			}
			$memberDetails['member_type'] = $type;
			$latestTransaction = StripePaymentTransaction::where('member_id', $memberDetails['id'])
				->where('payment_type', 'membership')
				->orderBy('created_at', 'desc')
				->first(['amount_paid', 'discount_amount', 'original_amount_paid']);
			//print("<PRE>");print_r($latestTransaction);die();
			if ($latestTransaction) {

				$discountAmount = $latestTransaction['discount_amount'] ?? 0;
				$memberDetails['discount_amount'] = ($discountAmount > 0)
					? $latestTransaction['original_amount_paid']
					: 0;
				$memberDetails['membership_amount'] = $latestTransaction['amount_paid'] ?? 0;
			} else {
				$memberDetails['discount_amount'] = 0;
				$memberDetails['membership_amount'] = 0;
			}

			//Profile Display Status
			$displayStatus = ReachProfileDisplayStatus::where('member_id', $memberDetails['id'])
				->select('field_name', 'field_status')
				->get()
				->mapWithKeys(function ($displayStatus) {
					return [$displayStatus->field_name => $displayStatus->field_status];
				})
				->toArray();

			if ($displayStatus) {
				$memberDetails['displayStatus'] = $displayStatus;
			} else {
				$defaultStatuses = [
					'members_email' => 'A',
					'members_dob' => 'A',
					'members_address' => 'A',
					'members_phone' => 'A',
				];

				$memberDetails['displayStatus'] = $defaultStatuses;

				foreach ($defaultStatuses as $field_name => $field_status) {
					ReachProfileDisplayStatus::create([
						'member_id' => $memberDetails['id'],
						'field_name' => $field_name,
						'field_status' => $field_status
					]);
				}
			}

			$refferal_count = ReachMemberRefferals::where('refferal_member_id', $memberDetails['id'])->count();
			$memberDetails['refferal_count'] = $refferal_count;


			if ($member->members_type != 'M') {
				$referral = ReachMemberRefferals::where('member_id', $memberDetails['id'])
					->select('refferal_code') // Adjust this based on your schema
					->first();

				$memberDetails['referral_code'] = $referral ? $referral->refferal_code : null; // This will be null if no referral exists
			}

			return response()->json(['success' => true, 'message' => 'OK', 'data' => $memberDetails], 200);
		} else {

			return response()->json(['error' => 'Unauthorized'], 401);
		}
	}

	public function addProfilePicture(Request $request)
	{
		$file = $request->file('file');
		$filename = $request->input('name');
		$folderName = $request->input('folderName');
		$chunkNumber = $request->input('currentChunkIndex');
		$totalChunks = $request->input('totalChunks');
		$finalFilePath = "";
		$thumbnailPath = "";

		// Store the chunk in the appropriate directory
		$file->move(public_path('storage/temp-upload'), "{$filename}_part{$chunkNumber}");

		// Check if all chunks have been uploaded
		if ($chunkNumber == $totalChunks - 1) {
			// All chunks have been uploaded, combine them and move to final directory
			$tempPath = public_path('storage/temp-upload');
			$randomNumber = rand(100000, 999999);
			$finalPath = public_path('storage/' . $folderName . '/' . $randomNumber . '_' . $filename);
			$finalFilePath = $folderName . '/' . $randomNumber . '_' . $filename;

			for ($i = 0; $i < $totalChunks; $i++) {
				$chunk = "{$tempPath}/{$filename}_part{$i}";

				$data = file_get_contents($chunk);
				$data = explode(',', $data)[1];
				$fileContent = base64_decode($data);

				file_put_contents($finalPath, $fileContent, FILE_APPEND);
				unlink($chunk); // Delete the chunk after combining
			}

			if ($folderName == "profile-images") {
				$member = $request->user();

				$oldImagePath = public_path('storage/' . $member->members_profile_picture);
				if (file_exists($oldImagePath) && $member->members_profile_picture != '') {
					unlink($oldImagePath); // Remove the old image file
				}

				$member->members_profile_picture = $finalFilePath;
				$member->save();
			}

			// Create a thumbnail if the uploaded file is a video
			if ($this->isVideoFile($finalPath)) {
				$thumbnailPath = $this->createVideoThumbnail($finalPath, $folderName, $randomNumber, $filename);
			}

			return response()->json([
				'success' => true,
				'message' => 'File uploaded successfully',
				'finalFilename' => $finalFilePath,
				'thumbnailPath' => $thumbnailPath ?? ''
			], 200);
		} else {
			return response()->json(['success' => true, 'finalFilename' => ''], 200);
		}
	}

	public function updateProfile(Request $request)
	{

		$member = $request->user();

		if ($member) {
			$requestData = $request->all();
			unset($requestData['members_profile_picture']);
			unset($requestData['members_subscription_end_date']);
			unset($requestData['members_subscription_plan']);
			unset($requestData['members_subscription_start_date']);

			if (isset($requestData['members_dob'])) {
				if ($requestData['members_dob'] === '-00-00') {
					unset($requestData['members_dob']);
				}
			}

			if (($requestData['member_type'] === 'Member' || $requestData['members_type'] === 'M') && isset($requestData['members_country']) && $requestData['members_country'] === 'United States') {
				return response()->json([
					'error' => 'Currently the full membership option is not available for American residents.'
				], 422);
			}
			$validator = Validator::make($requestData, [
				'members_fname' => 'required',
				'members_lname' => 'required',
				'members_email' => [
					'required',
					'email',
					Rule::unique('reach_members', 'members_email')
						->where(function ($query) {
							return $query->where('is_deleted', '!=', 'Y');
						})
						->ignore($member->id, 'id'),
				],
			], [
				'members_fname.required' => 'The first name is required.',
				'members_lname.required' => 'The last name is required.',
				'members_email.required' => 'The email is required.',
				'members_email.email' => 'Please enter a valid email address.',
				'members_email.unique' => 'The email id already exists.',
			]);

			if ($validator->fails()) {
				$errors = $validator->errors()->toArray();
				$firstErrors = "";

				foreach ($errors as $field => $errorMessages) {
					$firstErrors = $errorMessages[0];
				}
				return response()->json(['error' => $firstErrors], 422);
			}

			$member->update($requestData);

			// Retrieve the token for the authenticated user
			$token = $request->bearerToken();
			return response()->json(['success' => true, 'message' => 'OK', 'data' => ['token' => $token, 'id' => $member->id]], 200);
		} else {

			return response()->json(['error' => 'Unauthorized'], 401);
		}
	}

	public function removePicture(Request $request)
	{
		$member = $request->user();
		if ($member) {
			$filePath = $member->members_profile_picture;

			$member->members_profile_picture = NULL;
			$member->save();


			// Delete the file
			Storage::disk('public')->delete($filePath);
			$token = $request->bearerToken();
			return response()->json(['success' => true, 'message' => 'Profile picture removed successfully', 'data' => ['token' => $token, 'id' => $member->id]], 200);
		} else {

			return response()->json(['error' => 'Unauthorized'], 401);
		}
	}

	public function deactivateProfile(Request $request)
	{

		$member = $request->user();
		if ($member) {

			if ($member->members_status != 'I') {
				$member->members_status = 'I';
				$member->save();
			}

			return response()->json(['success' => true, 'message' => 'Profile deactivated successfully'], 200);
		} else {

			return response()->json(['error' => 'Unauthorized'], 401);
		}
	}

	public function deleteProfile(Request $request)
	{

		$member = $request->user();
		if ($member) {
			$member->members_status = 'I';
			$member->is_deleted = 'Y';
			$member->deleted_by = 'User';
			$member->deleted_at = date('Y-m-d H:i:s');
			$member->save();

			$member->delete();

			// Save activation log
			MemberActivationLog::create([
				'member_id' => $member->id,
				'action_type' => 'D',
				'reason' => 'Member Deleted',
				'created_by' => 'User',
			]);


			$transaction = StripePaymentTransaction::where('member_id', $member->id)
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
			return response()->json(['success' => true, 'message' => 'Profile deleted successfully'], 200);
		} else {

			return response()->json(['error' => 'Unauthorized'], 401);
		}
	}



	public function changePassword(Request $request)
	{
		$member = $request->user();

		if ($member) {

			$requestData = $request->all();

			// Validate the request data
			$validator = Validator::make($requestData, [
				'old_password' => 'required',
				'new_password' => 'required|min:8|different:old_password|confirmed',
			], [
				'old_password.required' => 'The old password is required.',
				'new_password.required' => 'The new password is required.',
			]);

			// If validation fails, return the errors
			if ($validator->fails()) {

				$errors = $validator->errors()->toArray();
				$firstErrors = "";

				foreach ($errors as $field => $errorMessages) {
					$firstErrors = $errorMessages[0];
				}

				return response()->json(['error' => $firstErrors], 422);
			} else {

				// Check if the old password matches the current password
				if (!Hash::check($request->old_password, $member->members_password)) {
					return response()->json(['error' => 'The old password is incorrect.'], 422);
				}

				// Update the password
				$member->update([
					'members_password' => Hash::make($request->new_password),
				]);

				// Retrieve the token for the authenticated user
				$token = $request->bearerToken();

				return response()->json(['success' => true, 'message' => 'Password changed successfully.', 'data' => ['Token' => $token, 'Member_id' => $member->id]], 200);
			}
		} else {
			return response()->json(['error' => 'Unauthorized'], 422);
		}
	}

	public function chunkUpload(Request $request)
	{
		try {

			$folderName = $request->input('folderName');
			$size = $request->input('size');
			$finalPath = public_path('storage/' . $folderName . '/');
			$tempPath = public_path('storage/temp-upload/');

			$chunk = new Chunk([
				'name' => 'file', // same as    $_FILES['video']
				'chunk_size' => 1048576, // must be equal to the value specified on the client side

				// Driver
				'driver' => 'local', // [local, ftp]

				// Local driver details
				'local_driver' => [
					'path' => $finalPath, // where to upload the final file
					'tmp_path' => $tempPath, // where to store the temp chunks
				],

				// File details
				'file_name' => Chunk::RANDOM_FILE_NAME,
				'file_extension' => Chunk::ORIGINAL_FILE_EXTENSION,

				// File validation
				'validation' => ['extension:jpeg,png,jpg,mp4,mkv,mov,avi,heic,heif'],
			]);

			$chunk->validate()->store();

			if ($chunk->isLast()) {

				// All chunks are uploaded
				$filePath = $chunk->getFilePath();
				$fullFileName = $chunk->getFullFileName();
				$finalFilePath = $folderName . '/' . $fullFileName;

				if ($folderName == "profile-images") {
					$member = $request->user();

					$oldImagePath = public_path('storage/' . $member->members_profile_picture);
					if (file_exists($oldImagePath) && $member->members_profile_picture != '') {
						unlink($oldImagePath); // Remove the old image file
					}

					$member->members_profile_picture = $finalFilePath;
					$member->save();
				}

				// Create a thumbnail if the uploaded file is a video
				$thumbnailPath = '';
				if ($this->isVideoFile($filePath)) {
					$thumbnailPath = $this->chunkVideoThumbnail($filePath, $folderName, pathinfo($fullFileName, PATHINFO_FILENAME));
				}

				$chunk->response()->json(['status' => 200, 'finalFilename' => $finalFilePath, 'thumbnailPath' => $thumbnailPath]);
			} else {
				$chunk->response()->json(['status' => 201, 'progress' => $chunk->getProgress()]);
			}
		} catch (ValidationException $e) {
			$e->response(422)->json([
				'message' => $e->getMessage(),
				'data' => $e->getErrors(),
			]);
		} catch (\Exception $e) {
			$e->response(400)->abort();
		}
	}

	public function updateStatus(Request $request)
	{
		$member = $request->user();
		if ($member) {

			$requestData = $request->all();

			$field_name = $requestData['field_name'];
			$field_status = $requestData['field_status'];

			ReachProfileDisplayStatus::updateOrCreate(
				['member_id' => $member->id, 'field_name' => $field_name],
				['field_status' => $field_status]
			);
			return response()->json(['success' => true, 'message' => 'OK'], 200);
		} else {

			return response()->json(['error' => 'Unauthorized'], 401);
		}
	}

	public function isVideoFile($filePath)
	{
		$mime = mime_content_type($filePath);
		return strpos($mime, 'video') !== false;
	}

	public function createVideoThumbnail($videoPath, $folderName, $randomNumber, $filename)
	{
		$thumbnailFilename = $randomNumber . '_' . pathinfo($filename, PATHINFO_FILENAME) . '.jpg';
		$thumbnailPath = public_path('storage/' . $folderName . '/thumb_' . $thumbnailFilename);

		$command = "ffmpeg -i $videoPath -ss 00:00:01.000 -vframes 1 $thumbnailPath";
		shell_exec($command);

		return $folderName . '/thumb_' . $thumbnailFilename;
	}

	public function chunkVideoThumbnail($videoPath, $folderName, $filename)
	{
		$thumbnailFilename = $filename . '.jpg';
		$thumbnailPath = public_path('storage/' . $folderName . '/thumb_' . $thumbnailFilename);

		$command = "ffmpeg -i $videoPath -ss 00:00:01.000 -vframes 1 $thumbnailPath";
		shell_exec($command);

		return $folderName . '/thumb_' . $thumbnailFilename;
	}

	/*public function bookingHistory(Request $request)
				   {

					   try {

						   $member = $request->user();


						   $history = Specialist_call_schedule::with(['specialist:id,members_fname,members_lname,members_employment,members_profile_picture,members_country'])
							   //->where('call_status', $type)

							   ->where('member_id', $member->id)
							   ->where('booking_status', 'S')
							   ->whereHas('specialist', function ($query) {
								   $query->where('is_specialist', 'Y');
							   })
							   ->select('id', 'member_id', 'call_scheduled_date', 'timeSlot', 'uk_scheduled_time', 'call_fee', 'call_status', 'specialist_id', 'meeting_id', 'member_rearrange')
							   ->orderBy('created_at', 'desc')
							   ->get()
							   ->map(function ($schedule) use ($member) {
								   $addminutes = 30;
								   if ($schedule->timeSlot === '1 hour' || $schedule->timeSlot === '1hr') {
									   $addminutes = 60;
								   }

								   $currentDateTime = Carbon::now(); // Get the current date and time
								   $scheduledDateTime = Carbon::parse($schedule->call_scheduled_date . ' ' . $schedule->uk_scheduled_time)
									   ->addMinute($addminutes);
								   $isSpecialist = $member->is_specialist ?? false;
								   // Update call_status based on the current time comparison
								   if ($scheduledDateTime->lessThan($currentDateTime) && $schedule->call_status !== 'H') {
									   $schedule->call_status = 'H'; // Change status to 'R' if the time has passed
								   }
								   return [
									   'id' => $schedule->id,
									   'call_scheduled_date' => $schedule->call_scheduled_date,
									   'uk_scheduled_time' => $schedule->uk_scheduled_time,
									   'timeSlot' => $schedule->timeSlot,
									   'call_fee' => $schedule->call_fee,
									   'specialist_id' => $schedule->specialist_id,
									   'member_id' => $schedule->member_id,
									   'members_fname' => $schedule->specialist->members_fname,
									   'members_lname' => $schedule->specialist->members_lname,
									   'members_employment' => $schedule->specialist->members_employment,
									   'members_profile_picture' => $schedule->specialist->members_profile_picture,
									   'members_country' => $schedule->specialist->members_country,
									   'call_status' => $schedule->call_status,
									   'meeting_link' => $schedule->meeting_id,
									   'member_rearrange' => $schedule->member_rearrange,
									   'currency' => $member->currency,

								   ];
							   })
							   ->toArray();

						   return response()->json([
							   'success' => true,
							   'message' => 'OK',
							   'data' => $history,
							   'filePath' => url('storage')
						   ], 200);
					   } catch (\Exception $e) {

						   return response()->json([
							   'success' => false,
							   'message' => 'An error occurred while fetching data',
							   'error' => $e->getMessage()
						   ], 500);
					   }
				   }*/
	public function bookingHistory(Request $request)
	{
		try {
			$member = $request->user();

			$history = Specialist_call_schedule::with(['specialist:id,members_fname,members_lname,members_employment,members_profile_picture,members_country'])
				->where('member_id', $member->id)
				->where('booking_status', 'S')
				->whereHas('specialist', function ($query) {
					$query->where('is_specialist', 'Y');
				})
				->selectRaw('
				GROUP_CONCAT(id ORDER BY uk_scheduled_time SEPARATOR ", ") as ids,
                meeting_id,
                MIN(call_scheduled_date) as call_scheduled_date,
                MIN(uk_scheduled_time) as start_time,
                MAX(uk_scheduled_time) as end_time,
                SUM(call_fee) as total_fee,
                GROUP_CONCAT(timeSlot ORDER BY uk_scheduled_time SEPARATOR ", ") as duration,
                member_id,
                specialist_id,
                call_status,
                member_rearrange
            ')
				->groupBy('meeting_id', 'member_id', 'specialist_id', 'call_status', 'member_rearrange')
				->orderBy('call_scheduled_date', 'desc')
				->get()
				->map(function ($schedule) use ($member) {
					$firstCall = $schedule->first();
					$addminutes = ($schedule->timeSlot === '1 hour' || $schedule->timeSlot === '1hr') ? 60 : 30;
					$currentDateTime = Carbon::now();
					$endDateTime = Carbon::parse($schedule->call_scheduled_date . ' ' . $schedule->end_time)->addMinute($addminutes);;

					// Update call_status dynamically
					// if ($endDateTime->lessThan($currentDateTime) && $schedule->call_status !== 'H') {
					// 	$schedule->call_status = 'H';
					// }
					$timeSlotsArray = array_map('trim', explode(',', $schedule->duration));
					$firstTimeSlot = reset($timeSlotsArray);

					// Ensure all values are numeric before summing
					// Convert time slots into numeric minutes
					$numericTimeSlots = array_map(function ($slot) {
						if (strpos($slot, 'hr') !== false) {
							return intval($slot) * 60; // Convert hours to minutes
						} elseif (strpos($slot, 'min') !== false) {
							return intval($slot); // Keep minutes as is
						} elseif (strpos($slot, 'hour') !== false) {
							return intval($slot) * 60; // Convert hours to minutes
						}
						return 0; // Default to 0 if format is unexpected
					}, $timeSlotsArray);

					// Calculate total duration in minutes
					$totalDurationMinutes = array_sum($numericTimeSlots);

					return [
						'id' => explode(',', $schedule->ids)[0],
						'meeting_id' => $schedule->meeting_id,
						'call_scheduled_date' => $schedule->call_scheduled_date,
						'uk_scheduled_time' => $schedule->start_time,
						'timeSlot' => $firstTimeSlot,
						'call_fee' => $schedule->total_fee,
						'specialist_id' => $schedule->specialist_id,
						'member_id' => $schedule->member_id,
						'members_fname' => $schedule->specialist->members_fname,
						'members_lname' => $schedule->specialist->members_lname,
						'members_employment' => $schedule->specialist->members_employment,
						'members_profile_picture' => $schedule->specialist->members_profile_picture,
						'members_country' => $schedule->specialist->members_country,
						'call_status' => $schedule->call_status,
						'meeting_link' => $schedule->meeting_id,
						'member_rearrange' => $schedule->member_rearrange,
						'currency' => $member->currency,
						'duration' => $totalDurationMinutes,
					];
				})
				->toArray();

			return response()->json([
				'success' => true,
				'message' => 'OK',
				'data' => $history,
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

	public function getMemberDetails($id)
	{
		$member = ReachMember::with('EmployeeDetails')->find($id);
		if ($member) {

			$memberDetails = $member->only(['id', 'members_fname', 'members_lname', 'members_country', 'members_address', 'members_region', 'members_profile_picture', 'members_interest', 'members_employment', 'members_employment_history', 'members_biography', 'members_about_me', 'members_email', 'members_phone', 'members_dob', 'members_phone_code', 'members_town', 'members_street', 'members_postcode']);
			$country = ReachCountry::where('country_status', 'A')->where('id', $member->members_phone_code)->first();
			if ($country) {
				$memberDetails['members_dial_code'] = '+' . $country->country_phonecode;
			}
			$memberDetails['employee_id'] = $member->EmployeeDetails->employee_id ?? null;
			$memberDetails['members_profile_picture'] = $member->members_profile_picture ? asset('storage/' . $member->members_profile_picture) : "";

			$memberDetails['members_employment_history'] = $member->members_employment_history;
			$memberDetails['members_biography'] = $member->members_biography;
			$memberDetails['members_about_me'] = $member->members_about_me;

			// Profile Display Status
			$displayStatus = ReachProfileDisplayStatus::where('member_id', $memberDetails['id'])
				->where('field_status', 'I')
				->pluck('field_status', 'field_name')
				->toArray();
			//print_r($displayStatus);die();

			if (($displayStatus['members_email'] ?? 'A') == 'I') {
				unset($memberDetails['members_email']);
			}
			if (($displayStatus['members_address'] ?? 'A') == 'I') {
				unset($memberDetails['members_address']);
				unset($memberDetails['members_country']);
				unset($memberDetails['members_region']);
				unset($memberDetails['members_postcode']);
				unset($memberDetails['members_town']);
				unset($memberDetails['members_street']);
			}

			if (($displayStatus['members_dob'] ?? 'A') == 'I') {
				unset($memberDetails['members_dob']);
			}
			if (($displayStatus['members_phone'] ?? 'A') == 'I') {
				unset($memberDetails['members_phone']);
			}


			return response()->json(['success' => true, 'data' => $memberDetails], 200);
		} else {

			return response()->json(['error' => 'Unauthorized'], 401);
		}
	}

	public function getChatMemberDetails(Request $request)
	{
		$requestData = $request->all();

		$id = $requestData['id'];
		$type = $requestData['type'];
		$memberDts = [];
		$data = [];

		if ($type == "employer") {
			$memberDts = ReachJob::with('member')
				->where('id', $id)
				->first();
		} elseif ($type == "employee") {
			$memberDts = ReachEmployeeDetails::with('member')
				->where('employee_id', $id)
				->first();
		}

		if ($memberDts) {

			$data["member_id"] = $memberDts->member->id;
			$data["member_fname"] = $memberDts->member->members_fname;
			$data["member_lname"] = $memberDts->member->members_lname;
			$data["member_profile_picture"] = $memberDts->member->members_profile_picture;
			$data["active"] = true;
			$data["joined_at"] = date("Y-m-d", strtotime($memberDts->created_at));
			$data["pending_message_count"] = 0;
			$data["last_message_time"] = "";

			if ($type == "employer") {
				$data["job_id"] = $memberDts->id;
				$data["job_role"] = $memberDts->role ? $memberDts->role->job_role : '';
				$data["location"] = $memberDts->location ? $memberDts->location->boat_location : '';
				$data["vessel_size"] = $memberDts->vessel_size . 'M';
				$data["job_summary"] = $memberDts->job_summary;
			} elseif ($type == "employee") {
				$data["job_role"] = $memberDts->jobRole ? $memberDts->jobRole->job_role : '';
				$data["age"] = $memberDts->employee_age;
				$data["gender"] = $memberDts->employee_gender;
				$data["about"] = $memberDts->employee_intro;
			}

			return response()->json(['success' => true, 'data' => $data], 200);
		} else {
			return response()->json(['error' => 'Member details not found'], 404);
		}
	}

	public function getDashboardCount(Request $request)
	{
		$member = $request->user();
		if ($member) {

			$data = [
				"live_campaigns" => 0,
				"draft_campaigns" => 0,
				"archive_campaigns" => 0,
				"campaign_interviews" => 0,
				"my_matches" => 0,
				"liked_jobs" => 0,
				"disliked_jobs" => 0,
				"available_jobs" => 0,
				"profile_interviews" => 0,
				"interview_count" => 0,
				"job_count" => 0,
				"notification_count" => 0,
			];

			$campaigns = ReachJob::with(['member'])->withActiveMembers()
				->where('member_id', $member->id)->withTrashed()->get();
			if ($campaigns->isNotEmpty()) {
				$data["live_campaigns"] = $campaigns->whereIn('job_status', ['A', 'I'])->where('is_deleted', 'N')->count();
				$data["draft_campaigns"] = $campaigns->where('job_status', 'D')->where('is_deleted', 'N')->count();
				$data["archive_campaigns"] = $campaigns->where('job_status', 'D')->where('is_deleted', 'Y')->count();
			}

			$employeeDts = ReachEmployeeDetails::where('member_id', $member->id)->first();
			if ($employeeDts) {

				$employeeId = $employeeDts->employee_id;

				$employeeLikedJobIds = ReachLikedmatch::where('employee_id', $employeeId)
					->where('liked_by', 'EMPLOYEE')
					->pluck('job_id')
					->toArray();

				$matchingJobIds = ReachLikedmatch::where('employee_id', $employeeId)
					->where('liked_by', 'EMPLOYER')
					->whereIn('job_id', $employeeLikedJobIds)
					->pluck('job_id')
					->unique();

				$matchingJobCount = ReachJob::with(['member'])->withActiveMembers()
					->whereIn('id', $matchingJobIds)
					->where('job_status', 'A')
					->count();

				$data["my_matches"] = $matchingJobCount;

				$likedJobIds = ReachLikedmatch::where('employee_id', $employeeId)
					->where('liked_by', 'EMPLOYER')
					->pluck('job_id')
					->toArray();

				$likedMatches = ReachLikedmatch::where('employee_id', $employeeId)
					->where('liked_by', 'EMPLOYEE')
					->whereNotIn('job_id', $likedJobIds)
					->pluck('job_id')
					->toArray();

				$likedJobCount = ReachJob::with(['member'])->withActiveMembers()
					->whereIn('id', $likedMatches)
					->where('job_status', 'A')
					->count();

				$data["liked_jobs"] = $likedJobCount;

				$unlikedJobIds = ReachLikedmatch::where('employee_id', $employeeDts->employee_id)
					->where('unliked_by', 'EMPLOYEE')
					->pluck('job_id')
					->unique()
					->toArray();

				$unlikedJobCount = ReachJob::with(['member'])->withActiveMembers()
					->whereIn('id', $unlikedJobIds)
					->where('job_status', 'A')
					->count();

				$data["disliked_jobs"] = $unlikedJobCount;


				$searchParameters = ReachEmployeeSearch::where('employee_id', $employeeId)
					->pluck('search_value', 'search_parameter_name')
					->toArray();

				$jobCount = ReachJob::with(['member'])->withActiveMembers()
					->where('job_status', 'A')
					->where('member_id', '!=', $member->id)
					->whereRaw('FIND_IN_SET(job_role, ?)', [$employeeDts->employee_role])
					->whereNotIn('id', $unlikedJobIds)
					->whereNotIn('id', $employeeLikedJobIds);
				if ($searchParameters) {
					$jobCount->where(function ($query) use ($searchParameters) {
						foreach ($searchParameters as $paramName => $paramValue) {
							if ($paramName == "vessel_size") {
								if ($paramValue == '<20') {
									$query->orWhere('vessel_size', '<', 20);
								} elseif ($paramValue == '>50') {
									$query->orWhere('vessel_size', '>', 50);
								} else {
									$sizeRange = explode('-', $paramValue);
									if (count($sizeRange) == 2) {
										$query->orWhereBetween('vessel_size', [$sizeRange[0], $sizeRange[1]]);
									}
								}
							} else {
								$query->orWhere($paramName, $paramValue);
							}
						}
					});
				}

				$jobCount = $jobCount->count();
				$data["available_jobs"] = $jobCount;
				$interviewCount = Interview_schedule::where('employee_id', $employeeId)
					->whereNotIn('interview_status', ['H', 'C'])
					->count();
				$data["interview_count"] = $interviewCount;
			}
			$jobIds = $campaigns->pluck('id')->toArray();

			$jobCount = Interview_schedule::whereIn('job_id', $jobIds)
				->whereNotIn('interview_status', ['C', 'H'])

				->count();
			$data['job_count'] = $jobCount;
			$data['notification_count'] = $data["interview_count"] + $data['job_count'];
			return response()->json(['success' => true, 'data' => $data], 200);
		} else {
			return response()->json(['error' => 'Unauthorized'], 401);
		}
	}

	public function myBlockedList(Request $request)
	{
		$member = $request->user();
		if ($member) {

			$blockList = ReachBlockedMembers::where('member_id', $member->id)->get()->toArray();

			return response()->json(['success' => true, 'data' => $blockList], 200);
		} else {
			return response()->json(['error' => 'Unauthorized'], 401);
		}
	}

	public function CallScheduleWithSpecialist(Request $request)
	{
		$requestData = $request->all();

		$validator = Validator::make($requestData, [
			'specialist_id' => 'required',
		], [
			'specialist_id.required' => 'The specialist_id is required.',
		]);

		if ($validator->fails()) {
			return response()->json(['error' => $validator->errors()->first()], 422);
		} else {

			try {

				$member = $request->user();
				$current_date = date("Y-m-d");
				$averageRating = SpecialistRating::where('specialist_id', $requestData['specialist_id'])
					->avg('rating');
				$ratings = SpecialistRating::where('specialist_id', $requestData['specialist_id'])
					->with(['member:id,members_fname,members_lname']) // Assuming there is a `member` relation defined in the `SpecialistRating` model
					->orderBy('created_at', 'desc') // Order by the most recent ratings
					->get();
				$schedule = Specialist_call_schedule::whereIn('call_status', ['P', 'A'])
					->where('booking_status', 'S')
					->where('specialist_id', $requestData['specialist_id'])
					->where('member_id', $member->id)
					->where('call_scheduled_date', '>=', $current_date)
					->select('call_scheduled_date', 'uk_scheduled_time', 'call_scheduled_time', 'call_scheduled_timezone', 'call_status')
					->get()->toArray();

				return response()->json([
					'success' => true,
					'message' => 'OK',
					'data' => $schedule,
					'averageRating' => $averageRating ? round($averageRating, 2) : 0,
					'ratings' => $ratings,
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

	public function myReferralList(Request $request)
	{
		$member = $request->user();

		if ($member) {

			$referralListQuery = ReachMemberRefferals::with('member:id,members_fname,members_lname,members_email,members_status,members_type,currency')
				->leftJoin('stripe_payment_transaction', 'reach_member_refferals.member_id', '=', 'stripe_payment_transaction.member_id')
				->where('reach_member_refferals.refferal_member_id', $member->id)
				->where(function ($query) {
					$query->where('stripe_payment_transaction.payment_type', 'membership')
						->where('stripe_payment_transaction.discount_type', 'R')
						->orWhereNull('stripe_payment_transaction.payment_type');
				})
				->select(
					'reach_member_refferals.id',
					'reach_member_refferals.member_id',
					'reach_member_refferals.created_at',
					'stripe_payment_transaction.discount_amount as discount_amount',
					'stripe_payment_transaction.status',
					'stripe_payment_transaction.currency',
				)
				->orderBy('reach_member_refferals.created_at', 'desc');

			// Calculate total rewards
			$totalRewards = $referralListQuery->clone()
				->where('stripe_payment_transaction.status', 'A')
				->sum('stripe_payment_transaction.discount_amount');

			$referralList = $referralListQuery->get()
				->map(function ($referral) {
					if ($referral->member) { // Ensure referred member exists
						return [
							'id' => $referral->member->id,
							'members_fname' => $referral->member->members_fname,
							'members_lname' => $referral->member->members_lname,
							'members_email' => $referral->member->members_email,
							'members_type' => $referral->member->members_type,
							'amount' => $referral->discount_amount ?? '',
							'currency' => $referral->currency ?? '',
							'status' => $referral->status ?? 'P',
							'referred_at' => date("d-m-Y", strtotime($referral->created_at)),
						];
					}
					return null;
				})
				->filter()
				->toArray();

			$avlRewards = $totalRewards;

			//$feeSettings = MasterSetting::select('referral_bonus')->find(1);

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

			return response()->json(['success' => true, 'data' => $referralList, 'referral_code' => $member['referral_code'], 'referral_bonus' => $member['referral_rate'], 'total_rewards' => number_format($totalRewards, 2), 'available_rewards' => number_format($avlRewards, 2), 'stripe_varify' => $stripe_varify, 'stripe_url' => $stripe_url], 200);
		} else {
			return response()->json(['error' => 'Unauthorized'], 401);
		}
	}


	public function setWorkingHours(Request $request)
	{
		$member = $request->user();
		$validator = Validator::make($request->all(), [
			'working_hours' => 'required|array',
			'working_hours.*.days' => [
				'required',
				'array',
				function ($attribute, $value, $fail) {
					$allowedDays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
					foreach ($value as $day) {
						if (!in_array($day, $allowedDays)) {
							return $fail($day . ' is not a valid day.');
						}
					}
				}
			],
			'working_hours.*.time' => [
				'required',
				'array',
				function ($attribute, $value, $fail) {
					foreach ($value as $time) {

						if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]\s*-\s*([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $time)) {
							$fail($time . ' is not a valid time format. It should be in the format "HH:mm - HH:mm".');
						}
					}
				}
			]
		]);

		if ($validator->fails()) {
			return response()->json(['errors' => $validator->errors()], 422);
		}

		$data = $request->all();
		foreach ($request->working_hours as $workingHour) {
			ReachWorkingHours::create([
				'member_id' => $member->id,
				'days' => json_encode($workingHour['days']), // Store as JSON
				'working_hours' => json_encode($workingHour['time']), // Store as JSON
			]);
		}

		return response()->json(['message' => 'Working hours saved successfully.']);
	}

	public function unavailableList(Request $request)
	{
		$member = $request->user();
		$request->validate([

			'dates' => 'required|array',
			'dates.*' => 'array',
			'dates.*.*' => 'string',
			'excluded_dates' => 'array',
			'excluded_dates.*' => 'string|date',
		]);

		$data = $request->all();
		$memberId = $member->id;
		$dates = $request['dates'];
		$excludedDates = $request->input('excluded_dates', []);

		if (!empty($excludedDates)) {
			SpecialistUnavailableSchedules::where('member_id', $memberId)
				->whereIn('call_unavailable_date', $excludedDates)
				->delete();
		}
		foreach ($dates as $date => $times) {
			//print("<PRE>");print_r( $times);die();
			if (empty($times)) {
				return response()->json(['message' => "No unavailable times provided for date {$date}."]);
			}
			// Validate and remove duplicates by treating the time range as a string
			$uniqueTimes = array_unique($times);

			// Check if there are any duplicate time ranges for the same date
			if (count($times) !== count($uniqueTimes)) {

				return response()->json(['message' => "Duplicate time ranges found for date {$date}."]);
			}

			// Validate the time ranges for conflicts (start time >= end time, and overlapping times)
			foreach ($times as $time) {
				list($start, $end) = explode('-', $time);

				// Check if the start time is later than the end time
				if (strtotime($start) >= strtotime($end)) {
					return response()->json(['message' => "Invalid time range for {$date}: {$start}-{$end}. End time must be after start time."]);
				}

				// Check for overlaps with other time slots
				foreach ($times as $checkTime) {
					if ($time !== $checkTime) {
						list($checkStart, $checkEnd) = explode('-', $checkTime);
						// Check if the two time slots overlap
						if (
							(strtotime($start) >= strtotime($checkStart) && strtotime($start) < strtotime($checkEnd)) ||
							(strtotime($end) > strtotime($checkStart) && strtotime($end) <= strtotime($checkEnd)) ||
							(strtotime($start) <= strtotime($checkStart) && strtotime($end) >= strtotime($checkEnd))
						) {
							return response()->json(['message' => "Time slots overlap for {$date}: {$time} and {$checkTime}."]);
						}
					}
				}
			}

			SpecialistUnavailableSchedules::updateOrCreate(
				[
					'member_id' => $memberId,
					'call_unavailable_date' => $date
				],
				[
					'unavailable_time' => json_encode($times)
				]
			);
		}

		return response()->json(['message' => 'Unavailable dates saved successfully.']);
	}

	public function getWorkingHours(Request $request)
	{
		$member = $request->user();
		$workingHours = ReachWorkingHours::with(['member:id'])
			->where('member_id', $member->id)
			->select('id', 'member_id', 'days', 'working_hours')
			->orderBy('created_at', 'desc')
			->get();
		$response = [];
		foreach ($workingHours as $entry) {
			$response[] = [
				'id' => $entry->id,
				'days' => json_decode($entry->days, true),
				'time' => json_decode($entry->working_hours, true), // Decode the JSON times to return in the correct format
			];
		}
		return response()->json([
			'memberid' => $member->id,
			'working_hours' => $response,
		]);
	}

	public function getUnavailableList(Request $request)
	{
		$member = $request->user();
		$unavailableDates = SpecialistUnavailableSchedules::with(['member:id'])
			->where('member_id', $member->id)
			->where('call_unavailable_date', '>', Carbon::today())
			->select('id', 'member_id', 'unavailable_time', 'call_unavailable_date')
			->orderBy('created_at', 'desc')
			->get();
		$response = [
			'member_id' => $member->id,
			'dates' => []
		];
		foreach ($unavailableDates as $entry) {
			$id = $entry->id;
			$times = json_decode($entry->unavailable_time, true);
			//$response['dates'][$entry->call_unavailable_date] = $times;
			// Include the id within each date entry

			$response['dates'][$entry->call_unavailable_date] = [
				'id' => $id,
				'times' => $times
			];
		}
		return response()->json($response);
	}

	public function updateWorkingHours(Request $request)
	{
		try {
			$id = $request['id'];
			$workingHours = ReachWorkingHours::findOrFail($id);

			if (!$workingHours) {
				return response()->json(['message' => 'Working hours entry not found'], 404);
			}
			$validator = Validator::make($request->all(), [
				'working_hours' => 'required|array',
				'working_hours.*.days' => [
					'required',
					'array',
					function ($attribute, $value, $fail) {
						$allowedDays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
						foreach ($value as $day) {
							if (!in_array($day, $allowedDays)) {
								return $fail($day . ' is not a valid day.');
							}
						}
					}
				],
				'working_hours.*.time' => [
					'required',
					'array',
					function ($attribute, $value, $fail) {
						foreach ($value as $time) {
							if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]\s*-\s*([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $time)) {
								$fail($time . ' is not a valid time format. It should be in the format "HH:mm - HH:mm".');
							}
						}
					}
				]
			]);

			if ($validator->fails()) {
				return response()->json(['errors' => $validator->errors()], 422);
			}



			// Update the working hours
			foreach ($request->working_hours as $update) {
				$workingHours->days = json_encode($update['days']);
				$workingHours->working_hours = json_encode($update['time']);
				$workingHours->save();
			}
			return response()->json(['message' => 'Working hours updated successfully!'], 200);
		} catch (ModelNotFoundException $e) {
			// Handle the case when the ID is not found
			return response()->json(['error' => 'Working hours not found.'], 404);
		} catch (\Exception $e) {
			// Handle any other exceptions
			return response()->json(['error' => 'An error occurred while updating working hours.'], 500);
		}
	}

	public function deleteWorkingHours(Request $request)
	{
		$id = $request['id'];

		$workingHour = ReachWorkingHours::find($id);
		if (!$workingHour) {
			return response()->json(['message' => 'Working hours entry not found'], 404);
		}
		$workingHour->delete();
		return response()->json(['message' => 'Working hours deleted successfully']);
	}
	public function updateUnavilableList(Request $request)
	{
		$data = $request->all();
		$member = $request->user();
		$id = $request['id'];
		$validated = $request->validate([
			'dates' => 'required|array'
		]);

		$dates = $validated['dates'];
		$unavailableSchedule = SpecialistUnavailableSchedules::where('id', $id)
			->where('member_id', $member->id)
			->first();
		if (!$unavailableSchedule) {
			return response()->json(['error' => 'Unavailable schedule not found'], 404);
		}
		foreach ($dates as $date => $times) {

			$unavailableSchedule->call_unavailable_date = $date;
			$unavailableSchedule->unavailable_time = json_encode($times); // Convert array to JSON
			$unavailableSchedule->save();
		}
		return response()->json(['success' => 'Unavailable schedule updated successfully']);
	}

	public function deleteUnavilableList(Request $request)
	{
		$id = $request['id'];

		$unavailableSchedule = SpecialistUnavailableSchedules::whereIn('id', $id)
			->delete();
		if ($unavailableSchedule === 0) {
			return response()->json(['error' => 'Unavailable schedule not found or access denied'], 404);
		}

		//$unavailableSchedule->delete();
		return response()->json(['success' => 'Unavailable schedule deleted successfully']);
	}

	public function unsubscribePlan(Request $request)
	{
		$member = $request->user();

		$transaction = StripePaymentTransaction::where('member_id', $member->id)
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


					return [
						'success' => true,
						'message' => 'Subscription canceled successfully!',
					];
				} else {
					return [
						'success' => false,
						'message' => 'Subscription cancellation failed.',
					];
				}
			} else {
				return [
					'success' => false,
					'message' => 'Subscription not found.',
				];
			}
		} else {
			return [
				'success' => false,
				'message' => 'Subscription not found.',
			];
		}
	}

	public function notificationList(Request $request)
	{
		$member = $request->user();
		$member_id = $member->id;
		$query = Notification::query();
		$query->where('notified_to', $member_id);
		//$query->where('is_read', 0); 
		$notifications = $query->orderBy('created_at', 'asc')->get();
		return response()->json(['success' => true, 'data' => $notifications], 200);
	}

	public function readNotification(Request $request)
	{

		$member = $request->user();
		$member_id = $member->id;
		Notification::where('notified_to', $member_id)
			->where('is_read', 0)
			->update(['is_read' => 1]);
		return response()->json([
			'success' => true,
			'message' => 'Notifications read successfully',
		], 200);
	}

	// Create a new Stripe customer.
	public function createStripeAccount(Request $request)
	{
		$this->stripeconnect = new StripeConnect();

		$member = $request->user();

		// Check if the user already has a Stripe account
		if ($member->stripe_account_id) {

			return [
				'success' => true,
				'message' => 'Stripe account already exists.',
				'stripe_account_id' => $member->stripe_account_id,
			];
		}

		$requestData['member_id'] = $member->id;
		$requestData['email_id'] = $member->members_email;
		$requestData['first_name'] = $member->members_fname;
		$requestData['last_name'] = $member->members_lname;
		$requestData['phone'] = $member->members_phone;
		$requestData['address_line1'] = $member->members_address;
		$requestData['city'] = $member->members_town;
		$requestData['postal_code'] = $member->members_postcode;

		$country = ReachCountry::where('country_name', $member->members_country)->value('country_iso');
		$requestData['country'] = $country ?: "GB";

		if ($member->members_dob) {
			$dobParts = explode('-', $member->members_dob);
			$requestData['dob_year'] = $dobParts[0];
			$requestData['dob_month'] = $dobParts[1];
			$requestData['dob_day'] = $dobParts[2];
		} else {
			$requestData['dob_year'] = '';
			$requestData['dob_month'] = '';
			$requestData['dob_day'] = '';
		}

		$accountDts = $this->stripeconnect->create_account($requestData);

		if ($accountDts['status'] === 1) {
			$customer = $accountDts['data'];
			$member->update(['stripe_account_id' => $customer->id, 'stripe_account_url' => $accountDts['account_link']]);

			return [
				'success' => true,
				'account_link' => $accountDts['account_link'],
				'message' => 'Stripe account created successfully!',
			];
		} else {
			$errorMessage = explode('.', $accountDts['msg'])[0] . '.';
			return [
				'success' => false,
				'message' => 'Unavailable to created stripe account. ' . $errorMessage,
			];
		}
	}

	// Withdraw Amount
	public function withdrawAmount(Request $request)
	{
		$requestData = $request->all();

		$validator = Validator::make($requestData, [
			'withdraw_amount' => 'required|numeric|min:1',
			'currency' => 'required',
		], [
			'withdraw_amount.required' => 'The amount is required.',
			'withdraw_amount.numeric' => 'The amount must be a number.',
			'withdraw_amount.min' => 'The amount must be at least 1 unit.',
			'currency.required' => 'The currency is required.',
		]);

		if ($validator->fails()) {
			return response()->json(['error' => $validator->errors()->first()], 422);
		} else {

			$member = $request->user();
			$stripeAccountId = $member->stripe_account_id ?? null;

			//for inserting withdraw history
			// Refund query

			//$redeemAmount = $this->total_redeem_by_currency($allRedeem,$member);
			//end for withdarw history
			try {

				$withdraw_amount = $requestData['withdraw_amount'];
				$currency = $requestData['currency'];

				$this->stripeconnect = new StripeConnect();
				$transaction = $this->stripeconnect->addFundsToConnectedAccount($withdraw_amount, $currency, $member->stripe_account_id);

				$transfer_receipt = [];

				if ($transaction['status'] == 1) {

					$transfer_receipt = [
						'member_id' => $member->id,
						'transaction_id' => $transaction['data']['id'],
						'account_id' => $member->stripe_account_id,
						'transfer_amount' => $withdraw_amount,
						'currency' => $currency,
						'balance_transaction' => $transaction['data']['balance_transaction'],
						'transfer_date' => date("Y-m-d"),
						'status' => "A",
						'withdrawal_type' => 'Manual withdraw',
					];

					$transfer = new StripeWithdrawalTransaction($transfer_receipt);
					$transfer->save();


					$refunds = StripePaymentTransaction::where('refund_status', '1')
						->where(function ($query) use ($member) {
							$query->where('payment_to', $member->id)
								->orWhere('member_id', $member->id);
						})
						->with([
							'paymentToMember' => function ($query) {
								$query->select('id', 'members_fname', 'members_lname'); // Specify fields to retrieve
							}
						])
						->select(
							'payment_id',
							'member_id',
							'payment_to',
							'refund_amount',
							'payment_date',
							'currency',
							'payment_type',
							'status'
						)
						->orderBy('created_at', 'desc')
						->get();

					// Transform refunds
					$refundsArray = $refunds->transform(function ($refund) use ($member) {
						$refund->transaction_type = 'credit';
						$refund->payment_type = 'Refund';
						$refund->amount_paid = $refund->refund_amount;
						if ($refund->payment_to === $member['id']) {
							return null;
						}


						if ($refund->paymentToMember) {
							$refund->members_fname = $refund->paymentToMember->members_fname;
							$refund->members_lname = $refund->paymentToMember->members_lname;
						}
						unset($refund->refund_amount, $refund->paymentToMember);

						return $refund;
					})->filter()->toArray();
					//end refund
					//for referral
					$referralListQuery = ReachMemberRefferals::with('member:id,members_fname,members_lname,members_email,members_status,members_type,currency,stripe_account_id')
						->leftJoin('stripe_payment_transaction', 'reach_member_refferals.member_id', '=', 'stripe_payment_transaction.member_id')
						->where('reach_member_refferals.refferal_member_id', $member->id)
						->where(function ($query) {
							$query->where('stripe_payment_transaction.payment_type', 'membership')
								->where('stripe_payment_transaction.discount_type', 'R')
								->orWhereNull('stripe_payment_transaction.payment_type');
						})
						->select(
							'reach_member_refferals.id',
							'reach_member_refferals.member_id',
							'reach_member_refferals.created_at',
							'stripe_payment_transaction.discount_amount as discount_amount',
							'stripe_payment_transaction.referral_status as status',
							'stripe_payment_transaction.payment_id',
							'stripe_payment_transaction.payment_to',
						)
						->orderBy('reach_member_refferals.created_at', 'desc');
					$referralList = $referralListQuery->get()
						->map(function ($referral) {
							//print("<PRE>");print_r($referral->member);die();
							//echo $stripeAccountId = $referral->member->stripe_account_id ?? //'2';die();
							if ($referral->member) { // Ensure referred member exists
								return [
									'payment_id' => $referral->payment_id,
									'member_id' => $referral->member->id,
									'members_fname' => $referral->member->members_fname,
									'members_lname' => $referral->member->members_lname,
									'payment_type' => 'Referral',
									'amount_paid' => $referral->discount_amount ?? 0,
									'currency' => $referral->member->currency ?? '',
									'status' => $referral->status,
									'payment_date' => date("d-m-Y", strtotime($referral->created_at)),
									'payment_to' => $referral->payment_to,
									'transaction_type' => 'credit',
									//'connected_account_id' => $stripeAccountId,

								];
							}
							return null;
						})
						->filter()
						->toArray();
					//$filteredRefferal = $referralList;

					$filteredRefferal = array_filter($referralList, fn($refferalpayment) => $refferalpayment['status'] === 'A');
					$allRedeem = array_merge($refundsArray, $filteredRefferal);
					usort($allRedeem, function ($a, $b) {
						// Convert payment_date to timestamps for comparison
						return strtotime($b['payment_date']) <=> strtotime($a['payment_date']);
					});


					//	print("<PRE>");print_r($allRedeem);die();
					foreach ($allRedeem as $key => $value) {
						$convertArray = [];
						$convertArray[$value['currency']] = $value['amount_paid'];
						//print("<PRE>");print_r($convertArray);die();
						if ($value['currency'] === $member->currency) {
							$converted_object = new \stdClass();
							$converted_object->currency = $member->currency;
							$converted_object->amount = $value['amount_paid'];
							$converted_object->rate = 1;
							$converted_object->converted_amount = $value['amount_paid'];
							// Set it as part of the array of objects
							$converted_amount['converted_amounts'][0] = $converted_object;
						} else {
							$converted_amount = $this->get_converted_amount($convertArray, $member);
						}
						$withdraw_history = [
							'member_id' => $value['member_id'],
							'withdrawal_id' => $transfer->id,
							'payment_id' => $value['payment_id'],
							'connected_account_id' => $stripeAccountId ?? 0,
							'from_currency' => $value['currency'],
							'to_currency' => $requestData['currency'],
							'exchange_rate' => $converted_amount['converted_amounts'][0]->rate,
							'converted_amount' => $converted_amount['converted_amounts'][0]->converted_amount,
							'transfer_date' => date("Y-m-d"),
						];
						$withdraw_history = new WithdrawalTransactionHistory($withdraw_history);
						$withdraw_history->save();
					}

					//insert records to reach_transactions while withdraw

					$transaction_id = 'TXN-' . strtoupper(Str::random(10));
					$transactionRecord = [
						"transaction_id" => $transaction_id,
						"payment_id" => Null,
						"member_id" => $member->id,
						"connected_member_id" => Null,
						"parent_transaction_id" => Null,
						"original_amount" => $withdraw_amount,
						"reduced_amount" => NuLL,
						"actual_amount" => $withdraw_amount,
						"from_currency" => $requestData['currency'],
						"to_currency" => $requestData['currency'],
						"rate" => $this->currencyService->getCurrencyRate($requestData['currency'], $requestData['currency']),
						"payment_date" => date('Y-m-d H:i:s'),
						"status" => "Completed",
						"type" => "Withdraw",
						"description" => 'Withdrawn',
						'transaction_type' => 'Debit'
					];

					$reachtransaction = new ReachTransaction($transactionRecord);
					$reachtransaction->save();
					//end for reach_transactions
					$updatedRows = DB::table('reach_transactions')
						->where('status', 'completed')
						->where('transaction_type', 'credit')
						->update(['status' => 'withdraw']);
					return response()->json(['status' => 1, 'success' => 'Withdraw amount successfully'], 200);
				} else {

					$error_msg = $transaction['data']->getMessage();
					return response()->json(['status' => 0, 'error' => $error_msg], 500);
				}
			} catch (\Exception $e) {
				return response()->json(['error' => $e->getMessage()], 500);
			}
		}
	}

	// Create a verification session.
	public function createStripeVerification(Request $request)
	{

		$this->stripeconnect = new StripeConnect();

		$member = $request->user();

		$verification = $this->stripeconnect->createVerificationSession($member);
		//$verification = $this->stripeconnect->getCardLast4FromChargeId('ch_3QrhhaFmlGMjyCeE0YKy7pm1');

		if ($verification['status'] === 1) {

			$member->update(['verification_id' => $verification['session_id']]);
			return [
				'success' => true,
				'session_id' => $verification['session_id'],
				'ephemeral_key_secret' => $verification['ephemeral_key_secret'],
				'client_secret' => $verification['client_secret'],
			];
		} else {
			return [
				'success' => false,
				'message' => 'Unavailable to created  verification.',
			];
		}
	}


	public function extendslotAvailable_old(Request $request)
	{
		$member = $request->user();
		$requestData = $request->all();
		$schedule = Specialist_call_schedule::where('member_id', $member->id)
			->where('meeting_id', $requestData['meeting_id'])
			->select('specialist_id', 'uk_scheduled_time', 'call_scheduled_time', 'call_scheduled_timezone', 'call_status', 'call_scheduled_date', 'timeslot')
			->first();
		// Check if a schedule exists for the provided meeting_id
		if (!$schedule) {
			return response()->json([
				'status' => 'error',
				'message' => 'No schedule found for the provided meeting ID.'
			], 404);
		}
		// Set default timezone to UK (Europe/London) if not provided
		$defaultTimezone = 'Europe/London';
		$scheduledTimezone = $schedule->call_scheduled_timezone ?? $defaultTimezone;

		// Get the current scheduled time details
		$scheduledDate = trim($schedule->call_scheduled_date);
		$scheduledTime = trim($schedule->call_scheduled_time);



		$scheduledDateTime = Carbon::parse($scheduledDate . ' ' . $scheduledTime, $scheduledTimezone);
		if (($schedule['timeslot'] == '1 hour') || ($schedule['timeslot'] == '1hr')) {
			$scheduledDateTime = $scheduledDateTime->copy()->addHour();
		} else {
			$scheduledDateTime = $scheduledDateTime->copy()->addMinutes(30);
		}

		// Fetch the requested extension duration (default to 30 minutes if not provided)
		$extensionMinutes = 30;

		// Calculate the extended end time
		$extendedEndTime = $scheduledDateTime->copy()->addMinutes($extensionMinutes);

		$extendedEndTimeUK = $extendedEndTime->copy()->setTimezone('Europe/London');
		$scheduledDateTimeUK = $scheduledDateTime->copy()->setTimezone('Europe/London');

		// Check for overlapping schedules for the same specialist in UK timezone
		$query = Specialist_call_schedule::where('specialist_id', $schedule->specialist_id)
			->where('call_scheduled_date', '=', $scheduledDateTimeUK->toDateString())
			->whereNotIn('call_status', ['C', 'H'])
			->where(function ($subQuery) use ($scheduledDateTimeUK) {
				$subQuery->where('call_scheduled_time', '=', $scheduledDateTimeUK->format('H:i:s'));
			});
		$conflictExists = $query->exists();

		// If a conflict is found, the extension is not possible
		if ($conflictExists) {
			return response()->json([
				'status' => 'unavailable',
				'message' => 'The requested extension overlaps with another scheduled slot.'
			], 200);
		}

		// If no conflict, the slot can be extended
		return response()->json([
			'status' => 'available',
			'message' => 'The requested extension is available.',
			'extended_start_time' => $scheduledDateTimeUK->format('Y-m-d H:i'),
			'extended_end_time' => $extendedEndTime->format('Y-m-d H:i'),
			'specialist_id' => $schedule->specialist_id
		], 200);
	}
	public function extendslotAvailable(Request $request)
	{
		$member = $request->user();
		$requestData = $request->all();

		// Fetch the current schedule
		$schedule = Specialist_call_schedule::where('member_id', $member->id)
			->where('meeting_id', $requestData['meeting_id'])
			->select('id', 'specialist_id', 'uk_scheduled_time', 'call_scheduled_time', 'call_scheduled_timezone', 'call_status', 'call_scheduled_date', 'timeslot', 'extended_parent_id')
			->orderBy('id', 'desc')
			->first();
		//print("<PRE>");print_r($schedule);die();

		if (!$schedule) {
			return response()->json([
				'status' => 'error',
				'message' => 'No schedule found for the provided meeting ID.'
			], 404);
		}

		// Retrieve the original parent schedule if it exists
		$parentSchedule = $schedule->parent_schedule_id
			? Specialist_call_schedule::find($schedule->parent_schedule_id)
			: $schedule;

		$defaultTimezone = 'Europe/London';
		$scheduledTimezone = $parentSchedule->call_scheduled_timezone ?? $defaultTimezone;

		$scheduledDate = trim($parentSchedule->call_scheduled_date);
		$scheduledTime = trim($parentSchedule->call_scheduled_time);

		$scheduledDateTime = Carbon::parse($scheduledDate . ' ' . $scheduledTime, $scheduledTimezone);

		// Calculate cumulative end time for the parent schedule
		$currentSchedule = $parentSchedule;
		while ($currentSchedule) {
			if (($currentSchedule['timeslot'] == '1 hour') || ($currentSchedule['timeslot'] == '1hr')) {
				$scheduledDateTime = $scheduledDateTime->copy()->addHour();
			} else {
				$scheduledDateTime = $scheduledDateTime->copy()->addMinutes(30);
			}

			$currentSchedule = Specialist_call_schedule::where('extended_parent_id', $currentSchedule->id)->first();
		}

		$extensionMinutes = 30;
		$extendedEndTime = $scheduledDateTime->copy()->addMinutes($extensionMinutes);

		$extendedEndTimeUK = $extendedEndTime->copy()->setTimezone('Europe/London');
		$scheduledDateTimeUK = $scheduledDateTime->copy()->setTimezone('Europe/London');
		// echo $scheduledDateTimeUK->format('H:i:s');die();

		// Check for overlapping schedules based on the parent schedule's timeslot chain
		$conflictExists = Specialist_call_schedule::where('specialist_id', $parentSchedule->specialist_id)
			//->orWhere('member_id', $member['id'])
			->where('call_scheduled_date', '=', $scheduledDateTimeUK->toDateString())
			->whereNotIn('call_status', ['C', 'H'])
			->where(function ($subQuery) use ($scheduledDateTimeUK) {
				$subQuery->where('call_scheduled_time', '=', $scheduledDateTimeUK->format('H:i:s'));
			})
			//$rawQuery = $conflictExists->toSql(); // Get the raw SQL query
			//$bindings = $conflictExists->getBindings(); // Get the query bindings
			//dd(vsprintf(str_replace('?', '%s', $rawQuery), array_map(fn($binding) => "'//{$binding}'", $bindings)));
			->exists();

		if ($conflictExists) {
			return response()->json([
				'status' => 'unavailable',
				'message' => 'The requested extension overlaps with another scheduled slot.'
			], 200);
		}


		//for fetching currency
		$feeSettings = MasterSetting::select(
			'specialist_booking_fee_half_hour',
			'specialist_booking_fee_half_hour_euro',
			'specialist_booking_fee_half_hour_dollar'
		)->find(1);

		if ($member['currency'] === 'EUR') {
			$adminSetting['half_EUR'] = $feeSettings['specialist_booking_fee_half_hour_euro'];
		} else if ($member['currency'] === 'USD') {
			$adminSetting['half_USD'] = $feeSettings['specialist_booking_fee_half_hour_dollar'];
		} else {
			$adminSetting['half_GBP'] = $feeSettings['specialist_booking_fee_half_hour'];
		}

		$callRate = SpecialistCallRate::where('specialist_id', $parentSchedule->specialist_id)
			->select('rate')
			->orderBy('created_at', 'desc')
			->first();
		if ($callRate) {
			$rateArray = json_decode($callRate->rate, true);
		} else {
			$rateArray = []; // or set a default value
		}
		$type = 'half_';

		$rateIndex = $type . '' . $member['currency'];
		$call_fee = ($rateArray && $rateArray[$rateIndex]) ? $rateArray[$rateIndex] : $adminSetting[$rateIndex];

		//end for currency
		$specialistMember = ReachMember::select('id')
			->where('id', $parentSchedule->specialist_id) // Assuming `specialist_id` is stored in the `member_id` column
			->first();

		if ($specialistMember) {
			$specialistMemberId = $specialistMember->id;
		} else {
			$specialistMemberId = null; // Handle case where no record is found
		}

		return response()->json([
			'status' => 'available',
			'message' => 'The requested extension is available.',
			'extended_start_time' => $scheduledDateTimeUK->format('Y-m-d H:i'),
			'extended_end_time' => $extendedEndTimeUK->format('Y-m-d H:i'),
			'specialist_id' => $parentSchedule->specialist_id,
			'call_fee' => $call_fee,
			'specialist_member_id' => $specialistMemberId

		], 200);
	}

	public function transactionhistory_old(Request $request)
	{
		//for activation fee after one month
		$oneMonthAgo = Carbon::now()->subMonth();
		$query = DB::table('stripe_payment_transaction')
			->where('payment_type', 'membership')
			->where('discount_type', 'R')
			->whereDate('payment_date', '<', $oneMonthAgo);
		$oneMonthAgo = Carbon::now()->subMonth();
		$query = DB::table('stripe_payment_transaction')
			->where('payment_type', 'membership')
			->where('discount_type', 'R')
			->whereDate('payment_date', '<', $oneMonthAgo);
		$query->update(['referral_status' => 'A']);
		//for end fee after one month
		$member = $request->user() ?: Auth::guard('sanctum')->user();
		$transactions = collect(); // Initialize as empty collection

		if (!$member) {
			return response()->json([
				'success' => false,
				'message' => 'Member not authenticated.',
			], 401);
		}

		try {
			$query = StripePaymentTransaction::with([
				'member' => function ($query) {
					$query->select(
						'id',
						'members_fname',
						'members_lname',
						'members_email' // Add email if needed
					)->where('members_type', '=', 'M')->withTrashed(); // Include soft-deleted members
				},
				'paymentToMember' => function ($query) {
					$query->select(
						'id',
						'members_fname',
						'members_lname',
						'members_email' // Add email if needed
					)->withTrashed(); // Include soft-deleted members
				}
			])
				->where(function ($query) use ($member) {
					$query->where('payment_to', $member->id)
						->orWhere('member_id', $member->id);
				})
				->select(
					'booking_id',
					'payment_id',
					'member_id',
					'payment_to',
					'amount_paid',
					'payment_date',
					'currency',
					'payment_type',
					'status',
					'refund_status',
					'discount_type',
					'discount_amount',
					'specialist_amount',
					'original_amount_paid',
					'created_at',
				);

			// Sort and fetch transactions
			$transactions = $query->orderBy('created_at', 'desc')->get();
			//print("<PRE>");print_r($transactions);die();
			// Transform transaction data
			$transactionArray = $transactions->transform(function ($transaction) use ($member) {
				// Determine the transaction type
				$transaction->transaction_type = $transaction->member_id == $member->id ? 'debit' : 'credit';
				// Flatten the member details for both member_id and payment_to
				if ($transaction->relationLoaded('member') && $transaction->member) {
					$transaction->members_fname = $transaction->member->members_fname;
					$transaction->members_lname = $transaction->member->members_lname;
					$transaction->makeHidden('member');
				}

				if ($transaction->relationLoaded('paymentToMember') && $transaction->paymentToMember) {
					$transaction->payment_to_fname = $transaction->paymentToMember->members_fname;
					$transaction->payment_to_lname = $transaction->paymentToMember->members_lname;
					// Optionally, remove the nested 'paymentToMember' field from the result
					$transaction->makeHidden('paymentToMember');
				} else {
					// If paymentToMember is null, ensure it is not included
					unset($transaction->paymentToMember);
				}
				if ($transaction->payment_type === 'bookcall' && $transaction->payment_to === $member['id']) {

					$paymentTransfer = StripePaymentTransfer::where('booking_id', $transaction->booking_id)
						->select('transfer_amount', 'transfer_date')
						->first();

					if (($transaction->status == 'A')) {
						$transaction->status = 'A';
						$transaction->transaction_type = 'credit';
						$transaction->amount_paid = $transaction->specialist_amount;
					} else if ($paymentTransfer) {
						$transaction->status = 'W';
						$transaction->transaction_type = 'credit';
						$transaction->amount_paid = $transaction->specialist_amount;
					} else {

						return null; // Skip this transaction in the final collection
					}
				}
				if ($transaction->payment_type === 'bookcall' && $transaction->payment_to === $member['id'] && $transaction->refund_status == '1') {
					return null;
				}
				if ($transaction->payment_type === 'membership' && $transaction->discount_type == 'R') {
					$transaction->amount_paid = $transaction->original_amount_paid ?? 0;
				}

				// Set member_full_name to payment_to_name if payment_type is not 'membership'
				if ($transaction->payment_type == 'membership') {
					$transaction->members_fname = $transaction->members_fname;
					$transaction->members_lname = $transaction->members_lname;
					// Remove payment_to_name field after using it
					unset($transaction->payment_to_fname);
					unset($transaction->payment_to_lname);
				} else if ($transaction->transaction_type !== 'credit') {
					$transaction->members_fname = $transaction->payment_to_fname;
					$transaction->members_lname = $transaction->payment_to_lname;
					// Remove payment_to_name field after using it
					unset($transaction->payment_to_fname);
					unset($transaction->payment_to_lname);
				} else {
					$transaction->members_fname = $transaction->members_fname;
					$transaction->members_lname = $transaction->members_lname;
					// Remove payment_to_name field after using it
					unset($transaction->payment_to_fname);
					unset($transaction->payment_to_lname);
				}


				$transaction->members_id = $transaction->member_id;
				unset($transaction->member_id);
				unset($transaction->payment_to);
				unset($transaction->booking_id);
				unset($transaction->discount_amount);
				return $transaction;
			})->filter()->values()->toArray();


			// Refund query
			$refunds = StripePaymentTransaction::where('refund_status', '1')
				->where(function ($query) use ($member) {
					$query->where('payment_to', $member->id)
						->orWhere('member_id', $member->id);
				})
				->with([
					'paymentToMember' => function ($query) {
						$query->select('id', 'members_fname', 'members_lname'); // Specify fields to retrieve
					}
				])
				->select(
					'payment_id',
					'member_id as members_id',
					'payment_to',
					'refund_amount',
					'payment_date',
					'currency',
					'payment_type',
					'status',
					'updated_at as created_at'
				)
				->orderBy('created_at', 'desc')
				->get();

			// Transform refunds
			$refundsArray = $refunds->transform(function ($refund) use ($member) {
				$refund->transaction_type = 'credit';
				$refund->payment_type = 'Refund';
				$refund->amount_paid = $refund->refund_amount;
				if ($refund->payment_to === $member['id']) {
					return null;
				}


				if ($refund->paymentToMember) {
					$refund->members_fname = $refund->paymentToMember->members_fname;
					$refund->members_lname = $refund->paymentToMember->members_lname;
				}
				unset($refund->refund_amount, $refund->paymentToMember);

				return $refund;
			})->filter()->toArray();
			//end refund
			//for referral
			$referralListQuery = ReachMemberRefferals::with([
				'member' => function ($query) {
					$query->select(
						'id',
						'members_fname',
						'members_lname',
						'members_email',
						'members_status',
						'members_type',
						'currency'
					)
						->where('members_type', '=', 'M'); // Check the member type here
				}
			])

				->leftJoin('stripe_payment_transaction', 'reach_member_refferals.member_id', '=', 'stripe_payment_transaction.member_id')
				//->where('members_type', '=', 'M')
				->where('reach_member_refferals.refferal_member_id', $member->id)
				->where(function ($query) {
					$query->where('stripe_payment_transaction.payment_type', 'membership')
						->where('stripe_payment_transaction.discount_type', 'R')
						->orWhereNull('stripe_payment_transaction.payment_type');
				})
				->select(
					'reach_member_refferals.id',
					'reach_member_refferals.member_id',
					'reach_member_refferals.created_at',
					'stripe_payment_transaction.discount_amount as discount_amount',
					'stripe_payment_transaction.referral_status as status',
					'stripe_payment_transaction.payment_id',
					'stripe_payment_transaction.payment_to',

				)
				->orderBy('reach_member_refferals.created_at', 'desc');
			$referralList = $referralListQuery->get()
				->map(function ($referral) {
					if ($referral->member) { // Ensure referred member exists
						return [
							'payment_id' => $referral->payment_id,
							'members_id' => $referral->member->id,
							'members_fname' => $referral->member->members_fname,
							'members_lname' => $referral->member->members_lname,
							'payment_type' => 'Referral',
							'amount_paid' => $referral->discount_amount ?? 0,
							'currency' => $referral->member->currency ?? '',
							'status' => $referral->status,
							'payment_date' => date("Y-m-d", strtotime($referral->created_at)),
							'payment_to' => $referral->payment_to,
							'transaction_type' => 'credit',
							'created_at' => $referral->created_at,
							//'referral_status'      => $referral->referral_status,

						];
					}
					return null;
				})
				->filter()
				->toArray();

			//for withdrawl amount
			// Assuming the withdrawal transactions are retrieved similarly:
			$withdrawalListQuery = StripeWithdrawalTransaction::where('member_id', $member->id)
				->join('reach_members', 'reach_members.id', '=', 'stripe_withdrawal_transaction.member_id') // Join with members table
				->select(
					'stripe_withdrawal_transaction.id',
					'stripe_withdrawal_transaction.transfer_amount',
					'stripe_withdrawal_transaction.created_at',
					'stripe_withdrawal_transaction.status',
					'reach_members.members_fname',
					'reach_members.members_lname',
					'reach_members.id',
					'reach_members.currency',
					'stripe_withdrawal_transaction.withdrawal_type'
				) // Select relevant columns from both tables
				->orderBy('stripe_withdrawal_transaction.created_at', 'desc'); // Order by created_at of withdrawal transactions

			// Fetch and transform the withdrawal transactions
			$withdrawalList = $withdrawalListQuery->get()
				->map(function ($withdrawal) {
					return [
						'members_id' => $withdrawal->id,
						'members_fname' => $withdrawal->members_fname,
						'members_lname' => $withdrawal->members_lname,
						'amount_paid' => $withdrawal->transfer_amount,
						'payment_type' =>
						$withdrawal->withdrawal_type === 'Auto withdraw' ? 'Book a call auto withdraw' : $withdrawal->withdrawal_type,
						'status' => $withdrawal->status,
						'payment_date' => date("Y-m-d", strtotime($withdrawal->created_at)),
						'transaction_type' => 'debit',
						'created_at' => $withdrawal->created_at,
						'currency' => $withdrawal->currency,
						'withdraw' => '1',
					];
				})
				->toArray();
			//end for withdrawal

			$allTransactions = array_merge($transactionArray, $refundsArray, $referralList, $withdrawalList);
			$i = 1;
			foreach ($allTransactions as &$transaction) {
				// Combine relevant fields to create a unique ID, e.g., member_id and created_at
				$transaction['transaction_id'] = $i;
				$i++;
			}
			usort($allTransactions, function ($a, $b) {
				// Convert payment_date to timestamps for comparison
				return strtotime($b['created_at']) <=> strtotime($a['created_at']);
			});

			//get reddem amount
			$filteredRefferal = array_filter($referralList, fn($refferalpayment) => $refferalpayment['status'] === 'A');
			//$filteredRefferal =$referralList;
			//$allRedeem = array_merge($refundsArray, $filteredRefferal);
			$allRedeem = array_merge($refundsArray, $filteredRefferal);
			usort($allRedeem, function ($a, $b) {
				// Convert payment_date to timestamps for comparison
				return strtotime($b['payment_date']) <=> strtotime($a['payment_date']);
			});

			$redeemAmount = $this->total_redeem_by_currency($allRedeem, $member, $type = 'redeem');
			//end redeem amount
			$withdraw_amount = $this->total_withdraw_amount($member->id);


			//print("<PRE>");print_r($transactionArray);die();
			//for escrow balance
			$allescrow = array_merge($transactionArray, $refundsArray, $referralList,);
			usort($allescrow, function ($a, $b) {
				return strtotime($b['payment_date']) <=> strtotime($a['payment_date']);
			});

			$escrowAmount = $this->total_redeem_by_currency($allescrow, $member, $type = 'escrow');


			//for your earning section
			$allyourearning = array_merge($refundsArray, $filteredRefferal, $transactionArray);
			usort($allyourearning, function ($a, $b) {
				// Convert payment_date to timestamps for comparison
				return strtotime($b['payment_date']) <=> strtotime($a['payment_date']);
			});

			$allyourearning = $this->total_redeem_by_currency($allyourearning, $member, $type = 'yourearnings');
			//end for your earning section
			////end for escrow balance
			//print("<PRE>");print_r($allescrow);die();


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
					} else {
						$stripe_varify = false;
						$delete_stripe_account = $this->stripeconnect->deleteOldStripeAccount($member->stripe_account_id);
						$member->update(['stripe_account_id' => '', 'stripe_account_url' => '']);
					}
				}
			}

			//print("<PRE>");print_r($totalConvertedAmount);die();
			return response()->json([
				'success' => true,
				'message' => 'OK',
				'data' => $allTransactions,
				'escrow_balance' => number_format(max($escrowAmount['total_converted_amount'], 0), 2),
				'total_earned' => number_format($withdraw_amount, 2) ?? 0,
				'redeem_amount' => number_format($redeemAmount['total_converted_amount'], 2),
				'stripe_url' => $stripe_url,
				'stripe_verified' => $stripe_varify,
				'converted_amount' => $allyourearning['converted_amounts'],
				//'converted_amount' => $converted_amounts,

			], 200);
		} catch (\Exception $e) {
			return response()->json([
				'success' => false,
				'message' => 'An error occurred while fetching data.',
				'error' => $e->getMessage(),
			], 500);
		}
	}
	public function transactionHistory(Request $request)
	{
		//for activation fee after one month
		$oneMonthAgo = Carbon::now()->subMonth();
		$query = DB::table('stripe_payment_transaction')
			->where('payment_type', 'membership')
			->where('discount_type', 'R')
			->whereDate('payment_date', '<', $oneMonthAgo);
		$oneMonthAgo = Carbon::now()->subMonth();
		$query = DB::table('stripe_payment_transaction')
			->where('payment_type', 'membership')
			->where('discount_type', 'R')
			->whereDate('payment_date', '<', $oneMonthAgo);
		$query->update(['referral_status' => 'A']);
		//for end fee after one month
		$member = $request->user() ?: Auth::guard('sanctum')->user();
		$transactions = collect(); // Initialize as empty collection

		if (!$member) {
			return response()->json([
				'success' => false,
				'message' => 'Member not authenticated.',
			], 401);
		}
		try {

			$transactions = ReachTransaction::where('member_id', $member->id)
				->select('transaction_id', 'payment_date', 'original_amount', 'reduced_amount', 'actual_amount', 'type', 'description', 'transaction_type', 'rate', 'member_id', 'connected_member_id', 'from_currency', 'to_currency', 'status')
				->with(['member:id,members_fname,members_lname', 'connectedMember:id,members_fname,members_lname'])
				->orderBy('payment_date', 'desc')

				->get()
				// print_r($transactions);die();
				->map(function ($transaction) {
					$transaction->payment_date = Carbon::parse($transaction->payment_date)->format('d-m-Y');
					$transaction->transaction_id = $transaction->transaction_id;
					$transaction->original_amount = floatval($transaction->original_amount);
					$transaction->reduced_amount = floatval($transaction->reduced_amount);
					$transaction->actual_amount = floatval($transaction->actual_amount);
					$transaction->type = $transaction->type;
					$transaction->description = $transaction->description;
					$transaction->transaction_type = $transaction->transaction_type;
					$transaction->converted_original_amount = floatval(round($transaction->original_amount * number_format($transaction->rate, 2), 2));
					$transaction->converted_reduced_amount = floatval(round($transaction->reduced_amount * number_format($transaction->rate, 2), 2));
					$transaction->converted_actual_amount = floatval(round($transaction->actual_amount * number_format($transaction->rate, 2), 2));
					$transaction->from_currency = $transaction->from_currency;
					$transaction->to_currency = $transaction->to_currency;
					$transaction->transaction_type = $transaction->transaction_type;
					$transaction->status = $transaction->status;
					$transaction->rate = number_format($transaction->rate, 2);
					return $transaction;
				});

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
					} else {
						$stripe_varify = false;
						$delete_stripe_account = $this->stripeconnect->deleteOldStripeAccount($member->stripe_account_id);
						$member->update(['stripe_account_id' => '', 'stripe_account_url' => '']);
					}
				}
			}
			$escrow_balance = $this->get_escrow_balance($request);
			$redeem_amount = $this->get_redeem_amount($request);
			$total_earned = $this->get_total_earned_amount($request);
			//print_r($transaction);die();
			//print("<PRE>");print_r($totalConvertedAmount);die();
			return response()->json([
				'success' => true,
				'message' => 'OK',
				'data' => $transactions,
				'escrow_balance' => number_format($escrow_balance, 2),
				'total_earned' => number_format($total_earned, 2) ?? 0,
				'redeem_amount' => number_format($redeem_amount, 2),
				'stripe_url' => $stripe_url,
				'stripe_verified' => $stripe_varify,
				//'converted_amount' => $allyourearning['converted_amounts'],


			], 200);
		} catch (\Exception $e) {
			// Handle any errors
			return response()->json([
				'success' => false,
				'message' => 'Failed to retrieve transaction history.',
				'error' => $e->getMessage(),
			], 500);
		}
	}
	public function get_escrow_balance(Request $request)
	{
		$memberId = $request->user()->id ?? null;

		if (!$memberId) {
			return 0;
		}
		return DB::table('reach_transactions')
			->where('member_id', $memberId)
			->where('transaction_type', 'credit')
			->whereIn('status', ['pending', 'completed'])
			->selectRaw('SUM(actual_amount * rate) as escrow_balance')
			->value('escrow_balance') ?? 0;
	}
	public function get_redeem_amount(Request $request)
	{
		$memberId = $request->user()->id ?? null;

		if (!$memberId) {
			return 0;
		}
		return DB::table('reach_transactions')
			->where('member_id', $memberId)
			->where('transaction_type', 'credit')
			->whereIn('status', ['completed'])
			->selectRaw('SUM(actual_amount * rate) as redeem_amount')
			->value('redeem_amount') ?? 0;
	}

	public function get_total_earned_amount(Request $request)
	{
		$memberId = $request->user()->id ?? null;

		if (!$memberId) {
			return 0;
		}
		return DB::table('reach_transactions')
			->where('member_id', $memberId)
			//->where('transaction_type', 'credit')
			->whereIn('status', ['withdraw'])
			->selectRaw('SUM(actual_amount * rate) as withdraw_amount')
			->value('withdraw_amount') ?? 0;
	}
	public function total_withdraw_amount($member_id)
	{
		$totalWithdrawalAmount = StripeWithdrawalTransaction::where('member_id', $member_id)
			->sum('transfer_amount');

		if ($totalWithdrawalAmount > 0) {
			return $totalWithdrawalAmount;
		} else {
			return 0;
		}
	}
	public function total_redeem_by_currency($allRedeem, $member, $type)
	{

		$withdrawal_payment_ids = DB::table('withdrawal_transaction_history')
			->pluck('payment_id');
		$allRedeemPaymentIds = collect($allRedeem)->pluck('payment_id');
		//print("<PRE>");print_r($withdrawal_payment_ids);die();
		$missingPaymentIds = $allRedeemPaymentIds->diff($withdrawal_payment_ids);
		$allRedeem = collect($allRedeem)->whereIn('payment_id', $missingPaymentIds->toArray());
		//print_r($allRedeem->toArray());die();
		$defaultCurrencies = ['USD', 'EUR', 'GBP'];
		$totalreedemByCurrency = collect($allRedeem)
			->filter(function ($transaction) use ($type) {
				if ($type == 'escrow' || $type == 'allyourearnings') {
					return $transaction['transaction_type'] === 'credit' && in_array($transaction['status'], ['A', 'P']);
				} else {
					return $transaction['transaction_type'] === 'credit' && in_array($transaction['status'], ['A']);
				}
			})
			->groupBy('currency')
			->map(function ($transactions) {
				return $transactions->sum(function ($transaction) {
					return (float) $transaction['amount_paid'];
				});
			})
			->toArray();


		// Add missing default currencies with a sum of 0 if not already present
		foreach ($defaultCurrencies as $currency) {
			if (!array_key_exists($currency, $totalreedemByCurrency)) {
				$totalreedemByCurrency[$currency] = 0.0; // Set to zero if currency doesn't exist
			}
		}


		$totalreedemByCurrency = collect($totalreedemByCurrency)
			->sortKeys() // Sort the currencies alphabetically (optional)
			->toArray();
		//print("<PRE>");print_r($totalreedemByCurrency);die();
		$converted_amount = $this->get_converted_amount($totalreedemByCurrency, $member);

		$converted_amounts = $converted_amount['converted_amounts'];
		$totalConvertedAmount = $converted_amount['total_converted_amount'];
		return [
			'converted_amounts' => $converted_amounts,
			'total_converted_amount' => round($totalConvertedAmount, 2)
		];
	}

	public function get_converted_amount($totalEarnedByCurrency, $member)
	{
		$baseCurrency = $member->currency;
		//print("<PRE>");print_r( $totalEarnedByCurrency);die();
		if (count($totalEarnedByCurrency) === 1 && isset($totalEarnedByCurrency[$baseCurrency])) {
			return null;
		}
		$currencyRatesQuery = CurrencyExchangeRates::whereIn('currency_code', array_keys($totalEarnedByCurrency));
		if ($baseCurrency == 'USD') {
			$currencyRates = $currencyRatesQuery->pluck('exchange_rate_to_usd', 'currency_code')->toArray();
		} else if ($baseCurrency == 'GBP') {
			$currencyRates = $currencyRatesQuery->pluck('exchange_rate_to_gbp', 'currency_code')->toArray();
		} else if ($baseCurrency == 'EUR') {
			$currencyRates = $currencyRatesQuery->pluck('exchange_rate_to_eur', 'currency_code')->toArray();
		}

		// Loop through the currencies and convert each to USD based on the fetched rates
		$convertedAmounts = [];
		$totalConvertedAmount = 0;
		$onlyBaseCurrency = true;
		foreach ($totalEarnedByCurrency as $currencyCode => $amount) {
			if ($amount > 0) {
				// Get the conversion rate for this currency
				$rateToUsd = $currencyRates[$currencyCode] ?? 0;
				if ($rateToUsd != 0) {
					// Convert the amount to USD
					$convertedAmount = $amount * $rateToUsd;
					$totalConvertedAmount += $convertedAmount;
					$convertedAmounts[] = (object) [
						'currency' => $currencyCode,
						'amount' => round($amount, 2),
						'rate' => number_format($rateToUsd, 2),
						'converted_amount' => round($convertedAmount, 2)
					];
					// Set the flag to false if any non-base currency is found
					if ($currencyCode != $baseCurrency) {
						$onlyBaseCurrency = false;
					}
				} else {
					$convertedAmounts[] = (object) [
						'currency' => $currencyCode,
						'amount' => round($amount, 2),
						'rate' => 0,
						'converted_amount' => 0
					];
				}
			}
		}
		if ($onlyBaseCurrency) {
			return [
				'converted_amounts' => Null,
				'total_converted_amount' => round($totalConvertedAmount, 2)
			];
		} else {
			return [
				'converted_amounts' => $convertedAmounts,
				'total_converted_amount' => round($totalConvertedAmount, 2)
			];
		}
		//return $convertedAmounts;
	}
	public function meeting_jointime(Request $request)
	{
		$requestData = $request->all();

		$validator = Validator::make($requestData, [
			'member_id' => 'required',
			'meeting_id' => 'required|unique:reach_meeting_participant_history,meeting_id,NULL,id,member_id,' . $requestData['member_id'],

		], [
			'member_id.required' => 'The  member id is required.',
			'meeting_id.required' => 'The meeting id is required.',
			'meeting_id.unique' => 'This combination of member_id and meeting_id already exists.',
		]);

		if ($validator->fails()) {
			return response()->json(['error' => $validator->errors()->first()], 422);
		} else {
			$requestData['join_time'] = Carbon::now();
			$report = ReachMeetingParticipantHistory::create($requestData);
			return response()->json(['success' => true, 'message' => 'Meeting history saved  successfully'], 200);
		}
	}
	public function meeting_lefttime(Request $request)
	{

		$requestData = $request->all();

		$validator = Validator::make($requestData, [
			'member_id' => 'required',
			'meeting_id' => 'required',
		], [
			'member_id.required' => 'The  member id is required.',
			'meeting_id.required' => 'The meeting id is required.',


		]);

		if ($validator->fails()) {
			return response()->json(['error' => $validator->errors()->first()], 422);
		} else {
			//DB::enableQueryLog();
			$existingRecord = ReachMeetingParticipantHistory::where('member_id', $requestData['member_id'])
				->where('meeting_id', $requestData['meeting_id'])
				->first();
			//dd(DB::getQueryLog());	
			if ($existingRecord) {
				// Update the left_time field if the record exists
				$existingRecord->left_time = Carbon::now(); // Set to current time or any desired time
				$existingRecord->save();

				return response()->json([
					'success' => true,
					'message' => 'Meeting history updated successfully with left time.',
				], 200);
			}
		}
	}

	public function getFullmemberList(Request $request)
	{
		$member = $request->user();
		$member = Auth::guard('sanctum')->user();
		if (!$member) {
			$member = $request->user();
		}

		try {
			if ($member) {
				$query = ReachMember::query();

				// Example: Apply conditional logic
				if ($request->has('members_type')) {
					$query->where('members_type', 'M');
				}

				// Select specific fields
				$fullmembersList = $query->select('members_fname', 'members_lname', 'id')->get();
				return response()->json([
					'success' => true,
					'message' => 'OK',
					'data' => $fullmembersList,
				], 200);
			}
		} catch (\Exception $e) {

			return response()->json([
				'success' => false,
				'message' => 'An error occurred while fetching data',
				'error' => $e->getMessage()
			], 500);
		}
	}
	public function getspecialistCallpayment(Request $request)
	{
		$meetings = DB::table('reach_member_specialist_call_schedule as specialist_call_schedule')
			->join('reach_meeting_participant_history', 'specialist_call_schedule.meeting_id', '=', 'reach_meeting_participant_history.meeting_id')
			->whereNotNull('specialist_call_schedule.call_scheduled_date')
			->whereNotNull('specialist_call_schedule.call_scheduled_time')
			//->where('specialist_call_schedule.call_status', 'S')
			->select('specialist_call_schedule.*', 'reach_meeting_participant_history.*')
			->get();
		//print_r($meetings);die();

	}

	public function update_specialist_amount(Request $request)
	{
		$booking_list = DB::table('stripe_payment_transaction')
			->join('reach_member_specialist_call_schedule', 'reach_member_specialist_call_schedule.id', '=', 'stripe_payment_transaction.booking_id')
			->where('stripe_payment_transaction.payment_type', 'bookcall')
			->select('reach_member_specialist_call_schedule.call_fee', 'reach_member_specialist_call_schedule.id', 'stripe_payment_transaction.booking_id', 'stripe_payment_transaction.payment_id')
			->get();
		foreach ($booking_list as $key => $value) {
			$feeSettings = MasterSetting::select('reach_fee')->find(1);

			$stripe_change = 3;
			$service_fee = (($feeSettings['reach_fee'] + $stripe_change) / 100) * $value->call_fee;
			$transfer_amounts = ($value->call_fee) - $service_fee;
			DB::table('stripe_payment_transaction')
				->where('booking_id', $value->booking_id)
				->update(['specialist_amount' => $transfer_amounts]);
			//print("<PRE>");print_r($booking_id);die();
		}
	}

	public function getDocVerificationStatus(Request $request)
	{

		$member = $request->user();
		try {
			if ($member) {

				$this->stripeconnect = new StripeConnect();
				$verification = $this->stripeconnect->checkVerificationStatus($member->verification_id);

				// Check if the verification status is available and valid
				if ($verification['status'] === 1) {
					$verificationSession = $verification['verification'];

					if ($verificationSession->status === 'verified') {
						// Update user status to verified in the database
						$member->is_doc_verified = 1;
						$member->doc_verified_at = now();
						$member->save();

						return response()->json([
							'success' => true,
							'is_doc_verified' => $member->is_doc_verified,
							'message' => 'Document verified successfully'
						], 200);
					} elseif ($verificationSession->status === 'requires_input') {

						// Document verification requires additional input
						$errorCode = $verificationSession->last_error->code;

						if ($errorCode == 'document_unverified_other') {
							$message = "The document was invalid";
						} elseif ($errorCode == 'document_expired') {
							$message = "The document was expired";
						} elseif ($errorCode == 'document_type_not_supported') {
							$message = "The document type was not supported";
						} else {
							$message = "Verification requires input";
						}

						return response()->json([
							'success' => false,
							'is_doc_verified' => $member->is_doc_verified,
							'message' => $message,
							'error_code' => $errorCode,
						], 400);
					} else {
						return response()->json([
							'success' => false,
							'is_doc_verified' => $member->is_doc_verified,
							'message' => 'Verification failed'
						], 400);
					}
				} else {
					// If the verification check failed
					return response()->json([
						'success' => false,
						'is_doc_verified' => $member->is_doc_verified,
						'message' => $verification['msg']
					], 200);
				}
			}
		} catch (\Exception $e) {

			return response()->json([
				'success' => false,
				'message' => 'An error occurred while fetching data',
				'error' => $e->getMessage()
			], 500);
		}
	}

	public function varifyIosPaymentToken(Request $request)
	{
		$member = $request->user();
		$iosPaymentToken = $request->input('iosPaymentToken');
		$iosPaymentTokenDecode = base64_decode($iosPaymentToken);

		if ($member->ios_payment_token === $iosPaymentTokenDecode) {
			$member->update(['ios_payment_token' => '']);
			return response()->json([
				'success' => true,
				'message' => 'Payment token verified and cleared successfully.'
			], 200);
		} else {
			// Token does not match
			return response()->json([
				'success' => false,
				'message' => 'Invalid payment token.'
			], 400);
		}
	}

	public function getmemberCardDetails(Request $request)
	{
		$member = $request->user();
		$member_id = $member->id;
		$lastFour = StripePaymentTransaction::where('member_id', $member_id)
			->pluck('last_4')
			->last();
		if ($lastFour) {
			return response()->json([
				'status' => 1,
				'last_4' => $lastFour
			], 200);
		} else {
			return response()->json([
				'status' => 0,
				'error' => 'No payment data found for this member'
			], 404);
		}
	}

	public function generateIostoken(Request $request)
	{
		$member = $request->user();
		$member_id = $member->id;
		$randomString = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz"), 0, 3);
		$randomNumber = rand(100, 999);
		$ios_token = $randomString . $randomNumber;
		$member = ReachMember::find($member_id);
		if ($member) {
			$member->ios_payment_token = $ios_token;
			$member->save();
			$encodedToken = base64_encode($ios_token);
			return response()->json(['message' => 'Token updated successfully!', 'ios_token' => $encodedToken,]);
		}

		return response()->json(['message' => 'Member not found.'], 404);
	}



	public function payment_card_change(Request $request)
	{
		$this->stripeconnect = new StripeConnect();
		$member = $request->user();
		$member_id = $member->id;
		$existingCustomer = $this->stripeconnect->customer_retrive_by_email($member->member_email);
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
			$response = $this->stripeconnect->attach_payment_method($customer['id'], $paymentMethod['data']['id']);

			$paymentMethodId = $paymentMethod['data']['id'];
			$paymentData = [
				'customer_id' => $customer['id'],
				'currency' => $member->currency,
				'member_email' => $member->members_email,
				'member_name' => $member->members_fname . " " . $member->members_lname,
				'paymentMethod' => $paymentMethodId
			];
			//print_r($paymentData);die();
			$response = $this->stripeconnect->generate_new_card_payment_intent($paymentData);


			if ($response['status'] === 1) {
				$payment_intent_id = $response['payment_intent_id'];
				$cardDetails = $this->stripeconnect->getPaymentIntentCardLast4($payment_intent_id);
				$last_4 = $cardDetails['last4'] ?? '';
				$paymentRecord = [
					"stripe_payment_intend_id" => $payment_intent_id,
					"stripe_subscription_id" => 0,
					"stripe_charge_id" => "",
					"member_id" => $member_id,
					"amount_paid" => 0,
					"payment_date" => date("Y-m-d"),
					"currency" => 0,
					"charge_description" => 'Change Card',
					"last_4" => $last_4,
					"balance_transaction" => "",
					"status" => "A",
					"payment_type" => "Change Card",
					"original_amount_paid" => 0,
					"parent_currency" => 0,
				];

				$transaction = new StripePaymentTransaction($paymentRecord);
				$transaction->save();
				return [
					'success' => true,
					'message' => 'Payment Card Changed successfully!',
					'transaction_id' => $transaction->payment_id,

				];
			} else {
				return [
					'success' => false,
					'error' => 'Failed to change card details. Please check your information or try again later.',
				];
			}
		} else {
			return [
				'success' => false,
				'message' => 'Inavalid Customer.',
			];
		}
	}
	public function generatePdf(Request $request)
	{

		$member = $request->user();
		$member_id = $member->id;
		$members_profile_picture_path = storage_path('app/public/' . $member->members_profile_picture);

		$encoded_member_id = base64_encode($member_id);
		if (empty($members_profile_picture_path) || !file_exists($members_profile_picture_path) || is_dir($members_profile_picture_path)) {
			$members_profile_picture = storage_path('app/public/profile-images/Default.jpg');
			$base64_image = 'data:image/' . pathinfo($members_profile_picture, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($members_profile_picture));
		} else {

			$base64_image = 'data:image/' . pathinfo($members_profile_picture_path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($members_profile_picture_path));
		}
		$logoPath = public_path('assets/images/Logo.png');
		$logoPathBase64 = 'data:image/' . pathinfo($logoPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($logoPath));
		//$qr_code_url = "{APP_URL}/publicprofile/" . $encoded_member_id;
		$qr_code_url = config('site.url') . "/publicprofile/" . $encoded_member_id;
		$currentDate = Carbon::now()->format('d/m/Y');
		$membership_type = $member->members_type;

		if ($membership_type == 'M') {
			$membership_type = 'Full Member';
			if ($member->members_subscription_end_date) {
				$members_expairy = Carbon::parse($request->input('membership_expiry', $member->members_subscription_end_date))->format('d/m/Y');
			} else {
				$members_expairy = '';
			}
		} else {
			$membership_type = 'Free Member';
			$members_expairy = '';
		}

		$data = [
			'name' => $request->input('name', $member->members_fname . ' ' . $member->members_lname),
			'profile_photo' => $base64_image,
			'membership_type' => $request->input('membership_type', $membership_type),
			'membership_expiry' => $members_expairy,
			'email' => $request->input('email', $member->members_email),
			'logoPath' => $logoPathBase64,
			'qr_code' => base64_encode(QrCode::format('png')->size(150)->generate(($qr_code_url))),
			'generated_date' => $currentDate,

		];
		//print_r($data);die();
		// Generate PDF
		$pdf = PDF::loadView('pdf.profileCard', $data);
		return response($pdf->output(), 200)
			->header('Content-Type', 'application/pdf')
			->header('Content-Disposition', 'attachment; filename="membership.pdf"');
	}

	public function getCountryIso(Request $request)
	{

		$member = $request->user();
		$member_id = $member->id;
		$country_iso = 'GB';
		if ($member->members_country) {
			$country = ReachCountry::where('country_name', $member->members_country)->first();
			$country_iso = $country->country_iso;
		}

		if ($country_iso) {
			return response()->json([
				'status' => 1,
				'country_iso' => $country_iso
			], 200);
		} else {
			return response()->json([
				'status' => 0,
				'error' => 'No payment data found for this member'
			], 404);
		}
	}

	public function alert_notification(Request $request)
	{
		$users = ReachMember::pluck('id')->toArray();

		foreach ($users as $fcmNotifications) {
			$to = $fcmNotifications;

			$message = "We’ve released a new version of Reach!  Update now to enjoy the latest enhancements";
			$url_keyword = 'Alert';
			$this->notificationService->sendAlertNotification($to, $message, $url_keyword, '0', '0', '0');
		}
	}
	public function videoDuration(Request $request)
	{
		$member = $request->user();
		$requestData = $request->all();

		$validator = Validator::make($requestData, [
			'meeting_id' => 'required',

		], [
			'meeting_id.required' => 'The Meeting Id is required.',

		]);

		if ($validator->fails()) {
			return response()->json(['error' => $validator->errors()->first()], 422);
		} else {
			$totalDuration = DB::table('reach_member_specialist_call_schedule')
				->select(
					'meeting_id',
					DB::raw("SUM(CASE WHEN timeSlot = '1 hour' OR timeSlot = '1hr' THEN 60 ELSE 30 END) as total_duration")
				)
				->where('meeting_id', $requestData['meeting_id'])
				->groupBy('meeting_id')
				->first();

			if ($totalDuration) {
				return response()->json([
					'status' => 1,
					'duration' => (int) $totalDuration->total_duration ?? 0,
				], 200);
			} else {
				return response()->json([
					'status' => 0,
					'error' => 'No Record found'
				], 404);
			}
		}
	}
}
