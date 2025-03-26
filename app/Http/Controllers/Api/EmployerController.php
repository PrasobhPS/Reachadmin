<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DateTime;
use DateTimeZone;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Hash;
use App\Models\ReachMember;
use App\Models\ReachEmployer;
use App\Models\Interview_schedule;
use App\Models\ReachEmailTemplate;
use App\Libraries\MailchimpService;
use App\Models\ReachEmployeeDetails;
use App\Models\ReachJob;
use App\Models\JobDetails\ReachJobRole;
class EmployerController extends Controller
{
	protected $notificationService;

	public function __construct(NotificationService $notificationService)
	{
		$this->notificationService = $notificationService;
	}

	public function registration(Request $request)
	{
		$requestData = $request->all();

		$validator = Validator::make($requestData, [
			'employer_company_name' => 'required|string|max:255',
			'employer_email' => 'required|email|unique:reach_employers,employer_email',
		], [
			'employer_company_name.required' => 'The first name is required.',
			'employer_email.required' => 'The email is required.',
			'employer_email.email' => 'Please enter a valid email address.',
			'employer_email.unique' => 'The email address has already been taken.',
		]);

		// If validation fails, return the errors
		if ($validator->fails()) {
			return response()->json(['errors' => $validator->errors()], 422);
		} else {

			$member = $request->user();

			$arrayData = [
				'employer_company_name' => $requestData['employer_company_name'],
				'employer_email' => $requestData['employer_email'],
				'employer_phone' => $requestData['employer_phone'],
				'employer_country' => $requestData['employer_country'],
				'employer_vessel_name' => $requestData['employer_vessel_name'],
				'employer_profile_picture' => $requestData['employer_profile_picture'],
				'member_id' => $member->id,
			];
		}

		try {

			$employer = ReachEmployer::create($arrayData);

			return response()->json(['success' => true, 'message' => 'OK', 'data' => ['Employer_id' => $employer->id]], 200);

		} catch (\Exception $e) {
			// If an error occurs during creation, return an error response
			return response()->json(['error' => 'Failed to create employer' . $e], 500);
		}

	}

	public function getProfile(Request $request)
	{
		$member = $request->user();

		if ($member) {

			$employerDetails = $member->toArray();
			$employerDetails['employer_profile_picture'] = asset('storage/' . $member->employer_profile_picture);
			//$employerDetails['employer_about_me'] = strip_tags($employer->employer_about_me);

			return response()->json(['success' => true, 'message' => 'OK', 'data' => $employerDetails], 200);

		} else {

			return response()->json(['error' => 'Unauthorized'], 401);
		}
	}

	public function updateProfile(Request $request)
	{
		$member = $request->user();
		if ($member) {
			$requestData = $request->all();
			unset($requestData['employer_profile_picture']);
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

			$filePath = $member->employer_profile_picture;
			$member->employer_profile_picture = NULL;
			$member->save();

			// Delete the file
			Storage::disk('public')->delete($filePath);
			$token = $request->bearerToken();
			return response()->json(['success' => true, 'message' => 'Profile picture removed successfully', 'data' => ['token' => $token, 'id' => $member->id]], 200);

		} else {

			return response()->json(['error' => 'Unauthorized'], 401);

		}
	}

	public function deleteProfile(Request $request)
	{
		$member = $request->user();
		if ($member) {

			$member->is_deleted = 'Y';
			$member->deleted_date = date('Y-m-d H:i:s');
			$member->save();

			return response()->json(['success' => true, 'message' => 'Profile deleted successfully'], 200);

		} else {

			return response()->json(['error' => 'Unauthorized'], 401);
		}
	}

	public function bookAInterview(Request $request)
	{
		$requestData = $request->all();
		$validatorRules = [
			'interview_time' => 'required',
			'interview_date' => 'required',
			'status' => 'required',
		];

		// Conditionally add the employee_id rule if creating an interview
		if (!isset($requestData['interview_id'])) { // Set $isCreatingInterview based on your logic
			$validatorRules['employee_id'] = 'required';
			$validatorRules['job_id'] = 'required';
		}

		$validator = Validator::make($requestData, $validatorRules, [
			'employee_id.required' => 'The employee id is required.',
			'interview_time.required' => 'The interview time is required.',
			'interview_date.required' => 'The interview date is required.',
			'status.required' => 'The status is required.',
			'job_id.required' => 'The job Id is required.',
		]);

		if ($validator->fails()) {
			return response()->json(['error' => $validator->errors()->first()], 422);
		} else {

			$member = $request->user();
			$mappedTimezone = $requestData['interview_timezone'];
			$interview_time = date("H:i", strtotime($requestData['interview_time']));

			// Convert the time to UK time
			$dateTime = new DateTime($requestData['interview_date'] . ' ' . $interview_time, new DateTimeZone($mappedTimezone));
			$dateTime->setTimezone(new DateTimeZone('Europe/London'));
			$ukScheduledTime = $dateTime->format('H:i:s');

			$meeting_id = Interview_schedule::generateMeetingId();

			$arrayData = [
				'interview_time' => $interview_time,
				'interview_date' => $requestData['interview_date'],
				'interview_timezone' => $mappedTimezone,
				'interview_uk_time' => $ukScheduledTime,
				'meeting_id' => $meeting_id,
			];
		}

		try {

			if (isset($requestData['interview_id'])) {
				$interview = Interview_schedule::find($requestData['interview_id']);
				if ($interview) {
					$arrayData['interview_status'] = $requestData['status'];
					$interview->update($arrayData);
				} else {
					return response()->json(['success' => false, 'message' => 'Interview not found'], 200);
				}

			} else {
				$arrayData['employee_id'] = $requestData['employee_id'];
				$arrayData['job_id'] = $requestData['job_id'];

				$interviewExists = Interview_schedule::whereIn('interview_status', ['P', 'A'])
					->where('job_id', $requestData['job_id'])
					->where('interview_date', $requestData['interview_date'])
					->where('interview_uk_time', $ukScheduledTime)
					->exists();

				if ($interviewExists) {
					return response()->json(['success' => false, 'message' => 'An interview is already assigned for this date and time'], 200);
				} else {
					$interview = Interview_schedule::create($arrayData);

				}
			}
			//for notification

			$fullName = $member->members_fname . ' ' . $member->members_lname;
			$interviewDate = $requestData['interview_date'];
			$interviewTime = $requestData['interview_time'];
			$jobId = $requestData['job_id'] ?? $interview['job_id'];
			$job_role_id = ReachJob::where('id', $jobId)->value('job_role');
			$jobRoles = ReachJobRole::where('id', $job_role_id)->value('job_role');
			$employee_id = $requestData['employee_id'] ?? $interview['employee_id'];
			if (isset($requestData['interview_id']) && $requestData['interview_id']) {

				if ($interview['interview_status'] == 'P') {
					$message = 'Your interview for the ' . $jobRoles . ' position has been re scheduled by   ' . $fullName . ' on ' . date('d-m-Y', strtotime($interviewDate)) . " at " . $interviewTime . ".";
					$employee_id = $interview['employee_id'];
					$memberId = ReachEmployeeDetails::where('employee_id', $employee_id)->value('member_id');
				}
				if ($interview['interview_status'] == 'R') {

					$message = 'The interview scheduled for the ' . $jobRoles . '  has been re scheduled to ' . date('d-m-Y', strtotime($interviewDate)) . " at " . $interviewTime . " by the " . $fullName;
					$job_id = $interview['job_id'];
					$memberId = ReachJob::where('id', $job_id)->value('member_id');
				}
			} else {

				$memberId = ReachEmployeeDetails::where('employee_id', $employee_id)->value('member_id');
				$message = 'Your interview for the ' . $jobRoles . ' position has been scheduled by  ' . $fullName . " on " . date('d-m-Y', strtotime($interviewDate)) . " at " . $interviewTime . ".";
			}

			$url_keyword = 'Interview';
			$this->notificationService->new_notification($employee_id, $jobId, $member->id, $memberId, $message, $url_keyword);
			//end for notification
			return response()->json(['success' => true, 'message' => isset($requestData['interview_id']) ? 'Interview updated successfully' : 'Interview created successfully', 'data' => ['interview_id' => $interview->id]], 200);

		} catch (\Exception $e) {

			return response()->json(['error' => 'Failed to create interview' . $e], 500);
		}
	}

	public function bookedInterviews(Request $request)
	{
		$requestData = $request->all();

		$validator = Validator::make($requestData, [
			'job_id' => 'required',
			'interview_date' => 'required',
		], [
			'job_id.required' => 'The job id is required.',
			'interview_date.required' => 'The interview date is required.',
		]);

		if ($validator->fails()) {
			$errors = $validator->errors()->toArray();
			$firstErrors = "";

			foreach ($errors as $field => $errorMessages) {
				$firstErrors = $errorMessages[0];
			}

			return response()->json(['error' => $firstErrors], 422);
		} else {

			try {

				$interview = Interview_schedule::whereIn('interview_status', ['P', 'A'])
					->where('job_id', $requestData['job_id'])
					->where('interview_date', $requestData['interview_date'])
					->select('interview_time')
					->get()->toArray();

				return response()->json([
					'success' => true,
					'message' => 'OK',
					'data' => $interview,
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

	public function acceptInterview(Request $request)
	{
		$requestData = $request->all();
		$validator = Validator::make($requestData, [
			'interview_id' => 'required',
			'status' => 'required',
		], [
			'interview_id.required' => 'The interview id is required.',
			'interview_status.required' => 'The interview status is required.',
		]);

		if ($validator->fails()) {
			return response()->json(['error' => $validator->errors()->first()], 422);
		} else {

			try {

				$interview = Interview_schedule::find($requestData['interview_id']);
				$status = $requestData['status'];
				if ($interview) {
					if ($interview['interview_status'] == 'P' || $interview['interview_status'] == 'R' || $interview['interview_status'] == 'A') {
						//notification 

						$member = $request->user();
						$jobId = $interview['job_id'];
						$jobs = ReachJob::where('id', $jobId)->value('job_role');
						$jobRole = ReachJobRole::where('id', $jobs)->value('job_role');
						$msg = ($status == 'A') ? 'Accepted' : 'Cancelled';
						$fullname = $member->members_fname . ' ' . $member->members_lname;
						if ($requestData['is_cancel'] == 'job') {

							$notification_message = 'Your interview for the ' . $jobRole . ' position has been ' . $msg . ' by ' . $fullname;
							$employee_id = $interview['employee_id'];
							$memberId = ReachEmployeeDetails::where('employee_id', $employee_id)->value('member_id');
						}
						if ($requestData['is_cancel'] == 'employee') {

							$notification_message = 'The interview scheduled for the ' . $jobRole . ' has been ' . $msg . ' by ' . $fullname;
							$job_id = $interview['job_id'];
							$memberId = ReachJob::where('id', $job_id)->value('member_id');
						}

						$employee_id = $interview['employee_id'];
						$url_keyword = 'Interview';
						//end notification	
						$interview->interview_status = $status;
						$interview->save();
						$employee_member_id = $interview->employee->member_id;
						$memberDts = ReachMember::select('members_fname', 'members_lname', 'members_email')->find($employee_member_id);

						$employer_member_id = $interview->job->member_id;
						$interviewer = ReachMember::select('members_fname', 'members_lname')->find($employer_member_id);
						if ($status == 'A') {
							//Booking Email to member
							$emailTemplate = ReachEmailTemplate::where('template_type', 'interview_confirmation')->first();

							$result = "<ul>
		            			<li><strong>Date:</strong> " . date("m-d-Y", strtotime($interview->interview_date)) . "</li>
		            			<li><strong>Time:</strong> " . date("h:i A", strtotime($interview->interview_uk_time)) . " London (GMT)</li>
		            			<li><strong>Name:</strong> " . $interviewer->members_fname . " " . $interviewer->members_lname . "</li>
		            		   </ul>";

							$subject = $emailTemplate->template_subject;
							$body = $emailTemplate->template_message;
							$tags = explode(",", $emailTemplate->template_tags);
							$replace = [$memberDts->members_fname, $result];
							$body = str_replace($tags, $replace, $body);
							$message = 'Interview accepted successfully!';
							$to = $memberDts->members_email;
							$cc = [];
							//$bcc = $emailTemplate->template_bcc_address ? explode(',', $emailTemplate->template_bcc_address) : [];
							$bcc = [];

							//$mailchimpService = new MailchimpService();
							//$mailchimpService->sendTemplateEmail($to, $body, $subject, NULL, $cc, $bcc);
						}

						if ($status == 'C') {
							$message = 'Interview cancelled successfully!';
						}


						$this->notificationService->new_notification($employee_id, $jobId, $member->id, $memberId, $notification_message, $url_keyword);
						//end notification	



					} else {
						return response()->json([
							'success' => false,
							'message' => 'Invalid interview Status!',
							'error' => 'Invalid interview status. Update not allowed.'
						], 404);
					}


					return response()->json([
						'success' => true,
						'message' => $message,
					], 200);
				} else {
					return response()->json([
						'success' => false,
						'message' => 'Interview not found!',
						'error' => 'The interview with the given ID does not exist.'
					], 404);
				}
			} catch (\Exception $e) {

				return response()->json([
					'success' => false,
					'message' => 'An error occurred while accepting the interview.',
					'error' => $e->getMessage()
				], 500);
			}
		}
	}

}
